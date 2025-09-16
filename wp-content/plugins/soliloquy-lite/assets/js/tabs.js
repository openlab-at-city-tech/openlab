/* global document */
/* jshint unused:false */
/**
* Soliloquy Admin Tabs
*/
; (function ($, window, document) {

	"use strict";
	var soliloquy_tabs_hash = window.location.hash,
		soliloquy_tabs_current_tab = window.location.hash.replace('!', '');

	if (soliloquy_tabs_hash && soliloquy_tabs_hash.indexOf('soliloquy-tab') >= 0) {

		var $current_tab_container = $(soliloquy_tabs_current_tab.replace('tab_', '')),
		    $tab_container = $current_tab_container.parent(),
		    $tab_nav = $current_tab_container.parent().parent().find('ul.soliloquy-tabs-nav'),
		    soliloquy_post_action = $('#post').attr('action');

		$tab_container.find('.soliloquy-tab-active').removeClass('soliloquy-tab-active');
		$current_tab_container.addClass('soliloquy-tab-active');

		//Remove Active Class from the nav tab
		$tab_nav.find('.soliloquy-tab-nav-active').removeClass('soliloquy-tab-nav-active');

		//Add Class to $this
		$tab_nav.find('a[href="' + soliloquy_tabs_current_tab.replace('tab_', '') + '"]').parent().addClass('soliloquy-tab-nav-active');
		// Update the form action to contain the selected tab as a hash in the URL
		// This means when the user saves their Gallery, they'll see the last selected
		// tab 'open' on reload
		if (soliloquy_post_action) {
			// Remove any existing hash from the post action
			soliloquy_post_action = soliloquy_post_action.split('#')[0];

			// Append the selected tab as a hash to the post action
			$('#post').attr('action', soliloquy_post_action + window.location.hash);
		}

		$('body').trigger('SoliloquyTabChange');

	}
	//Dom Ready
	$(function () {

		//Tab Clicked
		$('[data-soliloquy-tab]').on('click', function (e) {

			//Prevent Default
			e.preventDefault();

			//
			var $this = $(this),
				tab_id = $this.attr('data-tab-id'),
				$parent = $this.parent(),
				$container = $parent.parent(),
				soliloquy_update_hash = $parent.attr('data-update-hashbang'),
				$tab = ((typeof $this.attr('href') !== 'undefined') ? "tab_" + $this.attr('href') : "tab_" + tab_id);

			//If the tabs active return
			if ($this.hasClass('soliloquy-tab-nav-active')) {
				return;
			}

			//Remove Active Class from container
			$container.find('.soliloquy-tab-active').removeClass('soliloquy-tab-active');

			//Remove Active Class from the nav tab
			$parent.find('.soliloquy-tab-nav-active').removeClass('soliloquy-tab-nav-active');

			//Add Class to $this
			$this.addClass('soliloquy-tab-nav-active');

			//Add Class to Tab
			$("#" + tab_id).addClass('soliloquy-tab-active');

			//Trigger an event
			$this.trigger('SoliloquyTabChange');

			//TYPE CHANGE
			if (tab_id === 'soliloquy-native' && $('#soliloquy-type-default').prop('checked') !== true) {
				// Remove the Soliloquy class from all Slider Types
				$('#soliloquy-types-nav li').removeClass('soliloquy-active');
				$('#soliloquy-type-default').prop('checked', true).trigger('change');

			}

			// Update the window URL to contain the selected tab as a hash in the URL.
			if (soliloquy_update_hash === '1') {
				window.location.hash = $tab.split('#').join('#!');

				// Update the form action to contain the selected tab as a hash in the URL
				// This means when the user saves their Gallery, they'll see the last selected
				// tab 'open' on reload
				var soliloquy_post_action = $('#post').attr('action');

				if (soliloquy_post_action) {
					// Remove any existing hash from the post action
					soliloquy_post_action = soliloquy_post_action.split('#')[0];

					// Append the selected tab as a hash to the post action
					$('#post').attr('action', soliloquy_post_action + window.location.hash);

				}
			}

			return false;

		});

	});

})(jQuery, window, document);