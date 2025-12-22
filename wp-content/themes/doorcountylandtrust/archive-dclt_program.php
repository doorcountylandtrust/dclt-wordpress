<?php
/**
 * Archive Programs Template
 * Location: archive-dclt_program.php
 */

get_header();

// Get filter from URL
$type_filter = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';

// Get all program types for filter dropdown
$program_types = get_terms([
    'taxonomy' => 'dclt_program_type',
    'hide_empty' => true,
]);

// Custom query for upcoming programs
$today = date('Y-m-d');
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$args = [
    'post_type' => 'dclt_program',
    'posts_per_page' => 12,
    'paged' => $paged,
    'meta_key' => 'dclt_program_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => [
        [
            'key' => 'dclt_program_date',
            'value' => $today,
            'compare' => '>=',
            'type' => 'DATE'
        ]
    ]
];

// Add type filter if selected
if ($type_filter) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'dclt_program_type',
            'field' => 'slug',
            'terms' => $type_filter,
        ]
    ];
}

$programs = new WP_Query($args);
?>

<main id="main" class="site-main">

    <!-- Header -->
    <header class="bg-gradient-to-br from-green-800 to-green-900 text-white py-12 md:py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                Programs & Events
            </h1>
            <p class="text-green-100 text-lg max-w-2xl">
                Join us for guided hikes, workshops, volunteer workdays, and special events throughout Door County.
            </p>
        </div>
    </header>

    <!-- Filters -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="get" class="flex flex-wrap items-center gap-4">
                <label for="type-filter" class="text-sm font-medium text-gray-700">Filter by:</label>
                <select name="type" id="type-filter" onchange="this.form.submit()" 
                        class="rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Programs</option>
                    <?php foreach ($program_types as $type): ?>
                    <option value="<?php echo esc_attr($type->slug); ?>" <?php selected($type_filter, $type->slug); ?>>
                        <?php echo esc_html($type->name); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <?php if ($type_filter): ?>
                <a href="<?php echo esc_url(get_post_type_archive_link('dclt_program')); ?>" 
                   class="text-sm text-green-700 hover:text-green-800">
                    Clear filter
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Programs Grid -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <?php if ($programs->have_posts()): ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <?php while ($programs->have_posts()): $programs->the_post(); 
                $post_id = get_the_ID();
                
                // Get meta
                $event_date = dclt_get_field($post_id, 'program_date', '');
                $event_time = dclt_get_field($post_id, 'program_time', '');
                $location = dclt_get_field($post_id, 'program_location', '');
                $preserve_id = dclt_get_field($post_id, 'program_preserve_id', '');
                $fee_type = dclt_get_field($post_id, 'program_fee_type', '', 'free');
                $fee_amount = dclt_get_field($post_id, 'program_fee_amount', '');
                $status = dclt_get_field($post_id, 'program_status', '', 'scheduled');
                
                // Format
                $formatted_date = $event_date ? date('M j', strtotime($event_date)) : '';
                $formatted_day = $event_date ? date('l', strtotime($event_date)) : '';
                $formatted_time = $event_time ? date('g:i A', strtotime($event_time)) : '';
                
                // Location display
                $location_display = $location;
                if ($preserve_id && get_post($preserve_id)) {
                    $location_display = get_the_title($preserve_id);
                }
                
                // Program type
                $types = get_the_terms($post_id, 'dclt_program_type');
                $type_name = $types && !is_wp_error($types) ? $types[0]->name : '';
                
                // Fee display
                $fee_display = 'Free';
                if ($fee_type === 'paid' && $fee_amount) {
                    $fee_display = '$' . number_format((float)$fee_amount, 0);
                } elseif ($fee_type === 'donation') {
                    $fee_display = 'Donation';
                }
                
                $is_cancelled = $status === 'cancelled';
            ?>
            
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow <?php echo $is_cancelled ? 'opacity-60' : ''; ?>">
                <a href="<?php the_permalink(); ?>" class="block">
                    
                    <!-- Date Banner -->
                    <div class="bg-green-800 text-white px-4 py-3 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold"><?php echo esc_html($formatted_date); ?></span>
                            <span class="text-green-200 text-sm ml-2"><?php echo esc_html($formatted_day); ?></span>
                        </div>
                        <?php if ($is_cancelled): ?>
                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">Cancelled</span>
                        <?php else: ?>
                        <span class="text-green-200 text-sm"><?php echo esc_html($fee_display); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4">
                        
                        <?php if ($type_name): ?>
                        <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">
                            <?php echo esc_html($type_name); ?>
                        </p>
                        <?php endif; ?>
                        
                        <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                            <?php the_title(); ?>
                        </h2>
                        
                        <div class="space-y-1 text-sm text-gray-600">
                            <?php if ($formatted_time): ?>
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <?php echo esc_html($formatted_time); ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if ($location_display): ?>
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <?php echo esc_html($location_display); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    
                </a>
            </article>
            
            <?php endwhile; ?>
            
        </div>
        
        <!-- Pagination -->
        <?php if ($programs->max_num_pages > 1): ?>
        <nav class="mt-8 flex justify-center">
            <div class="flex gap-2">
                <?php
                echo paginate_links([
                    'total' => $programs->max_num_pages,
                    'current' => $paged,
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'type' => 'list',
                    'class' => 'pagination',
                ]);
                ?>
            </div>
        </nav>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
        
        <?php else: ?>
        
        <!-- No Programs Found -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">No upcoming programs</h2>
            <p class="text-gray-600 mb-6">Check back soon for new events, or view our past programs.</p>
            
            <?php if ($type_filter): ?>
            <a href="<?php echo esc_url(get_post_type_archive_link('dclt_program')); ?>" 
               class="inline-block bg-green-700 hover:bg-green-800 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                View All Programs
            </a>
            <?php endif; ?>
        </div>
        
        <?php endif; ?>
        
    </div>

</main>

<?php get_footer(); ?>