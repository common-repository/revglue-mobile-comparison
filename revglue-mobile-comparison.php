<?php 

/*

Plugin Name: RevGlue Mobile Comparison

Description: This plugin connects your wordpress site with RevGlue. It imports the stores, mobiles, mobile deals and banners you selected at RevGlue to your wordpress website.

Version:     1.0.0

Author:      RevGlue

Author URI:  http://www.revglue.com/

License:     GPLv2 or later

License URI: https://www.gnu.org/licenses/gpl-2.0.html

Text Domain: revglue-mcomp

 

RevGlue Mobile Comparison is free software: you can redistribute it and/or modify

it under the terms of the GNU General Public License as published by

the Free Software Foundation, either version 2 of the License, or

any later version.

 

RevGlue Mobile Comparison is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the

GNU General Public License for more details.

 

You should have received a copy of the GNU General Public License

along with RevGlue Mobile Comparison. If not, see https://www.gnu.org/licenses/gpl-2.0.html.

*/



// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;



// Global Variables 

define( 'RGMCOMP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define( 'RGMCOMP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'RGMCOMP_API_URL', 'https://www.revglue.com/' );



function rg_mcomp_install() 

{

	// List of folders we need to create for mobile comparison in WordPress uploads folder

	$dir_structure_array = array( 'revglue', 'mobile-comparison', 'banners' );

	rg_mcomp_create_directory_structures( $dir_structure_array );

	

	rg_mcomp_create_directory_structures( $dir_structure_array );

	

	include( RGMCOMP_PLUGIN_DIR . 'includes/rg-install.php' );

}

register_activation_hook( __FILE__, 'rg_mcomp_install' );

	

function rg_mcomp_uninstall() 

{

	rg_mcomp_remove_directory_structures();

	include( RGMCOMP_PLUGIN_DIR . 'includes/rg-uninstall.php' );

}

register_uninstall_hook( __FILE__, 'rg_mcomp_uninstall' );



require_once( RGMCOMP_PLUGIN_DIR . 'includes/core.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/ajax-calls.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/main-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/import-mobiles-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/import-banners-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/stores-listing-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/mobiles-listing-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/deals-listing-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/banners-listing-page.php' );

require_once( RGMCOMP_PLUGIN_DIR . 'includes/pages/import-mobile-deal-page.php' );

?>