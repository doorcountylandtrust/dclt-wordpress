import React, { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup, GeoJSON } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix Leaflet default marker paths
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

// 🔁 Helper function to normalize various GeoJSON shapes
function normalizeGeoJson(input) {
  if (!input || typeof input !== 'object') return null;

  if (input.type === 'FeatureCollection') return input;

  if (input.type === 'Feature') {
    return {
      type: 'FeatureCollection',
      features: [input],
    };
  }

  if (
    input.type === 'Polygon' ||
    input.type === 'LineString' ||
    input.type === 'MultiPolygon' ||
    input.type === 'Point'
  ) {
    return {
      type: 'FeatureCollection',
      features: [
        {
          type: 'Feature',
          geometry: input,
          properties: {},
        },
      ],
    };
  }

  console.warn('Unrecognized GeoJSON structure:', input);
  return null;
}

// 🎯 Filter matching helper
function preserveMatchesFilters(preserve, filters) {
  if (!filters || Object.keys(filters).length === 0) return true;

  return Object.entries(filters).every(([filterType, selectedValues]) => {
    if (!selectedValues || selectedValues.length === 0) return true;
    
    const metaKey = `_preserve_filter_${filterType}`;
    const preserveValues = preserve.meta[metaKey] || [];
    
    // Handle both single values and arrays from WordPress meta
    const preserveArray = Array.isArray(preserveValues) ? preserveValues : [preserveValues];
    
    // Check if any selected filter values match the preserve's values
    return selectedValues.some(selectedValue => 
      preserveArray.includes(selectedValue)
    );
  });
}

// 🎨 Layer styling configuration
const layerStyles = {
  boundary: {
    color: '#22c55e',
    weight: 2,
    fillOpacity: 0.1,
    fillColor: '#22c55e'
  },
  trail: {
    color: '#ef4444',
    weight: 3,
    opacity: 0.8,
    dashArray: '5, 5'
  },
  accessible_trails: {
    color: '#3b82f6',
    weight: 4,
    opacity: 0.9
  },
  boardwalk: {
    color: '#8b5cf6',
    weight: 5,
    opacity: 0.8
  },
  structures: {
    radius: 8,
    fillColor: '#f59e0b',
    color: '#d97706',
    weight: 2,
    opacity: 1,
    fillOpacity: 0.8
  },
  parking: {
    radius: 10,
    fillColor: '#10b981',
    color: '#059669',
    weight: 2,
    opacity: 1,
    fillOpacity: 0.9
  }
};

export default function PreserveMap({ filters, layerToggles = {}, preserves = [] }) {
  const [geoJsonLayers, setGeoJsonLayers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Only process if we have preserves data
    if (!preserves || preserves.length === 0) {
      setLoading(true);
      return;
    }

    console.log('✅ PreserveMap received preserves:', preserves);
    setLoading(true);

    const processPreserves = async () => {
      const geoJsons = await Promise.all(
        preserves.map(async (preserve) => {
          const layers = [];
          const lat = preserve.meta._preserve_lat;
          const lng = preserve.meta._preserve_lng;
          
          console.log(`📍 ${preserve.title.rendered}`);
          console.log(`   → Lat/Lng: ${lat}, ${lng}`);

          // All the different layer types to fetch
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
                  console.log(`   → ${type} GeoJSON loaded`);
                  layers.push({
                    preserveId: preserve.id,
                    preserveTitle: preserve.title.rendered,
                    type: type,
                    data: normalized,
                  });
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
  }, [preserves]); // Only depend on preserves, not fetch from API

  // Filter preserves based on active filters
  const filteredPreserves = preserves.filter(preserve => 
    preserveMatchesFilters(preserve, filters)
  );

  // Filter layers based on preserve filtering and layer toggles
  const visibleLayers = geoJsonLayers.filter(layer => {
    // First check if the preserve should be visible based on filters
    const preserve = preserves.find(p => p.id === layer.preserveId);
    if (!preserve || !preserveMatchesFilters(preserve, filters)) {
      return false;
    }
    
    // Then check if this layer type should be visible based on toggles
    if (layerToggles.hasOwnProperty(layer.type)) {
      return layerToggles[layer.type];
    }
    
    // Default visibility (show core layers, hide optional ones)
    const defaultVisible = ['boundary', 'trail'].includes(layer.type);
    return defaultVisible;
  });

  console.log('Active filters:', filters);
  console.log('Layer toggles:', layerToggles);
  console.log(`Showing ${filteredPreserves.length} of ${preserves.length} preserves`);
  console.log(`Showing ${visibleLayers.length} of ${geoJsonLayers.length} layers`);

  if (loading) {
    return (
      <div style={{ 
        height: '600px', 
        width: '100%', 
        display: 'flex', 
        alignItems: 'center', 
        justifyContent: 'center',
        backgroundColor: '#f3f4f6'
      }}>
        <div style={{ textAlign: 'center' }}>
          <div style={{ 
            width: '40px', 
            height: '40px', 
            border: '4px solid #e5e7eb',
            borderTop: '4px solid #3b82f6',
            borderRadius: '50%',
            animation: 'spin 1s linear infinite',
            margin: '0 auto 16px'
          }}></div>
          <p style={{ color: '#6b7280', margin: 0 }}>Loading preserves...</p>
        </div>
      </div>
    );
  }

  return (
    <div style={{ height: '600px', width: '100%' }}>
      <MapContainer
        center={[44.8, -87.4]}
        zoom={10}
        scrollWheelZoom={true}
        style={{ height: '100%', width: '100%' }}
      >
        <TileLayer
          attribution='&copy; OpenStreetMap contributors'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />

        {/* Preserve markers - only show filtered preserves */}
        {filteredPreserves.map((preserve) => {
          const lat = parseFloat(preserve.meta._preserve_lat);
          const lng = parseFloat(preserve.meta._preserve_lng);
          if (!lat || !lng) return null;

          return (
            <Marker key={`marker-${preserve.id}`} position={[lat, lng]}>
              <Popup>
                <div style={{ minWidth: '200px' }}>
                  <strong style={{ fontSize: '16px', display: 'block', marginBottom: '8px' }}>
                    {preserve.title.rendered}
                  </strong>
                  
                  <div style={{ fontSize: '14px', lineHeight: '1.4' }}>
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
                </div>
              </Popup>
            </Marker>
          );
        })}

        {/* GeoJSON layers - only show visible layers */}
        {visibleLayers.map((layer, index) => {
          if (!layer?.data) {
            console.warn(
              `⛔ Skipping empty GeoJSON layer: ${layer?.type} for preserve ${layer?.preserveId}`
            );
            return null;
          }

          const style = layerStyles[layer.type] || layerStyles.boundary;

          return (
            <GeoJSON
              key={`geojson-${layer.preserveId}-${layer.type}-${index}`}
              data={layer.data}
              style={style}
              pointToLayer={(feature, latlng) => {
                // Handle point features (structures, parking)
                if (layer.type === 'structures' || layer.type === 'parking') {
                  return L.circleMarker(latlng, style);
                }
                return L.marker(latlng);
              }}
              onEachFeature={(feature, leafletLayer) => {
                // Add popups for structure points
                if (layer.type === 'structures' && feature.properties) {
                  const props = feature.properties;
                  let popupContent = `<strong>${layer.preserveTitle}</strong><br/>`;
                  
                  if (props.type) {
                    popupContent += `<strong>Type:</strong> ${props.type.replace('_', ' ')}<br/>`;
                  }
                  if (props.name) {
                    popupContent += `<strong>Name:</strong> ${props.name}<br/>`;
                  }
                  if (props.description) {
                    popupContent += `<strong>Description:</strong> ${props.description}<br/>`;
                  }
                  if (props.accessible !== undefined) {
                    popupContent += `<strong>Accessible:</strong> ${props.accessible ? 'Yes' : 'No'}`;
                  }
                  
                  leafletLayer.bindPopup(popupContent);
                }
                
                // Add popups for parking areas
                if (layer.type === 'parking' && feature.properties) {
                  leafletLayer.bindPopup(`
                    <strong>${layer.preserveTitle}</strong><br/>
                    <strong>Parking Area</strong>
                    ${feature.properties.name ? `<br/><strong>Name:</strong> ${feature.properties.name}` : ''}
                  `);
                }
              }}
            />
          );
        })}
      </MapContainer>
      
      <style jsx>{`
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
}