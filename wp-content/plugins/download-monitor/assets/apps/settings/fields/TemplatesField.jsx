import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Button } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { SkeletonPanel } from '../Skeleton';
import styles from './TemplatesField.module.scss';

export default function TemplatesField() {
	const [ overrides, setOverrides ] = useState( [] );
	const [ loading, setLoading ] = useState( true );

	useEffect( () => {
		apiFetch( { path: '/download-monitor/v1/theme-templates' } )
			.then( ( data ) => setOverrides( data.overrides || [] ) )
			.finally( () => setLoading( false ) );
	}, [] );

	if ( loading ) {
		return <SkeletonPanel />;
	}

	if ( 0 === overrides.length ) {
		return (
			<h3>{ __( "None of Download Monitor's output templates are being overridden by your theme.", 'download-monitor' ) }</h3>
		);
	}

	return (
		<div className={ styles.wrapper }>
			<h3>{ sprintf( __( 'There are %s overriden templates!', 'download-monitor' ), overrides.length ) }</h3>

			<table className="widefat striped">
				<thead>
					<tr>
						<th>{ __( 'Overridden file', 'download-monitor' ) }</th>
						<th>{ __( 'Overridden file version', 'download-monitor' ) }</th>
						<th>{ __( 'Core version', 'download-monitor' ) }</th>
						<th>{ __( 'Status', 'download-monitor' ) }</th>
						<th>{ __( 'Edit', 'download-monitor' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ overrides.map( ( override ) => (
						<tr key={ override.file }>
							<td><strong>{ override.file }</strong></td>
							<td>{ override.version || '-' }</td>
							<td>{ override.core_version || '-' }</td>
							<td>
								<span
									className={ `dashicons ${ override.needs_update ? 'dashicons-warning' : 'dashicons-yes' }` }
									style={ { color: override.needs_update ? 'red' : 'green' } }
									title={ override.needs_update ? __( 'needs update', 'download-monitor' ) : '' }
								/>
							</td>
							<td>
								<Button variant="secondary" href={ override.edit_url } target="_blank">
									{ __( 'Edit', 'download-monitor' ) }
								</Button>
							</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}
