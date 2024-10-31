<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

 

global $wpdb;

global $rg_db_version;

$rg_db_version = '1.0.0';

add_option("rg_db_version", $rg_db_version);

require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

$charset_collate = $wpdb->get_charset_collate();



$table_name = $wpdb->prefix.'rg_projects'; 

$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 

(

	`rg_project_id` int(11) NOT NULL AUTO_INCREMENT,

	`subcription_id` varchar(255) NOT NULL,

	`partner_iframe_id` varchar(255) NOT NULL,

	`user_name` varchar(100) NOT NULL,

	`email` varchar(100) NOT NULL,

	`project` varchar(100) NOT NULL,

	`password` varchar(100) NOT NULL,

	`expiry_date` varchar(100) NOT NULL,

	`status` enum('active','inactive') NOT NULL DEFAULT 'inactive',

	PRIMARY KEY (`rg_project_id`)

) $charset_collate;";

dbDelta($sql);



$table_name = $wpdb->prefix.'rg_stores'; 

$sql = "CREATE TABLE IF NOT EXISTS `$table_name` 

(

	`rg_store_id` int(11) unsigned NOT NULL AUTO_INCREMENT,

	`mid` int(11) NOT NULL,

	`title` varchar(255) DEFAULT NULL,

	`url_key` varchar(255) NOT NULL,

	`description` text,

	`image_url` varchar(255) DEFAULT NULL,

	`affiliate_network` varchar(255) DEFAULT NULL,

	`affiliate_network_link` varchar(255) DEFAULT NULL,

	`store_base_currency` varchar(255) DEFAULT NULL,

	`store_base_country` varchar(255) DEFAULT NULL,

	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

	`status` enum('active','in-active') NOT NULL DEFAULT 'active',

	PRIMARY KEY (`rg_store_id`)

) $charset_collate;";

dbDelta($sql);



$table_name = $wpdb->prefix.'rg_banner'; 

$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 

(

	`rg_id` int(11) NOT NULL AUTO_INCREMENT,

	`rg_store_banner_id` int(11),

	`rg_store_id` int(11),

	`title` varchar(255) NOT NULL,

	`rg_store_name` varchar(255) NOT NULL,

	`image_url` varchar(255) NOT NULL,

	`url` varchar(255) NOT NULL,

	`placement` varchar(100) NOT NULL,

	`rg_size` varchar(50) NOT NULL,

	`banner_type` enum('local','imported') NOT NULL DEFAULT 'local',

	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

	`status` enum('active','inactive') NOT NULL DEFAULT 'active',

	PRIMARY KEY (`rg_id`)

) $charset_collate;";

dbDelta($sql);



$table_name = $wpdb->prefix.'rg_mobiles'; 

$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 

(

	`rg_mobile_id` int(11),

	`title` varchar(255) NOT NULL,

	`brand` varchar(100),

	`model` varchar(100),

	`color` varchar(100),

	`operating_system` varchar(100),

	`width` DECIMAL,

	`height` DECIMAL,

	`thickness` DECIMAL,

	`weight` INT,

	`talk_time` varchar(100),

	`standby_time` varchar(100),

	`battery_capacity` varchar(100),

	`camera` varchar(100),

	`front_camera` varchar(100),

	`resolution` varchar(100),

	`processor` DECIMAL,

	`ram` varchar(100),

	`wifi` enum('yes','no') NOT NULL DEFAULT 'no',

	`wifi_hotspot` enum('yes','no') NOT NULL DEFAULT 'no',

	`three_or_4g` varchar(100),

	`internal_memory` varchar(100),

	`usb` enum('yes','no') NOT NULL DEFAULT 'no',

	`gps` enum('yes','no') NOT NULL DEFAULT 'no',

	`bluetooth` enum('yes','no') NOT NULL DEFAULT 'no',

	`memory_card` enum('yes','no') NOT NULL DEFAULT 'no',

	`touchscreen` enum('yes','no') NOT NULL DEFAULT 'no',

	`sims_capacity` INT,

	`image_url` varchar(255) NOT NULL,

	`description` text,

	`best_mobile_tag` enum('yes','no') NOT NULL DEFAULT 'no',

	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

	`status` enum('active','inactive') NOT NULL DEFAULT 'active',

	PRIMARY KEY (`rg_mobile_id`)

) $charset_collate;";

dbDelta($sql);



$table_name = $wpdb->prefix.'rg_networks'; 

$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 

(

	`rg_network_id` INT(11) NOT NULL AUTO_INCREMENT,

	`network_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`image_url` varchar(255) NOT NULL,


    `show_network_tag` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',

	PRIMARY KEY (`rg_network_id`)
) 
$charset_collate;";
dbDelta($sql);








$table_name = $wpdb->prefix.'rg_brands'; 

$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 

(

	`rg_brand_id` INT(11) NOT NULL AUTO_INCREMENT,

	`brand_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`image_url` varchar(255) NOT NULL,

    `show_brand_tag` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',

	PRIMARY KEY (`rg_brand_id`)
) 
$charset_collate;";
dbDelta($sql);








$table_name = $wpdb->prefix.'rg_mobile_deals'; 

$sql= "CREATE TABLE IF NOT EXISTS `$table_name`

(

 `rg_mobile_deal_id` int(11) NOT NULL,

 `rg_mobile_id` int(11) DEFAULT NULL,

 `rg_store_id` int(11) DEFAULT NULL,

 `network` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `contract_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `contract_term` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,

 `deeplink` varchar(555) COLLATE utf8mb4_unicode_ci NOT NULL,

 `initial_cost` float DEFAULT NULL,

 `month_cost` float DEFAULT NULL,

 `minutes` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `sms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `mbs` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `connectivity` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `gift` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `special_offer` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

 `description` text COLLATE utf8mb4_unicode_ci,

 `recommended_deal_tag` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',

 `date` date NOT NULL,

 `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',

 PRIMARY KEY (`rg_mobile_deal_id`),

 KEY `rg_mobile_id` (`rg_mobile_id`,`rg_store_id`,`network`,`contract_type`),

 KEY `contract_term` (`contract_term`,`title`(191),`initial_cost`,`month_cost`,`minutes`,`sms`,`mbs`)

) 

ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

dbDelta($sql);



?>