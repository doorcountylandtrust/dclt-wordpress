<?php
error_log('DCLT Theme: functions.php loaded');
/**
 * Theme bootstrap for Door County Land Trust
 * Keep this file lean; block system lives in inc/blocks/blocks-init.php
 */

/* -------------------------------------------------------------
 * Utilities
 * ----------------------------------------------------------- */

// Extracts Vimeo video ID from various URL formats
function dclt_extract_vimeo_id($url) {
    if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches)) {
        return $matches[1];
    }
    return false;
}

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

require_once get_template_directory() . '/inc/design-tokens.php';

/**
 * Enable SVG uploads
 */
function dclt_enable_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'dclt_enable_svg_upload');

/**
 * Fix SVG display in media library
 */
function dclt_fix_svg_display($response, $attachment, $meta) {
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
        $response['image'] = array(
            'src' => $response['url'],
            'width' => 150,
            'height' => 150,
        );
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'dclt_fix_svg_display', 10, 3);

/**
 * Basic SVG sanitization
 */
function dclt_sanitize_svg($file) {
    if ($file['type'] === 'image/svg+xml') {
        $svg_content = file_get_contents($file['tmp_name']);
        
        // Remove potentially dangerous elements
        $svg_content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $svg_content);
        $svg_content = preg_replace('/on\w+="[^"]*"/i', '', $svg_content);
        
        file_put_contents($file['tmp_name'], $svg_content);
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'dclt_sanitize_svg');

// Programs CPT
require_once get_template_directory() . '/inc/post-types/programs.php';


function dclt_enqueue_scripts() {
  $theme_uri = get_template_directory_uri();
  $theme_version = wp_get_theme()->get('Version');
  
  // Shared utilities - loads site-wide (small file)
  wp_enqueue_script(
    'dclt-utils',
    $theme_uri . '/assets/js/utils.js',
    array(), // no dependencies
    $theme_version,
    true // in footer
  );
  
  // Donation form - only on donate pages
  if (is_page_template('page-donate.php') || is_page('donate') || is_page('give')) {
    wp_enqueue_script(
      'dclt-donate',
      $theme_uri . '/assets/js/components/donate.js',
      array('dclt-utils'), // depends on utils
      $theme_version,
      true
    );
  }
  
  // Modal - only when needed (add conditions as needed)
  // Example: load on pages that have a modal trigger
  if (is_page_template('page-donate.php') || has_shortcode(get_post()->post_content ?? '', 'donation_modal')) {
    wp_enqueue_script(
      'dclt-modal',
      $theme_uri . '/assets/js/components/modal.js',
      array('dclt-utils'),
      $theme_version,
      true
    );
  }
  
  // Event registration - only on event pages
  if (is_singular('dclt_program') || is_post_type_archive('dclt_program')) {
    wp_enqueue_script(
      'dclt-events',
      $theme_uri . '/assets/js/components/events.js',
      array('dclt-utils'),
      $theme_version,
      true
    );
  }
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_scripts');


/**
 * Add defer attribute to scripts for non-blocking load
 */
function dclt_defer_scripts($tag, $handle, $src) {
  // Scripts to defer
  $defer_scripts = array(
    'dclt-utils',
    'dclt-donate',
    'dclt-modal',
    'dclt-events'
  );
  
  if (in_array($handle, $defer_scripts)) {
    return str_replace(' src', ' defer src', $tag);
  }
  
  return $tag;
}
add_filter('script_loader_tag', 'dclt_defer_scripts', 10, 3);


/**
 * Pass PHP data to JavaScript (optional - for dynamic config)
 */
function dclt_localize_scripts() {
  // Only if donate script is enqueued
  if (wp_script_is('dclt-donate', 'enqueued')) {
    wp_localize_script('dclt-donate', 'dcltDonateConfig', array(
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('dclt_donate_nonce'),
      'successUrl' => home_url('/thank-you/'),
      'cancelUrl' => get_permalink()
    ));
  }
}
add_action('wp_enqueue_scripts', 'dclt_localize_scripts', 20);


/**
 * Donation modal shortcode (optional)
 * Usage: [donation_modal text="Donate Now"]
 */
function dclt_donation_modal_shortcode($atts) {
  $atts = shortcode_atts(array(
    'text' => 'Donate',
    'class' => 'dclt-donate-btn',
    'amount' => '',
    'campaign' => ''
  ), $atts);
  
  $data_attrs = '';
  if ($atts['amount']) {
    $data_attrs .= ' data-amount="' . esc_attr($atts['amount']) . '"';
  }
  if ($atts['campaign']) {
    $data_attrs .= ' data-campaign="' . esc_attr($atts['campaign']) . '"';
  }
  
  return '<button type="button" class="' . esc_attr($atts['class']) . '" onclick="DCLT.modal.open(\'dclt-donate-modal\')"' . $data_attrs . '>' . esc_html($atts['text']) . '</button>';
}
add_shortcode('donation_modal', 'dclt_donation_modal_shortcode');


function dclt_donate_canonical() {
  if (is_page_template('page-donate.php')) {
    echo '<link rel="canonical" href="' . home_url('/donate/') . '" />' . "\n";
  }
}
add_action('wp_head', 'dclt_donate_canonical');