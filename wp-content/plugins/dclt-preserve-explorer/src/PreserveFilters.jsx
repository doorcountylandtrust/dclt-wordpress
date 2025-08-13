import React, { useState, useMemo, useEffect } from 'react';
import { trackEvent } from './utils/analytics';

export default function PreserveFilters({ preserves = [], filters = {}, onFiltersChange }) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [filterOptions, setFilterOptions] = useState({ primary: {}, secondary: {} });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Fetch dynamic filter options from WordPress
  useEffect(() => {
    const fetchFilterOptions = async () => {
      try {
        setLoading(true);
        
        // Use the global preserveExplorerData passed from WordPress
        const filterOptionsUrl = window.preserveExplorerData?.filterOptionsUrl || '/wp-json/dclt/v1/filter-options';
        
        console.log('Fetching filter options from:', filterOptionsUrl);
        
        const response = await fetch(filterOptionsUrl);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Filter options received:', data);
        
        // Validate the response structure
        if (data && typeof data === 'object') {
          if (data.primary || data.secondary) {
            // Data is already in the expected format
            setFilterOptions(data);
          } else {
            // Fallback: treat response as flat structure and categorize
            const primary = {};
            const secondary = {};
            const primaryKeys = ['region', 'activity', 'accessibility', 'difficulty'];
            
            Object.entries(data || {}).forEach(([key, value]) => {
              if (primaryKeys.includes(key)) {
                primary[key] = value;
              } else {
                secondary[key] = value;
              }
            });
            
            setFilterOptions({ primary, secondary });
          }
        } else {
          throw new Error('Invalid filter options data received');
        }
        
        setError(null);
      } catch (err) {
        console.error('Failed to fetch filter options:', err);
        setError(err.message);
        
        // Fallback to empty structure
        setFilterOptions({ primary: {}, secondary: {} });
      } finally {
        setLoading(false);
      }
    };

    fetchFilterOptions();
  }, []);

  // Combine primary and secondary filters for stats calculation
  const allFilters = useMemo(() => {
    return { ...filterOptions.primary, ...filterOptions.secondary };
  }, [filterOptions]);

  // Calculate filter stats for all filters
  const filterStats = useMemo(() => {
    const stats = {};
    
    Object.keys(allFilters).forEach(filterType => {
      stats[filterType] = {};
      
      const filterDef = allFilters[filterType];
      if (filterDef && filterDef.options) {
        Object.keys(filterDef.options).forEach(optionKey => {
          const count = preserves.filter(preserve => {
            if (!preserve?.meta) return false;
            const preserveFilters = preserve.meta[`_preserve_filter_${filterType}`] || [];
            const preserveArray = Array.isArray(preserveFilters) ? preserveFilters : [preserveFilters];
            return preserveArray.includes(optionKey);
          }).length;
          
          stats[filterType][optionKey] = {
            count,
            available: count > 0,
            selected: filters[filterType]?.includes(optionKey) || false
          };
        });
      }
    });
    
    return stats;
  }, [preserves, filters, allFilters]);

  // Handle filter changes
  const handleFilterChange = (filterType, optionKey, checked) => {
    if (!onFiltersChange) return;
    
    // Analytics tracking
    trackEvent('Filter Used', {
      type: filterType,
      value: optionKey,
      action: checked ? 'add' : 'remove'
    });
    
    const currentFilters = filters[filterType] || [];
    let newFilters;
    
    if (checked) {
      newFilters = [...currentFilters, optionKey];
    } else {
      newFilters = currentFilters.filter(f => f !== optionKey);
    }
    
    onFiltersChange({
      ...filters,
      [filterType]: newFilters
    });
  };

  // Clear all filters
  const clearAllFilters = () => {
    trackEvent('Filters Cleared', { total_filters: Object.values(filters).reduce((sum, filterArray) => sum + (filterArray?.length || 0), 0) });
    onFiltersChange({});
  };

  // Count active filters
  const totalActiveFilters = Object.values(filters).reduce((sum, filterArray) => 
    sum + (filterArray?.length || 0), 0
  );

  // Count filtered preserves
  const filteredCount = preserves.filter(preserve => {
    return Object.entries(filters).every(([filterType, selectedValues]) => {
      if (!selectedValues || selectedValues.length === 0) return true;
      const preserveFilters = preserve.meta[`_preserve_filter_${filterType}`] || [];
      const preserveArray = Array.isArray(preserveFilters) ? preserveFilters : [preserveFilters];
      return selectedValues.some(value => preserveArray.includes(value));
    });
  }).length;

  // Show empty state
  const showEmptyState = totalActiveFilters > 0 && filteredCount === 0;

  // Loading state
  if (loading) {
    return (
      <div className="filter-loading">
        <div className="loading-spinner">⏳</div>
        <p>Loading filter options...</p>
        <style jsx>{`
          .filter-loading {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 800;
          }
          .loading-spinner {
            font-size: 24px;
            margin-bottom: 8px;
            animation: spin 2s linear infinite;
          }
          @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
          }
        `}</style>
      </div>
    );
  }

  // Error state
  if (error) {
    return (
      <div className="filter-error">
        <div className="error-icon">⚠️</div>
        <p>Could not load filter options</p>
        <small>{error}</small>
        <button onClick={() => window.location.reload()}>Retry</button>
        <style jsx>{`
          .filter-error {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            background: #fee;
            color: #c33;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #fcc;
            z-index: 800;
          }
          .error-icon {
            font-size: 24px;
            margin-bottom: 8px;
          }
          .filter-error button {
            margin-top: 10px;
            padding: 5px 10px;
            background: #c33;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
          }
        `}</style>
      </div>
    );
  }

  // No filters available
  if (Object.keys(allFilters).length === 0) {
    return (
      <div className="no-filters">
        <div className="no-filters-icon">🔧</div>
        <p>No filter options configured</p>
        <style jsx>{`
          .no-filters {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 800;
          }
          .no-filters-icon {
            font-size: 24px;
            margin-bottom: 8px;
          }
        `}</style>
      </div>
    );
  }

  return (
    <>

   
      {/* Streamlined Primary Filter Chips - Only 3 Most Important */}
      <div className="primary-filters">
        <div className="filter-chips-container">
          {/* Accessibility - Most Important for DEI */}
          {(filterOptions.primary.accessibility || filterOptions.secondary.accessibility) && (
            <button
              className={`filter-chip ${filters.accessibility?.length > 0 ? 'active' : ''}`}
              onClick={() => {
                trackEvent('Filter Button Clicked', { button: 'accessibility' });
                setIsModalOpen('accessibility');
              }}
            >
              <span className="chip-icon">♿</span>
              <span className="chip-label">Accessible</span>
              {filters.accessibility?.length > 0 && (
                <span className="chip-count">{filters.accessibility.length}</span>
              )}
            </button>
          )}
          
          {/* Activities/Features - What can I do here? */}
          {(filterOptions.primary.activity || filterOptions.secondary.activity) && (
            <button
              className={`filter-chip ${filters.activity?.length > 0 ? 'active' : ''}`}
              onClick={() => {
                trackEvent('Filter Button Clicked', { button: 'activity' });
                setIsModalOpen('activity');
              }}
            >
              <span className="chip-icon">🏃‍♀️</span>
              <span className="chip-label">Activities</span>
              {filters.activity?.length > 0 && (
                <span className="chip-count">{filters.activity.length}</span>
              )}
            </button>
          )}
          
          {/* More Filters - Everything else */}
          <button
            className={`more-filters-chip ${
              Object.entries(filters).some(([key, values]) => 
                !['accessibility', 'activity'].includes(key) && values?.length > 0
              ) ? 'active' : ''
            }`}
            onClick={() => {
              trackEvent('Filter Button Clicked', { button: 'more' });
              setIsModalOpen('more');
            }}
          >
            <span className="chip-icon">⚙️</span>
            <span className="chip-label">More</span>
            {(() => {
              const moreFiltersCount = Object.entries(filters)
                .filter(([key, values]) => !['accessibility', 'activity'].includes(key) && values?.length > 0)
                .reduce((sum, [, values]) => sum + values.length, 0);
              return moreFiltersCount > 0 && (
                <span className="chip-count">{moreFiltersCount}</span>
              );
            })()}
          </button>
        </div>

        {/* Results Summary */}
        <div className="results-summary">
          {showEmptyState ? (
            <div className="empty-state">
              <span className="empty-icon">🤷‍♀️</span>
              <span className="empty-text">No preserves match these filters</span>
              <button className="adjust-filters-btn" onClick={clearAllFilters}>
                Clear filters
              </button>
            </div>
          ) : (
            <div className="results-count">
              {totalActiveFilters > 0 ? (
                <>
                  <span className="count-text">
                    <strong>{filteredCount}</strong> of {preserves.length} preserves
                  </span>
                  <button className="clear-btn" onClick={clearAllFilters}>
                    Clear all
                  </button>
                </>
              ) : (
                <span className="count-text">
                  <strong>{preserves.length}</strong> preserves to explore
                </span>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Enhanced Filter Panel */}
      {isModalOpen && (
        <div className="filter-panel-overlay" onClick={() => setIsModalOpen(false)}>
          <div className="filter-panel" onClick={(e) => e.stopPropagation()}>
            <div className="panel-header">
              <h3>
                {isModalOpen === 'more' ? 'More Filters' : 
                 isModalOpen === 'accessibility' ? '♿ Accessibility Options' :
                 isModalOpen === 'activity' ? '🏃‍♀️ Activities & Features' :
                 (filterOptions.primary[isModalOpen]?.label || filterOptions.secondary[isModalOpen]?.label || isModalOpen)
                }
              </h3>
              <button 
                className="panel-close"
                onClick={() => setIsModalOpen(false)}
              >
                ✕
              </button>
            </div>

            <div className="panel-content">
              {isModalOpen === 'more' ? (
                // Show all filters except accessibility and activity
                <>
                  <div className="more-filters-intro">
                    <p>Additional filters to help you find the perfect preserve:</p>
                  </div>
                  {Object.entries({...filterOptions.primary, ...filterOptions.secondary})
                    .filter(([key]) => !['accessibility', 'activity'].includes(key))
                    .map(([filterType, filterDef]) => (
                      <FilterSection
                        key={filterType}
                        filterType={filterType}
                        filterDef={filterDef}
                        filterStats={filterStats[filterType] || {}}
                        selectedValues={filters[filterType] || []}
                        onFilterChange={handleFilterChange}
                      />
                    ))
                  }
                </>
              ) : (
                // Show specific filter
                <FilterSection
                  filterType={isModalOpen}
                  filterDef={allFilters[isModalOpen]}
                  filterStats={filterStats[isModalOpen] || {}}
                  selectedValues={filters[isModalOpen] || []}
                  onFilterChange={handleFilterChange}
                />
              )}
            </div>
          </div>
        </div>
      )}

      <style jsx>{`
        .primary-filters {
          position: fixed;
          bottom: 20px;
          left: 20px;
          right: 20px;
          z-index: 800;
          pointer-events: none;
        }

        .filter-chips-container {
          display: flex;
          justify-content: center;
          gap: 12px;
          padding: 8px 0;
          pointer-events: auto;
        }

        .filter-chip, .more-filters-chip {
          display: flex;
          align-items: center;
          gap: 8px;
          background: white;
          border: 2px solid #e5e7eb;
          border-radius: 25px;
          padding: 12px 18px;
          font-size: 15px;
          font-weight: 600;
          color: #374151;
          cursor: pointer;
          transition: all 0.2s ease;
          white-space: nowrap;
          box-shadow: 0 4px 12px rgba(0,0,0,0.1);
          min-width: 100px;
          justify-content: center;
        }

        .filter-chip:hover, .more-filters-chip:hover {
          border-color: #3b82f6;
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        .filter-chip.active, .more-filters-chip.active {
          background: #3b82f6;
          border-color: #2563eb;
          color: white;
          transform: translateY(-1px);
        }

        .chip-icon {
          font-size: 18px;
        }

        .chip-label {
          font-weight: 600;
        }

        .chip-count {
          background: rgba(255,255,255,0.3);
          border-radius: 12px;
          padding: 2px 8px;
          font-size: 12px;
          font-weight: 700;
          margin-left: 4px;
          min-width: 20px;
          text-align: center;
        }

        .filter-chip.active .chip-count, .more-filters-chip.active .chip-count {
          background: rgba(255,255,255,0.3);
          color: white;
        }

        .results-summary {
          margin-top: 16px;
          text-align: center;
          pointer-events: auto;
        }

        .results-count {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 12px;
          background: rgba(255,255,255,0.95);
          backdrop-filter: blur(10px);
          border-radius: 16px;
          padding: 10px 20px;
          font-size: 14px;
          color: #374151;
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
          border: 1px solid rgba(255,255,255,0.2);
        }

        .count-text strong {
          color: #1f2937;
        }

        .clear-btn {
          background: #ef4444;
          color: white;
          border: none;
          border-radius: 8px;
          padding: 4px 12px;
          font-size: 12px;
          font-weight: 600;
          cursor: pointer;
          transition: background 0.2s ease;
        }

        .clear-btn:hover {
          background: #dc2626;
        }

        .empty-state {
          display: flex;
          align-items: center;
          gap: 8px;
          background: rgba(255,255,255,0.95);
          backdrop-filter: blur(10px);
          border-radius: 12px;
          padding: 12px 16px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .empty-icon {
          font-size: 16px;
        }

        .empty-text {
          flex: 1;
          font-size: 13px;
          color: #6b7280;
        }

        .adjust-filters-btn {
          background: #ef4444;
          color: white;
          border: none;
          border-radius: 6px;
          padding: 4px 8px;
          font-size: 12px;
          font-weight: 500;
          cursor: pointer;
        }

        .more-filters-intro {
          margin-bottom: 20px;
          padding: 16px;
          background: #f8fafc;
          border-radius: 12px;
          border: 1px solid #e2e8f0;
        }

        .more-filters-intro p {
          margin: 0;
          color: #64748b;
          font-size: 14px;
          text-align: center;
          font-style: italic;
        }

        /* Mobile optimizations */
        @media (max-width: 480px) {
          .filter-chips-container {
            gap: 8px;
            overflow-x: auto;
            padding: 8px 4px;
            justify-content: flex-start;
          }
          
          .filter-chip, .more-filters-chip {
            padding: 10px 14px;
            font-size: 14px;
            min-width: 90px;
            flex-shrink: 0;
          }
          
          .chip-icon {
            font-size: 16px;
          }
        }

        /* Enhanced panel styles */
        .filter-panel-overlay {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.5);
          z-index: 2000;
          pointer-events: auto;
        }

        .filter-panel {
          position: absolute;
          right: 0;
          top: 0;
          bottom: 0;
          width: 100%;
          max-width: 400px;
          background: white;
          box-shadow: -4px 0 20px rgba(0,0,0,0.15);
          display: flex;
          flex-direction: column;
          animation: slideLeft 0.3s ease-out;
        }

        @keyframes slideLeft {
          from { transform: translateX(100%); }
          to { transform: translateX(0); }
        }

        .panel-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 24px 24px 20px;
          border-bottom: 2px solid #f1f5f9;
          background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .panel-header h3 {
          margin: 0;
          font-size: 20px;
          font-weight: 700;
          color: #1f2937;
        }

        .panel-close {
          background: #f3f4f6;
          border: none;
          font-size: 18px;
          color: #6b7280;
          cursor: pointer;
          padding: 8px;
          border-radius: 50%;
          width: 36px;
          height: 36px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        }

        .panel-close:hover {
          background: #e5e7eb;
          color: #374151;
          transform: scale(1.05);
        }

        .panel-content {
          flex: 1;
          overflow-y: auto;
          padding: 20px 24px;
        }

        @media (max-width: 768px) {
          .filter-panel {
            bottom: 0;
            top: auto;
            width: 100%;
            max-width: none;
            max-height: 75vh;
            border-radius: 20px 20px 0 0;
            animation: slideUp 0.3s ease-out;
          }

          @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
          }
          
          .panel-header {
            padding: 20px 20px 16px;
          }
          
          .panel-content {
            padding: 16px 20px 24px;
          }
        }
      `}</style>
    </>
  );
}

// Helper component for filter sections
function FilterSection({ filterType, filterDef, filterStats, selectedValues, onFilterChange }) {
  if (!filterDef || !filterDef.options) {
    return (
      <div className="filter-section">
        <h4 className="section-title">
          <span className="section-icon">{filterDef?.icon || '🔧'}</span>
          {filterDef?.label || filterType}
        </h4>
        <p className="no-options">No options available for this filter.</p>
        
        <style jsx>{`
          .filter-section {
            margin-bottom: 24px;
          }
          
          .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px 0;
            font-size: 16px;
            font-weight: 600;
            color: #374151;
          }
          
          .section-icon {
            font-size: 18px;
          }
          
          .no-options {
            color: #6b7280;
            font-style: italic;
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            margin: 0;
          }
        `}</style>
      </div>
    );
  }

  return (
    <div className="filter-section">
      <h4 className="section-title">
        <span className="section-icon">{filterDef.icon || '🔧'}</span>
        {filterDef.label || filterType}
      </h4>
      
      {filterDef.description && (
        <p className="section-description">{filterDef.description}</p>
      )}
      
      <div className="section-options">
        {Object.entries(filterDef.options).map(([optionKey, optionLabel]) => {
          const stat = filterStats[optionKey] || { count: 0, available: false };
          const isSelected = selectedValues.includes(optionKey);
          
          return (
            <label
              key={optionKey}
              className={`option-label ${!stat.available ? 'unavailable' : ''} ${isSelected ? 'selected' : ''}`}
            >
              <input
                type="checkbox"
                checked={isSelected}
                onChange={(e) => onFilterChange(filterType, optionKey, e.target.checked)}
                disabled={!stat.available}
              />
              <span className="option-text">{optionLabel}</span>
              <span className="option-count">({stat.count})</span>
            </label>
          );
        })}
      </div>

      <style jsx>{`
        .filter-section {
          margin-bottom: 24px;
        }

        .section-title {
          display: flex;
          align-items: center;
          gap: 8px;
          margin: 0 0 12px 0;
          font-size: 16px;
          font-weight: 600;
          color: #374151;
        }

        .section-icon {
          font-size: 18px;
        }

        .section-description {
          margin: 0 0 16px 0;
          color: #6b7280;
          font-size: 14px;
          font-style: italic;
          line-height: 1.5;
        }

        .section-options {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 8px;
        }

        .option-label {
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 8px 12px;
          border-radius: 8px;
          cursor: pointer;
          transition: background-color 0.2s ease;
          font-size: 14px;
        }

        .option-label:hover:not(.unavailable) {
          background: #f9fafb;
        }

        .option-label.selected {
          background: #eff6ff;
          color: #3b82f6;
        }

        .option-label.unavailable {
          opacity: 0.4;
          cursor: not-allowed;
        }

        .option-text {
          flex: 1;
        }

        .option-count {
          font-size: 12px;
          color: #6b7280;
        }

        .option-label.selected .option-count {
          color: #3b82f6;
        }

        @media (max-width: 768px) {
          .section-options {
            grid-template-columns: 1fr;
          }
        }
      `}</style>
    </div>
  );
}