import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, SelectControl } from '@wordpress/components';
import styles from './extension-bulk-actions.module.scss';
import ExtensionLicenseHeader from './extension-license-header';
import NeedsPro from './needs-pro';
import ProNeedsUpdate from './pro-needs-update';

export default function ExtensionBulkActions( { selectedIds, onBulkAction } ) {
	const { proExists, proOutdated } = window?.dlmExtensionsStrings || {};
	const [ selectedAction, setSelectedAction ] = useState( '' );

	const handleApply = () => {
		if ( ! selectedAction || selectedIds.length === 0 ) {
			return;
		}
		onBulkAction( selectedAction, selectedIds );
		setSelectedAction( '' );
	};

	const bulkActions = [
		{ value: '', label: __( 'Bulk Actions', 'download-monitor' ) },
		{ value: 'activate', label: __( 'Activate', 'download-monitor' ) },
		{ value: 'deactivate', label: __( 'Deactivate', 'download-monitor' ) },
	];

	return (
		<div className={ styles.bulkActionsBar }>
			<div className={ styles.bulkActionsSelect }>
				<SelectControl
					value={ selectedAction }
					options={ bulkActions }
					onChange={ setSelectedAction }
					className={ styles.bulkSelect }
					__nextHasNoMarginBottom={ true }
					__next40pxDefaultSize={ true }
				/>
				<Button
					variant="secondary"
					onClick={ handleApply }
					disabled={ ! selectedAction || selectedIds.length === 0 }
					className={ styles.applyButton }
				>
					{ __( 'Apply', 'download-monitor' ) }
				</Button>
			</div>
			<div className={ styles.bulkActionsLicense }>
				{ proOutdated ? <ProNeedsUpdate /> : ( proExists ? <ExtensionLicenseHeader /> : <NeedsPro /> ) }
			</div>
		</div>
	);
}
