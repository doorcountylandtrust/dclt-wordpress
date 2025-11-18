<?php
/**
 * Register DCLT Hero Block
 *
 * @package DCLT_Theme
 */

namespace DCLT\Blocks\Hero;

/**
 * Register the Hero block
 */
function register_hero_block() {
    // Register the block with custom script/style paths
    $asset_file = get_template_directory() . '/build/blocks/hero/index.asset.php';

    if (file_exists($asset_file)) {
        $asset = require $asset_file;
    } else {
        $asset = [
            'dependencies' => [],
            'version' => '1.0.0',
        ];
    }

    // Register the editor script
    wp_register_script(
        'dclt-hero-editor',
        get_template_directory_uri() . '/build/blocks/hero/index.js',
        $asset['dependencies'],
        $asset['version']
    );

    // Register the editor style
    wp_register_style(
        'dclt-hero-editor-style',
        get_template_directory_uri() . '/blocks/hero/editor.css',
        [],
        '1.0.0'
    );

    // Register the frontend style
    wp_register_style(
        'dclt-hero-style',
        get_template_directory_uri() . '/blocks/hero/style.css',
        [],
        '1.0.0'
    );

    register_block_type(
        __DIR__ . '/block.json',
        [
            'render_callback' => __NAMESPACE__ . '\render_hero_block',
            'editor_script' => 'dclt-hero-editor',
            'editor_style' => 'dclt-hero-editor-style',
            'style' => 'dclt-hero-style',
        ]
    );
}

// Call the registration function immediately when file is loaded
register_hero_block();

/**
 * Render callback for Hero block
 *
 * @param array $attributes Block attributes.
 * @return string Rendered HTML.
 */
function render_hero_block($attributes) {
    // Extract attributes with defaults
    $background_type = $attributes['backgroundType'] ?? 'image';
    $background_image = $attributes['backgroundImage'] ?? null;
    $background_video = $attributes['backgroundVideo'] ?? null;
    $headline = $attributes['headline'] ?? '';
    $subheadline = $attributes['subheadline'] ?? '';
    $overlay_opacity = $attributes['overlayOpacity'] ?? 0.4;
    $primary_cta_label = $attributes['primaryCTALabel'] ?? '';
    $primary_cta_url = $attributes['primaryCTAUrl'] ?? '';
    $secondary_cta_label = $attributes['secondaryCTALabel'] ?? '';
    $secondary_cta_url = $attributes['secondaryCTAUrl'] ?? '';
    $show_scroll_indicator = $attributes['showScrollIndicator'] ?? true;
    $min_height = $attributes['minHeight'] ?? '600px';

    // Generate unique ID for this block instance
    $block_id = 'dclt-hero-' . wp_unique_id();

    ob_start();
    ?>
    <section
        id="<?php echo esc_attr($block_id); ?>"
        class="dclt-hero relative overflow-hidden flex items-center"
        style="min-height: <?php echo esc_attr($min_height); ?>; height: 100vh;"
    >
        <!-- Background Media -->
        <div class="dclt-hero__background absolute inset-0 z-0">
            <?php if ($background_type === 'video' && !empty($background_video['url'])) : ?>
                <video
                    autoplay
                    loop
                    muted
                    playsinline
                    class="w-full h-full object-cover"
                >
                    <source src="<?php echo esc_url($background_video['url']); ?>" type="video/mp4">
                    <?php if (!empty($background_image['url'])) : ?>
                        <img
                            src="<?php echo esc_url($background_image['url']); ?>"
                            alt=""
                            class="w-full h-full object-cover"
                        >
                    <?php endif; ?>
                </video>
            <?php elseif ($background_type === 'image' && !empty($background_image['url'])) : ?>
                <?php
                // Get responsive image if ID is available
                if (!empty($background_image['id'])) {
                    echo wp_get_attachment_image(
                        $background_image['id'],
                        'full',
                        false,
                        [
                            'class' => 'w-full h-full object-cover',
                            'alt' => $background_image['alt'] ?? '',
                        ]
                    );
                } else {
                    ?>
                    <img
                        src="<?php echo esc_url($background_image['url']); ?>"
                        alt="<?php echo esc_attr($background_image['alt'] ?? ''); ?>"
                        class="w-full h-full object-cover"
                    >
                    <?php
                }
                ?>
            <?php else : ?>
                <div class="w-full h-full bg-gradient-to-br from-primary via-cedar-green to-water-blue"></div>
            <?php endif; ?>

            <!-- Gradient Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/60"
                style="opacity: <?php echo esc_attr($overlay_opacity); ?>;"
            ></div>
        </div>

        <!-- Content -->
        <div class="dclt-hero__content relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl">
                <?php if (!empty($headline)) : ?>
                    <h1 class="dclt-hero__headline text-white mb-6 leading-tight tracking-tight">
                        <?php echo wp_kses_post($headline); ?>
                    </h1>
                <?php endif; ?>

                <?php if (!empty($subheadline)) : ?>
                    <p class="dclt-hero__subheadline text-white/95 mb-8 max-w-2xl leading-relaxed">
                        <?php echo wp_kses_post($subheadline); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($primary_cta_label) || !empty($secondary_cta_label)) : ?>
                    <div class="dclt-hero__ctas flex flex-wrap gap-4">
                        <?php if (!empty($primary_cta_label) && !empty($primary_cta_url)) : ?>
                            <a
                                href="<?php echo esc_url($primary_cta_url); ?>"
                                class="dclt-button dclt-button--primary dclt-button--lg bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg"
                            >
                                <?php echo esc_html($primary_cta_label); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($secondary_cta_label) && !empty($secondary_cta_url)) : ?>
                            <a
                                href="<?php echo esc_url($secondary_cta_url); ?>"
                                class="dclt-button dclt-button--secondary dclt-button--lg bg-white/10 hover:bg-white/20 text-white border-white/30 backdrop-blur-sm"
                            >
                                <?php echo esc_html($secondary_cta_label); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($show_scroll_indicator) : ?>
            <!-- Scroll Indicator -->
            <div class="dclt-hero__scroll-indicator absolute bottom-8 left-1/2 -translate-x-1/2 z-10">
                <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center pt-2">
                    <div class="w-1 h-2 bg-white/70 rounded-full animate-bounce"></div>
                </div>
            </div>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}
