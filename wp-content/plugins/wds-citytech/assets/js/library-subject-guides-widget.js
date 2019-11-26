(function($){
	var $accordions;

	$(document).ready(function(){
		var $document = $(document);

		$accordions = $('.library-subject-guide-selectors');

		initAccordions();

		$('.widget-action').on('click', function(){
			var $widget = $(this).closest('.widget');

			setTimeout(
				function() {
					$accordions.accordion( 'refresh' );
				},
				200
			);
		});

		$document.on('widget-updated', initAccordions);
	});

	var initAccordions = function() {
		$accordions = $('.library-subject-guide-selectors');
		$accordions.accordion({
			animate: false,
			collapsible: true,
			heightStyle: 'content'
		});

	}
}(jQuery));
