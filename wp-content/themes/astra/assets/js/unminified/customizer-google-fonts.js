/**
 * Customizer Google Fonts AJAX Loader
 *
 * @package Astra
 * @since 4.11.13
 */

(function($) {
	'use strict';

	/**
	 * Initialize Google Fonts AJAX loading
	 */
	function initGoogleFontsLoader() {
		if (typeof AstFontFamilies !== 'undefined' && AstFontFamilies.googleLoaded === false) {
			loadGoogleFonts();
		}
	}

	/**
	 * Load Google Fonts via AJAX
	 */
	function loadGoogleFonts() {
		let data = {
			action: 'astra_load_google_fonts',
			nonce: astraCustomizer.customizer_nonce
		};

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
			dataType: 'json',
			success: function(response) {
				if (response.success && response.data) {
					if (typeof AstFontFamilies !== 'undefined') {
						AstFontFamilies.google = response.data.google || {};
						AstFontFamilies.custom = response.data.custom || AstFontFamilies.custom || {};
						AstFontFamilies.googleLoaded = true;

						$(document).trigger('astraGoogleFontsLoaded', [AstFontFamilies]);
					}
				}
			},
			error: function(xhr, status, error) {
				console.warn('Astra: Failed to load Google Fonts via AJAX:', error);
				
				loadGoogleFontsFallback();
			}
		});
	}

	/**
	 * Fallback method to load Google Fonts synchronously
	 */
	function loadGoogleFontsFallback() {
		let data = {
			action: 'astra_load_google_fonts',
			nonce: astraCustomizer.customizer_nonce
		};

		$.post(ajaxurl, data, function(response) {
			if (response.success && response.data) {
				if (typeof AstFontFamilies !== 'undefined') {
					AstFontFamilies.google = response.data.google || {};
					AstFontFamilies.custom = response.data.custom || AstFontFamilies.custom || {};
					AstFontFamilies.googleLoaded = true;

					$(document).trigger('astraGoogleFontsLoaded', [AstFontFamilies]);
				}
			}
		}).fail(function() {
			console.error('Astra: Google Fonts fallback loading also failed');
		});
	}

	/**
	 * Get Google Font data with loading support
	 * This replaces direct access to AstFontFamilies.google
	 */
	window.astraGetGoogleFonts = function(callback) {
		if (typeof AstFontFamilies !== 'undefined') {
			if (AstFontFamilies.googleLoaded) {
				// Fonts already loaded, return immediately
				if (callback && typeof callback === 'function') {
					callback(AstFontFamilies.google);
				}
				return AstFontFamilies.google;
			} else {
				// Fonts not loaded, wait for them
				$(document).on('astraGoogleFontsLoaded', function(event, fontFamilies) {
					if (callback && typeof callback === 'function') {
						callback(fontFamilies.google);
					}
				});
				return {};
			}
		}
		return {};
	};

	// Initialize when customizer is ready
	wp.customize.bind('ready', function() {
		setTimeout(initGoogleFontsLoader, 100);
	});

	$(document).ready(function() {
		if (typeof wp === 'undefined' || typeof wp.customize === 'undefined') {
			setTimeout(initGoogleFontsLoader, 500);
		}
	});

})(jQuery);