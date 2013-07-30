(function($) {
	$(document).ready(function(){
		if ( $('input[name="bwsmn_form_email"]').val() == '' )
			$('.bws_system_info_mata_box .inside').css('display','none');

		$('.bws_system_info_mata_box .handlediv').click( function(){
			if ( $('.bws_system_info_mata_box .inside').is(":visible") ) {
				$('.bws_system_info_mata_box .inside').css('display','none');
			} else {
				$('.bws_system_info_mata_box .inside').css('display','block');
			}					
		});	
	});
})(jQuery);