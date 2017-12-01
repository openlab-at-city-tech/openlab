(function($) {
	'use strict';

	var i, settings;



	/**
	 * Accordion Shortcodes plugin function
	 *
	 * @param object options Plugin settings to override the defaults
	 */
	$.fn.accordionShortcodes = function(options) {

		var allTitles  = this.children('.accordion-title'),
			allPanels  = this.children('.accordion-content').hide(),
			firstTitle = allTitles.first(),
			firstPanel = allPanels.first(),
			selectedId = $(window.location.hash),
			duration   = 250,
			settings   = $.extend({
				// Set default settings
				autoClose:    true,
				openFirst:    false,
				openAll:      false,
				clickToClose: false,
				scroll:       false
			}, options);



		/**
		 * Initial setup
		 * Remove the 'no-js' class since JavaScript is enabled and set the
		 * scroll offset.
		 */
		$('.accordion').removeClass('no-js');

		settings.scrollOffset = Math.floor(parseInt(settings.scroll)) | 0;



		/**
		 * Defualt click function
		 * Called when an accordion title is clicked.
		 */
		function clickHandler() {
			// Only open the item if item isn't already open
			if (!$(this).hasClass('open')) {
				// Close all accordion items
				if (settings.autoClose) {
					allTitles.each(function() {
						closeItem($(this));
					});
				}

				// Open clicked item
				openItem($(this), true);
			}
			// If item is open, and click to close is set, close it
			else if (settings.clickToClose) {
				closeItem($(this));
			}

			return false;
		}



		/**
		 * Opens an accordion item
		 * Also handles accessibility attribute settings.
		 *
		 * @param object ele The accordion item title to open
		 * @param bool scroll Whether to scroll the page
		 */
		function openItem(ele, scroll) {
			// Clear/stop any previous animations before revealing content
			ele.next().clearQueue().stop().slideDown(duration, function() {
				// Scroll page to the title
				if (scroll && settings.scroll) {
					$('html, body').animate({
						scrollTop: $(this).prev().offset().top - settings.scrollOffset
					}, duration);
				}
			});

			// Mark accordion item as open and read and set aria attributes
			ele.addClass('open read')
			.attr({
				'aria-selected': 'true',
				'aria-expanded': 'true'
			})
			.next().attr({
				'aria-hidden': 'false'
			});
		}



		/**
		 * Closes an accordion item
		 * Also handles accessibility attribute settings.
		 *
		 * @param object ele The accordion item title to open
		 */
		function closeItem(ele) {
			ele.next().slideUp(duration);
			ele.removeClass('open');

			// Set accessibility attributes
			ele.attr({
				'aria-selected': 'false',
				'aria-expanded': 'false'
			})
			.next().attr({
				'aria-hidden': 'true'
			});
		}



		/**
		 * Should any accordions be opened or closed on load?
		 * Open first, open all, open based on URL hash or open/closed based on
		 * initial state setting.
		 */
		if (selectedId.length && selectedId.hasClass('accordion-title')) {
			openItem(selectedId, true);
		}
		else if (settings.openAll) {
			allTitles.each(function() {
				openItem($(this), false);
			});
		}
		else if (settings.openFirst) {
			openItem(firstTitle, false);
		}

		// Open or close items if initial state set to open or close
		$('[data-initialstate!=""]').each(function() {
			switch ($(this).data('initialstate')) {
				case 'open':
					openItem($(this), false);
					break;
				case 'closed':
					// Only close it if the hash isn't for this item
					if ($(this).attr('id') !== selectedId.attr('id')) {
						closeItem($(this));
					}
					break;
			}
		});



		/**
		 * Add event listeners
		 */
		allTitles.click(clickHandler);

		allTitles.keydown(function(e) {
			var code = e.which;

			// 13 = Return, 32 = Space
			if ((code === 13) || (code === 32)) {
				// Simulate click on title
				$(this).click();
			}
		});

		// Listen for hash changes (in page jump links for accordions)
		$(window).on('hashchange', function() {
			selectedId = $(window.location.hash);

			if (selectedId.length && selectedId.hasClass('accordion-title')) {
				if (settings.autoClose) {
					allTitles.each(function() {
						closeItem($(this));
					});
				}

				openItem(selectedId, true);
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
