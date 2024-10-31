<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_mcomp_store_listing_page()
{
	?><div class="rg-admin-container">
		<h1 class="rg-admin-heading ">Stores</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="text-right pull-right">you can search by rg id, network, mid, name, country</div>
		<table id="stores_admin_screen_listing" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>RG ID</th>
					<th>Network</th>
					<th>MID</th>
					<th>Logo</th>
					<th>Name</th>
					<th>Country</th>
					<th>Affiliate network link</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>RG ID</th>
					<th>Network</th>
					<th>MID</th>
					<th>Logo</th>
					<th>Name</th>
					<th>Country</th>
					<th>Affiliate network link</th>
				</tr>
			</tfoot>
		</table>
	</div><?php
}