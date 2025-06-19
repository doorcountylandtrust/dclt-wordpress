<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DCLT_Preserve_REST_API {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_meta_fields']);
    }

    public function register_meta_fields() {
        $fields = [
            'preserve_lat' => 'string',
            'preserve_lng' => 'string',
            'preserve_acres' => 'string',
            'preserve_difficulty' => 'string',
            'preserve_trail_length' => 'string',
            'preserve_established' => 'string',
            'preserve_parking' => 'string',
            'preserve_facilities' => 'string',
            'preserve_boundary_file' => 'string',
            'preserve_trail_file' => 'string',
        ];

        foreach ($fields as $field => $type) {
            register_post_meta('preserve', "_{$field}", [
                'type' => $type,
                'single' => true,
                'show_in_rest' => true,
                'auth_callback' => function() { return true; },
            ]);
        }
    }
}
