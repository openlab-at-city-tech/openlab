<?php
/**
 * Welcome Related Functions
 *
 * @package BadgeOS
 * @subpackage Admin
 * @author LearningTimes, LLC
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com
 */

/**
 * Get addons data from badgeos.org.
 *
 * @return json
 */
function badgeos_addons_json_data() {

	$request = wp_remote_get( 'https://badgeos.org/wp-json/addons/get' );

	if ( is_wp_error( $request ) ) {
		return false;
	}

	$body = wp_remote_retrieve_body( $request );

	return json_decode( $body );
}

/**
 * Welcome Page Code.
 */
function badgeos_welcome_page() {

	global $wpdb;
	wp_enqueue_style( 'badgeos-jquery-slick-styles' );
	wp_enqueue_style( 'badgeos-jquery-slick-theme-styles' );
	wp_enqueue_script( 'badgeos-jquery-slick-js' );
	wp_enqueue_script( 'badgeos-jquery-welcome-js' );
	$root_url = badgeos_get_directory_url();
	$assets   = badgeos_assets_data();
	$addons   = badgeos_addons_json_data();
	?>
		<div class="badgeos-welcome-container">
				<div class="badgeos-welcome-site-inner">
					<div class="badgeos_dashboard_heading">
						<img src="<?php echo esc_url( $root_url . 'images/badges_icon.png' ); ?>">
						<div class="badgeos_dashboard_title">
							<h1><?php esc_attr_e( 'Welcome to BadgeOS', 'badgeos' ); ?></h1>
							<span><?php esc_attr_e( 'Version', 'badgeos' ); ?> <?php echo esc_attr( BadgeOS::$version ); ?></span>
						</div>
					</div>
					<?php if ( isset( $addons ) && is_array( $addons ) && count( $addons ) > 0 ) { ?>
						<div class="badgeos_welcome_addons_panel">
							<div class="badgeos_addons_panel_title">
								<h3><?php esc_attr_e( 'Most Popular Add-ons', 'badgeos' ); ?></h3>
							</div>
							<div class="badgeos_welcome_addons_list">
								<div class="badgeos_welcome_addons_list_ul slider multiple-items">
									<?php foreach ( $addons as $key => $addon ) { ?>
										<?php
										if ( 'badgeos' === strtolower( $addon->title ) ) {
											continue;
										}
										?>
										<div class="multiple">
											<div class="badgeos_welcome_addon">
												<div class="badgeos_addon_welcome_thumb">
													<img src="<?php echo esc_url( $addon->thumb ); ?>">
													<div class="badgeos_addon_welcome_btn">
														<a href="<?php echo esc_url( $addon->link ); ?>"><?php esc_attr_e( 'Read More', 'badgeos' ); ?></a>
													</div>
												</div>
												<div class="badgeos_welcome_on_content">
													<div class="badgeos_add-on_name"><?php echo esc_attr( $addon->title ); ?></div>
													<div class="badgeos_add_on_desc">
														<p><?php echo esc_attr( $addon->content ); ?></p>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if ( isset( $assets ) && is_object( $assets ) ) { ?>
						<div class="badgeos_assets_panel badgeos_assets">
							<div class="badgeos_assets_panel_title">
								<h3><?php esc_attr_e( 'Assets', 'badgeos' ); ?></h3>
							</div>
							<div class="badgeos_assets_list slider multiple-items">
								<?php foreach ( $assets as $key => $asset ) { ?>
									<div class="multiple">
										<?php if ( 'Yes' === $asset->active ) { ?>
											<div class="badgeos_asset">
												<img src="<?php echo esc_url( $asset->image ); ?>" alt="<?php echo esc_attr( $asset->title ); ?>" >
												<div class="badgeos_asset_content">
													<div class="badgeos_asset_name"><?php echo esc_attr( $asset->title ); ?></div>
													<div class="badgeos-assets-message-divs">
														<div class="badgeos_download_asset_success_message" style="display:none"><?php esc_attr_e( 'Thank you for downloading the pack! The icons are in your media files and you can use them for your badges, points, or ranks', 'badgeos' ); ?></div>
														<div class="badgeos_download_asset_failed_message" style="display:none"></div>
													</div>
													<div class="badgeos_asset_desc">
														<input type="hidden" name="badgeos_assets_id" class="badgeos_assets_id" id="badgeos_assets_id" value="<?php echo esc_attr( $key ); ?>" />
														<button class="btn_badgeos_download_assets " data-ajax_url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
															<?php esc_attr_e( 'Download', 'badgeos' ); ?>
														</button>
														<img id="btn_badgeos_download_assets_loader" style="width:16px;background-color:#000000; visibility:hidden;" src="<?php echo esc_url( $root_url . '/images/welcome-ajax-loader.gif' ); ?>" />
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
					<div class="clearfix"></div>
					<div class="badgeos_quick_links">
						<h3><?php esc_attr_e( 'Quick Links', 'badgeos' ); ?></h3>
						<hr>
						<div class="badgeos_quick_links_content">
							<ul>
								<li><?php esc_attr_e( 'Version', 'badgeos' ); ?> <?php echo esc_attr( BadgeOS::$version ); ?></li>
								<li><?php esc_attr_e( 'Need help? Get support on our public forum', 'badgeos' ); ?> <a target="_blank" href="https://wordpress.org/support/plugin/badgeos/"><?php esc_attr_e( 'here', 'badgeos' ); ?></a></li>
								<li><?php esc_attr_e( 'Looking for help, documentation, tutorials and more? Check out our', 'badgeos' ); ?> <a target="_blank" href="https://badgeos.org/docs/"><?php esc_attr_e( 'documentation', 'badgeos' ); ?></a></li>
								<li><?php esc_attr_e( 'Join our', 'badgeos' ); ?> <a target="_blank" href="https://www.facebook.com/groups/1899542540365937"><?php esc_attr_e( 'Facebook group', 'badgeos' ); ?></a> <?php esc_attr_e( 'to receive community support', 'badgeos' ); ?></li>
							</ul>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="badgeos_please_rate">
						<h3><?php esc_attr_e( 'Please rate us', 'badgeos' ); ?></h3>
						<hr>
						<p><?php esc_attr_e( 'Please rate BadgeOS', 'badgeos' ); ?> <a href="https://wordpress.org/plugins/badgeos/">★★★★★</a> on <a href="https://wordpress.org/plugins/badgeos/"><?php esc_attr_e( 'WordPress.org', 'badgeos' ); ?></a> <?php esc_attr_e( 'to help us continue growing and improving! Thank you from the BadgeOS team!', 'badgeos' ); ?></p>
					</div>
					<div class="clearfix"></div>
					<div class="badgeos_please_rate">
						<h3><?php esc_attr_e( 'Need any help?', 'badgeos' ); ?></h3>
						<hr>
						<p><?php esc_attr_e( 'Need any help configuring this plugin or designing a reward system for your needs? Get in touch with an expert!', 'badgeos' ); ?></p>
						<p><a class="button button-primary badgeos-btn-need-help" href="https://badgeos.org/services/game-reward-design/" aria-label="Activate bbPress" data-name="bbPress 2.6.5">Get Help</a></p>
					</div>
				</div>
			</div>
	<?php

}
