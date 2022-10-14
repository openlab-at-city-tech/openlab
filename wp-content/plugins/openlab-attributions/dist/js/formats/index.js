/**
 * WordPress dependencies
 */
import { registerFormatType } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Edit from './edit';

// Register fake format.
registerFormatType( 'ol/attributions', {
	title: __( 'Attribution', 'openlab-attributions' ),
	tagName: 'span',
	className: 'attribution-anchor',
	edit: Edit,
} );
