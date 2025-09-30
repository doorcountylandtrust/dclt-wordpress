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
    $secondary_cta_text = dclt_get_field($post->ID, 'hero_secondary_cta_text', '', '');
    $secondary_cta_url  = dclt_get_field($post->ID, 'hero_secondary_cta_url', '', '');
    $button_1_text    = dclt_get_field($post->ID, 'hero_button_1_text', '', $primary_cta_text);
    $button_1_url     = dclt_get_field($post->ID, 'hero_button_1_url', '', $primary_cta_url);
    $button_2_text    = dclt_get_field($post->ID, 'hero_button_2_text', '', $secondary_cta_text);
    $button_2_url     = dclt_get_field($post->ID, 'hero_button_2_url', '', $secondary_cta_url);
    $photo_credit     = dclt_get_field($post->ID, 'hero_photo_credit', '', '');
    $photo_note       = dclt_get_field($post->ID, 'hero_photo_note', '', '');
    $photo_website    = dclt_get_field($post->ID, 'hero_photo_website', '', '');
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
            <label for="hero_button_1_text">Button 1 Text</label>
            <input type="text" id="hero_button_1_text" name="hero_button_1_text"
                   value="<?php echo esc_attr($button_1_text); ?>" placeholder="Protect Your Land">
            <div class="description">Primary call-to-action label</div>
        </div>

        <div class="dclt-field">
            <label for="hero_button_1_url">Button 1 URL</label>
            <input type="url" id="hero_button_1_url" name="hero_button_1_url"
                   value="<?php echo esc_attr($button_1_url); ?>" placeholder="https://">
            <div class="description">Required for Button 1 to display</div>
        </div>

        <div class="dclt-field">
            <label for="hero_button_2_text">Button 2 Text</label>
            <input type="text" id="hero_button_2_text" name="hero_button_2_text"
                   value="<?php echo esc_attr($button_2_text); ?>" placeholder="Discover Our Work">
            <div class="description">Optional supporting action</div>
        </div>

        <div class="dclt-field">
            <label for="hero_button_2_url">Button 2 URL</label>
            <input type="url" id="hero_button_2_url" name="hero_button_2_url"
                   value="<?php echo esc_attr($button_2_url); ?>" placeholder="https://">
            <div class="description">Leave blank to hide Button 2</div>
        </div>

        <hr style="margin: 24px 0; border: 0; border-top: 1px solid #e0e0e0;">

        <div class="dclt-field">
            <label for="hero_photo_credit">Photo Credit</label>
            <input type="text" id="hero_photo_credit" name="hero_photo_credit"
                   value="<?php echo esc_attr($photo_credit); ?>" placeholder="Photo: Jane Smith">
            <div class="description">Short credit line displayed in the hero (optional)</div>
        </div>

        <div class="dclt-field">
            <label for="hero_photo_note">Photographer Note</label>
            <textarea id="hero_photo_note" name="hero_photo_note" rows="3"
                      placeholder="Reflection or context about the photo."><?php echo esc_textarea($photo_note); ?></textarea>
            <div class="description">Appears in an expandable panel below the credit</div>
        </div>

        <div class="dclt-field">
            <label for="hero_photo_website">Photographer Website</label>
            <input type="url" id="hero_photo_website" name="hero_photo_website"
                   value="<?php echo esc_attr($photo_website); ?>" placeholder="https://">
            <div class="description">Optional link shown inside the note panel</div>
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
        'hero_button_1_text',
        'hero_button_1_url',
        'hero_button_2_text',
        'hero_button_2_url',
        'hero_photo_credit',
        'hero_photo_note',
        'hero_photo_website',
        'hero_background_type',
        'hero_background_image',
        'hero_background_color',
        'hero_overlay_opacity',
    ];
    $url_fields = [
        'hero_primary_cta_url',
        'hero_button_1_url',
        'hero_button_2_url',
        'hero_photo_website',
    ];
    $textarea_fields = [
        'hero_photo_note',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $raw   = wp_unslash($_POST[$field]);
            if (in_array($field, $url_fields, true)) {
                $value = esc_url_raw($raw);
            } elseif (in_array($field, $textarea_fields, true)) {
                $value = sanitize_textarea_field($raw);
            } else {
                $value = sanitize_text_field($raw);
            }
            dclt_update_field($post_id, $field, $value);
        }
    }
}
