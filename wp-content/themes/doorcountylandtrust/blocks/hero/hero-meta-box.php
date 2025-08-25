<?php
/**
 * Hero Block Meta Box - Streamlined Version
 * File: blocks/hero/hero-meta-box.php
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Meta box callback - renders the streamlined fields interface
 */
function dclt_hero_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('dclt_hero_meta_box', 'dclt_hero_meta_box_nonce');
    
    // Get current values with better defaults
    $preset = dclt_get_field($post->ID, 'hero_preset', '', 'landowner');
    $headline = dclt_get_field($post->ID, 'hero_headline', '', 'Protect the Land You Love');
    $subheadline = dclt_get_field($post->ID, 'hero_subheadline', '', 'From majestic bluffs to pristine beaches, Door County\'s natural beauty is worth preserving for future generations.');
    $primary_cta_text = dclt_get_field($post->ID, 'hero_primary_cta_text', '', 'Protect Your Land');
    $primary_cta_url = dclt_get_field($post->ID, 'hero_primary_cta_url', '', '/protect-your-land');
    $primary_cta_style = dclt_get_field($post->ID, 'hero_primary_cta_style', '', 'primary');
    
    $background_type = dclt_get_field($post->ID, 'hero_background_type', '', 'image');
    $background_image = dclt_get_field($post->ID, 'hero_background_image', '');
    $background_video = dclt_get_field($post->ID, 'hero_background_video', '');
    $background_color = dclt_get_field($post->ID, 'hero_background_color', '', '#065f46');
    $overlay_style = dclt_get_field($post->ID, 'hero_overlay_style', '', 'medium');
    $image_focal_point = dclt_get_field($post->ID, 'hero_image_focal_point', '', 'center center');
    
    $text_alignment = dclt_get_field($post->ID, 'hero_text_alignment', '', 'left');
    $height = dclt_get_field($post->ID, 'hero_height', '', 'standard');
    $container_width = dclt_get_field($post->ID, 'hero_container_width', '', 'wide');
    $bottom_divider = dclt_get_field($post->ID, 'hero_bottom_divider', '', 'curve');
    
    $secondary_cta_enabled = dclt_get_field($post->ID, 'hero_secondary_cta_enabled', '', '0');
    $secondary_cta_text = dclt_get_field($post->ID, 'hero_secondary_cta_text', '', 'Learn More');
    $secondary_cta_url = dclt_get_field($post->ID, 'hero_secondary_cta_url', '');
    $secondary_cta_style = dclt_get_field($post->ID, 'hero_secondary_cta_style', '', 'outline');
    
    $animate_content = dclt_get_field($post->ID, 'hero_animate_content', '', '1');
    
    // Preset configurations
    $presets = array(
        'landowner' => array(
            'name' => 'Landowner Conversion',
            'headline' => 'Protect the Land You Love',
            'subheadline' => 'Partner with Door County Land Trust to preserve your family\'s legacy while maintaining ownership of your land.',
            'primary_cta_text' => 'Protect Your Land',
            'primary_cta_url' => '/protect-your-land',
            'background_type' => 'image',
            'overlay_style' => 'medium',
            'text_alignment' => 'left',
            'height' => 'standard'
        ),
        'campaign' => array(
            'name' => 'Campaign Push',
            'headline' => 'Help Protect Door County\'s Shorelines',
            'subheadline' => 'Join our campaign to preserve critical waterfront habitat for wildlife and future generations.',
            'primary_cta_text' => 'Support the Campaign',
            'primary_cta_url' => '/campaign',
            'background_type' => 'image',
            'overlay_style' => 'dark',
            'text_alignment' => 'center',
            'height' => 'tall',
            'secondary_cta_enabled' => '1',
            'secondary_cta_text' => 'Learn More',
            'secondary_cta_url' => '/about-campaign'
        ),
        'explore' => array(
            'name' => 'Explore / Community',
            'headline' => 'Explore Door County Preserves',
            'subheadline' => 'Discover miles of trails, pristine beaches, and protected natural areas open to the public.',
            'primary_cta_text' => 'Find Trails',
            'primary_cta_url' => '/preserves',
            'background_type' => 'image',
            'overlay_style' => 'light',
            'text_alignment' => 'center',
            'height' => 'short',
            'container_width' => 'wide'
        )
    );
    
    ?>
    <style>
    .dclt-hero-meta { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    
    /* Preset Selector */
    .dclt-preset-selector { 
        background: linear-gradient(135deg, #065f46 0%, #047857 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    .dclt-preset-selector h3 { 
        margin: 0 0 12px 0; 
        color: white;
        font-size: 16px;
    }
    .dclt-preset-selector select {
        background: white;
        color: #1f2937;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        min-width: 200px;
    }
    .dclt-preset-desc {
        margin-top: 8px;
        font-size: 13px;
        opacity: 0.9;
        line-height: 1.4;
    }
    
    /* Field Groups */
    .dclt-field-group { 
        margin-bottom: 24px; 
        border: 1px solid #ddd; 
        border-radius: 6px; 
        background: #fafafa;
    }
    .dclt-field-group h4 { 
        margin: 0; 
        padding: 12px 16px; 
        background: #f0f0f1; 
        border-bottom: 1px solid #ddd; 
        font-size: 14px; 
        font-weight: 600; 
        color: #1e1e1e;
        border-radius: 5px 5px 0 0;
    }
    .dclt-field-group-content { padding: 16px; }
    .dclt-field { margin-bottom: 16px; }
    .dclt-field:last-child { margin-bottom: 0; }
    .dclt-field label { 
        display: block; 
        font-weight: 600; 
        margin-bottom: 6px; 
        color: #1e1e1e;
        font-size: 13px;
    }
    .dclt-field input[type="text"], 
    .dclt-field input[type="url"], 
    .dclt-field textarea, 
    .dclt-field select { 
        width: 100%; 
        max-width: 500px; 
        padding: 8px 12px;
        border: 1px solid #8c8f94;
        border-radius: 4px;
        font-size: 13px;
    }
    .dclt-field input[type="color"] { 
        width: 60px; 
        height: 40px; 
        padding: 0; 
        border: 1px solid #8c8f94;
        border-radius: 4px;
    }
    .dclt-field .description { 
        font-style: normal; 
        color: #646970; 
        font-size: 12px; 
        margin-top: 4px;
        line-height: 1.4;
    }
    .dclt-field .description.warning { color: #d63638; }
    .dclt-field .description.success { color: #00a32a; }
    
    /* Character counting */
    .dclt-char-count { 
        float: right; 
        font-size: 11px; 
        color: #646970; 
        margin-top: 4px;
    }
    .dclt-char-count.warning { color: #d63638; }
    .dclt-char-count.error { color: #d63638; font-weight: 600; }
    
    /* Media preview */
    .dclt-image-preview { 
        max-width: 200px; 
        margin-top: 8px; 
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    .dclt-image-preview img { 
        max-width: 100%; 
        height: auto; 
        display: block;
    }
    .dclt-media-button, .dclt-remove-media { 
        margin-top: 6px; 
        margin-right: 8px;
    }
    
    /* Layout helpers */
    .dclt-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 782px) { .dclt-grid { grid-template-columns: 1fr; } }
    
    /* Collapsible sections */
    .dclt-collapsible { background: #f9f9f9; }
    .dclt-collapsible h4 { 
        background: #e8e8e8; 
        cursor: pointer; 
        user-select: none;
        position: relative;
        padding-left: 40px;
    }
    .dclt-collapsible h4:hover { background: #e0e0e0; }
    .dclt-toggle-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        transition: transform 0.2s ease;
    }
    .dclt-toggle-icon.open { transform: translateY(-50%) rotate(90deg); }
    
    /* Secondary CTA styling */
    .dclt-secondary-cta { background: #f0f6ff; }
    .dclt-secondary-cta h4 { background: #e1f5fe; }
    
    /* Advanced section styling */
    .dclt-advanced { background: #f5f5f5; }
    .dclt-advanced h4 { background: #e0e0e0; }
    </style>
    
    <div class="dclt-hero-meta">
        
        <!-- Preset Selector -->
        <div class="dclt-preset-selector">
            <h3>Quick Start Presets</h3>
            <select id="hero_preset" name="hero_preset" onchange="applyPreset(this.value)">
                <option value="custom" <?php selected($preset, 'custom'); ?>>Custom Configuration</option>
                <option value="landowner" <?php selected($preset, 'landowner'); ?>>Landowner Conversion</option>
                <option value="campaign" <?php selected($preset, 'campaign'); ?>>Campaign Push</option>
                <option value="explore" <?php selected($preset, 'explore'); ?>>Explore / Community</option>
            </select>
            <div class="dclt-preset-desc" id="preset-description">
                Choose a preset to quickly configure common hero layouts. You can customize any field after selecting.
            </div>
        </div>
        
        <!-- Essentials -->
        <div class="dclt-field-group">
            <h4>Content</h4>
            <div class="dclt-field-group-content">
                
                <div class="dclt-field">
                    <label for="hero_headline">Headline</label>
                    <input type="text" 
                           id="hero_headline" 
                           name="hero_headline" 
                           value="<?php echo esc_attr($headline); ?>" 
                           maxlength="90"
                           placeholder="Protect the Land You Love">
                    <div class="dclt-char-count" id="headline-count">0/90</div>
                    <div class="description">Keep to one line on desktop (60-90 characters). If it wraps, shorten it.</div>
                </div>
                
                <div class="dclt-field">
                    <label for="hero_subheadline">Subheadline</label>
                    <textarea id="hero_subheadline" 
                              name="hero_subheadline" 
                              rows="2" 
                              maxlength="220"
                              placeholder="One sentence beats two. Front-load the 'why'."><?php echo esc_textarea($subheadline); ?></textarea>
                    <div class="dclt-char-count" id="subheadline-count">0/220</div>
                    <div class="description">1-2 sentences. Soft limit: 160 characters. One sentence beats two.</div>
                </div>
                
                <div class="dclt-grid">
                    <div class="dclt-field">
                        <label for="hero_primary_cta_text">Primary CTA Text</label>
                        <input type="text" 
                               id="hero_primary_cta_text" 
                               name="hero_primary_cta_text" 
                               value="<?php echo esc_attr($primary_cta_text); ?>" 
                               maxlength="25"
                               placeholder="Protect Your Land">
                        <div class="description">Start with action verbs (Protect, Explore, Donate)</div>
                    </div>
                    
                    <div class="dclt-field">
                        <label for="hero_primary_cta_url">Primary CTA URL</label>
                        <input type="url" 
                               id="hero_primary_cta_url" 
                               name="hero_primary_cta_url" 
                               value="<?php echo esc_attr($primary_cta_url); ?>"
                               required>
                        <div class="description">Required for button to appear</div>
                    </div>
                </div>
                
                <div class="dclt-field">
                    <label for="hero_primary_cta_style">Primary CTA Style</label>
                    <select id="hero_primary_cta_style" name="hero_primary_cta_style">
                        <option value="primary" <?php selected($primary_cta_style, 'primary'); ?>>Primary (Filled)</option>
                        <option value="outline" <?php selected($primary_cta_style, 'outline'); ?>>Outline</option>
                    </select>
                </div>
                
            </div>
        </div>
        
        <!-- Background -->
        <div class="dclt-field-group">
            <h4>Background</h4>
            <div class="dclt-field-group-content">
                
                <div class="dclt-field">
                    <label for="hero_background_type">Background Type</label>
                    <select id="hero_background_type" name="hero_background_type" onchange="toggleBackgroundFields()">
                        <option value="image" <?php selected($background_type, 'image'); ?>>Image</option>
                        <option value="video" <?php selected($background_type, 'video'); ?>>Video</option>
                        <option value="color" <?php selected($background_type, 'color'); ?>>Solid Color</option>
                    </select>
                </div>
                
                <!-- Image Background -->
                <div class="dclt-field" id="background_image_field" style="<?php echo $background_type !== 'image' ? 'display:none;' : ''; ?>">
                    <label for="hero_background_image">Background Image</label>
                    <input type="hidden" id="hero_background_image" name="hero_background_image" value="<?php echo esc_attr($background_image); ?>">
                    <button type="button" class="dclt-media-button button">Choose Image</button>
                    <button type="button" class="dclt-remove-media button" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">Remove</button>
                    <div class="dclt-image-preview" id="image-preview" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">
                        <?php if ($background_image): ?>
                            <?php echo wp_get_attachment_image($background_image, 'medium'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="description" id="image-requirements">Minimum 1920×1080px recommended. Images below 1600px width may appear soft.</div>
                </div>
                
                <!-- Video Background -->
                <div class="dclt-field" id="background_video_field" style="<?php echo $background_type !== 'video' ? 'display:none;' : ''; ?>">
                    <label for="hero_background_video">Background Video</label>
                    <input type="hidden" id="hero_background_video" name="hero_background_video" value="<?php echo esc_attr($background_video); ?>">
                    <button type="button" class="dclt-media-button-video button">Choose Video</button>
                    <button type="button" class="dclt-remove-media-video button" style="<?php echo !$background_video ? 'display:none;' : ''; ?>">Remove</button>
                    <div class="description">MP4 format, under 10MB. Always provide a poster image for accessibility.</div>
                </div>
                
                <!-- Color Background -->
                <div class="dclt-field" id="background_color_field" style="<?php echo $background_type !== 'color' ? 'display:none;' : ''; ?>">
                    <label for="hero_background_color">Background Color</label>
                    <input type="color" id="hero_background_color" name="hero_background_color" value="<?php echo esc_attr($background_color); ?>">
                    <div class="description">Brand green default recommended</div>
                </div>
                
                <!-- Overlay (for image/video only) -->
                <div class="dclt-field" id="overlay_field" style="<?php echo ($background_type === 'color') ? 'display:none;' : ''; ?>">
                    <label for="hero_overlay_style">Overlay</label>
                    <select id="hero_overlay_style" name="hero_overlay_style">
                        <option value="none" <?php selected($overlay_style, 'none'); ?>>None</option>
                        <option value="light" <?php selected($overlay_style, 'light'); ?>>Light (20%)</option>
                        <option value="medium" <?php selected($overlay_style, 'medium'); ?>>Medium (40%)</option>
                        <option value="dark" <?php selected($overlay_style, 'dark'); ?>>Dark (60%)</option>
                    </select>
                    <div class="description">Improves text readability. If text fails contrast, nudge one step darker.</div>
                </div>
                
                <!-- Image Focal Point (Advanced, only show when image is selected) -->
                <div class="dclt-field dclt-collapsible" id="image_focal_point_field" style="<?php echo ($background_type !== 'image' || !$background_image) ? 'display:none;' : ''; ?>">
                    <h4 onclick="toggleCollapsible('focal-point')">
                        <span class="dclt-toggle-icon" id="focal-point-toggle">▶</span> Image Focal Point (Advanced)
                    </h4>
                    <div id="focal-point-content" style="display:none;">
                        <div class="dclt-field-group-content">
                            <div class="dclt-field">
                                <label for="hero_image_focal_point">Focal Point</label>
                                <select id="hero_image_focal_point" name="hero_image_focal_point">
                                    <option value="center center" <?php selected($image_focal_point, 'center center'); ?>>Center</option>
                                    <option value="top center" <?php selected($image_focal_point, 'top center'); ?>>Top Center</option>
                                    <option value="bottom center" <?php selected($image_focal_point, 'bottom center'); ?>>Bottom Center</option>
                                    <option value="center left" <?php selected($image_focal_point, 'center left'); ?>>Center Left</option>
                                    <option value="center right" <?php selected($image_focal_point, 'center right'); ?>>Center Right</option>
                                    <option value="top left" <?php selected($image_focal_point, 'top left'); ?>>Top Left</option>
                                    <option value="top right" <?php selected($image_focal_point, 'top right'); ?>>Top Right</option>
                                    <option value="bottom left" <?php selected($image_focal_point, 'bottom left'); ?>>Bottom Left</option>
                                    <option value="bottom right" <?php selected($image_focal_point, 'bottom right'); ?>>Bottom Right</option>
                                </select>
                                <div class="description">Controls which part of the image stays visible when cropped on different screen sizes</div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Layout -->
        <div class="dclt-field-group">
            <h4>Layout</h4>
            <div class="dclt-field-group-content">
                
                <div class="dclt-grid">
                    <div class="dclt-field">
                        <label for="hero_text_alignment">Text Alignment</label>
                        <select id="hero_text_alignment" name="hero_text_alignment">
                            <option value="left" <?php selected($text_alignment, 'left'); ?>>Left</option>
                            <option value="center" <?php selected($text_alignment, 'center'); ?>>Center</option>
                            <option value="right" <?php selected($text_alignment, 'right'); ?>>Right</option>
                        </select>
                    </div>
                    
                    <div class="dclt-field">
                        <label for="hero_height">Height</label>
                        <select id="hero_height" name="hero_height">
                            <option value="short" <?php selected($height, 'short'); ?>>Short (45vh)</option>
                            <option value="standard" <?php selected($height, 'standard'); ?>>Standard (65vh)</option>
                            <option value="tall" <?php selected($height, 'tall'); ?>>Tall (85vh)</option>
                        </select>
                    </div>
                </div>
                
                <div class="dclt-grid">
                    <div class="dclt-field">
                        <label for="hero_container_width">Container Width</label>
                        <select id="hero_container_width" name="hero_container_width">
                            <option value="content" <?php selected($container_width, 'content'); ?>>Content (1200px)</option>
                            <option value="wide" <?php selected($container_width, 'wide'); ?>>Wide (1400px)</option>
                            <option value="full" <?php selected($container_width, 'full'); ?>>Full Width</option>
                        </select>
                    </div>
                    
                    <div class="dclt-field">
                        <label for="hero_bottom_divider">Bottom Divider</label>
                        <select id="hero_bottom_divider" name="hero_bottom_divider">
                            <option value="none" <?php selected($bottom_divider, 'none'); ?>>None</option>
                            <option value="curve" <?php selected($bottom_divider, 'curve'); ?>>Curve</option>
                            <option value="angle" <?php selected($bottom_divider, 'angle'); ?>>Angle</option>
                        </select>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Secondary CTA (Collapsible) -->
        <div class="dclt-field-group dclt-secondary-cta">
            <h4 onclick="toggleCollapsible('secondary-cta')" style="cursor: pointer; padding-left: 40px; position: relative;">
                <span class="dclt-toggle-icon <?php echo $secondary_cta_enabled === '1' ? 'open' : ''; ?>" id="secondary-cta-toggle">▶</span>
                Secondary CTA (Optional)
            </h4>
            <div class="dclt-field-group-content" id="secondary-cta-content" style="<?php echo $secondary_cta_enabled !== '1' ? 'display:none;' : ''; ?>">
                
                <div class="dclt-field">
                    <label for="hero_secondary_cta_enabled">
                        <input type="checkbox" 
                               id="hero_secondary_cta_enabled" 
                               name="hero_secondary_cta_enabled" 
                               value="1" 
                               <?php checked($secondary_cta_enabled, '1'); ?>
                               onchange="toggleSecondaryFields(this.checked)">
                        Enable Secondary CTA
                    </label>
                </div>
                
                <div id="secondary-cta-fields" style="<?php echo $secondary_cta_enabled !== '1' ? 'display:none;' : ''; ?>">
                    <div class="dclt-grid">
                        <div class="dclt-field">
                            <label for="hero_secondary_cta_text">Secondary CTA Text</label>
                            <input type="text" 
                                   id="hero_secondary_cta_text" 
                                   name="hero_secondary_cta_text" 
                                   value="<?php echo esc_attr($secondary_cta_text); ?>" 
                                   maxlength="25"
                                   placeholder="Learn More">
                        </div>
                        
                        <div class="dclt-field">
                            <label for="hero_secondary_cta_url">Secondary CTA URL</label>
                            <input type="url" 
                                   id="hero_secondary_cta_url" 
                                   name="hero_secondary_cta_url" 
                                   value="<?php echo esc_attr($secondary_cta_url); ?>">
                        </div>
                    </div>
                    
                    <div class="dclt-field">
                        <label for="hero_secondary_cta_style">Secondary CTA Style</label>
                        <select id="hero_secondary_cta_style" name="hero_secondary_cta_style">
                            <option value="outline" <?php selected($secondary_cta_style, 'outline'); ?>>Outline</option>
                            <option value="primary" <?php selected($secondary_cta_style, 'primary'); ?>>Primary (Filled)</option>
                        </select>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Advanced (Collapsed) -->
        <div class="dclt-field-group dclt-advanced">
            <h4 onclick="toggleCollapsible('advanced')" style="cursor: pointer; padding-left: 40px; position: relative;">
                <span class="dclt-toggle-icon" id="advanced-toggle">▶</span> Advanced Options
            </h4>
            <div class="dclt-field-group-content" id="advanced-content" style="display:none;">
                
                <div class="dclt-field">
                    <label for="hero_animate_content">
                        <input type="checkbox" 
                               id="hero_animate_content" 
                               name="hero_animate_content" 
                               value="1" 
                               <?php checked($animate_content, '1'); ?>>
                        Animate content on page load
                    </label>
                    <div class="description">Automatically honors user's "reduce motion" preference</div>
                </div>
                
            </div>
        </div>
        
    </div>
    
    <script>
    // Preset configurations
    const presets = <?php echo json_encode($presets); ?>;
    
    jQuery(document).ready(function($) {
        // Initialize character counting
        updateCharacterCounts();
        
        // Character counting functions
        function updateCharCount(field, counter, softLimit, hardLimit) {
            const count = field.val().length;
            const display = `${count}/${hardLimit}`;
            
            counter.text(display);
            counter.removeClass('warning error');
            
            if (count > hardLimit) {
                counter.addClass('error');
            } else if (count > softLimit) {
                counter.addClass('warning');
            }
        }
        
        function updateCharacterCounts() {
            $('#hero_headline').on('input', function() {
                updateCharCount($(this), $('#headline-count'), 60, 90);
            }).trigger('input');
            
            $('#hero_subheadline').on('input', function() {
                updateCharCount($(this), $('#subheadline-count'), 160, 220);
            }).trigger('input');
        }
        
        // Media library for images
        $('.dclt-media-button').click(function(e) {
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
                
                // Show preview
                $('#image-preview').html(`<img src="${attachment.url}" alt="">`).show();
                $('.dclt-remove-media').show();
                
                // Check image dimensions and show feedback
                if (attachment.width < 1600) {
                    $('#image-requirements').html('⚠️ Image width is below 1600px - may appear soft on large screens').addClass('warning');
                } else {
                    $('#image-requirements').html('✓ Image meets minimum requirements').addClass('success');
                }
                
                // Show focal point controls if image is selected
                $('#image_focal_point_field').show();
            });
            
            media_uploader.open();
        });
        
        // Remove image
        $('.dclt-remove-media').click(function(e) {
            e.preventDefault();
            $('#hero_background_image').val('');
            $('#image-preview').hide();
            $(this).hide();
            $('#image-requirements').html('Minimum 1920×1080px recommended. Images below 1600px width may appear soft.').removeClass('warning success');
            $('#image_focal_point_field').hide();
        });
        
        // Media library for videos
        $('.dclt-media-button-video').click(function(e) {
            e.preventDefault();
            const media_uploader = wp.media({
                title: 'Choose Hero Background Video',
                button: { text: 'Use This Video' },
                multiple: false,
                library: { type: 'video' }
            });
            
            media_uploader.on('select', function() {
                const attachment = media_uploader.state().get('selection').first().toJSON();
                $('#hero_background_video').val(attachment.id);
                $('.dclt-remove-media-video').show();
                
                // Check video size
                if (attachment.filesizeInBytes > 10485760) { // 10MB
                    alert('Video file is larger than 10MB. Please use a smaller file for better performance.');
                }
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
    
    // Apply preset configuration
    function applyPreset(presetKey) {
        if (presetKey === 'custom') {
            document.getElementById('preset-description').textContent = 'Choose a preset to quickly configure common hero layouts. You can customize any field after selecting.';
            return;
        }
        
        const preset = presets[presetKey];
        if (!preset) return;
        
        // Update description
        document.getElementById('preset-description').textContent = preset.name + ': ' + preset.subheadline;
        
        // Apply preset values
        document.getElementById('hero_headline').value = preset.headline || '';
        document.getElementById('hero_subheadline').value = preset.subheadline || '';
        document.getElementById('hero_primary_cta_text').value = preset.primary_cta_text || '';
        document.getElementById('hero_primary_cta_url').value = preset.primary_cta_url || '';
        document.getElementById('hero_background_type').value = preset.background_type || 'image';
        document.getElementById('hero_overlay_style').value = preset.overlay_style || 'medium';
        document.getElementById('hero_text_alignment').value = preset.text_alignment || 'left';
        document.getElementById('hero_height').value = preset.height || 'standard';
        
        if (preset.container_width) {
            document.getElementById('hero_container_width').value = preset.container_width;
        }
        
        // Handle secondary CTA
        if (preset.secondary_cta_enabled) {
            document.getElementById('hero_secondary_cta_enabled').checked = true;
            document.getElementById('hero_secondary_cta_text').value = preset.secondary_cta_text || '';
            document.getElementById('hero_secondary_cta_url').value = preset.secondary_cta_url || '';
            toggleSecondaryFields(true);
        }
        
        // Update background fields visibility
        toggleBackgroundFields();
        
        // Update character counts
        jQuery('#hero_headline').trigger('input');
        jQuery('#hero_subheadline').trigger('input');
    }
    
    // Toggle background type fields
    function toggleBackgroundFields() {
        const type = document.getElementById('hero_background_type').value;
        
        // Hide all background fields
        document.getElementById('background_image_field').style.display = 'none';
        document.getElementById('background_video_field').style.display = 'none';
        document.getElementById('background_color_field').style.display = 'none';
        document.getElementById('image_focal_point_field').style.display = 'none';
        
        // Show relevant field
        document.getElementById(`background_${type}_field`).style.display = 'block';
        
        // Show/hide overlay field
        if (type === 'color') {
            document.getElementById('overlay_field').style.display = 'none';
        } else {
            document.getElementById('overlay_field').style.display = 'block';
        }
        
        // Show focal point if image is already selected
        if (type === 'image' && document.getElementById('hero_background_image').value) {
            document.getElementById('image_focal_point_field').style.display = 'block';
        }
    }
    
    // Toggle collapsible sections
    function toggleCollapsible(sectionId) {
        const content = document.getElementById(sectionId + '-content');
        const toggle = document.getElementById(sectionId + '-toggle');
        
        if (content.style.display === 'none' || !content.style.display) {
            content.style.display = 'block';
            toggle.classList.add('open');
        } else {
            content.style.display = 'none';
            toggle.classList.remove('open');
        }
    }
    
    // Toggle secondary CTA fields
    function toggleSecondaryFields(enabled) {
        const fields = document.getElementById('secondary-cta-fields');
        const content = document.getElementById('secondary-cta-content');
        const toggle = document.getElementById('secondary-cta-toggle');
        
        if (enabled) {
            fields.style.display = 'block';
            content.style.display = 'block';
            toggle.classList.add('open');
        } else {
            fields.style.display = 'none';
        }
    }
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
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Skip autosaves
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Define all hero fields
    $hero_fields = array(
        'hero_preset',
        'hero_headline',
        'hero_subheadline',
        'hero_primary_cta_text',
        'hero_primary_cta_url',
        'hero_primary_cta_style',
        'hero_background_type',
        'hero_background_image',
        'hero_background_video',
        'hero_background_color',
        'hero_overlay_style',
        'hero_image_focal_point',
        'hero_text_alignment',
        'hero_height',
        'hero_container_width',
        'hero_bottom_divider',
        'hero_secondary_cta_enabled',
        'hero_secondary_cta_text',
        'hero_secondary_cta_url',
        'hero_secondary_cta_style',
        'hero_animate_content'
    );
    
    // Save each field
    foreach ($hero_fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
        } else {
            // Handle checkboxes that aren't checked
            if (in_array($field, ['hero_secondary_cta_enabled', 'hero_animate_content'])) {
                dclt_update_field($post_id, $field, '0');
            }
        }
    }
}
add_action('save_post', 'dclt_save_hero_meta_box');