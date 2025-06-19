<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DCLT_Preserve_Post_Type {
    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type() {
        $labels = [
            'name' => 'Preserves',
            'singular_name' => 'Preserve',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Preserve',
            'edit_item' => 'Edit Preserve',
            'new_item' => 'New Preserve',
            'view_item' => 'View Preserve',
            'search_items' => 'Search Preserves',
            'not_found' => 'No preserves found',
            'not_found_in_trash' => 'No preserves found in Trash',
            'all_items' => 'All Preserves',
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-location-alt',
            'capability_type' => 'post',
            'hierarchical' => false,
            'has_archive' => true,
            'rewrite' => ['slug' => 'preserve'],
            'supports' => ['title', 'editor', 'excerpt', 'custom-fields', 'thumbnail'],
            'show_in_rest' => true,
            'rest_base' => 'preserves',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];

        register_post_type('preserve', $args);
    }
}