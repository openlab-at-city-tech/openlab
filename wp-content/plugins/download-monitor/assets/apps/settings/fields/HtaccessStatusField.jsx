import { Button } from '@wordpress/components';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import styles from './HtaccessStatusField.module.scss';

const ENDPOINTS = {
	dlm_regenerate_protection: '/download-monitor/v1/regenerate-htaccess',
	dlm_regenerate_robots: '/download-monitor/v1/regenerate-robots',
};

export default function HtaccessStatusField( { field } ) {
	const queryClient = useQueryClient();

	const mutation = useMutation( {
		mutationFn: async () => apiFetch( { path: ENDPOINTS[ field.name ], method: 'POST' } ),
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: [ 'dlm-settings-tabs' ] } );
		},
	} );

	return (
		<div className={ styles.wrapper }>
			<p className={ styles.status }>
				<span className={ `dashicons ${ field.icon || '' }` } style={ { color: field[ 'icon-color' ] || '' } } />
				<span dangerouslySetInnerHTML={ { __html: field[ 'icon-text' ] || '' } } />
			</p>
			<Button
				variant="secondary"
				onClick={ () => mutation.mutate() }
				isBusy={ mutation.isPending }
				disabled={ 'true' === field.disabled || mutation.isPending }
			>
				{ field.label }
			</Button>
		</div>
	);
}
