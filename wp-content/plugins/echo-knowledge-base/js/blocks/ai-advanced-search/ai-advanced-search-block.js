/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import AIAdvancedSearchBlockEdit from './ai-advanced-search-block-edit';
import AIAdvancedSearchBlockSave from "./ai-advanced-search-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/ai-advanced-search',
	{
		edit: AIAdvancedSearchBlockEdit,
		save: AIAdvancedSearchBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'ai-advanced-search');
})(window.wp);
