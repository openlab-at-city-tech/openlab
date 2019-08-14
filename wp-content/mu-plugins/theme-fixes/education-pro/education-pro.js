(function($){
	$(document).ready(function(){
		setTimeout(
			function() {
				var $subMenuToggles = $('.responsive-menu > .menu-item .menu-item');

				var mq = window.matchMedia( "(max-width: 1023px)" );
				if ( mq.matches ) {
					$subMenuToggles.find(".sub-menu:first").hide();
				}

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
