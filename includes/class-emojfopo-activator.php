<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/includes
 * @author     Gunjan Jaswal <hello@gunjanjaswal.me>
 */
class Emojfopo_Activator {

    /**
     * Create necessary database tables and default settings during activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Table to store emoji reactions
        $table_name = $wpdb->prefix . 'emojfopo_reactions';
        
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            emoji_id varchar(20) NOT NULL,
            ip_address varchar(100) NOT NULL,
            user_id bigint(20) DEFAULT 0 NOT NULL,
            reaction_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY emoji_id (emoji_id),
            UNIQUE KEY unique_reaction (post_id, ip_address)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Add default settings
        $default_emojis = array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠'
        );
        
        add_option('emojfopo_enabled_emojis', $default_emojis);
        add_option('emojfopo_position', 'after_content');
        add_option('emojfopo_post_types', array('post'));
    }
}
