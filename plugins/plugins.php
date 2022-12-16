<?php
require_once '../vendor/autoload.php';

// // Github - Sejoli Lead Gen
// $repo            = "https://github.com/asdzakky/sejoli-lead-gen";
// $branch          = "main/";
// $core_file       = "sejoli-lead-campaign";
// $current_version = "1.0.0";
// $access_token    = "";

// $client = new Updater( $repo, $branch, $core_file, $current_version, $access_token );

// Github - Sejoli Lead Gen
$sejoli_lead_gen = new Updater("https://github.com/asdzakky/sejoli-lead-gen", "main/", "sejoli-lead-campaign", "1.0.0", "");

// Bitbucket - Sejoli Standalone
$sejoli_standalone = new Updater("https://bitbucket.org/orangerdev-team/sejoli-standalon-main-plugin", "master/", "sejoli", "1.11.4", "");
?>
