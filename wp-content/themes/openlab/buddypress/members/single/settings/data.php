<?php
/**
 * BuddyPress - Members Settings Data
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 4.0.0
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_before_member_settings_template' ); ?>

<?php echo openlab_submenu_markup(); ?>

<div id="item-body" role="main">

	<form class="standard-form form-panel" id="bp-data-export" method="post">
		<div class="panel panel-default">
			<div class="panel-heading"><?php _e( 'Data Export', 'buddypress' ); ?></div>

			<div class="panel-body">
				<?php $request = bp_settings_get_personal_data_request(); ?>

				<?php if ( $request ) : ?>

					<?php if ( 'request-completed' === $request->status ) : ?>

						<?php if ( bp_settings_personal_data_export_exists( $request ) ) : ?>

							<p><?php esc_html_e( 'Your request for an export of personal data has been completed.', 'buddypress' ); ?></p>
							<p><?php printf( esc_html__( 'You may download your personal data by clicking on the link below. For privacy and security, we will automatically delete the file on %s, so please download it before then.', 'buddypress' ), bp_settings_get_personal_data_expiration_date( $request ) ); ?></p>

							<p><strong><?php printf( '<a href="%1$s">%2$s</a>', bp_settings_get_personal_data_export_url( $request ), esc_html__( 'Download personal data', 'buddypress' ) ); ?></strong></p>

						<?php else : ?>

							<p><?php esc_html_e( 'Your previous request for an export of personal data has expired.', 'buddypress' ); ?></p>
							<p><?php esc_html_e( 'Please click on the button below to make a new request.', 'buddypress' ); ?></p>

							<input type="hidden" name="bp-data-export-delete-request-nonce" value="<?php echo wp_create_nonce( 'bp-data-export-delete-request' ); ?>" />
							<button type="submit" name="bp-data-export-nonce" value="<?php echo wp_create_nonce( 'bp-data-export' ); ?>"><?php esc_html_e( 'Request new data export', 'buddypress' ); ?></button>

						<?php endif; ?>

					<?php elseif ( 'request-confirmed' === $request->status ) : ?>

						<p>Your previous data request did not complete.</p>
						<p><?php esc_html_e( 'Please click on the button below to make a new request.', 'buddypress' ); ?></p>

						<button type="submit" name="bp-data-export-nonce" value="<?php echo wp_create_nonce( 'bp-data-export' ); ?>">Generate personal data export</button>

					<?php endif; ?>

				<?php else : ?>

					<p><?php esc_html_e( 'You can request an export of your personal data, containing the following items if applicable:', 'buddypress' ); ?></p>

					<?php bp_settings_data_exporter_items(); ?>

					<p><?php esc_html_e( 'If you want to make a request, please click on the button below:', 'buddypress' ); ?></p>

					<button type="submit" name="bp-data-export-nonce" value="<?php echo wp_create_nonce( 'bp-data-export' ); ?>"><?php esc_html_e( 'Request personal data export', 'buddypress' ); ?></button>

				<?php endif; ?>

				<!--
				<h2 class="bp-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Data Erase', 'buddypress' );
				?></h2>

				<p>You can make a request to erase the following type of data from the site:</p>

				<p>If you want to make a request, please click on the button below:</p>

					<form id="bp-data-erase" method="post">
						<button type="submit" name="bp-data-erase-nonce" value="<?php echo wp_create_nonce( 'bp-data-erase' ); ?>">Request data erasure</button>
					</form>
				-->

			</div>
		</div>
	</form>
</div>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' );
