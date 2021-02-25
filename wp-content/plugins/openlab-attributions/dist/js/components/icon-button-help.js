/**
 * External dependencies
 */
import { Tooltip } from 'react-tippy';

/**
 * WordPress dependencies
 */
import { Button, Dashicon } from '@wordpress/components';

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

const IconButtonHelp = ( { text = '' } ) => (
	<Tooltip
		html={ text }
		arrow={ true }
		position="right"
		trigger="mouseenter"
		popperOptions={ popperOptions }
	>
		<Button className="component-help-button">
			<Dashicon icon="editor-help" />
		</Button>
	</Tooltip>
);

export default IconButtonHelp;
