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

require_once get_template_directory() . '/inc/blocks/helpers.php';
require_once get_template_directory() . '/inc/blocks/blocks-init.php';