<?php 
/**
 * Door County Land Trust - Custom Block Registration System (No ACF)
 */

error_log('DCLT blocks-init.php file is loading!');


// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}



// =============================================================================
// BLOCK REGISTRATION (Custom Gutenberg Blocks)
// =============================================================================

/**
 * Register Hero Block with custom render callback
 */
function dclt_register_blocks() {
    // Register Hero Block JavaScript for editor
    wp_register_script(
        'dclt-hero-block-editor',
        get_template_directory_uri() . '/blocks/hero/hero-editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        '1.0.0'
    );

    // Register Hero Block
    register_block_type('dclt/hero', array(
        'editor_script' => 'dclt-hero-block-editor',
        'render_callback' => 'dclt_render_hero_block',
        'attributes' => array(
            'blockId' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
}
add_action('init', 'dclt_register_blocks');

/**
 * Render callback for Hero block
 */
function dclt_render_hero_block($attributes, $content) {
    error_log('Hero block render called! Post ID: ' . (isset($GLOBALS['post']) ? $GLOBALS['post']->ID : 'no post'));

    // Get the current post ID
    global $post;
    if (!$post) return '';
    
    // Get the block's unique ID (we'll store fields with this prefix)
    $block_id = isset($attributes['blockId']) ? $attributes['blockId'] : '';
    if (!$block_id) {
        $block_id = 'hero_' . uniqid();
    }
    
    // Get field values using our custom function
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
    
    // Build the hero section HTML
    ob_start();
    ?>
    <section class="dclt-hero-block relative overflow-hidden <?php echo $background_type === 'color' ? 'text-white' : ''; ?>" 
             data-background-type="<?php echo esc_attr($background_type); ?>">
        
        <!-- Background Layer -->
        <div class="absolute inset-0">
            <?php if ($background_type === 'image' && $background_image): ?>
                <?php $image_data = wp_get_attachment_image_src($background_image, 'large'); ?>
                <?php if ($image_data): ?>
                    <img src="<?php echo esc_url($image_data[0]); ?>" 
                         alt=""
                         class="w-full h-full object-cover">
                <?php endif; ?>
            <?php elseif ($background_type === 'video' && $background_video): ?>
                <?php $video_url = wp_get_attachment_url($background_video); ?>
                <?php if ($video_url): ?>
                    <video autoplay muted loop playsinline class="w-full h-full object-cover">
                        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
            <?php elseif ($background_type === 'color'): ?>
                <div class="w-full h-full" style="background-color: <?php echo esc_attr($background_color); ?>"></div>
            <?php endif; ?>
            
            <!-- Overlay -->
            <?php if ($background_type !== 'color' && $overlay_opacity > 0): ?>
                <div class="absolute inset-0 bg-black" 
                     style="opacity: <?php echo ($overlay_opacity / 100); ?>"></div>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="<?php echo dclt_get_container_class($container_width); ?> relative z-10 py-20 md:py-32">
            <div class="hero-content <?php echo $content_alignment === 'center' ? 'text-center max-w-3xl mx-auto' : ($content_alignment === 'right' ? 'text-right ml-auto max-w-2xl' : 'max-w-2xl'); ?>">
                
                <?php if ($headline): ?>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight text-white">
                        <?php echo wp_kses_post($headline); ?>
                    </h1>
                <?php endif; ?>

                <?php if ($subheadline): ?>
                    <p class="text-xl md:text-2xl mb-8 text-white/90 leading-relaxed">
                        <?php echo wp_kses_post($subheadline); ?>
                    </p>
                <?php endif; ?>

                <!-- CTA Buttons -->
                <?php if ($primary_cta_text || $secondary_cta_text): ?>
                    <div class="flex flex-col sm:flex-row gap-4 <?php echo $content_alignment === 'center' ? 'justify-center' : ($content_alignment === 'right' ? 'justify-end' : 'justify-start'); ?>">
                        
                        <?php if ($primary_cta_text && $primary_cta_url): ?>
                            <a href="<?php echo esc_url($primary_cta_url); ?>" 
                               class="<?php echo dclt_get_button_class($primary_cta_style); ?> inline-block text-center">
                                <?php echo esc_html($primary_cta_text); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($secondary_cta_text && $secondary_cta_url): ?>
                            <a href="<?php echo esc_url($secondary_cta_url); ?>" 
                               class="<?php echo dclt_get_button_class($secondary_cta_style); ?> inline-block text-center">
                                <?php echo esc_html($secondary_cta_text); ?>
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Curved Bottom SVG -->
        <?php if ($curved_bottom === '1'): ?>
            <div class="absolute bottom-0 left-0 w-full">
                <svg class="w-full h-8 md:h-16" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M0,0 C300,120 900,120 1200,0 L1200,120 L0,120 Z" 
                          fill="white" class="curve-fill"/>
                </svg>
            </div>
        <?php endif; ?>

    </section>
    <?php
    return ob_get_clean();
}

// =============================================================================
// ADMIN META BOX FOR HERO BLOCK FIELDS
// =============================================================================

/**
 * Add meta box for Hero block settings
 */
function dclt_add_hero_meta_box() {
    $post_types = ['page', 'post']; // Add any post types where you want hero blocks
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'dclt-hero-fields',
            'Hero Block Settings',
            'dclt_hero_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'dclt_add_hero_meta_box');

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

// =============================================================================
// PERFORMANCE OPTIMIZED ASSET LOADING 
// =============================================================================

// Smart conditional asset loading - Only load CSS/JS for blocks actually used on the page
function dclt_enqueue_block_assets_conditionally() {
    global $post;
    
    if (!$post) return;
    
    $blocks_used = [];
    
    // Check which blocks are actually used on this page
    if (has_block('dclt/hero', $post)) $blocks_used[] = 'hero';
    // Add other blocks here as you build them
    
    // Only load shared assets if ANY blocks are used
    if (!empty($blocks_used)) {
        wp_enqueue_style(
            'dclt-blocks-shared',
            get_template_directory_uri() . '/blocks/shared/blocks.css',
            [],
            '1.0.0'
        );
        
        wp_enqueue_script(
            'dclt-blocks-shared',
            get_template_directory_uri() . '/blocks/shared/blocks.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
    
    // Load individual block assets only if used
    foreach ($blocks_used as $block) {
        dclt_enqueue_individual_block_assets($block);
    }
}
add_action('wp_enqueue_scripts', 'dclt_enqueue_block_assets_conditionally');

// Load individual block assets
function dclt_enqueue_individual_block_assets($block_name) {
    $block_path = get_template_directory();
    $block_url = get_template_directory_uri();
    
    // CSS - only if file exists
    $css_file = "{$block_path}/blocks/{$block_name}/{$block_name}.css";
    if (file_exists($css_file)) {
        wp_enqueue_style(
            "dclt-{$block_name}",
            "{$block_url}/blocks/{$block_name}/{$block_name}.css",
            ['dclt-blocks-shared'],
            '1.0.0'
        );
    }
    
    // JavaScript - only if file exists  
    $js_file = "{$block_path}/blocks/{$block_name}/{$block_name}.js";
    if (file_exists($js_file)) {
        wp_enqueue_script(
            "dclt-{$block_name}",
            "{$block_url}/blocks/{$block_name}/{$block_name}.js",
            ['dclt-blocks-shared'],
            '1.0.0',
            true
        );
    }
}

// Remove WordPress bloat for faster loading
function dclt_remove_wordpress_bloat() {
    // Remove emoji scripts (saves ~15KB)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Remove block editor CSS on frontend (saves ~20KB if not using core blocks)
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'dclt_remove_wordpress_bloat');

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

// Helper function to get block spacing class
function dclt_get_spacing_class($spacing = 'medium') {
    $spacing_map = [
        'small' => 'py-8',
        'medium' => 'py-16',
        'large' => 'py-24',
        'xlarge' => 'py-32'
    ];
    
    return isset($spacing_map[$spacing]) ? $spacing_map[$spacing] : $spacing_map['medium'];
}

// Helper function to get container width class
function dclt_get_container_class($width = 'content') {
    $container_map = [
        'narrow' => 'max-w-3xl mx-auto px-4',
        'content' => 'max-w-6xl mx-auto px-4', 
        'wide' => 'max-w-7xl mx-auto px-4',
        'full' => 'w-full px-4'
    ];
    
    return isset($container_map[$width]) ? $container_map[$width] : $container_map['content'];
}

// Helper function for button classes
function dclt_get_button_class($style = 'primary') {
    $button_map = [
        'primary' => 'bg-brand text-white hover:bg-brand-700 px-6 py-3 rounded-lg font-semibold transition-colors duration-200',
        'secondary' => 'bg-white text-brand border-2 border-brand hover:bg-brand hover:text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200',
        'landowner' => 'bg-primary-700 text-white hover:bg-primary-800 px-8 py-4 rounded-lg font-bold text-lg transition-colors duration-200',
        'explore' => 'bg-green-600 text-white hover:bg-green-700 px-6 py-3 rounded-lg font-semibold transition-colors duration-200'
    ];
    
    return isset($button_map[$style]) ? $button_map[$style] : $button_map['primary'];
}

// Assets for blocks
add_action('init', function () {
    if (!wp_script_is('dclt-hero-editor', 'registered')) {
        wp_register_script(
            'dclt-hero-editor',
            get_template_directory_uri() . '/blocks/hero/hero-editor.js',
            ['wp-blocks','wp-element','wp-i18n'],
            null,
            true
        );
    }
    if (!wp_script_is('dclt-hero-frontend', 'registered')) {
        wp_register_script(
            'dclt-hero-frontend',
            get_template_directory_uri() . '/blocks/hero/hero.js',
            [],
            null,
            true
        );
    }
    if (!wp_script_is('dclt-blocks-shared', 'registered')) {
        wp_register_script(
            'dclt-blocks-shared',
            get_template_directory_uri() . '/blocks/shared/blocks.js',
            ['jquery'],
            null,
            true
        );
    }
});

add_action('wp_enqueue_scripts', function () {
    if (!is_admin() && wp_script_is('dclt-blocks-shared', 'registered')) {
        wp_enqueue_script('dclt-blocks-shared');
    }
});

// Server-side block registration
add_action('init', function () {
    if (!function_exists('register_block_type')) return;

    $reg = WP_Block_Type_Registry::get_instance();
    if (!$reg->is_registered('dclt/hero')) {
        register_block_type('dclt/hero', [
            'editor_script'   => 'dclt-hero-editor',
            'script'          => 'dclt-hero-frontend',
            'render_callback' => 'dclt_render_block_hero',
            'attributes'      => [
                'blockId' => ['type' => 'string', 'default' => ''],
            ],
        ]);
    }
});

// Render callback for dclt/hero
if (!function_exists('dclt_render_block_hero')) {
    function dclt_render_block_hero($attributes = [], $content = '', $block = null) {
        $post_id  = get_the_ID();
        $block_id = isset($attributes['blockId']) ? sanitize_title($attributes['blockId']) : '';

        // If you have a template, use it:
        $template = get_template_directory() . '/blocks/hero/hero.php';
        if (file_exists($template)) {
            ob_start();
            // Make attributes available to template
            $dclt_attributes = $attributes;
            include $template;
            return ob_get_clean();
        }

        // Fallback debug output (proves render path works)
        $headline = dclt_get_field($post_id, 'hero_headline', $block_id, 'Protect the Land You Love');
        $bg_type  = dclt_get_field($post_id, 'hero_background_type', $block_id, 'image');

        return '<section class="dclt-hero-block" data-background-type="' . esc_attr($bg_type) . '" style="padding:4rem 1rem;background:#f0f9f4;border:2px dashed #006847;border-radius:12px">
                    <div class="container mx-auto">
                        <h1 class="text-3xl md:text-5xl font-semibold" style="margin:0 0 .5rem 0;color:#004d33">' . esc_html($headline) . ' <span style="font-size:.6em;opacity:.6">(debug)</span></h1>
                        <p style="margin:0;color:#444">Server-side block render is working. Add blocks/hero/hero.php for the designed version.</p>
                    </div>
                </section>';
    }
}
?>