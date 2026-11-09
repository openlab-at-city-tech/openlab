import { createRoot } from '@wordpress/element';
import Header from '../shared-components/header';

document.addEventListener( 'DOMContentLoaded', () => {
	const mount = document.getElementById( 'dlm-page-header-app' );

	if ( ! mount ) {
		return;
	}

	createRoot( mount ).render( <Header /> );
} );
