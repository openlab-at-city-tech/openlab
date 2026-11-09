import { useState, useCallback } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const IGNORED_EXTENSIONS = [ '', 'php', 'html', 'htm', 'tmp' ];

const FILE_ICONS = {
	pdf: 'dashicons-pdf',
	zip: 'dashicons-media-archive',
	gz: 'dashicons-media-archive',
	tar: 'dashicons-media-archive',
	rar: 'dashicons-media-archive',
	xls: 'dashicons-media-spreadsheet',
	xlsx: 'dashicons-media-spreadsheet',
	csv: 'dashicons-media-spreadsheet',
	mp4: 'dashicons-media-video',
	mov: 'dashicons-media-video',
	avi: 'dashicons-media-video',
	mp3: 'dashicons-media-audio',
	wav: 'dashicons-media-audio',
	jpg: 'dashicons-media-image',
	jpeg: 'dashicons-media-image',
	png: 'dashicons-media-image',
	gif: 'dashicons-media-image',
	webp: 'dashicons-media-image',
	svg: 'dashicons-media-image',
};

function getFileIcon( ext ) {
	return FILE_ICONS[ ext?.toLowerCase() ] || 'dashicons-media-document';
}

function FileItem( { item, depth, onSelect } ) {
	const [ open, setOpen ] = useState( false );
	const [ children, setChildren ] = useState( [] );
	const [ loading, setLoading ] = useState( false );

	const indentStyle = { paddingLeft: 12 + depth * 22 + 'px' };

	const toggleFolder = useCallback( () => {
		if ( open ) {
			setOpen( false );
			return;
		}
		setLoading( true );
		window.jQuery.post(
			window.dlmFileBrowser.ajaxUrl,
			{
				action: 'download_monitor_list_files',
				path: item.path,
				security: window.dlmFileBrowser.nonce,
			},
			( response ) => {
				setLoading( false );
				if ( response.success ) {
					setChildren( response.data );
				}
				setOpen( true );
			}
		);
	}, [ open, item.path ] );

	if ( item.type === 'folder' ) {
		return (
			<li className={ `dlm-fb-item dlm-fb-folder${ open ? ' dlm-fb-folder--open' : '' }` }>
				<button
					type="button"
					className="dlm-fb-item__row dlm-fb-folder__row"
					onClick={ toggleFolder }
					style={ indentStyle }
				>
					<span className="dlm-fb-item__arrow">
						<span
							className={ `dashicons ${ open ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-right-alt2' }` }
						/>
					</span>
					<span className="dlm-fb-item__icon">
						<span className="dashicons dashicons-category" />
					</span>
					<span className="dlm-fb-item__name">{ item.name }</span>
					{ loading && (
						<span className="dlm-fb-item__spinner">
							<Spinner />
						</span>
					) }
				</button>

				{ open && (
					<ul className="dlm-fb-tree">
						{ children.length === 0 && ! loading ? (
							<li
								className="dlm-fb-item dlm-fb-empty"
								style={ { paddingLeft: 12 + ( depth + 1 ) * 22 + 'px' } }
							>
								{ __( 'No files found', 'download-monitor' ) }
							</li>
						) : (
							children.map( ( child, i ) => (
								<FileItem
									key={ i }
									item={ child }
									depth={ depth + 1 }
									onSelect={ onSelect }
								/>
							) )
						) }
					</ul>
				) }
			</li>
		);
	}

	if ( IGNORED_EXTENSIONS.includes( item.ext ) ) {
		return null;
	}

	return (
		<li className="dlm-fb-item dlm-fb-file">
			<button
				type="button"
				className="dlm-fb-item__row dlm-fb-file__row"
				onClick={ () => onSelect( item.path ) }
				title={ item.path }
				style={ indentStyle }
			>
				<span className="dlm-fb-item__arrow dlm-fb-item__arrow--spacer" />
				<span className="dlm-fb-item__icon dlm-fb-file__icon">
					<span className={ `dashicons ${ getFileIcon( item.ext ) }` } />
				</span>
				<span className="dlm-fb-item__name">{ item.name }</span>
			</button>
		</li>
	);
}

export default function FileTree( { items, onSelect } ) {
	if ( ! items || items.length === 0 ) {
		return (
			<p className="dlm-fb-empty dlm-fb-empty--root">
				{ __( 'No files found.', 'download-monitor' ) }
			</p>
		);
	}

	return (
		<ul className="dlm-fb-tree dlm-fb-tree--root">
			{ items.map( ( item, i ) => (
				<FileItem key={ i } item={ item } depth={ 0 } onSelect={ onSelect } />
			) ) }
		</ul>
	);
}
