/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
import showInspectorControls from '../components';

export default function TabsLayoutBlockEdit({ attributes, setAttributes, name }) {

	// this should never happen except during development
	// and indicates a critical issue
	if (!epkb_tabs_layout_block_ui_config) {
		return (
			<>
				<div>Unable to load all assets.</div>
			</>
		);
	}

	return showInspectorControls(epkb_tabs_layout_block_ui_config, attributes, setAttributes, name);
}
