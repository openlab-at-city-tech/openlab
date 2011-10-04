/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#tbf2_custom_slidespot_box').click(function () {
		var cs = $('#tbf2_custom_slidespot');
		if(cs.val() == 'yes') {
			cs.val('no');
		} else {
			cs.val('yes');
		}
		
	});
});
/* ]]> */