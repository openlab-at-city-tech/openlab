import { __ } from '@wordpress/i18n';

export default function MailchimpConnectionField() {
	const App = window.DLMMailchimpConnectionApp;

	if ( ! App ) {
		return <p>{ __( 'Mailchimp connection settings are unavailable.', 'download-monitor' ) }</p>;
	}

	return <App />;
}
