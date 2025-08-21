/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import ClassicLayoutBlockEdit from './classic-layout-block-edit';
import ClassicLayoutBlockSave from "./classic-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/classic-layout',
	{
		icon: {
			src: (
				<svg
					id="Classic_Layout_copy"
					data-name="Classic Layout copy"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
				>
					<g>
						<rect
							x="34.97"
							y="24.8"
							width="51.41"
							height="4.55"
							style={{ stroke: "#000", strokeMiterlimit: 10 }}
						/>
						<path
							d="M61.05,48.49c-14.11,0-25.55,11.56-25.55,25.82s11.44,25.82,25.55,25.82,25.55-11.56,25.55-25.82-11.44-25.82-25.55-25.82ZM79.03,79.54h-12.81v12.94h-10.35v-12.94h-12.81v-10.46h12.81v-12.94h10.35v12.94h12.81v10.46Z"
							style={{ stroke: "#000", strokeMiterlimit: 10 }}
						/>
						<polygon
							points="23.85 4 23.85 79.31 40.45 79.31 40.45 68.51 31.21 68.51 31.21 9.12 90.01 9.31 90.01 68.51 82.53 68.51 82.66 79.31 97.5 79.31 97.5 4 23.85 4"
							style={{ stroke: "#000", strokeMiterlimit: 10 }}
						/>
					</g>
					<polygon
						points="105.89 4 105.89 79.31 122.5 79.31 122.5 68.51 113.26 68.51 113.26 9.12 172.05 9.31 172.05 68.51 164.57 68.51 164.7 79.31 179.55 79.31 179.55 4 105.89 4"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<polygon
						points="-58.2 4 -58.2 79.31 -41.6 79.31 -41.6 68.51 -50.84 68.51 -50.84 9.12 7.96 9.31 7.96 68.51 .48 68.51 .61 79.31 15.46 79.31 15.46 4 -58.2 4"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
				</svg>




			),
		},
		edit: ClassicLayoutBlockEdit,
		save: ClassicLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'classic-layout');
})(window.wp);
