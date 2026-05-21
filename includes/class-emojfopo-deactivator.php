<?php
/**
 * Fired during plugin deactivation.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/includes
 * @author     Gunjan Jaswal <hello@gunjanjaswal.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Emojfopo_Deactivator {

    /**
     * Plugin deactivation functionality.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // We don't delete the database tables on deactivation
        // This ensures user data is preserved if the plugin is reactivated
        
        // If you want to clean up completely, uncomment the following code
        /*
        global $wpdb;
        $table_name = $wpdb->prefix . 'emojfopo_reactions';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        
        delete_option('emojfopo_enabled_emojis');
        delete_option('emojfopo_position');
        delete_option('emojfopo_post_types');
        */
    }
}
