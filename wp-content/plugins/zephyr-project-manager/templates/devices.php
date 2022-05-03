<?php

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Api\Callbacks\AdminCallbacks;
	use Inc\Base\BaseController;

	$zpm_base = new AdminCallbacks();
	$devices = maybe_unserialize( get_option( 'zpm_devices', array() ) );


?>

<div class="zpm_settings_wrap">
	<?php $zpm_base->get_header(); ?>
	<div id="zpm_container" class="zpm_custom_fields">
		<h3 class="zpm-info-title"><?php _e( 'Linked Devices', 'zephyr-project-manager' ); ?> <small class="zpm-heading-subtext">- <?php _e( 'Displays all your linked mobile devices using the Zephyr Project Manager mobile app', 'zephyr-project-manager' ); ?></small></h3>
		
		<?php if (sizeof( $devices ) <= 0) : ?>
			<p class="zpm-no-devices-notice">
				<?php printf( __( 'There are no devices linked to your website. You can download the Android app from the Play Store %s here to start managing your projects from your device and get more done. %s', 'zephyr-project-manager' ), '<a target="_blank" href="https://play.google.com/store/apps/details?id=com.zephyr.dylank.zephyrprojectmanager">', '</a>' ) ?></p>
		<?php endif; ?>

		<div id="zpm-linked-device-list" class="zpm_list">
				<?php foreach ( $devices as $device ) : ?>
					<?php $linked_user = BaseController::get_member( $device[ 'linked_to' ] ); ?>

					<div class="zpm-linked-device">
						<div class="zpm-linked-device-card">
							<h3 class="zpm-device-name"><?php echo $device['name']; ?></h3>
							<h4 class="zpm-device-id"><?php echo $device['id']; ?></h4>
							<div class="zpm-device-user">
								<span class="zpm-device-user-name"><?php echo $linked_user['name']; ?></span>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
	</div>
	<?php $zpm_base->get_footer(); ?>
</div>