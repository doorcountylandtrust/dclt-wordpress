<?php
/**
 * Hero Block Template
 * File: blocks/hero/hero.php
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

global $dclt_attributes;

$post_id = get_the_ID();
$block_id = isset($dclt_attributes['blockId']) ? sanitize_title($dclt_attributes['blockId']) : '';

// Get field values using our custom function
$background_type = dclt_get_field($post_id, 'hero_background_type', '', 'image');
$background_image = dclt_get_field($post_id, 'hero_background_image', '');
$background_video = dclt_get_field($post_id, 'hero_background_video', '');
$background_color = dclt_get_field($post_id, 'hero_background_color', '', '#006847');
$overlay_opacity = dclt_get_field($post_id, 'hero_overlay_opacity', '', '40');
$content_alignment = dclt_get_field($post_id, 'hero_content_alignment', '', 'left');
$headline = dclt_get_field($post_id, 'hero_headline', '');
$subheadline = dclt_get_field($post_id, 'hero_subheadline', '');
$primary_cta_text = dclt_get_field($post_id, 'hero_primary_cta_text', '');
$primary_cta_url = dclt_get_field($post_id, 'hero_primary_cta_url', '');
$primary_cta_style = dclt_get_field($post_id, 'hero_primary_cta_style', '', 'primary');
$secondary_cta_text = dclt_get_field($post_id, 'hero_secondary_cta_text', '');
$secondary_cta_url = dclt_get_field($post_id, 'hero_secondary_cta_url', '');
$secondary_cta_style = dclt_get_field($post_id, 'hero_secondary_cta_style', '', 'secondary');
$container_width = dclt_get_field($post_id, 'hero_container_width', '', 'wide');
$curved_bottom = dclt_get_field($post_id, 'hero_curved_bottom', '', '1');
?>

<section class="dclt-hero-block relative overflow-hidden <?php echo $background_type === 'color' ? 'text-white' : ''; ?>" 
         data-background-type="<?php echo esc_attr($background_type); ?>">
    
    <!-- Background Layer -->
    <div class="absolute inset-0 hero-img-container">
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