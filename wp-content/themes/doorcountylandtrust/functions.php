<?php
/**
 * Theme bootstrap for Door County Land Trust
 * Keep this file lean; block system lives in inc/blocks/blocks-init.php
 */

/* -------------------------------------------------------------
 * Assets
 * ----------------------------------------------------------- */
function dclt_enqueue_assets() {
    // Frontend CSS (Tailwind or plain CSS). Cache‑bust via filemtime in dev.
    $css_path = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style(
        'dclt-tailwind',
        get_stylesheet_directory_uri() . '/style.css',
        [],
        file_exists($css_path) ? filemtime($css_path) : null
    );
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_assets');

/**
 * Make the editor roughly reflect frontend typography/spacing.
 * (Keep light to avoid fighting Gutenberg.)
 */
function dclt_add_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('style.css');
}
add_action('after_setup_theme', 'dclt_add_editor_styles');

/* -------------------------------------------------------------
 * Theme supports
 * ----------------------------------------------------------- */
function dclt_theme_setup() {
    // Core niceties
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);

    // Custom logo
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // Menus
 register_nav_menus([
  'primary'        => __('Primary Navigation', 'dclt'),
  'header-cta'     => __('Header CTA Button', 'dclt'),
  'footer'         => __('Footer Navigation', 'dclt'), // keep if you already use it
  'footer_about'   => __('Footer – About', 'dclt'),
  'footer_resources'=> __('Footer – Explore & Learn', 'dclt'),
  'footer_engage'  => __('Footer – Protect & Give', 'dclt'),
  'footer_support' => __('Footer – More', 'dclt'),
  'footer_legal'   => __('Footer – Legal', 'dclt'),
]);

    // Image sizes tuned for hero usage
    add_image_size('dclt-hero-xl', 1920, 1080, true);   // 16:9
    add_image_size('dclt-hero-2xl', 2400, 1350, true);  // large hero
}
add_action('after_setup_theme', 'dclt_theme_setup');

/* -------------------------------------------------------------
 * MIME / Upload helpers (GeoJSON support for maps)
 * ----------------------------------------------------------- */
function dclt_allow_json_uploads($mimes) {
    $mimes['json']    = 'application/json';
    $mimes['geojson'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'dclt_allow_json_uploads');

function dclt_check_json_filetype($data, $file, $filename, $mimes) {
    $ft = wp_check_filetype($filename, $mimes);
    if ($ft['ext'] === 'json' || $ft['ext'] === 'geojson') {
        $data['ext']  = $ft['ext'];
        $data['type'] = 'application/json';
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'dclt_check_json_filetype', 10, 4);

/* -------------------------------------------------------------
 * Includes
 * ----------------------------------------------------------- */
// Block system
$blocks_init = get_template_directory() . '/inc/blocks/blocks-init.php';
if (file_exists($blocks_init)) {
    require_once $blocks_init;
}

// Site Settings (Options page for org info / socials / newsletter)
$site_settings = get_template_directory() . '/inc/admin/site-settings.php';
if (file_exists($site_settings)) {
    require_once $site_settings;
}

/* -------------------------------------------------------------
 * Menus: walkers + fallbacks
 * ----------------------------------------------------------- */
/**
 * Clean desktop walker (no extraneous classes).
 */
class DCLT_Walker_Nav_Menu extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $output .= '<li class="menu-item">';
        $output .= '<a href="' . esc_url($item->url) . '" class="hover:opacity-80 transition">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}

/**
 * Mobile walker: simple div stack for your off‑canvas.
 */
class DCLT_Walker_Mobile_Nav_Menu extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $output .= '<div class="mobile-menu-item">';
        $output .= '<a href="' . esc_url($item->url) . '" class="block py-3">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</div>';
    }
}

/**
 * Fallback menus if none are assigned (keeps header usable).
 */
function dclt_fallback_menu() {
    echo '<ul class="primary-menu flex items-center space-x-8">';
    echo '<li><a href="' . esc_url( home_url('/about') ) . '">About</a></li>';
    echo '<li><a href="' . esc_url( home_url('/protect-your-land') ) . '">Protect Your Land</a></li>';
    echo '<li><a href="' . esc_url( home_url('/get-involved') ) . '">Get Involved</a></li>';
    echo '</ul>';
}
function dclt_mobile_fallback_menu() {
    echo '<div class="mobile-primary-menu space-y-2">';
    echo '<div class="mobile-menu-item"><a href="' . esc_url( home_url('/about') ) . '">About</a></div>';
    echo '<div class="mobile-menu-item"><a href="' . esc_url( home_url('/protect-your-land') ) . '">Protect Your Land</a></div>';
    echo '<div class="mobile-menu-item"><a href="' . esc_url( home_url('/get-involved') ) . '">Get Involved</a></div>';
    echo '</div>';
}

/**
 * Header CTA fallback if no menu item exists in `header-cta`.
 */
function dclt_header_cta_fallback() {
    echo '<a href="' . esc_url( home_url('/donate') ) . '" class="header-cta">Donate</a>';
}

/* -------------------------------------------------------------
 * Misc
 * ----------------------------------------------------------- */
// Tiny helper so page templates work in classic themes (WP quirk).
add_action('init', function () {
    add_theme_support('page-templates');
});

// Footer CSS (plain CSS so it works without Tailwind build)
add_action('wp_enqueue_scripts', function () {
    $footer_css = get_stylesheet_directory() . '/components/footer.css';
    if (file_exists($footer_css)) {
        wp_enqueue_style(
            'dclt-footer',
            get_stylesheet_directory_uri() . '/components/footer.css',
            [], filemtime($footer_css)
        );
    }
});