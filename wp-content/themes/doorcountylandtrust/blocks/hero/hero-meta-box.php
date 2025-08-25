<?php
/**
 * Hero Block Meta Box
 * File: blocks/hero/hero-meta-box.php
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Register the meta box (MAIN COLUMN)
 */
function dclt_add_hero_meta_box() {
    foreach (['page','post'] as $pt) {
        add_meta_box(
            'dclt-hero-fields',
            'Hero Block Settings',
            'dclt_hero_meta_box_callback',
            $pt,
            'normal',   // put it in the main column (not the sidebar)
            'high'
        );
    }
}

/**
 * Meta box UI
 */
function dclt_hero_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('dclt_hero_meta_box', 'dclt_hero_meta_box_nonce');

    // Current values
    $headline         = dclt_get_field($post->ID, 'hero_headline', '');
    $subheadline      = dclt_get_field($post->ID, 'hero_subheadline', '');
    $primary_cta_text = dclt_get_field($post->ID, 'hero_primary_cta_text', '', 'Protect Your Land');
    $primary_cta_url  = dclt_get_field($post->ID, 'hero_primary_cta_url', '');
    $background_type  = dclt_get_field($post->ID, 'hero_background_type', '', 'image');
    $background_image = dclt_get_field($post->ID, 'hero_background_image', '');
    $background_color = dclt_get_field($post->ID, 'hero_background_color', '', '#006847');
    $overlay_opacity  = dclt_get_field($post->ID, 'hero_overlay_opacity', '', '40');
    ?>
    <style>
    .dclt-hero-meta { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .dclt-field { margin-bottom: 16px; }
    .dclt-field label { display:block; font-weight:600; margin-bottom:6px; color:#1e1e1e; font-size:13px; }
    .dclt-field input[type="text"],
    .dclt-field input[type="url"],
    .dclt-field textarea,
    .dclt-field select { width:100%; max-width:500px; padding:8px 12px; border:1px solid #8c8f94; border-radius:4px; font-size:13px; }
    .dclt-field input[type="color"] { width:60px; height:40px; padding:0; border:1px solid #8c8f94; border-radius:4px; }
    .dclt-field .description { color:#646970; font-size:12px; margin-top:4px; line-height:1.4; }
    .dclt-media-button { margin-top:6px; margin-right:8px; }
    .dclt-image-preview { max-width:200px; margin-top:8px; border:1px solid #ddd; border-radius:4px; overflow:hidden; }
    .dclt-image-preview img { max-width:100%; height:auto; display:block; }
    </style>

    <div class="dclt-hero-meta">
        <div class="dclt-field">
            <label for="hero_headline">Headline</label>
            <input type="text" id="hero_headline" name="hero_headline"
                   value="<?php echo esc_attr($headline); ?>" placeholder="Protect the Land You Love">
            <div class="description">Keep concise and impactful</div>
        </div>

        <div class="dclt-field">
            <label for="hero_subheadline">Subheadline</label>
            <textarea id="hero_subheadline" name="hero_subheadline" rows="2"
                      placeholder="Supporting message about conservation"><?php echo esc_textarea($subheadline); ?></textarea>
            <div class="description">1–2 sentences max</div>
        </div>

        <div class="dclt-field">
            <label for="hero_primary_cta_text">Primary CTA Text</label>
            <input type="text" id="hero_primary_cta_text" name="hero_primary_cta_text"
                   value="<?php echo esc_attr($primary_cta_text); ?>" placeholder="Protect Your Land">
            <div class="description">Action-oriented button text</div>
        </div>

        <div class="dclt-field">
            <label for="hero_primary_cta_url">Primary CTA URL</label>
            <input type="url" id="hero_primary_cta_url" name="hero_primary_cta_url"
                   value="<?php echo esc_attr($primary_cta_url); ?>">
            <div class="description">Required for button to appear</div>
        </div>

        <div class="dclt-field">
            <label for="hero_background_type">Background Type</label>
            <select id="hero_background_type" name="hero_background_type">
                <option value="image" <?php selected($background_type, 'image'); ?>>Image</option>
                <option value="color" <?php selected($background_type, 'color'); ?>>Solid Color</option>
            </select>
        </div>

        <!-- Image Background -->
        <div class="dclt-field" id="background_image_field" style="<?php echo $background_type !== 'image' ? 'display:none;' : ''; ?>">
            <label for="hero_background_image">Background Image</label>
            <input type="hidden" id="hero_background_image" name="hero_background_image"
                   value="<?php echo esc_attr($background_image); ?>">
            <button type="button" class="dclt-media-button button">Choose Image</button>
            <button type="button" class="dclt-remove-media button" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">Remove</button>
            <div class="dclt-image-preview" id="image-preview" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">
                <?php if ($background_image) echo wp_get_attachment_image($background_image, 'medium'); ?>
            </div>
            <div class="description">Recommended: 1920×1080px or larger</div>
        </div>

        <!-- Color Background -->
        <div class="dclt-field" id="background_color_field" style="<?php echo $background_type !== 'color' ? 'display:none;' : ''; ?>">
            <label for="hero_background_color">Background Color</label>
            <input type="color" id="hero_background_color" name="hero_background_color"
                   value="<?php echo esc_attr($background_color); ?>">
            <div class="description">Brand green default recommended</div>
        </div>

        <div class="dclt-field" id="overlay_field" style="<?php echo ($background_type === 'color') ? 'display:none;' : ''; ?>">
            <label for="hero_overlay_opacity">Overlay Opacity (%)</label>
            <input type="range" id="hero_overlay_opacity" name="hero_overlay_opacity"
                   value="<?php echo esc_attr($overlay_opacity); ?>" min="0" max="80" step="10">
            <span id="opacity-value"><?php echo esc_html($overlay_opacity); ?>%</span>
            <div class="description">Improves text readability over images</div>
        </div>
    </div>

    <script>
    jQuery(function($) {
        $('#hero_background_type').on('change', function() {
            const type = $(this).val();
            $('#background_image_field, #background_color_field').hide();
            $('#background_' + type + '_field').show();
            $('#overlay_field').toggle(type !== 'color');
        });

        $('#hero_overlay_opacity').on('input', function() {
            $('#opacity-value').text($(this).val() + '%');
        });

        $('.dclt-media-button').on('click', function(e) {
            e.preventDefault();
            const media_uploader = wp.media({
                title: 'Choose Hero Background Image',
                button: { text: 'Use This Image' },
                multiple: false,
                library: { type: 'image' }
            });
            media_uploader.on('select', function() {
                const attachment = media_uploader.state().get('selection').first().toJSON();
                $('#hero_background_image').val(attachment.id);
                $('#image-preview').html('<img src="'+attachment.url+'" alt="">').show();
                $('.dclt-remove-media').show();
            });
            media_uploader.open();
        });

        $('.dclt-remove-media').on('click', function(e) {
            e.preventDefault();
            $('#hero_background_image').val('');
            $('#image-preview').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Save handler
 */
function dclt_save_hero_meta_box($post_id) {
    if (!isset($_POST['dclt_hero_meta_box_nonce']) || !wp_verify_nonce($_POST['dclt_hero_meta_box_nonce'], 'dclt_hero_meta_box')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = [
        'hero_headline',
        'hero_subheadline',
        'hero_primary_cta_text',
        'hero_primary_cta_url',
        'hero_background_type',
        'hero_background_image',
        'hero_background_color',
        'hero_overlay_opacity',
    ];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}