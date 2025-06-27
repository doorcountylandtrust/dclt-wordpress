<?php
/**
 * Unified template for both /preserve-explorer/ and /preserve/name/
 * File: templates/page-preserve-explorer.php
 */

// Detect if this is a preserve-specific URL
$is_preserve_page = false;
$preserve_data = null;
$preserve_slug = '';

if (get_query_var('preserve_single')) {
    // This is /preserve/preserve-name/ 
    $is_preserve_page = true;
    $preserve_slug = get_query_var('name');
    
    // Get the preserve data for SEO
    $preserve_posts = get_posts(array(
        'name' => $preserve_slug,
        'post_type' => 'preserve',
        'post_status' => 'publish',
        'numberposts' => 1
    ));
    
    if (!empty($preserve_posts)) {
        $preserve_data = $preserve_posts[0];
        setup_postdata($preserve_data);
    }
}

get_header(); 

// Get preserve meta data for SEO
$acres = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_acres', true) : '';
$trail_length = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_trail_length', true) : '';
$lat = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_lat', true) : '';
$lng = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_lng', true) : '';
$difficulty = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_filter_difficulty', true) : '';
$region = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_filter_region', true) : '';
?>

<head>
<?php if ($is_preserve_page && $preserve_data): ?>
    <!-- SEO META TAGS FOR PRESERVE PAGES -->
    <title><?php echo esc_html($preserve_data->post_title); ?> - Door County Land Trust</title>
    <meta name="description" content="<?php echo esc_attr(wp_trim_words($preserve_data->post_excerpt ?: $preserve_data->post_content, 30)); ?> Located in Door County, Wisconsin." />
    
    <!-- Open Graph for Social Sharing -->
    <meta property="og:title" content="<?php echo esc_attr($preserve_data->post_title); ?>" />
    <meta property="og:description" content="<?php echo esc_attr(wp_trim_words($preserve_data->post_excerpt ?: $preserve_data->post_content, 30)); ?>" />
    <meta property="og:url" content="<?php echo get_permalink($preserve_data); ?>" />
    <meta property="og:type" content="place" />
    
    <!-- Structured Data for Google -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TouristAttraction",
        "name": "<?php echo esc_js($preserve_data->post_title); ?>",
        "description": "<?php echo esc_js(wp_trim_words($preserve_data->post_content, 50)); ?>",
        "url": "<?php echo esc_url(get_permalink($preserve_data)); ?>",
        <?php if ($lat && $lng): ?>
        "geo": {
            "@type": "GeoCoordinates", 
            "latitude": <?php echo esc_js($lat); ?>,
            "longitude": <?php echo esc_js($lng); ?>
        },
        <?php endif; ?>
        <?php if ($acres): ?>
        "additionalProperty": {
            "@type": "PropertyValue",
            "name": "Area",
            "value": "<?php echo esc_js($acres); ?> acres"
        },
        <?php endif; ?>
        "provider": {
            "@type": "Organization",
            "name": "Door County Land Trust"
        }
    }
    </script>

<?php else: ?>
    <!-- GENERAL META TAGS FOR MAP EXPLORER -->
    <title>Preserve Explorer - Door County Land Trust</title>
    <meta name="description" content="Explore Door County's nature preserves with our interactive map. Discover trails, wildlife, and natural features across the peninsula." />
    
    <meta property="og:title" content="Door County Preserve Explorer" />
    <meta property="og:description" content="Interactive map to explore Door County's nature preserves, trails, and natural areas." />
    <meta property="og:url" content="<?php echo home_url('/preserve-explorer/'); ?>" />
    <meta property="og:type" content="website" />
<?php endif; ?>
</head>

<div class="preserve-explorer-container">
    
    <!-- SERVER-SIDE RENDERED CONTENT FOR SEO (hidden from users) -->
    <?php if ($is_preserve_page && $preserve_data): ?>
        <div id="preserve-seo-content" style="position: absolute; left: -9999px; visibility: hidden;">
            <h1><?php echo esc_html($preserve_data->post_title); ?></h1>
            <p>A <?php echo $acres ? esc_html($acres) . ' acre ' : ''; ?>nature preserve in Door County, Wisconsin.</p>
            
            <?php if ($trail_length): ?>
                <p>Trail Length: <?php echo esc_html($trail_length); ?> miles</p>
            <?php endif; ?>
            
            <?php if ($difficulty && is_array($difficulty)): ?>
                <p>Difficulty: <?php echo esc_html(implode(', ', $difficulty)); ?></p>
            <?php endif; ?>
            
            <?php if ($region && is_array($region)): ?>
                <p>Location: <?php echo esc_html(implode(', ', $region)); ?>, Door County</p>
            <?php endif; ?>
            
            <div><?php echo wpautop($preserve_data->post_content); ?></div>
        </div>
    <?php endif; ?>

    <!-- THE REACT APP CONTAINER -->
    <div id="preserve-explorer-root">
        
        <!-- Loading state while React initializes -->
        <div class="preserve-explorer-loading">
            <div class="loading-header">
                <h1><?php echo $is_preserve_page && $preserve_data ? esc_html($preserve_data->post_title) : 'Door County Preserve Explorer'; ?></h1>
                <p><?php echo $is_preserve_page ? 'Loading preserve details...' : 'Loading interactive map...'; ?></p>
            </div>
            
            <?php if ($is_preserve_page && $preserve_data): ?>
                <!-- Fallback content for preserve pages -->
                <div class="preserve-loading-summary">
                    <?php if ($acres): ?>
                        <div class="preserve-stat">📏 <?php echo esc_html($acres); ?> acres</div>
                    <?php endif; ?>
                    
                    <?php if ($trail_length): ?>
                        <div class="preserve-stat">🥾 <?php echo esc_html($trail_length); ?> miles</div>
                    <?php endif; ?>
                    
                    <?php if ($region && is_array($region)): ?>
                        <div class="preserve-stat">📍 <?php echo esc_html(implode(', ', $region)); ?></div>
                    <?php endif; ?>
                    
                    <div class="preserve-description">
                        <?php echo wpautop(wp_trim_words($preserve_data->post_content, 50)); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- Pass server data to React -->
<script>
window.preservePageData = {
    isPreservePage: <?php echo $is_preserve_page ? 'true' : 'false'; ?>,
    <?php if ($is_preserve_page && $preserve_data): ?>
    preserveSlug: '<?php echo esc_js($preserve_slug); ?>',
    preserveData: {
        id: <?php echo intval($preserve_data->ID); ?>,
        title: <?php echo json_encode($preserve_data->post_title); ?>,
        content: <?php echo json_encode($preserve_data->post_content); ?>,
        excerpt: <?php echo json_encode($preserve_data->post_excerpt); ?>,
        slug: '<?php echo esc_js($preserve_slug); ?>',
        meta: {
            _preserve_acres: '<?php echo esc_js($acres); ?>',
            _preserve_trail_length: '<?php echo esc_js($trail_length); ?>',
            _preserve_lat: '<?php echo esc_js($lat); ?>',
            _preserve_lng: '<?php echo esc_js($lng); ?>',
            _preserve_filter_difficulty: <?php echo json_encode($difficulty ?: []); ?>,
            _preserve_filter_region: <?php echo json_encode($region ?: []); ?>
        }
    }
    <?php endif; ?>
};
</script>

<style>
.preserve-explorer-loading {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    text-align: center;
}

.loading-header h1 {
    font-size: 2.5rem;
    color: #1f2937;
    margin-bottom: 8px;
}

.loading-header p {
    color: #6b7280;
    font-size: 1.1rem;
    margin-bottom: 24px;
}

.preserve-loading-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: center;
    margin-bottom: 24px;
}

.preserve-stat {
    background: #f3f4f6;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    color: #374151;
}

.preserve-description {
    max-width: 600px;
    margin: 0 auto 24px;
    color: #4b5563;
    line-height: 1.6;
}

.loading-spinner {
    margin: 20px 0;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .loading-header h1 {
        font-size: 2rem;
    }
    
    .preserve-loading-summary {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php get_footer(); ?>