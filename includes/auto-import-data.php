<?php 

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;



global $wpdb;

$stores_table = $wpdb->prefix.'rg_stores';

$project_table = $wpdb->prefix.'rg_projects';

$mobiles_table = $wpdb->prefix.'rg_mobiles';

$mobile_deals_table	= $wpdb->prefix.'rg_mobile_deals'; 

	

$sql = "SELECT *FROM $project_table where project like 'Mobile Comparison UK'";

$project_detail = $wpdb->get_results($sql);

$rows = $wpdb->num_rows;



$qry_response = '';



if( !empty ( $rows ) )

{

	$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/mobiles/json/".$project_detail[0]->subcription_id ) ), true); 

	$result = $resp_from_server['response']['mobiles'];



	if($resp_from_server['response']['success'] == 1 )

	{

		foreach($result as $row)

		{	

			$sqlinstore = "Select rg_mobile_id FROM $mobiles_table Where rg_mobile_id = '".$row['mobile_id']."'";

			$rg_mobile_exists = $wpdb->get_var( $sqlinstore );

			if( empty( $rg_mobile_exists ) )

			{

				$wpdb->insert( 

					$mobiles_table, 

					array( 

						'rg_mobile_id' 		=> $row['mobile_id'],

						'title' 			=> $row['mobile_title'],

						'brand' 			=> $row['brand'],

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

						'date' 				=> date('Y-m-d H:i:s')

					) 

				);

			}

		}

		echo 'You successfully imported the mobiles<br>';

	} else 

	{

		$qry_response .= '<p style="color:red">'.$resp_from_server['response']['message'].'</p>';

	}

	

	$i = 0;

	$page = 1;

	do {

		$store_sql = "SELECT rg_store_id FROM $stores_table";

		$fetch_store_id = get_var($store_sql);

		$store_id = $fetch_store_id;

		$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/mobile_deals/json/".$project_detail[0]->subcription_id."/".$store_id."/".$page, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
		


		$total = ceil( $resp_from_server['response']['mobile_deals_total'] / 1000 ) ;

		$result = $resp_from_server['response']['mobile_deals'];

		if($resp_from_server['response']['success'] == 1 )

		{

			foreach($result as $row)


			{

				$sqlinstore = "Select rg_mobile_deal_id FROM $mobile_deals_table Where rg_mobile_deal_id = '".$row['mobile_deal_id']."'";

				$rg_deal_exists = $wpdb->get_var( $sqlinstore );

				if( empty( $rg_deal_exists ) )

				{

					$wpdb->insert( 

						$mobile_deals_table, 

						array( 

							'rg_mobile_deal_id'	=> $row['mobile_deal_id'],

							'rg_mobile_id' 		=> $row['mobile_id'],

							'rg_store_id' 		=> $row['rg_store_id'],

							'network' 			=> $row['mobile_operator'],

							'contract_type' 	=> $row['contact_type'],

							'contract_term' 	=> $row['contract_term_months'],

							'title' 			=> $row['mobile_deal_title'],

							'deeplink'			=> $row['mobile deal_link'],

							'initial_cost' 		=> $row['initial_cost'],

							'month_cost' 		=> $row['monthly_cost'],

							'minutes' 			=> $row['minutes'],

							'sms' 				=> $row['sms'],

							'mbs' 				=> $row['data_mb'],

							'connectivity' 		=> $row['connectivity'],

							'gift' 				=> $row['gift'],

							'special_offer' 	=> $row['special_offer'],

							'image_url' 		=> $row['image'],

							'description' 		=> $row['mobile_deal_descriptin'],

							'date' 				=> date('Y-m-d H:i:s')

						) 

					);

				}					

			}

			echo 'You successfully imported the deals<br>';

		} else 

		{

			$qry_response .= '<div class="alert alert-danger" role="alert">'.$resp_from_server['response']['message'].'</div>';

		}

		$i++;

		$page++;

	} while ( $i < $total );

	

	$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/mobile_stores/json/".$project_detail[0]->subcription_id ) ), true); 

	$result = $resp_from_server['response']['stores'];

	

	if($resp_from_server['response']['success'] == 1 )

	{

		foreach($result as $row)

		{

			$sqlinstore = "Select rg_store_id FROM $stores_table Where rg_store_id = '".$row['rg_store_id']."'";

			$rg_store_exists = $wpdb->get_var( $sqlinstore );

			if( empty( $rg_store_exists ) )

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

						'category_ids' 				=> $row['category_ids'], 

						'date' 						=> date('Y-m-d H:i:s')

					) 

				);

			}					

		}

		echo 'You successfully imported the stores<br>';

	} else 

	{

		$qry_response .= '<p style="color:red">'.$resp_from_server['response']['message'].'</p>';

	}

}



$sql = "SELECT *FROM $project_table where project like 'Banners UK'";

$project_detail = $wpdb->get_results($sql);

$rows = $wpdb->num_rows;

    

if( !empty ( $rows ) )

{

	$banner_table = $wpdb->prefix.'rg_banner';

	$jsonDataBanners = json_decode( wp_remote_retrieve_body( wp_remote_get( RGMCOMP_API_URL . "api/banners/json/".$project_detail[0]->subcription_id ) ), true); 

	$result = $jsonDataBanners['response']['banners'];

	

	if($jsonDataBanners['response']['success'] == 'true' )

	{

		foreach($result as $row)

		{

			$sqlinstore = "Select rg_store_banner_id FROM $banner_table Where rg_store_banner_id = '".$row['store_banner_id']."' AND `banner_type` = 'imported'";

			$rg_banner_exists = $wpdb->get_var( $sqlinstore );

			if( empty( $rg_banner_exists ) )

			{

				$wpdb->insert( 

					$banner_table, 

					array( 

						'rg_store_banner_id' 	=> $row['store_banner_id'], 

						'rg_store_id' 			=> $row['rg_store_id'], 

						'title' 				=> $row['title'], 

						'image_url' 			=> $row['image_url'], 

						'placement' 			=> 'unassigned', 

						'banner_type' 			=> 'imported'

					) 

				);

			}					

		}

		echo 'You successfully imported the banners<br>';

	} else 

	{

		$qry_response .= '<p style="color:red">'.$jsonDataBanners['response']['message'].'</p>';

	}

	echo $qry_response;

}			

exit();

?>