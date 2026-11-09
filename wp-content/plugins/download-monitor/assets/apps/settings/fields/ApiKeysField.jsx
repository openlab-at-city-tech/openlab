import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Button, ComboboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SkeletonPanel } from '../Skeleton';
import styles from './ApiKeysField.module.scss';

export default function ApiKeysField() {
	const [ keys, setKeys ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ users, setUsers ] = useState( [] );
	const [ selectedUser, setSelectedUser ] = useState( null );
	const [ generating, setGenerating ] = useState( false );

	const load = () => {
		setLoading( true );
		apiFetch( { path: '/download-monitor/v1/api-keys' } )
			.then( setKeys )
			.finally( () => setLoading( false ) );
	};

	useEffect( load, [] );

	useEffect( () => {
		apiFetch( { path: '/download-monitor/v1/users-search' } ).then( setUsers );
	}, [] );

	const searchUsers = ( term ) => {
		apiFetch( { path: `/download-monitor/v1/users-search?q=${ encodeURIComponent( term || '' ) }` } ).then( setUsers );
	};

	const generateForUser = ( userId ) => {
		if ( ! userId ) {
			return;
		}

		setGenerating( true );
		apiFetch( {
			path: '/download-monitor/v1/api-keys',
			method: 'POST',
			data: { user_id: userId },
		} )
			.then( load )
			.finally( () => setGenerating( false ) );
	};

	const revoke = ( key ) => {
		// eslint-disable-next-line no-alert
		if ( ! window.confirm( __( 'Revoke this API key permanently?', 'download-monitor' ) ) ) {
			return;
		}

		apiFetch( { path: `/download-monitor/v1/api-keys/${ key.id }`, method: 'DELETE' } ).then( load );
	};

	if ( loading ) {
		return <SkeletonPanel />;
	}

	return (
		<div className={ styles.wrapper }>
			<div className={ styles.generator }>
				<ComboboxControl
					label={ __( 'User', 'download-monitor' ) }
					value={ selectedUser }
					options={ users.map( ( user ) => ( { value: user.value, label: user.label } ) ) }
					onChange={ setSelectedUser }
					onFilterValueChange={ searchUsers }
					__nextHasNoMarginBottom
				/>
				<Button
					variant="secondary"
					onClick={ () => generateForUser( selectedUser ) }
					isBusy={ generating }
					disabled={ ! selectedUser || generating }
				>
					{ __( 'Generate API Key', 'download-monitor' ) }
				</Button>
			</div>

			<table className="widefat striped">
				<thead>
					<tr>
						<th>{ __( 'Username', 'download-monitor' ) }</th>
						<th>{ __( 'Public key', 'download-monitor' ) }</th>
						<th>{ __( 'Token', 'download-monitor' ) }</th>
						<th>{ __( 'Secret key', 'download-monitor' ) }</th>
						<th>{ __( 'Creation date', 'download-monitor' ) }</th>
						<th>{ __( 'Actions', 'download-monitor' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ ! loading && 0 === keys.length && (
						<tr>
							<td colSpan={ 6 }>{ __( 'No API keys yet.', 'download-monitor' ) }</td>
						</tr>
					) }
					{ keys.map( ( key ) => (
						<tr key={ key.id }>
							<td>{ key.username }</td>
							<td>{ key.public_key }</td>
							<td>{ key.token }</td>
							<td>{ key.secret_key }</td>
							<td>{ key.create_date }</td>
							<td>
								<Button variant="link" onClick={ () => generateForUser( key.user_id ) }>
									{ __( 'Regenerate key', 'download-monitor' ) }
								</Button>
								{ ' | ' }
								<Button variant="link" isDestructive onClick={ () => revoke( key ) }>
									{ __( 'Revoke key', 'download-monitor' ) }
								</Button>
							</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}
