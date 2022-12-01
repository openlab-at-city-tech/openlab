/**
 * Block modifications.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.3.0
 * @version  1.3.8
 */

( function() {
	'use strict';

	// Modify block supports.
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'michelle/block-mods',
		function( settings, name ) {

			// Processing

				switch( name ) {

					case 'core/column':
					case 'core/columns':
						// See also `includes/Content/Block.php/Block:render_block()`
						settings = lodash.merge( {}, settings, {
							supports: {
								__experimentalLayout: false
							},
						} );
						break;

					case 'core/cover':
						// https://github.com/WordPress/gutenberg/issues/33723
						settings = lodash.merge( {}, settings, {
							supports: {
								color: { text: true }
							},
						} );
						break;

					case 'core/quote':
						settings = lodash.merge( {}, settings, {
							supports: {
								align: [ 'wide', 'full' ],
							},
						} );
						break;

					case 'core/separator':
						settings = lodash.merge( {}, settings, {
							supports: {
								align: [ 'wide', 'full' ],
							},
						} );
						break;
				}


			// Output

				return settings;

		},
		// Need to use low priority here so WordPress can hook with default
		// priority of 10 to add required `attributes` for us.
		5
	);

} )();
