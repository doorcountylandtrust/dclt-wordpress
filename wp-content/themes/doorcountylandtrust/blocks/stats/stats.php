<?php
/**
 * Stats Block Template with Custom SVG Support
 * Location: blocks/stats/stats.php
 */

if (!defined('ABSPATH')) exit;

$post_id = get_the_ID();

// Content fields
$kicker = dclt_get_field($post_id, 'stats_kicker', '');
$title  = dclt_get_field($post_id, 'stats_title', '');
$desc   = dclt_get_field($post_id, 'stats_desc', '');

// Items (3) - Define $items first
$items = [];
for ($i=1; $i<=3; $i++) {
    $items[] = [
        'label'    => dclt_get_field($post_id, "stats_item{$i}_label", ''),
        'value'    => dclt_get_field($post_id, "stats_item{$i}_value", ''),
        'suffix'   => dclt_get_field($post_id, "stats_item{$i}_suffix", ''),
        'icon'     => dclt_get_field($post_id, "stats_item{$i}_icon", ''),
        'icon_svg' => dclt_get_field($post_id, "stats_item{$i}_icon_svg", ''),
    ];
}

// If no real data, use test data with icons
if (empty(array_filter($items, function($item) { return !empty($item['value']); }))) {
    $items = [
        [
            'value' => '2,847',
            'suffix' => 'acres',
            'label' => 'Protected Forever',
            'icon' => 'tree',
            'icon_svg' => ''
        ],
        [
            'value' => '127',
            'suffix' => '',
            'label' => 'Landowner Partners',
            'icon' => 'handshake',
            'icon_svg' => ''
        ],
        [
            'value' => '15',
            'suffix' => 'years',
            'label' => 'Protecting Door County',
            'icon' => 'landscape',
            'icon_svg' => ''
        ]
    ];
    
    if (empty($title)) $title = 'Our Conservation Impact';
    if (empty($kicker)) $kicker = 'Since 2009';
    if (empty($desc)) $desc = 'Working with landowners to protect Door County\'s most important natural areas.';
}

// Layout helpers
$container = dclt_get_container_class( dclt_get_field($post_id, 'stats_container', '', 'content') );
$spacing   = dclt_get_spacing_class( dclt_get_field($post_id, 'stats_spacing', '', 'medium') );

// Function to render SVG icons with custom SVG support
if (!function_exists('dclt_render_stat_icon')) {
    function dclt_render_stat_icon($icon_type, $custom_svg = '') {
        // If custom SVG provided, use it
        if (!empty($custom_svg)) {
            // Ensure the SVG has proper classes for styling
            if (strpos($custom_svg, 'class="') === false) {
                $custom_svg = str_replace('<svg', '<svg class="stat-svg custom-icon"', $custom_svg);
            }
            return $custom_svg;
        }
        
        // Otherwise use preset icons
        $icons = [
            'tree' => '<svg viewBox="0 0 100 100" class="stat-svg tree-icon">
                <rect class="tree-trunk animate-trunk" x="45" y="65" width="10" height="25" fill="currentColor" opacity="0.7" />
                <circle class="tree-leaves animate-leaves" cx="50" cy="45" r="20" fill="currentColor" />
            </svg>',
            
            'handshake' => '<svg viewBox="0 0 100 100" class="stat-svg handshake-icon">
                <path class="hand-left animate-hand-left" d="M20 50 Q35 35 45 50 L48 55 Q43 65 30 60 Q20 65 15 50 Z" fill="currentColor" />
                <path class="hand-right animate-hand-right" d="M52 45 Q57 35 72 45 Q80 50 75 60 Q65 70 52 65 L48 55 Q52 45 52 45 Z" fill="currentColor" opacity="0.8" />
                <circle class="connection animate-connection" cx="50" cy="52" r="4" fill="#fbbf24" />
            </svg>',
            
            'landscape' => '<svg viewBox="0 0 100 100" class="stat-svg landscape-icon">
                <polygon class="mountain animate-mountain" points="25,75 50,25 75,75" fill="currentColor" />
                <ellipse class="water animate-water" cx="50" cy="80" rx="25" ry="6" fill="currentColor" opacity="0.6" />
                <path class="tree-small animate-tree-small" d="M65 65 L67 65 L66 60 Z" fill="currentColor" opacity="0.8" />
            </svg>',
            
            'bird' => '<svg viewBox="0 0 100 100" class="stat-svg bird-icon">
                <path class="flight-path animate-path" d="M15 60 Q50 25 85 45" stroke="currentColor" stroke-width="3" fill="none" opacity="0.7" />
                <path class="bird-silhouette animate-bird" d="M80 40 L88 43 L86 47 L80 45 L82 49 L78 47 Z" fill="currentColor" />
            </svg>',
            
            'shield' => '<svg viewBox="0 0 100 100" class="stat-svg shield-icon">
                <path class="shield-shape animate-shield" d="M50 15 L70 25 L70 55 Q70 75 50 85 Q30 75 30 55 L30 25 Z" fill="currentColor" />
                <path class="checkmark animate-checkmark" d="M42 45 L48 52 L62 35" stroke="white" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            </svg>',
            
            'leaf' => '<svg viewBox="0 0 100 100" class="stat-svg leaf-icon">
                <path class="leaf-shape animate-leaf" d="M50 15 Q35 30 40 50 Q45 70 50 85 Q55 70 60 50 Q65 30 50 15 Z" fill="currentColor" />
                <path class="leaf-vein animate-vein" d="M50 25 Q52 45 50 65" stroke="white" stroke-width="2" opacity="0.7" />
            </svg>',
            
            'default' => '<svg viewBox="0 0 100 100" class="stat-svg default-icon">
                <circle class="pulse-circle animate-pulse" cx="50" cy="50" r="20" fill="currentColor" opacity="0.3" />
                <circle class="inner-circle animate-inner" cx="50" cy="50" r="12" fill="currentColor" />
            </svg>'
        ];
        
        return isset($icons[$icon_type]) ? $icons[$icon_type] : $icons['default'];
    }
}

// Colors: mode token -> class, mode custom -> inline style
if (!function_exists('dclt_stats_color_attrs')) {
    function dclt_stats_color_attrs($mode, $token, $custom_hex) {
        $out = ['class' => '', 'style' => ''];
        if ($mode === 'custom' && $custom_hex) {
            $out['style'] = 'color:' . esc_attr($custom_hex) . ';';
        } elseif (!empty($token)) {
            $out['class'] = esc_attr($token);
        }
        return $out;
    }
}

$headline_mode   = dclt_get_field($post_id, 'stats_headline_color_mode', '', 'token');
$headline_token  = dclt_get_field($post_id, 'stats_headline_token', '', 'text-neutral-900');
$headline_custom = dclt_get_field($post_id, 'stats_headline_custom', '', '');
$headline_color  = dclt_stats_color_attrs($headline_mode, $headline_token, $headline_custom);

$number_mode   = dclt_get_field($post_id, 'stats_number_color_mode', '', 'token');
$number_token  = dclt_get_field($post_id, 'stats_number_token', '', 'text-brand-700');
$number_custom = dclt_get_field($post_id, 'stats_number_custom', '', '');
$number_color  = dclt_stats_color_attrs($number_mode, $number_token, $number_custom);

$label_mode   = dclt_get_field($post_id, 'stats_label_color_mode', '', 'token');
$label_token  = dclt_get_field($post_id, 'stats_label_token', '', 'text-neutral-700');
$label_custom = dclt_get_field($post_id, 'stats_label_custom', '', '');
$label_color  = dclt_stats_color_attrs($label_mode, $label_token, $label_custom);
?>

<section class="dclt-stats-block bg-white <?php echo esc_attr($spacing); ?>" data-stats-animate>
  <div class="<?php echo esc_attr($container); ?>">

    <?php if (!empty($kicker)) : ?>
      <div class="stats-kicker text-center">
        <span class="inline-block bg-brand-50 text-brand-700 px-4 py-2 rounded-full text-sm font-semibold tracking-wide uppercase">
          <?php echo esc_html($kicker); ?>
        </span>
      </div>
    <?php endif; ?>

    <?php if (!empty($title)) : ?>
      <h2 class="text-center text-3xl md:text-4xl font-extrabold tracking-tight mb-3 <?php echo $headline_color['class']; ?>" style="<?php echo $headline_color['style']; ?>">
        <?php echo esc_html($title); ?>
      </h2>
    <?php endif; ?>

    <?php if (!empty($desc)) : ?>
      <p class="text-gray-600 max-w-3xl mx-auto text-center mb-8"><?php echo esc_html($desc); ?></p>
    <?php endif; ?>

    <div class="stats-grid grid grid-cols-1 sm:grid-cols-3 gap-6 md:gap-8">
      <?php foreach ($items as $index => $item) : ?>
        <?php if (!empty($item['value']) || !empty($item['label'])) : ?>
        <div class="stat-card rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 p-6 text-center" data-delay="<?php echo $index * 200; ?>">
          
          <!-- Animated Icon -->
          <div class="stat-icon-container mb-4">
            <?php echo dclt_render_stat_icon($item['icon'] ?? 'default', $item['icon_svg'] ?? ''); ?>
          </div>

          <!-- Number -->
          <?php if (!empty($item['value'])) : ?>
          <div class="stat-value text-5xl md:text-6xl font-extrabold <?php echo $number_color['class']; ?>"
               style="<?php echo $number_color['style']; ?>"
               aria-live="polite">
            <?php echo esc_html($item['value']); ?>
            <?php if (!empty($item['suffix'])) : ?>
              <span class="stat-suffix text-lg md:text-xl font-semibold align-middle <?php echo $number_color['class']; ?>" style="<?php echo $number_color['style']; ?>">
                <?php echo esc_html($item['suffix']); ?>
              </span>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Label -->
          <?php if (!empty($item['label'])) : ?>
            <div class="stat-label mt-2 text-base md:text-lg font-medium <?php echo $label_color['class']; ?>"
                 style="<?php echo $label_color['style']; ?>">
              <?php echo esc_html($item['label']); ?>
            </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <!-- Debug output (remove after testing) -->
    <?php if (current_user_can('manage_options')) : ?>
    <div style="background: #f0f0f0; padding: 1rem; margin-top: 2rem; font-size: 12px; border: 1px solid #ccc;">
      <strong>Debug Info (admin only):</strong><br>
      Post ID: <?php echo $post_id; ?><br>
      Title: "<?php echo esc_html($title); ?>"<br>
      Items: <?php echo count(array_filter($items, function($item) { return !empty($item['value']) || !empty($item['label']); })); ?> non-empty<br>
      Item 1 Value: "<?php echo esc_html($items[0]['value']); ?>"<br>
      Item 1 Label: "<?php echo esc_html($items[0]['label']); ?>"<br>
      Item 1 Icon: "<?php echo esc_html($items[0]['icon']); ?>"<br>
      Item 1 Custom SVG: <?php echo !empty($items[0]['icon_svg']) ? 'Yes (' . strlen($items[0]['icon_svg']) . ' chars)' : 'No'; ?>
    </div>
    <?php endif; ?>

  </div>
</section>