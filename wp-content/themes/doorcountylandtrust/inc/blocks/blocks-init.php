<?php 
/**
 * Door County Land Trust - Modular Block Registration System (No ACF)
 */

error_log('DCLT blocks-init.php file is loading!');

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// CUSTOM FIELD FUNCTIONS (WordPress Native)
// =============================================================================

/**
 * Our replacement for get_field() - works with WordPress native meta
 */
function dclt_get_field($post_id, $field_name, $block_id = '', $default = '') {
    // Create unique meta key for this field
    $meta_key = $block_id ? "dclt_{$block_id}_{$field_name}" : "dclt_{$field_name}";
    
    // Get the meta value
    $value = get_post_meta($post_id, $meta_key, true);
    
    // Return default if empty
    return !empty($value) ? $value : $default;
}

/**
 * Our replacement for update_field()
 */
function dclt_update_field($post_id, $field_name, $value, $block_id = '') {
    $meta_key = $block_id ? "dclt_{$block_id}_{$field_name}" : "dclt_{$field_name}";
    return update_post_meta($post_id, $meta_key, $value);
}

// =============================================================================
// BLOCK REGISTRATION (Custom Gutenberg Blocks)
// =============================================================================

/**
 * Register all custom blocks
 */
function dclt_register_blocks() {
    // Register Hero Block JavaScript for editor
    wp_register_script(
        'dclt-hero-block-editor',
        get_template_directory_uri() . '/blocks/hero/hero-editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        '1.0.0'
    );

    // Register Hero Block
    register_block_type('dclt/hero', array(
        'editor_script' => 'dclt-hero-block-editor',
        'render_callback' => 'dclt_render_hero_block',
        'attributes' => array(
            'blockId' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));

    // Register CTA Block JavaScript for editor
    wp_register_script(
        'dclt-cta-block-editor',
        get_template_directory_uri() . '/blocks/cta/cta-editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        '1.0.0'
    );

    // Register CTA Block
    register_block_type('dclt/cta', array(
        'editor_script' => 'dclt-cta-block-editor',
        'render_callback' => 'dclt_render_cta_block',
        'attributes' => array(
            'blockId' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
    
    error_log('All blocks registered successfully!');
}
add_action('init', 'dclt_register_blocks');

// =============================================================================
// BLOCK RENDER CALLBACKS
// =============================================================================

/**
 * Render callback for Hero block - uses modular template
 */
function dclt_render_hero_block($attributes, $content) {
    global $dclt_attributes;
    $dclt_attributes = $attributes;
    
    ob_start();
    include get_template_directory() . '/blocks/hero/hero.php';
    return ob_get_clean();
}

/**
 * Render callback for CTA block - uses modular template
 */
function dclt_render_cta_block($attributes, $content) {
    global $dclt_attributes;
    $dclt_attributes = $attributes;
    
    ob_start();
    include get_template_directory() . '/blocks/cta/cta.php';
    return ob_get_clean();
}

// =============================================================================
// ADMIN META BOXES
// =============================================================================

/**
 * Add meta box for Hero block settings
 */
function dclt_add_hero_meta_box() {
    $post_types = ['page', 'post'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'dclt-hero-fields',
            'Hero Block Settings',
            'dclt_hero_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'dclt_add_hero_meta_box');

/**
 * Add meta box for CTA block settings
 */
function dclt_add_cta_meta_box() {
    $post_types = ['page', 'post'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'dclt-cta-fields',
            'CTA Block Settings',
            'dclt_cta_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'dclt_add_cta_meta_box');

// Include meta box functions
require_once get_template_directory() . '/blocks/hero/hero-meta-box.php';
require_once get_template_directory() . '/blocks/cta/cta-meta-box.php';

// =============================================================================
// PERFORMANCE OPTIMIZED ASSET LOADING
// =============================================================================

// Smart conditional asset loading - Only load CSS/JS for blocks actually used on the page
function dclt_enqueue_block_assets_conditionally() {
    global $post;
    
    if (!$post) {
        $post = get_queried_object();
    }
    
    if (!$post || !isset($post->post_content)) {
        return;
    }
    
    $blocks_used = [];
    
    // Check which blocks are actually used on this page
    if (has_block('dclt/hero', $post)) {
        $blocks_used[] = 'hero';
    }
    if (has_block('dclt/cta', $post)) {
        $blocks_used[] = 'cta';
    }
    
    // Only load shared assets if ANY blocks are used
    if (!empty($blocks_used)) {
        wp_enqueue_style(
            'dclt-blocks-shared',
            get_template_directory_uri() . '/blocks/shared/blocks.css',
            [],
            '1.0.0'
        );
        
        wp_enqueue_script(
            'dclt-blocks-shared',
            get_template_directory_uri() . '/blocks/shared/blocks.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
    
    // Load individual block assets only if used
    foreach ($blocks_used as $block) {
        dclt_enqueue_individual_block_assets($block);
    }
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_block_assets_conditionally');

// Load individual block assets
function dclt_enqueue_individual_block_assets($block_name) {
    $block_path = get_template_directory();
    $block_url = get_template_directory_uri();
    
    // CSS - only if file exists
    $css_file = "{$block_path}/blocks/{$block_name}/{$block_name}.css";
    if (file_exists($css_file)) {
        wp_enqueue_style(
            "dclt-{$block_name}",
            "{$block_url}/blocks/{$block_name}/{$block_name}.css",
            ['dclt-blocks-shared'],
            '1.0.0'
        );
    }
    
    // JavaScript - only if file exists  
    $js_file = "{$block_path}/blocks/{$block_name}/{$block_name}.js";
    if (file_exists($js_file)) {
        wp_enqueue_script(
            "dclt-{$block_name}",
            "{$block_url}/blocks/{$block_name}/{$block_name}.js",
            ['dclt-blocks-shared'],
            '1.0.0',
            true
        );
    }
}

// Remove WordPress bloat for faster loading
function dclt_remove_wordpress_bloat() {
    // Remove emoji scripts (saves ~15KB)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Remove block editor CSS on frontend (saves ~20KB if not using core blocks)
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'dclt_remove_wordpress_bloat');

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

// Helper function to get block spacing class
function dclt_get_spacing_class($spacing = 'medium') {
    $spacing_map = [
        'small' => 'py-8',
        'medium' => 'py-16',
        'large' => 'py-24',
        'xlarge' => 'py-32'
    ];
    
    return isset($spacing_map[$spacing]) ? $spacing_map[$spacing] : $spacing_map['medium'];
}

// Helper function to get container width class
function dclt_get_container_class($width = 'content') {
    $container_map = [
        'narrow' => 'max-w-3xl mx-auto px-4',
        'content' => 'max-w-6xl mx-auto px-4', 
        'wide' => 'max-w-7xl mx-auto px-4',
        'full' => 'w-full px-4'
    ];
    
    return isset($container_map[$width]) ? $container_map[$width] : $container_map['content'];
}

// Helper function for button classes
function dclt_get_button_class($style = 'primary') {
    $button_map = [
        'primary' => 'bg-brand text-white hover:bg-brand-700 px-6 py-3 rounded-lg font-semibold transition-colors duration-200',
        'secondary' => 'bg-white text-brand border-2 border-brand hover:bg-brand hover:text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200',
        'landowner' => 'bg-primary-700 text-white hover:bg-primary-800 px-8 py-4 rounded-lg font-bold text-lg transition-colors duration-200',
        'explore' => 'bg-green-600 text-white hover:bg-green-700 px-6 py-3 rounded-lg font-semibold transition-colors duration-200'
    ];
    
    return isset($button_map[$style]) ? $button_map[$style] : $button_map['primary'];
}

// Helper function for CTA button classes
function dclt_get_cta_button_class($style, $background_style, $is_primary) {
    $base_classes = 'inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-lg transition-all duration-200 hover:transform hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Adjust button style based on background using semantic tokens
    if ($background_style === 'brand-primary' || $background_style === 'image') {
        // On dark backgrounds
        if ($style === 'primary' || ($is_primary && $style === 'secondary')) {
            return $base_classes . ' bg-button-primary text-text-inverse hover:bg-button-hover focus:ring-button-primary';
        } else {
            return $base_classes . ' border-2 border-button-primary text-button-primary hover:bg-button-primary hover:text-text-inverse focus:ring-button-primary';
        }
    } else {
        // On light backgrounds
        if ($style === 'primary') {
            return $base_classes . ' bg-button-primary text-text-inverse hover:bg-button-hover focus:ring-button-primary';
        } else {
            return $base_classes . ' border-2 border-button-primary text-button-primary hover:bg-button-primary hover:text-text-inverse focus:ring-button-primary';
        }
    }
}
?>