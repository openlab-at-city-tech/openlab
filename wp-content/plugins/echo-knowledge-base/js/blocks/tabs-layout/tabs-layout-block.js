/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import TabsLayoutBlockEdit from './tabs-layout-block-edit';
import TabsLayoutBlockSave from "./tabs-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/tabs-layout',
	{
		icon: {
			src: (
				<svg
					id="Tab_Layout_2"
					data-name="Tab Layout 2"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
				>
					<polygon
						points="105.89 30.62 105.89 98.69 122.5 98.69 122.5 88.93 113.26 88.93 113.26 35.24 172.05 35.42 172.05 88.93 164.57 88.93 164.7 98.69 179.55 98.69 179.55 30.62 105.89 30.62"
						style={{ stroke: '#000', strokeMiterlimit: 10 }}
					/>
					<polygon
						points="-58.2 30.62 -58.2 98.69 -41.6 98.69 -41.6 88.93 -50.84 88.93 -50.84 35.24 7.96 35.42 7.96 88.93 .48 88.93 .61 98.69 15.46 98.69 15.46 30.62 -58.2 30.62"
						style={{ stroke: '#000', strokeMiterlimit: 10 }}
					/>
					<rect
						x="34.97"
						y="72.4"
						width="51.41"
						height="8.67"
						style={{ stroke: '#000', strokeMiterlimit: 10 }}
					/>
					<g>
						<path
							d="M31.21,88.93h1.13v-.02h51.41v.02h6.26v-53.51l-58.8-.18v53.69ZM34.97,49.42h51.41v8.67h-51.41v-8.67Z"
							style={{ fill: 'none', strokeWidth: 0 }}
						/>
						<rect
							x="34.97"
							y="49.42"
							width="51.41"
							height="8.67"
							style={{ strokeWidth: 0 }}
						/>
						<path
							d="M23.85,30.62v68.07h73.66V30.62H23.85ZM90.01,88.93h-6.26v-.02h-51.41v.02h-1.13v-53.69l58.8.18v53.51Z"
							style={{ strokeWidth: 0 }}
						/>
					</g>
					<rect
						x=".52"
						y=".57"
						width="56.02"
						height="20.11"
						style={{ stroke: '#000', strokeMiterlimit: 10 }}
					/>
					<polygon
						points="29.54 28.7 15.14 16.04 28.25 4.53 42.66 17.18 29.54 28.7"
						style={{ stroke: '#000', strokeMiterlimit: 10 }}
					/>
					<rect
						x="62.86"
						y=".57"
						width="56.02"
						height="20.11"
						style={{ stroke: '#000', strokeMiterlimit: 10 }}
					/>
				</svg>
			),
		},
		edit: TabsLayoutBlockEdit,
		save: TabsLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'tabs-layout');
})(window.wp);
