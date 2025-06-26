<?php
/**
 * DCLT Preserve Filter Options Management
 * File: includes/class-preserve-filter-options.php
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DCLT_Preserve_Filter_Options {
    
    private $option_name = 'dclt_preserve_filter_options';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Initialize default options if they don't exist
        add_action('init', array($this, 'init_default_options'));
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=preserve',
            __('Filter Options', 'dclt-preserve-explorer'),
            __('Filter Options', 'dclt-preserve-explorer'),
            'manage_options',
            'preserve-filter-options',
            array($this, 'admin_page_callback')
        );
    }
    
    /**
     * Initialize admin settings
     */
    public function admin_init() {
        register_setting(
            'dclt_preserve_filter_options_group',
            $this->option_name,
            array($this, 'sanitize_options')
        );
    }
    
    /**
     * Register REST API routes for filter options
     */
    public function register_rest_routes() {
        register_rest_route('dclt/v1', '/filter-options', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_filter_options_rest'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * REST API callback to get filter options
     */
    public function get_filter_options_rest($request) {
        $options = $this->get_filter_options();
        
        // Format for React component
        $formatted = array(
            'primary' => array(),
            'secondary' => array()
        );
        
        // Primary filters (used in main chips)
        $primary_keys = array('region', 'activity', 'accessibility', 'difficulty');
        
        foreach ($options as $key => $filter) {
            $target = in_array($key, $primary_keys) ? 'primary' : 'secondary';
            $formatted[$target][$key] = $filter;
        }
        
        return rest_ensure_response($formatted);
    }
    
    /**
     * Initialize default filter options if they don't exist
     */
    public function init_default_options() {
        if (!get_option($this->option_name)) {
            $default_options = $this->get_default_filter_options();
            update_option($this->option_name, $default_options);
        }
    }
    
    
    /**
     * Get filter options from database
     */
    public function get_filter_options() {
        $options = get_option($this->option_name, array());
        
        // Fallback to defaults if empty
        if (empty($options)) {
            $options = $this->get_default_filter_options();
            update_option($this->option_name, $options);
        }
        
        return $options;
    }
    
    /**
     * Get default filter options (your current hardcoded values)
     */
   /**
 * Get default filter options (COMPLETE VERSION - All 14 Categories)
 * Replace the get_default_filter_options() method in your class-preserve-filter-options.php
 */
private function get_default_filter_options() {
    return array(
        // PRIMARY FILTERS (show as main chips)
        'region' => array(
            'label' => __('Region', 'dclt-preserve-explorer'),
            'icon' => '📍',
            'description' => __('Select the Door County region(s) where this preserve is located.', 'dclt-preserve-explorer'),
            'options' => array(
                'northern_door' => __('Northern Door', 'dclt-preserve-explorer'),
                'central_door' => __('Central Door', 'dclt-preserve-explorer'),
                'southern_door' => __('Southern Door', 'dclt-preserve-explorer'),
                'washington_island' => __('Washington Island', 'dclt-preserve-explorer'),
            )
        ),
        'activity' => array(
            'label' => __('Activity', 'dclt-preserve-explorer'),
            'icon' => '🥾',
            'description' => __('Choose activities that visitors can enjoy at this preserve.', 'dclt-preserve-explorer'),
            'options' => array(
                'hiking' => __('Hiking', 'dclt-preserve-explorer'),
                'birdwatching' => __('Birdwatching', 'dclt-preserve-explorer'),
                'photography' => __('Photography', 'dclt-preserve-explorer'),
                'nature_study' => __('Nature Study', 'dclt-preserve-explorer'),
                'wildflower_viewing' => __('Wildflower Viewing', 'dclt-preserve-explorer'),
                'cross_country_skiing' => __('Cross Country Skiing', 'dclt-preserve-explorer'),
                'snowshoeing' => __('Snowshoeing', 'dclt-preserve-explorer'),
            )
        ),
        'difficulty' => array(
            'label' => __('Difficulty Level', 'dclt-preserve-explorer'),
            'icon' => '⛰️',
            'description' => __('What is the difficulty level for this preserve?', 'dclt-preserve-explorer'),
            'options' => array(
                'easy' => __('Easy', 'dclt-preserve-explorer'),
                'moderate' => __('Moderate', 'dclt-preserve-explorer'),
                'difficult' => __('Difficult', 'dclt-preserve-explorer'),
            )
        ),
        'accessibility' => array(
            'label' => __('Accessibility', 'dclt-preserve-explorer'),
            'icon' => '♿',
            'description' => __('How accessible is this preserve for different mobility levels?', 'dclt-preserve-explorer'),
            'options' => array(
                'wheelchair_accessible' => __('Wheelchair Accessible', 'dclt-preserve-explorer'),
                'stroller_friendly' => __('Stroller Friendly', 'dclt-preserve-explorer'),
                'uneven_terrain' => __('Uneven Terrain', 'dclt-preserve-explorer'),
                'mobility_challenges' => __('May Be Challenging for Limited Mobility', 'dclt-preserve-explorer'),
            )
        ),
        
        // SECONDARY FILTERS (show in "More" panel)
        'ecology' => array(
            'label' => __('Ecology', 'dclt-preserve-explorer'),
            'icon' => '🌿',
            'description' => __('Select the ecological features and habitats found in this preserve.', 'dclt-preserve-explorer'),
            'options' => array(
                'prairie' => __('Prairie', 'dclt-preserve-explorer'),
                'wetland' => __('Wetland', 'dclt-preserve-explorer'),
                'forest' => __('Forest', 'dclt-preserve-explorer'),
                'shoreline' => __('Shoreline', 'dclt-preserve-explorer'),
                'cedar_swamp' => __('Cedar Swamp', 'dclt-preserve-explorer'),
                'oak_savanna' => __('Oak Savanna', 'dclt-preserve-explorer'),
                'limestone_bluff' => __('Limestone Bluff', 'dclt-preserve-explorer'),
            )
        ),
        'available_facilities' => array(
            'label' => __('Available Facilities', 'dclt-preserve-explorer'),
            'icon' => '🏢',
            'description' => __('What facilities are available at this preserve?', 'dclt-preserve-explorer'),
            'options' => array(
                'restrooms' => __('Restrooms', 'dclt-preserve-explorer'),
                'picnic_tables' => __('Picnic Tables', 'dclt-preserve-explorer'),
                'water_fountains' => __('Water Fountains', 'dclt-preserve-explorer'),
                'trash_bins' => __('Trash/Recycling Bins', 'dclt-preserve-explorer'),
                'interpretive_signs' => __('Interpretive Signs', 'dclt-preserve-explorer'),
                'bike_racks' => __('Bike Racks', 'dclt-preserve-explorer'),
                'dog_waste_stations' => __('Dog Waste Stations', 'dclt-preserve-explorer'),
                'parking_available' => __('Parking Available', 'dclt-preserve-explorer'),
            )
        ),
        'trail_surface' => array(
            'label' => __('Trail Surface', 'dclt-preserve-explorer'),
            'icon' => '🛤️',
            'description' => __('What types of surfaces do the trails have?', 'dclt-preserve-explorer'),
            'options' => array(
                'paved' => __('Paved', 'dclt-preserve-explorer'),
                'boardwalk' => __('Boardwalk', 'dclt-preserve-explorer'),
                'natural_path' => __('Natural Path', 'dclt-preserve-explorer'),
                'rocky' => __('Rocky', 'dclt-preserve-explorer'),
                'sandy' => __('Sandy', 'dclt-preserve-explorer'),
            )
        ),
        'physical_challenges' => array(
            'label' => __('Physical Challenges', 'dclt-preserve-explorer'),
            'icon' => '💪',
            'description' => __('What physical challenges might visitors encounter?', 'dclt-preserve-explorer'),
            'options' => array(
                'hills' => __('Hills/Elevation Changes', 'dclt-preserve-explorer'),
                'water_crossings' => __('Water Crossings', 'dclt-preserve-explorer'),
                'long_distances' => __('Long Distances', 'dclt-preserve-explorer'),
                'steep_grades' => __('Steep Grades', 'dclt-preserve-explorer'),
                'rough_terrain' => __('Rough Terrain', 'dclt-preserve-explorer'),
            )
        ),
        'notable_features' => array(
            'label' => __('Notable Features', 'dclt-preserve-explorer'),
            'icon' => '⭐',
            'description' => __('Special natural or historic features that make this preserve unique.', 'dclt-preserve-explorer'),
            'options' => array(
                'waterfalls' => __('Waterfalls', 'dclt-preserve-explorer'),
                'overlooks' => __('Scenic Overlooks', 'dclt-preserve-explorer'),
                'historic_sites' => __('Historic Sites', 'dclt-preserve-explorer'),
                'rare_plants' => __('Rare Plants', 'dclt-preserve-explorer'),
                'rock_formations' => __('Rock Formations', 'dclt-preserve-explorer'),
                'caves' => __('Caves', 'dclt-preserve-explorer'),
                'springs' => __('Natural Springs', 'dclt-preserve-explorer'),
                'lighthouse' => __('Lighthouse', 'dclt-preserve-explorer'),
            )
        ),
        'photography' => array(
            'label' => __('Photography Opportunities', 'dclt-preserve-explorer'),
            'icon' => '📸',
            'description' => __('What photography opportunities are available here?', 'dclt-preserve-explorer'),
            'options' => array(
                'landscapes' => __('Landscapes', 'dclt-preserve-explorer'),
                'wildlife' => __('Wildlife', 'dclt-preserve-explorer'),
                'macro_flowers' => __('Macro/Flowers', 'dclt-preserve-explorer'),
                'sunrise_sunset' => __('Sunrise/Sunset Spots', 'dclt-preserve-explorer'),
                'water_reflections' => __('Water Reflections', 'dclt-preserve-explorer'),
                'seasonal_colors' => __('Seasonal Colors', 'dclt-preserve-explorer'),
            )
        ),
        'educational' => array(
            'label' => __('Educational Features', 'dclt-preserve-explorer'),
            'icon' => '📚',
            'description' => __('Educational resources and learning opportunities available.', 'dclt-preserve-explorer'),
            'options' => array(
                'interpretive_trails' => __('Interpretive Trails', 'dclt-preserve-explorer'),
                'guided_tours' => __('Guided Tours Available', 'dclt-preserve-explorer'),
                'educational_signage' => __('Educational Signage', 'dclt-preserve-explorer'),
                'nature_center' => __('Nature Center', 'dclt-preserve-explorer'),
                'self_guided_tour' => __('Self-Guided Tour', 'dclt-preserve-explorer'),
            )
        ),
        'wildlife_spotting' => array(
            'label' => __('Wildlife Spotting', 'dclt-preserve-explorer'),
            'icon' => '🦌',
            'description' => __('Types of wildlife commonly seen at this preserve.', 'dclt-preserve-explorer'),
            'options' => array(
                'birds_of_prey' => __('Birds of Prey', 'dclt-preserve-explorer'),
                'waterfowl' => __('Waterfowl', 'dclt-preserve-explorer'),
                'mammals' => __('Mammals', 'dclt-preserve-explorer'),
                'butterflies' => __('Butterflies', 'dclt-preserve-explorer'),
                'reptiles' => __('Reptiles', 'dclt-preserve-explorer'),
                'amphibians' => __('Amphibians', 'dclt-preserve-explorer'),
                'songbirds' => __('Songbirds', 'dclt-preserve-explorer'),
            )
        ),
        'habitat_diversity' => array(
            'label' => __('Habitat Diversity', 'dclt-preserve-explorer'),
            'icon' => '🌍',
            'description' => __('How diverse are the habitats within this preserve?', 'dclt-preserve-explorer'),
            'options' => array(
                'multiple_ecosystems' => __('Multiple Ecosystems', 'dclt-preserve-explorer'),
                'single_habitat' => __('Single Habitat Focus', 'dclt-preserve-explorer'),
                'transitional_zones' => __('Transitional Zones', 'dclt-preserve-explorer'),
                'rare_habitats' => __('Rare Habitats', 'dclt-preserve-explorer'),
            )
        ),
        'map_features' => array(
            'label' => __('Map Features & Structures', 'dclt-preserve-explorer'),
            'icon' => '🗺️',
            'description' => __('Structures and features that can be shown/hidden on maps.', 'dclt-preserve-explorer'),
            'options' => array(
                'trail_markers' => __('Trail Markers', 'dclt-preserve-explorer'),
                'benches' => __('Benches', 'dclt-preserve-explorer'),
                'observation_decks' => __('Observation Decks', 'dclt-preserve-explorer'),
                'bridges' => __('Bridges', 'dclt-preserve-explorer'),
                'shelters' => __('Shelters', 'dclt-preserve-explorer'),
                'viewing_blinds' => __('Wildlife Viewing Blinds', 'dclt-preserve-explorer'),
                'kiosks' => __('Information Kiosks', 'dclt-preserve-explorer'),
                'gates' => __('Gates/Entrances', 'dclt-preserve-explorer'),
            )
        )
    );
}
    
    /**
     * Sanitize filter options
     */
    public function sanitize_options($input) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        
        foreach ($input as $filter_key => $filter_data) {
            if (!is_array($filter_data)) continue;
            
            $sanitized[$filter_key] = array(
                'label' => sanitize_text_field($filter_data['label'] ?? ''),
                'icon' => sanitize_text_field($filter_data['icon'] ?? ''),
                'description' => sanitize_textarea_field($filter_data['description'] ?? ''),
                'options' => array()
            );
            
            if (isset($filter_data['options']) && is_array($filter_data['options'])) {
                foreach ($filter_data['options'] as $option_key => $option_label) {
                    $clean_key = sanitize_key($option_key);
                    $clean_label = sanitize_text_field($option_label);
                    if (!empty($clean_key) && !empty($clean_label)) {
                        $sanitized[$filter_key]['options'][$clean_key] = $clean_label;
                    }
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Simple admin page callback
     */
    public function admin_page_callback() {
        $options = $this->get_filter_options();
        ?>
        <div class="wrap">
            <h1><?php _e('Preserve Filter Options', 'dclt-preserve-explorer'); ?></h1>
            <p><?php _e('✅ Dynamic filter system is active! Your current filter options are loaded below.', 'dclt-preserve-explorer'); ?></p>
            
            <div class="card">
                <h2>Current Filter Options</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Filter Type</th>
                            <th>Label</th>
                            <th>Icon</th>
                            <th>Options Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($options as $key => $filter): ?>
                            <tr>
                                <td><code><?php echo esc_html($key); ?></code></td>
                                <td><?php echo esc_html($filter['label']); ?></td>
                                <td><?php echo esc_html($filter['icon']); ?></td>
                                <td><?php echo count($filter['options']); ?> options</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <h2>Next Steps</h2>
                <p>The dynamic filter system is now active! Your preserve explorer will use these filter options.</p>
                <ul>
                    <li>✅ Filter options are loaded from database</li>
                    <li>✅ REST API endpoint is available: <code>/wp-json/dclt/v1/filter-options</code></li>
                    <li>📝 Full admin interface coming in next step</li>
                </ul>
            </div>
        </div>
        
        <style>
        .card {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        </style>
        <?php
    }
}