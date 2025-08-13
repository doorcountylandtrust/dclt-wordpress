import React, { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup, GeoJSON } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import PreserveListView from './PreserveListView';
import PhotoGallery from './PhotoGallery';

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
    <div className={`preserve-map-wrapper ${mode === 'preserve-focused' ? 'preserve-focused-layout' : ''}`}>
      
      {/* Discovery Mode: Show view toggle and normal layout */}
      {mode === 'discovery' && (
        <>
          {/* View Toggle */}
          <div style={{ display: 'flex', justifyContent: 'flex-end', padding: '0.5rem 0' }}>
            <button 
              onClick={() => setViewMode('map')} 
              className={`mr-2 px-4 py-2 rounded-md border-0 cursor-pointer transition-colors ${
                viewMode === 'map' 
                  ? 'bg-blue-600 text-white' 
                  : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
              }`}
            >
              🗺 Map View
            </button>
            <button 
  onClick={() => setViewMode('list')} 
  className={`px-4 py-2 rounded-md border-0 cursor-pointer transition-colors ${
    viewMode === 'list' 
      ? 'bg-blue-600 text-white' 
      : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
  }`}
>
  📋 List View
</button>
          </div>

          {/* Discovery Mode Content */}
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
                
                {/* Discovery Mode Markers */}
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
                          
                          {/* Single View Details Button */}
                          <button 
                            onClick={() => {
                              window.location.href = getPreserveUrl(preserve);
                            }}
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
                            📖 View Details
                          </button>
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

          {/* Discovery Mode Modal */}
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
                  <p>Discovery mode modal - quick preview</p>
                  
                  <div style={{ marginTop: '16px', display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                    <a 
                      href={getPreserveUrl(selectedPreserve)}
                      style={{
                        padding: '8px 16px',
                        backgroundColor: '#3b82f6',
                        color: 'white',
                        textDecoration: 'none',
                        borderRadius: '6px',
                        fontSize: '14px'
                      }}
                    >
                      🔗 View Full Details
                    </a>
                  </div>
                </div>
              </div>
            </div>
          )}
        </>
      )}

      {/* Preserve-Focused Mode: New Side Panel Layout */}
      {mode === 'preserve-focused' && (
        <div className="preserve-focused-content">
          
          {/* Map Container - Left side on desktop, top on mobile */}
          <div className="preserve-map-container">
            <MapContainer 
              center={selectedPreserve && selectedPreserve.meta ? 
                [parseFloat(selectedPreserve.meta._preserve_lat || selectedPreserve.meta.lat || 44.8), 
                 parseFloat(selectedPreserve.meta._preserve_lng || selectedPreserve.meta.lng || -87.4)] : 
                [44.8, -87.4]
              }
              zoom={selectedPreserve ? 15 : 10}
              scrollWheelZoom={true} 
              style={{ height: '100%', width: '100%' }}
              whenCreated={setMapInstance}
            >
              <TileLayer 
                attribution='&copy; OpenStreetMap contributors' 
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" 
              />
              
              {/* Single preserve marker */}
              {selectedPreserve && selectedPreserve.meta && (
                <Marker position={[
                  parseFloat(selectedPreserve.meta._preserve_lat || selectedPreserve.meta.lat),
                  parseFloat(selectedPreserve.meta._preserve_lng || selectedPreserve.meta.lng)
                ]}>
                  <Popup>
                    <div>
                      <strong>{selectedPreserve.title.rendered}</strong>
                    </div>
                  </Popup>
                </Marker>
              )}
              
              {/* Show only this preserve's GeoJSON layers */}
              {visibleLayers
                .filter(layer => !selectedPreserve || layer.preserveId === selectedPreserve.id)
                .map((layer, i) => (
                  <GeoJSON
                    key={`focused-geojson-${layer.preserveId}-${layer.type}-${i}`}
                    data={layer.data}
                    style={layerStyles[layer.type] || layerStyles.boundary}
                    pointToLayer={(feature, latlng) => {
                      if (['structures', 'parking'].includes(layer.type)) return L.circleMarker(latlng, layerStyles[layer.type]);
                      return L.marker(latlng);
                    }}
                  />
                ))
              }
            </MapContainer>
          </div>

          {/* Detail Panel - Right side on desktop, bottom on mobile */}
          <div className="preserve-detail-panel">
            {selectedPreserve && (
              <div className="preserve-detail-content">
                
               {/* Breadcrumb Navigation */}
      <div style={{ 
        marginBottom: '20px', 
        paddingBottom: '12px', 
        borderBottom: '1px solid #e5e7eb',
        fontSize: '14px',
        color: '#6b7280'
      }}>
        <a 
          href="/preserve-explorer/" 
          style={{ 
            color: '#3b82f6', 
            textDecoration: 'none',
            fontWeight: '500'
          }}
        >
          🗺️ Preserve Explorer
        </a>
        <span style={{ margin: '0 8px', color: '#d1d5db' }}>›</span>
        <span style={{ color: '#1f2937', fontWeight: '500' }}>
          {selectedPreserve.title.rendered}
        </span>
      </div>

      {/* Hero Section */}
      <div className="preserve-hero-section">
        <h1 style={{ fontSize: '1.5rem', fontWeight: 'bold', color: '#1f2937', margin: '0 0 8px 0' }}>
        
          {selectedPreserve.title.rendered}
        </h1>
      </div>



                {/* Photo Gallery */}
              {(() => {
                  // Get gallery data from the correct location
                  const galleryData = selectedPreserve?._preserve_gallery || 
                                    selectedPreserve?.meta?._preserve_gallery || 
                                    window.preservePageData?.preserveData?.meta?._preserve_gallery || 
                                    [];
                  
                  return galleryData && galleryData.length > 0 && (
                    <div className="preserve-section">
                      <h3 className="section-title">📸 Photo Gallery</h3>
                      <PhotoGallery photos={galleryData} />
                    </div>
                  );
                })()}

                {/* Stats Cards */}
                <div className="preserve-stats-grid">
                  {selectedPreserve.meta._preserve_acres && (
                    <div style={{ textAlign: 'center', padding: '12px', background: '#f0f9ff', borderRadius: '8px', border: '1px solid #e0f2fe' }}>
                      <div style={{ fontSize: '1.5rem', fontWeight: 'bold', color: '#0369a1' }}>
                        {selectedPreserve.meta._preserve_acres}
                      </div>
                      <div style={{ fontSize: '0.75rem', color: '#6b7280' }}>acres</div>
                    </div>
                  )}
                  
                  {selectedPreserve.meta._preserve_trail_length && (
                    <div style={{ textAlign: 'center', padding: '12px', background: '#f0fdf4', borderRadius: '8px', border: '1px solid #dcfce7' }}>
                      <div style={{ fontSize: '1.5rem', fontWeight: 'bold', color: '#059669' }}>
                        {selectedPreserve.meta._preserve_trail_length}
                      </div>
                      <div style={{ fontSize: '0.75rem', color: '#6b7280' }}>miles</div>
                    </div>
                  )}
                  
                  {selectedPreserve.meta._preserve_filter_difficulty && (
                    <div style={{ textAlign: 'center', padding: '12px', background: '#fefce8', borderRadius: '8px', border: '1px solid #fef3c7' }}>
                      <div style={{ fontSize: '1rem', fontWeight: 'bold', color: '#a16207', textTransform: 'capitalize' }}>
                        {selectedPreserve.meta._preserve_filter_difficulty.join(', ')}
                      </div>
                      <div style={{ fontSize: '0.75rem', color: '#6b7280' }}>difficulty</div>
                    </div>
                  )}
                </div>

                {/* Description Section */}
                {selectedPreserve.content?.rendered && (
                  <div className="preserve-section">
                    <h3 className="section-title">🏞️ About This Preserve</h3>
                    <div 
                      style={{ fontSize: '14px', lineHeight: '1.6', color: '#374151' }}
                      dangerouslySetInnerHTML={{ __html: selectedPreserve.content.rendered }}
                    />
                  </div>
                )}

                {/* Location Section */}
                {selectedPreserve.meta._preserve_filter_region && (
                  <div className="preserve-section">
                    <h3 className="section-title">📍 Location</h3>
                    <p style={{ margin: 0, color: '#6b7280', fontSize: '14px' }}>
                      {selectedPreserve.meta._preserve_filter_region.join(', ')}, Door County, Wisconsin
                    </p>
                    {selectedPreserve.meta._preserve_lat && selectedPreserve.meta._preserve_lng && (
                      <a
                        href={`https://www.google.com/maps/dir/?api=1&destination=${selectedPreserve.meta._preserve_lat},${selectedPreserve.meta._preserve_lng}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        style={{
                          display: 'inline-block',
                          marginTop: '8px',
                          padding: '6px 12px',
                          backgroundColor: '#f59e0b',
                          color: 'white',
                          textDecoration: 'none',
                          borderRadius: '6px',
                          fontSize: '13px',
                          fontWeight: '500'
                        }}
                      >
                        🧭 Get Directions
                      </a>
                    )}
                  </div>
                )}

                {/* Actions */}
                <div style={{ marginTop: '24px', display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                  <button
                    onClick={() => {
                      const shareUrl = getShareUrl ? getShareUrl(selectedPreserve) : window.location.href;
                      navigator.clipboard.writeText(shareUrl);
                      alert('Preserve link copied to clipboard!');
                    }}
                    style={{
                      padding: '8px 16px',
                      backgroundColor: '#10b981',
                      color: 'white',
                      border: 'none',
                      borderRadius: '6px',
                      fontSize: '14px',
                      cursor: 'pointer',
                      fontWeight: '500'
                    }}
                  >
                    📋 Share
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
      )}

      {/* CSS Styles */}
      <style jsx>{`
        .preserve-map-wrapper {
          width: 100%;
        }

        .preserve-focused-layout .preserve-focused-content {
          display: grid;
          grid-template-areas: 
            "map panel";
          grid-template-columns: 1fr 400px;
          height: calc(100vh - 60px);
        }

        .preserve-map-container {
          grid-area: map;
        }

        .preserve-detail-panel {
          grid-area: panel;
          background: white;
          border-left: 1px solid #e5e7eb;
          overflow-y: auto;
        }

        .preserve-detail-content {
          padding: 20px;
        }

        .preserve-stats-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
          gap: 12px;
          margin-bottom: 24px;
        }

        .preserve-section {
          margin-bottom: 24px;
          padding-bottom: 24px;
          border-bottom: 1px solid #f3f4f6;
        }

        .preserve-section:last-child {
          border-bottom: none;
          margin-bottom: 0;
        }

        .section-title {
          font-size: 1.1rem;
          font-weight: 600;
          color: #1f2937;
          margin: 0 0 12px 0;
          display: flex;
          align-items: center;
          gap: 8px;
        }

        /* Mobile Layout */
        @media (max-width: 768px) {
          .preserve-focused-layout .preserve-focused-content {
            grid-template-areas: 
              "map"
              "panel";
            grid-template-columns: 1fr;
            grid-template-rows: 40vh 1fr;
            height: calc(100vh - 60px);
          }
          
          .preserve-detail-panel {
            border-left: none;
            border-top: 1px solid #e5e7eb;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
          }
          
          .preserve-detail-panel::before {
            content: '';
            width: 36px;
            height: 4px;
            background: #d1d5db;
            border-radius: 2px;
            margin: 8px auto 16px;
            display: block;
          }

          .preserve-detail-content {
            padding: 16px;
          }
          
          .preserve-stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
            gap: 8px;
          }
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
}