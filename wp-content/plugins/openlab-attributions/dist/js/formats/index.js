/**
 * WordPress dependencies
 */
import { registerFormatType } from '@wordpress/rich-text';

/**
 * Internal dependencies
 */
import Edit from './edit';

// Register fake format.
registerFormatType( 'ol/attributions', {
	title: 'Attribution',
	tagName: 'a',
	className: 'attribution-anchor',
	edit: Edit,
} );
