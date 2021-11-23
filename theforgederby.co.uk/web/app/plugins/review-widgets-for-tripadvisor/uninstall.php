<?php
require_once plugin_dir_path( __FILE__ ) . 'plugin-load.php';
$trustindex_pm_tripadvisor = new TrustindexPlugin("tripadvisor", __FILE__, "7.4", "WP Tripadvisor Review Widgets", "Tripadvisor");
$trustindex_pm_tripadvisor->uninstall();
?>