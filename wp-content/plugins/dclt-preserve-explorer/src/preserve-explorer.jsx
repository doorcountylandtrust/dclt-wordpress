import React from 'react';
import { createRoot } from 'react-dom/client';
import PreserveMap from './PreserveMap';

console.log('building preserve-explorer.jsx');


function PreserveExplorerApp() {
  return (
    <div className="p-6">
      
      <h1 className="text-2xl font-bold">🚀 Preserve Explorer is mounted!</h1>
      <h2 className="text-4xl font-bold text-red-600 mb-4">Tailwind is Working</h2>
      <p className="text-gray-700 mt-2">Now let’s add Leaflet and filters.</p>
      <PreserveMap />
    </div>
  );
}

// Mount to DOM
const rootElement = document.getElementById('preserve-explorer-root');
if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<PreserveExplorerApp />);
}