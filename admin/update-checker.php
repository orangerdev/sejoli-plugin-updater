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
class Update_Checker {

	private $repo_url      = "";
	private $product_repo  = "";
	private $repo_cat_name = "";
    private $repo_name     = "";
    private $user_name     = "";
    private $base_url      = "https://api.github.com/repos/_user_/_repo_/branches";
    private $access_token  = "";
    private $branch        = "";
    private $core_file     = "";
    private $plugin_repo   = "";
    private $username      = "";
    private $password      = "";

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

		$posts = new \WP_Query(array(
	        'post_type'              => 'sejoli-product-repo',
	        'posts_per_page'         => -1,
	        'no_found_rows'          => true,
	        'update_post_meta_cache' => false,
	        'update_post_term_cache' => false
	    ));

	    while ( $posts->have_posts() ) : $posts->the_post();

	    	$this->product_repo  = get_the_ID();
		    $this->repo_cat_name = get_post_meta( get_the_ID(), '_sejoli_crb_repo_name', true );
		    $this->repo_url      = get_post_meta( get_the_ID(), '_sejoli_crb_repo_url', true );
		    $this->branch        = get_post_meta( get_the_ID(), '_sejoli_crb_repo_branch', true );
		    $this->core_file     = get_post_meta( get_the_ID(), '_sejoli_crb_core_file', true );
		    $this->plugin_repo   = get_post_meta( get_the_ID(), '_sejoli_crb_repo_type', true );
		    $this->username      = get_post_meta( get_the_ID(), '_sejoli_crb_repo_bitbucket_username', true );
		    $this->password      = get_post_meta( get_the_ID(), '_sejoli_crb_repo_bitbucket_app_password', true );
		    
			$this->writeLog("setup Updater");

	    	$repo_url     = $this->repo_url;
	    	$core_file    = $this->core_file;
	        $plugin_repo  = $this->plugin_repo;
	        $repo_version = '';

	        // TODO - check on this line is github or bitbucket       
	        if ( preg_match("/\bgithub\b/i", $repo_url, $match) ) {

	        	if( $plugin_repo === 'github' ) {

		            $repos_url = preg_split('/\//', $repo_url);

		            $this->repo_name = $repos_url[4];
		            $this->user_name = $repos_url[3];
		            $this->base_url  = preg_replace('/_repo_/', $this->repo_name, $this->base_url);
		            $this->base_url  = preg_replace('/_user_/', $this->user_name, $this->base_url);

		            // get repo version
		            $this->writeLog("get version from core file");
		            $readCoreFile = isset(preg_split('/Version:\ /', $this->readCoreFile())[1]) ? preg_split('/Version:\ /', $this->readCoreFile())[1] : null;
		            $contents     = $readCoreFile;
		            $repo_version = trim(preg_split('/\n/', $contents)[0]);

		        }

	        } else {

	        	if( $plugin_repo === 'bitbucket' ) {

	        		$repos_url = preg_split('/\//', $repo_url);

		            $this->repo_name = $repos_url[4];
		            $this->user_name = $repos_url[3];

	        		$repo_version = $this->getRepoBitbucketReleaseInfo()->name;
		        
		        }

	        }

	        $repo_file    = $this->core_file."-".$repo_version.".zip";
	        $fileDir      = SEJOLI_PLUGIN_FILE_DIR . '/'; 
			$current_file = array_diff(scandir($fileDir), array('.', '..')); 

	        // if( !file_exists(plugin_dir_path( __FILE__ ) . "../plugins/tmp/" . $this->core_file."-".$current_version.".zip") ) {
	        // if( $repo_version !== $current_version ) {
			  
			if( 0 < count(array_intersect(array_map('strtolower', explode(' ', $repo_file)), $current_file)) ) {

	            $this->writeLog("File exist.");

	        } else {
	            
	            if ( preg_match("/\bbitbucket\b/i", $this->repo_url, $match) ) {

	            	if( $plugin_repo === 'bitbucket' ) {

	            		if( empty( $repo_version ) ) {

	            			return false;

	            		}

	            		$branch      = preg_replace('/\//', '', $this->branch);
			            $filename    = $this->core_file."-".$repo_version.".zip";
			            $file_name   = SEJOLI_PLUGIN_FILE_DIR . '/' . $filename;
			            $plugin_repo = $this->plugin_repo;

			            $this->writeLog("download patch");
			            $this->writeLog("start downloading");

		                $url         = $repo_url . "/get/".$branch.".zip";
		                $destination = $file_name;
		                $file_data   = $this->file_get_contents_curl( $url, $destination );

		                $handle = fopen( $file_name, 'w' );
		                fclose( $handle );

		                if ( wp_mkdir_p( SEJOLI_PLUGIN_FILE_DIR ) ) {

				            $file = SEJOLI_PLUGIN_FILE_DIR . '/' . $filename;

				        } else {

				            $file = SEJOLI_PLUGIN_FILE_DIR . '/' . $filename;

				        }

		                // Save Content to file
		                $downloaded = file_put_contents( $file, $file_data );

		                if( $downloaded > 0 ) {

		                	$plugin_name      = $this->core_file." v.".$repo_version;
			                $plugin_changelog = $this->getChangelogFile();
			                $plugin_version   = $repo_version;
			                $plugin_category  = $this->product_repo;

			                $saving_plugin_info = wp_insert_post(array (
							   'post_type'      => 'sejoli-file-updater',
							   'post_title'     => $plugin_name,
							   'post_content'   => $plugin_changelog,
							   'post_status'    => 'publish',
							   'comment_status' => 'closed',
							   'ping_status'    => 'closed',
							));

							if ( $saving_plugin_info ) {

								// insert post meta
							   	add_post_meta( $saving_plugin_info, '_sejoli_crb_repo_version', $plugin_version );
							   	add_post_meta( $saving_plugin_info, '_sejoli_crb_repo_category', $plugin_category );

						        $wp_filetype = wp_check_filetype( $filename, null );
						        $attachment = array(
						            'guid'           => SEJOLI_PLUGIN_FILE_URL . '/' . $filename,
						            'post_mime_type' => $wp_filetype['type'],
						            'post_title'     => sanitize_file_name($filename),
						            'post_content'   => '',
						            'post_status'    => 'inherit',
						        );

						        $attach_id = wp_insert_attachment( $attachment, $file, $saving_plugin_info );
						        require_once(ABSPATH . 'wp-admin/includes/file.php');
						        // $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
						        // wp_update_attachment_metadata( $attach_id, $attach_data );

							}

		                    $this->writeLog("Complete.");

		                }

	            	}

	            } else {

	            	if( $plugin_repo === 'github' ) {

	            		$branch      = preg_replace('/\//', '', $this->branch);
			            $filename    = $this->core_file."-".$repo_version.".zip";
			            $file_name   = SEJOLI_PLUGIN_FILE_DIR . '/' . $filename;
			            $plugin_repo = $this->plugin_repo;

			            $this->writeLog("download patch");
			            $this->writeLog("start downloading");

		                $file_data = file_get_contents( $repo_url . "/archive/".$this->branch.".zip?access_token=".$this->access_token );
		                $handle    = fopen( $file_name, 'w' );
		                fclose( $handle );

		                if ( wp_mkdir_p( SEJOLI_PLUGIN_FILE_DIR ) ) {

				            $file = SEJOLI_PLUGIN_FILE_DIR . '/' . $filename;

				        } else {

				            $file = SEJOLI_PLUGIN_FILE_DIR . '/' . $filename;

				        }

		                // Save Content to file
		                $downloaded = file_put_contents( $file, $file_data );

		                if( $downloaded > 0 ) {

		                	$plugin_name      = $this->core_file." v.".$repo_version;
			                $plugin_changelog = $this->getChangelogFile();
			                $plugin_version   = $repo_version;
			                $plugin_category  = $this->product_repo;

			                $saving_plugin_info = wp_insert_post(array (
							   'post_type'      => 'sejoli-file-updater',
							   'post_title'     => $plugin_name,
							   'post_content'   => $plugin_changelog,
							   'post_status'    => 'publish',
							   'comment_status' => 'closed',
							   'ping_status'    => 'closed',
							));

							if ( $saving_plugin_info ) {

							   	// insert post meta
							   	add_post_meta( $saving_plugin_info, '_sejoli_crb_repo_version', $plugin_version );
							   	add_post_meta( $saving_plugin_info, '_sejoli_crb_repo_category', $plugin_category );

						        $wp_filetype = wp_check_filetype( $filename, null );
						        $attachment = array(
						            'guid'           => SEJOLI_PLUGIN_FILE_URL . '/' . $filename,
						            'post_mime_type' => $wp_filetype['type'],
						            'post_title'     => sanitize_file_name($filename),
						            'post_content'   => '',
						            'post_status'    => 'inherit',
						        );

						        $attach_id = wp_insert_attachment( $attachment, $file, $saving_plugin_info );
						        require_once(ABSPATH . 'wp-admin/includes/file.php');
						        // $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
						        // wp_update_attachment_metadata( $attach_id, $attach_data );

							}

	                    	$this->writeLog("Complete.");

		                }

		            }

	            }

	        }

	    endwhile;

	    wp_reset_postdata(); 

	}

	/**
	 * Authenticating with Bitbucket Repositories for Getting plugin file as .zip
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function file_get_contents_curl( $url, $destination ) {

        $fp = fopen( $destination, "w" );
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $ch, CURLOPT_USERPWD, $this->username . ":" . $this->password );
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 500 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 500 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, -1 );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, 'cookiejar' );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, 'cookiejar' );

        $content  = curl_exec( $ch );
        $response = curl_getinfo( $ch );

        curl_close ( $ch );

        return $content;

    }

    /**
	 * Check latest version from core file plugins
	 * @since 	1.0.0
	 * @return 	void
	 */
    public function readCoreFile() {

        $file          = $this->repo_url . "/" . $this->branch . "/" . $this->core_file . ".php?access_token=".$this->access_token;
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
	 * Authenticating with Github Repositories for Getting plugin file as .zip
	 * @since 	1.0.0
	 * @return 	void
	 */
    public function callGitHubAPI( $url, $username ) {

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201' );

        $output = curl_exec( $ch );
        echo $output;

        curl_close( $ch );

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

	/**
	 * Get information regarding our plugin from GitHub
	 * @since 	1.0.0
	 * @return 	void
	 */
	private function getRepoBitbucketReleaseInfo() {

		$repo = $this->user_name.'/'.$this->repo_name;
	    $url  = sprintf( 'https://api.bitbucket.org/2.0/repositories/%s/refs/tags?sort=-target.date', $repo );

	    $response = $this->getContentsBitbucket( $url );

	    if ( $response ) {

	      	$data = json_decode( $response );

	      	if ( isset( $data, $data->values ) && is_array( $data->values ) ) {

	        	$tag = reset( $data->values );

	        	if ( isset( $tag->name ) ) {

	          		$bitbucketAPIResult = $tag;

	    			return $bitbucketAPIResult;

	        	}

	      	}

	    }

	}

	/**
	 * Get Changelog Data From Plugin File
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function getChangelogFile() {

		$file               = $this->repo_url . "/" . $this->branch . "README.txt?access_token=".$this->access_token;
        $core_file_raw      = preg_replace('/github.com/', "raw.githubusercontent.com", $file);
        $changelog_contents = $this->getContents( $core_file_raw );

        if ( preg_match("/\bbitbucket\b/i", $this->repo_url, $match) ) {

            $core_file_raw = $this->base_url . '?access_token=' . $this->access_token;
            $core_file_raw = $this->base_url;

            $repo = $this->user_name.'/'.$this->repo_name;
		    $url  = sprintf( 'https://api.bitbucket.org/2.0/repositories/%s/src/HEAD/changelog', $repo );

		    $changelog_contents = $this->getContentsBitbucket( $url );
		    $decode             = json_decode( $changelog_contents );

		    // No file found or other error.
		    if ( $decode ) {
		      	return false;
		    }

        }

	    return htmlentities( $changelog_contents );

	}

	/**
	 * Set Write Log History
	 * @since 	1.0.0
	 * @return 	void
	 */
    public function writeLog( $param ){

        $fd  = fopen( plugin_dir_path( __FILE__ ) . "../plugins/tmp.txt", "a" ); 
        $msg = "[".date('d/M/Y H:i:s')."] " . $param;

        fwrite( $fd, $msg . "\n" ); 
        fclose( $fd ); 

    }

}