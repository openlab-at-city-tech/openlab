/**
 * JavaScript code for the "Default Style Customizer Screen" table style variations.
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

const styleVariations = {
	'default': {
		name: __( 'Blue', 'tablepress' ),
		style: {
			'--style-variation': 'default',
			'--text-color': '#111111',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#d9edf7',
			'--head-active-bg-color': '#049cdb',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#f9f9f9',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#ffffff',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#f3f3f3',
			'--border-color': '#dddddd',
			'--padding': '0.5rem',
		},
	},
	'red': {
		name: __( 'Red', 'tablepress' ),
		style: {
			'--style-variation': 'red',
			'--text-color': '#111111',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#fbaeae',
			'--head-active-bg-color': '#dd0000',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#fbe7e7',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#ffffff',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#f6a1a1',
			'--border-color': '#dddddd',
			'--padding': '0.5rem',
		},
	},
	'green': {
		name: __( 'Green', 'tablepress' ),
		style: {
			'--style-variation': 'green',
			'--text-color': '#111111',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#c9f3ca',
			'--head-active-bg-color': '#0cad0c',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#f2f7f2',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#ffffff',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#beeab8',
			'--border-color': '#dddddd',
			'--padding': '0.5rem',
		},
	},
	'yellow': {
		name: __( 'Yellow', 'tablepress' ),
		style: {
			'--style-variation': 'yellow',
			'--text-color': '#111111',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#fff3cd',
			'--head-active-bg-color': '#f5d772',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#fffcf3',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#ffffff',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#fff1bf',
			'--border-color': '#e6dbb9',
			'--padding': '0.5rem',
		},
	},
	'purple': {
		name: __( 'Purple', 'tablepress' ),
		style: {
			'--style-variation': 'purple',
			'--text-color': '#111111',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#fde6fd',
			'--head-active-bg-color': '#9370db',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#fff5ff',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#ffffff',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#e0b0ff',
			'--border-color': '#dddddd',
			'--padding': '0.5rem',
		},
	},
	'dark': {
		name: __( 'Dark', 'tablepress' ),
		style: {
			'--style-variation': 'dark',
			'--text-color': '#ffffff',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#101010',
			'--head-active-bg-color': '#000000',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#202020',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#303030',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#404040',
			'--border-color': '#404040',
			'--padding': '0.5rem',
		},
	},
	'light': {
		name: __( 'Light', 'tablepress' ),
		style: {
			'--style-variation': 'light',
			'--text-color': '#111111',
			'--head-text-color': 'var(--text-color)',
			'--head-active-text-color': 'var(--head-text-color)',
			'--head-bg-color': '#f6f6f6',
			'--head-active-bg-color': '#e6e6e6',
			'--odd-text-color': 'var(--text-color)',
			'--odd-bg-color': '#fcfcfc',
			'--even-text-color': 'var(--text-color)',
			'--even-bg-color': '#ffffff',
			'--hover-text-color': 'var(--text-color)',
			'--hover-bg-color': '#f0f0f0',
			'--border-color': '#dddddd',
			'--padding': '0.5rem',
		},
	},
};

export default styleVariations;
