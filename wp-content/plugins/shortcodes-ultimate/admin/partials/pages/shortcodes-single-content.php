<?php defined( 'ABSPATH' ) || exit; ?>

<!-- Description section -->
<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-description wp-clearfix">
	<span><?php $this->shortcode_image( $data, 60 ); ?></span>
	<h2><?php echo esc_html( $data['name'] ); ?></h2>
	<p>
		<?php if ( isset( $data['desc'] ) ) : ?>
			<?php echo esc_html( $data['desc'] ); ?>
		<?php endif; ?>
		<?php if ( isset( $data['article'] ) ) : ?>
			<br><a href="<?php echo esc_url( $data['article'] ); ?>" target="_blank"><strong><?php esc_html_e( 'Shortcode documentation', 'shortcodes-ultimate' ); ?></strong></a>.
		<?php endif; ?>
	</p>
</div>
<!-- /Description section -->

<!-- Preview section -->
<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-preview">
	<h2><?php esc_html_e( 'Preview', 'shortcodes-ultimate' ); ?></h2>
	<div class="su-admin-shortcodes-single-preview-content wp-clearfix">
		<?php echo do_shortcode( $this->get_shortcode_code( $data['id'] ) ); ?>
	</div>
</div>
<!-- /Preview section -->

<!-- Shortcode section -->
<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-code">
	<h2><?php esc_html_e( 'Shortcode', 'shortcodes-ultimate' ); ?></h2>
	<pre contenteditable="true" class=""><code><?php echo str_replace( "\t", '  ', esc_html( $this->get_shortcode_code( $data['id'] ) ) ); ?></code></pre>
</div>
<!-- /Shortcode section -->

<!-- Options section -->
<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-options">
	<h2><?php esc_html_e( 'Shortcode options', 'shortcodes-ultimate' ); ?></h2>

	<?php foreach ( $this->get_single_shortcode_options() as $table ) : ?>

		<div class="su-admin-shortcodes-single-options-table">

			<?php if ( count( $this->get_single_shortcode_options() ) > 1 ) : ?>
				<h3>[<?php echo esc_html( su_get_shortcode_prefix() . $table['id'] ); ?>]</h3>
			<?php endif; ?>

			<?php if ( ! is_array( $table['atts'] ) || ! count( $table['atts'] ) ) : ?>
				<p class="description"><?php esc_html_e( 'This shortcode do not have options', 'shortcodes-ultimate' ); ?></p>
			<?php else : ?>

				<table class="widefat striped">

					<tr>
						<th><?php esc_html_e( 'Option name', 'shortcodes-ultimate' ); ?></th>
						<th><?php esc_html_e( 'Possible values', 'shortcodes-ultimate' ); ?></th>
						<th><?php esc_html_e( 'Default value', 'shortcodes-ultimate' ); ?></th>
					</tr>

					<?php foreach ( $table['atts'] as $attr_id => $attr ) : ?>
						<tr>
							<td style="max-width:360px">
								<strong><?php echo esc_html( $attr_id ); ?></strong><br>
								<small class="description"><?php echo $this->get_shortcode_description( $attr['desc'] ); ?></small>
							</td>
							<td><?php echo $this->get_possible_values( $attr ); ?></td>
							<td><?php echo $this->get_default_value( $attr ); ?></td>
						</tr>
					<?php endforeach; ?>

				</table>

			<?php endif; ?>

		</div>

	<?php endforeach; ?>

</div>
<!-- /Options section -->
