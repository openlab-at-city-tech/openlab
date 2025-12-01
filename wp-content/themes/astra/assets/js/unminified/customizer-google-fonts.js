/**
 * Customizer Google Fonts AJAX Loader
 *
 * @package Astra
 * @since 4.11.13
 */

(function ($) {
	'use strict';

	/**
	 * Re-initialize font weights for all typography controls
	 */
	function reinitializeFontWeights() {
		if (typeof wp === 'undefined' || typeof wp.customize === 'undefined' || typeof AstTypography === 'undefined') {
			return;
		}

		$('.customize-control-ast-font-family select').each(function () {
			var link = $(this).data('customize-setting-link');
			var weight = $(this).data('connected-control');

			if (weight && link && wp.customize(link)) {
				AstTypography._setFontWeightOptions.apply(wp.customize(link), [true]);
			}
		});
	}

	/**
	 * Load Google Fonts via AJAX
	 */
	function loadGoogleFonts() {
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'astra_load_google_fonts',
				nonce: astraCustomizer.customizer_nonce
			},
			success: function (response) {
				if (response.success && response.data) {
					var fontData = response.data.data || response.data;

					AstFontFamilies.google = fontData.google || {};
					AstFontFamilies.custom = fontData.custom || AstFontFamilies.custom || {};
					AstFontFamilies.googleLoaded = true;

					// Update React component's font data
					if (typeof window.AstraBuilderCustomizerData !== 'undefined') {
						window.AstraBuilderCustomizerData.googleFonts = fontData.google || {};
					}

					$(document).trigger('astraGoogleFontsLoaded', [AstFontFamilies]);
					reinitializeFontWeights();
				}
			},
			error: function () {
				// Fallback: try once more with $.post
				$.post(ajaxurl, {
					action: 'astra_load_google_fonts',
					nonce: astraCustomizer.customizer_nonce
				}, function (response) {
					if (response.success && response.data) {
						var fontData = response.data.data || response.data;

						AstFontFamilies.google = fontData.google || {};
						AstFontFamilies.custom = fontData.custom || AstFontFamilies.custom || {};
						AstFontFamilies.googleLoaded = true;

						// Update React component's font data
						if (typeof window.AstraBuilderCustomizerData !== 'undefined') {
							window.AstraBuilderCustomizerData.googleFonts = fontData.google || {};
						}

						$(document).trigger('astraGoogleFontsLoaded', [AstFontFamilies]);
						reinitializeFontWeights();
					}
				});
			}
		});
	}

	// Initialize when customizer is ready
	wp.customize.bind('ready', function () {
		if (typeof AstFontFamilies !== 'undefined' && AstFontFamilies.googleLoaded === false) {
			setTimeout(loadGoogleFonts, 100);
		}
	});

})(jQuery);