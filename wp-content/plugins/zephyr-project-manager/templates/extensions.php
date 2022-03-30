<?php

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Api\Callbacks\AdminCallbacks;
	use Inc\Base\BaseController;

	$zpm_base = new AdminCallbacks();
	$extensions = zpm_get_extensions();
?>

<div class="zpm_settings_wrap">
	<?php $zpm_base->get_header(); ?>
	<div id="zpm_container" class="zpm_custom_fields">
		<h3 class="zpm-info-title"><?php _e( 'Extensions', 'zephyr-project-manager' ); ?> <small class="zpm-heading-subtext">- <?php _e( 'Zephyr Project Manager add-ons to increase productivity and integrate with your favourite tools', 'zephyr-project-manager' ); ?></small></h3>

		<div id="zpm-extensions">
			<?php foreach($extensions as $extension) : ?>
				<div class="zpm-extension" style="background-color: <?php echo $extension['color']; ?>;">
					<p class="zpm-extension__title"><?php echo $extension['title']; ?></p>
					<?php if (!$extension['installed']) : ?>
						<p class="zpm-extension__description"><?php echo $extension['description']; ?></p>
						<div class="zpm-buttons__float-right">
							<a href="<?php echo $extension['link']; ?>" class="zpm_button zpm-button__white" style="color: <?php echo $extension['color']; ?> !important;"><?php _e( 'Get Now', 'zephyr-project-manager' ); ?></a>
						</div>
					<?php else: ?>
						<p class="zpm-extension__description zpm-extension__installed"><i class="fa fa-check"></i><?php _e( 'Installed', 'zephyr-project-manager' ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php $zpm_base->get_footer(); ?>
</div>