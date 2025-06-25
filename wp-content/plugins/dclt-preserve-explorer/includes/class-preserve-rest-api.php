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
        // Core preserve information fields
        $core_fields = [
            'preserve_lat' => 'string',
            'preserve_lng' => 'string',
            'preserve_acres' => 'string',
            'preserve_trail_length' => 'string',
            'preserve_established' => 'string',
        ];

        // GeoJSON file fields
        $file_fields = [
            'preserve_boundary_file' => 'string',
            'preserve_trail_file' => 'string',
            'preserve_accessible_trails_file' => 'string',
            'preserve_boardwalk_file' => 'string',
            'preserve_structures_file' => 'string',
            'preserve_parking_file' => 'string',
        ];

        // Filter fields (arrays)
        $filter_fields = [
            'preserve_filter_region' => 'array',
            'preserve_filter_activity' => 'array',
            'preserve_filter_ecology' => 'array',
            'preserve_filter_difficulty' => 'array',
            'preserve_filter_available_facilities' => 'array',
            'preserve_filter_trail_surface' => 'array',
            'preserve_filter_accessibility' => 'array',
            'preserve_filter_physical_challenges' => 'array',
            'preserve_filter_notable_features' => 'array',
            'preserve_filter_photography' => 'array',
            'preserve_filter_educational' => 'array',
            'preserve_filter_wildlife_spotting' => 'array',
            'preserve_filter_habitat_diversity' => 'array',
            'preserve_filter_map_features' => 'array',
        ];

        // Register core fields (strings)
        foreach ($core_fields as $field => $type) {
            register_post_meta('preserve', "_{$field}", [
                'type' => $type,
                'single' => true,
                'show_in_rest' => true,
                'auth_callback' => function() { return current_user_can('edit_posts'); },
                'sanitize_callback' => 'sanitize_text_field',
            ]);
        }

        // Register file fields (URLs)
        foreach ($file_fields as $field => $type) {
            register_post_meta('preserve', "_{$field}", [
                'type' => $type,
                'single' => true,
                'show_in_rest' => true,
                'auth_callback' => function() { return current_user_can('edit_posts'); },
                'sanitize_callback' => 'esc_url_raw',
            ]);
        }

        // Register filter fields (arrays)
        foreach ($filter_fields as $field => $type) {
            register_post_meta('preserve', "_{$field}", [
                'type' => $type,
                'single' => true,
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'string'
                        ]
                    ]
                ],
                'auth_callback' => function() { return current_user_can('edit_posts'); },
                'sanitize_callback' => [$this, 'sanitize_filter_array'],
            ]);
        }
    }

    /**
     * Sanitize filter array values
     * 
     * @param array $value The array of filter values
     * @return array Sanitized array
     */
    public function sanitize_filter_array($value) {
        if (!is_array($value)) {
            return [];
        }
        
        return array_map('sanitize_key', array_filter($value));
    }
}