/**
 * This is the JavaScript related to group creation. It's loaded only during the group creation
 * process.
 *
 * Added by Boone 7/7/12. Don't remove me during remediation.
 */

function showHide(id) {
  var style = document.getElementById(id).style
   if (style.display == "none")
	style.display = "";
   else
	style.display = "none";
}

jQuery(document).ready(function($){
	function new_old_switch( noo ) {
		var radioid = '#new_or_old_' + noo;
		$(radioid).prop('checked','checked');

		$('input.noo_radio').each(function(i,v) {
			var thisval = $(v).val();
			var thisid = '#noo_' + thisval + '_options';
			console.log($(thisid));
			if ( noo == thisval ) {
				$(thisid).removeClass('disabled-opt');
				$(thisid).find('input').each(function(index,element){
					$(element).removeProp('disabled').removeClass('disabled');
				});
				$(thisid).find('select').each(function(index,element){
					$(element).removeProp('disabled').removeClass('disabled');
				});
			} else {
				$(thisid).addClass('disabled-opt');
				$(thisid).find('input').each(function(index,element){
					$(element).prop('disabled','disabled').addClass('disabled');
				});
				$(thisid).find('select').each(function(index,element){
					$(element).prop('disabled','disabled').addClass('disabled');
				});
			}
		});

	}

	$('.noo_radio').click(function(el){
		var whichid = $(el.target).prop('id').split('_').pop();
		new_old_switch(whichid);
	});

	// setup
	new_old_switch( 'new' );
},(jQuery));