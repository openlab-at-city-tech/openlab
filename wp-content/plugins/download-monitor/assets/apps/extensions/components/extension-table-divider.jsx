import { __, sprintf } from '@wordpress/i18n';
import styles from './extension-table-divider.module.scss';

export default function ExtensionTableDivider( { plan, url } ) {
	const { offer } = window?.dlmExtensionsStrings || {};

	const planName = plan ? plan.charAt( 0 ).toUpperCase() + plan.slice( 1 ).toLowerCase() : '';

	const upgradeMessage = offer?.message
		? offer?.message
		: sprintf(
				/* translators: %1$s: Plan name */
				__( 'Upgrade to %1$s plan to unlock these extensions.', 'download-monitor' ),
				planName
		  );

	const handleClick = () => {
		if ( url ) {
			window.open( url, '_blank' );
		}
	};

	const handleKeyDown = ( e ) => {
		if ( e.key === 'Enter' || e.key === ' ' ) {
			e.preventDefault();
			handleClick();
		}
	};

	return (
		<tr className={ styles.dividerRow }>
			<td colSpan="4" className={ styles.dividerCell }>
				<div className={ styles.dividerContent }>
					<div className={ styles.dividerInfo }>
						<span className={ styles.planBadge }>{ planName }</span>
						<span className={ styles.upgradeText }>{ upgradeMessage }</span>
					</div>
					{ url && (
						<button className={ styles.ctaButton } onClick={ handleClick } onKeyDown={ handleKeyDown } type="button">
							{ __( 'Upgrade Now', 'download-monitor' ) }
							<svg className={ styles.arrowIcon } width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M4.5 9L7.5 6L4.5 3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
							</svg>
						</button>
					) }
				</div>
			</td>
		</tr>
	);
}
