/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import FaqsBlockEdit from './faqs-block-edit';
import FaqsBlockSave from "./faqs-block-save";
import { unregister_block_for_non_page } from '../utils';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
	'echo-knowledge-base/faqs',
	{
		icon: {
			src: (
				<svg
					id="FAQs_2"
					data-name="FAQs 2"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 119.41 101.85"
				>
					<g>
						<path
							d="M0,0v27.6h119.41V0H0ZM14.42,24.84c-6.29,0-11.39-4.94-11.39-11.04S8.13,2.76,14.42,2.76s11.39,4.94,11.39,11.04-5.1,11.04-11.39,11.04ZM112.52,20.06H38.6V6.88h73.92v13.17Z"
							style={{ strokeWidth: 0 }}
						/>
						<rect
							x="32.42"
							y="31.89"
							width="86.95"
							height="15.19"
							style={{ strokeWidth: 0 }}
						/>
						<ellipse
							cx="21.89"
							cy="39.49"
							rx="8.21"
							ry="7.95"
							style={{ strokeWidth: 0 }}
						/>
					</g>
					<g>
						<path
							d="M0,53.41v27.6h119.41v-27.6H0ZM14.42,78.25c-6.29,0-11.39-4.94-11.39-11.04s5.1-11.04,11.39-11.04,11.39,4.94,11.39,11.04-5.1,11.04-11.39,11.04ZM112.52,73.47H38.6v-13.17h73.92v13.17Z"
							style={{ strokeWidth: 0 }}
						/>
						<rect
							x="32.42"
							y="85.3"
							width="86.95"
							height="15.19"
							style={{ strokeWidth: 0 }}
						/>
						<ellipse
							cx="21.89"
							cy="92.9"
							rx="8.21"
							ry="7.95"
							style={{ strokeWidth: 0 }}
						/>
					</g>
				</svg>

			),
		},
		edit: FaqsBlockEdit,
		save: FaqsBlockSave,
	}
);

// Unregister block if not 'page' post type
(function(wp) {
	unregister_block_for_non_page(wp, 'faqs');
})(window.wp);
