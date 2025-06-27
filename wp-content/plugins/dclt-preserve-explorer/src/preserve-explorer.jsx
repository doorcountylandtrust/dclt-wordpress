import React from 'react';
import { createRoot } from 'react-dom/client';
import PreserveMap from './PreserveMap';
import PreserveFilters from './PreserveFilters';

function PreserveExplorerApp() {
  const [filters, setFilters] = React.useState({});
  const [preserves, setPreserves] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  // NEW: Get initial state from server-side data
  const getInitialState = () => {
    const serverData = window.preservePageData || {};
    
    if (serverData.isPreservePage && serverData.preserveSlug) {
      return {
        mode: 'preserve-focused',
        selectedPreserve: serverData.preserveSlug,
        showDetailPanel: true,
        initialPreserveData: serverData.preserveData
      };
    }
    
    // Check URL params for discovery mode
    const urlParams = new URLSearchParams(window.location.search);
    const preserveParam = urlParams.get('preserve');
    
    if (preserveParam) {
      return {
        mode: 'discovery',
        selectedPreserve: preserveParam,
        showDetailModal: true
      };
    }
    
    return {
      mode: 'discovery',
      selectedPreserve: null,
      showDetailModal: false
    };
  };

  const [appState, setAppState] = React.useState(getInitialState());

  // Fetch preserves data on mount
  React.useEffect(() => {
    setLoading(true);
    fetch(preserveExplorerData.apiUrl)
      .then((res) => res.json())
      .then((data) => {
        console.log('✅ Preserves loaded in main app:', data);
        
        // If we have initial preserve data from server, make sure it's in the list
        if (appState.initialPreserveData) {
          const serverPreserve = appState.initialPreserveData;
          const existingPreserve = data.find(p => p.id === serverPreserve.id);
          
          if (!existingPreserve) {
            // Add server preserve data to the list
            const formattedPreserve = {
              id: serverPreserve.id,
              title: { rendered: serverPreserve.title },
              content: { rendered: serverPreserve.content },
              excerpt: { rendered: serverPreserve.excerpt },
              slug: serverPreserve.slug,
              post_name: serverPreserve.slug,
              meta: serverPreserve.meta
            };
            data.push(formattedPreserve);
          }
        }
        
        setPreserves(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error('❌ Failed to fetch preserves in main app:', err);
        setLoading(false);
      });
  }, []);

  // Handle preserve selection with smart URL updating
  const handlePreserveSelect = (preserve) => {
    const preserveSlug = preserve.slug || preserve.post_name || preserve.title.rendered.toLowerCase().replace(/\s+/g, '-');
    
    if (appState.mode === 'preserve-focused') {
      // Update URL to new preserve
      const newUrl = `/preserve/${preserveSlug}/`;
      window.history.pushState({}, '', newUrl);
      
      setAppState({
        ...appState,
        selectedPreserve: preserveSlug,
        initialPreserveData: preserve
      });
    } else {
      // Discovery mode - use query params
      const newUrl = new URL(window.location);
      newUrl.searchParams.set('preserve', preserveSlug);
      window.history.replaceState({}, '', newUrl);
      
      setAppState({
        ...appState,
        selectedPreserve: preserveSlug,
        showDetailModal: true
      });
    }
  };

  // Handle back to explorer from preserve-focused mode
  const handleBackToExplorer = () => {
    window.history.pushState({}, '', '/preserve-explorer/');
    setAppState({
      mode: 'discovery',
      selectedPreserve: null,
      showDetailModal: false,
      showDetailPanel: false
    });
  };

  // Generate share URLs (always preserve-specific for better SEO)
  const getShareUrl = (preserve) => {
    const preserveSlug = preserve.slug || preserve.post_name || preserve.title.rendered.toLowerCase().replace(/\s+/g, '-');
    return `${window.location.origin}/preserve/${preserveSlug}/`;
  };

  return (
    <div className="preserve-explorer-app">
      
      {/* Conditional header based on mode */}
      {appState.mode === 'preserve-focused' ? (
        <PreserveFocusedHeader 
          onBackToExplorer={handleBackToExplorer}
          shareUrl={appState.initialPreserveData ? getShareUrl(appState.initialPreserveData) : ''}
        />
      ) : (
        <div className="preserve-explorer-header">
          {/* Your existing header content - keeping it simple for now */}
        </div>
      )}

      {/* Filters only in discovery mode */}
      {appState.mode === 'discovery' && (
        <PreserveFilters 
          preserves={preserves}
          filters={filters}
          onFiltersChange={setFilters}
        />
      )}

      {/* Map Component */}
      <PreserveMap 
        preserves={preserves}
        filters={appState.mode === 'preserve-focused' ? {} : filters} // No filters in preserve-focused mode
        mode={appState.mode}
        initialSelectedPreserve={appState.selectedPreserve}
        onPreserveSelect={handlePreserveSelect}
        getShareUrl={getShareUrl}
        initialPreserveData={appState.initialPreserveData}
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

        /* Adjust layout for preserve-focused mode */
        .preserve-explorer-app[data-mode="preserve-focused"] {
          padding-top: 60px; /* Space for fixed header */
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
          
          .preserve-explorer-app[data-mode="preserve-focused"] {
            padding-top: 0;
          }
        }
      `}</style>
    </div>
  );
}

// Header component for preserve-focused mode
function PreserveFocusedHeader({ onBackToExplorer, shareUrl }) {
  return (
    <div style={{
      position: 'fixed',
      top: 0,
      left: 0,
      right: 0,
      background: 'rgba(255, 255, 255, 0.95)',
      borderBottom: '1px solid #e5e7eb',
      padding: '12px 20px',
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center',
      zIndex: 1000,
      backdropFilter: 'blur(10px)'
    }}>
      <button 
        onClick={onBackToExplorer}
        style={{
          background: 'none',
          border: 'none',
          color: '#3b82f6',
          fontSize: '14px',
          fontWeight: '500',
          cursor: 'pointer',
          display: 'flex',
          alignItems: 'center',
          gap: '4px'
        }}
      >
        ← Explore All Preserves
      </button>
      
      <div style={{ display: 'flex', gap: '8px' }}>
        {shareUrl && (
          <button 
            onClick={() => {
              navigator.clipboard.writeText(shareUrl);
              alert('Preserve link copied to clipboard!');
            }}
            style={{
              background: '#3b82f6',
              color: 'white',
              border: 'none',
              borderRadius: '6px',
              padding: '6px 12px',
              fontSize: '13px',
              fontWeight: '500',
              cursor: 'pointer'
            }}
          >
            📱 Share
          </button>
        )}
      </div>
    </div>
  );
}

// Mount to DOM
const rootElement = document.getElementById('preserve-explorer-root');
if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<PreserveExplorerApp />);
}