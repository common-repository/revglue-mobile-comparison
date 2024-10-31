<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_mcomp_mobile_listing_page()
{
	?><div class="rg-admin-container">
		<h1 class="rg-admin-heading ">Mobiles with Specifications</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="text-right">you can search by brand, model, color , internal memory, camera MPx</div>
		<table id="mobiles_admin_screen_listing" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Mobile Image</th>
					<th>Brand</th>
					<th>Model</th>
					<th>Color</th>
					<th>Internal Memory</th>
					<th>Camera (MPx)</th>
					<th>Best Selling</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Mobile Image</th>
					<th>Brand</th>
					<th>Model</th>
					<th>Color</th>
					<th>Internal Memory</th>
					<th>Camera (MPx)</th>
					<th>Best Selling</th>
				</tr>
			</tfoot>
		</table>
	</div><?php
}
?>