<?php
/**
 * Plugin Name: DCLT Preserve Explorer
 * Description: Custom plugin to explore preserves on a Leaflet map with React.
 * Version: 1.0.0
 * Author: Door County Land Trust
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (!defined('DCLT_PRESERVE_PLUGIN_URL')) {
    define('DCLT_PRESERVE_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('DCLT_PRESERVE_PLUGIN_DIR')) {
    define('DCLT_PRESERVE_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('DCLT_PRESERVE_VERSION')) {
    define('DCLT_PRESERVE_VERSION', '1.0.0');
}

// Autoload files
require_once DCLT_PRESERVE_PLUGIN_DIR . 'includes/class-preserve-post-type.php';
require_once DCLT_PRESERVE_PLUGIN_DIR . 'includes/class-preserve-filter-options.php';
require_once DCLT_PRESERVE_PLUGIN_DIR . 'includes/class-preserve-rest-api.php';
require_once DCLT_PRESERVE_PLUGIN_DIR . 'includes/class-preserve-meta-boxes.php';
require_once DCLT_PRESERVE_PLUGIN_DIR . 'includes/class-dclt-analytics.php';

// SEO Router Class
class DCLT_Preserve_SEO_Router {
    
    public function __construct() {
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_preserve_routes'));
        add_filter('post_type_link', array($this, 'custom_preserve_permalink'), 10, 2);
        add_action('wp_head', array($this, 'add_preserve_structured_data'));
    }
    
    /**
     * Add custom rewrite rules for preserve URLs
     */
    public function add_rewrite_rules() {
        // Single preserve pages: /preserve/preserve-name/
        add_rewrite_rule(
            '^preserve/([^/]+)/?$',
            'index.php?post_type=preserve&name=$matches[1]&preserve_single=1',
            'top'
        );
        
        // Map deep links: /preserve-explorer/?preserve=preserve-slug
        add_rewrite_rule(
            '^preserve-explorer/?$',
            'index.php?preserve_explorer=1',
            'top'
        );
    }
    
    /**
     * Add custom query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'preserve_single';
        $vars[] = 'preserve_explorer';
        $vars[] = 'preserve';
        return $vars;
    }
    
    /**
     * Handle preserve routing
     */
    public function handle_preserve_routes() {
        global $wp_query;
        
        // Handle single preserve pages
        if (get_query_var('preserve_single')) {
            $this->load_single_preserve_template();
            return;
        }
        
        // Handle map explorer with deep link
        if (get_query_var('preserve_explorer')) {
            $this->load_explorer_template();
            return;
        }
    }
    
    /**
     * Load single preserve template
     */
    private function load_single_preserve_template() {
        $preserve_slug = get_query_var('name');
        $preserve = get_posts(array(
            'name' => $preserve_slug,
            'post_type' => 'preserve',
            'post_status' => 'publish',
            'numberposts' => 1
        ));
        
        if (empty($preserve)) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            return;
        }
        
        // Set up global post data
        global $post;
        $post = $preserve[0];
        setup_postdata($post);
        
        // Use the SAME template as the explorer
        $this->load_explorer_template();
    }
    
    /**
     * Load explorer template
     */
    private function load_explorer_template() {
        $template = locate_template('page-preserve-explorer.php');
        if (!$template) {
            $template = plugin_dir_path(__FILE__) . 'templates/page-preserve-explorer.php';
        }
        
        include $template;
        exit;
    }
    
    /**
     * Custom permalink structure for preserves
     */
    public function custom_preserve_permalink($post_link, $post) {
        if ($post->post_type === 'preserve') {
            return home_url('/preserve/' . $post->post_name . '/');
        }
        return $post_link;
    }
    
    /**
     * Add structured data for preserve SEO
     */
    public function add_preserve_structured_data() {
        if (!is_singular('preserve')) return;
        
        global $post;
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'TouristAttraction',
            'name' => get_the_title(),
            'description' => get_the_excerpt() ?: wp_trim_words(get_the_content(), 30),
            'url' => get_permalink(),
        );
        
        // Add location data if available
        $lat = get_post_meta($post->ID, '_preserve_lat', true);
        $lng = get_post_meta($post->ID, '_preserve_lng', true);
        if ($lat && $lng) {
            $structured_data['geo'] = array(
                '@type' => 'GeoCoordinates',
                'latitude' => $lat,
                'longitude' => $lng
            );
        }
        
        // Add additional properties
        $acres = get_post_meta($post->ID, '_preserve_acres', true);
        if ($acres) {
            $structured_data['additionalProperty'] = array(
                '@type' => 'PropertyValue',
                'name' => 'Area',
                'value' => $acres . ' acres'
            );
        }
        
        echo '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}

// Instantiate core classes
new DCLT_Preserve_Post_Type();
new DCLT_Preserve_Filter_Options();
new DCLT_Preserve_REST_API();
new DCLT_Preserve_Meta_Boxes();
new DCLT_Preserve_SEO_Router();

// Add debugging for REST API
add_action('rest_api_init', function() {
    error_log('DCLT Preserve Explorer: REST API initialized');
});

// Debug hook to check if preserves are being fetched
add_action('rest_after_insert_preserve', function($post, $request, $creating) {
    error_log('DCLT Preserve Explorer: Preserve post accessed via REST API: ' . $post->post_title);
}, 10, 3);

// Enqueue assets for Preserve Explorer page
if (!function_exists('dclt_enqueue_preserve_explorer_assets')) {
    function dclt_enqueue_preserve_explorer_assets() {
        // Leaflet
        wp_enqueue_style(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        wp_enqueue_script(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );

        // React (from WP core)
        wp_enqueue_script('react');
        wp_enqueue_script('react-dom');

        // Your compiled React app
        wp_enqueue_script(
            'dclt-preserve-explorer',
            plugin_dir_url(__FILE__) . 'assets/js/preserve-explorer.js',
            array('react', 'react-dom', 'leaflet'),
            DCLT_PRESERVE_VERSION,
            true
        );

        // Tailwind CSS compiled locally
        wp_enqueue_style(
            'dclt-preserve-style',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'assets/style.css')
        );

        // Enhanced API data with debugging
        $api_url = esc_url_raw(rest_url('wp/v2/preserves'));
        $filter_options_url = esc_url_raw(rest_url('dclt/v1/filter-options'));
        
        // Debug: Log the API URLs being used
        error_log('DCLT Preserve Explorer: API URL being passed to frontend: ' . $api_url);
        error_log('DCLT Preserve Explorer: Filter Options URL: ' . $filter_options_url);
        
        // Localize REST API data
        wp_localize_script('dclt-preserve-explorer', 'preserveExplorerData', array(
            'analyticsNonce' => wp_create_nonce('dclt_analytics_nonce'),
            'apiUrl' => $api_url,
            'filterOptionsUrl' => $filter_options_url,
            'restNonce' => wp_create_nonce('wp_rest'),
            'debug' => WP_DEBUG,
        ));
    }
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_preserve_explorer_assets');

// Register template for page dropdown in Gutenberg
if (!function_exists('dclt_register_preserve_template')) {
    function dclt_register_preserve_template($templates) {
        $templates['page-preserve-explorer.php'] = 'Preserve Explorer';
        return $templates;
    }
}
add_filter('theme_page_templates', 'dclt_register_preserve_template');

// Load template from plugin directory
if (!function_exists('dclt_load_preserve_template')) {
    function dclt_load_preserve_template($template) {
        if (is_page() && get_page_template_slug() === 'page-preserve-explorer.php') {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/page-preserve-explorer.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }
}
add_filter('template_include', 'dclt_load_preserve_template');

// Add admin notice to check if preserves exist
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        $preserve_count = wp_count_posts('preserve');
        if ($preserve_count && $preserve_count->publish == 0) {
            echo '<div class="notice notice-warning"><p><strong>DCLT Preserve Explorer:</strong> No published preserves found. <a href="' . admin_url('post-new.php?post_type=preserve') . '">Create your first preserve</a> to test the map.</p></div>';
        }
    }
});

// Flush rewrite rules on activation
register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});