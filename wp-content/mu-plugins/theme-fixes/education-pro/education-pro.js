(function($){
	$(document).ready(function(){
		setTimeout(
			function() {
				var $subMenuToggles = $('.responsive-menu > .menu-item .menu-item');
				$subMenuToggles.addClass( 'menu-open' );
				$subMenuToggles.click(function(event){
					if (event.target !== this) {
						return;
					};

					event.preventDefault();

					$(this).find(".sub-menu:first").slideToggle(function() {
						$(this).parent().toggleClass("menu-open");
					});
				});
			},
			1000
		);
	});
}(jQuery))
