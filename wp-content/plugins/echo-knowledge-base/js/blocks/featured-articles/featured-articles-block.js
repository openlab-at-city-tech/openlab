/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import FeaturedArticlesBlockEdit from './featured-articles-block-edit';
import FeaturedArticlesBlockSave from "./featured-articles-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/featured-articles',
	{
		icon: {
			src: (
				<svg
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
					id="Featured_Articles"
					data-name="Featured Articles"
				>
					<g>
						<circle cx="7.96" cy="42.96" r="7.96" />
						<rect x="19.06" y="35" width="38.77" height="15.92" />
						<circle cx="69.54" cy="42.96" r="7.96" />
						<rect x="80.64" y="35" width="38.77" height="15.92" />
					</g>
					<g>
						<circle cx="8.32" cy="68.34" r="7.96" />
						<rect x="19.41" y="60.38" width="38.77" height="15.92" />
						<circle cx="69.9" cy="68.34" r="7.96" />
						<rect x="81" y="60.38" width="38.77" height="15.92" />
					</g>
					<g>
						<circle cx="7.96" cy="93.13" r="7.96" />
						<rect x="19.06" y="85.17" width="38.77" height="15.92" />
						<circle cx="69.54" cy="93.13" r="7.96" />
						<rect x="80.64" y="85.17" width="38.77" height="15.92" />
					</g>
					<rect x="15.92" width="89.09" height="26.39" stroke="#000" strokeMiterlimit="10" />
				</svg>
			),
		},
		edit: FeaturedArticlesBlockEdit,
		save: FeaturedArticlesBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'featured-articles');
})(window.wp);
