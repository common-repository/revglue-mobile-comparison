<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function revglue_comp_update_best_selling_mobile()
{
	global $wpdb;
	$rg_mobile_id = sanitize_text_field($_REQUEST['rg_mobile_id']);
	$checked = sanitize_text_field($_REQUEST['checked']);
	$mobiles_table = $wpdb->prefix.'rg_mobiles';
	$wpdb->query("update $mobiles_table set best_mobile_tag='$checked' where rg_mobile_id='$rg_mobile_id'");
	die();
}
add_action( 'wp_ajax_revglue_comp_update_best_selling_mobile', 'revglue_comp_update_best_selling_mobile' );
function revglue_mcomp_subscription_validate() 
{
	global $wpdb;
	$project_table = $wpdb->prefix.'rg_projects';
	$sanitized_sub_id	= sanitize_text_field( $_REQUEST['sub_id']);
	$sanitized_email	= sanitize_email( $_REQUEST['sub_email']);
	$password  			= $_REQUEST['sub_pass'];
	$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/validate_subscription_key/$sanitized_email/$password/$sanitized_sub_id", array( 'timeout' => 120, 'sslverify'   => false ) ) ), true);
	$result = $resp_from_server['response']['result'];
	//pre($result);
	//die;
	$iFrameid =$result['iframe_id'];
	$data=array();
	if($iFrameid!=""){
		$data=array(
		'subcription_id' 	=> $sanitized_sub_id,
		'user_name' 		=> $result['user_name'],
		'email' 			=> $result['email'],
		'project' 			=> $result['project'],
		'password'     		=> $password,
		'expiry_date' 		=> $result['expiry_date'],
		'partner_iframe_id'	=> $result['iframe_id'],
		'status' 			=> $result['status']
		);
	} else{
		$data=array(
		'subcription_id' 	=> $sanitized_sub_id,
		'user_name' 		=> $result['user_name'],
		'email' 			=> $result['email'],
		'project' 			=> $result['project'],
		'password'     		=> $password,
		'expiry_date' 		=> $result['expiry_date'],
		'status' 			=> $result['status']
		);
	}
	$string = '';
	if( $resp_from_server['response']['success'] == 'true')
	{
		$sql = "Select *FROM $project_table Where project like '".$result['project']."' and status = 'active'";
	    $execute_query = $wpdb->get_results( $sql );
		$rows = $wpdb->num_rows;
		if( empty ( $rows ))
		{
			$string .= "<div class='panel-white mgBot'>";
			if($iFrameid!=""){
				$string .= "<p><b>Your RevEmbed Mobile Comparison subscription data is ". $result['status'].".</b><img  class='tick-icon' src=".RGMCOMP_PLUGIN_URL. 'admin/images/ticks_icon.png'." />  </p>";
				$string .= "<p><b>Name = </b> RevEmbed Data </p>";
				$string .= "<p><b>Project = </b>".$result['project']." UK </p>";
				$string .= "<p><b>Email = </b>".$result['email']."</p>";
			} else{
			$string .= "<p><b>Your Mobile Comparison subscription is ". $result['status'].".</b><img  class='tick-icon' src=".RGMCOMP_PLUGIN_URL. 'admin/images/ticks_icon.png'." />  </p>";
			$string .= "<p><b>Name = </b>".$result['user_name']."</p>";
			$string .= "<p><b>Project = </b>".$result['project']."</p>";
			$string .= "<p><b>Email = </b>".$result['email']."</p>";
			$string .= "<p><b>Expiry Date = </b>".date("d-M-Y", strtotime($result['expiry_date']))."</p>";
			$string .= "</div>";
			}
			$wpdb->insert(
				$project_table,
				$data
			);
		} else
		{
			$string .= "<div class='alert alert-info' role='alert'>You already have subscription of this project, thankyou! </div>";
		}
	} else
	{
		$string .= "<div class='alert alert-danger' role='alert'>&raquo; Your subscription unique ID <b class='grmsg'> ". $sanitized_sub_id ." </b> is Invalid.</div>";
	}
	echo $string;
	wp_die();
}
add_action( 'wp_ajax_revglue_mcomp_subscription_validate', 'revglue_mcomp_subscription_validate' );
function revglue_mcomp_data_import()
{
	global $wpdb;
	$project_table = $wpdb->prefix.'rg_projects';
	$stores_table = $wpdb->prefix.'rg_stores';
	$mobiles_table = $wpdb->prefix.'rg_mobiles';
	$brands_table = $wpdb->prefix.'rg_brands';
	$mobile_deals_table	= $wpdb->prefix.'rg_mobile_deals';
	$date = date("Y-m-d H:i:s");
	$string = '';
	$import_type = sanitize_text_field( $_REQUEST['import_type']);
	$sql = "SELECT *FROM $project_table where project NOT LIKE 'banners uk'";
	$project_detail = $wpdb->get_results($sql);
	$rows = $wpdb->num_rows;
	if( !empty ( $rows ))
	{
		$subscriptionid 	= 	$project_detail[0]->subcription_id;
		$useremail 			= 	$project_detail[0]->email;
		$userpassword 		= 	$project_detail[0]->password;
		$projectid 			= 	$project_detail[0]->partner_iframe_id;
		if( $import_type == 'rg_stores_import')
		{
			rg_update_subscription_expiry_date($subscriptionid, $userpassword, $useremail, $projectid);
			if($project_detail[0]->expiry_date=="Free" && $project_detail[0]->partner_iframe_id!="" )
			{
			$partner_broadband_stores_url ="https://www.revglue.com/partner/mobile_stores/".$project_detail[0]->partner_iframe_id."/json/wp";
			//die($partner_broadband_stores_url);
			$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $partner_broadband_stores_url, array( 'timeout' => 12000, 'sslverify'   => false ) ) ), true);
			} else {
				//die(RGMCOMP_API_URL . "api/mobile_stores/json/".$project_detail[0]->subcription_id);
			$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/mobile_stores/json/".$project_detail[0]->subcription_id, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true);
			}
			$result = $resp_from_server['response']['stores'];
	  		if($resp_from_server['response']['success'] == 1 )
			{
				foreach($result as $row)
				{
					$sqlinstore = "Select rg_store_id FROM $stores_table Where rg_store_id = '".$row['rg_store_id']."'";
					$rg_store_exists = $wpdb->get_var( $sqlinstore);
					if( empty( $rg_store_exists))
					{
						$wpdb->insert(
							$stores_table,
							array(
								'rg_store_id' 				=> $row['rg_store_id'],
								'mid' 						=> $row['affiliate_network_mid'],
								'title' 					=> $row['store_title'],
								'url_key' 					=> $row['url_key'],
								'description' 				=> $row['store_description'],
								'image_url' 				=> $row['image_url'],
								'affiliate_network' 		=> $row['affiliate_network'],
								'affiliate_network_link'	=> $row['affiliate_network_link'],
								'store_base_currency' 		=> $row['store_base_currency'],
								'store_base_country' 		=> $row['store_base_country'],
								'date' 						=> $date
							)
						);
					} else
					{
						$wpdb->update(
							$stores_table,
							array(
								'mid' 						=> $row['affiliate_network_mid'],
								'title' 					=> $row['store_title'],
								'url_key' 					=> $row['url_key'],
								'description' 				=> $row['store_description'],
								'image_url' 				=> $row['image_url'],
								'affiliate_network' 		=> $row['affiliate_network'],
								'affiliate_network_link'	=> $row['affiliate_network_link'],
								'store_base_currency' 		=> $row['store_base_currency'],
								'store_base_country' 		=> $row['store_base_country'],
								'date' 						=> $date
							),
							array( 'rg_store_id' => $rg_store_exists )
						);
					}
				}
				$wpdb->query( "delete from $stores_table WHERE `date` != '$date'");
			} else
			{
				$string .= '<div class="alert alert-danger" role="alert">'.$resp_from_server['response']['message'].'</div>';
			}
		} else if( $import_type == 'rg_mobiles_import')
		{
			rg_update_subscription_expiry_date($subscriptionid, $userpassword, $useremail, $projectid);
$sql4 = "SELECT * FROM $brands_table where show_brand_tag ='yes'";
	$rc = $wpdb->get_results($sql4);
	$limitbrand = 4-count($rc);
	$key_b = 0;
	$brands = array();
	foreach($rc as $rr){
		$brands[] = $rr->brand_title;
	}
	if($project_detail[0]->expiry_date=="Free" && $project_detail[0]->partner_iframe_id!="")
			{
			$partner_broadband_stores_url ="https://www.revglue.com/partner/mobiles/".$project_detail[0]->partner_iframe_id."/json/wp";
			//die($partner_broadband_stores_url);
			$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $partner_broadband_stores_url, array( 'timeout' => 12000, 'sslverify'   => false ) ) ), true);
			} else {
				//die( RGMCOMP_API_URL . "api/mobiles/json/".$project_detail[0]->subcription_id);
			$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/mobiles/json/".$project_detail[0]->subcription_id, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true);
			}
			$result = $resp_from_server['response']['mobiles'];
	  		if($resp_from_server['response']['success'] == 1 )
			{
				foreach($result as $row)
				{
						$show_brand_tag	=  'no';
						// pre($key_b.' < '.$limitbrand );
						if(!in_array($row['brand'], $brands)){
							$show_brand_tag	=  'no';
							if($key_b < $limitbrand){
								$brands[] = $row['brand'];
						 		$key_b++;
						 		$show_brand_tag	=  'yes';
							}
					 	 }
						if(in_array($row['brand'], $brands)){
						 		$show_brand_tag	=  'yes';
						}
					 	$sqlbrand = "Select * FROM $brands_table where brand_title = '".$row['brand']."'";
					 	$brands_exist = $wpdb->get_row( $sqlbrand );
					 	// $wpdb->show_errors();
					 	if( empty( $brands_exist ))
					 	{
					 		$image_url = get_template_directory_uri(). '/assets/images/brandsnetworks/' .$row['brand'].'.png';
						 	$wpdb->insert(
						 	$brands_table,
						 	array(
						 		'brand_title' 	=> $row['brand'],
						 		'show_brand_tag' 	=> $show_brand_tag,
						 		'image_url' 	=> $image_url,
								)
							);
							$rg_brand_id = $wpdb->insert_id;
						}
						 else
						{
							$image_url = get_template_directory_uri(). '/assets/images/brandsnetworks/' .$row['brand'].'.png';
							$rg_brand_id = $brands_exist->rg_brand_id;
							// pre($brands_table);
							$wpdb->update(
						 	$brands_table,
						 		array(
							 		'brand_title' 	=> $row['brand'],
							 		'show_brand_tag' 	=> $show_brand_tag,
							 		 'image_url' 	=> $image_url
								),
						 		array('rg_brand_id'=>$rg_brand_id)
							);
							// echo $wpdb->last_query.'<br />';
						}
					$sqlinstore = "Select rg_mobile_id FROM $mobiles_table Where rg_mobile_id = '".$row['mobile_id']."'";
					$rg_mobile_exists = $wpdb->get_var( $sqlinstore );
					if( empty( $rg_mobile_exists ))
					{
						$wpdb->insert(
							$mobiles_table, 
							array( 
								'rg_mobile_id' 		=> $row['mobile_id'],
								'title' 			=> $row['mobile_title'],
								'brand' 			=> $rg_brand_id,
								'model' 			=> $row['model'],
								'color' 			=> $row['color'],
								'operating_system'	=> $row['operating_system'],
								'width' 			=> $row['width'],
								'height' 			=> $row['height'],
								'thickness' 		=> $row['thickness'],
								'weight' 			=> $row['weight'],
								'talk_time' 		=> $row['talk_time'],
								'standby_time' 		=> $row['standby_time'],
								'battery_capacity' 	=> $row['battery_capacity'],
								'camera' 			=> $row['back_camera'],
								'front_camera' 		=> $row['front_camera'],
								'resolution' 		=> $row['resolution'],
								'processor' 		=> $row['processor'],
								'ram' 				=> $row['ram'],
								'wifi' 				=> $row['wifi'],
								'wifi_hotspot' 		=> $row['wifi_hotspot'],
								'three_or_4g' 		=> $row['three_or_4g'],
								'internal_memory'	=> $row['internal_memory'],
								'usb' 				=> $row['usb'],
								'gps' 				=> $row['gps'],
								'bluetooth' 		=> $row['bluetooth'],
								'memory_card' 		=> $row['memory_card'],
								'touchscreen' 		=> $row['touchscreen'],
								'sims_capacity' 	=> $row['sims_capacity'],
								'image_url' 		=> $row['mobile_image'],
								'description' 		=> $row['mobile_description'],
								'date' 				=> $date
							) 
						);
					} else
					{
						$wpdb->update(
							$mobiles_table,
							array( 
								'title' 			=> $row['mobile_title'],
								'brand' 			=> $rg_brand_id,
								'model' 			=> $row['model'],
								'color' 			=> $row['color'],
								'operating_system'	=> $row['operating_system'],
								'width' 			=> $row['width'],
								'height' 			=> $row['height'],
								'thickness' 		=> $row['thickness'],
								'weight' 			=> $row['weight'],
								'talk_time' 		=> $row['talk_time'],
								'standby_time' 		=> $row['standby_time'],
								'battery_capacity' 	=> $row['battery_capacity'],
								'camera' 			=> $row['back_camera'],
								'front_camera' 		=> $row['front_camera'],
								'resolution' 		=> $row['resolution'],
								'processor' 		=> $row['processor'],
								'ram' 				=> $row['ram'],
								'wifi' 				=> $row['wifi'],
								'wifi_hotspot' 		=> $row['wifi_hotspot'],
								'three_or_4g' 		=> $row['three_or_4g'],
								'internal_memory'	=> $row['internal_memory'],
								'usb' 				=> $row['usb'],
								'gps' 				=> $row['gps'],
								'bluetooth' 		=> $row['bluetooth'],
								'memory_card' 		=> $row['memory_card'],
								'touchscreen' 		=> $row['touchscreen'],
								'sims_capacity' 	=> $row['sims_capacity'],
								'image_url' 		=> $row['mobile_image'],
								'description' 		=> $row['mobile_description'],
								'date' 				=> $date
							),
							array( 'rg_mobile_id' => $rg_mobile_exists )
						);
					}
				}
				$wpdb->query( "DELETE FROM $mobiles_table WHERE `date` != '$date' " );
				 $sqlMQ = "SELECT rg_mobile_id FROM $mobiles_table WHERE  `best_mobile_tag`='no'  LIMIT 12";
						$MCData = $wpdb->get_results( $sqlMQ );
						foreach ($MCData as $Mdata) {
								$wpdb->update(
										$mobiles_table,
										array(
											'best_mobile_tag' 	=> 'yes'
										),
										array( 'rg_mobile_id' => $Mdata->rg_mobile_id )
									);
								}
			} else
			{
				$string .= '<div class="alert alert-danger" role="alert">'.$resp_from_server['response']['message'].'</div>';
			}
		} else if( $import_type == 'rg_deals_import' )
		{
			rg_update_subscription_expiry_date($subscriptionid, $userpassword, $useremail, $projectid);
			$i = 0;
			$page = 1;
			do {
				$store_sql = "SELECT rg_store_id FROM $stores_table";
				$fetch_store_id = get_var($store_sql);
				$store_id = $fetch_store_id;
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get(RGMCOMP_API_URL . "api/mobile_deals/json/".$project_detail[0]->subcription_id."/".$store_id."/".$page, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true);
				$total = ceil( $resp_from_server['response']['mobile_deals_total'] / 1000 ) ;
				$result = $resp_from_server['response']['mobile_deals'];
				if($resp_from_server['response']['success'] == 1 )
				{
					foreach($result  as $row)
					{
						$rg_mobile_deal_id	=  $row['mobile_deal_id'];
						$rg_mobile_id 		= $row['mobile_id'];
						$rg_store_id 		= $row['rg_store_id'];
						$network 			= $row['mobile_operator'];
						$contract_type 		= $row['contact_type'];
						$contract_term 		= $row['contract_term_months'];
						$title 				= strip_tags(addslashes($row['mobile_deal_title']));
						$description 		= strip_tags(addslashes($row['mobile_deal_descriptin']));
						$deeplink			= $row['mobile_deal_link'];
						$initial_cost 		= $row['initial_cost'];
						$month_cost 		= $row['monthly_cost'];
						$minutes 			= $row['minutes'];
						$sms 				= $row['sms'];
						$mbs 				= $row['data_mb'];
						$connectivity 		= $row['connectivity'];
						$gift 				= $row['gift'];
						$special_offer 		= $row['special_offer'];
						$image_url 			= $row['image'];
						$date 				= $date;
								$insQuery=	"INSERT INTO $mobile_deals_table (
										`mobile_deal_id`,
										 `mobile_id`,
										 `rg_store_id`,
										 `mobile_operator`,
										 `contact_type`,
										 `contract_term_months`,
										 `mobile_deal_title`,
										 `mobile_deal_descriptin`,
										 `mobile_deal_link`,
										 `initial_cost`,
										 `monthly_cost`,
										 `minutes`,
										 `sms`,
										 `data_mb`,
										 `connectivity`,
										 `gift`,
										 `special_offer`,
										 `image`,
										 `date`) VALUES
										 (
										 '$mobile_deal_id',
										 '$mobile_id',
										 '$rg_store_id',
										 '$mobile_operator',
										 '$contact_type',
										 '$contract_term_months',
										 '$mobile_deal_title',
										 '$mobile_deal_descriptin',
										 '$category_ids',
										 '$mobile_deal_link',
										 '$initial_cost',
										 '$monthly_cost',
										 '$minutes',
										 '$sms',
										 '$data_mb',
										 '$connectivity',
										 '$gift',
										 '$special_offer',
										 '$image',
										 '$date') ON DUPLICATE KEY UPDATE `date` = VALUES(date)";
								$wpdb->query($insQuery);
								//echo $wpdb->last_query;
					}
					$wpdb->query( "DELETE FROM $mobile_deals_table WHERE `date` != '$date' " );
								// echo $wpdb->last_query;
				} else
				{
					$string .= '<div class="alert alert-danger" role="alert">'.$resp_from_server['response']['message'].'</div>';
				}
				$i++;
				$page++;
			} while ( $i < $total );
		}
	} else
	{
		$string .= "<div class='alert alert-danger' role='alert'>Please subscribe for your RevGlue project first, then you have the facility to import the data</div>";
	}
	$response_array = array();
	$response_array['error_msgs'] = $string;
	$sql = "SELECT MAX(date) FROM $stores_table";
	$last_updated_store = $wpdb->get_var($sql);
	$response_array['last_updated_store'] = ( $last_updated_store ? date( "l , d-M-Y H:i:s", strtotime( $last_updated_store ) ) : '-' );
	$sql_1 = "SELECT count(*) as stores FROM $stores_table";
	$count_store = $wpdb->get_results($sql_1);
	$response_array['count_store'] = $count_store[0]->stores;
	$sql_2 = "SELECT MAX(date) FROM $mobiles_table";
	$last_updated_mobile = $wpdb->get_var($sql_2);
	$response_array['last_updated_mobile'] = ( $last_updated_mobile ? date( "l , d-M-Y H:i:s", strtotime( $last_updated_mobile ) ) : '-' );
	$sql_3 = "SELECT count(*) as mobiles FROM $mobiles_table";
	$count_mobile = $wpdb->get_results($sql_3);
	$response_array['count_mobile'] = $count_mobile[0]->mobiles;
	$sql_4 = "SELECT MAX(date) FROM $mobile_deals_table";
	$last_updated_deal = $wpdb->get_var($sql_4);
	$response_array['last_updated_deal'] = ( $last_updated_deal ? date( "l , d-M-Y H:i:s", strtotime( $last_updated_deal ) ) : '-' );
	$sql_5 = "SELECT count(*) as deals FROM $mobile_deals_table";
	$count_deal = $wpdb->get_results($sql_5);
	$response_array['count_deal'] = $count_deal[0]->deals;
	echo json_encode($response_array);
	wp_die();
}
add_action( 'wp_ajax_revglue_mcomp_data_import', 'revglue_mcomp_data_import' );
function revglue_mcomp_banner_data_import()
{
	global $wpdb;
	$project_table = $wpdb->prefix.'rg_projects';
	$banner_table = $wpdb->prefix.'rg_banner';
	$date =date("Y-m-d H:i:s");
	$string = '';
	$import_type = sanitize_text_field( $_REQUEST['import_type']);
	$sql = "SELECT *FROM $project_table where project like 'Banners UK'";
	$project_detail = $wpdb->get_results($sql);
	$rows = $wpdb->num_rows;
	if( !empty ( $rows ))
	{
		if( $import_type == 'rg_banners_import')
		{
			$i = 0;
			$page = 1;
			do {
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/banners/json/".$project_detail[0]->subcription_id."/".$page, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true);
				$total = ceil( $resp_from_server['response']['banners_total'] / 1000 ) ;
				$result = $resp_from_server['response']['banners'];
				if($resp_from_server['response']['success'] == 'true' )
				{
					foreach($result as $row)
					{
						// print_r(expression)
						$sqlinstore = "SELECT rg_store_banner_id FROM $banner_table WHERE rg_store_banner_id = '".$row['store_banner_id']."' AND `banner_type` = 'imported'";
						$rg_banner_exists = $wpdb->get_var( $sqlinstore );
						if( empty( $rg_banner_exists ))
						{
							$wpdb->insert(
								$banner_table,
								array(
									'rg_store_banner_id' 	=> $row['rg_banner_id'],
									'rg_store_id' 			=> $row['rg_store_id'],
									'title' 				=> $row['banner_alt_text'],
									'image_url' 			=> $row['banner_image_url'],
									'url' 					=> $row['deep_link'],
									'rg_size' 					=> $row['width_pixels']."x".$row['height_pixels'],
									'placement' 			=> 'unassigned',
									'banner_type' 			=> 'imported',
									'date'					=> $date
								)
							);
						} else
						{
							$wpdb->update(
								$banner_table,
								array(
									'rg_store_banner_id' 	=> $row['rg_banner_id'],
									'rg_store_id' 			=> $row['rg_store_id'],
									'title' 				=> $row['banner_alt_text'],
									'image_url' 			=> $row['banner_image_url'],
									'url' 					=> $row['deep_link'],
									'rg_size' 					=> $row['width_pixels']."x".$row['height_pixels'],
									'date'					=> $date
								),
								array( 'rg_store_banner_id' => $rg_banner_exists )
							);
						}				
					}
					$wpdb->query( "DELETE FROM $banner_table WHERE `date` != '$date' " );
				} else
				{
					$string .= '<div class="alert alert-danger" role="alert">'.$resp_from_server['response']['message'].'</div>';
				}
				$i++;
				$page++;
			} while ( $i < $total );
		}
	} else
	{
		$string .= "<div class='alert alert-danger' role='alert'>Please subscribe for your RevGlue project first, then you have the facility to import the data</div>";
	}
	$response_array = array();
	$response_array['error_msgs'] = $string;
	$sql1 = "SELECT count(*) as banner FROM $banner_table where banner_type= 'imported'";
	$count_banner = $wpdb->get_results($sql1);
	$response_array['count_banner'] = $count_banner[0]->banner;
	echo json_encode($response_array);
	wp_die();
}
add_action( 'wp_ajax_revglue_mcomp_banner_data_import', 'revglue_mcomp_banner_data_import' );
function revglue_mcomp_data_delete()
{
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$mobiles_table = $wpdb->prefix.'rg_mobiles';
	$banner_table = $wpdb->prefix.'rg_banner';
	$mobile_deals_table	= $wpdb->prefix.'rg_mobile_deals'; 
	$data_type = sanitize_text_field( $_REQUEST['data_type'] );
	$response_array = array();
	if( $data_type == 'rg_stores_delete' )
	{
		$response_array['data_type'] = 'rg_stores';
		$wpdb->query( "DELETE FROM $stores_table" );	
		$sql = "SELECT MAX(date) FROM $stores_table";
		$last_updated_store = $wpdb->get_var($sql);
		$response_array['last_updated_store'] = ( $last_updated_store ? date( 'l , d-M-Y , h:i A', strtotime( $last_updated_store ) ) : '-' );
		$sql2 = "SELECT count(*) as stores FROM $stores_table";
		$count_store = $wpdb->get_results($sql2);
		$response_array['count_store'] = $count_store[0]->stores;
	} else if( $data_type == 'rg_mobiles_delete' )
	{
		$response_array['data_type'] = 'rg_mobiles';
		$wpdb->query( "DELETE FROM $mobiles_table" );	
		$sql = "SELECT MAX(date) FROM $mobiles_table";
		$last_updated_mobile = $wpdb->get_var($sql);
		$response_array['last_updated_mobile'] = ( $last_updated_mobile ? date( 'l , d-M-Y , h:i A', strtotime( $last_updated_mobile ) ) : '-' );
		$sql2 = "SELECT count(*) as mobiles FROM $mobiles_table";
		$count_mobile = $wpdb->get_results($sql2);
		$response_array['count_mobile'] = $count_mobile[0]->mobiles;
	} else if( $data_type == 'rg_deals_delete' )
	{
		$response_array['data_type'] = 'rg_deals';
		$wpdb->query( "DELETE FROM $mobile_deals_table" );	
		$sql = "SELECT MAX(date) FROM $mobile_deals_table";
		$last_updated_deal = $wpdb->get_var($sql);
		$response_array['last_updated_deal'] = ( $last_updated_deal ? date( 'l , d-M-Y , h:i A', strtotime( $last_updated_deal ) ) : '-' );
		$sql2 = "SELECT count(*) as deals FROM $mobile_deals_table";
		$count_mobile = $wpdb->get_results($sql2);
		$response_array['count_deal'] = $count_mobile[0]->deals;
	} else if( $data_type == 'rg_banners_delete' )
	{
		$response_array['data_type'] = 'rg_banners';
		$wpdb->query( "DELETE FROM $banner_table where banner_type='imported'" );	
		$sql1 = "SELECT count(*) as banner FROM $banner_table where banner_type= 'imported'";
		$count_banner = $wpdb->get_results($sql1);
		$response_array['count_banner'] = $count_banner[0]->banner;
	}
	echo json_encode($response_array);
	wp_die();
}
add_action( 'wp_ajax_revglue_mcomp_data_delete', 'revglue_mcomp_data_delete' );
function revglue_mcomp_update_best_mobile()
{
	global $wpdb; 
	$mobiles_table = $wpdb->prefix.'rg_mobiles';
	$mobile_id		= absint( $_REQUEST['mobile_id'] );
	$mobile_state 	= sanitize_text_field( $_REQUEST['state'] );
	$wpdb->update( 
		$mobiles_table, 
		array( 'best_mobile_tag' => $mobile_state ), 
		array( 'rg_mobile_id' => $mobile_id )
	);
	echo $mobile_id;
	wp_die();
}
add_action( 'wp_ajax_revglue_mcomp_update_best_mobile', 'revglue_mcomp_update_best_mobile' );
function revglue_mcomp_update_recommended_deal()
{
	global $wpdb; 
	$mobile_deals_table = $wpdb->prefix.'rg_mobile_deals';
	$deal_id		= absint( $_REQUEST['deal_id'] );
	$deal_state 	= sanitize_text_field( $_REQUEST['state'] );
	$wpdb->update( 
		$mobile_deals_table, 
		array( 'recommended_deal_tag' => $deal_state ), 
		array( 'rg_mobile_deal_id' => $deal_id )
	);
	echo $deal_id;
	//echo $wpdb->last_query;
	wp_die();
}
add_action( 'wp_ajax_revglue_mcomp_update_recommended_deal', 'revglue_mcomp_update_recommended_deal' );
function revglue_mcomp_load_banners()
{
	global $wpdb; 
	$stores_table = $wpdb->prefix.'rg_stores';
	$sTable = $wpdb->prefix.'rg_banner';
	$upload = wp_upload_dir();
	$base_url = $upload['baseurl'];
	$uploadurl = $base_url.'/revglue/mobile-comparison/banners/';
	$placements = array(
		'home-top'				=> 'Home:: Top Header',
		'home-slider'			=> 'Home:: Main Banners',
		'home-mid'				=> 'Home:: After Categories',
		'home-bottom'			=> 'Home:: Before Footer',
		'cat-top'				=> 'Category:: Top Header',
		'cat-side-top'			=> 'Category:: Top Sidebar',
		'cat-side-bottom'		=> 'Category:: Bottom Sidebar 1',
		'cat-side-bottom-two'	=> 'Category:: Bottom Sidebar 2',
		'cat-bottom'			=> 'Category:: Before Footer',
		'store-top'				=> 'Store:: Top Header',
		'store-side-top'		=> 'Store:: Top Sidebar',
		'store-side-bottom'		=> 'Store:: Bottom Sidebar 1',
		'store-side-bottom-two'	=> 'Store:: Bottom Sidebar 2',
		'store-main-bottom'		=> 'Store:: After Review',
		'store-bottom'			=> 'Store:: Before Footer',
		'unassigned' 			=> 'Unassigned Banners'
	);
	$aColumns = array( 'banner_type', 'placement', 'status', 'title', 'url', 'image_url', 'rg_store_id', 'rg_id', 'rg_size'  ); 
	$sIndexColumn = "rg_store_id"; 
	$sLimit = "LIMIT 1, 50";

	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length'])  != '-1' )
	{
		$sLimit = "LIMIT ".intval( sanitize_text_field($_REQUEST['start']) ).", ".intval( sanitize_text_field($_REQUEST['length'])  );
	}

	$sOrder = "";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value']) != '' ) {
		$str = sanitize_text_field($_REQUEST['search']['value']);

		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}


		for ( $i=0, $ien=count($request_columns); $i<$ien ; $i++ ) 
		{
			$requestColumn = sanitize_text_field($request_columns[$i]) ;

			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}


	/*	for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Individual column filtering
	if ( isset( $_REQUEST['columns'] ) ) {
		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			//$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}

	/*	for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch ) ) {
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch ) ) {
		$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !== '' ) {
		$where = 'WHERE '.$where;
	}
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."` FROM   $sTable $where $sOrder $sLimit";
	$rResult = $wpdb->get_results($sQuery, ARRAY_A);
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $wpdb->get_results($sQuery, ARRAY_N); 
	$iFilteredTotal = $rResultFilterTotal [0];
	$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM   $sTable";
	$rResultTotal = $wpdb->get_results($sQuery, ARRAY_N); 
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( $_REQUEST['draw'] ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if( $i == 0 )
			{
				if( $aRow[ $aColumns[5] ] == '' )
				{
					$uploadedbanner = $uploadurl . $aRow[ $aColumns[3] ];
					$row[] = '<div class="revglue-banner-thumb"><img class="revglue-unveil" src="'. RGMCOMP_PLUGIN_URL .'/admin/images/loading.gif" data-src="'. esc_url( $uploadedbanner ) .'"/></div>';
				} else
				{
					$row[] = '<div class="revglue-banner-thumb"><img class="revglue-unveil" src="'. RGMCOMP_PLUGIN_URL .'/admin/images/loading.gif" data-src="'. esc_url( $aRow[ $aColumns[5] ] ) .'" /></div>';
				}
			} else if( $i == 1 )
			{
				$row[] = ( $aRow[ $aColumns[0] ] == 'local' ? 'Local' : 'RevGlue Banner' );
			} else if( $i == 2 )
			{
				$row[] = $placements[$aRow[ $aColumns[1]]];
			}
			else if( $i == 3 )
			{
				$row[] = $aRow[ $aColumns[8]];
			}
			 else if( $i == 4 )
			{
				if( ! empty( $aRow[ $aColumns[4]] ) )
				{
					$url_to_show = esc_url( $aRow[ $aColumns[4]] ); 
				} else if( ! empty( $aRow[ $aColumns[6]] ) )
				{
					$sql_1 = "SELECT affiliate_network_link FROM $stores_table where rg_store_id = ".$aRow[ $aColumns[6]];
					$deep_link = $wpdb->get_results($sql_1);
					$url_to_show = ( !empty( $deep_link[0]->affiliate_network_link ) ? esc_url( $deep_link[0]->affiliate_network_link ) : 'No Link'  );
				} else
				{
					$url_to_show = 'No Link';
				}
				$row[] = '<a class="" id="'. $aRow[ $aColumns[7]] .'" title = "'. $url_to_show .'" href="'. $url_to_show .'" target="_blank"><img src="'. RGMCOMP_PLUGIN_URL .'/admin/images/linkicon.png" style="width:50px;"/><div id="imp_popup'. $aRow[ $aColumns[7]] .'" style="background: #ececec; left: 60px; margin: 5px 0; padding: 10px; position: absolute; top: 10px; display:none; border-radius: 8px; border: 1px solid #ccc">'.$url_to_show.'</div></a>';
			} else if( $i == 5 )
			{
				$row[] = $aRow[ $aColumns[2]];
			} else if( $i == 6 )
			{
				$row[] = '<a href="'. admin_url( 'admin.php?page=revglue-banners&action=edit&banner_id='.$aRow[ $aColumns[7]] ) .'">Edit</a>';
			} else if ( $aColumns[$i] != ' ' )
			{    
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	die(); 
}
add_action( 'wp_ajax_revglue_mcomp_load_banners', 'revglue_mcomp_load_banners' );
function revglue_mdeals_get_daily_deals()
{
	global $wpdb; 
	$project_table = $wpdb->prefix.'rg_projects';
	$mobile_deals_table = $wpdb->prefix.'rg_mobile_deals';
	$mobile_deals_networks = $wpdb->prefix.'rg_networks';
	$date = date("Y-m-d");
	$rg_store_id = absint( $_REQUEST['rg_store_id'] );
	$sql = "SELECT *FROM $project_table where project NOT like 'banners uk'";
	$project_detail = $wpdb->get_results($sql);
	//pre($project_detail);
	//die();
	$rows = $wpdb->num_rows;
	$sql12 = "SELECT count(*) FROM $mobile_deals_table where recommended_deal_tag ='yes' and rg_store_id!='$rg_store_id'";
	$rd = $wpdb->get_var($sql12);
	$limit1212 = 12-$rd;
	$sql4 = "SELECT * FROM $mobile_deals_networks where show_network_tag ='yes'";
	$rc = $wpdb->get_results($sql4);
	$limit4 = 4-count($rc);
	$key_c = 0;
	$key_d = 0;
	$networkss = array();
	foreach($rc as $nn){
		$networkss[] = $nn->network_title;
	}
	if( !empty ( $rows))
	{
		$i = 0;
		$currentpage = 1;
		$total_pages = 1;
		while($currentpage <= $total_pages){
			if($project_detail[0]->expiry_date=="Free" && $project_detail[0]->partner_iframe_id!="" )
			{
				$partner_broadband_stores_url ="https://www.revglue.com/partner/mobile_deals/".$currentpage."/".$rg_store_id."/".$project_detail[0]->partner_iframe_id."/json/wp";
				//die($partner_broadband_stores_url);
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $partner_broadband_stores_url, array( 'timeout' => 12000, 'sslverify'   => false ) ) ), true);
			} else {
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/mobile_deals/json/".$project_detail[0]->subcription_id."/".$rg_store_id."/".$currentpage, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true);
			}
			$currentpage++;
			$total =ceil( $resp_from_server['response']['mobile_deals_total'] / 1000 );
			$total_pages =ceil( $resp_from_server['response']['mobile_deals_total'] / 1000 );
			$result = $resp_from_server['response']['mobile_deals'];
			//pre($result);
			//die();
			if($resp_from_server['response']['success'] == true)
			{
				foreach($result as $row)
				 {
				 	 $show_network_tag	=  'no';
						// pre($key_d.' < '.$limit4 );
						if(!in_array($row['mobile_operator'], $networkss)){
							$show_network_tag	=  'no';
							if($key_d < $limit4){
								$networkss[] = $row['mobile_operator'];
						 		$key_d++;
						 		$show_network_tag	=  'yes';
							}
					 	 }
						if(in_array($row['mobile_operator'], $networkss)){
						 		$show_network_tag	=  'yes';
						}
				 	$sqlnetwork = "Select * FROM $mobile_deals_networks where network_title = '".$row['mobile_operator']."'";
				 	$network_exist = $wpdb->get_row( $sqlnetwork);
				 	// $wpdb->show_errors();
				 	if( empty( $network_exist))
				 	{
				 		$image_url = get_template_directory_uri(). '/assets/images/brandsnetworks/' .$row['mobile_operator'].'.png';
					 	$wpdb->insert(
					 	$mobile_deals_networks,
					 	array(
					 		'network_title' 	=> $row['mobile_operator'],
					 		'show_network_tag' 	=> $show_network_tag,
					 		'image_url' 	=> $image_url,
					 	)
						);
						$rg_network_id = $wpdb->insert_id;
					}
					 else
					{
						$image_url = get_template_directory_uri(). '/assets/images/brandsnetworks/' .$row['mobile_operator'].'.png';
						$rg_network_id = $network_exist->rg_network_id;
						$wpdb->update(
					 	$mobile_deals_networks,
					 	array(
					 		'network_title' 	=> $row['mobile_operator'],
					 		'show_network_tag' 	=> $show_network_tag,
					 		 'image_url' 	=> $image_url,
							),array('rg_network_id'=>$rg_network_id)
						);
					}
				 	// pre($row);
				 	// die();
							$recommended_deal_tag	=  'no';
				 		if($key_c < $limit1212 ){
				 			if($row['initial_cost'] > 0 && $row['minutes'] > 0 && $row['mobile_id'] != '0'  && $row['contract_term_months'] > 0 ){
				 				$key_c++;
								$recommended_deal_tag	=  'yes';
				 			}
				 			else{
				 				$recommended_deal_tag	=  'no';
				 			}
						}
					 $sqlinstore = "Select rg_mobile_deal_id FROM $mobile_deals_table Where rg_mobile_deal_id = '".$row['mobile_deal_id']."'";
					$rg_deal_exists = $wpdb->get_var( $sqlinstore);
					$wpdb->show_errors();
					if( empty( $rg_deal_exists))
					{
					 	$wpdb->insert(
							$mobile_deals_table,
							array(
								'rg_mobile_deal_id'	=> $row['mobile_deal_id'],
								'rg_mobile_id' 		=> $row['mobile_id'],
								'rg_store_id' 		=> $row['rg_store_id'],
								'network' 			=> $rg_network_id,
								'contract_type' 	=> $row['contact_type'],
								'contract_term' 	=> $row['contract_term_months'],
								'title' 			=> $row['mobile_deal_title'],
								'deeplink'			=>  $row['mobile_deal_link'],
								'initial_cost' 		=> $row['initial_cost'],
								'month_cost' 		=> $row['monthly_cost'],
								'minutes' 			=> $row['minutes'],
								'sms' 				=> $row['sms'],
								'mbs' 				=> $row['data_mb'],
								'connectivity' 		=> $row['connectivity'],
								'gift' 				=> $row['gift'],
								'special_offer' 	=> $row['special_offer'],
								'description' 		=> $row['mobile_deal_descriptin'],
								'recommended_deal_tag' 		=> $recommended_deal_tag,
								'date' 				=> $date
							)
						);
					} else
					{
						$wpdb->update(
							$mobile_deals_table,
							array(
								   'rg_mobile_id' 		=> $row['mobile_id'],
									'rg_store_id' 		=> $row['rg_store_id'],
									'network' 			=> $row['rg_network_id'],
									'contract_type' 	=> $row['contact_type'],
									'contract_term' 	=> $row['contract_term_months'],
									'title' 			=> $row['mobile_deal_title'],
									'deeplink'			=> $row['mobile_deal_link'],
									'initial_cost' 		=> $row['initial_cost'],
									'month_cost' 		=> $row['monthly_cost'],
									'minutes' 			=> $row['minutes'],
									'sms' 				=> $row['sms'],
									'mbs' 				=> $row['data_mb'],
									'connectivity' 		=> $row['connectivity'],
									'gift' 				=> $row['gift'],
									'special_offer' 	=> $row['special_offer'],
									'description' 		=> $row['mobile_deal_descriptin'],
									'recommended_deal_tag' 		=> $recommended_deal_tag,
									'date' 				=> $date
							),
							array( 'rg_mobile_deal_id' => $rg_deal_exists )
						);
					}
				}
				// echo $wpdb->last_query;
				// die();
			}
			$i++;
			$page++;
		}
	}
	// echo "$i $total";
	$wpdb->query( "DELETE FROM $mobile_deals_table WHERE `date` != '$date' and rg_store_id='$rg_store_id'");
	$response_array = array();
	$response_array['rg_store_id'] = $rg_store_id;
	$sql1  = "SELECT COUNT( rg_store_id ) as total_deals, MAX( date ) as last_updated
	 FROM $mobile_deals_table WHERE rg_store_id = $rg_store_id";
	$project_detail = $wpdb->get_results($sql1);
	$sqld = "SELECT MAX(date) FROM $mobile_deals_table WHERE rg_store_id = $rg_store_id";
	$last_updated_deal = $wpdb->get_var($sqld);
	$response_array['last_updated_deal'] = ( $last_updated_deal ? date('d-M-Y', strtotime($last_updated_deal)) : '-' );
	$sqlc = "SELECT COUNT(*) FROM $mobile_deals_table WHERE rg_store_id = $rg_store_id";
	$count_deal = $wpdb->get_var($sqlc);
	 $count_deal = $count_deal==0 ?"no deals":$count_deal;
	$response_array['count_deal'] = $count_deal;
	echo json_encode($response_array);
	wp_die();
}
add_action( 'wp_ajax_revglue_mdeals_get_daily_deals', 'revglue_mdeals_get_daily_deals' );
function revglue_comp_stores_admin_screen_listing_query()
{
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$aColumns = array( 'rg_store_id', 'affiliate_network', 'mid', 'image_url', 'title', 
		'store_base_country', 'affiliate_network_link');
	$sIndexColumn = "rg_store_id";
	$sLimit = "LIMIT 1, 50";

	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length'])  != '-1' )
	{
		$sLimit = "LIMIT ".intval( sanitize_text_field($_REQUEST['start']) ).", ".intval( sanitize_text_field($_REQUEST['length'])  );
	}

	$sOrder = "";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value']) != ''){
		$str = sanitize_text_field($_REQUEST['search']['value']);
		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}
			for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;

			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}

		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++){
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data']];
			if ( $requestColumn['searchable'] == 'true'){
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Individual column filtering
	if ( isset( $_REQUEST['columns'])){

			$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

			for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			//$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column =  sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}

		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++){
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data']];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != ''){
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch )){
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch )){
		$where = $where === ''?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !== ''){
		$where = 'WHERE '.$where;
	}
	$sQuery = " SELECT * FROM $stores_table $where";
	//die($sQuery);
	$rResult = $wpdb->get_results($sQuery, ARRAY_A);
	$sQuery1 = " SELECT count(*) FROM $stores_table $where";
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $wpdb->get_results($sQuery, ARRAY_N);
	$iFilteredTotal = $rResultFilterTotal [0];
	$rResultTotal = $wpdb->get_results($sQuery1, ARRAY_N);
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( sanitize_text_field($_REQUEST['draw'])  ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if( $i == 0 )
			{
				$row[] = $aRow[ $aColumns[0] ];
			} else if( $i == 1 )
			{
				$row[] = $aRow[ $aColumns[1] ];
			} else if( $i == 2 )
			{
				$row[] = $aRow[ $aColumns[2] ];
			} else if( $i == 3 )
			{
				$row[]= '<div class="revglue-banner-thumb"><img class="revglue-unveil" src="'. RGMCOMP_PLUGIN_URL .'/admin/images/loading.gif" data-src="'. $aRow[ $aColumns[3] ] .'" /> </div>';
			//$row[] = $aRow[ $aColumns[3] ];
			} else if( $i == 4 )
			{
				$row[] = $aRow[ $aColumns[4] ];
			} else if( $i == 5 )
			{
				$row[] = $aRow[ $aColumns[5] ];
			} else if( $i == 6 )
			{
				$row[]='<a class="rg_store_link_pop_up" id="'. $aRow[ $aColumns[0] ] .'" title="'.$aRow[ $aColumns[6] ].'" href="'. $aRow[ $aColumns[6] ] .'" target="_blank">
								<img src="'. RGMCOMP_PLUGIN_URL .'/admin/images/linkicon.png" style="width:50px;"></a>';
			} 
		}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	die();
}
add_action( 'wp_ajax_revglue_comp_stores_admin_screen_listing_query', 'revglue_comp_stores_admin_screen_listing_query' );
function revglue_comp_mobiles_admin_screen_listing_query()
{
	global $wpdb;
	$brands_table = $wpdb->prefix.'rg_brands';
	$mobiles_table = $wpdb->prefix.'rg_mobiles';
	$aColumns = array( 'image_url', 'brand_title', 'model', 'color', 'internal_memory', 
		'camera', 'best_mobile_tag', 'rg_mobile_id');
	$sIndexColumn = "rg_store_id";
	$sLimit = "LIMIT 1, 50";
	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length']) != '-1' )
	
	{
		$sLimit = "LIMIT ".intval(sanitize_text_field($_REQUEST['start'])).", ".intval(sanitize_text_field($_REQUEST['length']));
	}

	
	$sOrder = "";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value']) != ''){
		$str = sanitize_text_field($_REQUEST['search']['value']);

		 	$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field( $request_columns[$i]);
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			if ( $requestColumn['searchable'] == 'true' ) {
				$table123 = '';
				if($column == 'image_url'){
					$table123 = "$mobiles_table.";
					$globalSearch[] = "$table123`title` LIKE '%".$str."%'";
				}
				$globalSearch[] = "$table123`".$column."` LIKE '%".$str."%'";
			}
		}

/*
		for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++){
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data']];
			if ( $requestColumn['searchable'] == 'true'){
				$table123 = '';
				if($column == 'image_url'){
					$table123 = "$mobiles_table.";
					$globalSearch[] = "$table123`title` LIKE '%".$str."%'";
				}
				$globalSearch[] = "$table123`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// pre($globalSearch);
	// die;
	// Individual column filtering
	if ( isset( $_REQUEST['columns'])){
		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}



/*
		for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++){
			$requestColumn = $_REQUEST['columns'][$i];
			//$columnIdx = array_search( $requestColumn['data'], $dtColumns);
			$column = $dtColumns[ $requestColumn['data']];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str !=''){
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch )){
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch )){
		$where = $where === ''?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !==''){
		$where = 'WHERE '.$where;
	}
	$sQuery = " SELECT $mobiles_table.*, $brands_table.brand_title as brand_title  FROM $mobiles_table left join $brands_table on $brands_table.rg_brand_id= $mobiles_table.brand $where $sOrder $sLimit ";
	//die($sQuery);
	$rResult = $wpdb->get_results($sQuery, ARRAY_A);
	// print_r($rResult);
	$sQuery1 = " SELECT count(*) FROM $mobiles_table $where";
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $wpdb->get_results($sQuery1, ARRAY_N);
	$iFilteredTotal = $rResultFilterTotal [0];
	$rResultTotal = $wpdb->get_results($sQuery1, ARRAY_N);
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( sanitize_text_field($_REQUEST['draw'])  ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++)
		{
			if( $i == 0 )
			{
				$row[]='<div class="revglue-banner-thumb"><img class="revglue-unveil mb" src="'. RGMCOMP_PLUGIN_URL .'/admin/images/loading.gif" data-src="'. $aRow[ $aColumns[0] ] .'" /></div>';
			} else if( $i == 1 )
			{
				$row[] = $aRow[ $aColumns[1] ];
			} else if( $i == 2 )
			{
				$row[] = $aRow[ $aColumns[2] ];
			} else if( $i == 3 )
			{
				$row[] = $aRow[ $aColumns[3] ];
			} else if( $i == 4 )
			{
				$row[] = $aRow[ $aColumns[4] ];
			}  else if( $i == 5 )
			{
				$row[] = $aRow[ $aColumns[5] ];
			}  else if( $i == 6 )
			{
			if( $aRow[ $aColumns[6] ] == 'yes' )
			{
				$checked = 'checked="checked"';
			} else
			{
				$checked = '';
			}
				$row[] ='<input '. $checked.'  type="checkbox" onchange="changeCheckbocValue(this,'.$aRow[ $aColumns[7] ].')" id="'. $aRow[ $aColumns[7] ].'" class="rg_best_mobile_tag" />';
			}
		}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	die(); 
}
add_action( 'wp_ajax_revglue_comp_mobiles_admin_screen_listing_query', 'revglue_comp_mobiles_admin_screen_listing_query' );
function revglue_comp_deal_admin_screen_import_query()
{
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$mobile_deals_table = $wpdb->prefix.'rg_mobile_deals';
	$aColumns = array( 'rg_store_id', 'image_url', 'title');
	$sLimit = "LIMIT 0, 50";

	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length']) != '-1' )
	
	{
		$sLimit = "LIMIT ".intval(sanitize_text_field($_REQUEST['start'])).", ".intval(sanitize_text_field($_REQUEST['length']));
	}


	$sOrder = "order by rg_store_id asc";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value']) != '' ) {
		$str = sanitize_text_field($_REQUEST['search']['value']);


		 	$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}

		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			if ( $requestColumn['searchable'] == 'true' ){
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Individual column filtering
	if ( isset( $_REQUEST['columns'])){




		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}
		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}
	/*	
		for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++){
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data']];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str !=''){
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch)){
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch )){
		$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !==''){
		$where = 'WHERE '.$where;
	}
	$sQuery = " SELECT * FROM $stores_table $where $sOrder $sLimit";
	//echo $sQuery ;
	$rResult = $wpdb->get_results($sQuery, ARRAY_A);
	$sQuery1 = " SELECT count(*) FROM $stores_table $where";
	 //echo $sQuery1;
	$sqld = "SELECT rg_store_id, MAX( `date` ) as last_updated, count(rg_store_id) as total 
		FROM $mobile_deals_table group by rg_store_id";
		$mdData = $wpdb->get_results($sqld);
		$mdarray = array();
		foreach($mdData as $row){
				$mdarray[$row->rg_store_id]['last_updated'] = $row->last_updated;
				$mdarray[$row->rg_store_id]['total'] = $row->total;
		}
		//print_r($mdarray);
		//die();
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $wpdb->get_results($sQuery1, ARRAY_N);
	$iFilteredTotal = $rResultFilterTotal [0];
	$rResultTotal = $wpdb->get_results($sQuery1, ARRAY_N);
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( sanitize_text_field($_REQUEST['draw']) ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		$row = array();
		for ( $i=0 ; $i<6 ; $i++)
		{
			if( $i == 0 )
			{
				$row[] = $aRow[ $aColumns[0]];
			} else if( $i == 1 )
			{
				 $row[]='<div class="revglue-banner-thumb"><img class="revglue-unveil" src="'. RGMCOMP_PLUGIN_URL .'/admin/images/loading.gif" data-src="'. $aRow[ $aColumns[1] ] .'" /></div>';
			} else if( $i == 2 )
			{
				$row[] = $aRow[ $aColumns[2]];
			} else if( $i == 3 )
			{
				 $uDAte =   @date("d-M-Y", strtotime($mdarray[$aRow[ $aColumns[0] ]]['last_updated'] ));
				 	$uDAte = ($uDAte != "01-Jan-1970") ? $uDAte  : "-"	 .'</div>';
				  $row[]='<div id="mdeal_updated_'.$aRow[ $aColumns[0] ].'"> '.$uDAte; 
			} else if( $i == 4 )
			{
				//$row[] = $mdarray[$aRow[ $aColumns[4] ]]['total'] ? $mdarray[$aRow[ $aColumns[4] ]]['total']  : 0 ; 
				$countofProducts = $mdarray[$aRow[ $aColumns[0] ]]['total'] ? $mdarray[$aRow[ $aColumns[0] ]]['total']  : 0 ;  
					$countofProducts = ($countofProducts != 0) ? $countofProducts  : "no deals"	 .'</div>';  
					$row[]='<div id="mdeal_fcount_'.$aRow[ $aColumns[0] ].'"> '.$countofProducts; 
			}  else if( $i == 5 )
			{
				$row[]='<div id="mdeal_antiloader_'.$aRow[ $aColumns[0] ].'" class="mdeal_antiloader">
								<a href="javascript:" class="rg_import_mdeal btn txtwhite btn-primary" data-rg_store_id="'.$aRow[ $aColumns[0] ].'" >Import</a>
							</div>
							<div id="mdeal_loader_'.$aRow[ $aColumns[0] ].'" style="display:none"><img src="'. RGMCOMP_PLUGIN_URL.'/admin/images/loading.gif" /></div>';
			}  
		}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	die(); 
}
add_action( 'wp_ajax_revglue_comp_deal_admin_screen_import_query', 'revglue_comp_deal_admin_screen_import_query' );
function deals_admin_screen_listing_query()
{
	global $wpdb;
	$network_table = $wpdb->prefix.'rg_networks';
	$stores_table = $wpdb->prefix.'rg_stores';
	$mobiles_table = $wpdb->prefix.'rg_mobiles';
	$mobile_deals_table	= $wpdb->prefix.'rg_mobile_deals'; 
	$aColumns = array( 'rg_mobile_id', 'image_url', 'rg_store_id','contract_type','network_title','minutes','sms','initial_cost','recommended_deal_tag','deeplink', 'rg_mobile_deal_id');
	$sLimit = "LIMIT 1, 50";

	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length']) != '-1' )
	
	{
		$sLimit = "LIMIT ".intval(sanitize_text_field($_REQUEST['start'])).", ".intval(sanitize_text_field($_REQUEST['length']));
	}
	$sOrder = "";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value']) != '' ) {
		$str = sanitize_text_field($_REQUEST['search']['value']);

		 	$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]);
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}

		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Individual column filtering
	if ( isset( $_REQUEST['columns'] ) ) {

			$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}
		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}


		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i]; 
			$column = $dtColumns[ $requestColumn['data'] ];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch ) ) {
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch ) ) {
		$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !== '' ) {
		$where = 'WHERE '.$where;
	}
	$sQuery = " SELECT $mobile_deals_table.*, $network_table.network_title as network_title  FROM $mobile_deals_table left join $network_table on $network_table.rg_network_id= $mobile_deals_table.network $where $sOrder $sLimit ";
	$rResult = $wpdb->get_results($sQuery, ARRAY_A); 
	$sQuery1 = " SELECT count(*) FROM $mobile_deals_table $where"; 
	$rResultFilterTotal = $wpdb->get_results($sQuery1, ARRAY_N); 
	$iFilteredTotal = $rResultFilterTotal [0];
	$rResultTotal = $wpdb->get_results($sQuery1, ARRAY_N); 
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( sanitize_text_field($_REQUEST['draw']) ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		$row = array();
		for ( $i=0 ; $i<10; $i++ )
		{
			if( $i == 0 )
			{
				 $row[] = $aRow[ $aColumns[0] ]; 
			} else if( $i == 1 )
			{
				$mobile_deal_id =$aRow[$aColumns[0]];
				 $sql =" SELECT image_url FROM $mobiles_table WHERE rg_mobile_id = $mobile_deal_id ";
				 $image_url = $wpdb->get_var($sql);
				$row[]='<div class="revglue-banner-thumb"><img class="revglue-unveil mb" src="'. RGMCOMP_PLUGIN_URL .'/admin/images/loading.gif" data-src="'.$image_url .'" /></div>';
			} else if( $i == 2 )
			{
				$row[] = $aRow[ $aColumns[2] ];
			} else if( $i == 3 )
			{
				$row[] = $aRow[ $aColumns[3] ];
			} else if( $i == 4 )
			{
				$row[] = $aRow[ $aColumns[4] ];
			}  else if( $i == 5 )
			{
				$row[] = $aRow[ $aColumns[5] ];
			}  else if( $i == 6 )
			{
				$row[] = $aRow[ $aColumns[6] ];
			} else if( $i == 7 )
			{
				$row[] = $aRow[ $aColumns[7] ];
			}  else if( $i == 8 )
			{
				$row[] = "<a class='rg_store_link_pop_up' id='2141' title='".$aRow[ $aColumns[9] ]."' href='".$aRow[ $aColumns[9] ]."' target='_blank'>
				<img src='". RGMCOMP_PLUGIN_URL ."/admin/images/linkicon.png' style='width:50px;'></a>";
			} else if( $i == 9 )
			{
				if($aRow[ $aColumns[9] ]== 'yes' )
							{
								$checked = 'checked="checked"';
							} else
							{
								$checked = '';
							}
				$row[] ='<input  '. $checked .' type="checkbox" id="'. $aRow[ $aColumns[10] ] .'" class="rg_recommended_deal_tag" />';
			} 
		}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	wp_die();
}
add_action( 'wp_ajax_deals_admin_screen_listing_query', 'deals_admin_screen_listing_query' );



function rg_update_subscription_expiry_date($purchasekey, $userpassword, $useremail, $projectid){
 global $wpdb;
 $projects_table = $wpdb->prefix.'rg_projects';
 $apiurl = RGMCOMP_API_URL."api/validate_subscription_key/$useremail/$userpassword/$purchasekey";
 $resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $apiurl , array( 'timeout' => 120, 'sslverify' => false ))), true);
 $expiry_date = $resp_from_server['response']['result']['expiry_date'];
 if ( empty($projectid)){
  $sql ="UPDATE $projects_table SET `expiry_date` = '$expiry_date' WHERE `subcription_id` ='$purchasekey'";
  $wpdb->query($sql);
 } 
}