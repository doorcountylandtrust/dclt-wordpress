import React from 'react';
import { createRoot } from 'react-dom/client';
import PreserveMap from './PreserveMap';

function PreserveExplorerApp() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold">🚀 Preserve Explorer is mounted!</h1>
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