<?php
// blocks/hero/hero.php

$post_id  = get_the_ID();
$block_id = isset($dclt_attributes['blockId']) ? sanitize_title($dclt_attributes['blockId']) : '';

// Read fields (no block_id since your meta box saves global per post)
$bg_type          = dclt_get_field($post_id, 'hero_background_type', '', 'image');   // image | video | color
$bg_image_id      = dclt_get_field($post_id, 'hero_background_image', '');
$bg_video_id      = dclt_get_field($post_id, 'hero_background_video', '');
$bg_color         = dclt_get_field($post_id, 'hero_background_color', '', '#004d33');
$overlay_opacity  = (int) dclt_get_field($post_id, 'hero_overlay_opacity', '', '40'); // 0-80
$align            = dclt_get_field($post_id, 'hero_content_alignment', '', 'left');   // left | center | right
$headline         = dclt_get_field($post_id, 'hero_headline', '', 'Protect the Land You Love');
$subheadline      = dclt_get_field($post_id, 'hero_subheadline', '', '');
$primary_text     = dclt_get_field($post_id, 'hero_primary_cta_text', '', '');
$primary_url      = dclt_get_field($post_id, 'hero_primary_cta_url', '', '');
$primary_style    = dclt_get_field($post_id, 'hero_primary_cta_style', '', 'primary');
$secondary_text   = dclt_get_field($post_id, 'hero_secondary_cta_text', '', '');
$secondary_url    = dclt_get_field($post_id, 'hero_secondary_cta_url', '', '');
$secondary_style  = dclt_get_field($post_id, 'hero_secondary_cta_style', '', 'secondary');
$container_width  = dclt_get_field($post_id, 'hero_container_width', '', 'wide');     // narrow|content|wide|full
$curved_bottom    = dclt_get_field($post_id, 'hero_curved_bottom', '', '1');

// Derived
$container_class = dclt_get_container_class($container_width);
$text_align = $align === 'center' ? 'text-center mx-auto' : ($align === 'right' ? 'text-right ml-auto' : 'text-left');
$justify = $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : 'justify-start');
$text_on_media = ($bg_type !== 'color'); // if image/video, we’ll default to white text for contrast

// Media URLs
$bg_image_url = '';
if ($bg_type === 'image' && $bg_image_id) {
  $img = wp_get_attachment_image_src($bg_image_id, 'full');
  if ($img) $bg_image_url = $img[0];
}
$bg_video_url = '';
if ($bg_type === 'video' && $bg_video_id) {
  $bg_video_url = wp_get_attachment_url($bg_video_id);
}
?>
<section class="relative w-full overflow-hidden dclt-hero-block"
         data-background-type="<?php echo esc_attr($bg_type); ?>">

  <!-- Background layer -->
  <div class="absolute inset-0">
    <?php if ($bg_type === 'image' && $bg_image_url): ?>
      <img src="<?php echo esc_url($bg_image_url); ?>" alt="" aria-hidden="true"
           class="absolute inset-0 w-full h-full object-cover" />
    <?php elseif ($bg_type === 'video' && $bg_video_url): ?>
      <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline>
        <source src="<?php echo esc_url($bg_video_url); ?>" type="video/mp4" />
      </video>
    <?php elseif ($bg_type === 'color'): ?>
      <div class="absolute inset-0" style="background: <?php echo esc_attr($bg_color); ?>;"></div>
    <?php endif; ?>

    <?php if ($bg_type !== 'color' && $overlay_opacity > 0): ?>
      <div class="absolute inset-0 bg-black" style="opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;"></div>
    <?php endif; ?>
  </div>

  <!-- Content layer -->
  <div class="<?php echo esc_attr($container_class); ?> relative z-10">
    <div class="min-h-[60vh] md:min-h-[70vh] py-16 md:py-24 flex items-center">
      <div class="max-w-3xl <?php echo esc_attr($text_align); ?>">
        <h1 class="font-semibold leading-tight
                   text-4xl md:text-5xl lg:text-6xl
                   <?php echo $text_on_media ? 'text-white' : 'text-primary-900'; ?>">
          <?php echo wp_kses_post($headline); ?>
        </h1>

        <?php if ($subheadline): ?>
          <p class="mt-4 md:mt-5 text-lg md:text-xl
                    <?php echo $text_on_media ? 'text-white/90' : 'text-primary-900/80'; ?>">
            <?php echo wp_kses_post($subheadline); ?>
          </p>
        <?php endif; ?>

        <?php if ($primary_text || $secondary_text): ?>
          <div class="mt-8 flex flex-col sm:flex-row gap-4 <?php echo esc_attr($justify); ?>">
            <?php if ($primary_text && $primary_url): ?>
              <a href="<?php echo esc_url($primary_url); ?>"
                 class="<?php echo esc_attr(dclt_get_button_class($primary_style)); ?> inline-block text-center">
                 <?php echo esc_html($primary_text); ?>
              </a>
            <?php endif; ?>

            <?php if ($secondary_text && $secondary_url): ?>
              <a href="<?php echo esc_url($secondary_url); ?>"
                 class="<?php echo esc_attr(dclt_get_button_class($secondary_style)); ?> inline-block text-center">
                 <?php echo esc_html($secondary_text); ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if ($curved_bottom === '1'): ?>
    <div class="absolute bottom-0 left-0 w-full">
      <svg class="w-full h-8 md:h-16" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0,0 C300,120 900,120 1200,0 L1200,120 L0,120 Z" fill="white" />
      </svg>
    </div>
  <?php endif; ?>
</section>