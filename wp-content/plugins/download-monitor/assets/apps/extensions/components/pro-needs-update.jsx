import { __experimentalText as Text } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function ProNeedsUpdate() {
	return <Text>{ __( 'Please update the Pro version of the plugin to version 1.1.0 or later to manage your license and extensions.', 'download-monitor' ) }</Text>;
}
