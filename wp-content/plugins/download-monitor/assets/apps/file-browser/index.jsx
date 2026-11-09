import { createRoot, useState } from '@wordpress/element';
import FileBrowserModal from './FileBrowserModal';
import './index.css';

let _setState = null;

function FileBrowserApp() {
	const [ state, setState ] = useState( { open: false, target: null } );
	_setState = setState;

	if ( ! state.open ) {
		return null;
	}

	const handleSelect = ( path ) => {
		const $target = window.jQuery( state.target );
		const existing = $target.val().trim();
		$target.val( existing ? existing + '\n' + path : path );
		setState( { open: false, target: null } );
	};

	const handleClose = () => {
		setState( { open: false, target: null } );
	};

	return (
		<FileBrowserModal onClose={ handleClose } onSelect={ handleSelect } />
	);
}

document.addEventListener( 'DOMContentLoaded', () => {
	const root = document.getElementById( 'dlm-file-browser-root' );
	if ( ! root ) {
		return;
	}
	createRoot( root ).render( <FileBrowserApp /> );
} );

window.dlmOpenFileBrowser = ( targetTextarea ) => {
	if ( _setState ) {
		_setState( { open: true, target: targetTextarea } );
	}
};
