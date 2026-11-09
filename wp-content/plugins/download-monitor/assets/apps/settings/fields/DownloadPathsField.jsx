import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Button, Modal, TextControl, ToggleControl, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SkeletonPanel } from '../Skeleton';
import styles from './DownloadPathsField.module.scss';

const emptyForm = { id: 0, path_val: '', enabled: true };

export default function DownloadPathsField() {
	const [ paths, setPaths ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ modalOpen, setModalOpen ] = useState( false );
	const [ form, setForm ] = useState( emptyForm );
	const [ error, setError ] = useState( '' );

	const load = () => {
		setLoading( true );
		apiFetch( { path: '/download-monitor/v1/download-paths' } )
			.then( setPaths )
			.finally( () => setLoading( false ) );
	};

	useEffect( load, [] );

	const openAdd = () => {
		setForm( emptyForm );
		setError( '' );
		setModalOpen( true );
	};

	const openEdit = ( path ) => {
		setForm( path );
		setError( '' );
		setModalOpen( true );
	};

	const save = () => {
		apiFetch( {
			path: '/download-monitor/v1/download-paths',
			method: 'POST',
			data: form,
		} )
			.then( () => {
				setModalOpen( false );
				load();
			} )
			.catch( ( err ) => setError( err?.message || __( 'Something went wrong.', 'download-monitor' ) ) );
	};

	const toggleEnabled = ( path ) => {
		apiFetch( {
			path: '/download-monitor/v1/download-paths',
			method: 'POST',
			data: { ...path, enabled: ! path.enabled },
		} ).then( load );
	};

	const remove = ( path ) => {
		// eslint-disable-next-line no-alert
		if ( ! window.confirm( __( 'Delete this approved path permanently?', 'download-monitor' ) ) ) {
			return;
		}

		apiFetch( { path: `/download-monitor/v1/download-paths/${ path.id }`, method: 'DELETE' } ).then( load );
	};

	if ( loading ) {
		return <SkeletonPanel />;
	}

	return (
		<div className={ styles.wrapper }>
			<div className={ styles.header }>
				<Button variant="primary" onClick={ openAdd }>
					{ __( 'Add New', 'download-monitor' ) }
				</Button>
			</div>

			<table className="widefat striped">
				<thead>
					<tr>
						<th>{ __( 'URL', 'download-monitor' ) }</th>
						<th>{ __( 'Enabled', 'download-monitor' ) }</th>
						<th>{ __( 'Actions', 'download-monitor' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ ! loading && 0 === paths.length && (
						<tr>
							<td colSpan={ 3 }>{ __( 'No approved paths yet.', 'download-monitor' ) }</td>
						</tr>
					) }
					{ paths.map( ( path ) => (
						<tr key={ path.id }>
							<td>{ path.path_val }</td>
							<td>
								<ToggleControl
									checked={ !! path.enabled }
									onChange={ () => toggleEnabled( path ) }
									__nextHasNoMarginBottom
								/>
							</td>
							<td>
								<Button variant="link" onClick={ () => openEdit( path ) }>
									{ __( 'Edit', 'download-monitor' ) }
								</Button>
								{ ' | ' }
								<Button variant="link" isDestructive onClick={ () => remove( path ) }>
									{ __( 'Delete', 'download-monitor' ) }
								</Button>
							</td>
						</tr>
					) ) }
				</tbody>
			</table>

			{ modalOpen && (
				<Modal
					title={ form.id ? __( 'Edit Approved Path', 'download-monitor' ) : __( 'Add New Approved Path', 'download-monitor' ) }
					onRequestClose={ () => setModalOpen( false ) }
				>
					{ error && <Notice status="error" isDismissible={ false }>{ error }</Notice> }

					<TextControl
						label={ __( 'Directory URL', 'download-monitor' ) }
						value={ form.path_val }
						onChange={ ( value ) => setForm( { ...form, path_val: value } ) }
						help={ __( 'Absolute path on the server, e.g. the WordPress installation directory.', 'download-monitor' ) }
					/>

					<ToggleControl
						label={ __( 'Enabled', 'download-monitor' ) }
						checked={ !! form.enabled }
						onChange={ ( enabled ) => setForm( { ...form, enabled } ) }
					/>

					<div className={ styles.modalActions }>
						<Button variant="tertiary" onClick={ () => setModalOpen( false ) }>
							{ __( 'Cancel', 'download-monitor' ) }
						</Button>
						<Button variant="primary" onClick={ save } disabled={ ! form.path_val }>
							{ __( 'Save Changes', 'download-monitor' ) }
						</Button>
					</div>
				</Modal>
			) }
		</div>
	);
}
