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
require_once plugin_dir_path(__FILE__) . 'includes/class-preserve-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-preserve-rest-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-preserve-meta-boxes.php';

// Instantiate core classes
new DCLT_Preserve_Post_Type();
new DCLT_Preserve_REST_API();
new DCLT_Preserve_Meta_Boxes();

// Add debugging for REST API
add_action('rest_api_init', function() {
    error_log('DCLT Preserve Explorer: REST API initialized');
});

// Debug hook to check if preserves are being fetched
add_action('rest_after_insert_preserve', function($post, $request, $creating) {
    error_log('DCLT Preserve Explorer: Preserve post accessed via REST API: ' . $post->post_title);
}, 10, 3);

// Enqueue assets for Preserve Explorer page
function dclt_enqueue_preserve_explorer_assets() {
    // You can change this back to conditional once working: if (!is_page('preserve-explorer')) return;

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
    
    // Debug: Log the API URL being used
    error_log('DCLT Preserve Explorer: API URL being passed to frontend: ' . $api_url);
    
    // Localize REST API data
    wp_localize_script('dclt-preserve-explorer', 'preserveExplorerData', array(
        'apiUrl' => $api_url,
        'nonce' => wp_create_nonce('wp_rest'),
        'debug' => WP_DEBUG, // Pass debug mode to frontend
    ));
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_preserve_explorer_assets');

// Register template for page dropdown in Gutenberg
function dclt_register_preserve_template($templates) {
    $templates['page-preserve-explorer.php'] = 'Preserve Explorer';
    return $templates;
}
add_filter('theme_page_templates', 'dclt_register_preserve_template');

// Load template from plugin directory
function dclt_load_preserve_template($template) {
    if (is_page() && get_page_template_slug() === 'page-preserve-explorer.php') {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/page-preserve-explorer.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
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

// Debug function - remove after fixing
function dclt_debug_preserves_endpoint() {
    if (isset($_GET['dclt_debug']) && current_user_can('manage_options')) {
        header('Content-Type: application/json');
        
        $preserves = get_posts([
            'post_type' => 'preserve',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);
        
        $debug_data = [
            'preserve_count' => count($preserves),
            'api_url' => rest_url('wp/v2/preserves'),
            'first_preserve_meta' => $preserves ? get_post_meta($preserves[0]->ID) : null,
            'rest_api_enabled' => function_exists('rest_url'),
        ];
        
        echo json_encode($debug_data, JSON_PRETTY_PRINT);
        exit;
    }
}
add_action('init', 'dclt_debug_preserves_endpoint');