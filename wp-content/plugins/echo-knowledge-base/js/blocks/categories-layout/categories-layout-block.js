/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import CategoriesLayoutBlockEdit from './categories-layout-block-edit';
import CategoriesLayoutBlockSave from "./categories-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/categories-layout',
	{
		icon: {
			src: (
				<svg
					id="Category_2"
					data-name="Category 2"
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
					<polygon
						points="31.21 91.86 32.34 91.86 32.36 91.86 83.75 91.86 90.01 91.86 90.01 41.98 31.21 41.98 31.21 91.86"
						style={{ fill: "none", strokeWidth: "0px" }}
					/>
					<rect
						x="-49.63"
						y="8.73"
						width="58.8"
						height="33.25"
						style={{ strokeWidth: "0px" }}
					/>
					<rect
						x="113.32"
						y="8.73"
						width="58.8"
						height="33.25"
						style={{ strokeWidth: "0px" }}
					/>
					<g>
						<polygon
							points="31.21 91.86 32.34 91.86 32.36 91.86 83.75 91.86 90.01 91.86 90.01 41.98 31.21 41.98 31.21 91.86"
							style={{ fill: "none", strokeWidth: "0px" }}
						/>
						<path
							d="M23.85,3.61v95.08h73.66V3.61H23.85ZM90.01,91.86H31.21v-49.88h58.8v49.88ZM81.6,34.53c-6.49,0-11.75-5.26-11.75-11.75s5.26-11.75,11.75-11.75,11.75,5.26,11.75,11.75-5.26,11.75-11.75,11.75Z"
							style={{ strokeWidth: "0px" }}
						/>
					</g>
					<path
						d="M80.93,15.53h-.06l-3.39,1.83-.51-2.01,4.26-2.28h2.25v19.5h-2.55V15.53Z"
						style={{ strokeWidth: "0px" }}
					/>
				</svg>
			),
		},
		edit: CategoriesLayoutBlockEdit,
		save: CategoriesLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'categories-layout');
})(window.wp);
