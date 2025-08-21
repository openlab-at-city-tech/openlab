/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import BasicLayoutBlockEdit from './basic-layout-block-edit';
import BasicLayoutBlockSave from "./basic-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/basic-layout',
	{
		icon: {
			src: (
				<svg
					id="Basic_Layout_2"
					data-name="Basic Layout 2"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
				>
					<polygon
						points="105.89 3.61 105.89 98.69 122.5 98.69 122.5 92.33 113.26 92.32 113.26 10.07 172.05 10.32 172.05 85.06 164.57 85.06 164.7 98.69 179.55 98.69 179.55 3.61 105.89 3.61"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<polygon
						points="-58.2 3.61 -58.2 98.69 -41.6 98.69 -41.6 85.06 -50.84 85.06 -50.84 10.07 7.96 10.32 7.96 92.35 .48 92.32 .49 98.69 15.46 98.69 15.46 3.61 -58.2 3.61"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<rect
						x="34.97"
						y="61.96"
						width="51.41"
						height="12.12"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<g>
						<path
							d="M31.21,85.06h1.13v-.02h51.41v.02h6.26V10.32l-58.8-.25v74.99ZM34.97,29.87h51.41v12.12h-51.41v-12.12Z"
							style={{ fill: "none", strokeWidth: 0 }}
						/>
						<rect
							x="34.97"
							y="29.87"
							width="51.41"
							height="12.12"
							style={{ strokeWidth: 0 }}
						/>
						<path
							d="M23.85,3.61v95.08h73.66V3.61H23.85ZM90.01,91.86H31.21V10.07l58.8.27v81.52Z"
							style={{ strokeWidth: 0 }}
						/>
					</g>
				</svg>
			),
		},
		edit: BasicLayoutBlockEdit,
		save: BasicLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'basic-layout');
})(window.wp);
