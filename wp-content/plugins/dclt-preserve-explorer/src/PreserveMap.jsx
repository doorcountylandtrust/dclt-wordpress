import React, { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup, GeoJSON } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import PreserveListView from './PreserveListView';

// Fix Leaflet default markers
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

// Helper: Normalize GeoJSON input
function normalizeGeoJson(input) {
  if (!input || typeof input !== 'object') return null;

  if (input.type === 'FeatureCollection') return input;
  if (input.type === 'Feature') {
    return { type: 'FeatureCollection', features: [input] };
  }
  if (['Polygon', 'LineString', 'MultiPolygon', 'Point'].includes(input.type)) {
    return {
      type: 'FeatureCollection',
      features: [{ type: 'Feature', geometry: input, properties: {} }],
    };
  }

  console.warn('Unrecognized GeoJSON structure:', input);
  return null;
}

// Helper: Check if preserve matches filters
function preserveMatchesFilters(preserve, filters) {
  if (!filters || Object.keys(filters).length === 0) return true;

  return Object.entries(filters).every(([filterType, selectedValues]) => {
    if (!selectedValues || selectedValues.length === 0) return true;
    const metaKey = `_preserve_filter_${filterType}`;
    const preserveValues = preserve.meta[metaKey] || [];
    const preserveArray = Array.isArray(preserveValues) ? preserveValues : [preserveValues];
    return selectedValues.some(val => preserveArray.includes(val));
  });
}

// GeoJSON styles
const layerStyles = {
  boundary: { color: '#22c55e', weight: 2, fillOpacity: 0.1, fillColor: '#22c55e' },
  trail: { color: '#ef4444', weight: 3, opacity: 0.8, dashArray: '5, 5' },
  accessible_trails: { color: '#3b82f6', weight: 4, opacity: 0.9 },
  boardwalk: { color: '#8b5cf6', weight: 5, opacity: 0.8 },
  structures: { radius: 8, fillColor: '#f59e0b', color: '#d97706', weight: 2, opacity: 1, fillOpacity: 0.8 },
  parking: { radius: 10, fillColor: '#10b981', color: '#059669', weight: 2, opacity: 1, fillOpacity: 0.9 },
};

export default function PreserveMap({ 
  filters, 
  layerToggles = {}, 
  preserves = [],
  // NEW PROPS:
  mode = 'discovery',
  initialSelectedPreserve = null,
  onPreserveSelect = null,
  getShareUrl = null,
  initialPreserveData = null
}) {
    const [geoJsonLayers, setGeoJsonLayers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [viewMode, setViewMode] = useState('map');
  
  // Deep linking state
  const [selectedPreserve, setSelectedPreserve] = useState(null);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [mapInstance, setMapInstance] = useState(null);

  // Handle URL deep linking
useEffect(() => {
    if (mode === 'preserve-focused' && initialSelectedPreserve && preserves.length > 0) {
      // Find the preserve by slug
      let preserve = preserves.find(p => 
        p.slug === initialSelectedPreserve || 
        p.post_name === initialSelectedPreserve ||
        p.title.rendered.toLowerCase().replace(/\s+/g, '-') === initialSelectedPreserve
      );
      
      // If not found in API data, use server data
      if (!preserve && initialPreserveData) {
        preserve = {
          id: initialPreserveData.id,
          title: { rendered: initialPreserveData.title },
          slug: initialPreserveData.slug,
          post_name: initialPreserveData.slug,
          meta: initialPreserveData.meta
        };
      }
      
      if (preserve) {
        setSelectedPreserve(preserve);
        setShowDetailModal(true);
        
        // Center map on this preserve
        const lat = parseFloat(preserve.meta._preserve_lat || preserve.meta.lat);
        const lng = parseFloat(preserve.meta._preserve_lng || preserve.meta.lng);
        if (lat && lng && mapInstance) {
          mapInstance.setView([lat, lng], 15);
        }
      }
    }
  }, [mode, initialSelectedPreserve, preserves, mapInstance, initialPreserveData]);

  // Handle preserve selection from popup
const handlePreserveSelect = (preserve) => {
    if (onPreserveSelect) {
      onPreserveSelect(preserve);
    } else {
      // Fallback to existing behavior
      setSelectedPreserve(preserve);
      setShowDetailModal(true);
      
      const newUrl = new URL(window.location);
      const preserveSlug = preserve.slug || preserve.post_name || preserve.title.rendered.toLowerCase().replace(/\s+/g, '-');
      newUrl.searchParams.set('preserve', preserveSlug);
      window.history.replaceState({}, '', newUrl);
    }
  };

  // Handle modal close
  const handleModalClose = () => {
    setShowDetailModal(false);
    setSelectedPreserve(null);
    
    // Remove preserve parameter from URL
    const newUrl = new URL(window.location);
    newUrl.searchParams.delete('preserve');
    window.history.replaceState({}, '', newUrl);
  };

  // Generate URLs for sharing
  const getPreserveUrl = (preserve) => {
    const preserveSlug = preserve.slug || preserve.post_name || preserve.title.rendered.toLowerCase().replace(/\s+/g, '-');
    return `${window.location.origin}/preserve/${preserveSlug}/`;
  };

  const getMapDeepLink = (preserve) => {
    const preserveSlug = preserve.slug || preserve.post_name || preserve.title.rendered.toLowerCase().replace(/\s+/g, '-');
    return `${window.location.origin}/preserve-explorer/?preserve=${preserveSlug}`;
  };

  // Load GeoJSON layers
  useEffect(() => {
    if (!preserves || preserves.length === 0) {
      setLoading(true);
      return;
    }

    setLoading(true);
    const processPreserves = async () => {
      const geoJsons = await Promise.all(
        preserves.map(async (preserve) => {
          const layers = [];
          const layerTypes = [
            { key: '_preserve_boundary_file', type: 'boundary' },
            { key: '_preserve_trail_file', type: 'trail' },
            { key: '_preserve_accessible_trails_file', type: 'accessible_trails' },
            { key: '_preserve_boardwalk_file', type: 'boardwalk' },
            { key: '_preserve_structures_file', type: 'structures' },
            { key: '_preserve_parking_file', type: 'parking' }
          ];

          for (const { key, type } of layerTypes) {
            const url = preserve.meta[key];
            if (url) {
              try {
                const res = await fetch(url);
                const json = await res.json();
                const normalized = normalizeGeoJson(json);
                if (normalized) {
                  layers.push({ preserveId: preserve.id, preserveTitle: preserve.title.rendered, type, data: normalized });
                }
              } catch (e) {
                console.warn(`⚠️ ${type} JSON failed for ${preserve.title.rendered}`, e);
              }
            }
          }
          return layers;
        })
      );
      setGeoJsonLayers(geoJsons.flat());
      setLoading(false);
    };

    processPreserves();
  }, [preserves]);

  const filteredPreserves = preserves.filter(p => preserveMatchesFilters(p, filters));
  const visibleLayers = geoJsonLayers.filter(layer => {
    const preserve = preserves.find(p => p.id === layer.preserveId);
    if (!preserve || !preserveMatchesFilters(preserve, filters)) return false;
    if (layerToggles.hasOwnProperty(layer.type)) return layerToggles[layer.type];
    return ['boundary', 'trail'].includes(layer.type);
  });

  if (loading) {
    return (
      <div style={{ height: '600px', width: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', backgroundColor: '#f3f4f6' }}>
        <div style={{ textAlign: 'center' }}>
          <div style={{ width: '40px', height: '40px', border: '4px solid #e5e7eb', borderTop: '4px solid #3b82f6', borderRadius: '50%', animation: 'spin 1s linear infinite', margin: '0 auto 16px' }}></div>
          <p style={{ color: '#6b7280', margin: 0 }}>Loading preserves...</p>
        </div>
      </div>
    );
  }

  return (
    <div>
      {/* View Toggle */}
      <div style={{ display: 'flex', justifyContent: 'flex-end', padding: '0.5rem 0' }}>
        <button 
          onClick={() => setViewMode('map')} 
          style={{ 
            marginRight: '0.5rem', 
            padding: '0.5rem 1rem', 
            background: viewMode === 'map' ? '#3b82f6' : '#e5e7eb', 
            color: viewMode === 'map' ? 'white' : '#111827', 
            borderRadius: '6px', 
            border: 'none', 
            cursor: 'pointer' 
          }}
        >
          🗺 Map View
        </button>
        <button 
          onClick={() => setViewMode('list')} 
          style={{ 
            padding: '0.5rem 1rem', 
            background: viewMode === 'list' ? '#3b82f6' : '#e5e7eb', 
            color: viewMode === 'list' ? 'white' : '#111827', 
            borderRadius: '6px', 
            border: 'none', 
            cursor: 'pointer' 
          }}
        >
          📋 List View
        </button>
      </div>

      {/* Conditionally render view */}
      {viewMode === 'map' ? (
        <div style={{ height: '600px', width: '100%' }}>
          <MapContainer 
            center={[44.8, -87.4]} 
            zoom={10} 
            scrollWheelZoom={true} 
            style={{ height: '100%', width: '100%' }}
            whenCreated={setMapInstance}
          >
            <TileLayer 
              attribution='&copy; OpenStreetMap contributors' 
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" 
            />
            
            {/* Preserve Markers with Enhanced Popups */}
            {filteredPreserves.map((preserve) => {
              const lat = parseFloat(preserve.meta._preserve_lat);
              const lng = parseFloat(preserve.meta._preserve_lng);
              if (!lat || !lng) return null;

              return (
                <Marker key={`marker-${preserve.id}`} position={[lat, lng]}>
                  <Popup>
                    <div style={{ minWidth: '250px', maxWidth: '300px' }}>
                      <strong style={{ fontSize: '16px', display: 'block', marginBottom: '8px', color: '#1f2937' }}>
                        {preserve.title.rendered}
                      </strong>
                      
                      <div style={{ fontSize: '14px', lineHeight: '1.4', marginBottom: '12px' }}>
                        {preserve.meta._preserve_acres && (
                          <div><strong>Size:</strong> {preserve.meta._preserve_acres} acres</div>
                        )}
                        {preserve.meta._preserve_trail_length && (
                          <div><strong>Trail Length:</strong> {preserve.meta._preserve_trail_length} miles</div>
                        )}
                        {preserve.meta._preserve_filter_difficulty && (
                          <div><strong>Difficulty:</strong> {preserve.meta._preserve_filter_difficulty.join(', ')}</div>
                        )}
                        {preserve.meta._preserve_filter_region && (
                          <div style={{ marginTop: '8px', fontSize: '12px', color: '#6b7280' }}>
                            <strong>Region:</strong> {preserve.meta._preserve_filter_region.join(', ')}
                          </div>
                        )}
                      </div>
                      
                      {/* Action buttons */}
                      <div style={{ display: 'flex', gap: '6px', flexDirection: 'column' }}>
                        <button 
                          onClick={() => handlePreserveSelect(preserve)}
                          style={{
                            width: '100%',
                            padding: '8px 12px',
                            backgroundColor: '#3b82f6',
                            color: 'white',
                            border: 'none',
                            borderRadius: '6px',
                            fontSize: '13px',
                            fontWeight: '500',
                            cursor: 'pointer'
                          }}
                        >
                          📖 Learn More
                        </button>
                        
                        <div style={{ display: 'flex', gap: '4px' }}>
                          <a 
                            href={getPreserveUrl(preserve)}
                            target="_blank"
                            rel="noopener noreferrer"
                            style={{
                              flex: 1,
                              padding: '6px 8px',
                              backgroundColor: '#10b981',
                              color: 'white',
                              textDecoration: 'none',
                              borderRadius: '4px',
                              fontSize: '11px',
                              textAlign: 'center',
                              fontWeight: '500'
                            }}
                          >
                            🔗 Full Page
                          </a>
                          
                          <button
                            onClick={() => {
                              navigator.clipboard.writeText(getMapDeepLink(preserve));
                              alert('Map link copied to clipboard!');
                            }}
                            style={{
                              flex: 1,
                              padding: '6px 8px',
                              backgroundColor: '#8b5cf6',
                              color: 'white',
                              border: 'none',
                              borderRadius: '4px',
                              fontSize: '11px',
                              cursor: 'pointer',
                              fontWeight: '500'
                            }}
                          >
                            📋 Share
                          </button>
                        </div>
                      </div>
                    </div>
                  </Popup>
                </Marker>
              );
            })}
            
            {/* GeoJSON Layers */}
            {visibleLayers.map((layer, i) => (
              <GeoJSON
                key={`geojson-${layer.preserveId}-${layer.type}-${i}`}
                data={layer.data}
                style={layerStyles[layer.type] || layerStyles.boundary}
                pointToLayer={(feature, latlng) => {
                  if (['structures', 'parking'].includes(layer.type)) return L.circleMarker(latlng, layerStyles[layer.type]);
                  return L.marker(latlng);
                }}
                onEachFeature={(feature, leafletLayer) => {
                  if (layer.type === 'structures' && feature.properties) {
                    const props = feature.properties;
                    let popup = `<strong>${layer.preserveTitle}</strong><br/>`;
                    if (props.type) popup += `<strong>Type:</strong> ${props.type}<br/>`;
                    if (props.name) popup += `<strong>Name:</strong> ${props.name}<br/>`;
                    if (props.description) popup += `<strong>Description:</strong> ${props.description}<br/>`;
                    if (props.accessible !== undefined) popup += `<strong>Accessible:</strong> ${props.accessible ? 'Yes' : 'No'}`;
                    leafletLayer.bindPopup(popup);
                  }
                  if (layer.type === 'parking') {
                    leafletLayer.bindPopup(`<strong>${layer.preserveTitle}</strong><br/><strong>Parking Area</strong>`);
                  }
                }}
              />
            ))}
          </MapContainer>
        </div>
      ) : (
        <div>
          <PreserveListView preserves={filteredPreserves} />
        </div>
      )}

      {/* Detail Modal - Simple version for now */}
      {showDetailModal && selectedPreserve && (
        <div 
          style={{
            position: 'fixed',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: 'rgba(0, 0, 0, 0.75)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 1000,
            padding: '20px'
          }}
          onClick={handleModalClose}
        >
          <div 
            style={{
              backgroundColor: 'white',
              borderRadius: '12px',
              padding: '24px',
              maxWidth: '600px',
              width: '100%',
              maxHeight: '80vh',
              overflow: 'auto'
            }}
            onClick={(e) => e.stopPropagation()}
          >
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '16px' }}>
              <h2 style={{ margin: 0, fontSize: '24px', color: '#1f2937' }}>
                {selectedPreserve.title.rendered}
              </h2>
              <button 
                onClick={handleModalClose}
                style={{
                  background: 'none',
                  border: 'none',
                  fontSize: '24px',
                  cursor: 'pointer',
                  padding: '4px',
                  color: '#6b7280'
                }}
              >
                ✕
              </button>
            </div>
            
            <div style={{ color: '#374151', lineHeight: '1.6' }}>
              <p>This is a placeholder for the detailed preserve modal. You can implement the full modal component here or replace this with your PreserveDetailModal component.</p>
              
              <div style={{ marginTop: '16px', display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                <a 
                  href={getPreserveUrl(selectedPreserve)}
                  target="_blank"
                  rel="noopener noreferrer"
                  style={{
                    padding: '8px 16px',
                    backgroundColor: '#3b82f6',
                    color: 'white',
                    textDecoration: 'none',
                    borderRadius: '6px',
                    fontSize: '14px'
                  }}
                >
                  🔗 View Full Page
                </a>
                
                <button
                  onClick={() => {
                    navigator.clipboard.writeText(getMapDeepLink(selectedPreserve));
                    alert('Map link copied!');
                  }}
                  style={{
                    padding: '8px 16px',
                    backgroundColor: '#10b981',
                    color: 'white',
                    border: 'none',
                    borderRadius: '6px',
                    fontSize: '14px',
                    cursor: 'pointer'
                  }}
                >
                  📋 Share Map Link
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      <style jsx>{`
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
}