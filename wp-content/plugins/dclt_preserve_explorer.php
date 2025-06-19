<?php
/**
 * Plugin Name: Door County Preserve Explorer
 * Plugin URI: https://github.com/doorcountylandtrust/preserve-explorer
 * Description: A comprehensive preserve management and exploration system for Door County Land Trust
 * Version: 1.0.0
 * Author: Door County Land Trust
 * License: GPL v2 or later
 * Text Domain: dclt-preserve-explorer
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DCLT_PRESERVE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DCLT_PRESERVE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DCLT_PRESERVE_VERSION', '1.0.0');

/**
 * Main Plugin Class
 */
class DCLT_Preserve_Explorer {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('dclt-preserve-explorer', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize components
        $this->register_post_types();
        $this->register_taxonomies();
        $this->register_meta_boxes();
        $this->register_rest_fields();
        $this->register_shortcodes();
        
        // Add admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }
    
    /**
     * Register Custom Post Types
     */
    public function register_post_types() {
        $args = array(
            'labels' => array(
                'name' => __('Preserves', 'dclt-preserve-explorer'),
                'singular_name' => __('Preserve', 'dclt-preserve-explorer'),
                'add_new' => __('Add New Preserve', 'dclt-preserve-explorer'),
                'add_new_item' => __('Add New Preserve', 'dclt-preserve-explorer'),
                'edit_item' => __('Edit Preserve', 'dclt-preserve-explorer'),
                'new_item' => __('New Preserve', 'dclt-preserve-explorer'),
                'view_item' => __('View Preserve', 'dclt-preserve-explorer'),
                'search_items' => __('Search Preserves', 'dclt-preserve-explorer'),
                'not_found' => __('No preserves found', 'dclt-preserve-explorer'),
                'not_found_in_trash' => __('No preserves found in trash', 'dclt-preserve-explorer')
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rest_base' => 'preserves',
            'query_var' => true,
            'rewrite' => array('slug' => 'preserve'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-location-alt',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'taxonomies' => array('preserve_type', 'preserve_activity')
        );
        
        register_post_type('preserve', $args);
    }
    
    /**
     * Register Custom Taxonomies
     */
    public function register_taxonomies() {
        // Preserve Types (Forest, Wetland, Prairie, etc.)
        register_taxonomy('preserve_type', 'preserve', array(
            'labels' => array(
                'name' => __('Preserve Types', 'dclt-preserve-explorer'),
                'singular_name' => __('Preserve Type', 'dclt-preserve-explorer'),
            ),
            'public' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'preserve-type'),
        ));
        
        // Activities (Hiking, Photography, Birdwatching, etc.)
        register_taxonomy('preserve_activity', 'preserve', array(
            'labels' => array(
                'name' => __('Activities', 'dclt-preserve-explorer'),
                'singular_name' => __('Activity', 'dclt-preserve-explorer'),
            ),
            'public' => true,
            'show_in_rest' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'activity'),
        ));
    }
    
    /**
     * Register Meta Boxes
     */
    public function register_meta_boxes() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }
    
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
            'preserve_files',
            __('GeoJSON & Trail Files', 'dclt-preserve-explorer'),
            array($this, 'files_meta_box_callback'),
            'preserve',
            'normal',
            'high'
        );
    }
    

// Add this to your existing location_meta_box_callback function
// Replace the existing function with this enhanced version

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
        <tr>
            <th><?php _e('Map Preview', 'dclt-preserve-explorer'); ?></th>
            <td>
                <div id="admin-map-container">
                    <div id="admin-map" style="height: 400px; width: 100%; border: 1px solid #ddd; border-radius: 4px;"></div>
                    <p class="description" style="margin-top: 10px;">
                        <?php _e('Map updates automatically when you enter coordinates above. You can also click on the map to set coordinates.', 'dclt-preserve-explorer'); ?>
                    </p>
                </div>
                <div id="map-loading" style="display: none; text-align: center; padding: 50px;">
                    <p><?php _e('Loading map...', 'dclt-preserve-explorer'); ?></p>
                </div>
                <div id="map-error" style="display: none; color: #d63638; padding: 20px; background: #f8f9fa; border-radius: 4px;">
                    <p><strong><?php _e('Map could not be loaded.', 'dclt-preserve-explorer'); ?></strong></p>
                    <p><?php _e('Please check your internet connection or enter coordinates manually.', 'dclt-preserve-explorer'); ?></p>
                </div>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        let adminMap;
        let currentMarker;
        let doorCountyBounds = {
            north: 45.35,
            south: 44.65,
            east: -86.80,
            west: -87.80
        };
        
        // Initialize map
        function initAdminMap() {
            try {
                $('#map-loading').show();
                $('#admin-map-container').hide();
                
                // Default to Door County center
                let defaultLat = <?php echo $lat ?: '44.75'; ?>;
                let defaultLng = <?php echo $lng ?: '-87.35'; ?>;
                
                adminMap = L.map('admin-map').setView([defaultLat, defaultLng], <?php echo ($lat && $lng) ? '18' : '10'; ?>);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18
                }).addTo(adminMap);
                
                // Add existing marker if coordinates exist
                <?php if ($lat && $lng): ?>
                updateMapMarker(<?php echo $lat; ?>, <?php echo $lng; ?>, false);
                <?php endif; ?>
                
                // Click handler for setting coordinates
                adminMap.on('click', function(e) {
                    let lat = e.latlng.lat.toFixed(6);
                    let lng = e.latlng.lng.toFixed(6);
                    
                    // Check if click is within Door County area
                    if (lat > doorCountyBounds.north || lat < doorCountyBounds.south || 
                        lng > doorCountyBounds.east || lng < doorCountyBounds.west) {
                        if (!confirm('This location appears to be outside Door County. Continue?')) {
                            return;
                        }
                    }
                    
                    updateMapMarker(lat, lng, true);
                    $('#preserve_lat').val(lat);
                    $('#preserve_lng').val(lng);
                });

                // After the map loads, if there's a boundary file, fit to those bounds
<?php 
$boundary_file = get_post_meta($post->ID, '_preserve_boundary_file', true);
if ($boundary_file): 
?>
setTimeout(function() {
    $.getJSON('<?php echo esc_js($boundary_file); ?>')
        .done(function(geoJsonData) {
            var boundaryLayer = L.geoJSON(geoJsonData, {
                style: { color: '#22c55e', weight: 2, fillOpacity: 0.1 }
            }).addTo(adminMap);
            
            // Zoom to fit the boundary
            adminMap.fitBounds(boundaryLayer.getBounds(), { padding: [20, 20] });
        });
}, 500);
<?php endif; ?>
                
                $('#map-loading').hide();
                $('#admin-map-container').show();
                
                // Force map to recalculate size
                setTimeout(() => {
                    adminMap.invalidateSize();
                }, 100);
                
            } catch (error) {
                console.error('Error initializing admin map:', error);
                $('#map-loading').hide();
                $('#map-error').show();
            }
        }
        
        // Update marker position
        function updateMapMarker(lat, lng, panTo = true) {
            if (currentMarker) {
                adminMap.removeLayer(currentMarker);
            }
            
            currentMarker = L.marker([lat, lng], {
                draggable: true,
                title: 'Preserve Location (drag to adjust)'
            }).addTo(adminMap);
            
            // Handle marker dragging
            currentMarker.on('dragend', function(e) {
                let position = e.target.getLatLng();
                let newLat = position.lat.toFixed(6);
                let newLng = position.lng.toFixed(6);
                
                $('#preserve_lat').val(newLat);
                $('#preserve_lng').val(newLng);
            });
            
            if (panTo) {
                adminMap.setView([lat, lng], Math.max(adminMap.getZoom(), 12));
            }
        }
        
        // Input field handlers
        $('#preserve_lat, #preserve_lng').on('input', function() {
            let lat = parseFloat($('#preserve_lat').val());
            let lng = parseFloat($('#preserve_lng').val());
            
            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                updateMapMarker(lat, lng, true);
            }
        });
        
        // Geocoding helper (optional enhancement)
        function addGeocodeButton() {
            let geocodeBtn = $('<button type="button" class="button" style="margin-left: 10px;">Find Address</button>');
            $('#preserve_lng').parent().append(geocodeBtn);
            
            geocodeBtn.on('click', function() {
                let address = prompt('Enter an address to find coordinates:');
                if (address) {
                    // Simple geocoding using Nominatim (free service)
                    $.getJSON('https://nominatim.openstreetmap.org/search', {
                        q: address + ', Door County, Wisconsin',
                        format: 'json',
                        limit: 1
                    }).done(function(data) {
                        if (data.length > 0) {
                            let lat = parseFloat(data[0].lat);
                            let lng = parseFloat(data[0].lon);
                            $('#preserve_lat').val(lat.toFixed(6));
                            $('#preserve_lng').val(lng.toFixed(6));
                            updateMapMarker(lat, lng, true);
                        } else {
                            alert('Address not found. Please try a different address.');
                        }
                    }).fail(function() {
                        alert('Geocoding service unavailable. Please enter coordinates manually.');
                    });
                }
            });
        }
        
        // Load boundary data if available
        function loadBoundaryOverlay() {
            <?php 
            $boundary_file = get_post_meta($post->ID, '_preserve_boundary_file', true);
            if ($boundary_file): 
            ?>
            $.getJSON('<?php echo esc_js($boundary_file); ?>')
                .done(function(geoJsonData) {
                    L.geoJSON(geoJsonData, {
                        style: {
                            color: '#22c55e',
                            weight: 2,
                            fillOpacity: 0.1,
                            opacity: 0.8
                        }
                    }).addTo(adminMap);
                })
                .fail(function() {
                    console.log('Could not load boundary data for map preview');
                });
            <?php endif; ?>
        }
        
        // Initialize everything when Leaflet is ready
        if (typeof L !== 'undefined') {
            initAdminMap();
            addGeocodeButton();
            setTimeout(loadBoundaryOverlay, 1000); // Load boundary after map is ready
        } else {
            $('#map-error').show();
            console.error('Leaflet library not loaded');
        }
    });
    </script>
    
    <style>
    #admin-map {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .leaflet-container {
        font-family: inherit;
    }
    
    .leaflet-popup-content {
        font-size: 13px;
    }
    
    #admin-map-container .description {
        font-style: italic;
        color: #666;
    }
    </style>
    <?php
}

    
    public function details_meta_box_callback($post) {
        wp_nonce_field('preserve_details_nonce', 'preserve_details_nonce');
        
        $acres = get_post_meta($post->ID, '_preserve_acres', true);
        $difficulty = get_post_meta($post->ID, '_preserve_difficulty', true);
        $trail_length = get_post_meta($post->ID, '_preserve_trail_length', true);
        $established = get_post_meta($post->ID, '_preserve_established', true);
        $parking = get_post_meta($post->ID, '_preserve_parking', true);
        $facilities = get_post_meta($post->ID, '_preserve_facilities', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="preserve_acres"><?php _e('Size (Acres)', 'dclt-preserve-explorer'); ?></label></th>
                <td><input type="number" id="preserve_acres" name="preserve_acres" value="<?php echo esc_attr($acres); ?>" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="preserve_difficulty"><?php _e('Difficulty Level', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <select id="preserve_difficulty" name="preserve_difficulty">
                        <option value=""><?php _e('Select Difficulty', 'dclt-preserve-explorer'); ?></option>
                        <option value="easy" <?php selected($difficulty, 'easy'); ?>><?php _e('Easy', 'dclt-preserve-explorer'); ?></option>
                        <option value="moderate" <?php selected($difficulty, 'moderate'); ?>><?php _e('Moderate', 'dclt-preserve-explorer'); ?></option>
                        <option value="difficult" <?php selected($difficulty, 'difficult'); ?>><?php _e('Difficult', 'dclt-preserve-explorer'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_trail_length"><?php _e('Trail Length (miles)', 'dclt-preserve-explorer'); ?></label></th>
                <td><input type="number" step="0.1" id="preserve_trail_length" name="preserve_trail_length" value="<?php echo esc_attr($trail_length); ?>" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="preserve_established"><?php _e('Year Established', 'dclt-preserve-explorer'); ?></label></th>
                <td><input type="number" id="preserve_established" name="preserve_established" value="<?php echo esc_attr($established); ?>" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="preserve_parking"><?php _e('Parking Available', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="checkbox" id="preserve_parking" name="preserve_parking" value="1" <?php checked($parking, '1'); ?> />
                    <label for="preserve_parking"><?php _e('Yes, parking is available', 'dclt-preserve-explorer'); ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_facilities"><?php _e('Available Facilities', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <textarea id="preserve_facilities" name="preserve_facilities" rows="3" class="large-text"><?php echo esc_textarea($facilities); ?></textarea>
                    <p class="description"><?php _e('List available facilities (restrooms, picnic tables, etc.)', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function files_meta_box_callback($post) {
        wp_nonce_field('preserve_files_nonce', 'preserve_files_nonce');
        
        $boundary_file = get_post_meta($post->ID, '_preserve_boundary_file', true);
        $trail_file = get_post_meta($post->ID, '_preserve_trail_file', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="preserve_boundary_file"><?php _e('Boundary GeoJSON File', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="text" id="preserve_boundary_file" name="preserve_boundary_file" value="<?php echo esc_attr($boundary_file); ?>" class="large-text" />
                    <button type="button" class="button" id="upload_boundary_file"><?php _e('Upload File', 'dclt-preserve-explorer'); ?></button>
                    <p class="description"><?php _e('Upload a GeoJSON file defining the preserve boundary', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="preserve_trail_file"><?php _e('Trail GeoJSON File', 'dclt-preserve-explorer'); ?></label></th>
                <td>
                    <input type="text" id="preserve_trail_file" name="preserve_trail_file" value="<?php echo esc_attr($trail_file); ?>" class="large-text" />
                    <button type="button" class="button" id="upload_trail_file"><?php _e('Upload File', 'dclt-preserve-explorer'); ?></button>
                    <p class="description"><?php _e('Upload a GeoJSON file defining preserve trails', 'dclt-preserve-explorer'); ?></p>
                </td>
            </tr>
        </table>
        
        <script>
        jQuery(document).ready(function($) {
            // Media uploader for boundary file
            $('#upload_boundary_file').click(function(e) {
                e.preventDefault();
                var mediaUploader = wp.media({
                    title: '<?php _e('Choose GeoJSON File', 'dclt-preserve-explorer'); ?>',
                    button: { text: '<?php _e('Choose File', 'dclt-preserve-explorer'); ?>' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#preserve_boundary_file').val(attachment.url);
                });
                
                mediaUploader.open();
            });
            
            // Media uploader for trail file
            $('#upload_trail_file').click(function(e) {
                e.preventDefault();
                var mediaUploader = wp.media({
                    title: '<?php _e('Choose GeoJSON File', 'dclt-preserve-explorer'); ?>',
                    button: { text: '<?php _e('Choose File', 'dclt-preserve-explorer'); ?>' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#preserve_trail_file').val(attachment.url);
                });
                
                mediaUploader.open();
            });
        });
        </script>
        <?php
    }
    
    public function save_meta_boxes($post_id) {
        // Check nonces and permissions
        if (!isset($_POST['preserve_location_nonce']) || !wp_verify_nonce($_POST['preserve_location_nonce'], 'preserve_location_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save location fields
        $location_fields = ['preserve_lat', 'preserve_lng'];
        foreach ($location_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Save detail fields
        $detail_fields = ['preserve_acres', 'preserve_difficulty', 'preserve_trail_length', 'preserve_established', 'preserve_facilities'];
        foreach ($detail_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Save parking checkbox
        update_post_meta($post_id, '_preserve_parking', isset($_POST['preserve_parking']) ? '1' : '');
        
        // Save file fields
        $file_fields = ['preserve_boundary_file', 'preserve_trail_file'];
        foreach ($file_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, esc_url_raw($_POST[$field]));
            }
        }
    }
    
    /**
     * Register REST API Fields
     */
    public function register_rest_fields() {
        register_rest_field('preserve', 'preserve_data', array(
            'get_callback' => array($this, 'get_preserve_rest_data'),
            'schema' => array(
                'description' => __('Preserve specific data', 'dclt-preserve-explorer'),
                'type' => 'object'
            )
        ));
    }
    
    public function get_preserve_rest_data($post) {
        return array(
            'lat' => get_post_meta($post['id'], '_preserve_lat', true),
            'lng' => get_post_meta($post['id'], '_preserve_lng', true),
            'acres' => get_post_meta($post['id'], '_preserve_acres', true),
            'difficulty' => get_post_meta($post['id'], '_preserve_difficulty', true),
            'trail_length' => get_post_meta($post['id'], '_preserve_trail_length', true),
            'established' => get_post_meta($post['id'], '_preserve_established', true),
            'parking' => get_post_meta($post['id'], '_preserve_parking', true),
            'facilities' => get_post_meta($post['id'], '_preserve_facilities', true),
            'boundary_file' => get_post_meta($post['id'], '_preserve_boundary_file', true),
            'trail_file' => get_post_meta($post['id'], '_preserve_trail_file', true),
            'featured_image' => get_the_post_thumbnail_url($post['id'], 'large'),
        );
    }
    
    /**
     * Register Shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('preserve_explorer', array($this, 'preserve_explorer_shortcode'));
        add_shortcode('preserve_map', array($this, 'preserve_map_shortcode'));
        add_shortcode('preserve_list', array($this, 'preserve_list_shortcode'));
    }
    
    public function preserve_explorer_shortcode($atts) {
        $atts = shortcode_atts(array(
            'view' => 'both', // map, list, both
            'filter' => 'true', // true, false
            'search' => 'true', // true, false
            'height' => '600px'
        ), $atts);
        
        // Enqueue scripts and styles
        wp_enqueue_script('dclt-preserve-explorer');
        wp_enqueue_style('dclt-preserve-explorer');
        
        ob_start();
        include DCLT_PRESERVE_PLUGIN_PATH . 'templates/preserve-explorer.php';
        return ob_get_clean();
    }
    
    /**
     * Admin Menu
     */
    public function admin_menu() {
        add_submenu_page(
            'edit.php?post_type=preserve',
            __('Settings', 'dclt-preserve-explorer'),
            __('Settings', 'dclt-preserve-explorer'),
            'manage_options',
            'preserve-settings',
            array($this, 'admin_settings_page')
        );
        
        add_submenu_page(
            'edit.php?post_type=preserve',
            __('Import/Export', 'dclt-preserve-explorer'),
            __('Import/Export', 'dclt-preserve-explorer'),
            'manage_options',
            'preserve-import-export',
            array($this, 'admin_import_export_page')
        );
    }
    
    public function admin_settings_page() {
        echo '<div class="wrap">';
        echo '<h1>' . __('Preserve Explorer Settings', 'dclt-preserve-explorer') . '</h1>';
        echo '<p>' . __('Settings for the Preserve Explorer plugin will go here.', 'dclt-preserve-explorer') . '</p>';
        echo '</div>';
    }
    
    public function admin_import_export_page() {
        echo '<div class="wrap">';
        echo '<h1>' . __('Import/Export Preserves', 'dclt-preserve-explorer') . '</h1>';
        echo '<p>' . __('Import and export functionality will go here.', 'dclt-preserve-explorer') . '</p>';
        echo '</div>';
    }
    
    /**
     * Enqueue Scripts and Styles
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
    
    public function frontend_scripts() {
        if (is_singular('preserve') || is_post_type_archive('preserve') || has_shortcode(get_post()->post_content, 'preserve_explorer')) {
            wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
            wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
            
            wp_enqueue_script('dclt-preserve-explorer', DCLT_PRESERVE_PLUGIN_URL . 'assets/js/preserve-explorer.js', array('jquery'), DCLT_PRESERVE_VERSION, true);
            wp_enqueue_style('dclt-preserve-explorer', DCLT_PRESERVE_PLUGIN_URL . 'assets/css/preserve-explorer.css', array(), DCLT_PRESERVE_VERSION);
            
            // Localize script with WordPress data
            wp_localize_script('dclt-preserve-explorer', 'dcltPreserveData', array(
                'apiUrl' => rest_url('wp/v2/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'pluginUrl' => DCLT_PRESERVE_PLUGIN_URL
            ));
        }
    }
    
    /**
     * Plugin Activation
     */
    public function activate() {
        $this->register_post_types();
        $this->register_taxonomies();
        flush_rewrite_rules();
        
        // Create default terms
        $this->create_default_terms();
    }
    
    private function create_default_terms() {
        // Default preserve types
        $preserve_types = array('Forest', 'Wetland', 'Prairie', 'Coastal', 'Mixed');
        foreach ($preserve_types as $type) {
            if (!term_exists($type, 'preserve_type')) {
                wp_insert_term($type, 'preserve_type');
            }
        }
        
        // Default activities
        $activities = array('Hiking', 'Photography', 'Birdwatching', 'Nature Study', 'Family Activities');
        foreach ($activities as $activity) {
            if (!term_exists($activity, 'preserve_activity')) {
                wp_insert_term($activity, 'preserve_activity');
            }
        }
    }
    
    /**
     * Plugin Deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new DCLT_Preserve_Explorer();


?>

