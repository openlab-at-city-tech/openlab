import { useMemo, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import styles from './extension-table.module.scss';
import ExtensionTableRow from './extension-table-row';
import ExtensionBulkActions from './extension-bulk-actions';
import { useExtensionQuery } from '../query/use-extension-query';
import { useExtensionsMutation } from '../query/use-extensions-mutation';
import ExtensionTableDivider from './extension-table-divider';
import { refreshAdminMenu } from '../query/refresh-admin-menu';

export default function ExtensionTable() {
	const {
		data: extensions,
		isLoading,
		isError,
		isRefetching,
	} = useExtensionQuery();

	const { mutate: toggleExtensions, isPending } = useExtensionsMutation();
	const [ selectedIds, setSelectedIds ] = useState( [] );

	const currentExtensions = useMemo( () => {
		if ( isLoading || isError || ! extensions ) {
			return [];
		}

		return Object.values( extensions );
	}, [ extensions, isLoading, isError ] );

	const handleSelectAll = ( checked ) => {
		if ( checked ) {
			setSelectedIds(
				currentExtensions.filter( ( ext ) => ext.available ).map( ( ext ) => ext.slug )
			);
		} else {
			setSelectedIds( [] );
		}
	};

	const handleSelectOne = ( id, checked ) => {
		if ( checked ) {
			setSelectedIds( [ ...selectedIds, id ] );
		} else {
			setSelectedIds( selectedIds.filter( ( selectedId ) => selectedId !== id ) );
		}
	};

	const handleBulkAction = ( action, ids ) => {
		toggleExtensions(
			{ extensions: ids, status: action },
			{
				onSuccess: refreshAdminMenu,
			}
		);
		setSelectedIds( [] );
	};

	const availableCount = currentExtensions.filter( ( ext ) => ext.available ).length;
	const allSelected = selectedIds.length === availableCount && availableCount > 0;
	const someSelected = selectedIds.length > 0 && selectedIds.length < availableCount;

	return (
		<>
			<ExtensionBulkActions selectedIds={ selectedIds } onBulkAction={ handleBulkAction } />
			<div className={ styles.tableWrapper }>
				<table className={ styles.extensionTable }>
					<thead>
						<tr>
							<th className={ styles.checkboxColumn }>
								<input
									type="checkbox"
									checked={ allSelected }
									ref={ ( input ) => {
										if ( input ) {
											input.indeterminate = someSelected;
										}
									} }
									onChange={ ( e ) => handleSelectAll( e.target.checked ) }
								/>
							</th>
							<th className={ styles.extensionColumn }>{ __( 'Extension', 'download-monitor' ) }</th>
							<th className={ styles.descriptionColumn }>{ __( 'Description', 'download-monitor' ) }</th>
							<th className={ styles.statusColumn }>{ __( 'Status', 'download-monitor' ) }</th>
						</tr>
					</thead>
					<tbody>
						{ currentExtensions.map( ( extension ) =>
							extension?.is_divider ? (
								<ExtensionTableDivider
									key={ extension.slug }
									plan={ extension?.plan || 'free' }
									url={ extension?.url }
								/>
							) : (
								<ExtensionTableRow
									key={ extension.slug }
									extension={ extension }
									selected={ selectedIds.includes( extension.slug ) }
									onSelectChange={ ( checked ) => handleSelectOne( extension.slug, checked ) }
									isPending={ selectedIds.includes( extension.slug ) && ( isPending || isRefetching ) }
								/>
							)
						) }
					</tbody>
				</table>
			</div>
		</>
	);
}
