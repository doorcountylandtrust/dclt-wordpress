<?php
/**
 * Door County Land Trust — Modular Block Registration (No ACF)
 * Safe helpers, admin-safe render callbacks, conditional assets.
 */

if (!defined('ABSPATH')) { exit; }

/* ============================================================================
 * Helpers (guarded to avoid redeclare fatals)
 * ========================================================================== */
if (!function_exists('dclt_get_field')) {
    function dclt_get_field($post_id, $field_name, $block_id = '', $default = '') {
        $key   = $block_id ? "dclt_{$block_id}_{$field_name}" : "dclt_{$field_name}";
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}
if (!function_exists('dclt_update_field')) {
    function dclt_update_field($post_id, $field_name, $value, $block_id = '') {
        $key = $block_id ? "dclt_{$block_id}_{$field_name}" : "dclt_{$field_name}";
        return update_post_meta($post_id, $key, $value);
    }
}

/* ============================================================================
 * Utilities (spacing/container/buttons)
 * ========================================================================== */
if (!function_exists('dclt_get_spacing_class')) {
    function dclt_get_spacing_class($spacing = 'medium') {
        $map = [
            'compact'  => 'py-8 md:py-12',
            'medium'   => 'py-16 md:py-20',
            'spacious' => 'py-24 md:py-32',
        ];
        return isset($map[$spacing]) ? $map[$spacing] : $map['medium'];
    }
}
if (!function_exists('dclt_get_container_class')) {
    function dclt_get_container_class($width = 'content') {
        $map = [
            'content' => 'max-w-6xl mx-auto px-4 sm:px-6 lg:px-8',
            'wide'    => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
            'full'    => 'w-full px-4 sm:px-6 lg:px-8',
        ];
        return isset($map[$width]) ? $map[$width] : $map['content'];
    }
}
if (!function_exists('dclt_get_button_class')) {
    function dclt_get_button_class($style = 'primary') {
        $base = 'inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
        $map = [
            'primary'   => $base.' bg-brand-700 text-white hover:bg-brand-800 focus:ring-brand-500 shadow-sm',
            'secondary' => $base.' bg-white text-brand-700 border-2 border-brand-700 hover:bg-brand-50 focus:ring-brand-500',
            'outline'   => $base.' border-2 border-current text-current hover:bg-current hover:text-white focus:ring-current',
            'landowner' => $base.' bg-brand-700 text-white hover:bg-brand-800 px-8 py-4 text-lg font-bold focus:ring-brand-500',
            'explore'   => $base.' bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        ];
        return isset($map[$style]) ? $map[$style] : $map['primary'];
    }
}

/* ============================================================================
 * Block registration
 * ========================================================================== */
function dclt_register_blocks() {
    // Register editor scripts
    wp_register_script(
        'dclt-hero-block-editor',
        get_template_directory_uri() . '/blocks/hero/hero-editor.js',
        ['wp-blocks','wp-element','wp-editor','wp-components','wp-i18n'],
        '1.1.0',
        true
    );

    wp_register_script(
        'dclt-stats-block-editor',
        get_template_directory_uri() . '/blocks/stats/stats-editor.js',
        ['wp-blocks','wp-element','wp-editor','wp-components','wp-i18n'],
        '1.0.0',
        true
    );

    wp_register_script(
        'dclt-cta-block-editor',
        get_template_directory_uri() . '/blocks/cta/cta-editor.js',
        ['wp-blocks','wp-element','wp-editor','wp-components','wp-i18n'],
        '1.0.0',
        true
    );

    // Register blocks (server-rendered)
    register_block_type('dclt/hero', [
        'editor_script'   => 'dclt-hero-block-editor',
        'render_callback' => 'dclt_render_hero_block',
        'attributes'      => [
            'blockId' => ['type'=>'string','default'=>''],
        ],
    ]);

    register_block_type('dclt/stats', [
        'editor_script'   => 'dclt-stats-block-editor',
        'render_callback' => 'dclt_render_stats_block',
        'attributes'      => [
            'blockId' => ['type'=>'string','default'=>''],
        ],
    ]);

    register_block_type('dclt/cta', [
        'editor_script'   => 'dclt-cta-block-editor',
        'render_callback' => 'dclt_render_cta_block',
        'attributes'      => [
            'blockId' => ['type'=>'string','default'=>''],
        ],
    ]);
}
add_action('init', 'dclt_register_blocks');

/* ============================================================================
 * Render callbacks (admin-safe)
 * ========================================================================== */
function dclt_render_hero_block($attributes, $content) {
    // Prevent template fatals from crashing wp-admin lists
    if (is_admin() && !wp_doing_ajax()) { return '<!-- dclt/hero: admin bypass -->'; }

    global $dclt_attributes;
    $dclt_attributes = $attributes;

    $template = get_template_directory() . '/blocks/hero/hero.php';
    if (file_exists($template)) {
        ob_start();
        include $template;
        return ob_get_clean();
    }
    return '<section class="dclt-hero-block" style="padding:2rem;border:2px dashed #065f46;border-radius:12px">Missing hero.php</section>';
}

function dclt_render_stats_block($attributes, $content) {
    if (is_admin() && !wp_doing_ajax()) { return '<!-- dclt/stats: admin bypass -->'; }

    global $dclt_attributes;
    $dclt_attributes = $attributes;

    $template = get_template_directory() . '/blocks/stats/stats.php';
    if (file_exists($template)) {
        ob_start();
        include $template;
        return ob_get_clean();
    }
    return '<div class="dclt-error">Stats block template not found</div>';
}

function dclt_render_cta_block($attributes, $content) {
    if (is_admin() && !wp_doing_ajax()) { return '<!-- dclt/cta: admin bypass -->'; }

    global $dclt_attributes;
    $dclt_attributes = $attributes;

    $template = get_template_directory() . '/blocks/cta/cta.php';
    if (file_exists($template)) {
        ob_start();
        include $template;
        return ob_get_clean();
    }
    return '<div class="dclt-error">CTA block template not found</div>';
}

/* ============================================================================
 * Meta boxes (include files and register)
 * ========================================================================== */
// Include meta box files
$hero_meta = get_template_directory() . '/blocks/hero/hero-meta-box.php';
if (file_exists($hero_meta)) { require_once $hero_meta; }

$stats_meta = get_template_directory() . '/blocks/stats/stats-meta-box.php';
if (file_exists($stats_meta)) { require_once $stats_meta; }

$cta_meta = get_template_directory() . '/blocks/cta/cta-meta-box.php';
if (file_exists($cta_meta)) { require_once $cta_meta; }

// Add meta box registration function for stats (missing from your original file)
function dclt_add_stats_meta_box() {
    add_meta_box(
        'dclt_stats_settings',
        'Stats Block Settings',
        'dclt_stats_meta_box_callback',
        ['page', 'post'],
        'normal',
        'high'
    );
}

// Hook all meta boxes
if (function_exists('dclt_add_hero_meta_box')) {
    add_action('add_meta_boxes', 'dclt_add_hero_meta_box');
}
if (function_exists('dclt_save_hero_meta_box')) {
    add_action('save_post', 'dclt_save_hero_meta_box');
}

if (function_exists('dclt_add_stats_meta_box')) {
    add_action('add_meta_boxes', 'dclt_add_stats_meta_box');
}
if (function_exists('dclt_save_stats_meta_box')) {
    add_action('save_post', 'dclt_save_stats_meta_box');
}

if (function_exists('dclt_add_cta_meta_box')) {
    add_action('add_meta_boxes', 'dclt_add_cta_meta_box');
}
if (function_exists('dclt_save_cta_meta_box')) {
    add_action('save_post', 'dclt_save_cta_meta_box');
}

// Enqueue media library for meta boxes
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
    }
});

/* ============================================================================
 * Conditional asset loading
 * ========================================================================== */
function dclt_enqueue_block_assets_conditionally() {
    if (is_admin()) { return; } // frontend only

    global $post;
    if (!$post) { $post = get_queried_object(); }
    if (!$post || empty($post->post_content)) { return; }

    $used = [];
    if (has_block('dclt/hero', $post)) { $used[] = 'hero'; }
    if (has_block('dclt/cta',  $post)) { $used[] = 'cta';  }
    if (has_block('dclt/stats', $post)) { $used[] = 'stats';}
    
    if (!empty($used)) {
        $shared_css = get_template_directory() . '/blocks/shared/blocks.css';
        if (file_exists($shared_css)) {
            wp_enqueue_style('dclt-blocks-shared', get_template_directory_uri().'/blocks/shared/blocks.css', [], '1.0.0');
        }
        $shared_js = get_template_directory() . '/blocks/shared/blocks.js';
        if (file_exists($shared_js)) {
            wp_enqueue_script('dclt-blocks-shared', get_template_directory_uri().'/blocks/shared/blocks.js', ['jquery'], '1.0.0', true);
        }
    }

    foreach ($used as $block) {
        $base = get_template_directory();
        $url  = get_template_directory_uri();
        $css  = "{$base}/blocks/{$block}/{$block}.css";
        $js   = "{$base}/blocks/{$block}/{$block}.js";

        if (file_exists($css)) {
            wp_enqueue_style("dclt-{$block}", "{$url}/blocks/{$block}/{$block}.css", ['dclt-blocks-shared'], '1.1.0');
        }
        if (file_exists($js)) {
            wp_enqueue_script("dclt-{$block}", "{$url}/blocks/{$block}/{$block}.js", ['dclt-blocks-shared'], '1.1.0', true);
        }
    }
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_block_assets_conditionally');

function dclt_remove_wordpress_bloat() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'dclt_remove_wordpress_bloat', 100);

/* ============================================================================
 * Brand CSS variables (optional helper)
 * ========================================================================== */
function dclt_add_brand_colors() {
    echo '<style>:root{
        --brand-50:#f0fdf4;--brand-100:#dcfce7;--brand-200:#bbf7d0;--brand-300:#86efac;
        --brand-400:#4ade80;--brand-500:#22c55e;--brand-600:#16a34a;--brand-700:#065f46;
        --brand-800:#047857;--brand-900:#064e3b;
    }</style>';
}
add_action('wp_head', 'dclt_add_brand_colors');