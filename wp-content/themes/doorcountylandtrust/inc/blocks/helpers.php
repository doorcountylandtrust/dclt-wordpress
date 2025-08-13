<?php
if (!defined('ABSPATH')) exit;

/**
 * Meta key builder: dclt_{blockId?}_{field}
 */
if (!function_exists('dclt_meta_key')) {
    function dclt_meta_key($field_name, $block_id = '') {
        $prefix = 'dclt_';
        return $block_id ? "{$prefix}{$block_id}_{$field_name}" : "{$prefix}{$field_name}";
    }
}

/**
 * Get field from post meta with default
 */
if (!function_exists('dclt_get_field')) {
    function dclt_get_field($post_id, $field_name, $block_id = '', $default = '') {
        $key   = dclt_meta_key($field_name, $block_id);
        $value = get_post_meta($post_id, $key, true);
        return ($value === '' || $value === null) ? $default : $value;
    }
}

/**
 * Update field
 */
if (!function_exists('dclt_update_field')) {
    function dclt_update_field($post_id, $field_name, $value, $block_id = '') {
        $key = dclt_meta_key($field_name, $block_id);
        return update_post_meta($post_id, $key, $value);
    }
}