<?php
/**
 * Imagely Header
 *
 * @since 3.5.0
 *
 * @package Nextgen Galllery
 */

use Imagely\NGG\Admin\AMNotifications as Notifications;

$upgrade_link        = M_Marketing::get_utm_link( 'https://www.imagely.com/lite', 'topbar', 'getnextgenpro' );
$notifications       = new Notifications();
$notifications_count = $notifications->get_count();
$dismissed_count     = $notifications->get_dismissed_count();
$data_count          = '';

if ( $notifications_count > 0 ) {
	$data_count = sprintf(
		'data-count="%d"',
		absint( $notifications_count )
	);
}
$is_pro = nextgen_is_plus_or_pro_enabled();
?>
<div id="nextgen-header-temp"></div>

<div id="nextgen-top-notification" class="nextgen-header-notification<?php echo esc_attr( $is_pro ? ' nextgen-pro-active' : '' ); ?>">
	<p>
		<?php
			printf(
				// Translators: %1$s - Opening anchor tag, do not translate. %2$s - Closing anchor tag, do not translate.
				esc_html__( 'You\'re using NextGEN Gallery Lite. To unlock more features consider %1$s upgrading to PRO%2$s for 50%% off', 'nggallery' ),
				'<a href="' . esc_url( $upgrade_link ) . '" target="_blank" rel="noopener noreferrer">',
				'</a>'
			);
			?>
</div>

<div id="nextgen-header" class="nextgen-header">
	<h1 class="nextgen-logo" id="nextgen-logo">
		<img src="<?php echo esc_url( trailingslashit( NGG_PLUGIN_URI ) . 'assets/images/logo.png' ); ?>" alt="<?php esc_attr_e( 'Nextgen Gallery', 'nggallery' ); ?>" width="339"/>
	</h1>

	<div class="nextgen-right">
		<a type="button" id="nextgen-notifications-button" class="nextgen-button-just-icon nextgen-notifications-inbox nextgen-open-notifications" data-dismissed="<?php echo esc_attr( $dismissed_count ); ?>" <?php echo $data_count; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<svg class="nextgen-icon nextgen-icon-inbox" width="20" height="20" viewBox="0 0 15 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M13.3333 0.5H1.66667C0.75 0.5 0 1.25 0 2.16667V13.8333C0 14.75 0.741667 15.5 1.66667 15.5H13.3333C14.25 15.5 15 14.75 15 13.8333V2.16667C15 1.25 14.25 0.5 13.3333 0.5ZM13.3333 13.8333H1.66667V11.3333H4.63333C5.20833 12.325 6.275 13 7.50833 13C8.74167 13 9.8 12.325 10.3833 11.3333H13.3333V13.8333ZM9.175 9.66667H13.3333V2.16667H1.66667V9.66667H5.84167C5.84167 10.5833 6.59167 11.3333 7.50833 11.3333C8.425 11.3333 9.175 10.5833 9.175 9.66667Z" fill="#777777"></path></svg>
		</a>
	</div>

	<?php do_action( 'nextgen_admin_in_header' ); ?>

</div>
