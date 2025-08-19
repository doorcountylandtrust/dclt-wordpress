<?php
/**
 * Custom Analytics System for DCLT Preserve Explorer
 * Add this to your WordPress plugin
 */

class DCLT_Analytics {
    
    public function __construct() {
        add_action('init', [$this, 'create_analytics_table']);
        add_action('wp_ajax_dclt_track_event', [$this, 'track_event']);
        add_action('wp_ajax_nopriv_dclt_track_event', [$this, 'track_event']);
        add_action('admin_menu', [$this, 'add_analytics_menu']);
    }
    
    /**
     * Create analytics table on plugin activation
     */
    public function create_analytics_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'dclt_analytics';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            event_name varchar(255) NOT NULL,
            event_data longtext,
            preserve_name varchar(255),
            user_ip varchar(45),
            user_agent text,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_name (event_name),
            KEY preserve_name (preserve_name),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Track an analytics event
     */
    public function track_event() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'dclt_analytics_nonce')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'dclt_analytics';
        
        $event_name = sanitize_text_field($_POST['event_name']);
        $event_data = json_encode($_POST['event_data']);
        $preserve_name = sanitize_text_field($_POST['preserve_name'] ?? '');
        $user_ip = $this->get_user_ip();
        $user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '');
        
        $wpdb->insert(
            $table_name,
            [
                'event_name' => $event_name,
                'event_data' => $event_data,
                'preserve_name' => $preserve_name,
                'user_ip' => $user_ip,
                'user_agent' => $user_agent
            ]
        );
        
        wp_send_json_success(['message' => 'Event tracked']);
    }
    
    /**
     * Get user IP (privacy-conscious)
     */
    private function get_user_ip() {
        // Hash the IP for privacy
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return hash('sha256', $ip . 'dclt_salt'); // Hash for privacy
    }
    
    /**
     * Add analytics menu to WordPress admin
     */
    public function add_analytics_menu() {
        add_submenu_page(
            'edit.php?post_type=preserve',
            'Analytics',
            'Analytics',
            'manage_options',
            'dclt-analytics',
            [$this, 'analytics_dashboard']
        );
    }
    
    /**
     * Analytics dashboard
     */
    public function analytics_dashboard() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dclt_analytics';
        
        // Get date range
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        
        // Top events
        $top_events = $wpdb->get_results($wpdb->prepare("
            SELECT event_name, COUNT(*) as count 
            FROM $table_name 
            WHERE DATE(timestamp) BETWEEN %s AND %s
            GROUP BY event_name 
            ORDER BY count DESC 
            LIMIT 10
        ", $start_date, $end_date));
        
        // Top preserves
        $top_preserves = $wpdb->get_results($wpdb->prepare("
            SELECT preserve_name, COUNT(*) as count 
            FROM $table_name 
            WHERE preserve_name != '' 
            AND DATE(timestamp) BETWEEN %s AND %s
            GROUP BY preserve_name 
            ORDER BY count DESC 
            LIMIT 10
        ", $start_date, $end_date));
        
        // Filter usage
        $filter_usage = $wpdb->get_results($wpdb->prepare("
            SELECT 
                JSON_EXTRACT(event_data, '$.type') as filter_type,
                JSON_EXTRACT(event_data, '$.value') as filter_value,
                COUNT(*) as count
            FROM $table_name 
            WHERE event_name = 'Filter Used'
            AND DATE(timestamp) BETWEEN %s AND %s
            GROUP BY filter_type, filter_value
            ORDER BY count DESC
        ", $start_date, $end_date));
        
        ?>
        <div class="wrap">
            <h1>🗺️ Preserve Explorer Analytics</h1>
            
            <form method="get" style="margin: 20px 0;">
                <input type="hidden" name="post_type" value="preserve">
                <input type="hidden" name="page" value="dclt-analytics">
                <label>From: <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>"></label>
                <label>To: <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>"></label>
                <input type="submit" class="button" value="Update">
            </form>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <!-- Top Events -->
                <div class="postbox">
                    <h3 class="hndle">📊 Top Events</h3>
                    <div class="inside">
                        <table class="widefat">
                            <thead>
                                <tr><th>Event</th><th>Count</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_events as $event): ?>
                                    <tr>
                                        <td><?php echo esc_html($event->event_name); ?></td>
                                        <td><?php echo esc_html($event->count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Top Preserves -->
                <div class="postbox">
                    <h3 class="hndle">🏞️ Popular Preserves</h3>
                    <div class="inside">
                        <table class="widefat">
                            <thead>
                                <tr><th>Preserve</th><th>Views</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_preserves as $preserve): ?>
                                    <tr>
                                        <td><?php echo esc_html($preserve->preserve_name); ?></td>
                                        <td><?php echo esc_html($preserve->count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Filter Usage -->
            <div class="postbox" style="margin-top: 20px;">
                <h3 class="hndle">🔍 Filter Usage (DEI Focus)</h3>
                <div class="inside">
                    <table class="widefat">
                        <thead>
                            <tr><th>Filter Type</th><th>Filter Value</th><th>Usage Count</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filter_usage as $filter): ?>
                                <tr style="<?php echo $filter->filter_type === '"accessibility"' ? 'background: #e7f3ff;' : ''; ?>">
                                    <td><?php echo esc_html(trim($filter->filter_type, '"')); ?></td>
                                    <td><?php echo esc_html(trim($filter->filter_value, '"')); ?></td>
                                    <td><?php echo esc_html($filter->count); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p><em>💡 Accessibility filters highlighted in blue - track your DEI impact!</em></p>
                </div>
            </div>
        </div>
        <?php
    }
}

// Initialize the analytics system
new DCLT_Analytics();