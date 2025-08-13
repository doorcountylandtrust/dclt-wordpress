<?php
/**
 * CTA Block Template
 * File: blocks/cta/cta.php
 */

// Get field values
$layout_style = get_field('layout_style') ?: 'centered';
$background_style = get_field('background_style') ?: 'brand-green';
$background_image = get_field('background_image');
$icon = get_field('icon');
$headline = get_field('headline');
$description = get_field('description');
$primary_action = get_field('primary_action');
$secondary_action = get_field('secondary_action');
$urgency_indicator = get_field('urgency_indicator');
$container_width = get_field('container_width') ?: 'content';
$spacing = get_field('spacing') ?: 'medium';

// Build section classes
$section_classes = ['dclt-cta-block', 'dclt-block', dclt_get_spacing_class($spacing)];

// Background styling
switch ($background_style) {
    case 'brand-green':
        $section_classes[] = 'bg-brand text-white';
        break;
    case 'light-green':
        $section_classes[] = 'bg-primary-50 text-primary-900';
        break;
    case 'white':
        $section_classes[] = 'bg-white text-gray-900 border border-gray-200';
        break;
    case 'image':
        $section_classes[] = 'bg-gray-900 text-white relative';
        break;
}

// Layout classes
$container_classes = [dclt_get_container_class($container_width)];
$content_classes = [];

if ($layout_style === 'centered') {
    $content_classes[] = 'text-center max-w-3xl mx-auto';
} elseif ($layout_style === 'split') {
    $content_classes[] = 'grid md:grid-cols-2 gap-8 items-center';
} elseif ($layout_style === 'card-grid') {
    $content_classes[] = 'grid md:grid-cols-2 lg:grid-cols-3 gap-6';
}
?>

<section class="<?php echo implode(' ', $section_classes); ?>">
    
    <!-- Background Image -->
    <?php if ($background_style === 'image' && $background_image): ?>
        <div class="absolute inset-0">
            <img src="<?php echo esc_url($background_image['sizes']['large'] ?? $background_image['url']); ?>" 
                 alt="<?php echo esc_attr($background_image['alt'] ?? ''); ?>"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
    <?php endif; ?>

    <div class="<?php echo implode(' ', $container_classes); ?>">
        
        <!-- Urgency Indicator -->
        <?php if ($urgency_indicator): ?>
            <div class="text-center mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 animate-pulse">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Time-Sensitive Opportunity
                </span>
            </div>
        <?php endif; ?>

        <div class="<?php echo implode(' ', $content_classes); ?> relative z-10">
            
            <?php if ($layout_style === 'split'): ?>
                <!-- Split Layout -->
                <div class="space-y-4">
                    <?php if ($icon): ?>
                        <div class="flex-shrink-0">
                            <img src="<?php echo esc_url($icon['sizes']['thumbnail'] ?? $icon['url']); ?>" 
                                 alt="<?php echo esc_attr($icon['alt'] ?? ''); ?>"
                                 class="w-16 h-16 object-contain">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($headline): ?>
                        <h2 class="text-3xl md:text-4xl font-bold">
                            <?php echo wp_kses_post($headline); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <p class="text-lg opacity-90">
                            <?php echo wp_kses_post($description); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Actions for Split Layout -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center sm:justify-start">
                    <?php echo dclt_render_cta_actions($primary_action, $secondary_action, $background_style); ?>
                </div>

            <?php else: ?>
                <!-- Centered or Card Grid Layout -->
                <?php if ($icon): ?>
                    <div class="mb-6">
                        <img src="<?php echo esc_url($icon['sizes']['medium'] ?? $icon['url']); ?>" 
                             alt="<?php echo esc_attr($icon['alt'] ?? ''); ?>"
                             class="w-20 h-20 object-contain mx-auto">
                    </div>
                <?php endif; ?>
                
                <?php if ($headline): ?>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                        <?php echo wp_kses_post($headline); ?>
                    </h2>
                <?php endif; ?>

                <?php if ($description): ?>
                    <p class="text-lg md:text-xl mb-8 opacity-90 leading-relaxed">
                        <?php echo wp_kses_post($description); ?>
                    </p>
                <?php endif; ?>

                <!-- Actions for Centered Layout -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php echo dclt_render_cta_actions($primary_action, $secondary_action, $background_style); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php
/**
 * Helper function to render CTA actions
 */
function dclt_render_cta_actions($primary_action, $secondary_action, $background_style) {
    ob_start();
    
    if ($primary_action && $primary_action['text']):
        $button_class = dclt_get_cta_button_class($primary_action['style'] ?? 'primary', $background_style, true);
        
        if ($primary_action['type'] === 'form' && $primary_action['salesforce_form_id']):
            // Form trigger button
            ?>
            <button type="button" 
                    class="<?php echo $button_class; ?> dclt-form-trigger" 
                    data-form-id="<?php echo esc_attr($primary_action['salesforce_form_id']); ?>"
                    data-form-type="<?php echo esc_attr($primary_action['form_type'] ?? 'general'); ?>">
                <?php echo esc_html($primary_action['text']); ?>
            </button>
            <?php
        elseif ($primary_action['type'] === 'phone'):
            ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $primary_action['url'])); ?>" 
               class="<?php echo $button_class; ?>">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <?php echo esc_html($primary_action['text']); ?>
            </a>
            <?php
        elseif ($primary_action['type'] === 'email'):
            ?>
            <a href="mailto:<?php echo esc_attr($primary_action['url']); ?>" 
               class="<?php echo $button_class; ?>">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <?php echo esc_html($primary_action['text']); ?>
            </a>
            <?php
        else:
            // Regular button link
            ?>
            <a href="<?php echo esc_url($primary_action['url']); ?>" 
               class="<?php echo $button_class; ?>">
                <?php echo esc_html($primary_action['text']); ?>
            </a>
            <?php
        endif;
    endif;

    if ($secondary_action && $secondary_action['text'] && $secondary_action['url']):
        $secondary_class = dclt_get_cta_button_class($secondary_action['style'] ?? 'secondary', $background_style, false);
        ?>
        <a href="<?php echo esc_url($secondary_action['url']); ?>" 
           class="<?php echo $secondary_class; ?>">
            <?php echo esc_html($secondary_action['text']); ?>
        </a>
        <?php
    endif;

    return ob_get_clean();
}

/**
 * Helper function to get appropriate button classes based on CTA background
 */
function dclt_get_cta_button_class($style, $background_style, $is_primary) {
    $base_classes = 'inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-lg transition-all duration-200 hover:transform hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Adjust button style based on background
    if ($background_style === 'brand-green' || $background_style === 'image') {
        // On dark backgrounds
        if ($style === 'primary' || ($is_primary && $style === 'secondary')) {
            return $base_classes . ' bg-white text-brand hover:bg-gray-100 focus:ring-white';
        } else {
            return $base_classes . ' border-2 border-white text-white hover:bg-white hover:text-brand focus:ring-white';
        }
    } elseif ($background_style === 'light-green') {
        // On light green background
        if ($style === 'primary') {
            return $base_classes . ' bg-primary-700 text-white hover:bg-primary-800 focus:ring-primary-500';
        } else {
            return $base_classes . ' border-2 border-primary-700 text-primary-700 hover:bg-primary-700 hover:text-white focus:ring-primary-500';
        }
    } else {
        // On white background
        if ($style === 'primary') {
            return $base_classes . ' bg-brand text-white hover:bg-brand-700 focus:ring-brand';
        } else {
            return $base_classes . ' border-2 border-brand text-brand hover:bg-brand hover:text-white focus:ring-brand';
        }
    }
}

/**
 * ACF Fields for CTA Block
 */
if (function_exists('acf_add_local_field_group')) {
    
    acf_add_local_field_group([
        'key' => 'group_dclt_cta_block',
        'title' => 'DCLT CTA Block Settings',
        'fields' => [
            
            // Layout Tab
            [
                'key' => 'field_cta_layout_tab',
                'label' => 'Layout & Style',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_cta_layout_style',
                'label' => 'Layout Style',
                'name' => 'layout_style',
                'type' => 'select',
                'choices' => [
                    'centered' => 'Centered - Content in center with icon above',
                    'split' => 'Split - Content left, actions right',
                    'card-grid' => 'Card Grid - Multiple CTA cards',
                ],
                'default_value' => 'centered',
                'required' => 1,
            ],
            
            [
                'key' => 'field_cta_background_style',
                'label' => 'Background Style',
                'name' => 'background_style',
                'type' => 'select',
                'choices' => [
                    'brand-green' => 'Brand Green - Main green color',
                    'light-green' => 'Light Green - Subtle green background',
                    'white' => 'White - Clean white background',
                    'image' => 'Image Background - Custom image',
                ],
                'default_value' => 'brand-green',
                'required' => 1,
            ],
            
            [
                'key' => 'field_cta_background_image',
                'label' => 'Background Image',
                'name' => 'background_image',
                'type' => 'image',
                'instructions' => 'Background image for the CTA section. A dark overlay will be applied.',
                'return_format' => 'array',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_cta_background_style',
                            'operator' => '==',
                            'value' => 'image',
                        ],
                    ],
                ],
            ],
            
            [
                'key' => 'field_cta_container_width',
                'label' => 'Container Width',
                'name' => 'container_width',
                'type' => 'select',
                'choices' => [
                    'narrow' => 'Narrow (768px)',
                    'content' => 'Content (1200px)',
                    'wide' => 'Wide (1400px)',
                ],
                'default_value' => 'content',
            ],
            
            [
                'key' => 'field_cta_spacing',
                'label' => 'Section Spacing',
                'name' => 'spacing',
                'type' => 'select',
                'choices' => [
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                    'xlarge' => 'Extra Large',
                ],
                'default_value' => 'medium',
            ],
            
            // Content Tab
            [
                'key' => 'field_cta_content_tab',
                'label' => 'Content',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_cta_icon',
                'label' => 'Icon',
                'name' => 'icon',
                'type' => 'image',
                'instructions' => 'Optional icon to display above the headline.',
                'return_format' => 'array',
            ],
            
            [
                'key' => 'field_cta_headline',
                'label' => 'Headline',
                'name' => 'headline',
                'type' => 'text',
                'instructions' => 'Main CTA headline. Keep it action-oriented and compelling.',
                'required' => 1,
                'maxlength' => 80,
            ],
            
            [
                'key' => 'field_cta_description',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'textarea',
                'instructions' => 'Supporting text that explains the value or urgency.',
                'rows' => 3,
                'maxlength' => 200,
            ],
            
            [
                'key' => 'field_cta_urgency_indicator',
                'label' => 'Show Urgency Indicator',
                'name' => 'urgency_indicator',
                'type' => 'true_false',
                'instructions' => 'Add a "Time-Sensitive" badge above the content.',
                'default_value' => 0,
                'ui' => 1,
            ],
            
            // Actions Tab
            [
                'key' => 'field_cta_actions_tab',
                'label' => 'Actions',
                'name' => '',
                'type' => 'tab',
            ],
            
            [
                'key' => 'field_cta_primary_action',
                'label' => 'Primary Action',
                'name' => 'primary_action',
                'type' => 'group',
                'required' => 1,
                'sub_fields' => [
                    [
                        'key' => 'field_cta_primary_text',
                        'label' => 'Button Text',
                        'name' => 'text',
                        'type' => 'text',
                        'required' => 1,
                        'maxlength' => 25,
                    ],
                    [
                        'key' => 'field_cta_primary_type',
                        'label' => 'Action Type',
                        'name' => 'type',
                        'type' => 'select',
                        'choices' => [
                            'button' => 'Link Button - Goes to a page',
                            'form' => 'Form - Opens contact form',
                            'phone' => 'Phone - Click to call',
                            'email' => 'Email - Click to email',
                        ],
                        'default_value' => 'button',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_cta_primary_url',
                        'label' => 'URL/Phone/Email',
                        'name' => 'url',
                        'type' => 'text',
                        'instructions' => 'URL for links, phone number for calls, email for email actions.',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_cta_primary_salesforce_form_id',
                        'label' => 'Salesforce Form ID',
                        'name' => 'salesforce_form_id',
                        'type' => 'text',
                        'instructions' => 'Form identifier for Salesforce integration.',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_cta_primary_type',
                                    'operator' => '==',
                                    'value' => 'form',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_cta_primary_style',
                        'label' => 'Button Style',
                        'name' => 'style',
                        'type' => 'select',
                        'choices' => [
                            'primary' => 'Primary',
                            'secondary' => 'Secondary',
                            'landowner' => 'Landowner Focus',
                        ],
                        'default_value' => 'primary',
                    ],
                ],
            ],
            
            [
                'key' => 'field_cta_secondary_action',
                'label' => 'Secondary Action (Optional)',
                'name' => 'secondary_action',
                'type' => 'group',
                'sub_fields' => [
                    [
                        'key' => 'field_cta_secondary_text',
                        'label' => 'Button Text',
                        'name' => 'text',
                        'type' => 'text',
                        'maxlength' => 25,
                    ],
                    [
                        'key' => 'field_cta_secondary_url',
                        'label' => 'Button URL',
                        'name' => 'url',
                        'type' => 'url',
                    ],
                    [
                        'key' => 'field_cta_secondary_style',
                        'label' => 'Button Style',
                        'name' => 'style',
                        'type' => 'select',
                        'choices' => [
                            'secondary' => 'Secondary',
                            'primary' => 'Primary',
                        ],
                        'default_value' => 'secondary',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/dclt-cta',
                ],
            ],
        ],
    ]);
}
?>