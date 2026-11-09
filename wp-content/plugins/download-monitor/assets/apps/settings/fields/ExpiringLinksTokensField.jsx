import { __ } from '@wordpress/i18n';

export default function ExpiringLinksTokensField() {
	const App = window.DLMExpiringLinksTokensApp;

	if ( ! App ) {
		return <p>{ __( 'Expiring Links settings are unavailable.', 'download-monitor' ) }</p>;
	}

	return <App />;
}
