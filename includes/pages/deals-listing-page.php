<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_mcomp_deal_listing_page()
{
	?><div class="rg-admin-container"> 
		<h1 class="rg-admin-heading ">Mobile Deals</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="text-right">You can filter the result by Mobile Id, Store Id, Contract Type, Network, Number of Minutes, Number of Sms, price.</div>
		 <table id="deals_admin_screen_listing" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Mobile Id</th> 
						<th>Mobile Image</th>
						<th>Store ID</th>
						<th>Contract Type</th>
						<th>Network</th>
						<th>Minutes</th>
						<th>Sms</th>
						<th>Price</th>
						<th>Deeplink</th>
						<th>Recommended Deal</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Mobile Id</th> 
						<th>Mobile Image</th>
						<th>Store ID</th>
						<th>Contract Type</th>
						<th>Network</th>
						<th>Minutes</th>
						<th>Sms</th>
						<th>Price</th>
						<th>Deeplink</th>
						<th>Recommended Deal</th>
					</tr>
				</tfoot> 
			</table>
	</div><?php
}
?>