import { __, sprintf } from '@wordpress/i18n';
import styles from './LockedForm.module.scss';

export default function LockedForm( { badge, reason, extensionName } ) {
	if ( 'disabled' === reason ) {
		return (
			<div className={ styles.lockedForm }>
				<p>
					{ extensionName
						? sprintf(
							/* translators: %s: extension name */
							__( 'Enable %s on the Extensions page to use these settings.', 'download-monitor' ),
							extensionName
						)
						: __( 'Enable this extension on the Extensions page to use these settings.', 'download-monitor' ) }
					{ ' ' }
					<a href={ dlmSettings.extensionsUrl }>{ __( 'Go to Extensions', 'download-monitor' ) }</a>
				</p>
			</div>
		);
	}

	return (
		<div className={ styles.lockedForm }>
			<p>
				{ sprintf(
					/* translators: %s: plan name */
					__( 'Feature available starting with the %s plan.', 'download-monitor' ),
					badge ? badge.charAt( 0 ).toUpperCase() + badge.slice( 1 ) : ''
				) }
			</p>
		</div>
	);
}
