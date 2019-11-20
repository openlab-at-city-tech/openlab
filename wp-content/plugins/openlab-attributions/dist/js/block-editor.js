/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './data';
import './formats/';
import Metabox from './components/metabox';

// This should go into different file.
domReady( () => {
	render( <Metabox />, document.getElementById( 'attribution-box' ) );
} );
