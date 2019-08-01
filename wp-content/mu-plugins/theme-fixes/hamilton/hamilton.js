(function($){
	var $header,
	  $siteNav,
		resizeTimer;

	$(window).ready(function(){
		$header = $('.site-header');
		$siteNav = $('.site-nav');
	});

	$(window).on('load resize orientationchange', function(){
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(
			function() {
				var headerBottom = $header.position().top + $header.outerHeight(true);
				var newPadding = headerBottom + 40;
				$siteNav.css('padding-top', newPadding + 'px');
			},
		250);
	});
}(jQuery));
