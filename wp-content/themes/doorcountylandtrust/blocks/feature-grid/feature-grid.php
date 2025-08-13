<?php
/**
 * Feature Grid Block Template
 * File: blocks/feature-grid/feature-grid.php
 */

// Get field values
$section_title = get_field('section_title');
$section_subtitle = get_field('section_subtitle');
$grid_columns = get_field('grid_columns') ?: '3';
$features = get_field('features');
$background_color = get_field('background_color');
$container_width = get_field('container_width') ?: 'content';
$spacing = get_field('spacing') ?: 'medium';

if (!$features) return;

// Build classes
$section_classes = ['dclt-feature-grid-block', 'dclt-block', dclt_get_spacing_class($spacing)];
if ($background_color && $background_color !== '#ffffff') {
    $section_classes[] = 'text-white';
}

$grid_classes = [
    'grid', 
    'gap-8', 
    'md:gap-12',
    "md:grid-cols-{$grid_columns}"
];
?>

<section class="<?php echo implode(' ', $section_classes); ?>" 
         <?php if ($background_color): ?>style="background-color: <?php echo esc_attr($background_color); ?>"<?php endif; ?>>
    
    <div class="<?php echo dclt_get_container_class($container_width); ?>">
        
        <!-- Section Header -->
        <?php if ($section_title || $section_subtitle): ?>
            <div class="text-center mb-12 lg:mb-16">
                <?php if ($section_title): ?>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                        <?php echo wp_kses_post($section_title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ($section_subtitle): ?>
                    <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                        <?php echo wp_kses_post($section_subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Feature Grid -->
        <div class="<?php echo implode(' ', $grid_classes); ?>">
            <?php foreach ($features as $index => $feature): ?>
                <div class="feature-item text-center group" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    
                    <!-- Icon/Image -->
                    <?php if ($feature['icon']): ?>
                        <div class="mb-6 flex justify-center">
                            <?php if ($feature['icon_style'] === 'number'): ?>
                                <div class="w-16 h-16 rounded-full bg-brand text-white flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform duration-200">
                                    <?php echo esc_html($index + 1); ?>
                                </div>
                            <?php else: ?>
                                <div class="w-16 h-16 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <img src="<?php echo esc_url($feature['icon']['sizes']['thumbnail'] ?? $feature['icon']['url']); ?>" 
                                         alt="<?php echo esc_attr($feature['icon']['alt'] ?? ''); ?>"
                                         class="w-full h-full object-contain">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Title -->
                    <?php if ($feature['title']): ?>
                        <h3 class="text-xl md:text-2xl font-bold mb-4">
                            <?php echo wp_kses_post($feature['title']); ?>
                        </h3>
                    <?php endif; ?>

                    <!-- Description -->
                    <?php if ($feature['description']): ?>
                        <p class="text-lg opacity-90 mb-6 leading-relaxed">
                            <?php echo wp_kses_post($feature['description']); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Link -->
                    <?php if ($feature['link_url'] && $feature['link_text']): ?>
                        <a href="<?php echo esc_url($feature['link_url']); ?>" 
                           class="inline-flex items-center text-brand font-semibold hover:text-brand-700 transition-colors duration-200">
                            <?php echo esc_html($feature['link_text']); ?>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php
/**
 * Stats Block Template  
 * File: blocks/stats/stats.php
 */

// Get field values
$layout = get_field('layout') ?: 'grid';
$background_type = get_field('background_type') ?: 'transparent';
$background_image = get_field('background_image');
$stats = get_field('stats');
$counter_animation = get_field('counter_animation');
$section_title = get_field('section_title');
$container_width = get_field('container_width') ?: 'content';
$spacing = get_field('spacing') ?: 'medium';

if (!$stats) return;

// Build classes
$section_classes = ['dclt-stats-block', 'dclt-block', dclt_get_spacing_class($spacing), 'relative'];

switch ($background_type) {
    case 'brand':
        $section_classes[] = 'bg-brand text-white';
        break;
    case 'image':
        $section_classes[] = 'text-white';
        break;
    case 'light':
        $section_classes[] = 'bg-gray-50';
        break;
}

// Grid layout classes
if ($layout === 'horizontal') {
    $grid_classes = 'flex flex-wrap justify-center gap-8 md:gap-16';
} elseif ($layout === 'featured-large') {
    $grid_classes = 'grid md:grid-cols-2 lg:grid-cols-4 gap-8 text-center';
} else {
    $grid_classes = 'grid grid-cols-2 lg:grid-cols-4 gap-8 text-center';
}
?>

<section class="<?php echo implode(' ', $section_classes); ?>">
    
    <!-- Background Image -->
    <?php if ($background_type === 'image' && $background_image): ?>
        <div class="absolute inset-0">
            <img src="<?php echo esc_url($background_image['sizes']['large'] ?? $background_image['url']); ?>" 
                 alt="<?php echo esc_attr($background_image['alt'] ?? ''); ?>"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/60"></div>
        </div>
    <?php endif; ?>

    <div class="<?php echo dclt_get_container_class($container_width); ?> relative z-10">
        
        <!-- Section Title -->
        <?php if ($section_title): ?>
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold">
                    <?php echo wp_kses_post($section_title); ?>
                </h2>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="<?php echo $grid_classes; ?>">
            <?php foreach ($stats as $index => $stat): ?>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    
                    <!-- Icon -->
                    <?php if ($stat['icon'] && $stat['icon'] !== 'custom'): ?>
                        <div class="mb-4 flex justify-center">
                            <?php echo dclt_get_stat_icon($stat['icon']); ?>
                        </div>
                    <?php elseif ($stat['custom_icon']): ?>
                        <div class="mb-4 flex justify-center">
                            <img src="<?php echo esc_url($stat['custom_icon']['sizes']['thumbnail'] ?? $stat['custom_icon']['url']); ?>" 
                                 alt="<?php echo esc_attr($stat['custom_icon']['alt'] ?? ''); ?>"
                                 class="w-12 h-12 object-contain">
                        </div>
                    <?php endif; ?>

                    <!-- Number -->
                    <?php if ($stat['number']): ?>
                        <div class="<?php echo $stat['emphasis'] ? 'text-5xl md:text-6xl' : 'text-4xl md:text-5xl'; ?> font-bold mb-2 <?php echo $counter_animation ? 'counter-number' : ''; ?>"
                             <?php if ($counter_animation): ?>data-target="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $stat['number'])); ?>"<?php endif; ?>>
                            <?php echo $counter_animation ? '0' : wp_kses_post($stat['number']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Label -->
                    <?php if ($stat['label']): ?>
                        <div class="text-lg md:text-xl font-semibold opacity-90">
                            <?php echo wp_kses_post($stat['label']); ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php
/**
 * Helper function to get stat icons
 */
function dclt_get_stat_icon($icon_type) {
    $icons = [
        'acres' => '<svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>',
        'species' => '<svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                      </svg>',
        'volunteers' => '<svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                           <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                         </svg>',
        'donations' => '<svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>',
    ];
    
    return isset($icons[$icon_type]) ? $icons[$icon_type] : '';
}

/**
 * ACF Fields for Feature Grid Block
 */
if (function_exists('acf_add_local_field_group')) {
    
    acf_add_local_field_group([
        'key' => 'group_dclt_feature_grid_block',
        'title' => 'DCLT Feature Grid Block Settings',
        'fields' => [
            
            // Header Tab
            [
                'key' => 'field_feature_header_tab',
                'label' => 'Section Header',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_feature_section_title',
                'label' => 'Section Title',
                'name' => 'section_title',
                'type' => 'text',
                'instructions' => 'Optional title for the entire feature section.',
            ],
            
            [
                'key' => 'field_feature_section_subtitle',
                'label' => 'Section Subtitle',
                'name' => 'section_subtitle',
                'type' => 'textarea',
                'instructions' => 'Optional subtitle or description for the section.',
                'rows' => 2,
            ],
            
            // Layout Tab
            [
                'key' => 'field_feature_layout_tab',
                'label' => 'Layout Settings',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_feature_grid_columns',
                'label' => 'Grid Columns',
                'name' => 'grid_columns',
                'type' => 'select',
                'choices' => [
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
                'default_value' => '3',
                'required' => 1,
            ],
            
            [
                'key' => 'field_feature_background_color',
                'label' => 'Background Color',
                'name' => 'background_color',
                'type' => 'color_picker',
                'instructions' => 'Optional background color for the section.',
                'default_value' => '',
            ],
            
            [
                'key' => 'field_feature_container_width',
                'label' => 'Container Width',
                'name' => 'container_width',
                'type' => 'select',
                'choices' => [
                    'narrow' => 'Narrow',
                    'content' => 'Content',
                    'wide' => 'Wide',
                ],
                'default_value' => 'content',
            ],
            
            [
                'key' => 'field_feature_spacing',
                'label' => 'Section Spacing',
                'name' => 'spacing',
                'type' => 'select',
                'choices' => [
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                ],
                'default_value' => 'medium',
            ],
            
            // Features Tab
            [
                'key' => 'field_feature_content_tab',
                'label' => 'Features',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_features',
                'label' => 'Features',
                'name' => 'features',
                'type' => 'repeater',
                'instructions' => 'Add your features, services, or benefits.',
                'required' => 1,
                'min' => 1,
                'max' => 12,
                'layout' => 'block',
                'button_label' => 'Add Feature',
                'sub_fields' => [
                    [
                        'key' => 'field_feature_icon',
                        'label' => 'Icon',
                        'name' => 'icon',
                        'type' => 'image',
                        'instructions' => 'Icon or image for this feature.',
                        'return_format' => 'array',
                    ],
                    [
                        'key' => 'field_feature_icon_style',
                        'label' => 'Icon Style',
                        'name' => 'icon_style',
                        'type' => 'select',
                        'choices' => [
                            'image' => 'Custom Image/Icon',
                            'number' => 'Step Number (for processes)',
                        ],
                        'default_value' => 'image',
                    ],
                    [
                        'key' => 'field_feature_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'required' => 1,
                        'maxlength' => 50,
                    ],
                    [
                        'key' => 'field_feature_description',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                        'required' => 1,
                        'rows' => 3,
                        'maxlength' => 200,
                    ],
                    [
                        'key' => 'field_feature_link_url',
                        'label' => 'Link URL',
                        'name' => 'link_url',
                        'type' => 'url',
                        'instructions' => 'Optional link for this feature.',
                    ],
                    [
                        'key' => 'field_feature_link_text',
                        'label' => 'Link Text',
                        'name' => 'link_text',
                        'type' => 'text',
                        'instructions' => 'Text for the link (e.g., "Learn More").',
                        'maxlength' => 20,
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/dclt-feature-grid',
                ],
            ],
        ],
    ]);

    /**
     * ACF Fields for Stats Block
     */
    acf_add_local_field_group([
        'key' => 'group_dclt_stats_block',
        'title' => 'DCLT Stats Block Settings',
        'fields' => [
            
            // Layout Tab
            [
                'key' => 'field_stats_layout_tab',
                'label' => 'Layout & Style',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_stats_section_title',
                'label' => 'Section Title',
                'name' => 'section_title',
                'type' => 'text',
                'instructions' => 'Optional title above the statistics.',
            ],
            
            [
                'key' => 'field_stats_layout',
                'label' => 'Layout Style',
                'name' => 'layout',
                'type' => 'select',
                'choices' => [
                    'grid' => 'Grid - Even columns',
                    'horizontal' => 'Horizontal - Inline row',
                    'featured-large' => 'Featured - One large stat highlighted',
                ],
                'default_value' => 'grid',
                'required' => 1,
            ],
            
            [
                'key' => 'field_stats_background_type',
                'label' => 'Background Type',
                'name' => 'background_type',
                'type' => 'select',
                'choices' => [
                    'transparent' => 'Transparent',
                    'brand' => 'Brand Green',
                    'light' => 'Light Gray',
                    'image' => 'Background Image',
                ],
                'default_value' => 'transparent',
                'required' => 1,
            ],
            
            [
                'key' => 'field_stats_background_image',
                'label' => 'Background Image',
                'name' => 'background_image',
                'type' => 'image',
                'instructions' => 'Background image for the stats section.',
                'return_format' => 'array',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_stats_background_type',
                            'operator' => '==',
                            'value' => 'image',
                        ],
                    ],
                ],
            ],
            
            [
                'key' => 'field_stats_counter_animation',
                'label' => 'Counter Animation',
                'name' => 'counter_animation',
                'type' => 'true_false',
                'instructions' => 'Animate numbers counting up when they come into view.',
                'default_value' => 1,
                'ui' => 1,
            ],
            
            [
                'key' => 'field_stats_container_width',
                'label' => 'Container Width',
                'name' => 'container_width',
                'type' => 'select',
                'choices' => [
                    'narrow' => 'Narrow',
                    'content' => 'Content',
                    'wide' => 'Wide',
                ],
                'default_value' => 'content',
            ],
            
            [
                'key' => 'field_stats_spacing',
                'label' => 'Section Spacing',
                'name' => 'spacing',
                'type' => 'select',
                'choices' => [
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                ],
                'default_value' => 'medium',
            ],
            
            // Stats Tab
            [
                'key' => 'field_stats_content_tab',
                'label' => 'Statistics',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_stats',
                'label' => 'Statistics',
                'name' => 'stats',
                'type' => 'repeater',
                'instructions' => 'Add your impact statistics and key numbers.',
                'required' => 1,
                'min' => 1,
                'max' => 6,
                'layout' => 'block',
                'button_label' => 'Add Statistic',
                'sub_fields' => [
                    [
                        'key' => 'field_stat_number',
                        'label' => 'Number',
                        'name' => 'number',
                        'type' => 'text',
                        'instructions' => 'The statistic number (e.g., "2,847", "95%", "24").',
                        'required' => 1,
                        'maxlength' => 20,
                    ],
                    [
                        'key' => 'field_stat_label',
                        'label' => 'Label',
                        'name' => 'label',
                        'type' => 'text',
                        'instructions' => 'Description of what the number represents.',
                        'required' => 1,
                        'maxlength' => 50,
                    ],
                    [
                        'key' => 'field_stat_icon',
                        'label' => 'Icon',
                        'name' => 'icon',
                        'type' => 'select',
                        'choices' => [
                            'acres' => 'Acres/Land',
                            'species' => 'Species/Wildlife',
                            'volunteers' => 'Volunteers/People',
                            'donations' => 'Donations/Support',
                            'custom' => 'Custom Icon',
                        ],
                        'default_value' => 'acres',
                    ],
                    [
                        'key' => 'field_stat_custom_icon',
                        'label' => 'Custom Icon',
                        'name' => 'custom_icon',
                        'type' => 'image',
                        'instructions' => 'Upload a custom icon for this statistic.',
                        'return_format' => 'array',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_stat_icon',
                                    'operator' => '==',
                                    'value' => 'custom',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_stat_emphasis',
                        'label' => 'Emphasize This Stat',
                        'name' => 'emphasis',
                        'type' => 'true_false',
                        'instructions' => 'Make this statistic larger and more prominent.',
                        'default_value' => 0,
                        'ui' => 1,
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/dclt-stats',
                ],
            ],
        ],
    ]);
}
?>

<script>
/**
 * Stats Block JavaScript
 * File: blocks/stats/stats.js
 */
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation for stats
    const counters = document.querySelectorAll('.counter-number');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.dataset.target);
        const duration = 2000; // 2 seconds
        const step = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    };
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                animateCounter(entry.target);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
});
</script>