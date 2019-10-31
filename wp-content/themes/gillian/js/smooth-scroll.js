// Smooth scroll to top

( function( $ ) {

$(document).ready(function($){
	$(window).scroll(function(){
        if ($(this).scrollTop() < 200) {
			$('.back-to-top') .fadeOut();
        } else {
			$('.back-to-top') .fadeIn();
        }
    });
	$('.back-to-top').on('click', function(){
		$('html, body').animate({scrollTop:0}, 'fast');
		return false;
		});
});

} ( jQuery ));