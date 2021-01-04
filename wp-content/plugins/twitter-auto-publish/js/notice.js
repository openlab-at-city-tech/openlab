	jQuery(document).ready(function() {
		jQuery('#system_notice_area').animate({
			opacity : 'show',
			height : 'show'
		}, 500);

		jQuery('#system_notice_area_dismiss').click(function() {
			jQuery('#system_notice_area').animate({
				opacity : 'hide',
				height : 'hide'
			}, 500);

		});

	});
	function XyzTwapToggleRadio(value,buttonId)
	{
		if (value == '1') {
	    	jQuery("#"+buttonId+"_no").removeClass( "xyz_twap_toggle_on" ).addClass( "xyz_twap_toggle_off" );
	    	jQuery("#"+buttonId+"_yes").removeClass( "xyz_twap_toggle_off" ).addClass( "xyz_twap_toggle_on" );
	        }
	    else if (value == '0') {
	    	jQuery("#"+buttonId+"_yes").removeClass( "xyz_twap_toggle_on" ).addClass( "xyz_twap_toggle_off" );
	    	jQuery("#"+buttonId+"_no").removeClass( "xyz_twap_toggle_off" ).addClass( "xyz_twap_toggle_on" );
	    	
	    }
	}