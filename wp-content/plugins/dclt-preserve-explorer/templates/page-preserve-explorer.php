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

$gallery_images = $preserve_data ? get_post_meta($preserve_data->ID, '_preserve_gallery_images', true) : array();
$gallery_data = array();

if (!empty($gallery_images) && is_array($gallery_images)) {
    foreach ($gallery_images as $image_id) {
        $image_data = wp_get_attachment_image_src($image_id, 'large');
        $image_thumb = wp_get_attachment_image_src($image_id, 'medium');
        $caption = get_post_meta($preserve_data->ID, "_preserve_gallery_caption_{$image_id}", true);
        
        if ($image_data) {
            $gallery_data[] = array(
                'id' => (int) $image_id,
                'url' => $image_data[0],
                'thumbnail' => $image_thumb ? $image_thumb[0] : $image_data[0],
                'width' => (int) $image_data[1],
                'height' => (int) $image_data[2],
                'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '',
                'caption' => $caption ?: '',
                'title' => get_the_title($image_id) ?: ''
            );
        }
    }
}

$all_filters = array();
if ($is_preserve_page && $preserve_data) {
    try {
        $filter_manager = new DCLT_Preserve_Filter_Options();
        $all_filters = $filter_manager->get_filter_options();
    } catch (Exception $e) {
        // Fallback if filter manager fails
        $all_filters = array();
        error_log('Filter manager error in template: ' . $e->getMessage());
    }
}


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
<?php
    if ($is_preserve_page && $preserve_data) {
    $filter_manager = new DCLT_Preserve_Filter_Options();
    $all_filters = $filter_manager->get_filter_options();
}
?>

<!-- THE REACT APP CONTAINER -->
<div id="preserve-explorer-root">
    
    <!-- Enhanced loading state with SEO content -->
    <div class="preserve-explorer-loading">
        <div class="loading-header">
            <h1><?php echo $is_preserve_page && $preserve_data ? esc_html($preserve_data->post_title) : 'Door County Preserve Explorer'; ?></h1>
            <p><?php echo $is_preserve_page ? 'Loading preserve details...' : 'Loading interactive map...'; ?></p>
        </div>
        
        <?php if ($is_preserve_page && $preserve_data): ?>
            <!-- Rich content for preserve pages that crawlers can see -->
            <div class="preserve-seo-content">
                
                <!-- Key Statistics -->
                <div class="preserve-stats-grid">
                    <?php if ($acres): ?>
                        <div class="preserve-stat-card">
                            <div class="stat-number"><?php echo esc_html($acres); ?></div>
                            <div class="stat-label">acres</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($trail_length): ?>
                        <div class="preserve-stat-card">
                            <div class="stat-number"><?php echo esc_html($trail_length); ?></div>
                            <div class="stat-label">miles of trails</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($difficulty && is_array($difficulty)): ?>
                        <div class="preserve-stat-card">
                            <div class="stat-text"><?php echo esc_html(ucwords(implode(', ', $difficulty))); ?></div>
                            <div class="stat-label">difficulty</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Full Description -->
                <?php if ($preserve_data->post_content): ?>
                    <div class="preserve-description">
                        <h2>About <?php echo esc_html($preserve_data->post_title); ?></h2>
                        <?php echo wpautop($preserve_data->post_content); ?>
                    </div>
                <?php endif; ?>

                <!-- Location Information -->
                <?php if ($region && is_array($region)): ?>
                    <div class="preserve-location">
                        <h3>📍 Location</h3>
                        <p>Located in <?php echo esc_html(implode(', ', $region)); ?>, Door County, Wisconsin.</p>
                        
                        <?php if ($lat && $lng): ?>
                            <p><strong>Coordinates:</strong> <?php echo esc_html($lat); ?>, <?php echo esc_html($lng); ?></p>
                            <p><a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($lat . ',' . $lng); ?>" target="_blank" rel="noopener">🧭 Get Directions</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Preserve Features (from dynamic filters) -->
                <div class="preserve-features">
                    <h3>Preserve Features</h3>
                    
                    <?php foreach ($all_filters as $filter_key => $filter_data): ?>
                        <?php 
                        $preserve_values = get_post_meta($preserve_data->ID, '_preserve_filter_' . $filter_key, true);
                        if (!empty($preserve_values) && is_array($preserve_values)):
                        ?>
                            <div class="feature-group">
                                <h4>
                                    <?php echo esc_html($filter_data['icon'] ?? ''); ?> 
                                    <?php echo esc_html($filter_data['label'] ?? ucfirst($filter_key)); ?>
                                </h4>
                                <ul class="feature-list">
                                    <?php foreach ($preserve_values as $value): ?>
                                        <?php if (isset($filter_data['options'][$value])): ?>
                                            <li><?php echo esc_html($filter_data['options'][$value]); ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Call to Action -->
                <div class="preserve-cta">
                    <h3>Visit <?php echo esc_html($preserve_data->post_title); ?></h3>
                    <p>Experience this preserve with our interactive map explorer, featuring detailed trail information, accessibility details, and real-time navigation.</p>
                    
                    <div class="cta-buttons">
                        <noscript>
                            <!-- Fallback for users without JavaScript -->
                            <p><strong>Note:</strong> This page features an interactive map that requires JavaScript. Please enable JavaScript for the full experience.</p>
                        </noscript>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="loading-spinner">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<style>
/* Enhanced SEO content styling */
.preserve-seo-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.preserve-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.preserve-stat-card {
    text-align: center;
    padding: 20px;
    background: #f0f9ff;
    border-radius: 12px;
    border: 1px solid #e0f2fe;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #0369a1;
    margin-bottom: 4px;
}

.stat-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0369a1;
    margin-bottom: 4px;
    text-transform: capitalize;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    text-transform: lowercase;
}

.preserve-description {
    margin-bottom: 32px;
    line-height: 1.6;
}

.preserve-description h2 {
    color: #1f2937;
    font-size: 1.5rem;
    margin-bottom: 16px;
}

.preserve-location,
.preserve-features {
    margin-bottom: 32px;
}

.preserve-location h3,
.preserve-features h3 {
    color: #1f2937;
    font-size: 1.3rem;
    margin-bottom: 16px;
}

.feature-group {
    margin-bottom: 20px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.feature-group h4 {
    margin: 0 0 8px 0;
    color: #374151;
    font-size: 1.1rem;
}

.feature-list {
    margin: 0;
    padding-left: 20px;
}

.feature-list li {
    margin-bottom: 4px;
    color: #6b7280;
}

.preserve-cta {
    text-align: center;
    padding: 24px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 12px;
    border: 1px solid #e0f2fe;
}

.preserve-cta h3 {
    color: #1f2937;
    margin-bottom: 8px;
}

/* Existing loading spinner styles */
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
    .preserve-stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}
</style>
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
            _preserve_filter_region: <?php echo json_encode($region ?: []); ?>,
            _preserve_gallery: <?php echo json_encode($gallery_data); ?>
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