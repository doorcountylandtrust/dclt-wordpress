<?php
/**
 * Hero Block Template
 * File: blocks/hero/hero.php
 * Pair with blocks/hero/hero.css
 */

$post_id  = get_the_ID();
$block_id = isset($dclt_attributes['blockId']) ? sanitize_title($dclt_attributes['blockId']) : '';

// Fields

// Vimeo Video Embed Support
function dclt_get_vimeo_embed_url($url) {
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1] . '?background=1&autoplay=1&loop=1&byline=0&title=0&muted=1';
    }
    return '';
}
$overlay_strength = dclt_get_field($post_id, 'hero_overlay_strength', '', 'auto'); // optional (auto/light/medium/dark/none)
$overlay_opacity  = dclt_get_field($post_id, 'hero_overlay_opacity', '', '40');    // legacy slider support

$headline     = dclt_get_field($post_id, 'hero_headline', '', 'Protect the Land You Love');
$subheadline  = dclt_get_field($post_id, 'hero_subheadline', '', '');

$legacy_button_1_text = dclt_get_field($post_id, 'hero_primary_cta_text', '', 'Protect Your Land');
$legacy_button_1_url  = dclt_get_field($post_id, 'hero_primary_cta_url', '', '');
$legacy_button_2_text = dclt_get_field($post_id, 'hero_secondary_cta_text', '', '');
$legacy_button_2_url  = dclt_get_field($post_id, 'hero_secondary_cta_url', '', '');

$button_1_text = dclt_get_field($post_id, 'hero_button_1_text', '', $legacy_button_1_text);
$button_1_url  = dclt_get_field($post_id, 'hero_button_1_url', '', $legacy_button_1_url);
$button_2_text = dclt_get_field($post_id, 'hero_button_2_text', '', $legacy_button_2_text);
$button_2_url  = dclt_get_field($post_id, 'hero_button_2_url', '', $legacy_button_2_url);

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

$slideshow_enabled_flag = dclt_get_field($post_id, 'hero_slideshow_enabled', '', '1');

$media_sources = [
  get_post_meta($post_id, 'dclt_hero_media', true),
  dclt_get_field($post_id, 'hero_media_gallery', '', []),
  dclt_get_field($post_id, 'hero_media', '', []),
];

$photo_credit = '';
$photo_note = '';
$photo_website = '';

$slides = [];
foreach ($media_sources as $source) {
  if (!is_array($source) || empty($source)) { continue; }
  foreach ($source as $entry) {
    if (!is_array($entry)) { continue; }

    $type = isset($entry['type']) && $entry['type'] === 'video' ? 'video' : 'image';
    $image_id = isset($entry['image_id']) ? (int) $entry['image_id'] : 0;
    $image_url = '';
    if ($image_id) {
      $image_src = wp_get_attachment_image_src($image_id, 'full');
      if ($image_src) {
        $image_url = $image_src[0];
      }
    }

    if ($type === 'image' && !$image_url && !empty($entry['url'])) {
      $image_url = esc_url_raw($entry['url']);
    }

    $video_url_raw = isset($entry['video_url']) ? trim((string) $entry['video_url']) : '';
    if ($type === 'video' && $video_url_raw === '') {
      $type = 'image';
    }

    if ($type === 'image' && $image_url === '') {
      continue;
    }

    $credit   = isset($entry['credit']) ? trim((string) $entry['credit']) : '';
    $note     = isset($entry['note']) ? trim((string) $entry['note']) : '';
    $website  = isset($entry['website']) ? trim((string) $entry['website']) : '';
    if ($website === '' && isset($entry['link'])) {
      $website = trim((string) $entry['link']);
    }

    $slides[] = [
      'type'         => $type,
      'image_id'     => $image_id,
      'image_url'    => $image_url,
      'video_url'    => $type === 'video' ? esc_url_raw($video_url_raw) : '',
      'fallback_url' => $image_url,
      'color'        => '',
      'credit'       => $credit !== '' ? $credit : $photo_credit,
      'note'         => $note !== '' ? $note : $photo_note,
      'website'      => $website !== '' ? esc_url_raw($website) : $photo_website,
    ];
  }
  if (!empty($slides)) { break; }
}

if (empty($slides)) {
  $slides[] = [
    'type'         => 'color',
    'image_id'     => 0,
    'image_url'    => '',
    'video_url'    => '',
    'fallback_url' => '',
    'color'        => '#065f46',
    'credit'       => $photo_credit,
    'note'         => $photo_note,
    'website'      => $photo_website,
  ];
}

$slide_count = count($slides);
$autoplay_enabled = ($slideshow_enabled_flag !== '0') && $slide_count > 1;
$active_slide = $slide_count ? $slides[0] : null;

$active_credit  = $active_slide ? $active_slide['credit'] : $photo_credit;
$active_note    = $active_slide ? $active_slide['note'] : $photo_note;
$active_website = $active_slide ? $active_slide['website'] : $photo_website;

$any_slide_has_meta = false;
foreach ($slides as $slide_meta) {
  if (($slide_meta['credit'] ?? '') !== '' || ($slide_meta['note'] ?? '') !== '' || ($slide_meta['website'] ?? '') !== '') {
    $any_slide_has_meta = true;
    break;
  }
}

$overlay_applicable = false;
foreach ($slides as $slide_meta) {
  if (($slide_meta['type'] ?? '') !== 'color') {
    $overlay_applicable = true;
    break;
  }
}

$initial_has_note = $active_note !== '';
$initial_has_website = $active_website !== '';

$section_bg_type = $active_slide ? ($active_slide['type'] ?? 'image') : 'color';
?>

<section class="dclt-hero-block relative overflow-hidden <?php echo esc_attr($height_class); ?>"
         data-background-type="<?php echo esc_attr($section_bg_type); ?>"
         data-slideshow-enabled="<?php echo $autoplay_enabled ? '1' : '0'; ?>"
         data-slideshow-count="<?php echo esc_attr($slide_count); ?>"
         data-slideshow-interval="7000">

  <!-- Background media -->
  <div class="dclt-hero-media absolute inset-0" data-hero-slides>
    <?php foreach ($slides as $index => $slide_meta):
      $slide_type      = $slide_meta['type'] ?? 'image';
      $slide_image     = $slide_meta['image_url'] ?? '';
      $slide_video     = $slide_meta['video_url'] ?? '';
      $slide_color     = $slide_meta['color'] ?? '';
      $slide_credit    = $slide_meta['credit'] ?? '';
      $slide_note      = $slide_meta['note'] ?? '';
      $slide_website   = $slide_meta['website'] ?? '';
      $slide_fallback  = $slide_meta['fallback_url'] ?? '';
      $note_attr       = $slide_note !== '' ? esc_attr(wp_json_encode($slide_note)) : '';
      $website_attr    = $slide_website !== '' ? esc_attr($slide_website) : '';
      $fallback_attr   = $slide_fallback !== '' ? esc_attr($slide_fallback) : '';
    ?>
      <div class="hero-slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
           data-slide-index="<?php echo esc_attr($index); ?>"
           data-slide-type="<?php echo esc_attr($slide_type); ?>"
           <?php if ($slide_credit !== ''): ?>data-slide-credit="<?php echo esc_attr($slide_credit); ?>"<?php endif; ?>
           <?php if ($note_attr !== ''): ?>data-slide-note="<?php echo $note_attr; ?>"<?php endif; ?>
           <?php if ($website_attr !== ''): ?>data-slide-website="<?php echo $website_attr; ?>"<?php endif; ?>
           <?php if ($fallback_attr !== ''): ?>data-slide-fallback="<?php echo $fallback_attr; ?>"<?php endif; ?>>
        <?php if ($slide_type === 'video' && $slide_video): ?>
          <video class="hero-slide-video" autoplay muted loop playsinline preload="metadata"<?php if ($slide_fallback): ?> poster="<?php echo esc_url($slide_fallback); ?>"<?php endif; ?>>
            <source src="<?php echo esc_url($slide_video); ?>" type="video/mp4" />
          </video>
        <?php elseif ($slide_type === 'color'): ?>
          <div class="hero-slide-color" style="background: <?php echo esc_attr($slide_color ?: $bg_color); ?>;"></div>
        <?php else: ?>
          <img src="<?php echo esc_url($slide_image); ?>" alt="" aria-hidden="true"
               class="hero-media-fade"
               loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
               style="<?php echo esc_attr($fp_style); ?>" />
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Overlay layer -->
  <?php if ($overlay_applicable && $overlay_alpha > 0): ?>
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

      <?php $has_buttons = ($button_1_text && $button_1_url) || ($button_2_text && $button_2_url); ?>
      <?php if ($has_buttons): ?>
        <div class="hero-cta">
          <?php if ($button_1_text && $button_1_url): ?>
            <a href="<?php echo esc_url($button_1_url); ?>" class="hero-button">
               <?php echo esc_html($button_1_text); ?>
            </a>
          <?php endif; ?>

          <?php if ($button_2_text && $button_2_url): ?>
            <a href="<?php echo esc_url($button_2_url); ?>" class="hero-button">
               <?php echo esc_html($button_2_text); ?>
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
