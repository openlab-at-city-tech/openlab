(function($){
	var $badges;

	$(document).ready(function(){
		$badges = $('.avatar-badge');

		if ( !! ('ontouchstart' in window ) ) {
			$badges.on('click', toggleTooltip);
		} else {
			$badges.on('mouseover', enableTooltip);
			$badges.on('mouseleave', disableTooltip);
		}
	});

	var enableTooltip = function() {
		$badges.removeClass( 'tooltip-on' );
		$(this).addClass( 'tooltip-on' );
	}

	var disableTooltip = function(e) {
		$(this).removeClass( 'tooltip-on' );
	}

	var toggleTooltip = function() {
		$(this).toggleClass( 'tooltip-on' );
	}

}(jQuery));
