<?php
	/**
	* Page header
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Zephyr;
	use Inc\Pages\Admin;
	use Inc\Core\Utillities;
	use Inc\Base\BaseController;

	$Admin = new Admin();
	$Base = new BaseController();

	$current_user = wp_get_current_user();
	$user_id = $current_user->data->ID;
	$user_info = $Base->get_project_manager_user($user_id);
	$page_title = (!$page_title) ? get_admin_page_title() : $page_title;
	$frontend_settings = get_option('zpm_frontend_settings');
	$zpm_used = get_option('zpm_used');
	
?>

<div id="zpm_modal_background" class="zpm_modal_background"></div>

<div class="zpm_top_bar">
	<div class="zpm_header">

		<h3 class="zpm_header_text">
			<span id="zpm_hide_wp_adminbar">
				<i class="dashicons dashicons-arrow-left-alt2"></i>
			</span>

			<span id="zpm-zephyr-info" class="zpm-dismiss-notice" data-notice-id="<?php echo Zephyr::getPluginVersion(); ?>"><i class="dashicons dashicons-info"></i>
				<span id="zpm-zephyr-details-tooltip" class="zpm-tooltip-window">
					<h3 class="zpm-info__header"><?php _e( 'Whats new in Zephyr Project Manager', 'zephyr-project-manager' ); ?></h3>
					<div class="zpm-info__plugin-features">
						<?php Utillities::get_new_features(); ?>
					</div>
					<div class="zpm-info__plugin-versions">
						<p class="zpm-plugin-version version-basic">Zephyr Basic V<?php echo Zephyr::getPluginVersion(); ?></p>
						<?php if (Zephyr::isPro()) : ?>
							<p class="zpm-plugin-version version-pro">Zephyr Pro V<?php echo Zephyr::getProPluginVersion(); ?></p>
						<?php endif; ?>	
						</div>
				</span>

				</span><?php echo apply_filters( 'zpm_sidebar_icons', '' ); ?><?php echo $page_title; ?>
			<?php echo apply_filters( 'zpm_header_taskbar', '' ); ?>
		</h3>

		<button id="zpm_add_new_btn" zpm-ripple="ripple" class="<?php echo $quickbutton_class; ?>" data-zpm-dropdown-toggle="zpm_add_new_dropdown"><img src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon_plus.png'; ?>"/></button>

		<ul id="zpm_add_new_dropdown" class="zpm_fancy_dropdown">
			<?php ob_start(); ?>

				<?php if (Utillities::can_create_projects()) : ?>
					<li class="zpm_fancy_item zpm_fancy_divider" id="zpm_create_quickproject"><?php _e( 'New Project', 'zephyr-project-manager' ); ?></li>
				<?php endif; ?>

				<?php if (Utillities::can_create_tasks()) : ?>
					<li class="zpm_fancy_item" id="zpm_quickadd_task"><?php _e( 'New Task', 'zephyr-project-manager' ); ?></li>
				<?php endif; ?>

				<li class="zpm_fancy_item" id="zpm_new_quick_category"><?php _e( 'New Category', 'zephyr-project-manager' ); ?></li>
				<li class="zpm_fancy_item" id="zpm_new_quick_file"><?php _e( 'New File', 'zephyr-project-manager' ); ?></li>
				<li class="zpm_fancy_item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_settings')); ?>" title="<?php _e( 'Settings', 'zephyr-project-manager' ); ?>"><?php _e( 'Settings', 'zephyr-project-manager' ); ?></a></li>
				<?php if (!BaseController::is_pro()) : ?>
					<li id="zpm_premium_link" class="zpm_fancy_item zpm_fancy_divider_top"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_purchase_premium')); ?>" title="<?php _e( 'Premium', 'zephyr-project-manager' ); ?>"><?php _e( 'Get Premium', 'zephyr-project-manager' ); ?></a></li>
				<?php endif; ?>
			<?php
			$html = ob_get_clean();
			echo apply_filters('zpm_quickmenu_options', $html);
			echo apply_filters('zpm_after_quickmenu', '');
			?>
		</ul>
	</div>
</div>

<div id="zpm_admin_notice_section">
	<?php

	do_action( 'zpm-message' );
	
	if( empty( get_option( 'zpm_welcome_notice_dismissed' ) ) ) {
		$Admin->welcome_notice();
	}
	?>
	<?php
	if( empty( get_option( 'zpm_review_notice_dismissed' ) ) && $zpm_used > 2) {
		//$Admin->review_notice();
	}

	do_action( 'zpm_required_actions' );
	?>
</div>