import React, { useState, useMemo } from 'react';

// Filter definitions (matching your complete REST API data)
const FILTER_DEFINITIONS = {
  region: {
    label: 'Region',
    icon: '📍',
    options: {
      northern_door: 'Northern Door',
      central_door: 'Central Door', 
      southern_door: 'Southern Door',
      washington_island: 'Washington Island'
    }
  },
  activity: {
    label: 'Activity',
    icon: '🥾',
    options: {
      hiking: 'Hiking',
      birdwatching: 'Birdwatching',
      photography: 'Photography',
      nature_study: 'Nature Study',
      wildflower_viewing: 'Wildflower Viewing',
      cross_country_skiing: 'Cross Country Skiing',
      snowshoeing: 'Snowshoeing'
    }
  },
  ecology: {
    label: 'Ecology',
    icon: '🌿',
    options: {
      prairie: 'Prairie',
      wetland: 'Wetland',
      forest: 'Forest',
      shoreline: 'Shoreline',
      cedar_swamp: 'Cedar Swamp',
      oak_savanna: 'Oak Savanna',
      limestone_bluff: 'Limestone Bluff'
    }
  },
  difficulty: {
    label: 'Difficulty',
    icon: '⛰️',
    options: {
      easy: 'Easy',
      moderate: 'Moderate',
      difficult: 'Difficult'
    }
  },
  available_facilities: {
    label: 'Facilities',
    icon: '🏢',
    options: {
      restrooms: 'Restrooms',
      picnic_tables: 'Picnic Tables',
      water_fountains: 'Water Fountains',
      trash_bins: 'Trash/Recycling Bins',
      interpretive_signs: 'Interpretive Signs',
      bike_racks: 'Bike Racks',
      dog_waste_stations: 'Dog Waste Stations',
      parking_available: 'Parking Available'
    }
  },
  trail_surface: {
    label: 'Trail Surface',
    icon: '🛤️',
    options: {
      paved: 'Paved',
      boardwalk: 'Boardwalk',
      natural_path: 'Natural Path',
      rocky: 'Rocky',
      sandy: 'Sandy'
    }
  },
  accessibility: {
    label: 'Accessibility',
    icon: '♿',
    options: {
      wheelchair_accessible: 'Wheelchair Accessible',
      stroller_friendly: 'Stroller Friendly',
      uneven_terrain: 'Uneven Terrain',
      mobility_challenges: 'May Be Challenging for Limited Mobility'
    }
  },
  physical_challenges: {
    label: 'Physical Challenges',
    icon: '🏔️',
    options: {
      hills: 'Hills/Elevation Changes',
      water_crossings: 'Water Crossings',
      long_distances: 'Long Distances',
      steep_grades: 'Steep Grades',
      rough_terrain: 'Rough Terrain'
    }
  },
  notable_features: {
    label: 'Notable Features',
    icon: '⭐',
    options: {
      waterfalls: 'Waterfalls',
      overlooks: 'Scenic Overlooks',
      historic_sites: 'Historic Sites',
      rare_plants: 'Rare Plants',
      rock_formations: 'Rock Formations',
      caves: 'Caves',
      springs: 'Natural Springs',
      lighthouse: 'Lighthouse'
    }
  },
  photography: {
    label: 'Photography Opportunities',
    icon: '📸',
    options: {
      landscapes: 'Landscapes',
      wildlife: 'Wildlife',
      macro_flowers: 'Macro/Flowers',
      sunrise_sunset: 'Sunrise/Sunset Spots',
      water_reflections: 'Water Reflections',
      seasonal_colors: 'Seasonal Colors'
    }
  },
  educational: {
    label: 'Educational Features',
    icon: '📚',
    options: {
      interpretive_trails: 'Interpretive Trails',
      guided_tours: 'Guided Tours Available',
      educational_signage: 'Educational Signage',
      nature_center: 'Nature Center',
      self_guided_tour: 'Self-Guided Tour'
    }
  },
  wildlife_spotting: {
    label: 'Wildlife Spotting',
    icon: '🦅',
    options: {
      birds_of_prey: 'Birds of Prey',
      waterfowl: 'Waterfowl',
      mammals: 'Mammals',
      butterflies: 'Butterflies',
      reptiles: 'Reptiles',
      amphibians: 'Amphibians',
      songbirds: 'Songbirds'
    }
  },
  habitat_diversity: {
    label: 'Habitat Diversity',
    icon: '🌳',
    options: {
      multiple_ecosystems: 'Multiple Ecosystems',
      single_habitat: 'Single Habitat Focus',
      transitional_zones: 'Transitional Zones',
      rare_habitats: 'Rare Habitats'
    }
  },
  map_features: {
    label: 'Map Features & Structures',
    icon: '🗺️',
    options: {
      trail_markers: 'Trail Markers',
      benches: 'Benches',
      observation_decks: 'Observation Decks',
      bridges: 'Bridges',
      shelters: 'Shelters',
      viewing_blinds: 'Wildlife Viewing Blinds',
      kiosks: 'Information Kiosks',
      gates: 'Gates/Entrances'
    }
  }
};

export default function PreserveFilters({ preserves = [], filters = {}, onFiltersChange }) {
  const [isOpen, setIsOpen] = useState(false);
  const [expandedSections, setExpandedSections] = useState({
    region: true,
    activity: true,
    ecology: true
  });

  // Early return if no data yet
  if (!preserves || !Array.isArray(preserves)) {
    return (
      <div className="filter-button-container">
        <button className="filter-toggle-btn" disabled>
          🔍 Loading...
        </button>
      </div>
    );
  }

  // Calculate filter availability and counts
  const filterStats = useMemo(() => {
    console.log('🔍 Calculating filter stats...');
    console.log('📊 Preserves data:', preserves);
    
    const stats = {};
    
    Object.keys(FILTER_DEFINITIONS).forEach(filterType => {
      stats[filterType] = {};
      
      Object.keys(FILTER_DEFINITIONS[filterType].options).forEach(optionKey => {
        let count = 0;
        
        try {
          count = preserves.filter(preserve => {
            if (!preserve || !preserve.meta) {
              console.log(`⚠️ Preserve missing meta:`, preserve);
              return false;
            }
            
            const metaKey = `_preserve_filter_${filterType}`;
            const preserveFilters = preserve.meta[metaKey] || [];
            
            // Debug logging for specific cases
            if (filterType === 'region' || filterType === 'activity') {
              console.log(`🔎 ${preserve.title?.rendered || 'Unknown'} - ${metaKey}:`, preserveFilters);
            }
            
            const preserveArray = Array.isArray(preserveFilters) ? preserveFilters : [preserveFilters];
            const hasFilter = preserveArray.filter(Boolean).includes(optionKey);
            
            if (hasFilter && (filterType === 'region' || filterType === 'activity')) {
              console.log(`✅ ${preserve.title?.rendered} has ${filterType}.${optionKey}`);
            }
            
            return hasFilter;
          }).length;
        } catch (e) {
          console.error(`❌ Error calculating filter stats for ${filterType}.${optionKey}:`, e);
          count = 0;
        }
        
        stats[filterType][optionKey] = {
          count,
          available: count > 0,
          selected: filters[filterType]?.includes(optionKey) || false
        };
        
        // Log non-zero counts
        if (count > 0) {
          console.log(`📈 ${filterType}.${optionKey}: ${count} preserve(s)`);
        }
      });
    });
    
    console.log('📋 Final filter stats:', stats);
    return stats;
  }, [preserves, filters]);

  // Handle filter selection
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
  };

  // Clear all filters
  const clearAllFilters = () => {
    if (!onFiltersChange) return;
    onFiltersChange({});
  };

  // Count total active filters
  const totalActiveFilters = Object.values(filters).reduce((sum, filterArray) => 
    sum + (filterArray?.length || 0), 0
  );

  // Toggle section expansion
  const toggleSection = (filterType) => {
    setExpandedSections(prev => ({
      ...prev,
      [filterType]: !prev[filterType]
    }));
  };

  // Close panel when clicking backdrop
  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
      setIsOpen(false);
    }
  };

  return (
    <>
      {/* Filter Toggle Button */}
      <div className="filter-button-container">
        <button 
          className={`filter-toggle-btn ${totalActiveFilters > 0 ? 'has-filters' : ''}`}
          onClick={() => setIsOpen(true)}
        >
          🔍 Filters
          {totalActiveFilters > 0 && (
            <span className="filter-badge">{totalActiveFilters}</span>
          )}
        </button>
      </div>

      {/* Filter Panel Overlay */}
      {isOpen && (
        <div className="filter-overlay" onClick={handleBackdropClick}>
          <div className="filter-panel">
            {/* Panel Header */}
            <div className="filter-panel-header">
              <h2>Filter Preserves</h2>
              <button 
                className="close-btn"
                onClick={() => setIsOpen(false)}
                aria-label="Close filters"
              >
                ✕
              </button>
            </div>

            {/* Active Filters Summary */}
            {totalActiveFilters > 0 && (
              <div className="active-filters-summary">
                <span>{totalActiveFilters} filter{totalActiveFilters !== 1 ? 's' : ''} active</span>
                <button 
                  onClick={clearAllFilters}
                  className="clear-all-btn"
                >
                  Clear All
                </button>
              </div>
            )}

            {/* Filter Content */}
            <div className="filter-content">
              {Object.entries(FILTER_DEFINITIONS).map(([filterType, filterDef]) => {
                const isExpanded = expandedSections[filterType];
                const sectionStats = filterStats[filterType];
                const availableCount = Object.values(sectionStats).filter(stat => stat.available).length;
                const selectedCount = Object.values(sectionStats).filter(stat => stat.selected).length;
                
                return (
                  <div 
                    key={filterType} 
                    className={`filter-section ${availableCount === 0 ? 'no-options' : ''}`}
                  >
                    {/* Section Header */}
                    <button
                      className={`section-header ${isExpanded ? 'expanded' : ''}`}
                      onClick={() => toggleSection(filterType)}
                    >
                      <div className="section-title">
                        <span className="section-icon">{filterDef.icon}</span>
                        <span className="section-name">{filterDef.label}</span>
                      </div>
                      <div className="section-info">
                        {selectedCount > 0 && (
                          <span className="selected-badge">{selectedCount}</span>
                        )}
                        <span className="expand-arrow">{isExpanded ? '▼' : '▶'}</span>
                      </div>
                    </button>

                    {/* Section Options */}
                    {isExpanded && (
                      <div className="section-options">
                        {Object.entries(filterDef.options).map(([optionKey, optionLabel]) => {
                          const stat = sectionStats[optionKey];
                          const isSelected = stat.selected;
                          const isAvailable = stat.available;
                          const count = stat.count;

                          return (
                            <label
                              key={optionKey}
                              className={`filter-option ${!isAvailable ? 'unavailable' : ''} ${isSelected ? 'selected' : ''}`}
                            >
                              <input
                                type="checkbox"
                                checked={isSelected}
                                onChange={(e) => handleFilterChange(filterType, optionKey, e.target.checked)}
                                disabled={!isAvailable}
                              />
                              <span className="option-label">{optionLabel}</span>
                              <span className="option-count">
                                ({count})
                              </span>
                            </label>
                          );
                        })}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>

            {/* Panel Footer */}
            <div className="filter-panel-footer">
              <button 
                className="apply-btn"
                onClick={() => setIsOpen(false)}
              >
                Apply Filters
              </button>
            </div>
          </div>
        </div>
      )}

      <style jsx>{`
        .filter-button-container {
          position: fixed;
          top: 20px;
          right: 20px;
          z-index: 1000;
        }

        .filter-toggle-btn {
          background: white;
          border: 2px solid #e5e7eb;
          border-radius: 25px;
          padding: 12px 20px;
          font-size: 16px;
          font-weight: 600;
          color: #374151;
          cursor: pointer;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          transition: all 0.2s ease;
          display: flex;
          align-items: center;
          gap: 8px;
          min-width: 120px;
          justify-content: center;
        }

        .filter-toggle-btn:hover {
          background: #f9fafb;
          border-color: #3b82f6;
          transform: translateY(-1px);
          box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .filter-toggle-btn.has-filters {
          background: #3b82f6;
          color: white;
          border-color: #2563eb;
        }

        .filter-badge {
          background: #ef4444;
          color: white;
          border-radius: 12px;
          padding: 2px 8px;
          font-size: 12px;
          font-weight: 700;
          margin-left: 4px;
        }

        .filter-overlay {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.5);
          z-index: 2000;
          display: flex;
          align-items: flex-end;
          justify-content: center;
        }

        .filter-panel {
          background: white;
          width: 100%;
          max-width: 500px;
          max-height: 85vh;
          border-radius: 20px 20px 0 0;
          display: flex;
          flex-direction: column;
          animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
          from {
            transform: translateY(100%);
          }
          to {
            transform: translateY(0);
          }
        }

        .filter-panel-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 20px 24px 16px;
          border-bottom: 1px solid #e5e7eb;
        }

        .filter-panel-header h2 {
          margin: 0;
          font-size: 20px;
          font-weight: 700;
          color: #1f2937;
        }

        .close-btn {
          background: none;
          border: none;
          font-size: 24px;
          color: #6b7280;
          cursor: pointer;
          padding: 4px;
          border-radius: 50%;
          width: 36px;
          height: 36px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .close-btn:hover {
          background: #f3f4f6;
          color: #374151;
        }

        .active-filters-summary {
          padding: 16px 24px;
          background: #eff6ff;
          border-bottom: 1px solid #e5e7eb;
          display: flex;
          justify-content: space-between;
          align-items: center;
          font-size: 14px;
        }

        .clear-all-btn {
          background: none;
          border: none;
          color: #3b82f6;
          font-weight: 600;
          cursor: pointer;
          padding: 4px 8px;
          border-radius: 4px;
        }

        .clear-all-btn:hover {
          background: rgba(59, 130, 246, 0.1);
        }

        .filter-content {
          flex: 1;
          overflow-y: auto;
          padding: 16px 24px;
        }

        .filter-section {
          margin-bottom: 16px;
        }

        .filter-section.no-options {
          opacity: 0.5;
        }

        .section-header {
          width: 100%;
          background: none;
          border: none;
          padding: 12px 0;
          display: flex;
          justify-content: space-between;
          align-items: center;
          cursor: pointer;
          border-bottom: 1px solid #f3f4f6;
        }

        .section-title {
          display: flex;
          align-items: center;
          gap: 12px;
        }

        .section-icon {
          font-size: 18px;
        }

        .section-name {
          font-weight: 600;
          color: #374151;
          font-size: 16px;
        }

        .section-info {
          display: flex;
          align-items: center;
          gap: 8px;
        }

        .selected-badge {
          background: #3b82f6;
          color: white;
          border-radius: 12px;
          padding: 2px 8px;
          font-size: 12px;
          font-weight: 600;
        }

        .expand-arrow {
          color: #9ca3af;
          font-size: 12px;
          transition: transform 0.2s ease;
        }

        .section-options {
          padding: 8px 0 0 30px;
        }

        .filter-option {
          display: flex;
          align-items: center;
          gap: 12px;
          padding: 8px 0;
          cursor: pointer;
          font-size: 14px;
        }

        .filter-option.unavailable {
          opacity: 0.4;
          cursor: not-allowed;
        }

        .filter-option.selected {
          color: #3b82f6;
          font-weight: 500;
        }

        .filter-option input[type="checkbox"] {
          margin: 0;
          width: 18px;
          height: 18px;
        }

        .option-label {
          flex: 1;
        }

        .option-count {
          color: #6b7280;
          font-size: 12px;
          font-weight: 500;
        }

        .filter-option.selected .option-count {
          color: #3b82f6;
        }

        .filter-panel-footer {
          padding: 16px 24px;
          border-top: 1px solid #e5e7eb;
        }

        .apply-btn {
          width: 100%;
          background: #3b82f6;
          color: white;
          border: none;
          border-radius: 12px;
          padding: 16px;
          font-size: 16px;
          font-weight: 600;
          cursor: pointer;
          transition: background-color 0.2s ease;
        }

        .apply-btn:hover {
          background: #2563eb;
        }

        /* Desktop adjustments */
        @media (min-width: 768px) {
          .filter-overlay {
            align-items: center;
            justify-content: flex-end;
            padding-right: 20px;
          }

          .filter-panel {
            width: 400px;
            max-height: 80vh;
            border-radius: 12px;
            animation: slideLeft 0.3s ease-out;
          }

          @keyframes slideLeft {
            from {
              transform: translateX(100%);
            }
            to {
              transform: translateX(0);
            }
          }
        }
      `}</style>
    </>
  );
}