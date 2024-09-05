/**
 * JavaScript code for the "Default Style Customizer Screen" custom CSS properties.
 *
 * @package TablePress
 * @subpackage Default Style Customizer Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';

const cssProperties = {
	'--style-variation': {
		name: '',
		type: '',
	},
	'--text-color': {
		name: __( 'Cell content', 'tablepress' ),
		type: 'color',
		category: 'text',
	},
	'--head-text-color': {
		name: __( 'Head row', 'tablepress' ),
		type: 'color',
		category: 'text',
	},
	'--head-active-text-color': {
		name: __( 'Active head cells', 'tablepress' ),
		type: 'color',
		category: 'text',
	},
	'--head-bg-color': {
		name: __( 'Head row', 'tablepress' ),
		type: 'color',
		category: 'background',
	},
	'--head-active-bg-color': {
		name: __( 'Active head cells', 'tablepress' ),
		type: 'color',
		category: 'background',
	},
	'--odd-text-color': {
		name: __( 'Odd rows', 'tablepress' ),
		type: 'color',
		category: 'text',
	},
	'--odd-bg-color': {
		name: __( 'Odd rows', 'tablepress' ),
		type: 'color',
		category: 'background',
	},
	'--even-text-color': {
		name: __( 'Even rows', 'tablepress' ),
		type: 'color',
		category: 'text',
	},
	'--even-bg-color': {
		name: __( 'Even rows', 'tablepress' ),
		type: 'color',
		category: 'background',
	},
	'--hover-text-color': {
		name: __( 'Hovered row', 'tablepress' ),
		type: 'color',
		category: 'text',
	},
	'--hover-bg-color': {
		name: __( 'Hovered row', 'tablepress' ),
		type: 'color',
		category: 'background',
	},
	'--border-color': {
		name: __( 'Border color', 'tablepress' ),
		type: 'color',
		category: 'border',
	},
	'--padding'     : {
		name: __( 'Cell padding', 'tablepress' ),
		type: 'length',
		category: 'spacing',
	},

};

export default cssProperties;
