(function($){
	$(document).ready(function(){
		$('#olgc-add-a-grade').change(function(){
			if ( $(this).is(':checked') ) {
				$('textarea#comment').removeAttr('required');
			} else {
				$('textarea#comment').attr('required', 'required');
			}
		});
	});
}(jQuery));
