<?php 
function dclt_theme_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'dclt_theme_setup');

function dclt_enqueue_assets() {
    wp_enqueue_style('dclt-style', get_stylesheet_uri());
    wp_enqueue_script('dclt-react-app', get_template_directory_uri() . '/build/index.js', [], false, true);
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_assets');

?>