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
	title: 'OpenLab Help',

	edit,

	/**
	 * Rendered in PHP.
	 */
	save: () => { return null }
} )
