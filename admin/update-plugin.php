<?php

namespace Sejoli_Plugin_Updater;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://sejoli.co.id
 * @since      1.0.0
 *
 * @package    Sejoli_Plugin_Updater
 * @subpackage Sejoli_Plugin_Updater/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sejoli_Plugin_Updater
 * @subpackage Sejoli_Plugin_Updater/admin
 * @author     Sejoli <it@sejoli.co.id>
 */
class Update_Plugin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

	}

	/**
     * Set Cron Schedule for Checking Update Plugin from Repositories
     * Hooked via filter cron_schecules, priority 1
     *
     * @since 1.0.0
     */
    public function sejoli_checking_update_plugin_for_repositories_cron_schedules($schedules) {

        $schedules['sejoli_plugin_checking_update'] = array(
            'interval' => 172800, // once 2 days
            'display'  => 'Plugin Updater Checker Once every 2 days'
        );

        return $schedules;

    }

    /**
     * Set Schedule Event for Checking Plugin Update from Repositories
     * Hooked via action admin_init, priority 1
     *
     * @since 1.0.0
     */
    public function sejoli_schedule_checking_update_plugin_for_repositories() {

        // Schedule an action if it's not already scheduled
        if ( ! wp_next_scheduled( 'sejoli_plugin_updater' ) ) {
            
            wp_schedule_event( time(), 'sejoli_plugin_checking_update', 'sejoli_plugin_updater' );
        
        }

    }

	/**
     * Checking Plugin Update
     * @since   1.0.0
     * @param   array   $order_data [description]
     */
    public function sejoli_checking_plugin_update() {

    	$sejoli_plugin_updater = new Update_Checker();

    }

}