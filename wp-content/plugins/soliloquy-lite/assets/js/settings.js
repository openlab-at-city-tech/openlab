/* global soliloquy_settings */
/* ==========================================================
 * settings.js
 * http://soliloquywp.com/
 * ==========================================================
 * Copyright 2016 Soliloquy Team.
 *
 * Licensed under the GPL License, Version 2.0 or later (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
; (function ($, window, document, soliloquy_settings) {

	//Dom Ready
	$(function () {

		// Initialize the slider tabs.
		var soliloquy_tabs = $('#soliloquy-tabs'),
			soliloquy_tabs_nav = $('#soliloquy-tabs-nav'),
			soliloquy_tabs_hash = window.location.hash,
			soliloquy_tabs_hash_sani = window.location.hash.replace('!', '');

		// If we have a hash and it begins with "soliloquy-tab", set the proper tab to be opened.
		if (soliloquy_tabs_hash && soliloquy_tabs_hash.indexOf('soliloquy-tab-') >= 0) {

			$('.soliloquy-active').removeClass('soliloquy-active nav-tab-active');

			soliloquy_tabs_nav.find('a[href="' + soliloquy_tabs_hash_sani + '"]').addClass('soliloquy-active nav-tab-active');

			soliloquy_tabs.find(soliloquy_tabs_hash_sani).addClass('soliloquy-active').show();

		}

		// Start the upgrade process.
		$('.soliloquy-start-upgrade').on('click', function (e) {

			e.preventDefault();

			var $this = $(this);

			// Show the spinner.
			$('.soliloquy-spinner').css({
				'display': 'inline-block',
				'float': 'none',
				'vertical-align': 'text-bottom'
			});

			// Prepare our data to be sent via Ajax.
			var upgrade = {
				action: 'soliloquy_upgrade_sliders',
				nonce: soliloquy_settings.upgrade_nonce
			};

			// Process the Ajax response and output all the necessary data.
			$.post(
				soliloquy_settings.ajax,
				upgrade,
				function (response) {
					// Hide the spinner.
					$('.soliloquy-spinner').hide();
					// Redirect back to Soliloquy screen.
					window.location.replace(soliloquy_settings.redirect);
				}, 'json');

		});

		// Change tabs on click.
		$('#soliloquy-tabs-nav a').on('click', function (e) {

			e.preventDefault();

			var $this = $(this);

			//If the tab is active return, else switch tabs
			if ($this.hasClass('soliloquy-active')) {

				return;

			} else {

				window.location.hash = soliloquy_tabs_hash = this.hash.split('#').join('#!');

				var current = soliloquy_tabs_nav.find('.soliloquy-active').removeClass('soliloquy-active nav-tab-active').attr('href');

				$this.addClass('soliloquy-active nav-tab-active');

				soliloquy_tabs.find(current).removeClass('soliloquy-active').hide();

				soliloquy_tabs.find($this.attr('href')).addClass('soliloquy-active').show();

			}

		});

		//Create the Select boxes
		$('.soliloquy-chosen').each(function () {

			var data_options = $(this).data('soliloquy-chosen-options');

			$(this).chosen(data_options);

		});

	});

})(jQuery, window, document, soliloquy_settings);