<?php
/**
 * Plugin Name: Emojis for Posts and Pages
 * Plugin URI: https://wordpress.org/plugins/emojis-for-posts-and-pages/
 * GitHub Plugin URI: https://github.com/gunjanjaswal/Emojis-For-Posts-And-Pages
 * Description: Add colorful emoji reactions to your WordPress posts and pages, similar to Facebook reactions.
 * Version: 1.1.3
 * Author: Gunjan Jaswal
 * Author URI: https://gunjanjaswal.me
 * Donate link: https://ko-fi.com/gunjanjaswal
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: emojis-for-posts-and-pages
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 7.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'EMOJFOPO_VERSION', '1.1.3' );

/**
 * Define plugin paths and URLs.
 */
define( 'EMOJFOPO_PATH', plugin_dir_path( __FILE__ ) );
define( 'EMOJFOPO_URL', plugin_dir_url( __FILE__ ) );
define( 'EMOJFOPO_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * @since 1.0.0
 */
function emojfopo_activate() {
	require_once EMOJFOPO_PATH . 'includes/class-emojfopo-activator.php';
	Emojfopo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @since 1.0.0
 */
function emojfopo_deactivate() {
	require_once EMOJFOPO_PATH . 'includes/class-emojfopo-deactivator.php';
	Emojfopo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'emojfopo_activate' );
register_deactivation_hook( __FILE__, 'emojfopo_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require EMOJFOPO_PATH . 'includes/class-emojfopo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 * @return void
 */
function emojfopo_run() {
	$plugin = new Emojfopo();
	$plugin->run();
}

// Start the plugin
emojfopo_run();
