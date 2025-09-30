<?php
/**
 * Hero Block Template
 * File: blocks/hero/hero.php
 * Pair with blocks/hero/hero.css
 */

$post_id  = get_the_ID();
$block_id = isset($dclt_attributes['blockId']) ? sanitize_title($dclt_attributes['blockId']) : '';

// Fields
$bg_type         = dclt_get_field($post_id, 'hero_background_type', '', 'image'); // image|video|color
$bg_image_id     = dclt_get_field($post_id, 'hero_background_image', '');
$bg_video_id     = dclt_get_field($post_id, 'hero_background_video', '');
$bg_color        = dclt_get_field($post_id, 'hero_background_color', '', '#065f46');

$overlay_strength = dclt_get_field($post_id, 'hero_overlay_strength', '', 'auto'); // optional (auto/light/medium/dark/none)
$overlay_opacity  = dclt_get_field($post_id, 'hero_overlay_opacity', '', '40');    // legacy slider support

$headline     = dclt_get_field($post_id, 'hero_headline', '', 'Protect the Land You Love');
$subheadline  = dclt_get_field($post_id, 'hero_subheadline', '', '');

$primary_text = dclt_get_field($post_id, 'hero_primary_cta_text', '', '');
$primary_url  = dclt_get_field($post_id, 'hero_primary_cta_url', '', '');

$secondary_text = dclt_get_field($post_id, 'hero_secondary_cta_text', '', '');
$secondary_url  = dclt_get_field($post_id, 'hero_secondary_cta_url', '', '');

$container_width = dclt_get_field($post_id, 'hero_container_width', '', 'wide');
$curved_bottom   = dclt_get_field($post_id, 'hero_curved_bottom', '', '1');

$height         = dclt_get_field($post_id, 'hero_height', '', 'standard'); // optional: short|standard|tall
$height_class   = 'h-' . $height;

// Focal point (optional future fields)
$focal_x = (int) dclt_get_field($post_id, 'hero_focal_x', '', '50');
$focal_y = (int) dclt_get_field($post_id, 'hero_focal_y', '', '50');
$fp_style = sprintf('--fx:%d%%;--fy:%d%%;', $focal_x, $focal_y);

// Derived classes
$container_class = dclt_get_container_class($container_width);

// Overlay alpha (if you adopt strength field)
$alpha_map = ['light'=>0.25,'medium'=>0.45,'dark'=>0.65,'none'=>0.0];
$overlay_alpha = ($overlay_strength === 'auto')
    ? (max(0.0, min(1.0, (int)$overlay_opacity / 100)))   // use slider if present
    : ($alpha_map[$overlay_strength] ?? 0.45);

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

<section class="dclt-hero-block relative overflow-hidden <?php echo esc_attr($height_class); ?>"
         data-background-type="<?php echo esc_attr($bg_type); ?>">

  <!-- Background media -->
  <div class="dclt-hero-media absolute inset-0">
    <?php if ($bg_type === 'image' && $bg_image_url): ?>
      <img src="<?php echo esc_url($bg_image_url); ?>" alt="" aria-hidden="true"
           class="w-full h-full object-cover"
           style="<?php echo esc_attr($fp_style); ?>" />
    <?php elseif ($bg_type === 'video' && $bg_video_url): ?>
      <video class="w-full h-full object-cover" autoplay muted loop playsinline
             style="<?php echo esc_attr($fp_style); ?>">
        <source src="<?php echo esc_url($bg_video_url); ?>" type="video/mp4" />
      </video>
    <?php elseif ($bg_type === 'color'): ?>
      <div style="background: <?php echo esc_attr($bg_color); ?>; width:100%; height:100%;"></div>
    <?php endif; ?>
  </div>

  <!-- Overlay layer -->
  <?php if ($bg_type !== 'color' && $overlay_alpha > 0): ?>
    <div class="dclt-hero-overlay absolute inset-0" style="opacity: <?php echo esc_attr($overlay_alpha); ?>"></div>
  <?php endif; ?>

  <!-- Content -->
  <div class="<?php echo esc_attr($container_class); ?> relative z-10 hero-inner">
    <div class="hero-content">

      <?php if ($subheadline): ?>
        <p class="hero-intro">
          <?php echo wp_kses_post($subheadline); ?>
        </p>
      <?php endif; ?>

      <?php if ($headline): ?>
        <h1 class="hero-headline">
          <?php echo wp_kses_post($headline); ?>
        </h1>
      <?php endif; ?>

      <?php if ($primary_text || $secondary_text): ?>
        <div class="hero-cta">
          <?php if ($primary_text && $primary_url): ?>
            <a href="<?php echo esc_url($primary_url); ?>" class="hero-button hero-button--primary">
               <?php echo esc_html($primary_text); ?>
            </a>
          <?php endif; ?>

          <?php if ($secondary_text && $secondary_url): ?>
            <a href="<?php echo esc_url($secondary_url); ?>" class="hero-button hero-button--secondary">
               <?php echo esc_html($secondary_text); ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- Curved Bottom Divider -->
  <?php if ($curved_bottom === '1'): ?>
    <div class="absolute bottom-0 left-0 w-full">
      <svg class="w-full h-8 md:h-16" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0,0 C300,120 900,120 1200,0 L1200,120 L0,120 Z" fill="white" />
      </svg>
    </div>
  <?php endif; ?>

</section>
