<?php
error_log('DCLT Programs: File loaded');
/**
 * Door County Land Trust — Programs Custom Post Type
 * Location: inc/post-types/programs.php
 * 
 * Registers the Programs/Events CPT, taxonomies, and admin meta box.
 * Follow existing patterns from blocks-init.php and meta box files.
 */

if (!defined('ABSPATH')) { exit; }

/* ============================================================================
 * Register Custom Post Type
 * ========================================================================== */

function dclt_register_programs_cpt() {
    $labels = [
        'name'                  => 'Programs',
        'singular_name'         => 'Program',
        'menu_name'             => 'Programs',
        'name_admin_bar'        => 'Program',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Program',
        'new_item'              => 'New Program',
        'edit_item'             => 'Edit Program',
        'view_item'             => 'View Program',
        'all_items'             => 'All Programs',
        'search_items'          => 'Search Programs',
        'not_found'             => 'No programs found.',
        'not_found_in_trash'    => 'No programs found in Trash.',
        'archives'              => 'Program Archives',
        'filter_items_list'     => 'Filter programs list',
        'items_list_navigation' => 'Programs list navigation',
        'items_list'            => 'Programs list',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true, // Gutenberg support
        'query_var'          => true,
        'rewrite'            => ['slug' => 'programs', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
    ];

    register_post_type('dclt_program', $args);
}
add_action('init', 'dclt_register_programs_cpt');

/* ============================================================================
 * Register Taxonomies
 * ========================================================================== */

function dclt_register_program_taxonomies() {
    
    // Program Type (Guided Hike, Workshop, Science on Tap, Workday, etc.)
    $type_labels = [
        'name'              => 'Program Types',
        'singular_name'     => 'Program Type',
        'search_items'      => 'Search Program Types',
        'all_items'         => 'All Program Types',
        'edit_item'         => 'Edit Program Type',
        'update_item'       => 'Update Program Type',
        'add_new_item'      => 'Add New Program Type',
        'new_item_name'     => 'New Program Type Name',
        'menu_name'         => 'Program Types',
    ];

    register_taxonomy('dclt_program_type', ['dclt_program'], [
        'labels'            => $type_labels,
        'hierarchical'      => true, // like categories
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'program-type'],
    ]);

    // Default program types
    $default_types = [
        'guided-hike'   => 'Guided Hike',
        'workshop'      => 'Workshop',
        'science-on-tap'=> 'Science on Tap',
        'workday'       => 'Workday',
        'fundraiser'    => 'Fundraiser',
        'special-event' => 'Special Event',
    ];

    foreach ($default_types as $slug => $name) {
        if (!term_exists($slug, 'dclt_program_type')) {
            wp_insert_term($name, 'dclt_program_type', ['slug' => $slug]);
        }
    }

    // Audience (optional taxonomy for filtering)
    $audience_labels = [
        'name'              => 'Audience',
        'singular_name'     => 'Audience',
        'search_items'      => 'Search Audiences',
        'all_items'         => 'All Audiences',
        'edit_item'         => 'Edit Audience',
        'update_item'       => 'Update Audience',
        'add_new_item'      => 'Add New Audience',
        'new_item_name'     => 'New Audience Name',
        'menu_name'         => 'Audience',
    ];

    register_taxonomy('dclt_program_audience', ['dclt_program'], [
        'labels'            => $audience_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'audience'],
    ]);

    // Default audiences
    $default_audiences = [
        'all-ages'      => 'All Ages',
        'adults'        => 'Adults Only',
        'families'      => 'Family Friendly',
        'members'       => 'Members Only',
    ];

    foreach ($default_audiences as $slug => $name) {
        if (!term_exists($slug, 'dclt_program_audience')) {
            wp_insert_term($name, 'dclt_program_audience', ['slug' => $slug]);
        }
    }
}
add_action('init', 'dclt_register_program_taxonomies');

/* ============================================================================
 * Meta Box Registration
 * ========================================================================== */

function dclt_add_program_meta_box() {
    add_meta_box(
        'dclt_program_details',
        'Program Details',
        'dclt_program_meta_box_callback',
        'dclt_program',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dclt_add_program_meta_box');

/* ============================================================================
 * Meta Box UI
 * ========================================================================== */

function dclt_program_meta_box_callback($post) {
    wp_nonce_field('dclt_program_meta_box', 'dclt_program_meta_box_nonce');

    // Get current values using existing helper
    $event_date     = dclt_get_field($post->ID, 'program_date', '');
    $event_time     = dclt_get_field($post->ID, 'program_time', '');
    $end_time       = dclt_get_field($post->ID, 'program_end_time', '');
    $location_text  = dclt_get_field($post->ID, 'program_location', '');
    $preserve_id    = dclt_get_field($post->ID, 'program_preserve_id', '');
    $meeting_point  = dclt_get_field($post->ID, 'program_meeting_point', '');
    
    $fee_type       = dclt_get_field($post->ID, 'program_fee_type', '', 'free');
    $fee_amount     = dclt_get_field($post->ID, 'program_fee_amount', '');
    $fee_note       = dclt_get_field($post->ID, 'program_fee_note', '');
    
    $capacity       = dclt_get_field($post->ID, 'program_capacity', '');
    $registration   = dclt_get_field($post->ID, 'program_registration', '', 'required');
    
    $trail_length   = dclt_get_field($post->ID, 'program_trail_length', '');
    $difficulty     = dclt_get_field($post->ID, 'program_difficulty', '', 'easy');
    $accessibility  = dclt_get_field($post->ID, 'program_accessibility', '');
    $what_to_bring  = dclt_get_field($post->ID, 'program_what_to_bring', '');
    $rules          = dclt_get_field($post->ID, 'program_rules', '');
    
    $leader_name    = dclt_get_field($post->ID, 'program_leader_name', '');
    $leader_bio     = dclt_get_field($post->ID, 'program_leader_bio', '');
    
    $status         = dclt_get_field($post->ID, 'program_status', '', 'scheduled');

    // Get preserves for dropdown (if Preserves CPT exists)
    $preserves = [];
    if (post_type_exists('dclt_preserve')) {
        $preserves = get_posts([
            'post_type'      => 'dclt_preserve',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
    }

    ?>
    <style>
        .dclt-program-meta { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .dclt-meta-section { background: #f9f9f9; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
        .dclt-meta-section h3 { margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #065f46; color: #065f46; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; }
        .dclt-field { margin-bottom: 16px; }
        .dclt-field:last-child { margin-bottom: 0; }
        .dclt-field label { display: block; font-weight: 600; margin-bottom: 6px; color: #1f2937; font-size: 13px; }
        .dclt-field input[type="text"],
        .dclt-field input[type="url"],
        .dclt-field input[type="number"],
        .dclt-field input[type="date"],
        .dclt-field input[type="time"],
        .dclt-field textarea,
        .dclt-field select { width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .dclt-field textarea { max-width: 100%; }
        .dclt-field .description { color: #6b7280; font-size: 12px; margin-top: 4px; line-height: 1.4; }
        .dclt-row { display: flex; gap: 16px; flex-wrap: wrap; }
        .dclt-row .dclt-field { flex: 1; min-width: 180px; }
        .dclt-status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .dclt-status-scheduled { background: #dbeafe; color: #1e40af; }
        .dclt-status-cancelled { background: #fee2e2; color: #991b1b; }
        .dclt-status-completed { background: #d1fae5; color: #065f46; }
    </style>

    <div class="dclt-program-meta">

        <!-- Date & Time -->
        <div class="dclt-meta-section">
            <h3>📅 Date & Time</h3>
            <div class="dclt-row">
                <div class="dclt-field">
                    <label for="program_date">Event Date *</label>
                    <input type="date" id="program_date" name="program_date" value="<?php echo esc_attr($event_date); ?>" required>
                </div>
                <div class="dclt-field">
                    <label for="program_time">Start Time *</label>
                    <input type="time" id="program_time" name="program_time" value="<?php echo esc_attr($event_time); ?>" required>
                </div>
                <div class="dclt-field">
                    <label for="program_end_time">End Time</label>
                    <input type="time" id="program_end_time" name="program_end_time" value="<?php echo esc_attr($end_time); ?>">
                    <div class="description">Optional — helps visitors plan their day</div>
                </div>
            </div>
            <div class="dclt-field">
                <label for="program_status">Status</label>
                <select id="program_status" name="program_status">
                    <option value="scheduled" <?php selected($status, 'scheduled'); ?>>Scheduled</option>
                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>>Cancelled</option>
                    <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
                </select>
            </div>
        </div>

        <!-- Location -->
        <div class="dclt-meta-section">
            <h3>📍 Location</h3>
            <?php if (!empty($preserves)) : ?>
            <div class="dclt-field">
                <label for="program_preserve_id">Preserve</label>
                <select id="program_preserve_id" name="program_preserve_id">
                    <option value="">— Select a Preserve —</option>
                    <?php foreach ($preserves as $preserve) : ?>
                        <option value="<?php echo esc_attr($preserve->ID); ?>" <?php selected($preserve_id, $preserve->ID); ?>>
                            <?php echo esc_html($preserve->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="description">Links to Preserve Explorer for details and directions</div>
            </div>
            <?php endif; ?>
            <div class="dclt-field">
                <label for="program_location">Location Name</label>
                <input type="text" id="program_location" name="program_location" value="<?php echo esc_attr($location_text); ?>" placeholder="e.g., Ellison Bluff State Natural Area">
                <div class="description">Use if not linked to a preserve, or for off-site events</div>
            </div>
            <div class="dclt-field">
                <label for="program_meeting_point">Meeting Point</label>
                <input type="text" id="program_meeting_point" name="program_meeting_point" value="<?php echo esc_attr($meeting_point); ?>" placeholder="e.g., Main parking lot trailhead kiosk">
                <div class="description">Specific spot where participants should gather</div>
            </div>
            <div class="dclt-field">
                <label for="program_google_maps_link">Google Maps Link</label>
                <input type="url" id="program_google_maps_link" name="program_google_maps_link" value="<?php echo esc_attr(dclt_get_field($post->ID, 'program_google_maps_link', '')); ?>" placeholder="https://maps.google.com/...">
                <div class="description">Paste the Google Maps link for directions</div>
            </div>
            <div class="dclt-field">
                <label for="program_parking_info">Parking Information</label>
                <textarea id="program_parking_info" name="program_parking_info" rows="2" placeholder="e.g., Park in the main lot. Overflow parking available on the road."><?php echo esc_textarea(dclt_get_field($post->ID, 'program_parking_info', '')); ?></textarea>
                <div class="description">Where to park, any restrictions</div>
            </div>
        </div>

        <!-- Fee & Registration -->
        <div class="dclt-meta-section">
            <h3>💰 Fee & Registration</h3>
            <div class="dclt-row">
                <div class="dclt-field">
                    <label for="program_fee_type">Fee</label>
                    <select id="program_fee_type" name="program_fee_type">
                        <option value="free" <?php selected($fee_type, 'free'); ?>>Free</option>
                        <option value="paid" <?php selected($fee_type, 'paid'); ?>>Paid</option>
                        <option value="donation" <?php selected($fee_type, 'donation'); ?>>Suggested Donation</option>
                    </select>
                </div>
                <div class="dclt-field" id="fee_amount_field" style="<?php echo $fee_type === 'free' ? 'opacity: 0.5;' : ''; ?>">
                    <label for="program_fee_amount">Amount ($)</label>
                    <input type="number" id="program_fee_amount" name="program_fee_amount" value="<?php echo esc_attr($fee_amount); ?>" min="0" step="1" placeholder="10">
                </div>
            </div>
            <div class="dclt-field">
                <label for="program_fee_note">Fee Note</label>
                <input type="text" id="program_fee_note" name="program_fee_note" value="<?php echo esc_attr($fee_note); ?>" placeholder="e.g., Covers honorarium for guest expert">
                <div class="description">Optional explanation for the fee</div>
            </div>
            <div class="dclt-row">
                <div class="dclt-field">
                    <label for="program_registration">Registration</label>
                    <select id="program_registration" name="program_registration">
                        <option value="required" <?php selected($registration, 'required'); ?>>Required</option>
                        <option value="recommended" <?php selected($registration, 'recommended'); ?>>Recommended</option>
                        <option value="drop-in" <?php selected($registration, 'drop-in'); ?>>Drop-in (no registration)</option>
                    </select>
                </div>
                <div class="dclt-field">
                    <label for="program_capacity">Capacity</label>
                    <input type="number" id="program_capacity" name="program_capacity" value="<?php echo esc_attr($capacity); ?>" min="0" placeholder="20">
                    <div class="description">Maximum participants (leave blank for unlimited)</div>
                </div>
            </div>
        </div>

        <!-- Accessibility & What to Bring -->
        <div class="dclt-meta-section">
            <h3>♿ Accessibility & Preparation</h3>
            <div class="dclt-row">
                <div class="dclt-field">
                    <label for="program_trail_length">Trail Length</label>
                    <input type="text" id="program_trail_length" name="program_trail_length" value="<?php echo esc_attr($trail_length); ?>" placeholder="e.g., 1.5 miles">
                </div>
                <div class="dclt-field">
                    <label for="program_difficulty">Difficulty</label>
                    <select id="program_difficulty" name="program_difficulty">
                        <option value="easy" <?php selected($difficulty, 'easy'); ?>>Easy — flat, paved or groomed</option>
                        <option value="moderate" <?php selected($difficulty, 'moderate'); ?>>Moderate — some hills, natural surface</option>
                        <option value="challenging" <?php selected($difficulty, 'challenging'); ?>>Challenging — steep, uneven terrain</option>
                    </select>
                </div>
            </div>
            <div class="dclt-field">
                <label for="program_accessibility">Accessibility Notes</label>
                <textarea id="program_accessibility" name="program_accessibility" rows="2" placeholder="e.g., Trail is not wheelchair accessible. Some uneven terrain."><?php echo esc_textarea($accessibility); ?></textarea>
                <div class="description">Help visitors know if they can participate</div>
            </div>
            <div class="dclt-field">
                <label for="program_what_to_bring">What to Bring</label>
                <textarea id="program_what_to_bring" name="program_what_to_bring" rows="3" placeholder="e.g., Sturdy footwear, water, binoculars, field guide (optional)"><?php echo esc_textarea($what_to_bring); ?></textarea>
            </div>
            <div class="dclt-field">
                <label for="program_rules">Rules & Notes</label>
                <textarea id="program_rules" name="program_rules" rows="2" placeholder="e.g., No dogs allowed. Children under 12 must be accompanied by an adult."><?php echo esc_textarea($rules); ?></textarea>
            </div>
        </div>

        <!-- Leader Info -->
        <div class="dclt-meta-section">
            <h3>👤 Program Leader</h3>
            <div class="dclt-field">
                <label for="program_leader_name">Leader Name</label>
                <input type="text" id="program_leader_name" name="program_leader_name" value="<?php echo esc_attr($leader_name); ?>" placeholder="e.g., Dr. Jane Smith">
            </div>
            <div class="dclt-field">
                <label for="program_leader_bio">Leader Bio</label>
                <textarea id="program_leader_bio" name="program_leader_bio" rows="3" placeholder="Brief background for guest experts or special leaders"><?php echo esc_textarea($leader_bio); ?></textarea>
                <div class="description">Optional — great for guest expert hikes</div>
            </div>
        </div>

    </div>

    <script>
    jQuery(function($) {
        // Toggle fee amount field based on fee type
        $('#program_fee_type').on('change', function() {
            const isFree = $(this).val() === 'free';
            $('#fee_amount_field').css('opacity', isFree ? '0.5' : '1');
        });
    });
    </script>
    <?php
}

/* ============================================================================
 * Save Meta Box Data
 * ========================================================================== */

function dclt_save_program_meta_box($post_id) {
    // Security checks
    if (!isset($_POST['dclt_program_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['dclt_program_meta_box_nonce'], 'dclt_program_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'dclt_program') return;

    // Fields to save
    $text_fields = [
        'program_date',
        'program_time',
        'program_end_time',
        'program_location',
        'program_preserve_id',
        'program_meeting_point',
        'program_fee_type',
        'program_fee_amount',
        'program_fee_note',
        'program_capacity',
        'program_registration',
        'program_trail_length',
        'program_difficulty',
        'program_status',
        'program_leader_name',
        'program_google_maps_link',
    ];

    $textarea_fields = [
        'program_accessibility',
        'program_what_to_bring',
        'program_rules',
        'program_leader_bio',
        'program_parking_info',
    ];

    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    foreach ($textarea_fields as $field) {
        if (isset($_POST[$field])) {
            dclt_update_field($post_id, $field, sanitize_textarea_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'dclt_save_program_meta_box');

/* ============================================================================
 * Admin Columns
 * ========================================================================== */

function dclt_program_admin_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['program_date'] = 'Date';
            $new_columns['program_location'] = 'Location';
            $new_columns['program_status'] = 'Status';
        }
    }
    return $new_columns;
}
add_filter('manage_dclt_program_posts_columns', 'dclt_program_admin_columns');

function dclt_program_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'program_date':
            $date = dclt_get_field($post_id, 'program_date', '');
            $time = dclt_get_field($post_id, 'program_time', '');
            if ($date) {
                echo esc_html(date('M j, Y', strtotime($date)));
                if ($time) {
                    echo '<br><span style="color: #6b7280;">' . esc_html(date('g:i A', strtotime($time))) . '</span>';
                }
            } else {
                echo '<span style="color: #9ca3af;">—</span>';
            }
            break;

        case 'program_location':
            $preserve_id = dclt_get_field($post_id, 'program_preserve_id', '');
            $location = dclt_get_field($post_id, 'program_location', '');
            if ($preserve_id && get_post($preserve_id)) {
                echo '<a href="' . get_edit_post_link($preserve_id) . '">' . esc_html(get_the_title($preserve_id)) . '</a>';
            } elseif ($location) {
                echo esc_html($location);
            } else {
                echo '<span style="color: #9ca3af;">—</span>';
            }
            break;

        case 'program_status':
            $status = dclt_get_field($post_id, 'program_status', '', 'scheduled');
            $badges = [
                'scheduled' => '<span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600;">Scheduled</span>',
                'cancelled' => '<span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600;">Cancelled</span>',
                'completed' => '<span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600;">Completed</span>',
            ];
            echo $badges[$status] ?? $badges['scheduled'];
            break;
    }
}
add_action('manage_dclt_program_posts_custom_column', 'dclt_program_admin_column_content', 10, 2);

// Make date column sortable
function dclt_program_sortable_columns($columns) {
    $columns['program_date'] = 'program_date';
    return $columns;
}
add_filter('manage_edit-dclt_program_sortable_columns', 'dclt_program_sortable_columns');

function dclt_program_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('post_type') !== 'dclt_program') return;

    $orderby = $query->get('orderby');
    if ($orderby === 'program_date') {
        $query->set('meta_key', 'dclt_program_date');
        $query->set('orderby', 'meta_value');
        $query->set('order', $query->get('order') ?: 'DESC');
    }
}
add_action('pre_get_posts', 'dclt_program_orderby');

/* ============================================================================
 * Flush Rewrite Rules on Activation
 * ========================================================================== */

function dclt_programs_flush_rewrites() {
    dclt_register_programs_cpt();
    dclt_register_program_taxonomies();
    flush_rewrite_rules();
}
// Call this once after adding the file, or on theme activation
// register_activation_hook(__FILE__, 'dclt_programs_flush_rewrites');