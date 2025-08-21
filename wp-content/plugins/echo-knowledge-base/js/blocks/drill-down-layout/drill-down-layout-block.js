/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import DrillDownLayoutBlockEdit from './drill-down-layout-block-edit';
import DrillDownLayoutBlockSave from "./drill-down-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/drill-down-layout',
	{
		icon: {
			src: (
				<svg
					id="Drill_Down_2"
					data-name="Drill Down 2"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
				>
					<polygon
						points="105.89 36.34 105.89 104.41 122.5 104.41 122.5 94.65 113.26 94.65 113.26 40.96 172.05 41.14 172.05 94.65 164.57 94.65 164.7 104.41 179.55 104.41 179.55 36.34 105.89 36.34"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<polygon
						points="-58.2 36.34 -58.2 104.41 -41.6 104.41 -41.6 94.65 -50.84 94.65 -50.84 40.96 7.96 41.14 7.96 94.65 .48 94.65 .61 104.41 15.46 104.41 15.46 36.34 -58.2 36.34"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<rect
						x="34.97"
						y="78.11"
						width="51.41"
						height="8.67"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<g>
						<path
							d="M31.21,94.65h1.13v-.02h51.41v.02h6.26v-53.51l-58.8-.18v53.69ZM34.97,55.13h51.41v8.67h-51.41v-8.67Z"
							style={{ fill: "none", strokeWidth: 0 }}
						/>
						<rect
							x="34.97"
							y="55.13"
							width="51.41"
							height="8.67"
							style={{ strokeWidth: 0 }}
						/>
						<path
							d="M23.85,36.34v68.07h73.66V36.34H23.85ZM90.01,94.65h-6.26v-.02h-51.41v.02h-1.13v-53.69l58.8.18v53.51Z"
							style={{ strokeWidth: 0 }}
						/>
					</g>
					<path
						d="M.5,0v32.95h118.92V0H.5ZM59.96,30.48c-7.73,0-14-6.27-14-14s6.27-14,14-14,14,6.27,14,14-6.27,14-14,14Z"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
				</svg>


			),
		},
		edit: DrillDownLayoutBlockEdit,
		save: DrillDownLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'drill-down-layout');
})(window.wp);
