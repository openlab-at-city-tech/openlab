import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { ToggleControl } from '@wordpress/components';
import styles from './GatewayOverviewField.module.scss';

export default function GatewayOverviewField( { getGatewayValue, onGatewayChange, disabled } ) {
	const [ gateways, setGateways ] = useState( [] );

	useEffect( () => {
		apiFetch( { path: '/download-monitor/v1/settings-gateways' } ).then( setGateways );
	}, [] );

	return (
		<ul className={ styles.list }>
			{ gateways.map( ( gateway ) => (
				<li key={ gateway.id }>
					<ToggleControl
						label={ gateway.title }
						checked={ getGatewayValue( gateway ) }
						disabled={ disabled }
						onChange={ ( checked ) => onGatewayChange( gateway, checked ) }
					/>
				</li>
			) ) }
		</ul>
	);
}
