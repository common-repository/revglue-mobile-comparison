<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;



function rg_mcomp_mobile_import_page()

{

	global $wpdb;

	$project_table = $wpdb->prefix.'rg_projects';

	$stores_table = $wpdb->prefix.'rg_stores';

	$mobiles_table = $wpdb->prefix.'rg_mobiles';

	$mobile_deals_table	= $wpdb->prefix.'rg_mobile_deals'; 

	

	$sql = "SELECT MAX(date) FROM $stores_table";

	$last_updated_store = $wpdb->get_var($sql);

	$last_updated_store = ( $last_updated_store ? date( 'l , d-M-Y , h:i A', strtotime( $last_updated_store ) ) : '-' );

	

	$sql_1 = "SELECT count(*) as stores FROM $stores_table";

	$count_store = $wpdb->get_results($sql_1);

	

	$sql_2 = "SELECT MAX(date) FROM $mobiles_table";

	$last_updated_mobile = $wpdb->get_var($sql_2);

	$last_updated_mobile = ( $last_updated_mobile ? date( 'l , d-M-Y , h:i A', strtotime( $last_updated_mobile ) ) : '-' );

	

	$sql_3 = "SELECT count(*) as mobiles FROM $mobiles_table";

	$count_mobile = $wpdb->get_results($sql_3);

	

	$sql_4 = "SELECT MAX(date) FROM $mobile_deals_table";

	$last_updated_deal = $wpdb->get_var($sql_4);

	$last_updated_deal = ( $last_updated_deal ? date( 'l ,  d-M-Y , h:i A', strtotime( $last_updated_deal ) ) : '-' );

	

	$sql_5 = "SELECT count(*) as deals FROM $mobile_deals_table";

	$count_deal = $wpdb->get_results($sql_5);

	

	$sql_6 = "SELECT *FROM $project_table where project NOT like 'Banners UK'";

	$project_detail = $wpdb->get_results($sql_6);
	//pre($project_detail);
	//die;

	$rows = $wpdb->num_rows;

	

	$qry_response = '';

	

	if( !empty ( $rows ) )

	{
		if($project_detail[0]->expiry_date=="Free" && $project_detail[0]->partner_iframe_id!="" )
		{
			$qry_response = "<div class='panel-white mgBot'>";

		$qry_response .= "<p><b>Your RevEmbed Mobile Comparison Subscription is ". $project_detail[0]->status.". &nbsp;</b><img  class='tick-icon' src=".RGMCOMP_PLUGIN_URL. 'admin/images/ticks_icon.png'." />  </p>";

		$qry_response .= "<p><b>Name = </b> RevEmbed Data </p>";

		$qry_response .= "<p><b>Project = </b>".$project_detail[0]->project." UK </p>";

		$qry_response .= "<p><b>Email = </b>".$project_detail[0]->email."</p>";

		$qry_response .= "</div>";

		} else {

			$sub_id = $project_detail[0]->subcription_id;

		$qry_response = "<div class='panel-white mgBot'>";

		$qry_response .= "<p><b>Your Mobile Comparison subscription is ". $project_detail[0]->status.". &nbsp;</b><img  class='tick-icon' src=".RGMCOMP_PLUGIN_URL. 'admin/images/ticks_icon.png'." />  </p>";

		$qry_response .= "<p><b>Name = </b>".$project_detail[0]->user_name."</p>";

		$qry_response .= "<p><b>Project = </b>".$project_detail[0]->project."</p>";

		$qry_response .= "<p><b>Email = </b>".$project_detail[0]->email."</p>";

		$qry_response .= "<p><b>Expiry Date =</b> ".date("d-M-Y", strtotime( $project_detail[0]->expiry_date ))."</p>";

		$qry_response .= "</div>";

		}

		

	}

	

	?><div class="rg-admin-container">

		<h1 class="rg-admin-heading ">Import RevGlue Mobiles</h1>

		<div style="clear:both;"></div>

		<hr/>

		<form id="subscription_form" method="post">

			<table class="inline-table">

				<tr>

					<td style="text-align:right;padding-right: 10px;">

						<label>Subscription ID:</label>

					</td>

					<td>

						<input id="rg_mcomp_sub_id" type="text" name="rg_mcomp_sub_id" class="regular-text revglue-input lg-input">

					</td>

					<td style="text-align:right;padding-right: 10px;">

						<label >RevGlue Email:</label>

					</td>

					<td>

						<input id="rg_mcomp_sub_email" type="text" name="rg_mcomp_sub_email" class="regular-text revglue-input lg-input">

					</td>

					<td style="text-align:right;padding-right: 10px;">

						<label >RevGlue Password:</label>

					</td>

					<td>

						<input id="rg_mcomp_sub_password" type="password" name="rg_mcomp_sub_password" class="regular-text revglue-input lg-input">

					</td>

					<td style="text-align:right;padding-right: 10px;">

						<button id="rg_mcomp_sub_activate" class="button-primary float-left" style="margin-right:5px;">Validate Account</button>

					</td>	

				</tr>

				<tr>

					<td colspan="7">

						<span id="subscription_error"></span>

					</td>

				</tr>

			</table>

		</form>

		<div id="sub_loader" align="center" style="display:none"><img src="<?php echo RGMCOMP_PLUGIN_URL; ?>/admin/images/loading.gif" /></div>

		<hr>

		<div id="subscription_response"><?php echo $qry_response; ?></div>

		<h3>RevGlue Mobiles Data Set</h3>

		<div class="sub_page_table">

			<table class="widefat revglue-admin-table">

				<thead>

					<tr>

						<th style="width:15%;">Data Type</th>

						<th style="width:25%;">No. of Entries</th>

						<th style="width:40%;">Last Updated</th>

						<th style="width:20%;">Action</th>

					</tr>	

				</thead>

					<tr>

						<td>Stores</td>

						<td><span id="rg_store_count"><?php esc_html_e( $count_store[0]->stores ); ?></span></td>

						<td><span id="rg_store_date"><?php esc_html_e( $last_updated_store ); ?></span></td>

						

						<td class="store-table">

							<a href='rg_stores_import' class="rg_mcomp_open_import_popup">Import</a> | <a href='rg_stores_delete' class="rg_mcomp_open_delete_popup">Delete</a>

							

							<div id="rg_mcomp_import_popup" style="background: #ececec; min-width:350px; right: 5%; margin: 5px 0; padding: 10px; position: absolute; bottom:20px; display:none; border-radius: 8px; border: 1px solid #ccc">This request will validate your API key and update current data. 

							Your current data will be removed and updated with latest data set.

							Please click on confirm if you wish to run the process.<br/>

							<a href="" id="rg_mcomp_import" class="rg_mcomp_start_import">Import</a> | <a href="javascript:{}" onClick="jQuery('#rg_mcomp_import_popup').hide()">Cancel</a>

							</div>



							<div id="rg_mcomp_delete_popup" style="background: #ececec; right: 5%; margin: 5px 0; padding: 10px; position: absolute; bottom:20px; display:none; border-radius: 8px; min-width:350px; border: 1px solid #ccc">This request will delete all your current data. Please confirm if you wish to run the process. You will have to import again.<br/>

							<a href="" id="rg_mcomp_delete"  class="rg_mcomp_start_delete">Delete</a> | <a href="javascript:{}" onClick="jQuery('#rg_mcomp_delete_popup').hide()">Cancel</a>

							</div>

						</td>

					</tr>

					<tr>

						<td>Mobiles with Specifications</td>

						<td><span id="rg_mobile_count"><?php esc_html_e( $count_mobile[0]->mobiles ); ?></span></td>

						<td><span id="rg_mobile_date"><?php esc_html_e( $last_updated_mobile ); ?></span></td>

						<td class="mobile-table">

							<a href='rg_mobiles_import' class="rg_mcomp_open_import_popup">Import</a> | <a href='rg_mobiles_delete' class="rg_mcomp_open_delete_popup">Delete</a>

						</td>

					</tr>

					<!--<tr>

						<td>Mobile Deals</td>

						<td><span id="rg_deal_count"><?php //esc_html_e( $count_deal[0]->deals ); ?></span></td>

						<td><span id="rg_deal_date"><?php //esc_html_e( $last_updated_deal ); ?></span></td>

						<td class="deal-table">

							<a href='rg_deals_import' class="rg_mcomp_open_import_popup">Import</a> | <a href='rg_deals_delete' class="rg_mcomp_open_delete_popup">Delete</a>

						</td>

					</tr>-->

			</table>

		</div>

		<div id="store_loader" align="center" style="display:none"><img src="<?php echo RGMCOMP_PLUGIN_URL; ?>/admin/images/loading.gif" /></div>

		<div class="panel-white">

			<h4>Setup Auto Import</h4>

			<p>If you wish to setup auto import of RevGlue Mobiles Data then go to your server panel and setup CRON JOB. Your server may ask you path for the file to setup. The file path for auto data update is provided below. You can also setup daily times for the auto import.</p> 

		</div>





		<table class="form-table">

			<tr>

				<th><label title="File Path">File Path:</label></th>

				<td><input type="text" class="regular-text revglue-input lg-input" value="<?php echo site_url() . '/revglue-mcomp/auto_import_data'; ?>">

				  </td>

			  </tr>

		</table>

	</div>

	<?php

}

?>