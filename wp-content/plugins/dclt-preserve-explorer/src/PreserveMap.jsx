import React, { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Default Leaflet marker fix for missing icons in some bundlers
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

const PreserveMap = () => {
  const [preserves, setPreserves] = useState([]);

  useEffect(() => {
    fetch(preserveExplorerData.apiUrl)
      .then((res) => res.json())
      .then((data) => setPreserves(data))
      .catch((err) => console.error('Failed to fetch preserves', err));
  }, []);

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

      {preserves.map((preserve) => {
        const lat = parseFloat(preserve.meta._preserve_lat);
        const lng = parseFloat(preserve.meta._preserve_lng);
        if (!lat || !lng) return null;

        return (
          <Marker key={preserve.id} position={[lat, lng]}>
            <Popup>
              <strong>{preserve.title.rendered}</strong>
              <br />
              Acres: {preserve.meta._preserve_acres || '—'}
            </Popup>
          </Marker>
        );
      })}
    </MapContainer>
  </div>
);
};

export default PreserveMap;