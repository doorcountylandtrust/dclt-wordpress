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

    // Get existing media items
    $hero_media = get_post_meta($post->ID, 'dclt_hero_media', true);
    if (!is_array($hero_media)) {
        $hero_media = [];
    }
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

    /* Repeater Styles */
    .dclt-repeater { border: 1px solid #ddd; border-radius: 6px; background: #fff; margin-bottom: 20px; }
    .dclt-repeater-header { background: #f9f9f9; padding: 12px 16px; border-bottom: 1px solid #ddd; font-weight: 600; color: #1e1e1e; }
    .dclt-repeater-items { padding: 0; }
    .dclt-repeater-item { padding: 16px; border-bottom: 1px solid #eee; position: relative; }
    .dclt-repeater-item:last-child { border-bottom: none; }
    .dclt-repeater-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .dclt-repeater-item-title { font-weight: 600; color: #1e1e1e; }
    .dclt-repeater-controls { display: flex; gap: 8px; }
    .dclt-repeater-btn { padding: 4px 8px; font-size: 12px; border: 1px solid #ccc; border-radius: 3px; background: #f7f7f7; cursor: pointer; text-decoration: none; color: #666; }
    .dclt-repeater-btn:hover { background: #e7e7e7; color: #333; }
    .dclt-repeater-btn.remove { color: #d63384; border-color: #d63384; }
    .dclt-repeater-btn.remove:hover { background: #d63384; color: white; }
    .dclt-add-item { margin-top: 10px; padding: 8px 16px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .dclt-add-item:hover { background: #005a87; }
    .dclt-repeater-item-content { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .dclt-repeater-item-content .dclt-field { margin-bottom: 12px; }
    .dclt-repeater-item-content .dclt-field.full-width { grid-column: 1 / -1; }
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

        <!-- Background Media Repeater -->
        <div class="dclt-repeater">
            <div class="dclt-repeater-header">Background Media Items</div>
            <div class="dclt-repeater-items" id="dclt-hero-media-items">
                <?php if (empty($hero_media)): ?>
                    <!-- Add first item if none exist -->
                    <div class="dclt-repeater-item" data-index="0">
                        <div class="dclt-repeater-item-header">
                            <div class="dclt-repeater-item-title">Media Item #1</div>
                            <div class="dclt-repeater-controls">
                                <button type="button" class="dclt-repeater-btn remove" onclick="dcltRemoveMediaItem(this)">Remove</button>
                            </div>
                        </div>
                        <div class="dclt-repeater-item-content">
                            <div class="dclt-field">
                                <label>Media Type</label>
                                <select name="dclt_hero_media[0][type]" class="media-type-select" onchange="dcltToggleMediaFields(this)">
                                    <option value="image" selected>Image</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                            <div class="dclt-field image-field">
                                <label>Background Image</label>
                                <input type="hidden" name="dclt_hero_media[0][image_id]" class="image-id-input" value="">
                                <button type="button" class="dclt-media-button" onclick="dcltSelectImage(this)">Select Image</button>
                                <button type="button" class="dclt-remove-media" onclick="dcltRemoveImage(this)" style="display:none;">Remove Image</button>
                                <div class="dclt-image-preview" style="display:none;"></div>
                            </div>
                            <div class="dclt-field video-field" style="display:none;">
                                <label>Video URL</label>
                                <input type="url" name="dclt_hero_media[0][video_url]" placeholder="https://vimeo.com/123456789">
                                <div class="description">Enter Vimeo URL or local video file URL</div>
                            </div>
                            <div class="dclt-field">
                                <label>Photo Credit</label>
                                <input type="text" name="dclt_hero_media[0][credit]" placeholder="Photographer name">
                            </div>
                            <div class="dclt-field">
                                <label>Photographer Note</label>
                                <textarea name="dclt_hero_media[0][note]" rows="2" placeholder="Additional photo details"></textarea>
                            </div>
                            <div class="dclt-field">
                                <label>Photographer Website</label>
                                <input type="url" name="dclt_hero_media[0][website]" placeholder="https://photographer-website.com">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($hero_media as $index => $item):
                        $type = isset($item['type']) ? $item['type'] : 'image';
                        $image_id = isset($item['image_id']) ? $item['image_id'] : '';
                        $video_url = isset($item['video_url']) ? $item['video_url'] : '';
                        $credit = isset($item['credit']) ? $item['credit'] : '';
                        $note = isset($item['note']) ? $item['note'] : '';
                        $website = isset($item['website']) ? $item['website'] : '';

                        $image_url = '';
                        if ($image_id) {
                            $image_src = wp_get_attachment_image_src($image_id, 'medium');
                            if ($image_src) {
                                $image_url = $image_src[0];
                            }
                        }
                    ?>
                        <div class="dclt-repeater-item" data-index="<?php echo esc_attr($index); ?>">
                            <div class="dclt-repeater-item-header">
                                <div class="dclt-repeater-item-title">Media Item #<?php echo $index + 1; ?></div>
                                <div class="dclt-repeater-controls">
                                    <button type="button" class="dclt-repeater-btn remove" onclick="dcltRemoveMediaItem(this)">Remove</button>
                                </div>
                            </div>
                            <div class="dclt-repeater-item-content">
                                <div class="dclt-field">
                                    <label>Media Type</label>
                                    <select name="dclt_hero_media[<?php echo esc_attr($index); ?>][type]" class="media-type-select" onchange="dcltToggleMediaFields(this)">
                                        <option value="image" <?php selected($type, 'image'); ?>>Image</option>
                                        <option value="video" <?php selected($type, 'video'); ?>>Video</option>
                                    </select>
                                </div>
                                <div class="dclt-field image-field" <?php echo $type === 'video' ? 'style="display:none;"' : ''; ?>>
                                    <label>Background Image</label>
                                    <input type="hidden" name="dclt_hero_media[<?php echo esc_attr($index); ?>][image_id]" class="image-id-input" value="<?php echo esc_attr($image_id); ?>">
                                    <button type="button" class="dclt-media-button" onclick="dcltSelectImage(this)">Select Image</button>
                                    <button type="button" class="dclt-remove-media" onclick="dcltRemoveImage(this)" <?php echo !$image_url ? 'style="display:none;"' : ''; ?>>Remove Image</button>
                                    <div class="dclt-image-preview" <?php echo !$image_url ? 'style="display:none;"' : ''; ?>>
                                        <?php if ($image_url): ?>
                                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="dclt-field video-field" <?php echo $type === 'image' ? 'style="display:none;"' : ''; ?>>
                                    <label>Video URL</label>
                                    <input type="url" name="dclt_hero_media[<?php echo esc_attr($index); ?>][video_url]" value="<?php echo esc_attr($video_url); ?>" placeholder="https://vimeo.com/123456789">
                                    <div class="description">Enter Vimeo URL or local video file URL</div>
                                </div>
                                <div class="dclt-field">
                                    <label>Photo Credit</label>
                                    <input type="text" name="dclt_hero_media[<?php echo esc_attr($index); ?>][credit]" value="<?php echo esc_attr($credit); ?>" placeholder="Photographer name">
                                </div>
                                <div class="dclt-field">
                                    <label>Photographer Note</label>
                                    <textarea name="dclt_hero_media[<?php echo esc_attr($index); ?>][note]" rows="2" placeholder="Additional photo details"><?php echo esc_textarea($note); ?></textarea>
                                </div>
                                <div class="dclt-field">
                                    <label>Photographer Website</label>
                                    <input type="url" name="dclt_hero_media[<?php echo esc_attr($index); ?>][website]" value="<?php echo esc_attr($website); ?>" placeholder="https://photographer-website.com">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="dclt-add-item" onclick="dcltAddMediaItem()">Add Media Item</button>
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

    // Repeater functionality
    let mediaItemIndex = <?php echo count($hero_media); ?>;

    function dcltToggleMediaFields(selectElement) {
        const item = $(selectElement).closest('.dclt-repeater-item');
        const type = $(selectElement).val();

        if (type === 'image') {
            item.find('.image-field').show();
            item.find('.video-field').hide();
        } else {
            item.find('.image-field').hide();
            item.find('.video-field').show();
        }
    }

    function dcltAddMediaItem() {
        const template = `
            <div class="dclt-repeater-item" data-index="${mediaItemIndex}">
                <div class="dclt-repeater-item-header">
                    <div class="dclt-repeater-item-title">Media Item #${mediaItemIndex + 1}</div>
                    <div class="dclt-repeater-controls">
                        <button type="button" class="dclt-repeater-btn remove" onclick="dcltRemoveMediaItem(this)">Remove</button>
                    </div>
                </div>
                <div class="dclt-repeater-item-content">
                    <div class="dclt-field">
                        <label>Media Type</label>
                        <select name="dclt_hero_media[${mediaItemIndex}][type]" class="media-type-select" onchange="dcltToggleMediaFields(this)">
                            <option value="image" selected>Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="dclt-field image-field">
                        <label>Background Image</label>
                        <input type="hidden" name="dclt_hero_media[${mediaItemIndex}][image_id]" class="image-id-input" value="">
                        <button type="button" class="dclt-media-button" onclick="dcltSelectImage(this)">Select Image</button>
                        <button type="button" class="dclt-remove-media" onclick="dcltRemoveImage(this)" style="display:none;">Remove Image</button>
                        <div class="dclt-image-preview" style="display:none;"></div>
                    </div>
                    <div class="dclt-field video-field" style="display:none;">
                        <label>Video URL</label>
                        <input type="url" name="dclt_hero_media[${mediaItemIndex}][video_url]" placeholder="https://vimeo.com/123456789">
                        <div class="description">Enter Vimeo URL or local video file URL</div>
                    </div>
                    <div class="dclt-field">
                        <label>Photo Credit</label>
                        <input type="text" name="dclt_hero_media[${mediaItemIndex}][credit]" placeholder="Photographer name">
                    </div>
                    <div class="dclt-field">
                        <label>Photographer Note</label>
                        <textarea name="dclt_hero_media[${mediaItemIndex}][note]" rows="2" placeholder="Additional photo details"></textarea>
                    </div>
                    <div class="dclt-field">
                        <label>Photographer Website</label>
                        <input type="url" name="dclt_hero_media[${mediaItemIndex}][website]" placeholder="https://photographer-website.com">
                    </div>
                </div>
            </div>
        `;

        $('#dclt-hero-media-items').append(template);
        dcltUpdateItemTitles();
        mediaItemIndex++;
    }

    function dcltRemoveMediaItem(button) {
        $(button).closest('.dclt-repeater-item').remove();
        dcltUpdateItemTitles();
    }

    function dcltUpdateItemTitles() {
        $('#dclt-hero-media-items .dclt-repeater-item').each(function(index) {
            $(this).find('.dclt-repeater-item-title').text('Media Item #' + (index + 1));
        });
    }

    function dcltSelectImage(button) {
        const $button = $(button);
        const $item = $button.closest('.dclt-repeater-item');

        const media_uploader = wp.media({
            title: 'Choose Background Image',
            button: { text: 'Use This Image' },
            multiple: false,
            library: { type: 'image' }
        });

        media_uploader.on('select', function() {
            const attachment = media_uploader.state().get('selection').first().toJSON();
            $item.find('.image-id-input').val(attachment.id);
            $item.find('.dclt-image-preview').html('<img src="'+attachment.url+'" alt="">').show();
            $item.find('.dclt-remove-media').show();
        });

        media_uploader.open();
    }

    function dcltRemoveImage(button) {
        const $item = $(button).closest('.dclt-repeater-item');
        $item.find('.image-id-input').val('');
        $item.find('.dclt-image-preview').hide().empty();
        $(button).hide();
    }
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

    // Handle repeater field
    if (isset($_POST['dclt_hero_media']) && is_array($_POST['dclt_hero_media'])) {
        $hero_media = [];
        foreach ($_POST['dclt_hero_media'] as $index => $item) {
            if (!is_array($item)) continue;

            $clean_item = [];

            // Media type
            $clean_item['type'] = isset($item['type']) && in_array($item['type'], ['image', 'video'])
                ? sanitize_text_field($item['type'])
                : 'image';

            // Image ID
            $clean_item['image_id'] = isset($item['image_id'])
                ? (int) $item['image_id']
                : 0;

            // Video URL
            $clean_item['video_url'] = isset($item['video_url'])
                ? esc_url_raw(wp_unslash($item['video_url']))
                : '';

            // Photo credit
            $clean_item['credit'] = isset($item['credit'])
                ? sanitize_text_field(wp_unslash($item['credit']))
                : '';

            // Photo note
            $clean_item['note'] = isset($item['note'])
                ? sanitize_textarea_field(wp_unslash($item['note']))
                : '';

            // Website
            $clean_item['website'] = isset($item['website'])
                ? esc_url_raw(wp_unslash($item['website']))
                : '';

            // Only save if we have some content
            if ($clean_item['image_id'] > 0 || !empty($clean_item['video_url'])) {
                $hero_media[] = $clean_item;
            }
        }

        update_post_meta($post_id, 'dclt_hero_media', $hero_media);
    } else {
        // If no data submitted, save empty array
        update_post_meta($post_id, 'dclt_hero_media', []);
    }
}
