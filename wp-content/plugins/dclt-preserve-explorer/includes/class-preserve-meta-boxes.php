<?php
/**
 * Preserve Meta Boxes
 * 
 * This file contains ALL your meta box functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class DCLT_Preserve_Meta_Boxes {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
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
     * Get filter options
     */
    private function get_filter_options() {
        return array(
            'region' => array(
                'label' => __('Region', 'dclt-preserve-explorer'),
                'options' => array(
                    'northern_door' => __('Northern Door', 'dclt-preserve-explorer'),
                    'central_door' => __('Central Door', 'dclt-preserve-explorer'),
                    'southern_door' => __('Southern Door', 'dclt-preserve-explorer'),
                    'washington_island' => __('Washington Island', 'dclt-preserve-explorer'),
                )
            ),
            'activity' => array(
                'label' => __('Activity', 'dclt-preserve-explorer'),
                'options' => array(
                    'hiking' => __('Hiking', 'dclt-preserve-explorer'),
                    'birdwatching' => __('Birdwatching', 'dclt-preserve-explorer'),
                    'photography' => __('Photography', 'dclt-preserve-explorer'),
                    'nature_study' => __('Nature Study', 'dclt-preserve-explorer'),
                    'wildflower_viewing' => __('Wildflower Viewing', 'dclt-preserve-explorer'),
                    'cross_country_skiing' => __('Cross Country Skiing', 'dclt-preserve-explorer'),
                    'snowshoeing' => __('Snowshoeing', 'dclt-preserve-explorer'),
                )
            ),
            'ecology' => array(
                'label' => __('Ecology', 'dclt-preserve-explorer'),
                'options' => array(
                    'prairie' => __('Prairie', 'dclt-preserve-explorer'),
                    'wetland' => __('Wetland', 'dclt-preserve-explorer'),
                    'forest' => __('Forest', 'dclt-preserve-explorer'),
                    'shoreline' => __('Shoreline', 'dclt-preserve-explorer'),
                    'cedar_swamp' => __('Cedar Swamp', 'dclt-preserve-explorer'),
                    'oak_savanna' => __('Oak Savanna', 'dclt-preserve-explorer'),
                    'limestone_bluff' => __('Limestone Bluff', 'dclt-preserve-explorer'),
                )
            ),
            'difficulty' => array(
                'label' => __('Difficulty Level', 'dclt-preserve-explorer'),
                'options' => array(
                    'easy' => __('Easy', 'dclt-preserve-explorer'),
                    'moderate' => __('Moderate', 'dclt-preserve-explorer'),
                    'difficult' => __('Difficult', 'dclt-preserve-explorer'),
                )
            ),
            'available_facilities' => array(
                'label' => __('Available Facilities', 'dclt-preserve-explorer'),
                'options' => array(
                    'restrooms' => __('Restrooms', 'dclt-preserve-explorer'),
                    'picnic_tables' => __('Picnic Tables', 'dclt-preserve-explorer'),
                    'water_fountains' => __('Water Fountains', 'dclt-preserve-explorer'),
                    'trash_bins' => __('Trash/Recycling Bins', 'dclt-preserve-explorer'),
                    'interpretive_signs' => __('Interpretive Signs', 'dclt-preserve-explorer'),
                    'bike_racks' => __('Bike Racks', 'dclt-preserve-explorer'),
                    'dog_waste_stations' => __('Dog Waste Stations', 'dclt-preserve-explorer'),
                    'parking_available' => __('Parking Available', 'dclt-preserve-explorer'),
                )
            ),
            'trail_surface' => array(
                'label' => __('Trail Surface', 'dclt-preserve-explorer'),
                'options' => array(
                    'paved' => __('Paved', 'dclt-preserve-explorer'),
                    'boardwalk' => __('Boardwalk', 'dclt-preserve-explorer'),
                    'natural_path' => __('Natural Path', 'dclt-preserve-explorer'),
                    'rocky' => __('Rocky', 'dclt-preserve-explorer'),
                    'sandy' => __('Sandy', 'dclt-preserve-explorer'),
                )
            ),
            'accessibility' => array(
                'label' => __('Accessibility', 'dclt-preserve-explorer'),
                'options' => array(
                    'wheelchair_accessible' => __('Wheelchair Accessible', 'dclt-preserve-explorer'),
                    'stroller_friendly' => __('Stroller Friendly', 'dclt-preserve-explorer'),
                    'uneven_terrain' => __('Uneven Terrain', 'dclt-preserve-explorer'),
                    'mobility_challenges' => __('May Be Challenging for Limited Mobility', 'dclt-preserve-explorer'),
                )
            ),
            'physical_challenges' => array(
                'label' => __('Physical Challenges', 'dclt-preserve-explorer'),
                'options' => array(
                    'hills' => __('Hills/Elevation Changes', 'dclt-preserve-explorer'),
                    'water_crossings' => __('Water Crossings', 'dclt-preserve-explorer'),
                    'long_distances' => __('Long Distances', 'dclt-preserve-explorer'),
                    'steep_grades' => __('Steep Grades', 'dclt-preserve-explorer'),
                    'rough_terrain' => __('Rough Terrain', 'dclt-preserve-explorer'),
                )
            ),
            'notable_features' => array(
                'label' => __('Notable Features', 'dclt-preserve-explorer'),
                'options' => array(
                    'waterfalls' => __('Waterfalls', 'dclt-preserve-explorer'),
                    'overlooks' => __('Scenic Overlooks', 'dclt-preserve-explorer'),
                    'historic_sites' => __('Historic Sites', 'dclt-preserve-explorer'),
                    'rare_plants' => __('Rare Plants', 'dclt-preserve-explorer'),
                    'rock_formations' => __('Rock Formations', 'dclt-preserve-explorer'),
                    'caves' => __('Caves', 'dclt-preserve-explorer'),
                    'springs' => __('Natural Springs', 'dclt-preserve-explorer'),
                    'lighthouse' => __('Lighthouse', 'dclt-preserve-explorer'),
                )
            ),
            'photography' => array(
                'label' => __('Photography Opportunities', 'dclt-preserve-explorer'),
                'options' => array(
                    'landscapes' => __('Landscapes', 'dclt-preserve-explorer'),
                    'wildlife' => __('Wildlife', 'dclt-preserve-explorer'),
                    'macro_flowers' => __('Macro/Flowers', 'dclt-preserve-explorer'),
                    'sunrise_sunset' => __('Sunrise/Sunset Spots', 'dclt-preserve-explorer'),
                    'water_reflections' => __('Water Reflections', 'dclt-preserve-explorer'),
                    'seasonal_colors' => __('Seasonal Colors', 'dclt-preserve-explorer'),
                )
            ),
            'educational' => array(
                'label' => __('Educational Features', 'dclt-preserve-explorer'),
                'options' => array(
                    'interpretive_trails' => __('Interpretive Trails', 'dclt-preserve-explorer'),
                    'guided_tours' => __('Guided Tours Available', 'dclt-preserve-explorer'),
                    'educational_signage' => __('Educational Signage', 'dclt-preserve-explorer'),
                    'nature_center' => __('Nature Center', 'dclt-preserve-explorer'),
                    'self_guided_tour' => __('Self-Guided Tour', 'dclt-preserve-explorer'),
                )
            ),
            'wildlife_spotting' => array(
                'label' => __('Wildlife Spotting', 'dclt-preserve-explorer'),
                'options' => array(
                    'birds_of_prey' => __('Birds of Prey', 'dclt-preserve-explorer'),
                    'waterfowl' => __('Waterfowl', 'dclt-preserve-explorer'),
                    'mammals' => __('Mammals', 'dclt-preserve-explorer'),
                    'butterflies' => __('Butterflies', 'dclt-preserve-explorer'),
                    'reptiles' => __('Reptiles', 'dclt-preserve-explorer'),
                    'amphibians' => __('Amphibians', 'dclt-preserve-explorer'),
                    'songbirds' => __('Songbirds', 'dclt-preserve-explorer'),
                )
            ),
            'habitat_diversity' => array(
                'label' => __('Habitat Diversity', 'dclt-preserve-explorer'),
                'options' => array(
                    'multiple_ecosystems' => __('Multiple Ecosystems', 'dclt-preserve-explorer'),
                    'single_habitat' => __('Single Habitat Focus', 'dclt-preserve-explorer'),
                    'transitional_zones' => __('Transitional Zones', 'dclt-preserve-explorer'),
                    'rare_habitats' => __('Rare Habitats', 'dclt-preserve-explorer'),
                )
            ),
            'map_features' => array(
                'label' => __('Map Features & Structures', 'dclt-preserve-explorer'),
                'options' => array(
                    'trail_markers' => __('Trail Markers', 'dclt-preserve-explorer'),
                    'benches' => __('Benches', 'dclt-preserve-explorer'),
                    'observation_decks' => __('Observation Decks', 'dclt-preserve-explorer'),
                    'bridges' => __('Bridges', 'dclt-preserve-explorer'),
                    'shelters' => __('Shelters', 'dclt-preserve-explorer'),
                    'viewing_blinds' => __('Wildlife Viewing Blinds', 'dclt-preserve-explorer'),
                    'kiosks' => __('Information Kiosks', 'dclt-preserve-explorer'),
                    'gates' => __('Gates/Entrances', 'dclt-preserve-explorer'),
                )
            )
        );
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

                    // Load boundary and trail overlays if available
                    <?php 
                    $boundary_file = get_post_meta($post->ID, '_preserve_boundary_file', true);
                    $trail_file = get_post_meta($post->ID, '_preserve_trail_file', true);
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
                    
                    <?php if ($trail_file): ?>
                    setTimeout(function() {
                        $.getJSON('<?php echo esc_js($trail_file); ?>')
                            .done(function(geoJsonData) {
                                L.geoJSON(geoJsonData, {
                                    style: {
                                        color: '#ef4444',
                                        weight: 3,
                                        opacity: 0.8,
                                        dashArray: '5, 5'
                                    }
                                }).addTo(adminMap);
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
            
            // Initialize everything when Leaflet is ready
            if (typeof L !== 'undefined') {
                initAdminMap();
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
            <p><strong><?php _e('Note:', 'dclt-preserve-explorer'); ?></strong> 
            <?php _e('Difficulty level, facilities, and parking information have been moved to the Preserve Filters section for better organization and filtering capabilities.', 'dclt-preserve-explorer'); ?></p>
        </div>
        
        <style>
        .preserve-details-note {
            margin-top: 20px;
            padding: 12px;
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 4px;
        }
        
        .preserve-details-note p {
            margin: 0;
            font-size: 13px;
            color: #0073aa;
        }
        
        .preserve-details-note strong {
            color: #005177;
        }
        </style>
        <?php
    }

    /**
     * Filters Meta Box Callback
     */
    public function filters_meta_box_callback($post) {
        wp_nonce_field('preserve_filters_nonce', 'preserve_filters_nonce');
        
        $filter_options = $this->get_filter_options();
        
        // Get current filter values
        $selected_filters = array();
        foreach ($filter_options as $filter_key => $filter_data) {
            $selected_filters[$filter_key] = (array) get_post_meta($post->ID, '_preserve_filter_' . $filter_key, true);
        }
        ?>
        
        <div class="preserve-filters-container">
            <?php foreach ($filter_options as $filter_key => $filter_data): ?>
                <fieldset class="preserve-filter-group">
                    <legend>
                        <strong><?php echo esc_html($filter_data['label']); ?></strong>
                        <span class="filter-count" id="count-<?php echo esc_attr($filter_key); ?>">
                            (<?php echo count($selected_filters[$filter_key]); ?> selected)
                        </span>
                    </legend>
                    
                    <div class="filter-description">
                        <?php 
                        switch($filter_key) {
                            case 'region':
                                _e('Select the Door County region(s) where this preserve is located.', 'dclt-preserve-explorer');
                                break;
                            case 'activity':
                                _e('Choose activities that visitors can enjoy at this preserve.', 'dclt-preserve-explorer');
                                break;
                            case 'ecology':
                                _e('Select the ecological features and habitats found in this preserve.', 'dclt-preserve-explorer');
                                break;
                            case 'trail_surface':
                                _e('What types of surfaces do the trails have?', 'dclt-preserve-explorer');
                                break;
                            case 'accessibility':
                                _e('How accessible is this preserve for different mobility levels?', 'dclt-preserve-explorer');
                                break;
                            case 'physical_challenges':
                                _e('What physical challenges might visitors encounter?', 'dclt-preserve-explorer');
                                break;
                            case 'notable_features':
                                _e('Special natural or historic features that make this preserve unique.', 'dclt-preserve-explorer');
                                break;
                            case 'photography':
                                _e('What photography opportunities are available here?', 'dclt-preserve-explorer');
                                break;
                            case 'educational':
                                _e('Educational resources and learning opportunities available.', 'dclt-preserve-explorer');
                                break;
                            case 'wildlife_spotting':
                                _e('Types of wildlife commonly seen at this preserve.', 'dclt-preserve-explorer');
                                break;
                            case 'habitat_diversity':
                                _e('How diverse are the habitats within this preserve?', 'dclt-preserve-explorer');
                                break;
                            case 'map_features':
                                _e('Structures and features that can be shown/hidden on maps.', 'dclt-preserve-explorer');
                                break;
                        }
                        ?>
                    </div>
                    
                    <div class="filter-options-grid">
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
                    </div>
                    
                    <?php if ($filter_key === 'region'): ?>
                        <div class="filter-note">
                            <strong><?php _e('Note:', 'dclt-preserve-explorer'); ?></strong> 
                            <?php _e('Most preserves should have at least one region selected.', 'dclt-preserve-explorer'); ?>
                        </div>
                    <?php endif; ?>
                </fieldset>
            <?php endforeach; ?>
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
        }
        
        .preserve-filter-group.has-selections legend {
            color: #135e96;
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
        $accessible_trails_file = get_post_meta($post->ID, '_preserve_accessible_trails_file', true);
        $boardwalk_file = get_post_meta($post->ID, '_preserve_boardwalk_file', true);
        $structures_file = get_post_meta($post->ID, '_preserve_structures_file', true);
        $parking_file = get_post_meta($post->ID, '_preserve_parking_file', true);
        ?>
        
        <div class="preserve-files-container">
            <!-- Core Map Data -->
            <fieldset class="preserve-file-group">
                <legend><strong><?php _e('Core Map Data', 'dclt-preserve-explorer'); ?></strong></legend>
                <p class="description"><?php _e('Essential boundary and trail information for the preserve.', 'dclt-preserve-explorer'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th><label for="preserve_boundary_file"><?php _e('Preserve Boundary', 'dclt-preserve-explorer'); ?></label></th>
                        <td>
                            <input type="text" id="preserve_boundary_file" name="preserve_boundary_file" 
                                   value="<?php echo esc_attr($boundary_file); ?>" class="large-text" />
                            <button type="button" class="button" data-upload-target="preserve_boundary_file">
                                <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                            </button>
                            <p class="description"><?php _e('GeoJSON file defining the preserve boundary (Polygon)', 'dclt-preserve-explorer'); ?></p>
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
                            <p class="description"><?php _e('GeoJSON file with all trail paths (LineString collection)', 'dclt-preserve-explorer'); ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            
            <!-- Accessibility Features -->
            <fieldset class="preserve-file-group">
                <legend><strong><?php _e('Accessibility Features', 'dclt-preserve-explorer'); ?></strong></legend>
                <p class="description"><?php _e('Accessible trail sections and boardwalks for mobility-limited visitors.', 'dclt-preserve-explorer'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th><label for="preserve_accessible_trails_file"><?php _e('Accessible Trail Sections', 'dclt-preserve-explorer'); ?></label></th>
                        <td>
                            <input type="text" id="preserve_accessible_trails_file" name="preserve_accessible_trails_file" 
                                   value="<?php echo esc_attr($accessible_trails_file); ?>" class="large-text" />
                            <button type="button" class="button" data-upload-target="preserve_accessible_trails_file">
                                <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                            </button>
                            <p class="description"><?php _e('Wheelchair/stroller accessible trail segments (LineString)', 'dclt-preserve-explorer'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="preserve_boardwalk_file"><?php _e('Boardwalks', 'dclt-preserve-explorer'); ?></label></th>
                        <td>
                            <input type="text" id="preserve_boardwalk_file" name="preserve_boardwalk_file" 
                                   value="<?php echo esc_attr($boardwalk_file); ?>" class="large-text" />
                            <button type="button" class="button" data-upload-target="preserve_boardwalk_file">
                                <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                            </button>
                            <p class="description"><?php _e('Elevated boardwalk sections (LineString)', 'dclt-preserve-explorer'); ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            
            <!-- Structures & Points of Interest -->
            <fieldset class="preserve-file-group">
                <legend><strong><?php _e('Structures & Points of Interest', 'dclt-preserve-explorer'); ?></strong></legend>
                <p class="description"><?php _e('Facilities, structures, and notable features that can be toggled on maps.', 'dclt-preserve-explorer'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th><label for="preserve_structures_file"><?php _e('Structures & Features', 'dclt-preserve-explorer'); ?></label></th>
                        <td>
                            <input type="text" id="preserve_structures_file" name="preserve_structures_file" 
                                   value="<?php echo esc_attr($structures_file); ?>" class="large-text" />
                            <button type="button" class="button" data-upload-target="preserve_structures_file">
                                <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                            </button>
                            <p class="description">
                                <?php _e('Points for benches, overlooks, bridges, kiosks, etc. (Point collection)', 'dclt-preserve-explorer'); ?>
                                <br><small><strong><?php _e('Tip:', 'dclt-preserve-explorer'); ?></strong> 
                                <?php _e('Include "type" property (bench, overlook, bridge, etc.) for filtering', 'dclt-preserve-explorer'); ?></small>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="preserve_parking_file"><?php _e('Parking Areas', 'dclt-preserve-explorer'); ?></label></th>
                        <td>
                            <input type="text" id="preserve_parking_file" name="preserve_parking_file" 
                                   value="<?php echo esc_attr($parking_file); ?>" class="large-text" />
                            <button type="button" class="button" data-upload-target="preserve_parking_file">
                                <?php _e('Upload GeoJSON', 'dclt-preserve-explorer'); ?>
                            </button>
                            <p class="description"><?php _e('Parking areas and trailheads (Point or Polygon)', 'dclt-preserve-explorer'); ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            
            <!-- Data Format Examples -->
            <fieldset class="preserve-file-group">
                <legend><strong><?php _e('GeoJSON Format Examples', 'dclt-preserve-explorer'); ?></strong></legend>
                <div class="geojson-examples">
                    <details>
                        <summary><?php _e('Structures GeoJSON Example', 'dclt-preserve-explorer'); ?></summary>
                        <pre><code>{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": [-87.3547, 44.7555]
      },
      "properties": {
        "type": "scenic_overlook",
        "name": "Forest Vista Overlook",
        "accessible": false,
        "description": "Scenic view of Door County forest"
      }
    },
    {
      "type": "Feature", 
      "geometry": {
        "type": "Point",
        "coordinates": [-87.3542, 44.7550]
      },
      "properties": {
        "type": "bench",
        "accessible": true,
        "material": "wood"
      }
    }
  ]
}</code></pre>
                    </details>
                    
                    <details>
                        <summary><?php _e('Structure Types Reference', 'dclt-preserve-explorer'); ?></summary>
                        <div class="structure-types">
                            <p><strong><?php _e('Recommended "type" values:', 'dclt-preserve-explorer'); ?></strong></p>
                            <ul>
                                <li><code>trailhead</code> - <?php _e('Trail starting points', 'dclt-preserve-explorer'); ?></li>
                                <li><code>scenic_overlook</code> - <?php _e('Viewing areas', 'dclt-preserve-explorer'); ?></li>
                                <li><code>bench</code> - <?php _e('Seating areas', 'dclt-preserve-explorer'); ?></li>
                                <li><code>bridge</code> - <?php _e('Stream/ravine crossings', 'dclt-preserve-explorer'); ?></li>
                                <li><code>observation_deck</code> - <?php _e('Elevated viewing platforms', 'dclt-preserve-explorer'); ?></li>
                                <li><code>shelter</code> - <?php _e('Weather protection', 'dclt-preserve-explorer'); ?></li>
                                <li><code>kiosk</code> - <?php _e('Information displays', 'dclt-preserve-explorer'); ?></li>
                                <li><code>viewing_blind</code> - <?php _e('Wildlife observation', 'dclt-preserve-explorer'); ?></li>
                                <li><code>gate</code> - <?php _e('Entry/exit points', 'dclt-preserve-explorer'); ?></li>
                            </ul>
                        </div>
                    </details>
                </div>
            </fieldset>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Unified media uploader for all GeoJSON files
            $('[data-upload-target]').click(function(e) {
                e.preventDefault();
                
                var targetField = $(this).data('upload-target');
                var fieldLabel = $('label[for="' + targetField + '"]').text();
                
                var mediaUploader = wp.media({
                    title: '<?php _e('Choose GeoJSON File', 'dclt-preserve-explorer'); ?>' + ' - ' + fieldLabel,
                    button: { text: '<?php _e('Use This File', 'dclt-preserve-explorer'); ?>' },
                    multiple: false,
                    library: {
                        type: ['application/json', 'text/plain']
                    }
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#' + targetField).val(attachment.url);
                    
                    // Visual feedback
                    $('#' + targetField).addClass('file-updated');
                    setTimeout(function() {
                        $('#' + targetField).removeClass('file-updated');
                    }, 2000);
                });
                
                mediaUploader.open();
            });
        });
        </script>
        
        <style>
        .preserve-files-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        .preserve-file-group {
            border: 1px solid #c3c4c7;
            border-radius: 6px;
            padding: 20px;
            background: #fff;
        }
        
        .preserve-file-group legend {
            padding: 0 12px;
            font-size: 14px;
            color: #1d2327;
        }
        
        .preserve-file-group > .description {
            margin: 8px 0 16px 0;
            color: #646970;
            font-size: 13px;
            font-style: italic;
        }
        
        .preserve-file-group .form-table {
            margin-top: 0;
        }
        
        .preserve-file-group .form-table th {
            width: 200px;
            font-weight: 600;
        }
        
        .preserve-file-group .button {
            margin-left: 8px;
        }
        
        .file-updated {
            border-color: #00a32a !important;
            box-shadow: 0 0 0 1px #00a32a !important;
            background-color: #f0f6fc !important;
            transition: all 0.3s ease;
        }
        
        .geojson-examples {
            margin-top: 12px;
        }
        
        .geojson-examples details {
            margin-bottom: 16px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        
        .geojson-examples summary {
            padding: 12px;
            cursor: pointer;
            background: #f8f9fa;
            font-weight: 600;
            border-radius: 4px 4px 0 0;
        }
        
        .geojson-examples summary:hover {
            background: #e9ecef;
        }
        
        .geojson-examples pre {
            margin: 0;
            padding: 16px;
            background: #2d3748;
            color: #e2e8f0;
            border-radius: 0 0 4px 4px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.5;
        }
        
        .structure-types {
            padding: 16px;
        }
        
        .structure-types ul {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 8px;
            margin: 12px 0;
        }
        
        .structure-types li {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .structure-types code {
            background: #f1f3f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 11px;
            color: #d73502;
            min-width: 120px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 782px) {
            .preserve-file-group .form-table th {
                width: auto;
            }
            
            .structure-types ul {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
    }
    
    /**
     * Save Meta Boxes
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
        
        // Save filter fields
        if (isset($_POST['preserve_filters_nonce']) && wp_verify_nonce($_POST['preserve_filters_nonce'], 'preserve_filters_nonce')) {
            $filter_options = $this->get_filter_options();
            $valid_options = array();
            
            // Build array of valid options for validation
            foreach ($filter_options as $filter_key => $filter_data) {
                $valid_options[$filter_key] = array_keys($filter_data['options']);
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
            $file_fields = [
                'preserve_boundary_file', 
                'preserve_trail_file',
                'preserve_accessible_trails_file',
                'preserve_boardwalk_file',
                'preserve_structures_file',
                'preserve_parking_file'
            ];
            foreach ($file_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, esc_url_raw($_POST[$field]));
                }
            }
        }
    }
    
    /**
     * Get preserve filters for a specific post (utility method)
     * 
     * @param int $post_id The post ID
     * @param string $filter_type Optional. Specific filter type to retrieve
     * @return array Array of filter values
     */
    public function get_preserve_filters($post_id, $filter_type = null) {
        if ($filter_type) {
            return (array) get_post_meta($post_id, '_preserve_filter_' . $filter_type, true);
        }
        
        $filter_options = $this->get_filter_options();
        $filters = array();
        
        foreach ($filter_options as $filter_key => $filter_data) {
            $filters[$filter_key] = (array) get_post_meta($post_id, '_preserve_filter_' . $filter_key, true);
        }
        
        return $filters;
    }
    
    /**
     * Check if preserve has specific filter value (utility method)
     * 
     * @param int $post_id The post ID
     * @param string $filter_type The filter type (region, activity, ecology)
     * @param string $filter_value The filter value to check for
     * @return bool True if preserve has the filter value
     */
    public function preserve_has_filter($post_id, $filter_type, $filter_value) {
        $filter_values = $this->get_preserve_filters($post_id, $filter_type);
        return in_array($filter_value, $filter_values);
    }
}