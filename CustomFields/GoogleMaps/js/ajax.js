jQuery(document).ready(function(){
	jQuery('#reload_pam').click(function(){
		get_map();
	});
	
	jQuery('#autocomplete_map_extra_field_googlemap input').focusout(function(){
		get_map();
	});

	jQuery('#autocomplete_map_extra_field_googlemap input').keyup(function(){
		if(event.keyCode==13){
			event.preventDefault();
			get_map();
			return false;
		}
	});
	
	jQuery('#to_map_address_house').keyup(function(){
		get_map();
	});
})

function get_map(){
		var adress = jQuery('#to_map_address_city').val() + ' ' + jQuery('#to_map_address_street').val() + ' ' + jQuery('#to_map_address_house').val();
		var img = '<div id="loader_gif"><img src="'+ jQuery('#hide_google_pam_dir_url').val() + 'image/big-loader.gif"></div>';
		
		jQuery('#map_box').animate( {opacity: "0.0"}, 400, function(){
			jQuery('#map_box').empty();
			jQuery('#map_box').append( img );
			jQuery('#map_box').animate( {opacity: "1"}, 400 );

			jQuery.ajax({
					type: "POST",
					url: jQuery('#hide_google_pam_dir_url').val() + 'ajax.php',
					data: "adress="+adress,
					// Выводим то что вернул PHP
					success: function(html) {
						jQuery('#map_box').animate( {opacity: "0.0"}, 400, function(){
							jQuery('#map_box').empty();
							jQuery('#map_box').append( html );
							jQuery('#map_box').animate( {opacity: "1"}, 400 );
						});
					}
			});
		} );
}
/***************************************************/