/* global soliloquy_addons, ajaxurl */
/* exported e */
/*jshint unused:false*/
/* ==========================================================
 * settings.js
 * https://soliloquywp.com/
 * ==========================================================
 * Copyright 2014 Soliloquy Team.
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
// jshint ignore:line
; (function ($, window, document, soliloquy_addons) {

	//DOM Ready
	$(function () {

		//Create the Select boxes
		$('.soliloquy-chosen').each(function () {

			$(this).chosen({
				disable_search: true
			});

		});

		//Sort Filter for addons
		$('#soliloquy-addon-filter').on('change', function () {

			var $select = $(this),
				$value = $select.val(),
				$container = $('#soliloquy-addons-area'),
				container_data = $container.data('soliloquy-filter'),
				$addon = $('#soliloquy-addons-area .soliloquy-addon');

			//Make sure the addons are visible.
			$addon.show();

			switch ($value) {

				case 'asc':

					$addon.sort(function (a, b) {

						return $(a).data('addon-title').localeCompare($(b).data('addon-title'));

					}).each(function (_, addon) {

						$(addon).removeClass('last');

						$container.append(addon).hide().fadeIn(100);

					});

					$("#soliloquy-addons-area .soliloquy-addon:nth-child(3n)").addClass('last');

					break;
				case 'desc':

					$addon.sort(function (a, b) {

						return $(b).data('addon-title').localeCompare($(a).data('addon-title'));

					}).each(function (_, addon) {

						$(addon).removeClass('last');
						$container.append(addon).hide().fadeIn(100);

					});

					$("#soliloquy-addons-area .soliloquy-addon:nth-child(3n)").addClass('last');

					break;
				case 'active':

					$addon.hide().filter('[data-addon-status="active"]').show();

					$addon.removeClass('last');

					$('#soliloquy-addons-area .soliloquy-addon:visible').each(function (i) {

						if ((i + 1) % 3 === 0) {

							$(this).addClass('last');
						}

					});

					break;
				case 'inactive':

					$addon.hide().filter('[data-addon-status="inactive"]').show();
					$addon.removeClass('last');

					$('#soliloquy-addons-area .soliloquy-addon:visible').each(function (i) {

						if ((i + 1) % 3 === 0) {
							$(this).addClass('last');
						}
					});

					break;
				case 'installed':
					var i = 0;
					$addon.hide().filter('[data-addon-status="not_installed"]').show();
					$addon.removeClass('last');
					$('#soliloquy-addons-area .soliloquy-addon:visible').each(function (i) {
						if ((i + 1) % 3 === 0) {
							$(this).addClass('last');
						}
					});
					break;

			}

		});

		// Re-enable install button if user clicks on it, needs creds but tries to install another addon instead.
		$('#soliloquy-addons-area').on('click.refreshInstallAddon', '.soliloquy-addon-action-button', function (e) {

			e.preventDefault();

			var $el = $(this),
				buttons = $('#soliloquy-addons-area').find('.soliloquy-addon-action-button');

			$.each(buttons, function (i, element) {

				if ($el === element) {

					return true;

				}

				soliloquyAddonRefresh(element);

			});
		});

		// Process Addon activations for those currently installed but not yet active.
		$('#soliloquy-addons-area').on('click.activateAddon', '.soliloquy-activate-addon', function (e) {

			e.preventDefault();

			var $button = $(this),
				plugin = $button.attr('rel'),
				$el = $button.parent().parent(),
				$message = $button.parent().parent().find('.addon-status').children('span');

			// Remove any leftover error messages, output an icon and get the plugin basename that needs to be activated.
			$('.soliloquy-addon-error').remove();

			$button.text(soliloquy_addons.activating);
			$button.next().css({
				'display': 'inline-block',
				'margin-top': '0px'
			});

			// Process the Ajax to perform the activation.
			var opts = {
				url: ajaxurl,
				type: 'post',
				async: true,
				cache: false,
				dataType: 'json',
				data: {
					action: 'soliloquy_activate_addon',
					nonce: soliloquy_addons.activate_nonce,
					plugin: plugin
				},
				success: function (response) {
					// If there is a WP Error instance, output it here and quit the script.
					if (response && true !== response) {
						$el.slideDown('normal', function () {
							$(this)
								.after('<div class="soliloquy-addon-error"><strong>' + response.error + '</strong></div>');
							$button.next()
								.hide();
							$('.soliloquy-addon-error')
								.delay(3000)
								.slideUp();
						});
						return;
					}
					// The Ajax request was successful, so let's update the output.
					$button.text(soliloquy_addons.deactivate).removeClass('soliloquy-activate-addon').addClass('soliloquy-deactivate-addon');
					$message.text(soliloquy_addons.active);
					$el.removeClass('soliloquy-addon-inactive').addClass('soliloquy-addon-active');
					$button.next().hide();
				},
				error: function (xhr, textStatus, e) {
					$button.next()
						.hide();
					return;
				}
			};
			$.ajax(opts);
		});

		// Process Addon deactivations for those currently active.
		$('#soliloquy-addons-area').on('click.deactivateAddon', '.soliloquy-deactivate-addon', function (e) {

			e.preventDefault();

			var $button = $(this),
				plugin = $button.attr('rel'),
				$el = $button.parent().parent(),
				$message = $button.parent().parent().find('.addon-status').children('span');

			// Remove any leftover error messages, output an icon and get the plugin basename that needs to be activated.
			$('.soliloquy-addon-error').remove();

			$button.text(soliloquy_addons.deactivating);
			$button.next().css({
				'display': 'inline-block',
				'margin-top': '0px'
			});

			// Process the Ajax to perform the activation.
			var opts = {
				url: ajaxurl,
				type: 'post',
				async: true,
				cache: false,
				dataType: 'json',
				data: {
					action: 'soliloquy_deactivate_addon',
					nonce: soliloquy_addons.deactivate_nonce,
					plugin: plugin
				},
				success: function (response) {
					// If there is a WP Error instance, output it here and quit the script.
					if (response && true !== response) {

						$el.slideDown('normal', function () {
							$(this).after('<div class="soliloquy-addon-error"><strong>' + response.error + '</strong></div>');
							$button.next().hide();
							$('.soliloquy-addon-error').delay(3000).slideUp();
						});

						return;
					}

					// The Ajax request was successful, so let's update the output.
					$button.text(soliloquy_addons.activate).removeClass('soliloquy-deactivate-addon').addClass('soliloquy-activate-addon');
					$message.text(soliloquy_addons.inactive);
					$el.removeClass('soliloquy-addon-active').addClass('soliloquy-addon-inactive');
					$button.next().hide();
				},
				error: function (xhr, textStatus, e) {
					$button.next().hide();
					return;
				}
			};
			$.ajax(opts);
		});

		// Process Addon installations.
		$('#soliloquy-addons-area').on('click.installAddon', '.soliloquy-install-addon', function (e) {

			e.preventDefault();

			var $button = $(this),
				plugin = $button.attr('rel'),
				$el = $button.parent().parent(),
				$message = $button.parent().parent().find('.addon-status').children('span');

			// Remove any leftover error messages, output an icon and get the plugin basename that needs to be activated.
			$('.soliloquy-addon-error').remove();

			$button.text(soliloquy_addons.installing);
			$button.next().css({
				'display': 'inline-block',
				'margin-top': '0px'
			});

			// Process the Ajax to perform the activation.
			var opts = {
				url: ajaxurl,
				type: 'post',
				async: true,
				cache: false,
				dataType: 'json',
				data: {
					action: 'soliloquy_install_addon',
					nonce: soliloquy_addons.install_nonce,
					plugin: plugin
				},
				success: function (response) {
					// If there is a WP Error instance, output it here and quit the script.
					if (response.error) {
						$el.slideDown('normal', function () {
							$button.parent().parent().after('<div class="soliloquy-addon-error"><strong>' + response.error + '</strong></div>');
							$button.text(soliloquy_addons.install);
							$button.next().hide();
							$('.soliloquy-addon-error').delay(4000)
								.slideUp();
						});
						return;
					}

					// If we need more credentials, output the form sent back to us.
					if (response.form) {
						// Display the form to gather the users credentials.
						$el.slideDown('normal', function () {
							$(this).after('<div class="soliloquy-addon-error">' + response.form + '</div>');
							$button.next().hide();
						});

						// Add a disabled attribute the install button if the creds are needed.
						$button.attr('disabled', true);

						$('#soliloquy-addons-area').on('click.installCredsAddon', '#upgrade', function (e) {
							// Prevent the default action, let the user know we are attempting to install again and go with it.
							e.preventDefault();
							$button.next().hide();
							$(this)
								.val(soliloquy_addons.installing);
							$(this)
								.next()
								.css({
									'display': 'inline-block',
									'margin-top': '0px'
								});

							// Now let's make another Ajax request once the user has submitted their credentials.
							var hostname = $(this).parent().parent().find('#hostname').val(),
								username = $(this).parent().parent().find('#username').val(),
								password = $(this).parent().parent().find('#password').val(),
								proceed = $(this),
								connect = $(this).parent().parent().parent().parent();
							var cred_opts = {
								url: ajaxurl,
								type: 'post',
								async: true,
								cache: false,
								dataType: 'json',
								data: {
									action: 'soliloquy_install_addon',
									nonce: soliloquy_addons.install_nonce,
									plugin: plugin,
									hostname: hostname,
									username: username,
									password: password
								},
								success: function (response) {
									// If there is a WP Error instance, output it here and quit the script.
									if (response.error) {
										$el.slideDown('normal', function () {
											$button.parent().parent().after('<div class="soliloquy-addon-error"><strong>' + response.error + '</strong></div>');
											$button.text(soliloquy_addons.install);
											$button.next().hide();
											$('.soliloquy-addon-error').delay(4000).slideUp();
										});

										return;
									}

									if (response.form) {
										$button.next().hide();
										$('.soliloquy-inline-error').remove();
										$(proceed)
											.val(soliloquy_addons.proceed);
										$(proceed)
											.after('<span class="soliloquy-inline-error">' + soliloquy_addons.connect_error + '</span>');
										return;
									}

									// The Ajax request was successful, so let's update the output.
									$(connect)
										.remove();
									$button.show();
									$button.text(soliloquy_addons.activate).removeClass('soliloquy-install-addon').addClass('soliloquy-activate-addon');
									$button.attr('rel', response.plugin);
									$button.removeAttr('disabled');
									$message.text(soliloquy_addons.inactive);
									$el.removeClass('soliloquy-addon-not-installed').addClass('soliloquy-addon-inactive');
									$button.next().hide();
								},
								error: function (xhr, textStatus, e) {
									$button.next().hide();
									return;
								}
							};
							$.ajax(cred_opts);
						});

						// No need to move further if we need to enter our creds.
						return;
					}

					// The Ajax request was successful, so let's update the output.
					$button.text(soliloquy_addons.activate).removeClass('soliloquy-install-addon').addClass('soliloquy-activate-addon');
					$button.attr('rel', response.plugin);
					$message.text(soliloquy_addons.inactive);
					$el.removeClass('soliloquy-addon-not-installed').addClass('soliloquy-addon-inactive');
					$button.next().hide();
				},

				error: function (xhr, textStatus, e) {
					$button.next()
						.hide();
					return;
				}
			};
			$.ajax(opts);
		});

		// Function to clear any disabled buttons and extra text if the user needs to add creds but instead tries to install a different addon.
		function soliloquyAddonRefresh(element) {
			if ($(element).attr('disabled')) {
				$(element)
					.removeAttr('disabled');
			}
			if ($(element).parent().parent().hasClass('soliloquy-addon-not-installed')) {

				$(element).text(soliloquy_addons.install);

			}
		}
	});

})(jQuery, window, document, soliloquy_addons);