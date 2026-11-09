import { createRoot } from '@wordpress/element';
import { QueryClientProvider } from '@tanstack/react-query';
import { queryClient } from './query/client';
import './index.css';
import ExtensionList from './components/extension-list';

document.addEventListener( 'DOMContentLoaded', () => {
	const page = document.getElementById( 'dlm-extensions-app' );

	if ( ! page ) {
		return;
	}

	const root = createRoot( page );
	root.render(
		<QueryClientProvider client={ queryClient }>
			<ExtensionList />
		</QueryClientProvider>,
	);
} );
