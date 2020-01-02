<?php defined( 'ABSPATH' ) || exit; ?>

<div id="su_admin_shortcodes" class="wrap su-admin-shortcodes">

	<h1><?php $this->the_page_title(); ?></h1>

	<div class="su-admin-shortcodes-single">

		<!-- "Go back" button -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-back">
			<a href="<?php echo esc_attr( $this->get_component_url() ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt"></span>
				<?php esc_html_e( 'Back to shortcodes list', 'shortcodes-ultimate' ); ?>
			</a>
		</div>
		<!-- /"Go back" button -->

		<?php $this->single_shortcode_page_content(); ?>

		<!-- "Go back" button -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-back">
			<a href="<?php echo esc_attr( $this->get_component_url() ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt"></span>
				<?php esc_html_e( 'Back to shortcodes list', 'shortcodes-ultimate' ); ?>
			</a>
		</div>
		<!-- /"Go back" button -->

	</div>

</div>
