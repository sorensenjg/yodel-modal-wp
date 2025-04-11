<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://useyodel.com
 * @since             1.0.0
 * @package           Yodel_Wp
 *
 * @wordpress-plugin
 * Plugin Name:       Yodel Modal
 * Plugin URI:        https://useyodel.com
 * Description:       Inspired by the ancient yodels of the Central Alps, Yodel brings you a unique way to reach your audience with custom modals. Just as yodelers once communicated across mountains, this plugin lets you share your message effectively and engagingly.
 * Version:           1.4.1
 * Author:            Yodel   
 * Author URI:        https://useyodel.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yodel-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'YODEL_WP_VERSION', '1.4.2' );  
define( 'YODEL_WP_BASENAME',  plugin_basename( __FILE__ ) ); 
define( 'YODEL_WP_API_URL', ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'local' ) ? 'http://localhost:3000' : 'https://useyodel.com' );
  
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yodel-wp-activator.php
 */
function activate_yodel_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yodel-wp-activator.php';
	Yodel_Wp_Activator::activate();    
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yodel-wp-deactivator.php
 */
function deactivate_yodel_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yodel-wp-deactivator.php';
	Yodel_Wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yodel_wp' );
register_deactivation_hook( __FILE__, 'deactivate_yodel_wp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yodel-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yodel_wp() {

	$plugin = new Yodel_Wp();
	$plugin->run();

}
run_yodel_wp();
