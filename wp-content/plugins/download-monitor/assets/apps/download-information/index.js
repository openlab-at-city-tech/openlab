import { createRoot } from '@wordpress/element';
import App from './App';
import './style.scss';

document.addEventListener( 'DOMContentLoaded', () => {
	const mount = document.getElementById( 'dlm-download-information-app' );

	if ( ! mount ) {
		return;
	}

	createRoot( mount ).render( <App /> );
} );
