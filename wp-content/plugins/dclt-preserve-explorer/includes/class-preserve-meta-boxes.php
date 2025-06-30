<?php
/**
 * Preserve Meta Boxes
 * 
 * This file contains ALL your meta box functionality
 * UPDATED: Now uses dynamic filter options instead of hardcoded arrays
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class DCLT_Preserve_Meta_Boxes {
    
    private $filter_options_manager;
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('add_meta_boxes', array($this, 'add_photo_gallery_meta_box')); // ADD THIS LINE
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('save_post', array($this, 'save_photo_gallery_meta')); // ADD THIS LINE
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // Initialize filter options manager
        $this->filter_options_manager = new DCLT_Preserve_Filter_Options();
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'preserve_location',
            __('Location Details', 'dclt-preserve-explorer'),
            array($this, 'location_meta_box_callback'),
            'preserve',
            'normal',
            'high'
        );
        
        add_meta_box(
            'preserve_details',
            __('Preserve Information', 'dclt-preserve-explorer'),
            array($this, 'details_meta_box_callback'),
            'preserve',
            'normal',
            'high'
        );
        
        add_meta_box(
            'preserve_filters',
            __('Preserve Filters', 'dclt-preserve-explorer'),
            array($this, 'filters_meta_box_callback'),
            'preserve',
            'normal',
            'high'
        );
        
        add_meta_box(
            'preserve_files',
            __('GeoJSON & Map Data Files', 'dclt-preserve-explorer'),
            array($this, 'files_meta_box_callback'),
            'preserve',
            'normal',
            'high'
        );
    }

    /**
     * Get filter options from the dynamic options manager
     * UPDATED: No longer hardcoded!
     */
    private function get_filter_options() {
        return $this->filter_options_manager->get_filter_options();
    }

    /**
     * Enqueue Admin Scripts
     */
    public function admin_scripts($hook) {
        global $post_type;
        
        if ($post_type === 'preserve') {
            wp_enqueue_media();
            wp_enqueue_script('dclt-preserve-admin', DCLT_PRESERVE_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), DCLT_PRESERVE_VERSION, true);
            wp_enqueue_style('dclt-preserve-admin', DCLT_PRESERVE_PLUGIN_URL . 'assets/css/admin.css', array(), DCLT_PRESERVE_VERSION);
            
            // Enqueue Leaflet for admin map preview
            wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
            wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
        }
    }
    
    /**
     * Location Meta Box Callback
     */
    public function location_meta_box_callback($post) {
        wp_nonce_field('preserve_location_nonce', 'preserve_location_nonce');
        
        $lat = get_post_meta($post->ID, '_preserve_lat', true);
        $lng = get_post_meta($post->ID, '_preserve_lng', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="preserve_lat"><?php _e('Latitude', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="text" id="preserve_lat" name="preserve_lat" value="<?php echo esc_attr($lat); ?>" 
                           placeholder="44.7555" class="regular-text" />
                    <p class="description"><?php _e('Decimal degrees (e.g., 44.7555)', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_lng"><?php _e('Longitude', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="text" id="preserve_lng" name="preserve_lng" value="<?php echo esc_attr($lng); ?>" 
                           placeholder="-87.3547" class="regular-text" />
                    <p class="description"><?php _e('Decimal degrees (e.g., -87.3547)', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Details Meta Box Callback  
     */
    public function details_meta_box_callback($post) {
        wp_nonce_field('preserve_details_nonce', 'preserve_details_nonce');
        
        $acres = get_post_meta($post->ID, '_preserve_acres', true);
        $trail_length = get_post_meta($post->ID, '_preserve_trail_length', true);
        $established = get_post_meta($post->ID, '_preserve_established', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="preserve_acres"><?php _e('Size (Acres)', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="number" id="preserve_acres" name="preserve_acres" value="<?php echo esc_attr($acres); ?>" class="small-text" />
                    <p class="description"><?php _e('Total preserve area in acres', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_trail_length"><?php _e('Trail Length (miles)', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="number" step="0.1" id="preserve_trail_length" name="preserve_trail_length" value="<?php echo esc_attr($trail_length); ?>" class="small-text" />
                    <p class="description"><?php _e('Total length of all trails combined', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_established"><?php _e('Year Established', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="number" id="preserve_established" name="preserve_established" value="<?php echo esc_attr($established); ?>" class="small-text" />
                    <p class="description"><?php _e('Year the preserve was established or acquired', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
        </table>
        
        <div class="preserve-details-note">
            <p><strong><?php _e('💡 Dynamic Filters Active', 'dclt-preserve-explorer'); ?></strong><br>
            <?php _e('Filter options are now managed dynamically. You can customize them in', 'dclt-preserve-explorer'); ?> 
            <a href="<?php echo admin_url('edit.php?post_type=preserve&page=preserve-filter-options'); ?>" target="_blank"><?php _e('Preserve > Filter Options', 'dclt-preserve-explorer'); ?></a>.</p>
        </div>
        
        <style>
        .preserve-details-note {
            margin-top: 20px;
            padding: 12px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
        }
        
        .preserve-details-note p {
            margin: 0;
            font-size: 13px;
            color: #0c5460;
        }
        
        .preserve-details-note a {
            color: #0c5460;
            text-decoration: none;
            font-weight: 600;
        }
        
        .preserve-details-note a:hover {
            text-decoration: underline;
        }
        </style>
        <?php
    }

    /**
     * Filters Meta Box Callback
     * UPDATED: Now uses dynamic filter options!
     */
    public function filters_meta_box_callback($post) {
        wp_nonce_field('preserve_filters_nonce', 'preserve_filters_nonce');
        
        // Get dynamic filter options instead of hardcoded array
        $filter_options = $this->get_filter_options();
        
        // Get current filter values
        $selected_filters = array();
        foreach ($filter_options as $filter_key => $filter_data) {
            $selected_filters[$filter_key] = (array) get_post_meta($post->ID, '_preserve_filter_' . $filter_key, true);
        }
        ?>
        
        <div class="preserve-filters-container">
            <div class="filter-management-notice">
                <p>
                    <strong><?php _e('✅ Dynamic Filters Active', 'dclt-preserve-explorer'); ?></strong><br>
                    <?php _e('Filter options are now managed dynamically.', 'dclt-preserve-explorer'); ?> 
                    <a href="<?php echo admin_url('edit.php?post_type=preserve&page=preserve-filter-options'); ?>" target="_blank">
                        <?php _e('Click here to manage filter options', 'dclt-preserve-explorer'); ?>
                    </a> 
                    <?php _e('and add new choices without touching code.', 'dclt-preserve-explorer'); ?>
                </p>
            </div>
            
            <?php foreach ($filter_options as $filter_key => $filter_data): ?>
                <fieldset class="preserve-filter-group">
                    <legend>
                        <span class="filter-icon"><?php echo esc_html($filter_data['icon'] ?? '🔧'); ?></span>
                        <strong><?php echo esc_html($filter_data['label']); ?></strong>
                        <span class="filter-count" id="count-<?php echo esc_attr($filter_key); ?>">
                            (<?php echo count($selected_filters[$filter_key]); ?> selected)
                        </span>
                    </legend>
                    
                    <div class="filter-description">
                        <?php echo esc_html($filter_data['description'] ?? ''); ?>
                    </div>
                    
                    <div class="filter-options-grid">
                        <?php if (!empty($filter_data['options'])): ?>
                            <?php foreach ($filter_data['options'] as $option_key => $option_label): 
                                $checkbox_id = 'preserve_filter_' . $filter_key . '_' . $option_key;
                                $is_checked = in_array($option_key, $selected_filters[$filter_key]);
                            ?>
                                <div class="preserve-filter-option">
                                    <label for="<?php echo esc_attr($checkbox_id); ?>">
                                        <input type="checkbox" 
                                               id="<?php echo esc_attr($checkbox_id); ?>" 
                                               name="preserve_filter_<?php echo esc_attr($filter_key); ?>[]" 
                                               value="<?php echo esc_attr($option_key); ?>"
                                               data-filter-type="<?php echo esc_attr($filter_key); ?>"
                                               <?php checked($is_checked); ?> />
                                        <?php echo esc_html($option_label); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-options-message">
                                <p><?php _e('No options available for this filter.', 'dclt-preserve-explorer'); ?> 
                                <a href="<?php echo admin_url('edit.php?post_type=preserve&page=preserve-filter-options'); ?>" target="_blank">
                                    <?php _e('Add some options', 'dclt-preserve-explorer'); ?>
                                </a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($filter_key === 'region'): ?>
                        <div class="filter-note">
                            <strong><?php _e('Note:', 'dclt-preserve-explorer'); ?></strong> 
                            <?php _e('Most preserves should have at least one region selected.', 'dclt-preserve-explorer'); ?>
                        </div>
                    <?php endif; ?>
                </fieldset>
            <?php endforeach; ?>
            
            <?php if (empty($filter_options)): ?>
                <div class="no-filters-message">
                    <h3><?php _e('No Filter Types Configured', 'dclt-preserve-explorer'); ?></h3>
                    <p><?php _e('It looks like no filter types have been set up yet.', 'dclt-preserve-explorer'); ?></p>
                    <p><a href="<?php echo admin_url('edit.php?post_type=preserve&page=preserve-filter-options'); ?>" class="button button-primary">
                        <?php _e('Set Up Filter Options', 'dclt-preserve-explorer'); ?>
                    </a></p>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Update filter counts when checkboxes change
            $('.preserve-filter-option input[type="checkbox"]').on('change', function() {
                var filterType = $(this).data('filter-type');
                var checkedCount = $('input[name="preserve_filter_' + filterType + '[]"]:checked').length;
                var countText = checkedCount === 1 ? '(1 selected)' : '(' + checkedCount + ' selected)';
                $('#count-' + filterType).text(countText);
                
                // Update fieldset appearance
                var fieldset = $(this).closest('.preserve-filter-group');
                if (checkedCount > 0) {
                    fieldset.addClass('has-selections');
                } else {
                    fieldset.removeClass('has-selections');
                }
            });
            
            // Initialize fieldset states
            $('.preserve-filter-group').each(function() {
                var fieldset = $(this);
                var checkedInputs = fieldset.find('input[type="checkbox"]:checked');
                if (checkedInputs.length > 0) {
                    fieldset.addClass('has-selections');
                }
            });
        });
        </script>
        
        <style>
        .preserve-filters-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .filter-management-notice {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 4px;
        }
        
        .filter-management-notice p {
            margin: 0;
            color: #0c5460;
            font-size: 14px;
        }
        
        .filter-management-notice a {
            color: #0c5460;
            font-weight: 600;
            text-decoration: none;
        }
        
        .filter-management-notice a:hover {
            text-decoration: underline;
        }
        
        .preserve-filter-group {
            border: 1px solid #c3c4c7;
            border-radius: 6px;
            padding: 16px;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        
        .preserve-filter-group:hover {
            border-color: #8c8f94;
        }
        
        .preserve-filter-group.has-selections {
            border-color: #135e96;
            box-shadow: 0 0 0 1px #135e96;
            background: #f7fbff;
        }
        
        .preserve-filter-group legend {
            padding: 0 12px;
            font-size: 14px;
            color: #1d2327;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;  /* ADD THIS - White background */
            border-radius: 4px; /* ADD THIS - Rounded corners */
        }
        
        .preserve-filter-group.has-selections legend {
            color: #135e96;
            background: #f7fbff;
        }
        
        .filter-icon {
            font-size: 16px;
        }
        
        .filter-count {
            font-size: 12px;
            font-weight: normal;
            color: #646970;
        }
        
        .preserve-filter-group.has-selections .filter-count {
            color: #135e96;
            font-weight: 600;
        }
        
        .filter-description {
            margin: 8px 0 16px 0;
            color: #646970;
            font-size: 13px;
            font-style: italic;
        }
        
        .filter-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            max-width: 600px;
            gap: 8px 16px;
        }
        
        .preserve-filter-option {
            margin: 0;
        }
        
        .preserve-filter-option label {
            display: flex;
            align-items: center;
            font-size: 13px;
            font-weight: normal;
            cursor: pointer;
            color: #1d2327;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 3px;
            transition: all 0.2s ease;
        }
        
        .preserve-filter-option label:hover {
            background: #f6f7f7;
            color: #135e96;
        }
        
        .preserve-filter-option input[type="checkbox"] {
            margin: 0;
        }
        
        .filter-note {
            margin-top: 12px;
            padding: 8px 12px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            font-size: 12px;
            color: #856404;
        }
        
        .filter-note strong {
            color: #533f03;
        }
        
        .no-options-message, .no-filters-message {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #666;
        }
        
        .no-options-message a, .no-filters-message a {
            color: #0073aa;
            text-decoration: none;
            font-weight: 600;
        }
        
        .no-options-message a:hover, .no-filters-message a:hover {
            text-decoration: underline;
        }
        
        /* Responsive adjustments */
        @media (max-width: 782px) {
            .filter-options-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
    }
    
    /**
     * Files Meta Box Callback
     */
    public function files_meta_box_callback($post) {
        wp_nonce_field('preserve_files_nonce', 'preserve_files_nonce');
        
        // Get current file values
        $boundary_file = get_post_meta($post->ID, '_preserve_boundary_file', true);
        $trail_file = get_post_meta($post->ID, '_preserve_trail_file', true);
        ?>
        
        <table class="form-table">
            <tr>
                <th><label for="preserve_boundary_file"><?php _e('Preserve Boundary', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="text" id="preserve_boundary_file" name="preserve_boundary_file" 
                           value="<?php echo esc_attr($boundary_file); ?>" class="large-text" />
                    <button type="button" class="button" data-upload-target="preserve_boundary_file">
                        <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                    </button>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_trail_file"><?php _e('Main Trails', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="text" id="preserve_trail_file" name="preserve_trail_file" 
                           value="<?php echo esc_attr($trail_file); ?>" class="large-text" />
                    <button type="button" class="button" data-upload-target="preserve_trail_file">
                        <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                    </button>
                </td>
            </tr>
        </table>
        
        <script>
        jQuery(document).ready(function($) {
            $('[data-upload-target]').click(function(e) {
                e.preventDefault();
                var targetField = $(this).data('upload-target');
                var mediaUploader = wp.media({
                    title: 'Choose GeoJSON File',
                    button: { text: 'Use This File' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#' + targetField).val(attachment.url);
                });
                
                mediaUploader.open();
            });
        });
        </script>
        <?php
    }


    /**
     * Add Photo Gallery meta box
     */
    public function add_photo_gallery_meta_box() {
        add_meta_box(
            'preserve_photo_gallery',
            __('Photo Gallery', 'dclt-preserve-explorer'),
            array($this, 'photo_gallery_meta_box_callback'),
            'preserve',
            'normal',
            'default'
        );
    }

    /**
     * Photo Gallery meta box callback
     */
    public function photo_gallery_meta_box_callback($post) {
        wp_nonce_field('preserve_photo_gallery_nonce', 'preserve_photo_gallery_nonce');
        
        $gallery_images = get_post_meta($post->ID, '_preserve_gallery_images', true);
        $gallery_images = $gallery_images ? $gallery_images : array();
        
        ?>
        <div id="preserve-photo-gallery-container">
            <div id="preserve-gallery-images" class="preserve-gallery-grid">
                <?php if (!empty($gallery_images)): ?>
                    <?php foreach ($gallery_images as $index => $image_id): ?>
                        <?php $image = wp_get_attachment_image_src($image_id, 'medium'); ?>
                        <?php if ($image): ?>
                            <div class="gallery-image-item" data-image-id="<?php echo esc_attr($image_id); ?>">
                                <img src="<?php echo esc_url($image[0]); ?>" alt="" style="width: 150px; height: 150px; object-fit: cover;" />
                                <div class="gallery-image-controls">
                                    <button type="button" class="button remove-gallery-image" data-image-id="<?php echo esc_attr($image_id); ?>">Remove</button>
                                    <input type="text" name="gallery_captions[<?php echo esc_attr($image_id); ?>]" 
                                           value="<?php echo esc_attr(get_post_meta($post->ID, "_preserve_gallery_caption_{$image_id}", true)); ?>" 
                                           placeholder="Photo caption..." style="width: 100%; margin-top: 5px;" />
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="preserve-gallery-actions">
                <button type="button" id="add-gallery-image" class="button button-secondary">
                    📷 Add Photos
                </button>
                <p class="description">
                    Add photos to showcase this preserve. Images will be displayed in a gallery on the preserve detail page.
                </p>
            </div>
            
            <!-- Hidden input to store gallery image IDs -->
            <input type="hidden" id="preserve-gallery-ids" name="preserve_gallery_images" 
                   value="<?php echo esc_attr(implode(',', $gallery_images)); ?>" />
        </div>
        
        <style>
        .preserve-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            min-height: 80px;
        }
        
        .gallery-image-item {
            position: relative;
            text-align: center;
        }
        
        .gallery-image-controls {
            margin-top: 8px;
        }
        
        .preserve-gallery-actions {
            margin-top: 15px;
        }
        
        #add-gallery-image {
            font-size: 14px;
            padding: 8px 16px;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            var galleryFrame;
            var galleryIds = [];
            
            // Initialize gallery IDs from hidden input
            var existingIds = $('#preserve-gallery-ids').val();
            if (existingIds) {
                galleryIds = existingIds.split(',').filter(id => id.trim() !== '');
            }
            
            // Add gallery image button
            $('#add-gallery-image').on('click', function(e) {
                e.preventDefault();
                
                // Create media frame if it doesn't exist
                if (galleryFrame) {
                    galleryFrame.open();
                    return;
                }
                
                galleryFrame = wp.media({
                    title: 'Select Photos for Gallery',
                    button: {
                        text: 'Add to Gallery'
                    },
                    multiple: true,
                    library: {
                        type: 'image'
                    }
                });
                
                // When images are selected
                galleryFrame.on('select', function() {
                    var selection = galleryFrame.state().get('selection');
                    var $container = $('#preserve-gallery-images');
                    
                    selection.map(function(attachment) {
                        attachment = attachment.toJSON();
                        
                        // Check if image is already in gallery
                        if (galleryIds.indexOf(attachment.id.toString()) === -1) {
                            galleryIds.push(attachment.id.toString());
                            
                            // Add image to grid
                            var imageHtml = '<div class="gallery-image-item" data-image-id="' + attachment.id + '">' +
                                '<img src="' + attachment.sizes.medium.url + '" alt="" style="width: 150px; height: 150px; object-fit: cover;" />' +
                                '<div class="gallery-image-controls">' +
                                    '<button type="button" class="button remove-gallery-image" data-image-id="' + attachment.id + '">Remove</button>' +
                                    '<input type="text" name="gallery_captions[' + attachment.id + ']" value="" placeholder="Photo caption..." style="width: 100%; margin-top: 5px;" />' +
                                '</div>' +
                            '</div>';
                            
                            $container.append(imageHtml);
                        }
                    });
                    
                    // Update hidden input
                    $('#preserve-gallery-ids').val(galleryIds.join(','));
                });
                
                galleryFrame.open();
            });
            
            // Remove image from gallery
            $(document).on('click', '.remove-gallery-image', function(e) {
                e.preventDefault();
                var imageId = $(this).data('image-id').toString();
                var $item = $(this).closest('.gallery-image-item');
                
                // Remove from array
                galleryIds = galleryIds.filter(id => id !== imageId);
                
                // Remove from DOM
                $item.remove();
                
                // Update hidden input
                $('#preserve-gallery-ids').val(galleryIds.join(','));
            });
        });
        </script>
        <?php
    }

    /**
     * Save Photo Gallery meta data
     */
    public function save_photo_gallery_meta($post_id) {
        // Verify nonce
        if (!isset($_POST['preserve_photo_gallery_nonce']) || 
            !wp_verify_nonce($_POST['preserve_photo_gallery_nonce'], 'preserve_photo_gallery_nonce')) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save gallery image IDs
        if (isset($_POST['preserve_gallery_images'])) {
            $gallery_images = sanitize_text_field($_POST['preserve_gallery_images']);
            $gallery_ids = array_filter(explode(',', $gallery_images));
            $gallery_ids = array_map('intval', $gallery_ids); // Ensure integers
            update_post_meta($post_id, '_preserve_gallery_images', $gallery_ids);
        } else {
            delete_post_meta($post_id, '_preserve_gallery_images');
        }
        
        // Save gallery captions
        if (isset($_POST['gallery_captions']) && is_array($_POST['gallery_captions'])) {
            foreach ($_POST['gallery_captions'] as $image_id => $caption) {
                $image_id = intval($image_id);
                $caption = sanitize_text_field($caption);
                
                if (!empty($caption)) {
                    update_post_meta($post_id, "_preserve_gallery_caption_{$image_id}", $caption);
                } else {
                    delete_post_meta($post_id, "_preserve_gallery_caption_{$image_id}");
                }
            }
        }
    }
    
    /**
     * Save Meta Boxes
     * UPDATED: Now validates against dynamic filter options
     */
    public function save_meta_boxes($post_id) {
        // Skip for autosave, revisions, and other non-user saves
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (get_post_type($post_id) !== 'preserve') {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save location fields
        if (isset($_POST['preserve_location_nonce']) && wp_verify_nonce($_POST['preserve_location_nonce'], 'preserve_location_nonce')) {
            $location_fields = ['preserve_lat', 'preserve_lng'];
            foreach ($location_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
        }
        
        // Save detail fields
        if (isset($_POST['preserve_details_nonce']) && wp_verify_nonce($_POST['preserve_details_nonce'], 'preserve_details_nonce')) {
            $detail_fields = ['preserve_acres', 'preserve_trail_length', 'preserve_established'];
            foreach ($detail_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
        }
        
        // Save filter fields (UPDATED: Now validates against dynamic options)
        if (isset($_POST['preserve_filters_nonce']) && wp_verify_nonce($_POST['preserve_filters_nonce'], 'preserve_filters_nonce')) {
            $filter_options = $this->get_filter_options();
            $valid_options = array();
            
            // Build array of valid options for validation from dynamic data
            foreach ($filter_options as $filter_key => $filter_data) {
                if (isset($filter_data['options']) && is_array($filter_data['options'])) {
                    $valid_options[$filter_key] = array_keys($filter_data['options']);
                }
            }
            
            // Process each filter type
            foreach ($valid_options as $filter_key => $valid_keys) {
                $post_key = 'preserve_filter_' . $filter_key;
                $meta_key = '_preserve_filter_' . $filter_key;
                
                if (isset($_POST[$post_key]) && is_array($_POST[$post_key])) {
                    // Sanitize and validate submitted values
                    $submitted_values = array_map('sanitize_key', $_POST[$post_key]);
                    $valid_values = array_intersect($submitted_values, $valid_keys);
                    
                    if (!empty($valid_values)) {
                        // Remove duplicates and reindex array
                        $valid_values = array_values(array_unique($valid_values));
                        update_post_meta($post_id, $meta_key, $valid_values);
                    } else {
                        delete_post_meta($post_id, $meta_key);
                    }
                } else {
                    // No values submitted, delete the meta
                    delete_post_meta($post_id, $meta_key);
                }
            }
        }
        
        // Save file fields
        if (isset($_POST['preserve_files_nonce']) && wp_verify_nonce($_POST['preserve_files_nonce'], 'preserve_files_nonce')) {
            $file_fields = ['preserve_boundary_file', 'preserve_trail_file'];
            foreach ($file_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, esc_url_raw($_POST[$field]));
                }
            }
        }
    }
}