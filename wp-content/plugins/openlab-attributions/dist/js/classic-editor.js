/* global tinymce */
const licenses = Object.values( window.attrLicenses );

/**
 * Internal dependencies
 */
import formatAttribution from './utils/format-attribution';
import formTmpl from './utils/form-template';
import tinyIcon from './utils/tiny-icon';

const getFormData = ( form ) => {
	const data = Array
		.from( form.elements )
		.map( ( input ) => {
			return { [ input.name ]: input.value };
		} );

	return data;
};

const addShortcode = ( editor, value ) => {
	const form = document.getElementById( 'attribution-builder' );

	// Get format data.
	const data = getFormData( form );

	// Convert form data array into object and pass for formatting.
	const attribution = formatAttribution( Object.assign( ...data ), licenses );
	const shortcode = `[ref]${ attribution }[/ref]`;
	const newValue = value.concat( ' ', shortcode );

	editor.execCommand( 'mceInsertContent', false, newValue );
};

const renderPreview = () => {
	const form = document.getElementById( 'attribution-builder' );
	const preview = document.getElementById( 'attribution-preview' );
	const data = getFormData( form );

	const attribution = formatAttribution( Object.assign( ...data ), licenses );

	preview.innerHTML = attribution;
};

tinymce.create( 'tinymce.plugins.Attributions', {
	init( editor ) {
		editor.addButton( 'olAttrButton', {
			title: 'Add Attribution',
			cmd: 'olAttrButtonCmd',
			icon: 'attribution',
			onPostRender: () => {
				const icon = document.getElementsByClassName( 'mce-i-attribution' );
				icon[ 0 ].innerHTML = tinyIcon;
			},
		} );

		editor.addCommand( 'olAttrButtonCmd', function() {
			const value = editor.selection.getContent( { format: 'html' } );
			const selected = editor.selection.getContent( { format: 'text' } );

			const modal = editor.windowManager.open( {
				title: 'Add Attribution',
				body: {
					type: 'container',
					classes: 'ol-attributions-modal',
					minWidth: 750,
					minHeight: 500,
					html: formTmpl( selected, licenses ),
					onPostRender: () => {
						const form = document.getElementById( 'attribution-builder' );
						form.addEventListener( 'blur', renderPreview, true );
					},
				},
				buttons: [
					{
						text: 'Cancel',
						onclick: () => modal.close(),
					},
					{
						text: 'Insert',
						subtype: 'primary',
						onclick: () => modal.submit(),
					},
				],
				onsubmit: () => {
					addShortcode( editor, value );
				},
			} );
		} );
	},
} );

tinymce.PluginManager.add( 'olAttrButton', tinymce.plugins.Attributions );
