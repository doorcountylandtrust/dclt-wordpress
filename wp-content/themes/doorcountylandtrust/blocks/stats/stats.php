<?php
/**
 * DCLT Stats Block (MVP)
 * Server-rendered counters with accessible fallbacks.
 */
if (!defined('ABSPATH')) exit;

$post_id   = get_the_ID();
$block_id  = isset($dclt_attributes['blockId']) ? $dclt_attributes['blockId'] : 'stats-' . uniqid();

// Content
$title       = dclt_get_field($post_id, 'stats_title', $block_id, 'Our Impact');
$kicker      = dclt_get_field($post_id, 'stats_kicker', $block_id, '');
$desc        = dclt_get_field($post_id, 'stats_desc', $block_id, '');

// Items (three simple stats)
$items = [];
for ($i = 1; $i <= 3; $i++) {
  $label = dclt_get_field($post_id, "stats_{$i}_label", $block_id, $i === 1 ? 'Acres Preserved' : ($i === 2 ? 'Landowners Helped' : 'Preserves Established'));
  $value = dclt_get_field($post_id, "stats_{$i}_value", $block_id, $i === 1 ? '10000' : ($i === 2 ? '250' : '36'));
  $suffix = dclt_get_field($post_id, "stats_{$i}_suffix", $block_id, $i === 1 ? '' : '');
  $items[] = [
    'label'  => $label,
    'value'  => preg_replace('/[^0-9]/', '', (string)$value),
    'suffix' => $suffix,
  ];
}

// Presentation
$container = dclt_get_container_class('wide');           // reuse helper: content|wide|full
$spacing   = dclt_get_spacing_class('medium');           // compact|medium|spacious
?>
<section class="dclt-stats-block bg-white <?php echo esc_attr($spacing); ?>" data-dclt-stats>
  <div class="<?php echo esc_attr($container); ?>">
    <div class="stats-head text-center max-w-3xl mx-auto mb-10">
      <?php if ($kicker) : ?><p class="kicker text-brand-700 font-semibold tracking-wide mb-2"><?php echo esc_html($kicker); ?></p><?php endif; ?>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900"><?php echo esc_html($title); ?></h2>
      <?php if ($desc) : ?><p class="mt-3 text-gray-600 text-lg"><?php echo esc_html($desc); ?></p><?php endif; ?>
    </div>

    <div class="stats-grid">
      <?php foreach ($items as $i => $it) : ?>
        <div class="stat">
          <div class="stat-number" aria-live="polite">
            <span
              class="js-count"
              data-target="<?php echo esc_attr($it['value']); ?>"
              data-duration="1600"
              ><?php echo number_format_i18n((int)$it['value']); ?></span><?php if ($it['suffix']) echo ' ' . esc_html($it['suffix']); ?>
          </div>
          <div class="stat-label"><?php echo esc_html($it['label']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>