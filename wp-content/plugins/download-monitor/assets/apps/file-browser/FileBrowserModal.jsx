import { useState, useEffect } from '@wordpress/element';
import { Modal, Button, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import FileTree from './FileTree';

export default function FileBrowserModal( { onClose, onSelect } ) {
	const [ items, setItems ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		window.jQuery.post(
			window.dlmFileBrowser.ajaxUrl,
			{
				action: 'download_monitor_list_files',
				path: '',
				security: window.dlmFileBrowser.nonce,
			},
			( response ) => {
				setLoading( false );
				if ( response.success ) {
					setItems( response.data );
				} else {
					setError( __( 'Could not load files.', 'download-monitor' ) );
				}
			}
		).fail( () => {
			setLoading( false );
			setError( __( 'Could not load files.', 'download-monitor' ) );
		} );
	}, [] );

	return (
		<Modal
			title={ __( 'Browse for a file', 'download-monitor' ) }
			onRequestClose={ onClose }
			className="dlm-file-browser-modal"
			size="medium"
		>
			<div className="dlm-fb-wrap">
				<p className="dlm-fb-desc">
					{ __(
						'Choose files under your site uploads directory. Click a file to add it as a download source.',
						'download-monitor'
					) }
				</p>

				<div className="dlm-fb-tree-container">
					{ loading && (
						<div className="dlm-fb-loading">
							<Spinner />
						</div>
					) }
					{ error && (
						<p className="dlm-fb-error">{ error }</p>
					) }
					{ ! loading && ! error && (
						<FileTree items={ items } onSelect={ onSelect } />
					) }
				</div>

				<div className="dlm-fb-footer">
					<Button variant="secondary" onClick={ onClose }>
						{ __( 'Cancel', 'download-monitor' ) }
					</Button>
				</div>
			</div>
		</Modal>
	);
}
