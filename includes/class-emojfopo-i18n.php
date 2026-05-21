<?php
/**
 * Define the internationalization functionality.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/includes
 * @author     Gunjan Jaswal <hello@gunjanjaswal.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Emojfopo_i18n {

    /**
     * Set up the plugin for translation.
     *
     * Note: As of WordPress 4.6+, translations are automatically loaded
     * for plugins on WordPress.org, so this method is only needed for
     * custom translation loading scenarios.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        // No action needed for WordPress.org hosted plugins
        // WordPress will automatically load translations from wordpress.org/plugins/emojis-for-posts-and-pages/
    }
}
