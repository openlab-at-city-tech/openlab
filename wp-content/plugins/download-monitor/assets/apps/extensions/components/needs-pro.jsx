import { __experimentalText as Text } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function NeedsPro() {
	return <Text>{ __( 'Please install the Pro version of the plugin to access and install extensions.', 'download-monitor' ) }</Text>;
}
