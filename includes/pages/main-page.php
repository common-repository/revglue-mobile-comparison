<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_mcomp_main_page()
{
	?><div class="rg-admin-container">
		<h1 class="rg-admin-heading ">Welcome to RevGlue Mobile Comparison WordPress Plugin</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="panel-white mgBot">
			<h3>Introduction</h3>
			<p>RevGlue provides wordPress plugins for affiliates that are free to download and earn 100% commissions. RevGlue provides the following WordPress plugins.</p>
			<ul class="pagelist">
				<li>RevGlue Stores  - setup your shopping directory</li>
				<li>RevGlue Vouchers – setup your vouchers / coupons website.</li>
				<li>RevGlue Cashback – setup your cashback website within minutes.</li>
				<li>RevGlue Daily Deals – setup your daily deals aggregation engine in minutes.</li>
				<li>RevGlue Mobile Comparison – setup mobile comparison website in minutes.</li>
				<li>Banners API – add banners on your projects integrated in all plugins above.</li>
				<li>Broadband & TV -  setup broadband, tv and phone comparision website.</li>
			</ul> 
		</div>
		<div class="panel-white mgBot">
			
			<?php 
			$check = rg_check_subscription();
			if ($check=="Free") { ?>
			<h3>RevGlue Mobile Comparison Data and WordPress CMS Plugin </h3>
			<p>There are two ways you can obtain Mobile Comparison data on this plugin.</p>
			<p><b> 1 </b> - Subscribe to RevGlue affiliate Mobile Comparison data for £60 and add your own affiliate network IDs to earn 100% commission on your affiliate network accounts. Try is free for the first 30 days. Create RevGlue.com user account and subscribe with affiliate Mobile Comparison data set today.</p>
			<p><b> 2 </b> - You can use RevEmbed Mobile Comparison data set that is free to use and you are not required to create affiliate network accounts. RevEmbed data set for Mobile Comparison offers 80% commissiion to you on all the sales referred from your Mobile Comparison website. This is based on revenue share basis with with RevGlue that saves your time and money and provides you ability to create your Mobile Comparison website in minutes. Browse RevEmbed module.  Once you register for any both data source from the options given above. You will be provided with the project unique id that you are required to add in Import Mobile Comparison section and fetch the Mobile Comparison data.</p>
		<?php } else{ ?>
			<h3>RevGlue Mobile Comparison WordPress CMS Plugin</h3>
			<p>The aim of RevGlue Mobile Comparison plugin is to allow you to setup a mobile comparison website in UK. You will earn 100% commissions generated via the plugin and the CMS is totally free for all affiliates. You may make further copies or download latest versions from RevGlue website. You will require RevGlue account and then subscribe to RevGlue Mobile Comparison data set for any country you wish to setup the mobile comparison website. </p>
		<?php } ?>

		</div>
		<div class="panel-white mgBot">
			<h3>RevGlue Mobile Comparison Menu Explained</h3>
			<p><b>Import Mobiles</b>– Add your RevGlue Data account credentials to validate your account and obtain RevGlue Mobile Comparison Data. Use CRON file path to setup on your server to auto update the data dynamically.</p>
			<p><b>Import Banners</b>– Add your RevGlue Data account credentials to validate your account and obtain RevGlue Banners Data. Use CRON file path to setup on your server to auto update the data dynamically.</p>
			<p><b>Stores</b>– Shows all stores data obtained via RevGlue Data API. </p>
			<p><b>Mobiles</b>- Shows all mobiles data obtained from RevGlue Mobile Comparison Data API under upload Mobile Comparison menu.</p>
			<p><b>Mobile Deals</b>- Shows all mobile deals data obtained from RevGlue Mobile Comparison Data API under upload Mobile Comparison menu..</p>
			<p><b>Banners</b>- Allows you to add your own banner on website placements that are pre-defined for you. You may add multiple banners on one placements and they will auto change on each refresh. You may also subscribe with RevGlue Banners API and obtain latest banners for each Product Feed from RevGlue Banners. The banners you may add are known as LOCAL banners and others obtained via RevGlue Banner API are shown as RevGlue Banners.</p>
		</div>
		<div class="panel-white mgBot">
			<h3>Further Development</h3>
			<p>If you wish to add new modules or require additional design or development changes then contact us on <a href="mailto:support@revglue.com">support@revglue.com</a></p>
			<p>
				We are happy to analyse the required work and provide you a quote and schedule. 
			</p> 
		</div> 
		<div class="panel-white mgBot">
			<h3>Useful Links</h3>
			<p><b>RevGlue</b>- <a href="https://www.revglue.com/" target="_blank">https://www.revglue.com/</a></p>
			<p><b>RevGlue Mobile Comparison Data</b>- <a target="_blank" href="https://www.revglue.com/data">https://www.revglue.com/data</a></p>
			<p><b>RevGlue WordPress Plugin</b>- <a target="_blank" href="https://www.revglue.com/free-wordpress-plugins">https://www.revglue.com/free-wordpress-plugins</a></p>
			<p><b>RevGlue New Mobile Comparison Templates</b>- <a target="_blank" href="https://www.revglue.com/affiliate-website-templates">https://www.revglue.com/affiliate-website-templates</a></p>
		</div>
	</div><?php		
}
?>