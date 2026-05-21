<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for
 * the public-facing side of the site.
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/public
 * @author     Gunjan Jaswal <hello@gunjanjaswal.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Emojfopo_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     * @return   void
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/emojfopo-public.css',
            array(),
            $this->version,
            'all'
        );
        
        // Load Noto Color Emoji font
        wp_enqueue_style(
            $this->plugin_name . '-noto-emoji',
            'https://fonts.googleapis.com/css2?family=Noto+Color+Emoji&display=swap',
            array(),
            $this->version
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     * @return   void
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/emojfopo-public.js',
            array( 'jquery' ),
            $this->version,
            true // Load in footer for better performance
        );
        
        // Get custom reaction names
        $custom_names = get_option( 'emojfopo_custom_names', array() );
        
        wp_localize_script(
            $this->plugin_name,
            'emojfopo_reactions',
            array(
                'ajax_url'        => admin_url( 'admin-ajax.php' ),
                'nonce'           => wp_create_nonce( 'emojfopo_reactions_nonce' ),
                'already_reacted' => esc_html__( 'You have already reacted to this post.', 'emojis-for-posts-and-pages' ),
                'reaction_added'  => esc_html__( 'Your reaction has been added!', 'emojis-for-posts-and-pages' ),
                'error'           => esc_html__( 'An error occurred. Please try again.', 'emojis-for-posts-and-pages' ),
                'you_reacted_with' => esc_html__( 'You reacted with', 'emojis-for-posts-and-pages' ),
                'reaction_names'  => $custom_names
            )
        );
    }
    
    /**
     * Display emoji reactions after post content
     *
     * @since    1.0.0
     * @param    string    $content    The post content.
     * @return   string    Modified post content with emoji reactions.
     */
    public function display_emoji_reactions($content) {
        // Only show on singular posts of enabled post types
        if (!is_singular() || !$this->should_show_reactions()) {
            return $content;
        }
        
        $post_id = get_the_ID();
        $reactions_html = $this->get_reactions_html($post_id);
        
        return $content . $reactions_html;
    }
    
    /**
     * Display floating emoji reactions
     *
     * @since    1.0.0
     */
    public function display_floating_emoji_reactions() {
        // Only show on singular posts of enabled post types
        if (!is_singular() || !$this->should_show_reactions()) {
            return;
        }
        
        $post_id = get_the_ID();
        $reactions_html = $this->get_reactions_html($post_id, true);
        
        echo wp_kses_post($reactions_html);
    }
    
    /**
     * Check if reactions should be shown for current post
     *
     * @since    1.0.0
     * @return   boolean    Whether reactions should be shown.
     */
    private function should_show_reactions() {
        $post_types = get_option('emojfopo_post_types', array('post'));
        $current_post_type = get_post_type();
        
        return in_array($current_post_type, $post_types);
    }
    
    /**
     * Get HTML for emoji reactions
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @param    boolean   $floating   Whether the reactions are floating.
     * @return   string    HTML for emoji reactions.
     */
    private function get_reactions_html($post_id, $floating = false) {
        $enabled_emojis = get_option('emojfopo_enabled_emojis', array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠'
        ));
        
        if (empty($enabled_emojis)) {
            return '';
        }
        
        $custom_names = get_option('emojfopo_custom_names', array());
        $reaction_counts = $this->get_reaction_counts($post_id);
        $user_reaction = $this->get_user_reaction($post_id);
        
        $container_class = $floating ? 'emojfopo-container floating' : 'emojfopo-container';
        
        // Use template instead of building HTML manually
        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/emojfopo-public-display.php';
        $html = ob_get_clean();
        
        // HTML is now generated from the template
        
        return $html;
    }
    
    /**
     * Get reaction counts for a post
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @return   array     Reaction counts.
     */
    private function get_reaction_counts( $post_id ) {
        // Check for cached data first
        $cache_key = 'emojfopo_counts_' . $post_id;
        $counts = wp_cache_get( $cache_key );
        
        if ( false !== $counts ) {
            return $counts;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'emojfopo_reactions';
        $counts = array();
        
        // Check if table exists
        $table_exists = wp_cache_get( 'emojfopo_table_exists' );
        if ( false === $table_exists ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching is implemented right after this call
            $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;
            wp_cache_set( 'emojfopo_table_exists', $table_exists, '', HOUR_IN_SECONDS );
        }
        
        if ( ! $table_exists ) {
            return array();
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching is implemented right after this query
        $results = $wpdb->get_results(
            $wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is hardcoded with prefix
                "SELECT emoji_id, COUNT(*) as count FROM {$wpdb->prefix}emojfopo_reactions WHERE post_id = %d GROUP BY emoji_id",
                $post_id
            )
        );
        
        // Convert results to a simple array with emoji_id as key and count as value
        $counts = array();
        foreach ( $results as $row ) {
            $counts[$row->emoji_id] = (int) $row->count;
        }
        
        // Cache the results for 5 minutes
        wp_cache_set( $cache_key, $counts, '', 5 * MINUTE_IN_SECONDS );
        
        return $counts;
    }
    
    /**
     * Get user's reaction for a post
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @return   string|boolean    User's reaction or false if none.
     */
    private function get_user_reaction( $post_id ) {
        $ip_address = $this->get_user_ip();
        $user_id = get_current_user_id();
        
        // Generate a unique cache key based on post ID, IP address, and user ID
        $cache_key = 'emojfopo_user_' . $post_id . '_' . md5( $ip_address . '_' . $user_id );
        $reaction = wp_cache_get( $cache_key );
        
        if ( false !== $reaction ) {
            // Special case: we store 'none' in cache for no reaction
            return $reaction === 'none' ? false : $reaction;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'emojfopo_reactions';
        
        // Use the cached table exists check from get_reaction_counts
        $table_exists = wp_cache_get( 'emojfopo_table_exists' );
        if ( false === $table_exists ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching is implemented right after this call
            $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;
            wp_cache_set( 'emojfopo_table_exists', $table_exists, '', HOUR_IN_SECONDS );
        }
        
        if ( ! $table_exists ) {
            wp_cache_set( $cache_key, 'none', '', 5 * MINUTE_IN_SECONDS );
            return false;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching is implemented right after this query
        $reaction = $wpdb->get_var(
            $wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is hardcoded with prefix
                "SELECT emoji_id FROM {$wpdb->prefix}emojfopo_reactions WHERE post_id = %d AND (ip_address = %s OR (user_id > 0 AND user_id = %d)) LIMIT 1",
                $post_id,
                $ip_address,
                $user_id
            )
        );
        
        // Cache the result for 5 minutes
        wp_cache_set( $cache_key, $reaction ? $reaction : 'none', '', 5 * MINUTE_IN_SECONDS );
        
        return $reaction ? $reaction : false;
    }
    
    /**
     * Handle emoji reaction AJAX request
     *
     * @since    1.0.0
     */
    public function emojfopo_reaction() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'emojfopo_reactions_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'emojis-for-posts-and-pages')));
        }
        
        // Get post data
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $reaction = isset($_POST['reaction']) ? sanitize_text_field(wp_unslash($_POST['reaction'])) : '';
        
        if (!$post_id || !$reaction) {
            wp_send_json_error(array('message' => esc_html__('Invalid data.', 'emojis-for-posts-and-pages')));
        }
        
        // Verify post exists
        if (!get_post($post_id)) {
            wp_send_json_error(array('message' => esc_html__('Post not found.', 'emojis-for-posts-and-pages')));
        }
        
        // Verify reaction is valid
        $enabled_emojis = get_option('emojfopo_enabled_emojis', array());
        if (!isset($enabled_emojis[$reaction])) {
            wp_send_json_error(array('message' => esc_html__('Invalid reaction.', 'emojis-for-posts-and-pages')));
        }
        
        // Get user info
        $ip_address = $this->get_user_ip();
        $user_id = get_current_user_id();
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'emojfopo_reactions';
        
        // Check if user already reacted
        $existing_reaction = $this->get_user_reaction($post_id);
        
        if ( $existing_reaction ) {
            // Update existing reaction
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Cache is invalidated right after this query
            $result = $wpdb->update(
                $table_name,
                array(
                    'emoji_id' => $reaction,
                    'reaction_time' => current_time( 'mysql' )
                ),
                array(
                    'post_id' => $post_id,
                    'ip_address' => $ip_address
                )
            );
        } else {
            // Insert new reaction
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Cache is invalidated right after this query
            $result = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'emoji_id' => $reaction,
                    'ip_address' => $ip_address,
                    'user_id' => $user_id,
                    'reaction_time' => current_time( 'mysql' )
                )
            );
        }
        
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Database error.', 'emojis-for-posts-and-pages' ) ) );
        }
        
        // Clear caches related to this post and user
        $cache_key_user = 'emojfopo_user_' . $post_id . '_' . md5( $ip_address . '_' . $user_id );
        $cache_key_counts = 'emojfopo_counts_' . $post_id;
        wp_cache_delete( $cache_key_user );
        wp_cache_delete( $cache_key_counts );
        
        // Get updated counts
        $counts = $this->get_reaction_counts( $post_id );
        
        wp_send_json_success(array(
            'message' => esc_html__('Reaction saved successfully.', 'emojis-for-posts-and-pages'),
            'counts' => $counts,
            'user_reaction' => $reaction
        ));
    }
    
    /**
     * Get emoji reaction counts via AJAX
     *
     * @since    1.0.0
     */
    public function get_emojfopo_reactions() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'emojfopo_reactions_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Security check failed.', 'emojis-for-posts-and-pages')));
        }
        
        // Get post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if (!$post_id) {
            wp_send_json_error(array('message' => esc_html__('Invalid post ID.', 'emojis-for-posts-and-pages')));
        }
        
        // Get reaction counts
        $counts = $this->get_reaction_counts($post_id);
        $user_reaction = $this->get_user_reaction($post_id);
        
        wp_send_json_success(array(
            'counts' => $counts,
            'user_reaction' => $user_reaction
        ));
    }
    
    /**
     * Get user's IP address
     *
     * @since    1.0.0
     * @return   string    User's IP address.
     */
    private function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } else {
            $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        }
        return $ip;
    }
}
