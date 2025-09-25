<?php
/**
 * Feature Grid Meta Box
 * Location: blocks/feature-grid/feature-grid-meta-box.php
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Add meta box for Feature Grid settings
 */
function dclt_add_feature_grid_meta_box() {
    add_meta_box(
        'dclt_feature_grid_settings',
        'Feature Grid Block Settings',
        'dclt_feature_grid_meta_box_callback',
        ['page', 'post'],
        'normal',
        'high'
    );
}

/**
 * Meta box callback function
 */
function dclt_feature_grid_meta_box_callback($post) {
    wp_nonce_field('dclt_feature_grid_meta_box', 'dclt_feature_grid_meta_box_nonce');

    // Get current values
    $title = dclt_get_field($post->ID, 'feature_grid_title', '');
    $subtitle = dclt_get_field($post->ID, 'feature_grid_subtitle', '');
    $columns = dclt_get_field($post->ID, 'feature_grid_columns', '', '3');
    $container = dclt_get_field($post->ID, 'feature_grid_container', '', 'content');
    $spacing = dclt_get_field($post->ID, 'feature_grid_spacing', '', 'medium');

    echo '<div class="dclt-meta-box-wrap" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 10px 0;">';

    // Header section
    echo '<h3 style="margin-top: 0; color: #065f46; border-bottom: 2px solid #065f46; padding-bottom: 10px;">Section Header</h3>';

    echo '<table class="form-table" style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">';

    // Title field
    echo '<tr>';
    echo '<th><label for="dclt_feature_grid_title">Section Title</label></th>';
    echo '<td>';
    echo '<input type="text" id="dclt_feature_grid_title" name="dclt_feature_grid_title" value="' . esc_attr($title) . '" style="width: 100%; max-width: 400px;" />';
    echo '<p class="description">Optional title for the feature grid section</p>';
    echo '</td>';
    echo '</tr>';

    // Subtitle field
    echo '<tr>';
    echo '<th><label for="dclt_feature_grid_subtitle">Section Subtitle</label></th>';
    echo '<td>';
    echo '<textarea id="dclt_feature_grid_subtitle" name="dclt_feature_grid_subtitle" rows="2" style="width: 100%; max-width: 500px;">' . esc_textarea($subtitle) . '</textarea>';
    echo '<p class="description">Optional subtitle or description</p>';
    echo '</td>';
    echo '</tr>';

    echo '</table>';

    // Layout section
    echo '<h3 style="color: #065f46; border-bottom: 2px solid #065f46; padding-bottom: 10px;">Layout Settings</h3>';

    echo '<table class="form-table" style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">';

    // Columns
    echo '<tr>';
    echo '<th><label for="dclt_feature_grid_columns">Grid Columns</label></th>';
    echo '<td>';
    echo '<select id="dclt_feature_grid_columns" name="dclt_feature_grid_columns">';
    echo '<option value="2"' . selected($columns, '2', false) . '>2 Columns</option>';
    echo '<option value="3"' . selected($columns, '3', false) . '>3 Columns</option>';
    echo '<option value="4"' . selected($columns, '4', false) . '>4 Columns</option>';
    echo '</select>';
    echo '<p class="description">Number of columns for the feature grid</p>';
    echo '</td>';
    echo '</tr>';

    // Container width
    echo '<tr>';
    echo '<th><label for="dclt_feature_grid_container">Container Width</label></th>';
    echo '<td>';
    echo '<select id="dclt_feature_grid_container" name="dclt_feature_grid_container">';
    echo '<option value="content"' . selected($container, 'content', false) . '>Content (Default)</option>';
    echo '<option value="wide"' . selected($container, 'wide', false) . '>Wide</option>';
    echo '<option value="full"' . selected($container, 'full', false) . '>Full Width</option>';
    echo '</select>';
    echo '<p class="description">Width of the content container</p>';
    echo '</td>';
    echo '</tr>';

    // Spacing
    echo '<tr>';
    echo '<th><label for="dclt_feature_grid_spacing">Section Spacing</label></th>';
    echo '<td>';
    echo '<select id="dclt_feature_grid_spacing" name="dclt_feature_grid_spacing">';
    echo '<option value="compact"' . selected($spacing, 'compact', false) . '>Compact</option>';
    echo '<option value="medium"' . selected($spacing, 'medium', false) . '>Medium (Default)</option>';
    echo '<option value="spacious"' . selected($spacing, 'spacious', false) . '>Spacious</option>';
    echo '</select>';
    echo '<p class="description">Vertical spacing around the section</p>';
    echo '</td>';
    echo '</tr>';

    echo '</table>';

    // Feature items section
    echo '<h3 style="color: #065f46; border-bottom: 2px solid #065f46; padding-bottom: 10px;">Feature Items (2-6 items)</h3>';

    // Icon options for dropdown
    $icon_options = [
        'none' => 'No Icon',
        'default' => 'Default Circle',
        'tree' => '🌳 Tree',
        'handshake' => '🤝 Handshake',
        'landscape' => '🏔️ Landscape',
        'bird' => '🐦 Bird',
        'shield' => '🛡️ Shield',
        'leaf' => '🍃 Leaf'
    ];

    for ($i = 1; $i <= 6; $i++) {
        $heading = dclt_get_field($post->ID, "feature_grid_item{$i}_heading", '');
        $description = dclt_get_field($post->ID, "feature_grid_item{$i}_description", '');
        $cta_text = dclt_get_field($post->ID, "feature_grid_item{$i}_cta_text", '');
        $cta_url = dclt_get_field($post->ID, "feature_grid_item{$i}_cta_url", '');
        $icon = dclt_get_field($post->ID, "feature_grid_item{$i}_icon", '', 'default');

        echo '<div style="background: white; padding: 20px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #065f46;">';
        echo '<h4 style="margin-top: 0; color: #065f46;">Feature Item ' . $i . '</h4>';

        echo '<table class="form-table">';

        // Heading
        echo '<tr>';
        echo '<th style="width: 150px;"><label for="dclt_feature_grid_item' . $i . '_heading">Heading</label></th>';
        echo '<td>';
        echo '<input type="text" id="dclt_feature_grid_item' . $i . '_heading" name="dclt_feature_grid_item' . $i . '_heading" value="' . esc_attr($heading) . '" style="width: 100%; max-width: 400px;" />';
        echo '</td>';
        echo '</tr>';

        // Description
        echo '<tr>';
        echo '<th><label for="dclt_feature_grid_item' . $i . '_description">Description</label></th>';
        echo '<td>';
        echo '<textarea id="dclt_feature_grid_item' . $i . '_description" name="dclt_feature_grid_item' . $i . '_description" rows="3" style="width: 100%; max-width: 500px;">' . esc_textarea($description) . '</textarea>';
        echo '</td>';
        echo '</tr>';

        // Icon
        echo '<tr>';
        echo '<th><label for="dclt_feature_grid_item' . $i . '_icon">Icon</label></th>';
        echo '<td>';
        echo '<select id="dclt_feature_grid_item' . $i . '_icon" name="dclt_feature_grid_item' . $i . '_icon">';
        foreach ($icon_options as $value => $label) {
            echo '<option value="' . esc_attr($value) . '"' . selected($icon, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '</td>';
        echo '</tr>';

        // CTA Text
        echo '<tr>';
        echo '<th><label for="dclt_feature_grid_item' . $i . '_cta_text">CTA Text</label></th>';
        echo '<td>';
        echo '<input type="text" id="dclt_feature_grid_item' . $i . '_cta_text" name="dclt_feature_grid_item' . $i . '_cta_text" value="' . esc_attr($cta_text) . '" style="width: 200px;" placeholder="e.g., Learn More" />';
        echo '</td>';
        echo '</tr>';

        // CTA URL
        echo '<tr>';
        echo '<th><label for="dclt_feature_grid_item' . $i . '_cta_url">CTA URL</label></th>';
        echo '<td>';
        echo '<input type="url" id="dclt_feature_grid_item' . $i . '_cta_url" name="dclt_feature_grid_item' . $i . '_cta_url" value="' . esc_attr($cta_url) . '" style="width: 100%; max-width: 400px;" placeholder="https://example.com" />';
        echo '</td>';
        echo '</tr>';

        echo '</table>';
        echo '</div>';
    }

    echo '<div style="background: #e8f5e8; padding: 15px; border-radius: 6px; border-left: 4px solid #22c55e;">';
    echo '<p style="margin: 0;"><strong>💡 Tips:</strong></p>';
    echo '<ul style="margin: 10px 0 0 20px;">';
    echo '<li>Only items with a heading or description will be displayed</li>';
    echo '<li>CTA (Call-to-Action) buttons are optional</li>';
    echo '<li>Icons help make your features more visually appealing</li>';
    echo '<li>Keep descriptions concise for better readability</li>';
    echo '</ul>';
    echo '</div>';

    echo '</div>';
}

/**
 * Save meta box data
 */
function dclt_save_feature_grid_meta_box($post_id) {
    // Verify nonce
    if (!isset($_POST['dclt_feature_grid_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['dclt_feature_grid_meta_box_nonce'], 'dclt_feature_grid_meta_box')) {
        return;
    }

    // Check if user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Don't save during autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save header fields
    $fields = [
        'feature_grid_title',
        'feature_grid_subtitle',
        'feature_grid_columns',
        'feature_grid_container',
        'feature_grid_spacing'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save feature items (1-6)
    for ($i = 1; $i <= 6; $i++) {
        $item_fields = [
            "feature_grid_item{$i}_heading",
            "feature_grid_item{$i}_description",
            "feature_grid_item{$i}_cta_text",
            "feature_grid_item{$i}_cta_url",
            "feature_grid_item{$i}_icon"
        ];

        foreach ($item_fields as $field) {
            if (isset($_POST[$field])) {
                if (strpos($field, '_description') !== false) {
                    dclt_update_field($post_id, $field, sanitize_textarea_field($_POST[$field]));
                } elseif (strpos($field, '_cta_url') !== false) {
                    dclt_update_field($post_id, $field, esc_url_raw($_POST[$field]));
                } else {
                    dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
                }
            }
        }
    }
}