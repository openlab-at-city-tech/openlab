jQuery(document).ready(function($) {

	/********************************************************************
    	 *                      FAQs shortcode
    	 ********************************************************************/

	// Accordion mode
	// click triggered only if data-faqs-type is 'faqs'

	$('.epkb-faqs-accordion-mode .epkb-faqs__item__question').filter(function() {
		return $(this).data('faq-type') == 'faqs';
	}).on('click', function(){

		var container = $(this).closest('.epkb-faqs__item-container').eq(0);

		if (container.hasClass('epkb-faqs__item-container--active')) {
			container.find('.epkb-faqs__item__answer').stop().slideUp(400);
		} else {
			container.find('.epkb-faqs__item__answer').stop().slideDown(400);
		}
		container.toggleClass('epkb-faqs__item-container--active');
	});
	// Toggle Mode
	$('.epkb-faqs-toggle-mode .epkb-faqs__item__question').filter(function() {
		return $(this).data('faq-type') == 'faqs';
	}).on('click', function(){
		var container = $(this).closest('.epkb-faqs__item-container').eq(0);

		// Close other opened items
		$('.epkb-faqs__item-container--active').not(container).removeClass('epkb-faqs__item-container--active')
			.find('.epkb-faqs__item__answer').stop().slideUp(400);

		// Toggle the clicked item
		if (container.hasClass('epkb-faqs__item-container--active')) {
			container.find('.epkb-faqs__item__answer').stop().slideUp(400);
		} else {
			container.find('.epkb-faqs__item__answer').stop().slideDown(400);
		}
		container.toggleClass('epkb-faqs__item-container--active');
	});


});
