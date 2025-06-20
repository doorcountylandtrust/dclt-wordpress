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

if (!defined('DCLT_PRESERVE_PLUGIN_URL')) {
    define('DCLT_PRESERVE_PLUGIN_URL', plugin_dir_url(__FILE__));
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

// Enqueue assets for Preserve Explorer page template
function dclt_enqueue_preserve_explorer_assets() {
    // TEMP: load scripts unconditionally
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);

    wp_enqueue_style('tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
    wp_enqueue_script('react');
    wp_enqueue_script('react-dom');

    wp_enqueue_style(
        'dclt-preserve-explorer',
        plugin_dir_url(__FILE__) . 'assets/css/preserve-explorer.css'
    );

    wp_enqueue_script(
        'dclt-preserve-explorer',
        plugin_dir_url(__FILE__) . 'assets/js/preserve-explorer.js',
        array('react', 'react-dom', 'leaflet'),
        null,
        true
    );

    wp_localize_script('dclt-preserve-explorer', 'preserveExplorerData', array(
        'apiUrl' => esc_url_raw(rest_url('wp/v2/preserves'))
    ));
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_preserve_explorer_assets');

// Register page template for Gutenberg dropdown
function dclt_register_preserve_template($templates) {
    $templates['page-preserve-explorer.php'] = 'Preserve Explorer';
    return $templates;
}
add_filter('theme_page_templates', 'dclt_register_preserve_template');

// Load custom template from plugin directory
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