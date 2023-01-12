<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://sejoli.co.id
 * @since      1.0.0
 *
 * @package    Sejoli_Plugin_Updater
 * @subpackage Sejoli_Plugin_Updater/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sejoli_Plugin_Updater
 * @subpackage Sejoli_Plugin_Updater/includes
 * @author     Sejoli <it@sejoli.co.id>
 */
class Sejoli_Plugin_Updater {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sejoli_Plugin_Updater_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'SEJOLI_PLUGIN_UPDATER_VERSION' ) ) {
			$this->version = SEJOLI_PLUGIN_UPDATER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sejoli-plugin-updater';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sejoli_Plugin_Updater_Loader. Orchestrates the hooks of the plugin.
	 * - Sejoli_Plugin_Updater_i18n. Defines internationalization functionality.
	 * - Sejoli_Plugin_Updater_Admin. Defines all hooks for the admin area.
	 * - Sejoli_Plugin_Updater_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejoli-plugin-updater-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejoli-plugin-updater-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/update-checker.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/update-plugin.php';

		$this->loader = new Sejoli_Plugin_Updater_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sejoli_Plugin_Updater_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sejoli_Plugin_Updater_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$admin = new Sejoli_Plugin_Updater\Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'after_setup_theme',	$admin, 'load_carbon_fields', 999 );

		$settings = new Sejoli_Plugin_Updater\Admin\Settings( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $settings, 'sejoli_register_post_type', 80 );
		$this->loader->add_action( 'init', $settings, 'sejoli_register_post_type_product_repository', 80 );
		$this->loader->add_action( 'admin_init', $settings, 'sejoli_add_file_updater_caps', 10 );
		$this->loader->add_action( 'carbon_fields_register_fields',	$settings, 'setup_carbon_fields_post_meta', 999 );
		$this->loader->add_filter( 'manage_sejoli-file-updater_posts_columns', $settings, 'sejoli_file_updater_posttype_table_head' );
		$this->loader->add_action( 'manage_sejoli-file-updater_posts_custom_column', $settings, 'sejoli_file_updater_posttype_table_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-sejoli-file-updater_sortable_columns', $settings, 'sejoli_file_updater_posttype_table_sorting' );
		$this->loader->add_filter( 'request', $settings, 'sejoli_file_updater_posttype_product_cat_column_orderby' );

		$update = new Sejoli_Plugin_Updater\Update_Plugin();

		$this->loader->add_filter( 'cron_schedules', $update, 'sejoli_checking_update_plugin_for_repositories_cron_schedules', 1 );
		$this->loader->add_action( 'admin_init', $update, 'sejoli_schedule_checking_update_plugin_for_repositories', 1 );
		$this->loader->add_action( 'sejoli_plugin_updater', $update, 'sejoli_checking_plugin_update', 1 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sejoli_Plugin_Updater_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
