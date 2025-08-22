<?php 
function dclt_enqueue_assets() {
    wp_enqueue_style(
        'dclt-tailwind',
        get_stylesheet_directory_uri() . '/style.css',
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_assets');

// Add this to wp-content/themes/your-theme/functions.php
function dclt_allow_json_uploads($mimes) {
    $mimes['json'] = 'application/json';
    $mimes['geojson'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'dclt_allow_json_uploads');

function dclt_check_json_filetype($data, $file, $filename, $mimes) {
    $filetype = wp_check_filetype($filename, $mimes);
    
    if ($filetype['ext'] === 'json' || $filetype['ext'] === 'geojson') {
        $data['ext'] = $filetype['ext'];
        $data['type'] = 'application/json';
    }
    
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'dclt_check_json_filetype', 10, 4);

add_action('init', function () {
    add_theme_support('page-templates');
});


require_once get_template_directory() . '/inc/blocks/blocks-init.php';

// Register Navigation Menus
function dclt_register_nav_menus() {
    register_nav_menus(array(
        'primary' => 'Primary Navigation',
        'header-cta' => 'Header CTA Button',
        'footer'  => 'Footer Navigation',
    ));
}
add_action('after_setup_theme', 'dclt_register_nav_menus');

function dclt_header_cta_fallback() {
    echo '<a href="/donate" class="header-cta">Donate</a>';
}

// Add theme support for custom logo
function dclt_theme_setup() {
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'dclt_theme_setup');

// Custom Walker for Desktop Navigation
class DCLT_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $output .= '<li class="menu-item">';
        $output .= '<a href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

// Custom Walker for Mobile Navigation
class DCLT_Walker_Mobile_Nav_Menu extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $output .= '<div class="mobile-menu-item">';
        $output .= '<a href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</div>';
    }
}

// Fallback menus if no menu is assigned
function dclt_fallback_menu() {
    echo '<ul class="primary-menu flex items-center space-x-8">';
    echo '<li><a href="' . home_url('/about') . '">About</a></li>';
    echo '<li><a href="' . home_url('/protect-your-land') . '">Protect Your Land</a></li>';
    echo '<li><a href="' . home_url('/get-involved') . '">Get Involved</a></li>';
    echo '</ul>';
}

function dclt_mobile_fallback_menu() {
    echo '<div class="mobile-primary-menu space-y-2">';
    echo '<div class="mobile-menu-item"><a href="' . home_url('/about') . '">About</a></div>';
    echo '<div class="mobile-menu-item"><a href="' . home_url('/protect-your-land') . '">Protect Your Land</a></div>';
    echo '<div class="mobile-menu-item"><a href="' . home_url('/get-involved') . '">Get Involved</a></div>';
    echo '</div>';
}
