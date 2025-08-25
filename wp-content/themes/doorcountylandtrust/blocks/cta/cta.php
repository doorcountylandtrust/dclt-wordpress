<?php
/**
 * Door County Land Trust – CTA block template
 * File: blocks/cta/cta.php
 */
if (!defined('ABSPATH')) { exit; }

$post_id  = get_the_ID();
$attrs    = isset($dclt_attributes) && is_array($dclt_attributes) ? $dclt_attributes : [];
$block_id = isset($attrs['blockId']) ? sanitize_title($attrs['blockId']) : 'cta-' . (string) $post_id;

/** ------------------------------------------------------------------------
 * Admin safety: keep the editor stable
 * ---------------------------------------------------------------------- */
if (is_admin() && !wp_doing_ajax()) {
    echo '<div class="dclt-cta-block" style="border:1px dashed #cbd5e1;padding:12px;border-radius:8px;background:#f8fafc;">
            <strong>CTA block</strong> (server-rendered). Use “CTA Block Settings” in the sidebar panels.
          </div>';
    return;
}

/** ------------------------------------------------------------------------
 * Read meta (native helpers you already use)
 * ---------------------------------------------------------------------- */
$layout          = dclt_get_field($post_id, 'cta_layout', '', 'split');                 // split | stacked
$bg_style        = dclt_get_field($post_id, 'cta_background_style', '', 'brand-primary'); // brand-primary | light | image
$container_width = dclt_get_field($post_id, 'cta_container_width', '', 'content');      // content | wide | full
$spacing         = dclt_get_field($post_id, 'cta_spacing', '', 'medium');               // compact | medium | spacious

$icon_id         = dclt_get_field($post_id, 'cta_icon_id', '', '');
$headline        = dclt_get_field($post_id, 'cta_headline', '', 'Ready to Protect Your Land?');
$description     = dclt_get_field($post_id, 'cta_description', '', '');
$urgency         = dclt_get_field($post_id, 'cta_show_urgency', '', '0');               // '1' or '0'

$primary_text    = dclt_get_field($post_id, 'cta_primary_text', '', 'Contact Us Today');
$primary_type    = dclt_get_field($post_id, 'cta_primary_type', '', 'link');            // link | phone | email
$primary_target  = dclt_get_field($post_id, 'cta_primary_target', '', '#');
$primary_style   = dclt_get_field($post_id, 'cta_primary_style', '', 'primary');        // primary | secondary | outline

$secondary_text  = dclt_get_field($post_id, 'cta_secondary_text', '', '');
$secondary_target= dclt_get_field($post_id, 'cta_secondary_target', '', '');
$secondary_style = dclt_get_field($post_id, 'cta_secondary_style', '', 'secondary');

$bg_image_id     = dclt_get_field($post_id, 'cta_background_image', '', '');
$bg_image_url    = $bg_image_id ? ( wp_get_attachment_image_src((int)$bg_image_id, 'full')[0] ?? '' ) : '';

/** ------------------------------------------------------------------------
 * Map config -> classes (matches your cta.css)
 * ---------------------------------------------------------------------- */
$container_class = function_exists('dclt_get_container_class') ? dclt_get_container_class($container_width) : 'max-w-6xl mx-auto px-4';
$spacing_class   = function_exists('dclt_get_spacing_class')   ? dclt_get_spacing_class($spacing)           : 'py-16';

switch ($bg_style) {
    case 'brand-primary': $bg_class = 'bg-brand-primary text-white'; break;
    case 'light':         $bg_class = 'bg-surface-default';          break;
    case 'image':         $bg_class = 'bg-gray-900 text-white';      break; // image underlay + overlay
    default:              $bg_class = 'bg-surface-default';
}

$layout_class = ($layout === 'split')
    ? 'grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center'
    : 'space-y-6 text-center';

$content_wrap_class = ($layout === 'split') ? '' : 'max-w-3xl mx-auto';

// Buttons – map to your semantic classes from cta.css
$primary_btn_class   = 'inline-flex bg-button-primary';
$secondary_btn_class = 'inline-flex border-button-primary';

/** ------------------------------------------------------------------------
 * Helpers
 * ---------------------------------------------------------------------- */
$build_href = function($type, $target) {
    $t = trim((string)$target);
    if ($type === 'phone') {
        $digits = preg_replace('/[^0-9+]/', '', $t);
        return $digits ? 'tel:' . $digits : '#';
    }
    if ($type === 'email') {
        return $t ? 'mailto:' . $t : '#';
    }
    // Link
    if (!$t) return '#';
    // Allow internal or external; trust esc_url later
    return $t;
};

// Accessibility labels
$primary_aria   = $headline ? ('Call to action: ' . $headline) : 'Primary call to action';
$secondary_aria = $headline ? ('Secondary action: ' . $headline) : 'Secondary call to action';
?>
<section id="<?php echo esc_attr($block_id); ?>"
         class="dclt-cta-block <?php echo esc_attr($spacing_class . ' ' . $bg_class); ?>">

  <?php if ($bg_style === 'image' && $bg_image_url): ?>
    <div class="absolute inset-0" aria-hidden="true">
      <img src="<?php echo esc_url($bg_image_url); ?>"
           alt=""
           class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-black/40"></div>
    </div>
  <?php endif; ?>

  <div class="<?php echo esc_attr($container_class); ?> relative z-10">
    <div class="<?php echo esc_attr($layout_class); ?>">

      <!-- Content -->
      <div class="cta-content <?php echo esc_attr($content_wrap_class); ?>">
        <?php if ($urgency === '1'): ?>
          <div class="urgency-badge">Time‑Sensitive</div>
        <?php endif; ?>

        <?php if ($icon_id): ?>
          <div class="mb-3">
            <?php echo wp_get_attachment_image((int)$icon_id, 'thumbnail', false, ['class' => 'w-12 h-12']); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($headline)): ?>
          <h2 class="cta-title">
            <?php echo esc_html($headline); ?>
          </h2>
        <?php endif; ?>

        <?php if (!empty($description)): ?>
          <p class="cta-desc">
            <?php echo wp_kses_post($description); ?>
          </p>
        <?php endif; ?>
      </div>

      <!-- Actions -->
      <div class="cta-actions <?php echo $layout === 'split' ? 'md:justify-self-end' : 'flex justify-center'; ?>">
        <div class="cta-buttons">
          <?php if (!empty($primary_text) && !empty($primary_target)): ?>
            <a class="<?php echo esc_attr($primary_btn_class); ?>"
               href="<?php echo esc_url($build_href($primary_type, $primary_target)); ?>"
               aria-label="<?php echo esc_attr($primary_aria); ?>">
              <?php echo esc_html($primary_text); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($secondary_text) && !empty($secondary_target)): ?>
            <a class="<?php echo esc_attr($secondary_btn_class); ?>"
               href="<?php echo esc_url($secondary_target); ?>"
               aria-label="<?php echo esc_attr($secondary_aria); ?>">
              <?php echo esc_html($secondary_text); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>