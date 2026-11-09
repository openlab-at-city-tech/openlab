import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function DriveAuthButtonField( { field } ) {
	return field.connected ? (
		<Button variant="secondary" isDestructive href={ field.revoke_url }>
			{ __( 'Revoke access', 'download-monitor' ) }
		</Button>
	) : (
		<Button variant="primary" href={ field.grant_url }>
			{ __( 'Grant Access', 'download-monitor' ) }
		</Button>
	);
}
