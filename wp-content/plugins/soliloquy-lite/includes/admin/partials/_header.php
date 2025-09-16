<?php
/**
 * Header Template
 *
 * @since 2.5.0
 * @package SoliloquyWP Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

$base         = Soliloquy_Lite::get_instance();
$upgrade_link = Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( 'https://soliloquywp.com/lite', 'topbar', 'goPro' );
// Load the base class object.
$notifications_count = $base->notifications->get_count();
$dismissed_count     = $base->notifications->get_dismissed_count();
$data_count          = '';
if ( $notifications_count > 0 ) {
	$data_count = sprintf(
		'data-count="%d"',
		absint( $notifications_count )
	);
}
?>

<div id="soliloquy-header-temp"></div>

<div id="soliloquy-top-notification" class="soliloquy-header-notification">
	<p>You're using Soliloquy Lite. To unlock more features, <a href="<?php echo esc_url( $upgrade_link ); ?>"
			target="_blank"><strong>consider upgrading to Pro.</strong></a></p>
</div>
<div id="soliloquy-header">

	<div id="soliloquy-logo">
		<a href="https://soliloquywp.com/?utm_source=liteplugin&utm_medium=logo&utm_campaign=liteplugin" aria-label="<?php esc_html_e( 'Soliloquy home page', 'soliloquy' ); ?>" target="_blank" rel="noopener noreferrer">
			<img src="<?php echo esc_url( plugins_url( 'assets/images/logo-color.png', $base->file ) ); ?>" alt="<?php esc_html_e( 'Soliloquy logo', 'soliloquy' ); ?>">
		</a>
	</div>

	<div class="soliloquy-right">
		<a type="button" id="soliloquy-notifications-button" class="soliloquy-button-just-icon soliloquy-notifications-inbox soliloquy-open-notifications" data-dismissed="<?php echo esc_attr( $dismissed_count ); ?>" <?php echo $data_count; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<svg class="soliloquy-icon soliloquy-icon-inbox" width="20" height="20" viewBox="0 0 15 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M13.3333 0.5H1.66667C0.75 0.5 0 1.25 0 2.16667V13.8333C0 14.75 0.741667 15.5 1.66667 15.5H13.3333C14.25 15.5 15 14.75 15 13.8333V2.16667C15 1.25 14.25 0.5 13.3333 0.5ZM13.3333 13.8333H1.66667V11.3333H4.63333C5.20833 12.325 6.275 13 7.50833 13C8.74167 13 9.8 12.325 10.3833 11.3333H13.3333V13.8333ZM9.175 9.66667H13.3333V2.16667H1.66667V9.66667H5.84167C5.84167 10.5833 6.59167 11.3333 7.50833 11.3333C8.425 11.3333 9.175 10.5833 9.175 9.66667Z" fill="#777777"></path></svg>
		</a>
	</div>

</div>
