<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_mcomp_mobile_deal_import_page()
{
	?><div class="rg-admin-container"> 
		<h1 class="rg-admin-heading ">Import Mobile Deals</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="text-right">you can search by RG ID, Store Name.</div>
		<table id="mcompdeal_admin_screen_import" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>RG ID</th>
					<th>Store Logo</th>
					<th>Store Name</th>
					<th>Last Imported</th>
					<th>Count</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>RG ID</th>
					<th>Store Logo</th>
					<th>Store Name</th>
					<th>Last Imported</th>
					<th>Count</th>
					<th>Actions</th>
				</tr>
			</tfoot>
		</table>
	</div><?php
}
?>