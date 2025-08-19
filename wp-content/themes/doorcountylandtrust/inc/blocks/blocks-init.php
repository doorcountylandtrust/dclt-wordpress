<?php 
/**
 * Door County Land Trust - Custom Block Registration System (No ACF)
 * FIXED VERSION - CTA Block now working properly
 */

error_log('DCLT blocks-init.php file is loading!');

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// CUSTOM FIELD FUNCTIONS (WordPress Native)
// =============================================================================

/**
 * Our replacement for get_field() - works with WordPress native meta
 */
function dclt_get_field($post_id, $field_name, $block_id = '', $default = '') {
    // Create unique meta key for this field
    $meta_key = $block_id ? "dclt_{$block_id}_{$field_name}" : "dclt_{$field_name}";
    
    // Get the meta value
    $value = get_post_meta($post_id, $meta_key, true);
    
    // Return default if empty
    return !empty($value) ? $value : $default;
}

/**
 * Our replacement for update_field()
 */
function dclt_update_field($post_id, $field_name, $value, $block_id = '') {
    $meta_key = $block_id ? "dclt_{$block_id}_{$field_name}" : "dclt_{$field_name}";
    return update_post_meta($post_id, $meta_key, $value);
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

    error_log('About to register CTA block...');
    
    // Register CTA Block JavaScript for editor
    wp_register_script(
        'dclt-cta-block-editor',
        get_template_directory_uri() . '/blocks/cta/cta-editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        '1.0.0'
    );

    // Register CTA Block
    register_block_type('dclt/cta', array(
        'editor_script' => 'dclt-cta-block-editor',
        'render_callback' => 'dclt_render_cta_block',
        'attributes' => array(
            'blockId' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
    
    error_log('CTA block registered successfully!');
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

/**
 * Render callback for CTA block - FIXED VERSION
 */
function dclt_render_cta_block($attributes, $content) {
    error_log('CTA block render called! Post ID: ' . (isset($GLOBALS['post']) ? $GLOBALS['post']->ID : 'no post'));
    
    global $post;
    if (!$post) return '';
    
    // Get field values using our custom function
    $layout_style = dclt_get_field($post->ID, 'cta_layout_style', '', 'centered');
    $background_style = dclt_get_field($post->ID, 'cta_background_style', '', 'brand-primary');
    $background_image = dclt_get_field($post->ID, 'cta_background_image', '');
    $icon = dclt_get_field($post->ID, 'cta_icon', '');
    $headline = dclt_get_field($post->ID, 'cta_headline', '');
    $description = dclt_get_field($post->ID, 'cta_description', '');
    $primary_action_text = dclt_get_field($post->ID, 'cta_primary_action_text', '');
    $primary_action_type = dclt_get_field($post->ID, 'cta_primary_action_type', '', 'link');
    $primary_action_url = dclt_get_field($post->ID, 'cta_primary_action_url', '');
    $primary_action_style = dclt_get_field($post->ID, 'cta_primary_action_style', '', 'primary');
    $secondary_action_text = dclt_get_field($post->ID, 'cta_secondary_action_text', '');
    $secondary_action_url = dclt_get_field($post->ID, 'cta_secondary_action_url', '');
    $secondary_action_style = dclt_get_field($post->ID, 'cta_secondary_action_style', '', 'secondary');
    $urgency_indicator = dclt_get_field($post->ID, 'cta_urgency_indicator', '', '0');
    $container_width = dclt_get_field($post->ID, 'cta_container_width', '', 'content');
    $spacing = dclt_get_field($post->ID, 'cta_spacing', '', 'medium');
    
    // Build section classes using semantic tokens
    $section_classes = ['dclt-cta-block', 'dclt-block', dclt_get_spacing_class($spacing), 'relative'];
    
    // Background styling with semantic tokens
    switch ($background_style) {
        case 'brand-primary':
            $section_classes[] = 'bg-brand-primary text-text-inverse';
            break;
        case 'brand-secondary':
            $section_classes[] = 'bg-brand-secondary text-text-primary';
            break;
        case 'surface-default':
            $section_classes[] = 'bg-surface-default text-text-primary border border-surface-border';
            break;
        case 'surface-card':
            $section_classes[] = 'bg-surface-card text-text-primary shadow-sm';
            break;
        case 'image':
            $section_classes[] = 'bg-gray-900 text-text-inverse relative';
            break;
    }
    
    // Layout classes
    $content_classes = [];
    if ($layout_style === 'centered') {
        $content_classes[] = 'text-center max-w-3xl mx-auto';
    } elseif ($layout_style === 'split') {
        $content_classes[] = 'grid md:grid-cols-2 gap-8 items-center';
    } elseif ($layout_style === 'card-grid') {
        $content_classes[] = 'grid md:grid-cols-2 lg:grid-cols-3 gap-6';
    }
    
    ob_start();
    ?>
    <section class="<?php echo implode(' ', $section_classes); ?>">
        
        <!-- Background Image -->
        <?php if ($background_style === 'image' && $background_image): ?>
            <div class="absolute inset-0">
                <?php $image_data = wp_get_attachment_image_src($background_image, 'large'); ?>
                <?php if ($image_data): ?>
                    <img src="<?php echo esc_url($image_data[0]); ?>" 
                         alt=""
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/50"></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="<?php echo dclt_get_container_class($container_width); ?>">
            
            <!-- Urgency Indicator -->
            <?php if ($urgency_indicator === '1'): ?>
                <div class="text-center mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-state-warning text-text-primary animate-pulse">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Time-Sensitive Opportunity
                    </span>
                </div>
            <?php endif; ?>

            <div class="<?php echo implode(' ', $content_classes); ?> relative z-10">
                
                <?php if ($layout_style === 'split'): ?>
                    <!-- Split Layout -->
                    <div class="space-y-4">
                        <?php if ($icon): ?>
                            <div class="flex-shrink-0">
                                <?php $icon_data = wp_get_attachment_image_src($icon, 'thumbnail'); ?>
                                <?php if ($icon_data): ?>
                                    <img src="<?php echo esc_url($icon_data[0]); ?>" 
                                         alt=""
                                         class="w-16 h-16 object-contain">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($headline): ?>
                            <h2 class="text-3xl md:text-4xl font-bold">
                                <?php echo wp_kses_post($headline); ?>
                            </h2>
                        <?php endif; ?>

                        <?php if ($description): ?>
                            <p class="text-lg opacity-90">
                                <?php echo wp_kses_post($description); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Actions for Split Layout -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center sm:justify-start">
                        <?php echo dclt_render_cta_actions($primary_action_text, $primary_action_type, $primary_action_url, $primary_action_style, $secondary_action_text, $secondary_action_url, $secondary_action_style, $background_style); ?>
                    </div>

                <?php else: ?>
                    <!-- Centered Layout -->
                    <?php if ($icon): ?>
                        <div class="mb-6">
                            <?php $icon_data = wp_get_attachment_image_src($icon, 'medium'); ?>
                            <?php if ($icon_data): ?>
                                <img src="<?php echo esc_url($icon_data[0]); ?>" 
                                     alt=""
                                     class="w-20 h-20 object-contain mx-auto">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($headline): ?>
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                            <?php echo wp_kses_post($headline); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <p class="text-lg md:text-xl mb-8 opacity-90 leading-relaxed">
                            <?php echo wp_kses_post($description); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Actions for Centered Layout -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <?php echo dclt_render_cta_actions($primary_action_text, $primary_action_type, $primary_action_url, $primary_action_style, $secondary_action_text, $secondary_action_url, $secondary_action_style, $background_style); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Helper function to render CTA actions
 */
function dclt_render_cta_actions($primary_text, $primary_type, $primary_url, $primary_style, $secondary_text, $secondary_url, $secondary_style, $background_style) {
    ob_start();
    
    if ($primary_text && $primary_url):
        $button_class = dclt_get_cta_button_class($primary_style, $background_style, true);
        
        if ($primary_type === 'phone'):
            ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $primary_url)); ?>" 
               class="<?php echo $button_class; ?>">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <?php echo esc_html($primary_text); ?>
            </a>
            <?php
        elseif ($primary_type === 'email'):
            ?>
            <a href="mailto:<?php echo esc_attr($primary_url); ?>" 
               class="<?php echo $button_class; ?>">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <?php echo esc_html($primary_text); ?>
            </a>
            <?php
        elseif ($primary_type === 'form'):
            ?>
            <button type="button" 
                    class="<?php echo $button_class; ?> dclt-form-trigger" 
                    data-form-type="landowner"
                    onclick="alert('Contact form will open here - Salesforce integration coming soon!')">
                <?php echo esc_html($primary_text); ?>
            </button>
            <?php
        else:
            // Regular button link
            ?>
            <a href="<?php echo esc_url($primary_url); ?>" 
               class="<?php echo $button_class; ?>">
                <?php echo esc_html($primary_text); ?>
            </a>
            <?php
        endif;
    endif;

    if ($secondary_text && $secondary_url):
        $secondary_class = dclt_get_cta_button_class($secondary_style, $background_style, false);
        ?>
        <a href="<?php echo esc_url($secondary_url); ?>" 
           class="<?php echo $secondary_class; ?>">
            <?php echo esc_html($secondary_text); ?>
        </a>
        <?php
    endif;

    return ob_get_clean();
}

/**
 * Helper function to get appropriate button classes based on CTA background and semantic tokens
 */
function dclt_get_cta_button_class($style, $background_style, $is_primary) {
    $base_classes = 'inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-lg transition-all duration-200 hover:transform hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Adjust button style based on background using semantic tokens
    if ($background_style === 'brand-primary' || $background_style === 'image') {
        // On dark backgrounds
        if ($style === 'primary' || ($is_primary && $style === 'secondary')) {
            return $base_classes . ' bg-button-primary text-text-inverse hover:bg-button-hover focus:ring-button-primary';
        } else {
            return $base_classes . ' border-2 border-button-primary text-button-primary hover:bg-button-primary hover:text-text-inverse focus:ring-button-primary';
        }
    } else {
        // On light backgrounds
        if ($style === 'primary') {
            return $base_classes . ' bg-button-primary text-text-inverse hover:bg-button-hover focus:ring-button-primary';
        } else {
            return $base_classes . ' border-2 border-button-primary text-button-primary hover:bg-button-primary hover:text-text-inverse focus:ring-button-primary';
        }
    }
}

// =============================================================================
// ADMIN META BOX FOR CTA BLOCK FIELDS
// =============================================================================

/**
 * Add meta box for CTA block settings
 */
function dclt_add_cta_meta_box() {
    $post_types = ['page', 'post'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'dclt-cta-fields',
            'CTA Block Settings',
            'dclt_cta_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'dclt_add_cta_meta_box');

/**
 * CTA Meta box callback
 */
function dclt_cta_meta_box_callback($post) {
    wp_nonce_field('dclt_cta_meta_box', 'dclt_cta_meta_box_nonce');
    
    // Get current values
    $layout_style = dclt_get_field($post->ID, 'cta_layout_style', '', 'centered');
    $background_style = dclt_get_field($post->ID, 'cta_background_style', '', 'brand-primary');
    $background_image = dclt_get_field($post->ID, 'cta_background_image', '');
    $icon = dclt_get_field($post->ID, 'cta_icon', '');
    $headline = dclt_get_field($post->ID, 'cta_headline', '');
    $description = dclt_get_field($post->ID, 'cta_description', '');
    $primary_action_text = dclt_get_field($post->ID, 'cta_primary_action_text', '');
    $primary_action_type = dclt_get_field($post->ID, 'cta_primary_action_type', '', 'link');
    $primary_action_url = dclt_get_field($post->ID, 'cta_primary_action_url', '');
    $primary_action_style = dclt_get_field($post->ID, 'cta_primary_action_style', '', 'primary');
    $secondary_action_text = dclt_get_field($post->ID, 'cta_secondary_action_text', '');
    $secondary_action_url = dclt_get_field($post->ID, 'cta_secondary_action_url', '');
    $secondary_action_style = dclt_get_field($post->ID, 'cta_secondary_action_style', '', 'secondary');
    $urgency_indicator = dclt_get_field($post->ID, 'cta_urgency_indicator', '', '0');
    $container_width = dclt_get_field($post->ID, 'cta_container_width', '', 'content');
    $spacing = dclt_get_field($post->ID, 'cta_spacing', '', 'medium');
    
    ?>
    <div class="dclt-cta-fields">
        
        <!-- Layout & Style -->
        <div class="dclt-field-group">
            <h4>Layout & Style</h4>
            
            <div class="dclt-field">
                <label for="cta_layout_style">Layout Style</label>
                <select id="cta_layout_style" name="cta_layout_style">
                    <option value="centered" <?php selected($layout_style, 'centered'); ?>>Centered - Content in center</option>
                    <option value="split" <?php selected($layout_style, 'split'); ?>>Split - Content left, actions right</option>
                </select>
            </div>
            
            <div class="dclt-field">
                <label for="cta_background_style">Background Style</label>
                <select id="cta_background_style" name="cta_background_style">
                    <option value="brand-primary" <?php selected($background_style, 'brand-primary'); ?>>Brand Primary - Main green</option>
                    <option value="brand-secondary" <?php selected($background_style, 'brand-secondary'); ?>>Brand Secondary - Light green</option>
                    <option value="surface-default" <?php selected($background_style, 'surface-default'); ?>>Surface Default - Clean white</option>
                    <option value="surface-card" <?php selected($background_style, 'surface-card'); ?>>Surface Card - Elevated white</option>
                    <option value="image" <?php selected($background_style, 'image'); ?>>Image Background</option>
                </select>
            </div>
            
            <div class="dclt-field" id="cta_background_image_field" style="<?php echo $background_style !== 'image' ? 'display:none;' : ''; ?>">
                <label for="cta_background_image">Background Image</label>
                <input type="hidden" id="cta_background_image" name="cta_background_image" value="<?php echo esc_attr($background_image); ?>">
                <button type="button" class="dclt-cta-media-button button">Choose Image</button>
                <button type="button" class="dclt-cta-remove-media button" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">Remove</button>
                <div class="dclt-cta-image-preview" style="<?php echo !$background_image ? 'display:none;' : ''; ?>">
                    <?php if ($background_image): ?>
                        <?php echo wp_get_attachment_image($background_image, 'medium'); ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="dclt-field">
                <label for="cta_container_width">Container Width</label>
                <select id="cta_container_width" name="cta_container_width">
                    <option value="narrow" <?php selected($container_width, 'narrow'); ?>>Narrow</option>
                    <option value="content" <?php selected($container_width, 'content'); ?>>Content</option>
                    <option value="wide" <?php selected($container_width, 'wide'); ?>>Wide</option>
                </select>
            </div>
            
            <div class="dclt-field">
                <label for="cta_spacing">Section Spacing</label>
                <select id="cta_spacing" name="cta_spacing">
                    <option value="small" <?php selected($spacing, 'small'); ?>>Small</option>
                    <option value="medium" <?php selected($spacing, 'medium'); ?>>Medium</option>
                    <option value="large" <?php selected($spacing, 'large'); ?>>Large</option>
                </select>
            </div>
        </div>
        
        <!-- Content -->
        <div class="dclt-field-group">
            <h4>Content</h4>
            
            <div class="dclt-field">
                <label for="cta_icon">Icon (Optional)</label>
                <input type="hidden" id="cta_icon" name="cta_icon" value="<?php echo esc_attr($icon); ?>">
                <button type="button" class="dclt-cta-icon-button button">Choose Icon</button>
                <button type="button" class="dclt-cta-remove-icon button" style="<?php echo !$icon ? 'display:none;' : ''; ?>">Remove</button>
                <div class="dclt-cta-icon-preview" style="<?php echo !$icon ? 'display:none;' : ''; ?>">
                    <?php if ($icon): ?>
                        <?php echo wp_get_attachment_image($icon, 'thumbnail'); ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="dclt-field">
                <label for="cta_headline">Headline</label>
                <input type="text" id="cta_headline" name="cta_headline" value="<?php echo esc_attr($headline); ?>" maxlength="80" placeholder="Ready to Protect Your Land?">
                <div class="description">Action-oriented headline that motivates visitors.</div>
            </div>
            
            <div class="dclt-field">
                <label for="cta_description">Description</label>
                <textarea id="cta_description" name="cta_description" rows="3" maxlength="200"><?php echo esc_textarea($description); ?></textarea>
                <div class="description">Supporting text that explains the value or creates urgency.</div>
            </div>
            
            <div class="dclt-field">
                <label for="cta_urgency_indicator">
                    <input type="checkbox" id="cta_urgency_indicator" name="cta_urgency_indicator" value="1" <?php checked($urgency_indicator, '1'); ?>>
                    Show Urgency Indicator
                </label>
                <div class="description">Add a "Time-Sensitive" badge for urgent campaigns.</div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="dclt-field-group">
            <h4>Actions</h4>
            
            <div class="dclt-field">
                <label for="cta_primary_action_text">Primary Action Text</label>
                <input type="text" id="cta_primary_action_text" name="cta_primary_action_text" value="<?php echo esc_attr($primary_action_text); ?>" maxlength="25" placeholder="Contact Us Today">
            </div>
            
            <div class="dclt-field">
                <label for="cta_primary_action_type">Primary Action Type</label>
                <select id="cta_primary_action_type" name="cta_primary_action_type">
                    <option value="link" <?php selected($primary_action_type, 'link'); ?>>Link - Goes to a page</option>
                    <option value="form" <?php selected($primary_action_type, 'form'); ?>>Form - Opens contact form</option>
                    <option value="phone" <?php selected($primary_action_type, 'phone'); ?>>Phone - Click to call</option>
                    <option value="email" <?php selected($primary_action_type, 'email'); ?>>Email - Click to email</option>
                </select>
            </div>
            
            <div class="dclt-field">
                <label for="cta_primary_action_url">Primary Action URL/Phone/Email</label>
                <input type="text" id="cta_primary_action_url" name="cta_primary_action_url" value="<?php echo esc_attr($primary_action_url); ?>" placeholder="https://example.com or phone number or email">
            </div>
            
            <div class="dclt-field">
                <label for="cta_primary_action_style">Primary Button Style</label>
                <select id="cta_primary_action_style" name="cta_primary_action_style">
                    <option value="primary" <?php selected($primary_action_style, 'primary'); ?>>Primary Button</option>
                    <option value="secondary" <?php selected($primary_action_style, 'secondary'); ?>>Secondary Button</option>
                </select>
            </div>
            
            <div class="dclt-field">
                <label for="cta_secondary_action_text">Secondary Action Text (Optional)</label>
                <input type="text" id="cta_secondary_action_text" name="cta_secondary_action_text" value="<?php echo esc_attr($secondary_action_text); ?>" maxlength="25" placeholder="Learn More">
            </div>
            
            <div class="dclt-field">
                <label for="cta_secondary_action_url">Secondary Action URL</label>
                <input type="url" id="cta_secondary_action_url" name="cta_secondary_action_url" value="<?php echo esc_attr($secondary_action_url); ?>">
            </div>
            
            <div class="dclt-field">
                <label for="cta_secondary_action_style">Secondary Button Style</label>
                <select id="cta_secondary_action_style" name="cta_secondary_action_style">
                    <option value="secondary" <?php selected($secondary_action_style, 'secondary'); ?>>Secondary Button</option>
                    <option value="primary" <?php selected($secondary_action_style, 'primary'); ?>>Primary Button</option>
                </select>
            </div>
        </div>
        
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Handle background type changes
        $('#cta_background_style').change(function() {
            var type = $(this).val();
            if (type === 'image') {
                $('#cta_background_image_field').show();
            } else {
                $('#cta_background_image_field').hide();
            }
        });
        
        // Media library for background images
        $('.dclt-cta-media-button').click(function(e) {
            e.preventDefault();
            var media_uploader = wp.media({
                title: 'Choose CTA Background Image',
                button: { text: 'Use This Image' },
                multiple: false,
                library: { type: 'image' }
            });
            
            media_uploader.on('select', function() {
                var attachment = media_uploader.state().get('selection').first().toJSON();
                $('#cta_background_image').val(attachment.id);
                $('.dclt-cta-image-preview').html('<img src="' + attachment.url + '" alt="">').show();
                $('.dclt-cta-remove-media').show();
            });
            
            media_uploader.open();
        });
        
        // Remove background image
        $('.dclt-cta-remove-media').click(function(e) {
            e.preventDefault();
            $('#cta_background_image').val('');
            $('.dclt-cta-image-preview').hide();
            $(this).hide();
        });
        
        // Media library for icons
        $('.dclt-cta-icon-button').click(function(e) {
            e.preventDefault();
            var media_uploader = wp.media({
                title: 'Choose CTA Icon',
                button: { text: 'Use This Icon' },
                multiple: false,
                library: { type: 'image' }
            });
            
            media_uploader.on('select', function() {
                var attachment = media_uploader.state().get('selection').first().toJSON();
                $('#cta_icon').val(attachment.id);
                $('.dclt-cta-icon-preview').html('<img src="' + attachment.url + '" alt="">').show();
                $('.dclt-cta-remove-icon').show();
            });
            
            media_uploader.open();
        });
        
        // Remove icon
        $('.dclt-cta-remove-icon').click(function(e) {
            e.preventDefault();
            $('#cta_icon').val('');
            $('.dclt-cta-icon-preview').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Save CTA meta box data
 */
function dclt_save_cta_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['dclt_cta_meta_box_nonce']) || !wp_verify_nonce($_POST['dclt_cta_meta_box_nonce'], 'dclt_cta_meta_box')) {
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
    
    // List of all CTA fields
    $cta_fields = [
        'cta_layout_style',
        'cta_background_style',
        'cta_background_image',
        'cta_icon',
        'cta_headline',
        'cta_description',
        'cta_primary_action_text',
        'cta_primary_action_type',
        'cta_primary_action_url',
        'cta_primary_action_style',
        'cta_secondary_action_text',
        'cta_secondary_action_url',
        'cta_secondary_action_style',
        'cta_urgency_indicator',
        'cta_container_width',
        'cta_spacing'
    ];
    
    // Save each field
    foreach ($cta_fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
        } else {
            // Handle checkboxes (urgency_indicator)
            if ($field === 'cta_urgency_indicator') {
                dclt_update_field($post_id, $field, '0');
            }
        }
    }
}
add_action('save_post', 'dclt_save_cta_meta_box');

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
// PERFORMANCE OPTIMIZED ASSET LOADING - FIXED VERSION
// =============================================================================

// Smart conditional asset loading - Only load CSS/JS for blocks actually used on the page
function dclt_enqueue_block_assets_conditionally() {
    // Try multiple ways to get the current post
    global $post;
    
    // If no global post, try get_queried_object
    if (!$post) {
        $post = get_queried_object();
    }
    
    // If still no post, or not a post object, return
    if (!$post || !isset($post->post_content)) {
        return;
    }
    
    $blocks_used = [];
    
    // Check which blocks are actually used on this page - FIXED VERSION
    if (has_block('dclt/hero', $post)) {
        $blocks_used[] = 'hero';
        error_log('Hero block detected!'); 
    }
    if (has_block('dclt/cta', $post)) {
        $blocks_used[] = 'cta';
        error_log('CTA block detected!'); 
    }

    error_log('Blocks used: ' . implode(', ', $blocks_used));
    
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
?>