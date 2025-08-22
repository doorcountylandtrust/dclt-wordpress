<?php
/**
 * Hero Block Meta Box
 * File: blocks/hero/hero-meta-box.php
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Meta box callback - renders the fields interface
 */
function dclt_hero_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('dclt_hero_meta_box', 'dclt_hero_meta_box_nonce');
    
    // Get current values
    $background_type = dclt_get_field($post->ID, 'hero_background_type', '', 'image');
    $background_image = dclt_get_field($post->ID, 'hero_background_image', '');
    $background_video = dclt_get_field($post->ID, 'hero_background_video', '');
    $background_color = dclt_get_field($post->ID, 'hero_background_color', '', '#006847');
    $overlay_opacity = dclt_get_field($post->ID, 'hero_overlay_opacity', '', '40');
    $content_alignment = dclt_get_field($post->ID, 'hero_content_alignment', '', 'left');
    $headline = dclt_get_field($post->ID, 'hero_headline', '');
    $subheadline = dclt_get_field($post->ID, 'hero_subheadline', '');
    $primary_cta_text = dclt_get_field($post->ID, 'hero_primary_cta_text', '');
    $primary_cta_url = dclt_get_field($post->ID, 'hero_primary_cta_url', '');
    $primary_cta_style = dclt_get_field($post->ID, 'hero_primary_cta_style', '', 'primary');
    $secondary_cta_text = dclt_get_field($post->ID, 'hero_secondary_cta_text', '');
    $secondary_cta_url = dclt_get_field($post->ID, 'hero_secondary_cta_url', '');
    $secondary_cta_style = dclt_get_field($post->ID, 'hero_secondary_cta_style', '', 'secondary');
    $container_width = dclt_get_field($post->ID, 'hero_container_width', '', 'wide');
    $curved_bottom = dclt_get_field($post->ID, 'hero_curved_bottom', '', '1');
    
    ?>
    <style>
    .dclt-field-group { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
    .dclt-field-group h4 { margin-top: 0; color: #23282d; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .dclt-field { margin-bottom: 15px; }
    .dclt-field label { display: block; font-weight: 600; margin-bottom: 5px; }
    .dclt-field input, .dclt-field select, .dclt-field textarea { width: 100%; max-width: 500px; }
    .dclt-field input[type="range"] { width: 200px; }
    .dclt-field .description { font-style: italic; color: #666; font-size: 12px; }
    .dclt-image-preview { max-width: 200px; margin-top: 10px; }
    .dclt-image-preview img { max-width: 100%; height: auto; border: 1px solid #ddd; }
    .dclt-media-button { margin-top: 5px; }
    </style>
    
    <div class="dclt-hero-fields">
        
        <!-- Background Settings -->
        <div class="dclt-field-group">
            <h4>Background Settings</h4>
            
            <div class="dclt-field">
                <label for="hero_background_type">Background Type</label>
                <select id="hero_background_type" name="hero_background_type">
                    <option value="image" <?php selected($background_type, 'image'); ?>>Image</option>
                    <option value="video" <?php selected($background_type, 'video'); ?>>Video</option>
                    <option value="color" <?php selected($background_type, 'color'); ?>>Solid Color</option>
                </select>
                <div class="description">Choose the type of background for your hero section.</div>
            </div>
            
            <div class="dclt-field" id="background_image_field" style="<?php echo $background_type !== 'image' ? 'display:none;' : ''; ?>">
                <label for="hero_background_image">Background Image</label>
                <input type="hidden" id="hero_background_image" name="hero_background_image" value="<?php echo esc_attr($background_image); ?>">
                <button type="button" class="dclt-media-button button">Choose Image</button>
                <button type="button" class="dclt-remove-media button" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">Remove</button>
                <div class="dclt-image-preview" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">
                    <?php if ($background_image): ?>
                        <?php echo wp_get_attachment_image($background_image, 'medium'); ?>
                    <?php endif; ?>
                </div>
                <div class="description">Recommended size: 1920x1080px or larger. Use high-quality nature images.</div>
            </div>
            
            <div class="dclt-field" id="background_video_field" style="<?php echo $background_type !== 'video' ? 'display:none;' : ''; ?>">
                <label for="hero_background_video">Background Video</label>
                <input type="hidden" id="hero_background_video" name="hero_background_video" value="<?php echo esc_attr($background_video); ?>">
                <button type="button" class="dclt-media-button-video button">Choose Video</button>
                <button type="button" class="dclt-remove-media-video button" style="<?php echo !$background_video ? 'display:none;' : ''; ?>">Remove</button>
                <div class="description">Upload MP4 format. Keep file size under 10MB for performance.</div>
            </div>
            
            <div class="dclt-field" id="background_color_field" style="<?php echo $background_type !== 'color' ? 'display:none;' : ''; ?>">
                <label for="hero_background_color">Background Color</label>
                <input type="color" id="hero_background_color" name="hero_background_color" value="<?php echo esc_attr($background_color); ?>">
                <div class="description">Choose a brand color for solid background.</div>
            </div>
            
            <div class="dclt-field" id="overlay_opacity_field" style="<?php echo $background_type === 'color' ? 'display:none;' : ''; ?>">
                <label for="hero_overlay_opacity">Overlay Opacity: <span id="overlay_value"><?php echo esc_html($overlay_opacity); ?>%</span></label>
                <input type="range" id="hero_overlay_opacity" name="hero_overlay_opacity" min="0" max="80" step="10" value="<?php echo esc_attr($overlay_opacity); ?>">
                <div class="description">Dark overlay to improve text readability.</div>
            </div>
        </div>
        
        <!-- Content Settings -->
        <div class="dclt-field-group">
            <h4>Content Settings</h4>
            
            <div class="dclt-field">
                <label for="hero_content_alignment">Content Alignment</label>
                <select id="hero_content_alignment" name="hero_content_alignment">
                    <option value="left" <?php selected($content_alignment, 'left'); ?>>Left Aligned</option>
                    <option value="center" <?php selected($content_alignment, 'center'); ?>>Center Aligned</option>
                    <option value="right" <?php selected($content_alignment, 'right'); ?>>Right Aligned</option>
                </select>
            </div>  
            
            <div class="dclt-field">
                <label for="hero_headline">Headline</label>
                <input type="text" id="hero_headline" name="hero_headline" value="<?php echo esc_attr($headline); ?>" maxlength="100">
                <div class="description">Main hero headline. Keep it concise and impactful (recommended: 5-8 words).</div>
            </div>
            
            <div class="dclt-field">
                <label for="hero_subheadline">Subheadline</label>
                <textarea id="hero_subheadline" name="hero_subheadline" rows="3" maxlength="200"><?php echo esc_textarea($subheadline); ?></textarea>
                <div class="description">Supporting text that expands on the headline (recommended: 1-2 sentences).</div>
            </div>
        </div>
        
        <!-- CTA Settings -->
        <div class="dclt-field-group">
            <h4>Call-to-Action Buttons</h4>
            
            <div class="dclt-field">
                <label for="hero_primary_cta_text">Primary CTA Text</label>
                <input type="text" id="hero_primary_cta_text" name="hero_primary_cta_text" value="<?php echo esc_attr($primary_cta_text); ?>" maxlength="30" placeholder="Protect Your Land">
            </div>
            
            <div class="dclt-field">
                <label for="hero_primary_cta_url">Primary CTA URL</label>
                <input type="url" id="hero_primary_cta_url" name="hero_primary_cta_url" value="<?php echo esc_attr($primary_cta_url); ?>">
            </div>
            
            <div class="dclt-field">
                <label for="hero_primary_cta_style">Primary CTA Style</label>
                <select id="hero_primary_cta_style" name="hero_primary_cta_style">
                    <option value="primary" <?php selected($primary_cta_style, 'primary'); ?>>Primary (Brand Green)</option>
                    <option value="landowner" <?php selected($primary_cta_style, 'landowner'); ?>>Landowner (Dark Green)</option>
                    <option value="explore" <?php selected($primary_cta_style, 'explore'); ?>>Explore (Forest Green)</option>
                </select>
            </div>
            
            <div class="dclt-field">
                <label for="hero_secondary_cta_text">Secondary CTA Text (Optional)</label>
                <input type="text" id="hero_secondary_cta_text" name="hero_secondary_cta_text" value="<?php echo esc_attr($secondary_cta_text); ?>" maxlength="30" placeholder="Learn More">
            </div>
            
            <div class="dclt-field">
                <label for="hero_secondary_cta_url">Secondary CTA URL</label>
                <input type="url" id="hero_secondary_cta_url" name="hero_secondary_cta_url" value="<?php echo esc_attr($secondary_cta_url); ?>">
            </div>
            
            <div class="dclt-field">
                <label for="hero_secondary_cta_style">Secondary CTA Style</label>
                <select id="hero_secondary_cta_style" name="hero_secondary_cta_style">
                    <option value="secondary" <?php selected($secondary_cta_style, 'secondary'); ?>>Secondary (White with Green Border)</option>
                    <option value="primary" <?php selected($secondary_cta_style, 'primary'); ?>>Primary (Brand Green)</option>
                    <option value="explore" <?php selected($secondary_cta_style, 'explore'); ?>>Explore (Forest Green)</option>
                </select>
            </div>
        </div>
        
        <!-- Layout Settings -->
        <div class="dclt-field-group">
            <h4>Layout Settings</h4>
            
            <div class="dclt-field">
                <label for="hero_container_width">Container Width</label>
                <select id="hero_container_width" name="hero_container_width">
                    <option value="narrow" <?php selected($container_width, 'narrow'); ?>>Narrow (768px) - Good for focused messaging</option>
                    <option value="content" <?php selected($container_width, 'content'); ?>>Content (1200px) - Standard width</option>
                    <option value="wide" <?php selected($container_width, 'wide'); ?>>Wide (1400px) - For more visual impact</option>
                    <option value="full" <?php selected($container_width, 'full'); ?>>Full Width - Edge to edge content</option>
                </select>
            </div>
            
            <div class="dclt-field">
                <label for="hero_curved_bottom">
                    <input type="checkbox" id="hero_curved_bottom" name="hero_curved_bottom" value="1" <?php checked($curved_bottom, '1'); ?>>
                    Add Curved Bottom Design
                </label>
                <div class="description">Add the signature curved bottom design to this hero section.</div>
            </div>
        </div>
        
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Handle background type changes
        $('#hero_background_type').change(function() {
            var type = $(this).val();
            $('.dclt-field[id$="_field"]').hide();
            $('#background_' + type + '_field').show();
            if (type !== 'color') {
                $('#overlay_opacity_field').show();
            }
        });
        
        // Handle overlay opacity slider
        $('#hero_overlay_opacity').on('input', function() {
            $('#overlay_value').text($(this).val() + '%');
        });
        
        // Media library for images
        $('.dclt-media-button').click(function(e) {
            e.preventDefault();
            var media_uploader = wp.media({
                title: 'Choose Hero Background Image',
                button: { text: 'Use This Image' },
                multiple: false,
                library: { type: 'image' }
            });
            
            media_uploader.on('select', function() {
                var attachment = media_uploader.state().get('selection').first().toJSON();
                $('#hero_background_image').val(attachment.id);
                $('.dclt-image-preview').html('<img src="' + attachment.url + '" alt="">').show();
                $('.dclt-remove-media').show();
            });
            
            media_uploader.open();
        });
        
        // Remove image
        $('.dclt-remove-media').click(function(e) {
            e.preventDefault();
            $('#hero_background_image').val('');
            $('.dclt-image-preview').hide();
            $(this).hide();
        });
        
        // Media library for videos
        $('.dclt-media-button-video').click(function(e) {
            e.preventDefault();
            var media_uploader = wp.media({
                title: 'Choose Hero Background Video',
                button: { text: 'Use This Video' },
                multiple: false,
                library: { type: 'video' }
            });
            
            media_uploader.on('select', function() {
                var attachment = media_uploader.state().get('selection').first().toJSON();
                $('#hero_background_video').val(attachment.id);
                $('.dclt-remove-media-video').show();
            });
            
            media_uploader.open();
        });
        
        // Remove video
        $('.dclt-remove-media-video').click(function(e) {
            e.preventDefault();
            $('#hero_background_video').val('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Save meta box data
 */
function dclt_save_hero_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['dclt_hero_meta_box_nonce']) || !wp_verify_nonce($_POST['dclt_hero_meta_box_nonce'], 'dclt_hero_meta_box')) {
        return;
    }
    
    // Check if user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Don't save on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // List of all hero fields
    $hero_fields = [
        'hero_background_type',
        'hero_background_image', 
        'hero_background_video',
        'hero_background_color',
        'hero_overlay_opacity',
        'hero_content_alignment',
        'hero_headline',
        'hero_subheadline',
        'hero_primary_cta_text',
        'hero_primary_cta_url', 
        'hero_primary_cta_style',
        'hero_secondary_cta_text',
        'hero_secondary_cta_url',
        'hero_secondary_cta_style',
        'hero_container_width',
        'hero_curved_bottom'
    ];
    
    // Save each field
    foreach ($hero_fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
        } else {
            // Handle checkboxes (curved_bottom)
            if ($field === 'hero_curved_bottom') {
                dclt_update_field($post_id, $field, '0');
            }
        }
    }
}
add_action('save_post', 'dclt_save_hero_meta_box');