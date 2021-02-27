/**
 * External dependencies
 */
import { Tooltip } from 'react-tippy';

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
		<Tooltip
		html={ text }
		arrow={ true }
		position="right"
		trigger="mouseenter"
		popperOptions={ popperOptions }
		>
			<Button icon="editor-help" className="component-help-button" />
		</Tooltip>
	);
};
