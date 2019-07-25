(function($){
	$(document).ready(function(){
		$(".responsive-menu > .menu-item .menu-item").addClass( 'menu-open' ).click(function(event){
			if (event.target !== this) {
				return;
			};

			event.preventDefault();

			$(this).find(".sub-menu:first").slideToggle(function() {
				$(this).parent().toggleClass("menu-open");
			});
		});
	});
}(jQuery))
