<?php
/**
 * Open Badge Integrations.
 *
 * @package BadgeOS
 * @subpackage Open Badge
 * @author Learning Times
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 */

/**
 * Abort if this file is accessed directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function badgeos_ob_integration_page() {

	$badgeos_assertion_url = badgeos_utilities::get_option( 'badgeos_assertion_url' );
	$badgeos_json_url      = badgeos_utilities::get_option( 'badgeos_json_url' );
	$badgeos_issuer_url    = badgeos_utilities::get_option( 'badgeos_issuer_url' );
	$badgeos_evidence_url  = badgeos_utilities::get_option( 'badgeos_evidence_url' );
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php esc_html_e( 'Open Badge Settings', 'badgeos' ); ?></h2>
		<form method="post">
			<table class="form-table">
				<tr valign="top">
					<td align="left" width="20%" valign="top">
						<?php esc_html_e( 'Assertion Page', 'badgeos' ); ?>
					</td>
					<td width="80%" align="left">
						<label for="badgeos_assertion_url">
						<?php
						wp_dropdown_pages(
							array(
								'show_option_none' => esc_html__( 'Select Assertion Page', 'badgeos' ),
								'selected'         => esc_html( $badgeos_assertion_url ),
								'name'             => esc_html__( 'badgeos_assertion_url', 'badgeos' ),
								'id'               => esc_html__( 'badgeos_assertion_url', 'badgeos' ),
							)
						);
						?>
						</label>
						<p class="badgeos_hint"><?php esc_html_e( 'Please, select a page where assertion json data will show up. Open badge needs assertion data in json format for validation purpose.', 'badgeos' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<td align="left" valign="top">
						<?php esc_html_e( 'Issuer Page', 'badgeos' ); ?>
					</td>
					<td align="left">
						<label for="badgeos_issuer_url">
						<?php
						wp_dropdown_pages(
							array(
								'show_option_none' => esc_html__( 'Select Issuer Page', 'badgeos' ),
								'selected'         => esc_html( $badgeos_issuer_url ),
								'name'             => esc_html__( 'badgeos_issuer_url', 'badgeos' ),
								'id'               => esc_html__( 'badgeos_issuer_url', 'badgeos' ),
							)
						);
						?>
						</label>
						<p class="badgeos_hint"><?php esc_html_e( 'Please, select a page where issuer json data will show up. Open badge needs issuer data in json format for validation purpose.', 'badgeos' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<td align="left" valign="top">
						<?php esc_html_e( 'Badge Page', 'badgeos' ); ?>
					</td>
					<td align="left">
						<label for="badgeos_json_url">
						<?php
						wp_dropdown_pages(
							array(
								'show_option_none' => esc_html__( 'Select Badge Page', 'badgeos' ),
								'selected'         => esc_html( $badgeos_json_url ),
								'name'             => esc_html__( 'badgeos_json_url', 'badgeos' ),
								'id'               => esc_html__( 'badgeos_json_url', 'badgeos' ),
							)
						);
						?>
						</label>
						<p class="badgeos_hint"><?php esc_html_e( 'Please, select a page where badge json data will show up. Open badge needs badge data in json format for validation purpose.', 'badgeos' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<td align="left" valign="top">
						<?php esc_html_e( 'Evidence Page', 'badgeos' ); ?>
					</td>
					<td align="left">
						<label for="badgeos_evidence_url">
						<?php
						wp_dropdown_pages(
							array(
								'show_option_none' => esc_html__( 'Select Evidence Page', 'badgeos' ),
								'selected'         => esc_html( $badgeos_evidence_url ),
								'name'             => esc_html__( 'badgeos_evidence_url', 'badgeos' ),
								'id'               => esc_html__( 'badgeos_evidence_url', 'badgeos' ),
							)
						);
						?>
						</label>
						<p class="badgeos_hint"><?php esc_html_e( 'Please, select a page for badge evidence and add [badgeos_evidence] shortcode in page description.', 'badgeos' ); ?></p>
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'bos_ob_settings', 'ob_settings_field' ); ?>
			<?php
				$assertion_page = badgeos_utilities::get_option( 'badgeos_assertion_url', false );
				$json_page      = badgeos_utilities::get_option( 'badgeos_json_url', false );
				$issuer_page    = badgeos_utilities::get_option( 'badgeos_issuer_url', false );
				$evidence_page  = badgeos_utilities::get_option( 'badgeos_evidence_url', false );
			if ( current_user_can( 'manage_options' ) && ( ! badgeos_ob_is_page_exists( $assertion_page ) || ! badgeos_ob_is_page_exists( $json_page ) || ! badgeos_ob_is_page_exists( $issuer_page ) || ! badgeos_ob_is_page_exists( $evidence_page ) ) ) {
				$config_link = 'admin-post.php?action=badgeos_config_pages';
				$message     = esc_html__( 'Please, click <a href="admin-post.php?action=badgeos_config_pages">here</a> to configure/create missing open badge pages.', 'badgeos' );
				printf( '<p><strong>%s</strong></p>', esc_html( $message ) );
			} else {
				$message = esc_html__( 'All of the open badge pages are configured.', 'badgeos' );
				printf( '<p><strong>%s</strong></p>', esc_html( $message ) );

			}
			?>
			<?php
				// We need to get our api key
				submit_button( esc_html__( 'Save Settings', 'badgeos' ) );
			?>
		</form>
		<?php

		$non_ob_conversion_progress = 0;
		get_transient( 'non_ob_conversion_progress' ); // check if background conversion process is running
		$non_os_badgeos_achievements = badgeos_ob_get_all_non_achievements();

		$message = 'Please click on "Convert Achievements" button below to convert existing non-open standard user\'s achievements into open standards.';
		?>
		<div class="open_badge_convert_wrapper">
			<h3><?php esc_html_e( 'Convert non-open standard achievements', 'badgeos' ); ?></h3>
			<?php if ( ! $non_ob_conversion_progress && ! empty( $non_os_badgeos_achievements ) ) : ?>
				<div id="non_ob_button_message">
					<p><?php esc_html_e( $message, 'badgeos' ); ?></p>
					<input type="button" id="convert_non_open_achievements" class="button-primary" value="<?php esc_html_e( 'Convert Achievements', 'badgeos' ); ?>" />
				</div>
			<?php elseif ( ! $non_ob_conversion_progress ) : ?>
				<div id="no-non-ob"><p><?php esc_html_e( 'All user\'s achievements are in open-standards, no non-open standard achievements found.' ); ?></p></div>
			<?php endif; ?>
			<div id="open_badge_conversion_in_progress" style="<?php echo ! $non_ob_conversion_progress ? 'display: none;' : ''; ?> padding: 10px; margin-top: 10px;">
				<div style="float: left; margin-right:10px;"><img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>"></div>
				<div style="float: left;"><?php esc_html_e( 'Non open standards achievements conversion is processing in background, you will receive an email after conversion is completed.', 'badgeos' ); ?></div>
				<div style="clear: both;"></div>
			</div> 
		</div>
		
		</div>
	<?php
}


function badgeos_ob_save_settings() {
	if ( isset( $_POST['ob_settings_field'] ) && check_admin_referer( 'bos_ob_settings', 'ob_settings_field' ) ) {

		if ( isset( $_POST['badgeos_assertion_url'] ) && ! empty( $_POST['badgeos_assertion_url'] ) ) {
			badgeos_utilities::update_option( 'badgeos_assertion_url', sanitize_text_field( $_POST['badgeos_assertion_url'] ) );
		}

		if ( isset( $_POST['badgeos_issuer_url'] ) && ! empty( $_POST['badgeos_issuer_url'] ) ) {
			badgeos_utilities::update_option( 'badgeos_issuer_url', sanitize_text_field( $_POST['badgeos_issuer_url'] ) );
		}

		if ( isset( $_POST['badgeos_json_url'] ) && ! empty( $_POST['badgeos_json_url'] ) ) {
			badgeos_utilities::update_option( 'badgeos_json_url', sanitize_text_field( $_POST['badgeos_json_url'] ) );
		}

		if ( isset( $_POST['badgeos_evidence_url'] ) && ! empty( $_POST['badgeos_evidence_url'] ) ) {
			badgeos_utilities::update_option( 'badgeos_evidence_url', sanitize_text_field( $_POST['badgeos_evidence_url'] ) );
		}

		add_action( 'admin_notices', 'badgeos_ob_save_settings_notification' );
	}
}

add_action( 'admin_init', 'badgeos_ob_save_settings' );

/**
 * Setting notification
 */
function badgeos_ob_save_settings_notification() {
	$class   = 'notice notice-success is-dismissible';
	$message = esc_html__( 'Settings Saved.', 'badgeos' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
