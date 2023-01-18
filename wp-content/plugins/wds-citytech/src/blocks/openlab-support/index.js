import { registerBlockType } from '@wordpress/blocks'

/**
 * Internal dependencies
 */
import edit from './edit'
import metadata from './block.json'

/**
 * Block definition.
 */
registerBlockType( metadata, {
	title: 'OpenLab Support',

	edit,

	/**
	 * Rendered in PHP.
	 */
	save: () => { return null }
} )
