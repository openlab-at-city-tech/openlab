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
		var $el = $(this);
		// don't have a better way to do this at the moment
		setTimeout(function(){
			$el.removeClass( 'tooltip-on' );
		},2000);
	}

	var toggleTooltip = function() {
		$(this).toggleClass('tooltip-on');
	}

}(jQuery));
