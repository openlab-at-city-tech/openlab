import { __, sprintf } from '@wordpress/i18n';
import { Button, Spinner, ToggleControl } from '@wordpress/components';
import styles from './extension-table-row.module.scss';
import { useExtensionMutation } from '../query/use-extension-mutation';
import { refreshAdminMenu } from '../query/refresh-admin-menu';

function Divider() {
	return <> | </>;
}

export default function ExtensionTableRow( {
	extension,
	selected = false,
	onSelectChange,
	isPending: rowPending = false,
} ) {
	const { mutate: toggleExtension, isPending } = useExtensionMutation();

	const handleToggle = () => {
		toggleExtension(
			{ extension: extension.slug },
			{
				onSuccess: refreshAdminMenu,
			}
		);
	};

	const handleKeyDown = ( e ) => {
		if ( e.key === 'Enter' || e.key === ' ' ) {
			e.preventDefault();
			handleToggle();
		}
	};

	if ( extension.free ) {
		return (
			<tr>
				<td className={ styles.checkboxColumn } />
				<td className={ styles.extensionColumn }>
					<div className={ styles.extensionDetails }>
						<strong className={ styles.title }>{ extension.name }</strong>
					</div>
				</td>
				<td className={ styles.descriptionColumn }>
					<div className={ styles.description }>{ extension.description }</div>
				</td>
				<td className={ styles.statusColumn }>
					{ extension.active
						? __( 'Active', 'download-monitor' )
						: extension.installed
							? __( 'Installed', 'download-monitor' )
							: __( 'Not installed', 'download-monitor' ) }
				</td>
			</tr>
		);
	}

	const dependencyMet = extension.dependency_met !== false;

	const renderActionText = () => {
		if ( ! extension.available ) {
			return null;
		}

		if ( extension.enabled ) {
			return (
				<>
					<Button variant="link" className={ styles.actionLink } onClick={ handleToggle } onKeyDown={ handleKeyDown } role="button" tabIndex={ 0 }>
						{ __( 'Deactivate', 'download-monitor' ) }
					</Button>
					{ ( isPending || rowPending ) && (
						<span className={ styles.actionLink }>
							<Spinner style={ { width: '9px', height: '9px' } } />
						</span>
					) }
				</>
			);
		}

		if ( ! dependencyMet ) {
			return null;
		}

		return (
			<>
				<Button variant="link" className={ styles.actionLink } onClick={ handleToggle } onKeyDown={ handleKeyDown } role="button" tabIndex={ 0 }>
					{ __( 'Activate', 'download-monitor' ) }
				</Button>
				{ ( isPending || rowPending ) && (
					<span className={ styles.actionLink }>
						<Spinner style={ { width: '9px', height: '9px' } } />
					</span>
				) }
			</>
		);
	};

	const blockedByDependency = ! extension.enabled && ! dependencyMet;

	return (
		<tr className={ ! extension.available || blockedByDependency ? styles.unavailable : '' }>
			<td className={ styles.checkboxColumn }>
				<input
					type="checkbox"
					disabled={ ! extension.available || blockedByDependency }
					checked={ selected }
					onChange={ ( e ) => onSelectChange( e.target.checked ) }
				/>
			</td>
			<td className={ styles.extensionColumn }>
				<div className={ styles.extensionInfo }>
					<div className={ styles.extensionDetails }>
						<strong className={ styles.title }>{ extension.name }</strong>
						<div className={ styles.actions }>{ renderActionText() }</div>
						{ ! dependencyMet && extension.requires_plugin_label && (
							<div className={ styles.dependencyNotice }>
								<svg className={ styles.warningIcon } width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<circle cx="6.5" cy="6.5" r="5.75" stroke="currentColor" strokeWidth="1.2" />
									<path d="M6.5 3.5V7" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" />
									<circle cx="6.5" cy="9.25" r="0.75" fill="currentColor" />
								</svg>
								{ sprintf(
									/* translators: %s: required plugin name, e.g. Gravity Forms */
									__( 'Requires %s to be active', 'download-monitor' ),
									extension.requires_plugin_label
								) }
							</div>
						) }
					</div>
				</div>
			</td>
			<td className={ styles.descriptionColumn }>
				<div className={ styles.description }>{ extension.description }</div>
			</td>
			<td className={ styles.statusColumn }>
				<div className={ styles.statusActions }>
					<ToggleControl
						checked={ extension.enabled }
						onChange={ handleToggle }
						disabled={ ! extension.available || blockedByDependency }
						__nextHasNoMarginBottom={ true }
						__next40pxDefaultSize={ true }
						aria-label={ __( 'Toggle extension status', 'download-monitor' ) }
					/>
				</div>
			</td>
		</tr>
	);
}
