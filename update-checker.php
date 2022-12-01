<?php
/**
 * Updater Plugin
 */
class Updater {

    private $repo_url     = "";
    private $repo_name    = "";
    private $user_name    = "";
    private $base_url     = "https://api.github.com/repos/_user_/_repo_/branches";
    private $access_token = "";
    private $branch       = "";
    private $core_file    = "";

    function __construct( $repo_url, $branch, $core_file, $current_version, $token = "" ) {

        $this->writeLog("setup Updater");
        $this->repo_url     = $repo_url;
        $this->access_token = $token;
        $this->branch       = $branch;
        $this->core_file    = $core_file;

        // TODO - check on this line is github or bitbucket       
        if ( preg_match("/\bgithub\b/i", $repo_url, $match) ) {

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

        } else {

            $repos_url = preg_split('/\//', $repo_url);

            $this->repo_name = $repos_url[4];
            $this->user_name = $repos_url[3];
            $this->base_url  = "https://bitbucket.org/" . $this->user_name . "/" . $this->repo_name . "/raw/master/" . $core_file . ".php";

            // get repo version
            $this->writeLog("get version from core file");
            $readCoreFile = isset(preg_split('/Version:\ /', $this->readCoreFile())[1]) ? preg_split('/Version:\ /', $this->readCoreFile())[1] : null;
            $contents     = $readCoreFile;
            $repo_version = trim(preg_split('/\n/', $contents)[0]);

        }

        $this->writeLog("compare current version with repo version " . $repo_version ." vs " . $current_version);

        if( !file_exists("./tmp/".$this->core_file."-".$current_version.".zip") ) {

            $this->writeLog("download patch");

            $branch    = preg_replace('/\//', '', $this->branch);
            $file_name = "./tmp/".$this->core_file."-".$current_version.".zip";

            $this->writeLog("start downloading");

            if ( preg_match("/\bbitbucket\b/i", $this->repo_url, $match) ) {

                $url = $repo_url . "/get/".$branch.".zip";
                $destination = $file_name;

                // $file_data = file_get_contents( $repo_url . "/get/".$branch.".zip?access_token=".$this->access_token );
                $file_data = $this->file_get_contents_curl( $url, $destination );
                 
                $handle = fopen( $file_name, 'w' );
                fclose( $handle );

                // Save Content to file
                $downloaded = file_put_contents( $file_name, $file_data );

                if( $downloaded > 0 ) {

                    echo "file from bitbucket has been downloaded! </br></br>";
                    $this->writeLog("Complete.");

                }

            } else {

                $file_data = file_get_contents( $repo_url . "/archive/".$this->branch.".zip?access_token=".$this->access_token );

                $handle = fopen( $file_name, 'w' );
                fclose( $handle );

                // Save Content to file
                $downloaded = file_put_contents( $file_name, $file_data );

                if( $downloaded > 0 ) {

                    echo "file from github has been downloaded! ";
                    $this->writeLog("Complete.");

                }

            }

        } else {

            echo "file has been downloaded! </br></br>";
            $this->writeLog("File exist.");

        }
    }

    function file_get_contents_curl( $url, $destination ) {

        $username = "asdzakky";
        $password = "ATBBJFSMyE8WgzySZVckeDSeVtVD265B1E41";

        $fp = fopen( $destination, "w" );
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $ch, CURLOPT_USERPWD, $username . ":" . $password );
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

        $content = curl_exec( $ch );
        $response = curl_getinfo( $ch );

        curl_close ( $ch );

        return $content;

    }

    function readCoreFile() {

        $file = $this->repo_url . "/" . $this->branch . $this->core_file . ".php?access_token=".$this->access_token;

        $core_file_raw = preg_replace('/github.com/', "raw.githubusercontent.com", $file);

        if ( preg_match("/\bbitbucket\b/i", $this->repo_url, $match) ) {

            $core_file_raw = $this->base_url . '?access_token=' . $this->access_token;

        }
        
        $core_contents = $this->getContents( $core_file_raw );

        return htmlentities( $core_contents );

    }

    function callGitHubAPI( $url, $username ) {

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201' );

        $output = curl_exec( $ch );
        echo $output;

        curl_close( $ch );

    }

    function getContents( $url ) {

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201' );

        $output = curl_exec( $ch );
        curl_close( $ch );

        return $output;

    }

    function writeLog( $param ){

        $fd  = fopen( "./tmp.txt", "a" ); 
        $msg = "[".date('d/M/Y H:i:s')."] " . $param;

        fwrite( $fd, $msg . "\n" ); 
        fclose( $fd ); 

    }

}
?>