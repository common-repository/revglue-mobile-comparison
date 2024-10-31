<?php 

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;



global $wpdb;

$project_table		= $wpdb->prefix.'rg_projects';

$stores_table		= $wpdb->prefix.'rg_stores';

$banner_table		= $wpdb->prefix.'rg_banner';

$mobile_table		= $wpdb->prefix.'rg_mobiles'; 

$mobile_deals_table	= $wpdb->prefix.'rg_mobile_deals';

$networks	= $wpdb->prefix.'rg_networks';

$brands	= $wpdb->prefix.'rg_brands';



delete_option('rg_db_version');



require_once(ABSPATH . '/wp-admin/includes/upgrade.php');



$sql = "DROP TABLE IF EXISTS `$project_table`";

$wpdb->query($sql);



$sql = "DROP TABLE IF EXISTS `$stores_table`";

$wpdb->query($sql);



$sql = "DROP TABLE IF EXISTS `$banner_table`";

$wpdb->query($sql);



$sql = "DROP TABLE IF EXISTS `$mobile_table`";

$wpdb->query($sql);



$sql = "DROP TABLE IF EXISTS `$mobile_deals_table`";

$wpdb->query($sql);

$sql = "DROP TABLE IF EXISTS `$networks`";

$wpdb->query($sql);

$sql = "DROP TABLE IF EXISTS `$brands`";

$wpdb->query($sql);



?>