<?php
/**
 * Hero block template
 */

$headline     = get_post_meta(get_the_ID(), 'dclt_hero_headline', true);
$intro        = get_post_meta(get_the_ID(), 'dclt_hero_intro', true);
$button1_text = get_post_meta(get_the_ID(), 'dclt_hero_button1_text', true);
$button1_url  = get_post_meta(get_the_ID(), 'dclt_hero_button1_url', true);
$button2_text = get_post_meta(get_the_ID(), 'dclt_hero_button2_text', true);
$button2_url  = get_post_meta(get_the_ID(), 'dclt_hero_button2_url', true);
$media_items  = get_post_meta(get_the_ID(), 'dclt_hero_media', true);

if (!is_array($media_items)) $media_items = [];

?>

<section class="dclt-hero-block relative overflow-hidden h-standard" data-slideshow-enabled="1" data-slideshow-count="<?php echo count($media_items); ?>" data-slideshow-interval="7000">
  <!-- Background media -->
  <div class="dclt-hero-media absolute inset-0" data-hero-slides>
    <?php foreach ($media_items as $index => $media): 
      $media_type = $media['type'] ?? 'image';
      $image_id   = $media['image'] ?? '';
      $video_url  = $media['video_url'] ?? '';
      $credit     = $media['photo_credit'] ?? '';
      $note       = $media['photo_note'] ?? '';
      $website    = $media['photo_website'] ?? '';
      $is_active  = $index === 0 ? 'is-active' : '';
      $slide_type = '';

      if ($media_type === 'video' && strpos($video_url, 'vimeo.com') !== false) {
        $slide_type = 'video-vimeo';
      } elseif ($media_type === 'video') {
        $slide_type = 'video-native';
      } elseif ($media_type === 'image') {
        $slide_type = 'image';
      }

      echo "<div class=\"hero-slide $is_active\" data-slide-index=\"$index\" data-slide-type=\"$slide_type\" data-slide-credit=\"" . esc_attr($credit) . "\" data-slide-note=\"" . esc_attr($note) . "\" data-slide-website=\"" . esc_url($website) . "\">";

      if ($slide_type === 'image' && $image_id) {
        $image_url = wp_get_attachment_image_url($image_id, 'full');
        echo "<img src=\"" . esc_url($image_url) . "\" class=\"hero-media-fade\" loading=\"lazy\" decoding=\"async\" aria-hidden=\"true\">";
      } elseif ($slide_type === 'video-vimeo' && $video_url) {
        $vimeo_url = esc_url($video_url) . '?autoplay=1&muted=1&loop=1&background=1';
        echo "<iframe class=\"hero-vimeo-iframe\" src=\"$vimeo_url\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen aria-hidden=\"true\"></iframe>";
      } elseif ($slide_type === 'video-native' && $video_url) {
        echo "<video class=\"hero-media-fade\" autoplay muted loop playsinline aria-hidden=\"true\">";
        echo "<source src=\"" . esc_url($video_url) . "\" type=\"video/mp4\">";
        echo "</video>";
      }

      echo "</div>";
    endforeach; ?>
  </div>

  <!-- Overlay -->
  <div class="dclt-hero-overlay absolute inset-0" style="opacity: 0.4;"></div>

  <!-- Text Content -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 hero-inner">
    <div class="hero-content">
      <?php if ($intro): ?>
        <p class="hero-intro"><?php echo esc_html($intro); ?></p>
      <?php endif; ?>
      <?php if ($headline): ?>
        <h1 class="hero-headline"><?php echo esc_html($headline); ?></h1>
      <?php endif; ?>

      <div class="hero-cta">
        <?php if ($button1_text && $button1_url): ?>
          <a href="<?php echo esc_url($button1_url); ?>" class="hero-button"><?php echo esc_html($button1_text); ?></a>
        <?php endif; ?>
        <?php if ($button2_text && $button2_url): ?>
          <a href="<?php echo esc_url($button2_url); ?>" class="hero-button"><?php echo esc_html($button2_text); ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Photo Credit -->
  <div class="photo-credit-block" data-note-id="hero-photo-note">
    <span class="hero-credit" data-photo-credit></span>
    <button type="button" class="hero-photo-note-toggle" aria-expanded="false" aria-controls="hero-photo-note" aria-haspopup="dialog">
      About this photo
    </button>
    <div id="hero-photo-note" class="hero-photo-note" role="region" hidden>
      <button type="button" class="hero-photo-note-close" aria-label="Close photo details">×</button>
      <p class="hero-photo-note-text" data-photo-note></p>
      <p class="hero-photo-note-link"><a href="#" target="_blank" rel="noopener noreferrer" data-photo-website>Visit photographer website</a></p>
    </div>
  </div>

  <!-- Bottom Divider -->
  <div class="absolute bottom-0 left-0 w-full">
    <svg class="w-full h-8 md:h-16" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
      <path d="M0,0 C300,120 900,120 1200,0 L1200,120 L0,120 Z" fill="white"></path>
    </svg>
  </div>
</section>