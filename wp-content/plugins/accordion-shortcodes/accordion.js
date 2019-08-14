(function($) {
	'use strict';

	var i, settings;



	/**
	 * Accordion Shortcodes plugin function
	 *
	 * @param object options Plugin settings to override the defaults
	 */
	$.fn.accordionShortcodes = function(options) {

		var items          = [];
		var allControllers = $('#' + options.id + ' .js-accordion-controller');
		var selectedId     = window.location.hash;
		var duration       = 250;
		var settings       = $.extend({
			// Set default settings
			autoClose:    true,
			openFirst:    false,
			openAll:      false,
			clickToClose: false,
			scroll:       false,
			usebuttons:   false,
		}, options);



		/**
		 * Initial setup
		 * Remove the 'no-js' class since JavaScript is enabled and set the
		 * scroll offset.
		 */
		$('.accordion').removeClass('no-js');

		settings.scrollOffset = Math.floor(parseInt(settings.scroll)) | 0;

		allControllers.each(function(index) {
			var initiallyOpen = false;

			// Should any accordions be opened or closed on load?
			if (index == 0 && settings.openFirst) {
				initiallyOpen = true;
			}

			// Open all overwrites open first setting
			if (settings.openAll) {
				initiallyOpen = true;
			}

			// Initial state settings on individual items override global initial state settings
			switch ($(this).data('initialstate')) {
				case 'open':
					var initiallyOpen = true;
					break;
				case 'closed':
					var initiallyOpen = false;
					break;
			}

			// ID hashs override all initial state settings
			if (selectedId.length && selectedId == '#' + $(this).attr('id')) {
				initiallyOpen = true;
			}

			var item = getAccordionItemObject($(this), initiallyOpen);

			items.push(item);

			// Add event listeners to controller
			$(this).click(function(event) {
				clickControllerHandler(item);
			});

			$(this).keyup(function(event) {
				var code = event.which;

				// We only need to add manual keyboard events if _not_ using `<button>` elements
				// `<button>` tags will natively fire the click event.
				if (!settings.usebuttons) {
					// 13 = Return, 32 = Space
					if ((code === 13) || (code === 32)) {
						$(this).click();
					}
				}

				// 27 = Esc
				if (code === 27) {
					if (settings.clickToClose) {
						closeAccordionItem(item);
					}
				}
			});

			// Should this item be opened or closed by default?
			if (item.isOpen) {
				openAccordionItem(item);
			}
			else {
				closeAccordionItem(item);
			}
		});



		/**
		 * Get an accordion item object
		 *
		 * @param array ele a jQuery object
		 * @param bool initiallyOpen Should this item be open by default?
		 * @return object An object with an accordion items components
		 */
		function getAccordionItemObject(ele, initiallyOpen) {
			return {
				id:         ele.attr('id'),
				controller: ele,
				controls:   ele.attr('aria-controls'),
				content:    $('#' + ele.attr('aria-controls')),
				isOpen:     initiallyOpen ? initiallyOpen : false,
			}
		}



		/**
		 * Defualt click function
		 * Called when an accordion controller is clicked.
		 */
		function clickControllerHandler(item) {
			// Only open the item if item isn't already open
			if (!item.isOpen) {
				// Close all accordion items
				if (settings.autoClose) {
					$.each(items, function(index, item) {
						closeAccordionItem(item);
					});
				}

				// Open clicked item
				openAccordionItem(item, true);
			}
			// If item is open, and click to close is set, close it
			else if (settings.clickToClose) {
				closeAccordionItem(item);
			}

			return false;
		}



		/**
		 * Opens an accordion item
		 * Also handles accessibility attribute settings.
		 *
		 * @param object item The accordion item to open
		 * @param bool scroll Whether to scroll the page
		 */
		function openAccordionItem(item, scroll) {
			item.content.clearQueue().stop().slideDown(duration, function() {
				// Scroll page to the title
				if (scroll && settings.scroll) {
					$('html, body').animate({
						scrollTop: item.controller.offset().top - settings.scrollOffset
					}, duration);
				}
			});

			item.controller.addClass('open read');

			// Set accessibility attributes
			item.controller.attr('aria-expanded', 'true');
			item.content.attr('aria-hidden', 'false');

			item.isOpen = true;
		}



		/**
		 * Closes an accordion item
		 * Also handles accessibility attribute settings.
		 *
		 * @param object item The accordion item to close
		 */
		function closeAccordionItem(item) {
			item.content.slideUp(duration);
			item.controller.removeClass('open');

			// Set accessibility attributes
			item.controller.attr('aria-expanded', 'false');
			item.content.attr('aria-hidden', 'true');

			item.isOpen = false;
		}



		// Listen for hash changes (in page jump links for accordions)
		$(window).on('hashchange', function() {
			selectedId = $(window.location.hash);

			if (selectedId.length && selectedId.hasClass('js-accordion-controller')) {
				// Simulate click on controller
				selectedId.click();
			}
		});

		return this;
	};



	// Loop through accordion settings objects
	// Wait for the entire page to load before loading the accordion
	$(window).on('load', function() {
		for (var i = 0; i < accordionShortcodesSettings.length; i += 1) {
			settings = accordionShortcodesSettings[i];

			$('#' + settings.id).accordionShortcodes(settings);
		}
	});
}(jQuery));
