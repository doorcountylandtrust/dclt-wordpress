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
    private function get_default_filter_options() {
        return array(
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