<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://sejoli.co.id
 * @since             1.0.0
 * @package           Sejoli_Plugin_Updater
 *
 * @wordpress-plugin
 * Plugin Name:       Sejoli - Plugin Updater
 * Plugin URI:        https://https://sejoli.co.id
 * Description:       For Updating Plugins of Sejoli
 * Version:           1.0.0
 * Author:            Sejoli
 * Author URI:        https://https://sejoli.co.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sejoli-plugin-updater
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
define( 'SEJOLI_PLUGIN_UPDATER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sejoli-plugin-updater-activator.php
 */
function activate_sejoli_plugin_updater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-plugin-updater-activator.php';
	Sejoli_Plugin_Updater_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sejoli-plugin-updater-deactivator.php
 */
function deactivate_sejoli_plugin_updater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-plugin-updater-deactivator.php';
	Sejoli_Plugin_Updater_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sejoli_plugin_updater' );
register_deactivation_hook( __FILE__, 'deactivate_sejoli_plugin_updater' );

// Require Autoload
require plugin_dir_path(__FILE__) . '/vendor/autoload.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-plugin-updater.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sejoli_plugin_updater() {

	$plugin = new Sejoli_Plugin_Updater();
	$plugin->run();

}
run_sejoli_plugin_updater();
