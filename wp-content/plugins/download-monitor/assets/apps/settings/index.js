import { createRoot } from '@wordpress/element';
import { QueryClientProvider } from '@tanstack/react-query';
import SettingsPage from './SettingsPage';
import { queryClient } from './query/client';
import { SettingsProvider } from './context/settings-context';
import './index.scss';

document.addEventListener( 'DOMContentLoaded', () => {
	const node = document.getElementById( 'dlm-settings-app' );

	if ( ! node ) {
		return;
	}

	createRoot( node ).render(
		<QueryClientProvider client={ queryClient }>
			<SettingsProvider>
				<SettingsPage />
			</SettingsProvider>
		</QueryClientProvider>
	);
} );
