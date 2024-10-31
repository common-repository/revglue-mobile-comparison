function changeCheckbocValue(element,id){
	var import_data = {
			'action': 'revglue_comp_update_best_selling_mobile',
			'checked': (jQuery(element).is(":checked"))?'yes':'no',
			'rg_mobile_id': id
		};
		jQuery.post(
			ajaxurl, 
			import_data, 
			function(response) 
			{
				
			}
		);
}
jQuery( document ).ready(function() {
	jQuery(".chosen-select").chosen() ;
	// Adds lazy load to images
	jQuery("img.revglue-unveil").unveil();
	// Initialize Stores Datatable
    jQuery('#stores_admin_screen_listing').DataTable({
    	"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=revglue_comp_stores_admin_screen_listing_query',
		"bPaginate": false,
		"order": [[ 4, 'asc' ]],
		"drawCallback": function( settings ) {
            jQuery("#stores_admin_screen_listing img:visible").unveil();
        }
	});
	// Initialize Banners Datatable
    jQuery('#banners_admin_screen').DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=revglue_mcomp_load_banners',
		"pageLength": 50,
		"order": [[ 0, 'desc' ]],
		"drawCallback": function( settings ) {
            jQuery("#banners_admin_screen img:visible").unveil(); 
        }
	});
	// Initialize Mobile Comparison Datatable 
    jQuery('#mobiles_admin_screen_listing').DataTable({
    	"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=revglue_comp_mobiles_admin_screen_listing_query',
		"pageLength": 50,
		"order": [[ 1, 'asc' ]],
		"drawCallback": function( settings ) {
            jQuery('.rg_best_mobile_tag').iphoneStyle();
			jQuery("#mobiles_admin_screen_listing img:visible").unveil();
        }
	});
	// Initialize Mobile Deals Page Datatable
    jQuery('#mcompdeal_admin_screen_import').DataTable({
    	"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=revglue_comp_deal_admin_screen_import_query',
		"bPaginate": false,
		"order": [[ 1, 'asc' ]],
		"drawCallback": function( settings ) { 
			jQuery("#mcompdeal_admin_screen_import img:visible").unveil();
        }
	});
	// Initialize Mobile Deals Listing Page Datatable
    jQuery('#deals_admin_screen_listing').DataTable({
    	"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=deals_admin_screen_listing_query',
		"pageLength": 50,
		"drawCallback": function( settings ) {
			jQuery('.rg_recommended_deal_tag').iphoneStyle();
            jQuery("#deals_admin_screen_listing img:visible").unveil();
        }
	});
	// Initialize Mobile Deals Datatable
    jQuery('#mcompdeal_admin_screen').DataTable({
		"pageLength": 50,
		"order": [[ 2, 'asc' ]],
		"drawCallback": function( settings ) {
            jQuery("#mcompdeal_admin_screen img:visible").unveil();
        }
	});
	jQuery( "#mcompdeal_admin_screen_import" ).on( "click", ".rg_import_mdeal", function( event ) {
		var rg_store_id = jQuery(this).data('rg_store_id');
		//alert(rg_store_id);
		jQuery("#mdeal_antiloader_"+rg_store_id).hide();
		jQuery("#mdeal_loader_"+rg_store_id).show();
		var mdeal_data = {
			'action': 'revglue_mdeals_get_daily_deals',
			'rg_store_id': rg_store_id
		}; 
		jQuery.post(
			ajaxurl, 
			mdeal_data, 
			function(response) 
			{ 
				console.log(response);
				var response_object = JSON.parse(response);
				jQuery("#mdeal_loader_"+response_object.rg_store_id).hide();
				jQuery("#mdeal_updated_"+response_object.rg_store_id).text(response_object.last_updated_deal);
				jQuery("#mdeal_fcount_"+response_object.rg_store_id).text(response_object.count_deal);
jQuery( "#mdeal_antiloader_"+response_object.rg_store_id ).html( "<a href='javascript:' class='rg_import_mdeal btn txtwhite btn-primary' data-rg_store_id='"+response_object.rg_store_id+"' >Import</a>" );
				jQuery("#mdeal_antiloader_"+response_object.rg_store_id).show();
			}
		);
	});
	jQuery( "#rg_mcomp_sub_activate" ).on( "click", function() {
		var sub_id 		= jQuery( "#rg_mcomp_sub_id" ).val();
		var sub_email 	= jQuery( "#rg_mcomp_sub_email" ).val();
		var sub_pass 	= jQuery( "#rg_mcomp_sub_password" ).val();
		if( sub_id == "" )
		{
			jQuery('#subscription_error').text("Please First enter your unique Subscription ID");	
			return false;
		}
		if( sub_email == "" )
		{
			jQuery('#subscription_error').text( "Please First enter your Email" );	
			return false;
		}
		if( sub_pass == "" )
		{
			jQuery('#subscription_error').text("Please First enter your Password");	
			return false;
		}
		var subscription_data = {
			'action'	: 'revglue_mcomp_subscription_validate',
			'sub_id'	: sub_id,
			'sub_email'	: sub_email,
			'sub_pass'	: sub_pass
		};
		jQuery('#subscription_error').html("");
		jQuery('#subscription_response').html("");
		jQuery("#sub_loader").show();
		jQuery.post(
			ajaxurl,
			subscription_data,
			function( response )
			{		
				jQuery("#rg_mcomp_sub_id").val("");
				jQuery('#sub_loader').hide();
				jQuery('#subscription_response').html(response);
			}
		);
		return false;
	});
	jQuery( "#rg_mcomp_import" ).on( "click", function(e) {
		e.preventDefault();
		console.log("************** Developed by Imran Javed as on 06-10-2017 *********************")
		type = jQuery( this ).attr( 'href' );
		var import_data = {
			'action': 'revglue_mcomp_data_import',
			'import_type': type
		};
		console.log(import_data);
		jQuery("#subscription_error").html("");
		jQuery(".sub_page_table").hide();
		jQuery('#store_loader').show();
		jQuery.post(
			ajaxurl, 
			import_data, 
			function(response) 
			{
				console.log(response);
				jQuery('#store_loader').hide();
				jQuery(".sub_page_table").show();
				jQuery('#rg_mcomp_import_popup').hide();
				var response_object = JSON.parse(response);
				jQuery(".sub_page_table").prepend(response_object.error_msgs);
				jQuery(".alert").delay(5000).fadeOut('slow');
				jQuery('#rg_store_count').text(response_object.count_store);	
				jQuery('#rg_store_date').text(response_object.last_updated_store);
				jQuery('#rg_mobile_count').text(response_object.count_mobile);	
				jQuery('#rg_mobile_date').text(response_object.last_updated_mobile);
				jQuery('#rg_deal_count').text(response_object.count_deal);	
				jQuery('#rg_deal_date').text(response_object.last_updated_deal);
			}
		);
		return false;
	});
	jQuery( "#rg_banner_import" ).on( "click", function(e) {
		e.preventDefault();
		type = jQuery( this ).attr( 'href' );
		var import_data = {
			'action': 'revglue_mcomp_banner_data_import',
			'import_type': type
		};
		jQuery("#subscription_error").html("");
		jQuery(".sub_page_table").hide();
		jQuery('#store_loader').show();
		jQuery.post(
			ajaxurl, 
			import_data, 
			function(response) 
			{
				jQuery('#store_loader').hide();
				jQuery(".sub_page_table").show();
				jQuery('#rg_mcomp_import_popup').hide();
				var response_object = JSON.parse(response);
				jQuery(".sub_page_table").prepend(response_object.error_msgs);
				jQuery(".alert").delay(5000).fadeOut('slow');
				jQuery('#rg_banner_count').text(response_object.count_banner);
			}
		);
		return false;
	});
	jQuery( "#rg_mcomp_delete" ).on( "click", function(e) {
		e.preventDefault();
		type = jQuery( this ).attr( 'href' );
		var delete_data = {
			'action': 'revglue_mcomp_data_delete',
			'data_type': type
		};
		jQuery("#subscription_error").html("");
		jQuery(".sub_page_table").hide();
		jQuery('#store_loader').show();
		jQuery.post(
			ajaxurl, 
			delete_data, 
			function(response) 
			{
				jQuery('#store_loader').hide();
				jQuery(".sub_page_table").show();
				jQuery('#rg_mcomp_delete_popup').hide();
				var response_object = JSON.parse(response);
				if( response_object.data_type == 'rg_stores' )
				{
					jQuery('#rg_store_count').text(response_object.count_store);	
					jQuery('#rg_store_date').text(response_object.last_updated_store);
				} else if( response_object.data_type == 'rg_mobiles' )
				{
					jQuery('#rg_mobile_count').text(response_object.count_mobile);		
					jQuery('#rg_mobile_date').text(response_object.last_updated_mobile);
				} else if( response_object.data_type == 'rg_deals' )
				{
					jQuery('#rg_deal_count').text(response_object.count_deal);		
					jQuery('#rg_deal_date').text(response_object.last_updated_deal);
				} else if( response_object.data_type == 'rg_banners' )
				{
					jQuery('#rg_banner_count').text(response_object.count_banner);
				}
			}
		);
		return false;
	});
	jQuery('.rg-admin-container').on('mouseenter', '.rg_store_link_pop_up', function( event ) {
		var id = this.id;
		jQuery('#imp_popup'+id).show();
	}).on('mouseleave', '.rg_store_link_pop_up', function( event ) {
		var id = this.id;
		jQuery('#imp_popup'+id).hide();
	});
	jQuery( ".rg_mcomp_open_import_popup" ).on( "click", function(e) {
		e.preventDefault();
		var type = jQuery( this ).attr( "href" );
		jQuery('#rg_mcomp_delete_popup').hide();	
		jQuery('#rg_mcomp_import_popup').show();
		jQuery('.rg_mcomp_start_import').attr( "href", type );
	});
	jQuery( ".rg_mcomp_open_delete_popup" ).on( "click", function(e) {
		e.preventDefault();
		var type = jQuery( this ).attr( "href" );
		jQuery('#rg_mcomp_import_popup').hide();
		jQuery('#rg_mcomp_delete_popup').show();	
		jQuery('.rg_mcomp_start_delete').attr( "href", type );
	});
	jQuery('#rg_banner_image_type').on( "change", function(e) {
		var type = jQuery( this ).val();
		if( type == 'url' )
		{
			jQuery('#rg_banner_image_file').val('');
			jQuery('#rg_mcomp_banner_image_upload').hide();
			jQuery('#rg_mcomp_banner_image_url').show();
		} else
		{
			jQuery('#rg_banner_image_url').val('');
			jQuery('#rg_mcomp_banner_image_url').hide();
			jQuery('#rg_mcomp_banner_image_upload').show();
		}
	});
	jQuery('.rg_best_mobile_tag').iphoneStyle();
	jQuery( "#mobiles_admin_screen" ).on( "change",  ".rg_best_mobile_tag", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var mobile_tag_data = {
			'action': 'revglue_mcomp_update_best_mobile',
			'mobile_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			mobile_tag_data, 
			function(response) 
			{
			}
		);
	});
	jQuery('.rg_recommended_deal_tag').iphoneStyle();
	jQuery( "#deals_admin_screen_listing" ).on( "change",  ".rg_recommended_deal_tag", function(e) {
		console.log("rg_recommended_deal_tag is changed.");
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var deal_tag_data = {
			'action': 'revglue_mcomp_update_recommended_deal',
			'deal_id': this.id,
			'state' : tag_checked
		};
		console.log(deal_tag_data);
		jQuery.post(
			ajaxurl, 
			deal_tag_data, 
			function(response) 
			{
		console.log(response);
			}
		);
	});
});