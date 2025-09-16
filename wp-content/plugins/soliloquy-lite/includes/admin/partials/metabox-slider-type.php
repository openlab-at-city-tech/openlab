<?php
/**
 * Slider Type Template
 *
 * @since 2.5.0
 * @package SoliloquyWP Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

$common       = Soliloquy_Common_Admin_Lite::get_instance();
$upgrade_link = $common->get_upgrade_link();
?>
<div id="soliloquy-uploader">

	<div id="soliloquy-slider-type-tabs">
		<a data-soliloquy-tab class="soliloquy-type-tab soliloquy-icon-soliloquy <?php echo ( ( $data['instance']->get_config( 'type', $data['instance']->get_config_default( 'type' ) ) === 'default' ) ? ' soliloquy-tab-nav-active' : '' ); ?>" href="#" data-tab-id="soliloquy-native">
			<input id="soliloquy-type-default" type="radio" name="_soliloquy[type]" value="default" <?php checked( $data['instance']->get_config( 'type', $data['instance']->get_config_default( 'type' ) ), 'default' ); ?> />
			<?php esc_html_e( 'Native Slider', 'soliloquy' ); ?></a>
		<a data-soliloquy-tab class="soliloquy-type-tab <?php echo ( ( $data['instance']->get_config( 'type', $data['instance']->get_config_default( 'type' ) ) !== 'default' ) ? ' soliloquy-tab-nav-active' : '' ); ?>" href="#" data-tab-id="soliloquy-external"><?php esc_html_e( 'External Slider', 'soliloquy' ); ?></a>

	</div>

	<div class="soliloquy-tab-container">

		<div id="soliloquy-native" class="soliloquy-tab <?php echo ( ( $data['instance']->get_config( 'type', $data['instance']->get_config_default( 'type' ) ) === 'default' ) ? 'soliloquy-tab-active' : '' ); ?>">

			<!-- Errors -->
			<div id="soliloquy-upload-error"></div>

			<!-- WP Media Upload Form -->
			<?php media_upload_form(); ?>
			<script type="text/javascript">
				var post_id = <?php echo esc_js( $data['post']->ID ); ?>, shortform = 3;
			</script>
			<input type="hidden" name="post_id" id="post_id" value="<?php echo esc_attr( $data['post']->ID ); ?>" />

		</div>

		<div id="soliloquy-external" class="soliloquy-tab <?php echo ( ( $data['instance']->get_config( 'type', $data['instance']->get_config_default( 'type' ) ) !== 'default' ) ? 'soliloquy-tab-active' : '' ); ?>">

			<h2 class="soliloquy-type-label"><span><?php esc_html_e( 'Create Dynamic Sliders with Soliloquy', 'soliloquy' ); ?></span></h2>

			<ul id="soliloquy-types-nav" class="soliloquy-clear">

				<li id="soliloquy-type-fc">
					<a href="<?php echo esc_url( $upgrade_link ); ?>">
						<label for="soliloquy-type-fc">
							<input id="soliloquy-type-fc" type="radio" name="_soliloquy[type]" value="fc">
							<div class="icon"></div>
							<div class="title"><?php esc_attr_e( 'Featured Content', 'soliloquy' ); ?></div>
						</label>
					</a>
				</li>

				<li id="soliloquy-type-instagram">
					<a href="<?php echo esc_url( $upgrade_link ); ?>">

						<label for="soliloquy-type-instagram">
							<input id="soliloquy-type-instagram" type="radio" name="_soliloquy[type]" value="instagram">
							<div class="icon"></div>
							<div class="title"><?php esc_attr_e( 'Instagram', 'soliloquy' ); ?></div>
						</label>
					</a>
				</li>

				<li id="soliloquy-type-wc">
					<a href="<?php echo esc_url( $upgrade_link ); ?>">

						<label for="soliloquy-type-wc">
							<input id="soliloquy-type-wc" type="radio" name="_soliloquy[type]" value="wc">
							<div class="icon"></div>
							<div class="title"><?php esc_attr_e( 'WooCommerce', 'soliloquy' ); ?></div>
						</label>
					</a>
				</li>
			</ul>

			<p class="soliloquy-upsell"><?php esc_html_e( 'Soliloquy Pro allows you to build sliders from Instagram photos, images from your posts, and more.', 'soliloquy' ); ?></p>

			<a href="<?php echo esc_url( $upgrade_link ); ?>" class="button button-soliloquy "><?php esc_attr_e( 'Click here to upgrade', 'soliloquy' ); ?></a>

		</div>

	</div>

</div>
