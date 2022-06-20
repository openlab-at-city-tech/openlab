<div class="ekit-onboard-main-header">
	<h1 class="ekit-onboard-main-header--title"><strong><?php echo esc_html__( 'Take your website to the next level', 'elementskit-lite' ); ?></strong></h1>
	<p class="ekit-onboard-main-header--description"><?php echo esc_html__( 'We have some plugins you can install to get most from WordPress.', 'elementskit-lite' ); ?></p>
	<p class="ekit-onboard-main-header--description"><?php echo esc_html__( 'These are absolute FREE to use.', 'elementskit-lite' ); ?></p>
</div>
<div class="ekit-onboard-plugin-list">
	<div class="attr-row">
		<?php
		$pluginStatus = \ElementsKit_Lite\Libs\Framework\Classes\Plugin_Status::instance();
		$plugins      = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( 'settings', array() );
		?>
		<div class="attr-col-lg-4">
			<div class="ekit-onboard-single-plugin badge--featured">
				<label>
					<img class="ekit-onboard-single-plugin--logo" src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shopengine-logo.png" alt="ShopEngine">
					<p class="ekit-onboard-single-plugin--description"><?php echo esc_html__( 'Completely customize your  WooCommerce WordPress', 'elementskit-lite' ); ?></p>
					<?php $plugin = $pluginStatus->get_status( 'shopengine/shopengine.php' ); ?>
					<a data-plugin_status="<?php echo esc_attr( $plugin['status'] ); ?>" data-activation_url="<?php echo esc_url( $plugin['activation_url'] ); ?>" href="<?php echo esc_url( $plugin['installation_url'] ); ?>" class="ekit-pro-btn ekit-onboard-single-plugin--install_plugin <?php echo $plugin['status'] == 'activated' ? 'activated' : ''; ?>"><?php echo esc_html( $plugin['title'], 'elementskit-lite' ); ?></a>
				</label>
			</div>
		</div>
		<div class="attr-col-lg-4">
			<div class="ekit-onboard-single-plugin">
				<label>
					<img class="ekit-onboard-single-plugin--logo" src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/metform-logo.png" alt="Metform">
					<p class="ekit-onboard-single-plugin--description"><?php echo esc_html__( 'Most flexible drag-and-drop form builder', 'elementskit-lite' ); ?></p>
					<?php $plugin = $pluginStatus->get_status( 'metform/metform.php' ); ?>
					<a data-plugin_status="<?php echo esc_attr( $plugin['status'] ); ?>" data-activation_url="<?php echo esc_url( $plugin['activation_url'] ); ?>" href="<?php echo esc_url( $plugin['installation_url'] ); ?>" class="ekit-pro-btn ekit-onboard-single-plugin--install_plugin <?php echo $plugin['status'] == 'activated' ? 'activated' : ''; ?>"><?php echo esc_html( $plugin['title'], 'elementskit-lite' ); ?></a>
				</label>
			</div>
		</div>
		<div class="attr-col-lg-4">
			<div class="ekit-onboard-single-plugin">
				<label>
					<img class="ekit-onboard-single-plugin--logo" src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/wp-social-logo.png" alt="WpSocial">
					<p class="ekit-onboard-single-plugin--description"><?php echo esc_html__( 'Integrate all your social media to your website', 'elementskit-lite' ); ?></p>
					<?php $plugin = $pluginStatus->get_status( 'wp-social/wp-social.php' ); ?>
					<a data-plugin_status="<?php echo esc_attr( $plugin['status'] ); ?>" data-activation_url="<?php echo esc_url( $plugin['activation_url'] ); ?>" href="<?php echo esc_url( $plugin['installation_url'] ); ?>" class="ekit-pro-btn ekit-onboard-single-plugin--install_plugin <?php echo $plugin['status'] == 'activated' ? 'activated' : ''; ?>"><?php echo esc_html( $plugin['title'], 'elementskit-lite' ); ?></a>
				</label>
			</div>
		</div>
		<div class="attr-col-lg-4">
			<div class="ekit-onboard-single-plugin">
				<label>
					<img class="ekit-onboard-single-plugin--logo" src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/ultimate-review-logo.png" alt="UltimateReview">
					<p class="ekit-onboard-single-plugin--description"><?php echo esc_html__( 'Integrate various styled review system in your website', 'elementskit-lite' ); ?></p>
					<?php $plugin = $pluginStatus->get_status( 'wp-ultimate-review/wp-ultimate-review.php' ); ?>
					<a data-plugin_status="<?php echo esc_attr( $plugin['status'] ); ?>" data-activation_url="<?php echo esc_url( $plugin['activation_url'] ); ?>" href="<?php echo esc_url( $plugin['installation_url'] ); ?>" class="ekit-pro-btn ekit-onboard-single-plugin--install_plugin <?php echo $plugin['status'] == 'activated' ? 'activated' : ''; ?>"><?php echo esc_html( $plugin['title'], 'elementskit-lite' ); ?></a>
				</label>
			</div>
		</div>
		<div class="attr-col-lg-4">
			<div class="ekit-onboard-single-plugin">
				<label>
					<img class="ekit-onboard-single-plugin--logo" src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/fundraising-logo.png" alt="Fundraising">
					<p class="ekit-onboard-single-plugin--description"><?php echo esc_html__( 'Integrate various styled review system in your website', 'elementskit-lite' ); ?></p>
					<?php $plugin = $pluginStatus->get_status( 'wp-fundraising-donation/wp-fundraising.php' ); ?>
					<a data-plugin_status="<?php echo esc_attr( $plugin['status'] ); ?>" data-activation_url="<?php echo esc_url( $plugin['activation_url'] ); ?>" href="<?php echo esc_url( $plugin['installation_url'] ); ?>" class="ekit-pro-btn ekit-onboard-single-plugin--install_plugin <?php echo $plugin['status'] == 'activated' ? 'activated' : ''; ?>"><?php echo esc_html( $plugin['title'], 'elementskit-lite' ); ?></a>
				</label>
			</div>
		</div>
	</div>
</div>
<div class="ekit-onboard-pagination">
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn prev" data-plugin_status="<?php echo esc_attr($plugin['status']); ?>" data-activation_url="<?php echo esc_url($plugin['activation_url']); ?>" href="#"><i class="icon icon-arrow-left"></i><?php echo esc_html__( 'Back', 'elementskit-lite' ); ?></a>
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn next" data-plugin_status="<?php echo esc_attr($plugin['status']); ?>" data-activation_url="<?php echo esc_url($plugin['activation_url']); ?>" href="#"><?php echo esc_html__( 'Next Step', 'elementskit-lite' ); ?></a>
</div>
<div class="ekit-onboard-shapes">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-06.png" alt="" class="shape-06">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-10.png" alt="" class="shape-10">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-11.png" alt="" class="shape-11">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-12.png" alt="" class="shape-12">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-13.png" alt="" class="shape-13">
</div>
