/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import GridLayoutBlockEdit from './grid-layout-block-edit';
import GridLayoutBlockSave from "./grid-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/grid-layout',
	{
		icon: {
			src: (
				<svg
					id="Grid_Layout"
					data-name="Grid Layout"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
				>
					<g>
						<rect
							x="36.22"
							y="52.01"
							width="51.41"
							height="4.55"
							style={{ stroke: "#000", strokeMiterlimit: 10 }}
						/>
						<circle
							cx="60.86"
							cy="29"
							r="19.01"
							style={{ stroke: "#000", strokeMiterlimit: 10 }}
						/>
						<rect
							x="32.97"
							y="62.61"
							width="57.82"
							height="4.87"
							style={{ fill: "none", strokeWidth: 0 }}
						/>
						<rect
							x="1.45"
							y="64.12"
							width="7.77"
							height="3.33"
							style={{ fill: "none", strokeWidth: 0 }}
						/>
						<path
							d="M25.11-.04v75.3h73.66V-.04H25.11ZM91.28,64.47h0v3.02h-58.79v-3.02s0,0,0,0V5.07l58.8.19v59.2Z"
							style={{ strokeWidth: 0 }}
						/>
						<polygon
							points="119.41 67.48 114.51 67.48 114.51 63.92 114.51 63.92 114.51 5.09 119.41 5.1 119.41 -.03 107.15 -.03 107.15 75.27 119.41 75.27 119.41 67.48"
							style={{ strokeWidth: 0 }}
						/>
						<polygon
							points="-.06 -.07 -.06 5.13 4.84 5.13 4.84 7.72 4.84 8.17 4.84 11.28 4.84 11.28 4.84 67.46 -.06 67.46 -.06 70.1 -.06 71.03 -.06 75.23 12.21 75.23 12.21 -.07 -.06 -.07"
							style={{ strokeWidth: 0 }}
						/>
					</g>
					<circle
						cx="60.86"
						cy="115.96"
						r="19.01"
						style={{ stroke: "#000", strokeMiterlimit: 10 }}
					/>
					<path
						d="M25.11,86.92v75.3h73.66v-75.3H25.11ZM91.28,151.42h0v3.02h-58.79v-3.02s0,0,0,0v-59.39l58.8.19v59.2Z"
						style={{ strokeWidth: 0 }}
					/>
					<polygon
						points="119.41 154.44 114.51 154.44 114.51 150.88 114.51 150.88 114.51 92.05 119.41 92.06 119.41 86.93 107.15 86.93 107.15 162.23 119.41 162.23 119.41 154.44"
						style={{ strokeWidth: 0 }}
					/>
					<polygon
						points="-.06 86.89 -.06 92.09 4.84 92.09 4.84 94.68 4.84 95.13 4.84 98.24 4.84 98.24 4.84 154.41 -.06 154.41 -.06 157.06 -.06 157.99 -.06 162.19 12.21 162.19 12.21 86.89 -.06 86.89"
						style={{ strokeWidth: 0 }}
					/>
				</svg>



			),
		},
		edit: GridLayoutBlockEdit,
		save: GridLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'grid-layout');
})(window.wp);
