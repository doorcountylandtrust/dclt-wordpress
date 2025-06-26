import React from 'react';
import { createRoot } from 'react-dom/client';
import PreserveMap from './PreserveMap';
import PreserveFilters from './PreserveFilters';

function PreserveExplorerApp() {
  const [filters, setFilters] = React.useState({});
  const [preserves, setPreserves] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  // Fetch preserves data on mount
  React.useEffect(() => {
    setLoading(true);
    fetch(preserveExplorerData.apiUrl)
      .then((res) => res.json())
      .then((data) => {
        console.log('✅ Preserves loaded in main app:', data);
        setPreserves(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error('❌ Failed to fetch preserves in main app:', err);
        setLoading(false);
      });
  }, []);

  return (
    <div className="preserve-explorer-app">
      <div className="preserve-explorer-header">
      
      </div>

      {/* Filters Component */}
      <PreserveFilters 
        preserves={preserves}
        filters={filters}
        onFiltersChange={setFilters}
      />

      {/* Map Component */}
      <PreserveMap 
        preserves={preserves}
        filters={filters}
      />

      <style jsx>{`
        .preserve-explorer-app {
          position: relative;
          width: 100%;
          height: 100vh;
          overflow: hidden;
        }

        .preserve-explorer-header {
          position: absolute;
          top: 20px;
          left: 20px;
          z-index: 500;
          background: rgba(255, 255, 255, 0.95);
          padding: 16px;
          border-radius: 8px;
          box-shadow: 0 2px 8px rgba(0,0,0,0.1);
          max-width: 400px;
        }

        .preserve-explorer-header h1 {
          margin: 0 0 8px 0;
          font-size: 18px;
          color: #1f2937;
        }

        .preserve-explorer-header h2 {
          margin: 0 0 8px 0;
          font-size: 16px;
          color: #dc2626;
        }

        .preserve-explorer-header p {
          margin: 0;
          font-size: 14px;
          color: #6b7280;
        }

        @media (max-width: 768px) {
          .preserve-explorer-header {
            position: relative;
            top: 0;
            left: 0;
            max-width: none;
            margin-bottom: 16px;
          }
          
          .preserve-explorer-app {
            height: auto;
            overflow: visible;
          }
        }
      `}</style>
    </div>
  );
}

// Mount to DOM
const rootElement = document.getElementById('preserve-explorer-root');
if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<PreserveExplorerApp />);
}