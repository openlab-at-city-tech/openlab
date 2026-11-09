import { __ } from '@wordpress/i18n';

export default function PageAddonSettingsField( { field } ) {
	const App = window.DLMPageAddonSettingsApp;

	if ( ! App ) {
		return <p>{ __( 'Document Library Manager settings are unavailable.', 'download-monitor' ) }</p>;
	}

	return <App type={ field.data_type || 'base' } />;
}
