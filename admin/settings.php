<?php

namespace Sejoli_Plugin_Updater\Admin;

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
class Settings {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Register file updater post type
     * Hooked via action init, priority 999
     * @return void
     */
    public function sejoli_register_post_type() {

		if( false === sejolisa_check_own_license() ) :

			return;

		endif;
  		
  		$labels = [
    		'name'               => _x( 'File Plugins Updater', 'post type general name', 'sejoli-plugin-updater' ),
    		'singular_name'      => _x( 'File Plugins Updater', 'post type singular name', 'sejoli-plugin-updater' ),
    		'menu_name'          => _x( 'File Plugins Updater', 'admin menu', 'sejoli-plugin-updater' ),
    		'name_admin_bar'     => _x( 'File Plugins Updater', 'add new on admin bar', 'sejoli-plugin-updater' ),
    		'add_new'            => _x( 'Add New', 'fileupdater', 'sejoli-plugin-updater' ),
    		'add_new_item'       => __( 'Add New File Plugins Updater', 'sejoli-plugin-updater' ),
    		'new_item'           => __( 'New File Plugins Updater', 'sejoli-plugin-updater' ),
    		'edit_item'          => __( 'Edit File Plugins Updater', 'sejoli-plugin-updater' ),
    		'view_item'          => __( 'View File Plugins Updater', 'sejoli-plugin-updater' ),
    		'all_items'          => __( 'All File Plugins Updater', 'sejoli-plugin-updater' ),
    		'search_items'       => __( 'Search File Plugins Updater', 'sejoli-plugin-updater' ),
    		'parent_item_colon'  => __( 'Parent File Plugins Updater:', 'sejoli-plugin-updater' ),
    		'not_found'          => __( 'No File Plugins Updater found.', 'sejoli-plugin-updater' ),
    		'not_found_in_trash' => __( 'No File Plugins Updater found in Trash.', 'sejoli-plugin-updater' )
    	];

    	$args = [
    		'labels'             => $labels,
            'description'        => __( 'Description.', 'sejoli-plugin-updater' ),
    		'public'             => true,
    		'publicly_queryable' => true,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
			'exclude_from_search'=> true,
    		'rewrite'            => [ 'slug' => 'file-updater' ],
    		'capability_type'    => 'sejoli_file_updater',
			'capabilities'		 => array(
				'publish_posts'      => 'publish_sejoli_file_updateres',
				'edit_posts'         => 'edit_sejoli_file_updateres',
				'edit_others_posts'  => 'edit_others_sejoli_file_updateres',
				'read_private_posts' => 'read_private_sejoli_file_updateres',
				'edit_post'          => 'edit_sejoli_file_updater',
				'delete_posts'       => 'delete_sejoli_file_updater',
				'read_post'          => 'read_sejoli_file_updater'
			),
    		'has_archive'        => false,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => [ 'title', 'editor' ],
			'menu_icon'			 => plugin_dir_url( __FILE__ ) . 'images/icon.png'
    	];

    	register_post_type( 'sejoli-file-updater', $args );

    }

    /**
	 * Post Type file updater capabilities
	 * Hooked via admin_init, prioritas 10
	 * @since 	1.0.0
	 * @return 	void
	 */
    public function sejoli_add_file_updater_caps() {

	    // gets the administrator role
	    $admins = get_role( 'administrator' );

	    $admins->add_cap( 'edit_sejoli_file_updater' ); 
	    $admins->add_cap( 'edit_sejoli_file_updateres' ); 
	    $admins->add_cap( 'edit_other_sejoli_file_updateres' ); 
	    $admins->add_cap( 'publish_sejoli_file_updateres' ); 
	    $admins->add_cap( 'read_sejoli_file_updater' ); 
	    $admins->add_cap( 'read_private_sejoli_file_updateres' ); 
	    $admins->add_cap( 'delete_sejoli_file_updater' ); 

	}

	/**
     * Setup category file plugin updater settings
     * Hooked via action carbon_fields_register_fields, priority 999
     * @since 	1.0.0
     * @return 	void
     */
    public function setup_carbon_fields() {

		// Add second options page under 'Basic Options'
		Container::make( 'theme_options', 'Category File Plugin Updater' )
			->set_icon( plugin_dir_url( __FILE__ ) . 'images/icon.png' )
		    ->add_fields( array(
		        Field::make( 'text', 'sejoli_crb_repo_name', 'Repo Name' ),
		        Field::make( 'text', 'sejoli_crb_repo_url', 'Repo URL' ),
		        Field::make( 'text', 'sejoli_crb_repo_branch', 'Branch' ),
		        Field::make( 'text', 'sejoli_crb_core_file', 'Core File (.php)' ),
		        Field::make( 'radio', 'sejoli_crb_repo_type', 'Repo Type' )
				    ->add_options( array(
				        'github' => 'Github',
				        'bitbucket' => 'BitBucket',
				    ) ),
				Field::make( 'text', 'sejoli_crb_repo_github_access_token', 'Repo Github Access Token' )
		        	->set_conditional_logic(array(
						array(
							'field'	=> 'sejoli_crb_repo_type',
							'value'	=> 'github'
						)
					)),
				Field::make( 'text', 'sejoli_crb_repo_bitbucket_username', 'Repo BitBucket Username' )
		        	->set_conditional_logic(array(
						array(
							'field'	=> 'sejoli_crb_repo_type',
							'value'	=> 'bitbucket'
						)
					)),
				Field::make( 'text', 'sejoli_crb_repo_bitbucket_app_password', 'Repo BitBucket APP Password' )
		        	->set_conditional_logic(array(
						array(
							'field'	=> 'sejoli_crb_repo_type',
							'value'	=> 'bitbucket'
						)
					)),
		    ) );

    }

    /**
     * Setup custom fields for file updater post type
     * Hooked via action carbon_fields_register_fields, priority 1009
     * @since 	1.0.0
     * @return 	void
     */
    public function setup_carbon_fields_post_meta() {

		// Add second options page under 'Basic Options'
		Container::make('post_meta', __('File Info', 'sejoli-plugin-updater'))
			->where( 'post_type', '=', 'sejoli-file-updater')
		    ->add_fields( array(
		        Field::make( 'text', 'sejoli_crb_repo_version', 'Version' ),
		        Field::make( 'text', 'sejoli_crb_repo_category', 'Category' ),
		        Field::make( 'html', 'sejoli_crb_plugin_info' )
		    ) );

    }

}
