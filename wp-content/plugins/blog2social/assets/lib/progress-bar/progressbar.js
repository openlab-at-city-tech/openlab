;
(function ($) {
	$.fn.loading = function () {
		var DEFAULTS = {
			backgroundColor: '#4b86db',
			progressColor: '#b3cef6',
			percent: 100,
			duration: 2000,
                        customText:''
		};	
		
		$(this).each(function () {
			var $target  = $(this);

			var opts = {
			backgroundColor: $target.data('color') ? $target.data('color').split(',')[0] : DEFAULTS.backgroundColor,
			progressColor: $target.data('color') ? $target.data('color').split(',')[1] : DEFAULTS.progressColor,
			percent: $target.data('percent') ? $target.data('percent') : DEFAULTS.percent,
                        customText: $target.data('custom-text') ? $target.data('custom-text') : DEFAULTS.customText,
			duration: $target.data('duration') ? $target.data('duration') : DEFAULTS.duration
			};
	
			$target.append('<div class="background"></div><div class="rotate"></div><div class="left"></div><div class="right"></div><div class=""><span>' + ((opts.customText != '') ? opts.customText :  opts.percent+'%') +'</span></div>');
	
			$target.find('.background').css('background-color', opts.backgroundColor);
			$target.find('.left').css('background-color', opts.backgroundColor);
			$target.find('.rotate').css('background-color', opts.progressColor);
			$target.find('.right').css('background-color', opts.progressColor);
	
			var $rotate = $target.find('.rotate');
			setTimeout(function () {	
				$rotate.css({
					'transition': 'transform ' + opts.duration + 'ms linear',
					'transform': 'rotate(' + opts.percent * 3.6 + 'deg)'
				});
			},1);		

			if (opts.percent > 50) {
				var animationRight = 'toggle ' + (opts.duration / opts.percent * 50) + 'ms step-end';
				var animationLeft = 'toggle ' + (opts.duration / opts.percent * 50) + 'ms step-start';  
				$target.find('.right').css({
					animation: animationRight,
					opacity: 1
				});
				$target.find('.left').css({
					animation: animationLeft,
					opacity: 0
				});
			} 
		});
	}
})(jQuery);