/**
 * Customizer custom controls scripts.
 *
 * WordPress customizer uses jQuery.
 * @requires  jQuery
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

( function( $ ) {
	'use strict';

	$( wp.customize ).ready( function() {

		// Navigate to error 404 page when control is focused.

			$( '[name="block_area_error_404"]' )
				.on( 'focusin', function() {
					let url = wp.customize.settings.url.home.replace( /\/+$/, '' );
					// "error-404-page" @ http://www.md5.cz/
					url += '/86a8820fe9ab96669c59a56f88d51b69';
					if ( url !== wp.customize.previewer.previewUrl() ) {
						wp.customize.previewer.previewUrl.set( url );
					}
				} );

		// Range inputs.

			$( 'input[type="range"]' )
				.wrap( '<div class="range-container" />' )
				.after( '<span class="range-value" />' )
				.on( 'input change', function() {

					const
						$this       = $( this ),
						value       = $this.val() * $this.data( 'multiply' ),
						valuePrefix = $this.data( 'prefix' ),
						valueSuffix = $this.data( 'suffix' );

					let decimals = $this.data( 'decimals' );

					if ( 1 > decimals ) {
						decimals = 1;
					} else {
						decimals = Math.pow( 10, decimals );
					}

					$this
						.next()
							.text( valuePrefix + Math.round( value * decimals ) / decimals + valueSuffix );

				} );

			$( '.range-value' )
				.each( function() {

					const
						$this       = $( this ),
						$inputField = $this.prev(),
						value       = $inputField.val() * $inputField.data( 'multiply' ),
						valuePrefix = $inputField.data( 'prefix' ),
						valueSuffix = $inputField.data( 'suffix' );

					let decimals = $inputField.data( 'decimals' );

					if ( 1 > decimals ) {
						decimals = 1;
					} else {
						decimals = Math.pow( 10, decimals );
					}

					$this
						.text( valuePrefix + Math.round( value * decimals ) / decimals + valueSuffix );

				} );

		// Background images.

			/**
			 * Background image control conditional hiding:
			 * ============================================
			 * If control with the ID ending with "_image" is found in theme options,
			 * we conditionally hide corresponding "_attachment", "_position",
			 * "_repeat", "_size" and also "_opacity" controls.
			 */

			let backgroundImages = [];

			// Get all image control under theme options.
			$.each( $( '.control-section-theme-options [id$="_image"]' ), function( i, o ) {
				backgroundImages.push( $( o ).attr( 'id' ).replace( 'customize-control-', '' ) );
			} );

			// Hide additional background image controls if no image set.
			$.each( backgroundImages, function( i, settingId ) {
				wp.customize( settingId, function( value ) {

					const selectors = [
						'[id$="' + settingId + '_attachment"]',
						'[id$="' + settingId + '_opacity"]',
						'[id$="' + settingId + '_position"]',
						'[id$="' + settingId + '_repeat"]',
						'[id$="' + settingId + '_size"]',
					];

					if ( ! _wpCustomizeSettings.settings[ settingId ].value ) {
						$( selectors.join() )
							.hide();
					}

					value
						.bind( function( to ) {
							if ( ! to ) {
								$( selectors.join() )
									.hide();
							} else {
								$( selectors.join() )
									.show();
							}
						} );

				} );
			} );

		// Multiple checkboxes.

			$( '.customize-control-multicheckbox' )
				.on( 'change', 'input[type="checkbox"]', function() {

					const
						$this   = $( this ),
						$values = $this
							.closest( '.customize-control' )
							.find( 'input[type="checkbox"]:checked' )
								.map( function() {
									return this.value;
								} )
								.get()
								.join( ',' );

					$this
						.closest( '.customize-control' )
						.find( 'input[type="hidden"]' )
							.val( $values )
							.trigger( 'change' );

				} );

	} );

} )( jQuery );
