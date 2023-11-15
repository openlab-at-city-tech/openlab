/**
 * External dependencies
 */
import Tippy from '@tippyjs/react';

/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

/**
 * Override `z-index`.
 * Gutenberg Modal uses `z-index: 100000`.
 */
const popperOptions = {
	modifiers: {
		addZIndex: {
			enabled: true,
			order: 810,
			fn: ( data ) => ( {
				...data,
				styles: { ...data.styles, zIndex: 100001 },
			} ),
		},
	},
};

export default function Help( { text = '' } ) {
	return (
		<Tippy
			content={ text }
			popperOptions={ popperOptions }
		>
			<span className="component-help-button">
				<span className="dashicons dashicons-editor-help"></span>
			</span>
		</Tippy>
	);
}
