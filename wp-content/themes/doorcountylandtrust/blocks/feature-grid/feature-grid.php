<?php
/**
 * Feature Grid Block Template
 * Location: blocks/feature-grid/feature-grid.php
 * Rewritten to match stats block structure exactly
 */

if (!defined('ABSPATH')) exit;

$post_id = get_the_ID();

// Content fields
$title = dclt_get_field($post_id, 'feature_grid_title', '');
$subtitle = dclt_get_field($post_id, 'feature_grid_subtitle', '');

// Layout options
$columns = dclt_get_field($post_id, 'feature_grid_columns', '', '3');
$container = dclt_get_container_class(dclt_get_field($post_id, 'feature_grid_container', '', 'content'));
$spacing = dclt_get_spacing_class(dclt_get_field($post_id, 'feature_grid_spacing', '', 'medium'));

// Get feature items (2-6 items)
$items = [];
for ($i = 1; $i <= 6; $i++) {
    $heading = dclt_get_field($post_id, "feature_grid_item{$i}_heading", '');
    $description = dclt_get_field($post_id, "feature_grid_item{$i}_description", '');
    $cta_text = dclt_get_field($post_id, "feature_grid_item{$i}_cta_text", '');
    $cta_url = dclt_get_field($post_id, "feature_grid_item{$i}_cta_url", '');
    $icon = dclt_get_field($post_id, "feature_grid_item{$i}_icon", '', 'default');

    // Only add if heading or description exists
    if (!empty($heading) || !empty($description)) {
        $items[] = [
            'heading' => $heading,
            'description' => $description,
            'cta_text' => $cta_text,
            'cta_url' => $cta_url,
            'icon' => $icon
        ];
    }
}

// Use sample data if no items exist
if (empty($items)) {
    $items = [
        [
            'heading' => 'Land Protection',
            'description' => 'Working with landowners to permanently protect important natural areas through conservation easements.',
            'cta_text' => 'Learn More',
            'cta_url' => '/protect-your-land',
            'icon' => 'shield'
        ],
        [
            'heading' => 'Restoration',
            'description' => 'Restoring native habitats and removing invasive species to enhance ecological health.',
            'cta_text' => 'Get Involved',
            'cta_url' => '/restoration',
            'icon' => 'leaf'
        ],
        [
            'heading' => 'Education',
            'description' => 'Teaching community members about local ecology and conservation practices.',
            'cta_text' => 'Explore Programs',
            'cta_url' => '/education',
            'icon' => 'bird'
        ]
    ];

    if (empty($title)) $title = 'Our Conservation Work';
    if (empty($subtitle)) $subtitle = 'Protecting Door County\'s natural heritage for future generations';
}

// Function to render SVG icons - EXACT copy from stats block
if (!function_exists('dclt_render_feature_icon')) {
    function dclt_render_feature_icon($icon_type, $custom_svg = '') {
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

// Grid class mapping
$grid_classes = [
    '2' => 'grid-cols-1 md:grid-cols-2',
    '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
];
$grid_class = isset($grid_classes[$columns]) ? $grid_classes[$columns] : $grid_classes['3'];
?>

<section class="dclt-feature-grid-block bg-white <?php echo esc_attr($spacing); ?>">
    <div class="<?php echo esc_attr($container); ?>">

        <?php if (!empty($title)) : ?>
            <div class="text-center mb-8">
                <h2 class="text-3xl md:text-4xl font-extrabold text-neutral-900 tracking-tight mb-4">
                    <?php echo esc_html($title); ?>
                </h2>
                <?php if (!empty($subtitle)) : ?>
                    <p class="text-lg text-neutral-700 max-w-3xl mx-auto">
                        <?php echo esc_html($subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="feature-grid grid <?php echo esc_attr($grid_class); ?> gap-6 lg:gap-8">
            <?php foreach ($items as $index => $item) : ?>
                <div class="feature-card group bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 text-center transition-all duration-300 hover:shadow-md hover:ring-gray-300">

                    <!-- Icon - EXACT structure from stats block -->
                    <div class="stat-icon-container mb-4">
                        <?php echo dclt_render_feature_icon($item['icon'] ?? 'default', ''); ?>
                    </div>

                    <!-- Content -->
                    <div class="feature-content">
                        <?php if (!empty($item['heading'])) : ?>
                            <h3 class="text-xl font-bold text-neutral-900 mb-3 group-hover:text-brand-700 transition-colors duration-200">
                                <?php echo esc_html($item['heading']); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if (!empty($item['description'])) : ?>
                            <p class="text-neutral-700 mb-4 leading-relaxed">
                                <?php echo esc_html($item['description']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- CTA Link - simplified -->
                        <?php if (!empty($item['cta_text']) && !empty($item['cta_url'])) : ?>
                            <a href="<?php echo esc_url($item['cta_url']); ?>"
                               class="inline-block text-brand-700 font-semibold hover:text-brand-800 transition-colors duration-200">
                                <?php echo esc_html($item['cta_text']); ?> →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Debug output (admin only) -->
        <?php if (current_user_can('manage_options')) : ?>
        <div style="background: #f0f0f0; padding: 1rem; margin-top: 2rem; font-size: 12px; border: 1px solid #ccc;">
            <strong>Debug Info (admin only):</strong><br>
            Post ID: <?php echo $post_id; ?><br>
            Title: "<?php echo esc_html($title); ?>"<br>
            Columns: <?php echo esc_html($columns); ?><br>
            Items: <?php echo count($items); ?><br>
            Container: <?php echo esc_html(dclt_get_field($post_id, 'feature_grid_container', '', 'content')); ?><br>
            Spacing: <?php echo esc_html(dclt_get_field($post_id, 'feature_grid_spacing', '', 'medium')); ?><br>
            <strong>Item 1 Debug:</strong><br>
            - Heading: "<?php echo esc_html($items[0]['heading'] ?? 'none'); ?>"<br>
            - Icon: "<?php echo esc_html($items[0]['icon'] ?? 'none'); ?>"<br>
            - CTA Text: "<?php echo esc_html($items[0]['cta_text'] ?? 'none'); ?>"<br>
            - CTA URL: "<?php echo esc_html($items[0]['cta_url'] ?? 'none'); ?>"<br>
            <strong>Raw Meta Fields:</strong><br>
            - dclt_feature_grid_item1_heading: "<?php echo esc_html(get_post_meta($post_id, 'dclt_feature_grid_item1_heading', true)); ?>"<br>
            - dclt_feature_grid_item1_icon: "<?php echo esc_html(get_post_meta($post_id, 'dclt_feature_grid_item1_icon', true)); ?>"<br>
        </div>
        <?php endif; ?>

    </div>
</section>