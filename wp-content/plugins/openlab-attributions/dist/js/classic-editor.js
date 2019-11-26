/**
 * External dependencies
 */
const nanoid = require( 'nanoid' );

/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { dispatch } from '@wordpress/data';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './data';
import Metabox from './components/metabox';
import tinyIcon from './utils/tiny-icon';

const tinymce = window.tinymce;

const addMarker = ( editor, value, data ) => {
	const id = nanoid( 8 );
	const item = { ...data, id };

	// Create marker element.
	const marker = document.createElement( 'a' );
	marker.setAttribute( 'href', `#ref-${ id }` );
	marker.setAttribute( 'id', `anchor-${ id }` );
	marker.setAttribute( 'class', 'attribution-anchor' );

	// Add attribution.
	dispatch( 'openlab/attributions' ).add( item );

	const newValue = value.concat( '', marker.outerHTML );

	editor.execCommand( 'mceInsertContent', false, newValue );
};

tinymce.create( 'tinymce.plugins.Attributions', {
	init( editor ) {
		editor.addButton( 'attribution-button', {
			title: 'Add Attribution',
			cmd: 'add-attribution',
			icon: 'attribution',
			onPostRender: () => {
				const icon = document.getElementsByClassName( 'mce-i-attribution' );
				icon[ 0 ].innerHTML = tinyIcon;
			},
		} );

		editor.addCommand( 'add-attribution', function() {
			const value = editor.selection.getContent( { format: 'html' } );

			dispatch( 'openlab/modal' ).open( {
				item: {},
				modalType: 'add',
				addItem: ( data ) => addMarker( editor, value, data ),
			} );
		} );
	},
} );

tinymce.PluginManager.add( 'attribution-button', tinymce.plugins.Attributions );

domReady( () => {
	render( <Metabox />, document.getElementById( 'attribution-box' ) );
} );
