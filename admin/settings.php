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
		$this->version     = $version;

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
     * Register product repository post type
     * Hooked via action init, priority 999
     * @return void
     */
    public function sejoli_register_post_type_product_repository() {

		if( false === sejolisa_check_own_license() ) :

			return;

		endif;

    	$labels = [
    		'name'               => _x( 'Product Repository', 'post type general name', 'sejoli-plugin-updater' ),
    		'singular_name'      => _x( 'Product Repository', 'post type singular name', 'sejoli-plugin-updater' ),
    		'menu_name'          => _x( 'Product Repository', 'admin menu', 'sejoli-plugin-updater' ),
    		'name_admin_bar'     => _x( 'Product Repository', 'add new on admin bar', 'sejoli-plugin-updater' ),
    		'add_new'            => _x( 'Add New', 'productrepo', 'sejoli-plugin-updater' ),
    		'add_new_item'       => __( 'Add New Product Repository', 'sejoli-plugin-updater' ),
    		'new_item'           => __( 'New Product Repository', 'sejoli-plugin-updater' ),
    		'edit_item'          => __( 'Edit Product Repository', 'sejoli-plugin-updater' ),
    		'view_item'          => __( 'View Product Repository', 'sejoli-plugin-updater' ),
    		'all_items'          => __( 'All Product Repository', 'sejoli-plugin-updater' ),
    		'search_items'       => __( 'Search Product Repository', 'sejoli-plugin-updater' ),
    		'parent_item_colon'  => __( 'Parent Product Repository:', 'sejoli-plugin-updater' ),
    		'not_found'          => __( 'No Product Repository found.', 'sejoli-plugin-updater' ),
    		'not_found_in_trash' => __( 'No Product Repository found in Trash.', 'sejoli-plugin-updater' )
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
    		'rewrite'            => [ 'slug' => 'product-repo' ],
    		'capability_type'    => 'sejoli_product_repo',
			'capabilities'		 => array(
				'publish_posts'      => 'publish_sejoli_product_repos',
				'edit_posts'         => 'edit_sejoli_product_repos',
				'edit_others_posts'  => 'edit_others_sejoli_product_repos',
				'read_private_posts' => 'read_private_sejoli_product_repos',
				'edit_post'          => 'edit_sejoli_product_repo',
				'delete_posts'       => 'delete_sejoli_product_repo',
				'read_post'          => 'read_sejoli_product_repo'
			),
    		'has_archive'        => false,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => [ 'title' ],
			'menu_icon'			 => plugin_dir_url( __FILE__ ) . 'images/icon.png'
    	];

    	register_post_type( 'sejoli-product-repo', $args );

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

	    $admins->add_cap( 'edit_sejoli_product_repo' ); 
	    $admins->add_cap( 'edit_sejoli_product_repos' ); 
	    $admins->add_cap( 'edit_other_sejoli_product_repos' ); 
	    $admins->add_cap( 'publish_sejoli_product_repos' ); 
	    $admins->add_cap( 'read_sejoli_product_repo' ); 
	    $admins->add_cap( 'read_private_sejoli_product_repos' ); 
	    $admins->add_cap( 'delete_sejoli_product_repo' ); 

	}

	/**
	 * Post Type file updater custom table head
	 * Hooked via add_filter
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function sejoli_file_updater_posttype_table_head( $defaults ) {

	    unset($defaults['title']);
	    unset($defaults['date']);

	    $defaults['title'] = __('Product Name', 'sejoli-plugin-updater');
	    $defaults['repo_version'] = __('Version', 'sejoli-plugin-updater');
	    $defaults['repo_product_category'] = __('Product Category', 'sejoli-plugin-updater');
		$defaults['date'] = 'Date';

	    return $defaults;

	}

	/**
	 * Post Type file updater custom post type table content
	 * Hooked via add_action, prioritas 10, 2
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function sejoli_file_updater_posttype_table_content( $column_name, $post_id ) {

	    if ( $column_name == 'repo_version' ) {

		    $repo_version = get_post_meta( $post_id, '_sejoli_crb_repo_version', true );

		    echo $repo_version ;

	    }

	    if ( $column_name == 'repo_product_category' ) {

		    $repo_product_category = get_post_meta( $post_id, '_sejoli_crb_repo_category', true );
	    	$args = array(
			  	'p'         => $repo_product_category,
			  	'post_type' => 'sejoli-product-repo'
			);

			$cats = new \WP_Query($args);

			if( $repo_product_category > 0 ) :

				while ( $cats->have_posts() ) : $cats->the_post();

			    	echo get_the_title();

				endwhile;

			else:

				echo "-";

			endif;

			wp_reset_postdata();

	    }

	}

	/**
	 * Post Type file updater custom post type table sorting
	 * Hooked via add_filter
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function sejoli_file_updater_posttype_table_sorting( $columns ) {

	    $columns['repo_product_category'] = __('Product Category', 'sejoli-plugin-updater');

	  	return $columns;

	}

	/**
	 * Post Type file updater custom post type table sort order by product category
	 * Hooked via add_filter
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function sejoli_file_updater_posttype_product_cat_column_orderby( $vars ) {
	    
	    if ( isset( $vars['orderby'] ) && 'ticket_status' == $vars['orderby'] ) {

	        $vars = array_merge( $vars, array(
	            'meta_key' => '_sejoli_crb_repo_category',
	            'orderby'  => 'meta_value'
	        ) );

	    }

	    return $vars;
	    
	}

    /**
     * Setup custom fields for file updater & product repo post type
     * Hooked via action carbon_fields_register_fields, priority 1009
     * @since 	1.0.0
     * @return 	void
     */
    public function setup_carbon_fields_post_meta() {

		// Add second options page under 'Basic Options'
		Container::make('post_meta', __('File Info', 'sejoli-plugin-updater'))
			->where( 'post_type', '=', 'sejoli-file-updater')
		    ->add_fields( array(
		        Field::make( 'text', 'sejoli_crb_repo_version', __('Version', 'sejoli-plugin-updater') ),
		        Field::make('association', 'sejoli_crb_repo_category', __('Category', 'sejoli'))
					->set_types([
						[
							'type'      => 'post',
							'post_type' => 'sejoli-product-repo'
						]
					])->set_max( 1 ),
		    ) );

		Container::make('post_meta', __('Product Info', 'sejoli-plugin-updater'))
			->where( 'post_type', '=', 'sejoli-product-repo')
		    ->add_fields( array(
		        Field::make( 'text', 'sejoli_crb_repo_name', __('Repo Name', 'sejoli-plugin-updater') ),
		        Field::make( 'text', 'sejoli_crb_repo_url', __('Repo URL', 'sejoli-plugin-updater') ),
		        Field::make( 'text', 'sejoli_crb_repo_branch', __('Branch', 'sejoli-plugin-updater') ),
		        Field::make( 'text', 'sejoli_crb_core_file', __('Core File (.php)', 'sejoli-plugin-updater') ),
		        Field::make( 'radio', 'sejoli_crb_repo_type', __('Repo Type', 'sejoli-plugin-updater') )
				    ->add_options( array(
				        'github'    => 'Github',
				        'bitbucket' => 'BitBucket',
				    ) ),
				Field::make( 'text', 'sejoli_crb_repo_github_access_token', __('Repo Github Access Token', 'sejoli-plugin-updater') )
		        	->set_conditional_logic(array(
						array(
							'field'	=> 'sejoli_crb_repo_type',
							'value'	=> 'github'
						)
					)),
				Field::make( 'text', 'sejoli_crb_repo_bitbucket_username', __('Repo BitBucket Username', 'sejoli-plugin-updater') )
		        	->set_conditional_logic(array(
						array(
							'field'	=> 'sejoli_crb_repo_type',
							'value'	=> 'bitbucket'
						)
					)),
				Field::make( 'text', 'sejoli_crb_repo_bitbucket_app_password', __('Repo BitBucket APP Password', 'sejoli-plugin-updater') )
		        	->set_conditional_logic(array(
						array(
							'field'	=> 'sejoli_crb_repo_type',
							'value'	=> 'bitbucket'
						)
					)),
		    ) );

    }

}