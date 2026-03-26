(function($){
	$(document).ready(function(){
		$('#olgc-add-a-grade').change(function(){
			if ( $(this).is(':checked') ) {
				$('textarea#comment').removeAttr('required');
			} else {
				$('textarea#comment').attr('required', 'required');
			}
		});

		$('input#s').before('<label for="s" class="sr-only">Search terms</label>');

		$('.header-inner > a.logo').append('<span class="sr-only">Home</span>');

		$('.posts .post-bubbles a.format-bubble').each(function(k,v){
			var $bubble = $(this);
			$bubble.html('<span class="sr-only">' + $bubble.attr('title') + '</span>');
		});

		/* Add text to nav toggle button */
		const navToggle = document.querySelector('.nav-toggle');
		if (navToggle) {
			navToggle.innerHTML += '<span class="sr-only">Toggle navigation</span><span class="nav-toggle-icon"></span>';
		}
	});
}(jQuery));
