/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
import EpkbInspectorControls from "../components";

export default function GlossaryIndexBlockEdit({ attributes, setAttributes, name }) {

	// this should never happen except during development
	// and indicates a critical issue
	if (!epkb_glossary_index_block_ui_config) {
		return (
			<>
				<div>Unable to load all assets.</div>
			</>
		);
	}

	return (
		<EpkbInspectorControls
			block_ui_config={epkb_glossary_index_block_ui_config}
			attributes={attributes}
			setAttributes={setAttributes}
			blockName={name}
		/>
	);
}
