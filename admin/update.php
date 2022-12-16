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
class Update {

	private $sejoli_url   = "";
	private $repo_url     = "";
    private $repo_name    = "";
    private $user_name    = "";
    private $base_url     = "https://api.github.com/repos/_user_/_repo_/branches";
    private $branch       = "";
    private $core_file    = "";
    private $username     = "";
    private $password     = "";
    private $repo_version = "";
    private $plugin_file  = "";
    private $update_file  = "";
    private $pluginData   = "";

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $repo_url, $branch, $core_file, $repo, $plugin_file ) {

		add_filter( "site_transient_update_plugins", array( $this, "setTransient" ) );
		add_filter( "plugins_api", array( $this, "setPluginInfo" ), 10, 3 );
    	add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );

        $this->sejoli_url  = "https://member.sejoli.co.id/wp-content/plugins/";
        $this->repo_url    = $repo_url;
        $this->repo_source = $repo;
        $this->branch      = $branch;
        $this->core_file   = $core_file;
        $this->plugin_file = $plugin_file;
        $this->username    = "asdzakky";
    	$this->password    = "ATBBJFSMyE8WgzySZVckeDSeVtVD265B1E41";

	}

	/**
	 * Get information regarding our plugin from WordPress
	 * @since 	1.0.0
	 * @return 	void
	 */
	 private function initPluginData() {

	 	if( !is_admin() ) {
	 		return;
	 	}

	 	if( $this->plugin_file ) {

		    $this->slug       = plugin_basename( $this->plugin_file );
		    $this->pluginData = get_plugin_data( $this->plugin_file );
		    
	 	}

	 }

	/**
	 * Push in plugin version information to display in the details lightbox
	 * @since 	1.0.0
	 * @return 	void
	 */
  	public function setPluginInfo( $res, $action, $args ) {

    	$this->initPluginData();

    	if ( $action == 'plugin_information' && $args->slug == $this->slug ) {

      		$res = new \stdClass();
      		$res->name = $this->pluginData['Name'];
      		$res->slug = $this->slug;

    	}

    	return $res;

  	}

  	/**
	 * Check latest version from core file plugins
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function readCoreFile() {

        $file          = $this->repo_url . "/" . $this->branch . $this->core_file . ".php";
        $core_file_raw = preg_replace('/github.com/', "raw.githubusercontent.com", $file);
        $core_contents = $this->getContents( $core_file_raw );

        if ( preg_match("/\bbitbucket\b/i", $this->repo_url, $match) ) {

            $core_file_raw = $this->base_url;

            $repo = $this->user_name.'/'.$this->repo_name;
		    $url  = sprintf( 'https://api.bitbucket.org/2.0/repositories/%s/src/HEAD/'.$this->core_file.'.php', $repo );

		    $core_contents = $this->getContentsBitbucket( $url );
		    $decode        = json_decode( $core_contents );

		    // No file found or other error.
		    if ( $decode ) {
		      return false;
		    }

        }

        return htmlentities( $core_contents );

    }

    /**
	 * Get Github Content Authenticating with Curl
	 * @since 	1.0.0
	 * @return 	void
	 */
    public function getContents( $url ) {

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201' );

        $output = curl_exec( $ch );
        curl_close( $ch );

        return $output;

    }

    /**
	 * Get Bitbucket Content Authenticating with Curl
	 * @since 	1.0.0
	 * @return 	void
	 */
    private function getContentsBitbucket( $url ) {

	    $process = curl_init( $url );
	    
	    curl_setopt( $process, CURLOPT_USERPWD, sprintf( '%s:%s', $this->username, $this->password ) );
	    curl_setopt( $process, CURLOPT_RETURNTRANSFER, TRUE );

	    $response = curl_exec( $process );
	    curl_close( $process );

	    return $response;

	}

	public function check_latest_version() {

		$check_transient_bitbucket_updater = get_transient( 'sejoli_bitbucket_updater_'.$this->core_file.'_plugin' );
		$check_transient_github_updater    = get_transient( 'sejoli_github_updater_'.$this->core_file.'_plugin' );

		// TODO - check on this line is github or bitbucket       
        if ( preg_match("/\bgithub\b/i", $this->repo_url, $match) ) {

        	if( $this->repo_source === 'github' ) {

        		if( $check_transient_github_updater ) {

        			$fileDir           = $this->sejoli_url . "sejoli-plugin-updater/plugins/tmp/"; 
					$this->update_file = $fileDir . $this->core_file."-".$check_transient_github_updater.".zip";

					return;

				}

	            $repos_url = preg_split('/\//', $this->repo_url);

	            $this->repo_name = $repos_url[4];
	            $this->user_name = $repos_url[3];
	            $this->base_url  = preg_replace('/_repo_/', $this->repo_name, $this->base_url);
	            $this->base_url  = preg_replace('/_user_/', $this->user_name, $this->base_url);

	            // get repo version
	            $readCoreFile = isset(preg_split('/Version:\ /', $this->readCoreFile())[1]) ? preg_split('/Version:\ /', $this->readCoreFile())[1] : null;
	            $contents     = $readCoreFile;
	            $this->repo_version = trim(preg_split('/\n/', $contents)[0]);

	            set_transient( 'sejoli_github_updater_'.$this->core_file.'_plugin', $this->repo_version, 2 * DAY_IN_SECONDS );

	            $repo_file    = $this->core_file."-".$this->repo_version.".zip";
		        $fileDir      = $this->sejoli_url . "sejoli-plugin-updater/plugins/tmp/"; 

				$this->update_file = $fileDir . $this->core_file."-".$this->repo_version.".zip";

	        }

        } else {

        	if( $this->repo_source === 'bitbucket' ) {

        		if( $check_transient_bitbucket_updater ) {
        			
        			$fileDir           = $this->sejoli_url . "sejoli-plugin-updater/plugins/tmp/"; 
					$this->update_file = $fileDir . $this->core_file."-".$check_transient_bitbucket_updater.".zip";

					return;

				}
		         
	            $repos_url = preg_split('/\//', $this->repo_url);

	            $this->repo_name = $repos_url[4];
	            $this->user_name = $repos_url[3];

	            $this->base_url  = "https://bitbucket.org/" . $this->user_name . "/" . $this->repo_name . "/raw/master/" . $this->core_file . ".php";

	            // get repo version
	            $readCoreFile = isset(preg_split('/Version:\ /', $this->readCoreFile())[1]) ? preg_split('/Version:\ /', $this->readCoreFile())[1] : null;
	            $contents     = $readCoreFile;
	            $this->repo_version = trim(preg_split('/\n/', $contents)[0]);

	            set_transient( 'sejoli_bitbucket_updater_'.$this->core_file.'_plugin', $this->repo_version, 2 * DAY_IN_SECONDS );
	        
		        $repo_file    = $this->core_file."-".$this->repo_version.".zip";
		        $fileDir      = $this->sejoli_url . "sejoli-plugin-updater/plugins/tmp/"; 

				$this->update_file = $fileDir . $this->core_file."-".$this->repo_version.".zip";

	        }

        }

	}

	/**
	 * Push in plugin version information to get the update notification
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function setTransient( $transient ) {

		if( false === sejolisa_check_own_license() ) :

			return;

		endif;
		
		if( !is_admin() ) {

	 		return;
	 	
	 	}
	    
	    // If we have checked the plugin data before, don't re-check
	    if ( empty( $transient->checked ) ) {
	      	return $transient;
	    }

	    // Get plugin & GitHub release information
	    $this->check_latest_version();
	    $this->initPluginData();
	    // $this->getRepoReleaseInfo();
	    
	    $get_repo_version = '';

	    if( $this->repo_source === 'bitbucket' ) {
	    	$get_repo_version = get_transient( 'sejoli_bitbucket_updater_'.$this->core_file.'_plugin' );
	    } elseif( $this->repo_source === 'github' ) {
	    	$get_repo_version = get_transient( 'sejoli_github_updater_'.$this->core_file.'_plugin' );
	    }
	    
	    $repo_version = str_replace('v', '', $get_repo_version);
	    $doUpdate     = '';

	    // Check the versions if we need to do an update
	    if( !empty($repo_version) && !empty($this->slug) ) {
	    	$doUpdate = version_compare( $repo_version, $transient->checked[$this->slug] );
	    }

	    // Update the transient to include our updated plugin data
	    if ( $doUpdate == 1 ) {

	      	$package = $this->update_file;	  

	      	$obj = new \stdClass();
		    $obj->slug = $this->slug;
		    $obj->new_version = str_replace('v', '', $get_repo_version);
		    $obj->url = $this->pluginData["PluginURI"];
		    $obj->package = $package;
		    $obj->compatibility = 
		    $transient->response[$this->slug] = $obj;

	    }

	    return $transient;

	}

	/**
	 * Perform additional actions to successfully install our plugin
	 * @since 	1.0.0
	 * @return 	void
	 */
  	public function postInstall( $true, $hook_extra, $result ) {

  		// Get plugin information
	    $this->initPluginData();

	    // Remember if our plugin was previously activated
	    $wasActivated = is_plugin_active( $this->slug );

	    // Since we are hosted in GitHub, our plugin folder would have a dirname of
	    // reponame-tagname change it to our original one:
	    global $wp_filesystem;

	    $pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
	    $wp_filesystem->move( $result['destination'], $pluginFolder );
	    $result['destination'] = $pluginFolder;

	    // Re-activate plugin if needed
	    if ( $wasActivated ) {
	      	$activate = activate_plugin( $this->slug );
	    }

	    return $result;
  	
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