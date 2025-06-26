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
   /**
 * Rich admin page callback with full editing capabilities
 * Replace the admin_page_callback() method in your class-preserve-filter-options.php
 */
/**
 * Rich admin page callback with full editing capabilities
 */
public function admin_page_callback() {
    $options = $this->get_filter_options();
    
    // Handle form submissions
    if (isset($_POST['action']) && wp_verify_nonce($_POST['filter_options_nonce'], 'filter_options_action')) {
        $this->handle_admin_actions();
        $options = $this->get_filter_options(); // Refresh after changes
    }
    ?>
    <div class="wrap">
        <h1>
            <?php _e('Preserve Filter Options', 'dclt-preserve-explorer'); ?>
            <button type="button" class="page-title-action" id="add-new-filter-type">
                <?php _e('Add New Filter Type', 'dclt-preserve-explorer'); ?>
            </button>
        </h1>
        
        <div class="notice notice-info">
            <p><strong><?php _e('💡 Dynamic Filter System', 'dclt-preserve-explorer'); ?></strong><br>
            <?php _e('Changes here instantly update your preserve explorer. Primary filters show as main chips, secondary filters appear in the "More" panel.', 'dclt-preserve-explorer'); ?></p>
        </div>

        <form method="post" action="" id="filter-options-form">
            <?php wp_nonce_field('filter_options_action', 'filter_options_nonce'); ?>
            <input type="hidden" name="action" value="save_filter_options" />
            
            <div class="filter-types-container">
                <?php foreach ($options as $filter_key => $filter_data): ?>
                    <div class="filter-type-card" data-filter-key="<?php echo esc_attr($filter_key); ?>">
                        <div class="card-header">
                            <div class="header-left">
                                <span class="filter-icon-display"><?php echo esc_html($filter_data['icon'] ?? '🔧'); ?></span>
                                <h3><?php echo esc_html($filter_data['label'] ?? $filter_key); ?></h3>
                                <span class="filter-type-badge <?php echo in_array($filter_key, ['region', 'activity', 'accessibility', 'difficulty']) ? 'primary' : 'secondary'; ?>">
                                    <?php echo in_array($filter_key, ['region', 'activity', 'accessibility', 'difficulty']) ? 'Primary' : 'Secondary'; ?>
                                </span>
                            </div>
                            <div class="header-actions">
                                <button type="button" class="button toggle-filter-card"><?php _e('Edit', 'dclt-preserve-explorer'); ?></button>
                                <button type="button" class="button button-link-delete delete-filter-type" data-filter-key="<?php echo esc_attr($filter_key); ?>">
                                    <?php _e('Delete', 'dclt-preserve-explorer'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-content" style="display: none;">
                            <table class="form-table">
                                <tr>
                                    <th><label><?php _e('Filter Key', 'dclt-preserve-explorer'); ?></label></th>
                                    <td>
                                        <code><?php echo esc_html($filter_key); ?></code>
                                        <p class="description"><?php _e('Used internally - cannot be changed', 'dclt-preserve-explorer'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Display Label', 'dclt-preserve-explorer'); ?></label></th>
                                    <td>
                                        <input type="text" 
                                               name="filters[<?php echo esc_attr($filter_key); ?>][label]" 
                                               value="<?php echo esc_attr($filter_data['label'] ?? ''); ?>" 
                                               class="regular-text" 
                                               placeholder="<?php _e('e.g., Region', 'dclt-preserve-explorer'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Icon', 'dclt-preserve-explorer'); ?></label></th>
                                    <td>
                                        <input type="text" 
                                               name="filters[<?php echo esc_attr($filter_key); ?>][icon]" 
                                               value="<?php echo esc_attr($filter_data['icon'] ?? ''); ?>" 
                                               class="small-text filter-icon-input" 
                                               placeholder="📍" 
                                               maxlength="4" />
                                        <p class="description"><?php _e('Emoji icon (copy from emojipedia.org)', 'dclt-preserve-explorer'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Description', 'dclt-preserve-explorer'); ?></label></th>
                                    <td>
                                        <textarea name="filters[<?php echo esc_attr($filter_key); ?>][description]" 
                                                  rows="2" 
                                                  class="large-text"
                                                  placeholder="<?php _e('Helper text shown to content editors...', 'dclt-preserve-explorer'); ?>"><?php echo esc_textarea($filter_data['description'] ?? ''); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Filter Options', 'dclt-preserve-explorer'); ?></label></th>
                                    <td>
                                        <div class="filter-options-manager" data-filter-key="<?php echo esc_attr($filter_key); ?>">
                                            <div class="options-list">
                                                <?php if (!empty($filter_data['options'])): ?>
                                                    <?php foreach ($filter_data['options'] as $option_key => $option_label): ?>
                                                        <div class="option-row">
                                                            <input type="hidden" 
                                                                   name="filters[<?php echo esc_attr($filter_key); ?>][options][<?php echo esc_attr($option_key); ?>][key]" 
                                                                   value="<?php echo esc_attr($option_key); ?>" />
                                                            <input type="text" 
                                                                   name="filters[<?php echo esc_attr($filter_key); ?>][options][<?php echo esc_attr($option_key); ?>][label]" 
                                                                   value="<?php echo esc_attr($option_label); ?>" 
                                                                   class="regular-text option-label" 
                                                                   placeholder="<?php _e('Option Label', 'dclt-preserve-explorer'); ?>" />
                                                            <code class="option-key"><?php echo esc_html($option_key); ?></code>
                                                            <button type="button" class="button remove-option"><?php _e('Remove', 'dclt-preserve-explorer'); ?></button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="add-option-section">
                                                <input type="text" 
                                                       class="new-option-input" 
                                                       placeholder="<?php _e('New option label...', 'dclt-preserve-explorer'); ?>" />
                                                <button type="button" class="button add-option" data-filter-key="<?php echo esc_attr($filter_key); ?>">
                                                    <?php _e('Add Option', 'dclt-preserve-explorer'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="submit-section">
                <?php submit_button(__('Save All Changes', 'dclt-preserve-explorer'), 'primary large'); ?>
                <button type="button" class="button button-secondary" id="preview-changes">
                    <?php _e('Preview API Response', 'dclt-preserve-explorer'); ?>
                </button>
            </div>
        </form>
    </div>
    
    <style>
    .filter-types-container { margin: 20px 0; }
    .filter-type-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 6px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .card-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f0f0f1; background: #fafafa; border-radius: 6px 6px 0 0; }
    .header-left { display: flex; align-items: center; gap: 12px; }
    .filter-icon-display { font-size: 20px; width: 24px; text-align: center; }
    .card-header h3 { margin: 0; font-size: 16px; font-weight: 600; }
    .filter-type-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .filter-type-badge.primary { background: #e7f3ff; color: #0073aa; }
    .filter-type-badge.secondary { background: #f0f6fc; color: #656970; }
    .header-actions { display: flex; gap: 8px; }
    .card-content { padding: 20px; }
    .filter-options-manager { border: 1px solid #ddd; border-radius: 4px; padding: 15px; background: #fafafa; }
    .option-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; padding: 8px; background: #fff; border-radius: 4px; border: 1px solid #e0e0e0; }
    .option-row .option-label { flex: 1; }
    .option-key { font-size: 11px; color: #666; background: #f1f1f1; padding: 2px 6px; border-radius: 3px; min-width: 100px; text-align: center; }
    .add-option-section { display: flex; gap: 8px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd; }
    .new-option-input { flex: 1; }
    .submit-section { background: #f9f9f9; padding: 20px; border-radius: 6px; margin-top: 20px; display: flex; gap: 15px; align-items: center; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // PREVENT DOUBLE EVENT BINDING - Unbind any existing handlers first
        $('.toggle-filter-card').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const card = $(this).closest('.filter-type-card');
            const content = card.find('.card-content');
            const button = $(this);
            
            content.slideToggle(300, function() {
                // Update button text after animation completes
                button.text(content.is(':visible') ? 'Collapse' : 'Edit');
            });
        });
        
        // Add new option
        $('.add-option').off('click').on('click', function(e) {
            e.preventDefault();
            const filterKey = $(this).data('filter-key');
            const input = $(this).siblings('.new-option-input');
            const label = input.val().trim();
            
            if (!label) { 
                alert('Please enter an option label'); 
                return; 
            }
            
            const key = label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            const optionsList = $(this).closest('.filter-options-manager').find('.options-list');
            
            const newRow = `
                <div class="option-row">
                    <input type="hidden" name="filters[${filterKey}][options][${key}][key]" value="${key}" />
                    <input type="text" name="filters[${filterKey}][options][${key}][label]" value="${label}" class="regular-text option-label" />
                    <code class="option-key">${key}</code>
                    <button type="button" class="button remove-option">Remove</button>
                </div>
            `;
            
            optionsList.append(newRow);
            input.val('');
        });
        
        // Remove option (use event delegation for dynamically added elements)
        $(document).off('click', '.remove-option').on('click', '.remove-option', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove this option?')) {
                $(this).closest('.option-row').remove();
            }
        });
        
        // Update icon display when typing
        $('.filter-icon-input').off('input').on('input', function() {
            const newIcon = $(this).val();
            $(this).closest('.filter-type-card').find('.filter-icon-display').text(newIcon || '🔧');
        });
        
        // Delete filter type
        $('.delete-filter-type').off('click').on('click', function(e) {
            e.preventDefault();
            const filterKey = $(this).data('filter-key');
            if (confirm(`Are you sure you want to delete the "${filterKey}" filter type? This cannot be undone and will affect all preserves.`)) {
                $(this).closest('.filter-type-card').remove();
            }
        });
    });
    </script>
    <?php
}

/**
 * Handle admin form submissions
 */
private function handle_admin_actions() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if ($_POST['action'] === 'save_filter_options' && isset($_POST['filters'])) {
        $filters = $_POST['filters'];
        $sanitized_filters = array();
        
        foreach ($filters as $filter_key => $filter_data) {
            $sanitized_key = sanitize_key($filter_key);
            
            $sanitized_filters[$sanitized_key] = array(
                'label' => sanitize_text_field($filter_data['label'] ?? ''),
                'icon' => sanitize_text_field($filter_data['icon'] ?? ''),
                'description' => sanitize_textarea_field($filter_data['description'] ?? ''),
                'options' => array()
            );
            
            if (isset($filter_data['options']) && is_array($filter_data['options'])) {
                foreach ($filter_data['options'] as $option_key => $option_data) {
                    $clean_key = sanitize_key($option_data['key'] ?? $option_key);
                    $clean_label = sanitize_text_field($option_data['label'] ?? '');
                    
                    if (!empty($clean_key) && !empty($clean_label)) {
                        $sanitized_filters[$sanitized_key]['options'][$clean_key] = $clean_label;
                    }
                }
            }
        }
        
        update_option($this->option_name, $sanitized_filters);
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p><strong>Filter options saved successfully!</strong> Changes are now live on your preserve explorer.</p></div>';
        });
    }
}
}