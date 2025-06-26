import React, { useState, useMemo, useEffect } from 'react';

// Filter presets - these can remain hardcoded as they're UI presets
const FILTER_PRESETS = {
  family_friendly: {
    label: 'Family Friendly',
    icon: '👨‍👩‍👧‍👦',
    filters: {
      difficulty: ['easy'],
      accessibility: ['stroller_friendly']
    }
  },
  photography: {
    label: 'Photography',
    icon: '📷',
    filters: {
      activity: ['photography']
    }
  },
  accessible: {
    label: 'Accessible',
    icon: '♿',
    filters: {
      accessibility: ['wheelchair_accessible', 'stroller_friendly']
    }
  }
};

export default function PreserveFilters({ preserves = [], filters = {}, onFiltersChange }) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedPreset, setSelectedPreset] = useState(null);
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
    
    setSelectedPreset(null); // Clear preset when manual filtering
  };

  // Apply preset filters
  const applyPreset = (presetKey) => {
    const preset = FILTER_PRESETS[presetKey];
    onFiltersChange(preset.filters);
    setSelectedPreset(presetKey);
  };

  // Clear all filters
  const clearAllFilters = () => {
    onFiltersChange({});
    setSelectedPreset(null);
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
      {/* Filter Presets Row */}
      <div className="filter-presets">
        <div className="preset-chips">
          {Object.entries(FILTER_PRESETS).map(([presetKey, preset]) => (
            <button
              key={presetKey}
              className={`preset-chip ${selectedPreset === presetKey ? 'active' : ''}`}
              onClick={() => applyPreset(presetKey)}
            >
              <span className="preset-icon">{preset.icon}</span>
              <span className="preset-label">{preset.label}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Primary Filter Chips */}
      <div className="primary-filters">
        <div className="filter-chips-container">
          {Object.entries(filterOptions.primary).map(([filterType, filterDef]) => {
            const hasSelections = filters[filterType]?.length > 0;
            const availableCount = Object.values(filterStats[filterType] || {})
              .filter(stat => stat.available).length;
            
            return (
              <button
                key={filterType}
                className={`filter-chip ${hasSelections ? 'active' : ''} ${availableCount === 0 ? 'disabled' : ''}`}
                onClick={() => setIsModalOpen(filterType)}
                disabled={availableCount === 0}
              >
                <span className="chip-icon">{filterDef.icon || '🔧'}</span>
                <span className="chip-label">{filterDef.label || filterType}</span>
                {hasSelections && (
                  <span className="chip-count">{filters[filterType].length}</span>
                )}
              </button>
            );
          })}
          
          {Object.keys(filterOptions.secondary).length > 0 && (
            <button
              className="more-filters-chip"
              onClick={() => setIsModalOpen('more')}
            >
              <span className="chip-icon">⚙️</span>
              <span className="chip-label">More</span>
            </button>
          )}
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
                  <span className="count-text">{filteredCount} of {preserves.length} preserves</span>
                  {totalActiveFilters > 0 && (
                    <button className="clear-btn" onClick={clearAllFilters}>
                      Clear all
                    </button>
                  )}
                </>
              ) : (
                <span className="count-text">{preserves.length} preserves</span>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Detailed Filter Panel */}
      {isModalOpen && (
        <div className="filter-panel-overlay">
          <div className="filter-panel">
            <div className="panel-header">
              <h3>
                {isModalOpen === 'more' 
                  ? 'More Filters' 
                  : (filterOptions.primary[isModalOpen]?.label || filterOptions.secondary[isModalOpen]?.label || isModalOpen)
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
                // Show all secondary filters
                Object.entries(filterOptions.secondary).map(([filterType, filterDef]) => (
                  <FilterSection
                    key={filterType}
                    filterType={filterType}
                    filterDef={filterDef}
                    filterStats={filterStats[filterType] || {}}
                    selectedValues={filters[filterType] || []}
                    onFilterChange={handleFilterChange}
                  />
                ))
              ) : (
                // Show specific filter (primary or secondary)
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
        .filter-presets {
          position: fixed;
          top: 80px;
          left: 20px;
          right: 20px;
          z-index: 800;
          pointer-events: none;
        }

        .preset-chips {
          display: flex;
          gap: 8px;
          overflow-x: auto;
          padding: 8px 0;
          pointer-events: auto;
        }

        .preset-chip {
          display: flex;
          align-items: center;
          gap: 6px;
          background: white;
          border: 1px solid #e5e7eb;
          border-radius: 20px;
          padding: 8px 12px;
          font-size: 13px;
          font-weight: 500;
          color: #374151;
          cursor: pointer;
          transition: all 0.2s ease;
          white-space: nowrap;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .preset-chip:hover {
          border-color: #3b82f6;
          transform: translateY(-1px);
          box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .preset-chip.active {
          background: #3b82f6;
          border-color: #2563eb;
          color: white;
        }

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
          gap: 8px;
          overflow-x: auto;
          padding: 8px 0;
          pointer-events: auto;
        }

        .filter-chip, .more-filters-chip {
          display: flex;
          align-items: center;
          gap: 6px;
          background: white;
          border: 1px solid #e5e7eb;
          border-radius: 20px;
          padding: 10px 14px;
          font-size: 14px;
          font-weight: 500;
          color: #374151;
          cursor: pointer;
          transition: all 0.2s ease;
          white-space: nowrap;
          box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .filter-chip:hover:not(.disabled), .more-filters-chip:hover {
          border-color: #3b82f6;
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .filter-chip.active {
          background: #3b82f6;
          border-color: #2563eb;
          color: white;
        }

        .filter-chip.disabled {
          opacity: 0.4;
          cursor: not-allowed;
        }

        .chip-icon {
          font-size: 16px;
        }

        .chip-count {
          background: rgba(255,255,255,0.3);
          border-radius: 10px;
          padding: 2px 6px;
          font-size: 11px;
          font-weight: 600;
          margin-left: 2px;
        }

        .filter-chip.active .chip-count {
          background: rgba(255,255,255,0.3);
        }

        .more-filters-chip {
          background: #f3f4f6;
          border-color: #d1d5db;
        }

        .results-summary {
          margin-top: 12px;
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
          border-radius: 12px;
          padding: 8px 16px;
          font-size: 13px;
          color: #374151;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .clear-btn {
          background: none;
          border: none;
          color: #3b82f6;
          font-size: 12px;
          font-weight: 600;
          cursor: pointer;
          padding: 2px 6px;
          border-radius: 4px;
        }

        .clear-btn:hover {
          background: rgba(59, 130, 246, 0.1);
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

        .filter-panel-overlay {
          position: fixed;
          top: 0;
          right: 0;
          bottom: 0;
          width: 100%;
          z-index: 2000;
          pointer-events: none;
        }

        .filter-panel {
          position: absolute;
          right: 0;
          top: 0;
          bottom: 0;
          width: 100%;
          max-width: 380px;
          background: white;
          box-shadow: -4px 0 20px rgba(0,0,0,0.15);
          display: flex;
          flex-direction: column;
          animation: slideLeft 0.3s ease-out;
          pointer-events: auto;
        }

        @keyframes slideLeft {
          from { transform: translateX(100%); }
          to { transform: translateX(0); }
        }

        .panel-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 20px 24px 16px;
          border-bottom: 1px solid #e5e7eb;
          background: white;
        }

        .panel-header h3 {
          margin: 0;
          font-size: 18px;
          font-weight: 700;
          color: #1f2937;
        }

        .panel-close {
          background: none;
          border: none;
          font-size: 20px;
          color: #6b7280;
          cursor: pointer;
          padding: 4px;
          border-radius: 50%;
          width: 32px;
          height: 32px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .panel-close:hover {
          background: #f3f4f6;
          color: #374151;
        }

        .panel-content {
          flex: 1;
          overflow-y: auto;
          padding: 16px 24px;
        }

        @media (max-width: 768px) {
          .filter-panel {
            bottom: 0;
            top: auto;
            width: 100%;
            max-width: none;
            max-height: 70vh;
            border-radius: 20px 20px 0 0;
            animation: slideUp 0.3s ease-out;
          }

          @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
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