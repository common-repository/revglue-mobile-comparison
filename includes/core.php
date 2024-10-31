<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

function rg_mcomp_admin_enqueue()

{

	global $hook_suffix;

	// List of Plugin Pages

	$rg_mcomp_hook_suffixes = array(

		'toplevel_page_revglue-dashboard',

		'revglue-mobile-comparison_page_revglue-import-mobiles-and-stores',

		'revglue-mobile-comparison_page_revglue-import-mobile-deal',

		'revglue-mobile-comparison_page_revglue-import-banners',

		'revglue-mobile-comparison_page_revglue-stores',

		'revglue-mobile-comparison_page_revglue-banners',

		'revglue-mobile-comparison_page_revglue-mobiles',

		'revglue-mobile-comparison_page_revglue-mobile-deals'

	);

	// Only enqueue if current page is one of plugin pages

	if ( in_array( $hook_suffix, $rg_mcomp_hook_suffixes ) ) 

	{

		// Enqueue Admin Styles

		wp_register_style( 'rg-mcomp-chosen', RGMCOMP_PLUGIN_URL . 'admin/css/chosen.css' );

		wp_enqueue_style( 'rg-mcomp-chosen' );

		wp_register_style( 'rg-mcomp-confirm', RGMCOMP_PLUGIN_URL . 'admin/css/jquery-confirm.css' );

		wp_enqueue_style( 'rg-mcomp-confirm' );

		wp_register_style( 'rg-mcomp-confirm-bundled', RGMCOMP_PLUGIN_URL . 'admin/css/bundled.css' );

		wp_enqueue_style( 'rg-mcomp-confirm-bundled' );

		wp_register_style( 'rg-mcomp-jqueryui', RGMCOMP_PLUGIN_URL . 'admin/css/jquery-ui.min.css' );

		wp_enqueue_style( 'rg-mcomp-jqueryui' );

		wp_register_style( 'rg-mcomp-main', RGMCOMP_PLUGIN_URL . 'admin/css/admin_style.css' );

		wp_enqueue_style( 'rg-mcomp-main' );

		wp_register_style( 'rg-mcomp-checkbox', RGMCOMP_PLUGIN_URL . 'admin/css/iphone_style.css' );

		wp_enqueue_style( 'rg-mcomp-checkbox' );

		wp_register_style( 'rg-mcomp-datatables', RGMCOMP_PLUGIN_URL . 'admin/css/jquery.dataTables.css' );

		wp_enqueue_style( 'rg-mcomp-datatables' );

		wp_register_style( 'rg-mcomp-fontawesome', RGMCOMP_PLUGIN_URL . 'admin/css/font-awesome.css' );

		wp_enqueue_style( 'rg-mcomp-fontawesome' );

		// Enqueue Admin Scripts

		wp_register_script( 'rg-mcomp-chosen', RGMCOMP_PLUGIN_URL . 'admin/js/chosen.jquery.js', array ( 'jquery' ) );

		wp_enqueue_script( 'rg-mcomp-chosen' );

		wp_register_script( 'rg-mcomp-datatables', RGMCOMP_PLUGIN_URL . 'admin/js/jquery.dataTables.js', array ( 'jquery' ) );

		wp_enqueue_script( 'rg-mcomp-datatables' );

		wp_register_script( 'rg-mcomp-unveil', RGMCOMP_PLUGIN_URL . 'admin/js/jquery.unveil.js', array ( 'jquery' ) );

		wp_enqueue_script( 'rg-mcomp-unveil' );

		wp_register_script( 'rg-mcomp-checkbox', RGMCOMP_PLUGIN_URL . 'admin/js/iphone-style-checkboxes.js', array ( 'jquery' ) );

		wp_enqueue_script( 'rg-mcomp-checkbox' );

		wp_register_script( 'rg-mcomp-confirm', RGMCOMP_PLUGIN_URL . 'admin/js/jquery-confirm.js', array ( 'jquery' ) );

		wp_enqueue_script( 'rg-mcomp-confirm' );

		wp_register_script( 'rg-mcomp-main', RGMCOMP_PLUGIN_URL . 'admin/js/main.js', array ( 'jquery', 'jquery-form' ) );

		wp_enqueue_script( 'rg-mcomp-main' );

		wp_localize_script( 'rg-mcomp-main', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_media();

	}

}

add_action( 'admin_enqueue_scripts', 'rg_mcomp_admin_enqueue' );

function rg_mcomp_admin_actions() 

{

	add_menu_page('RevGlue Mobile Comparison', 'RevGlue Mobile Comparison', 'manage_options', 'revglue-dashboard', 'rg_mcomp_main_page', RGMCOMP_PLUGIN_URL .'admin/images/menuicon.png' );

	add_submenu_page('revglue-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'revglue-dashboard', 'rg_mcomp_main_page');

	add_submenu_page('revglue-dashboard', 'Import Mobiles', 'Import Mobile Data', 'manage_options', 'revglue-import-mobiles-and-stores', 'rg_mcomp_mobile_import_page');

	add_submenu_page('revglue-dashboard', 'Stores', 'Stores', 'manage_options', 'revglue-stores', 'rg_mcomp_store_listing_page');

	add_submenu_page('revglue-dashboard', 'Mobiles', 'Mobiles', 'manage_options', 'revglue-mobiles', 'rg_mcomp_mobile_listing_page');		

	add_submenu_page('revglue-dashboard', 'Import Mobile Deals', 'Import Mobile Deals', 'manage_options', 'revglue-import-mobile-deal', 'rg_mcomp_mobile_deal_import_page');

	add_submenu_page('revglue-dashboard', 'Mobile Deals', 'Mobile Deals', 'manage_options', 'revglue-mobile-deals', 'rg_mcomp_deal_listing_page');

	

}

add_action( 'admin_menu', 'rg_mcomp_admin_actions' );

function rg_mcomp_create_directory_structures( $dir_structure_array )

{

	$upload = wp_upload_dir();

	$base_dir = $upload['basedir'];

	foreach( $dir_structure_array as $single_dir )

	{

		$create_dir = $base_dir.'/'.$single_dir;

		if ( ! is_dir( $create_dir ) ) 

		{

			mkdir( $create_dir, 0755 );

		}

		$base_dir = $create_dir;

	}

}

function rg_mcomp_remove_directory_structures()

{

	$upload = wp_upload_dir();

	$base_dir = $upload['basedir'].'\revglue';

	rg_mcomp_folder_cleanup($base_dir);

}

function rg_mcomp_folder_cleanup( $dirpath )

{

	if( substr( $dirpath, strlen($dirpath) - 1, 1 ) != '/' )

	{

        $dirpath .= '/';

    }

	$files = glob($dirpath . '*', GLOB_MARK);

	foreach( $files as $file )

	{

		if( is_dir( $file ) )

		{

			deleteDir($file);

		}

		else

		{

			unlink($file);

        }

    }

	rmdir($dirpath);

}

function rg_mcomp_auto_import_data()

{

    $auto_var = basename( $_SERVER["REQUEST_URI"] );

	if ( $auto_var ==  'auto_import_data') 

	{

		include( RGMCOMP_PLUGIN_DIR . 'includes/auto-import-data.php');

	}

}

add_action( 'template_redirect', 'rg_mcomp_auto_import_data' );

 function revglue_mcopm_user_subscription_id() {

		global $wpdb;

		$rg_projects_table = $wpdb->prefix.'rg_projects'; 

		$sql = "SELECT  email FROM $rg_projects_table where email !='' limit 1";

		$email = $wpdb->get_var($sql);

		if ($email =='') {

		echo '<div class="notice notice-success is-dismissible subscriptiondone" style = "text-align:center;">  ';

		echo  '<p>'. _e( "Please read the instructions on  <a href=\"admin.php?page=revglue-dashboard\" target=\"_blank\">RevGlue Dashbaord</a> for importing your RevGlue projects data.", "notice" ).' </p>';

		echo  '</div>'; 

		} 

}

add_action( 'admin_notices', 'revglue_mcopm_user_subscription_id' );

/**************************************************************************************************

*

* Remove Wordpress dashboard default widgets

*

***************************************************************************************************/

function rg_remove_default_widgets(){

	remove_action('welcome_panel', 'wp_welcome_panel');

	remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

	remove_meta_box( 'dashboard_quick_press',   'dashboard', 'side' );      //Quick Press widget

	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );      //Recent Drafts

	remove_meta_box( 'dashboard_primary',       'dashboard', 'side' );      //WordPress.com Blog

	remove_meta_box( 'dashboard_incoming_links','dashboard', 'normal' );    //Incoming Links

	remove_meta_box( 'dashboard_plugins',       'dashboard', 'normal' );    //Plugins

	remove_meta_box('dashboard_activity', 'dashboard', 'normal');

}

add_action('wp_dashboard_setup', 'rg_remove_default_widgets');

/*function remove_core_updates(){

global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);

}*/

//add_filter('pre_site_transient_update_core','remove_core_updates'); //hide updates for WordPress itself

//add_filter('pre_site_transient_update_plugins','remove_core_updates'); //hide updates for all plugins

//add_filter('pre_site_transient_update_themes','remove_core_updates'); //hide updates for all themes	