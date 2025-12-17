<?php
/**
 * Single Program Template
 * Location: single-dclt_program.php
 */

get_header();

$post_id = get_the_ID();

// Get all meta fields
$event_date     = dclt_get_field($post_id, 'program_date', '');
$event_time     = dclt_get_field($post_id, 'program_time', '');
$end_time       = dclt_get_field($post_id, 'program_end_time', '');
$location       = dclt_get_field($post_id, 'program_location', '');
$preserve_id    = dclt_get_field($post_id, 'program_preserve_id', '');
$meeting_point  = dclt_get_field($post_id, 'program_meeting_point', '');
$directions_link = dclt_get_field($post_id, 'program_directions_link', '');
$parking_info   = dclt_get_field($post_id, 'program_parking_info', '');

$fee_type       = dclt_get_field($post_id, 'program_fee_type', '', 'free');
$fee_amount     = dclt_get_field($post_id, 'program_fee_amount', '');
$fee_note       = dclt_get_field($post_id, 'program_fee_note', '');

$capacity       = dclt_get_field($post_id, 'program_capacity', '');
$registration   = dclt_get_field($post_id, 'program_registration', '', 'required');

$trail_length   = dclt_get_field($post_id, 'program_trail_length', '');
$difficulty     = dclt_get_field($post_id, 'program_difficulty', '', 'easy');
$accessibility  = dclt_get_field($post_id, 'program_accessibility', '');
$what_to_bring  = dclt_get_field($post_id, 'program_what_to_bring', '');
$rules          = dclt_get_field($post_id, 'program_rules', '');

$leader_name    = dclt_get_field($post_id, 'program_leader_name', '');
$leader_bio     = dclt_get_field($post_id, 'program_leader_bio', '');

$status         = dclt_get_field($post_id, 'program_status', '', 'scheduled');

// Get preserve info if linked
$preserve_title = '';
$preserve_url = '';
if ($preserve_id && get_post($preserve_id)) {
    $preserve_title = get_the_title($preserve_id);
    $preserve_url = get_permalink($preserve_id);
}

// Format date/time for display
$formatted_date = $event_date ? date('l, F j, Y', strtotime($event_date)) : '';
$formatted_time = $event_time ? date('g:i A', strtotime($event_time)) : '';
$formatted_end_time = $end_time ? date('g:i A', strtotime($end_time)) : '';

// Get program type
$program_types = get_the_terms($post_id, 'dclt_program_type');
$program_type_name = $program_types && !is_wp_error($program_types) ? $program_types[0]->name : '';

// Difficulty labels
$difficulty_labels = [
    'easy' => 'Easy',
    'moderate' => 'Moderate',
    'challenging' => 'Challenging'
];

// Fee display
$fee_display = 'Free';
if ($fee_type === 'paid' && $fee_amount) {
    $fee_display = '$' . number_format((float)$fee_amount, 0);
} elseif ($fee_type === 'donation' && $fee_amount) {
    $fee_display = '$' . number_format((float)$fee_amount, 0) . ' suggested';
}
?>

<main id="main" class="site-main">
    
    <?php if ($status === 'cancelled'): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="max-w-6xl mx-auto px-4">
            <p class="text-red-800 font-semibold">This program has been cancelled.</p>
        </div>
    </div>
    <?php endif; ?>

    <article <?php post_class('program-single'); ?>>
        
        <!-- Header Section -->
        <header class="bg-gradient-to-br from-green-800 to-green-900 text-white py-12 md:py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <?php if ($program_type_name): ?>
                <p class="text-green-200 text-sm font-semibold uppercase tracking-wider mb-2">
                    <?php echo esc_html($program_type_name); ?>
                </p>
                <?php endif; ?>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                    <?php the_title(); ?>
                </h1>
                
                <?php if ($formatted_date): ?>
                <div class="flex flex-wrap items-center gap-4 text-green-100 text-lg">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo esc_html($formatted_date); ?>
                    </span>
                    <?php if ($formatted_time): ?>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?php echo esc_html($formatted_time); ?>
                        <?php if ($formatted_end_time): ?>
                            – <?php echo esc_html($formatted_end_time); ?>
                        <?php endif; ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                
                <!-- Left Column: Description -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Description -->
                    <?php if (get_the_content()): ?>
                    <section class="prose prose-lg max-w-none">
                        <?php the_content(); ?>
                    </section>
                    <?php endif; ?>
                    
                    <!-- Program Leader -->
                    <?php if ($leader_name): ?>
                    <section class="bg-gray-50 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Program Leader</h2>
                        <p class="font-medium text-gray-900"><?php echo esc_html($leader_name); ?></p>
                        <?php if ($leader_bio): ?>
                        <p class="text-gray-600 mt-2"><?php echo esc_html($leader_bio); ?></p>
                        <?php endif; ?>
                    </section>
                    <?php endif; ?>
                    
                    <!-- What to Bring -->
                    <?php if ($what_to_bring): ?>
                    <section>
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">What to Bring</h2>
                        <p class="text-gray-700 whitespace-pre-line"><?php echo esc_html($what_to_bring); ?></p>
                    </section>
                    <?php endif; ?>
                    
                    <!-- Rules & Notes -->
                    <?php if ($rules): ?>
                    <section>
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Rules & Notes</h2>
                        <p class="text-gray-700 whitespace-pre-line"><?php echo esc_html($rules); ?></p>
                    </section>
                    <?php endif; ?>
                    
                    <!-- Accessibility -->
                    <?php if ($accessibility || $trail_length || $difficulty): ?>
                    <section class="bg-blue-50 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Accessibility & Trail Info</h2>
                        <div class="space-y-2 text-gray-700">
                            <?php if ($trail_length): ?>
                            <p><strong>Trail Length:</strong> <?php echo esc_html($trail_length); ?></p>
                            <?php endif; ?>
                            <?php if ($difficulty): ?>
                            <p><strong>Difficulty:</strong> <?php echo esc_html($difficulty_labels[$difficulty] ?? $difficulty); ?></p>
                            <?php endif; ?>
                            <?php if ($accessibility): ?>
                            <p class="mt-3"><?php echo esc_html($accessibility); ?></p>
                            <?php endif; ?>
                        </div>
                    </section>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Right Column: Details & Registration -->
                <aside class="space-y-6">
                    
                    <!-- Quick Details Card -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden sticky top-24">
                        
                        <!-- Fee Badge -->
                        <div class="bg-green-800 text-white px-6 py-4">
                            <p class="text-2xl font-bold"><?php echo esc_html($fee_display); ?></p>
                            <?php if ($fee_note): ?>
                            <p class="text-green-200 text-sm mt-1"><?php echo esc_html($fee_note); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            
                            <!-- Location -->
                            <?php if ($location || $preserve_title): ?>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Location</h3>
                                <?php if ($preserve_url && $preserve_title): ?>
                                <a href="<?php echo esc_url($preserve_url); ?>" class="text-green-700 hover:text-green-800 font-medium">
                                    <?php echo esc_html($preserve_title); ?>
                                </a>
                                <?php elseif ($location): ?>
                                <p class="text-gray-900"><?php echo esc_html($location); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Meeting Point -->
                            <?php if ($meeting_point): ?>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Meeting Point</h3>
                                <p class="text-gray-900"><?php echo esc_html($meeting_point); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Parking -->
                            <?php if ($parking_info): ?>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Parking</h3>
                                <p class="text-gray-700"><?php echo esc_html($parking_info); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Directions Link -->
                            <?php if ($directions_link): ?>
                            <a href="<?php echo esc_url($directions_link); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 text-green-700 hover:text-green-800 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Get Directions
                            </a>
                            <?php endif; ?>
                            
                            <!-- Registration -->
                            <?php if ($registration !== 'drop-in' && $status === 'scheduled'): ?>
                            <div class="pt-4 border-t border-gray-200">
                                
                                <!-- Registration Form Placeholder -->
                                <div id="program-registration-form" 
                                     class="bg-gray-50 rounded-lg p-4 text-center"
                                     data-program-id="<?php echo esc_attr($post_id); ?>"
                                     data-program-title="<?php echo esc_attr(get_the_title()); ?>"
                                     data-program-date="<?php echo esc_attr($event_date); ?>"
                                     data-program-time="<?php echo esc_attr($event_time); ?>">
                                    
                                    <p class="text-gray-600 text-sm mb-3">
                                        Registration is <?php echo $registration === 'required' ? 'required' : 'recommended'; ?> for this program.
                                    </p>
                                    
                                    <!-- Temporary: Link to contact -->
                                    <a href="<?php echo esc_url(home_url('/contact')); ?>?program=<?php echo urlencode(get_the_title()); ?>" 
                                       class="inline-block w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                                        Register Now
                                    </a>
                                    
                                    <?php if ($capacity): ?>
                                    <p class="text-gray-500 text-xs mt-2">Limited to <?php echo esc_html($capacity); ?> participants</p>
                                    <?php endif; ?>
                                    
                                </div>
                                
                            </div>
                            <?php elseif ($registration === 'drop-in'): ?>
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-gray-600 text-sm">
                                    <strong>Drop-in welcome!</strong> No registration required.
                                </p>
                            </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                    
                </aside>
                
            </div>
        </div>
        
    </article>
    
    <!-- Back to Programs -->
    <div class="bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="<?php echo esc_url(get_post_type_archive_link('dclt_program')); ?>" 
               class="inline-flex items-center gap-2 text-green-700 hover:text-green-800 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to All Programs
            </a>
        </div>
    </div>

</main>

<?php get_footer(); ?>
<!-- test -->