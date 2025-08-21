/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import SidebarLayoutBlockEdit from './sidebar-layout-block-edit';
import SidebarLayoutBlockSave from "./sidebar-layout-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/sidebar-layout',
	{
		icon: {
			src: (
				<svg id="Sidbar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 119.41 101.85">
					<g>
						<path
							d="M6.16,87.25h.94v-.03h43.01v.03h5.24V7.18l-49.19-.26v80.33ZM9.3,28.12h43.01v12.98H9.3v-12.98Z"
							style={{ fill: "none", strokeWidth: "0px" }}
						/>
						<rect
							x="9.25"
							y="15.87"
							width="43.01"
							height="12.98"
							style={{ strokeWidth: "0px" }}
						/>
						<path
							d="M0,0v101.85h61.62V0H0ZM55.35,94.53H6.16V6.92l49.19.29v87.33Z"
							style={{ strokeWidth: "0px" }}
						/>
					</g>
					<rect x="9.3" y="44.24" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
					<rect x="9.3" y="72.31" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
					<rect x="69.68" y="4.22" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
					<rect x="69.68" y="22.36" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
					<rect x="69.68" y="40.51" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
					<rect x="69.68" y="58.22" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
					<rect x="69.68" y="76.53" width="43.01" height="12.98" style={{ strokeWidth: "0px" }} />
				</svg>

			),
		},
		edit: SidebarLayoutBlockEdit,
		save: SidebarLayoutBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'sidebar-layout');
})(window.wp);
