<?php defined( 'ABSPATH' ) or exit; ?>

<?php
$shortcode = $this->get_single_shortcode();
$options   = $this->get_single_shortcode_options();
$prefix    = $this->get_shortcodes_prefix();
?>

<h1><?php $this->the_page_title(); ?></h1>

<div class="su-admin-shortcodes-single">

	<!-- "Go back" button -->
	<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-back">
		<a href="<?php echo $this->get_component_url(); ?>" class="button"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e( 'Back to shortcodes list', 'shortcodes-ultimate' ); ?></a>
	</div>
	<!-- /"Go back" button -->

	<?php if ( $shortcode ) : ?>

		<!-- Description section -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-description wp-clearfix">
			<span><?php $this->shortcode_image( $shortcode, 60 ); ?></span>
			<h2><?php echo $shortcode['name']; ?></h2>
			<p>
				<?php echo $shortcode['desc']; ?>
				<?php if ( isset( $shortcode['article'] ) ) : ?>
					<br><a href="<?php echo esc_url( $shortcode['article'] ); ?>" target="_blank"><strong><?php _e( 'Shortcode documentation', 'shortcodes-ultimate' ); ?></strong></a>.
				<?php endif; ?>
			</p>
		</div>
		<!-- /Description section -->

		<!-- Preview section -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-preview">
			<h2><?php _e( 'Preview', 'shortcodes-ultimate' ); ?></h2>
			<div class="su-admin-shortcodes-single-preview-content wp-clearfix">
				<?php echo do_shortcode( $this->get_shortcode_code( $shortcode['id'] ) ); ?>
			</div>
		</div>
		<!-- /Preview section -->

		<!-- Shortcode section -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-code">
			<h2><?php _e( 'Shortcode', 'shortcodes-ultimate' ); ?></h2>
			<pre contenteditable="true" class=""><code><?php echo str_replace( "\t", '  ', esc_html( $this->get_shortcode_code( $shortcode['id'] ) ) ); ?></code></pre>
		</div>
		<!-- /Shortcode section -->

		<!-- Options section -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-options">
			<h2><?php _e( 'Shortcode options', 'shortcodes-ultimate' ); ?></h2>

			<?php foreach( $options as $table ) : ?>

				<div class="su-admin-shortcodes-single-options-table">

					<?php if ( count( $options ) > 1 ) : ?>
						<h4>[<?php echo $prefix, $table['id']; ?>]</h4>
					<?php endif; ?>

					<?php if ( !is_array( $table['atts'] ) || !count( $table['atts'] ) ) : ?>
						<p class="description"><?php _e( 'This shortcode do not have options', 'shortcodes-ultimate' ); ?></p>
					<?php else : ?>

						<table class="widefat striped">

							<tr>
								<th><?php _e( 'Option name', 'shortcodes-ultimate' ); ?></th>
								<th><?php _e( 'Possible values', 'shortcodes-ultimate' ); ?></th>
								<th><?php _e( 'Default value', 'shortcodes-ultimate' ); ?></th>
							</tr>

							<?php foreach( $table['atts'] as $attr_id => $attr ) : ?>
								<tr>
									<td style="max-width:360px">
										<strong class="wp-ui-text-highlight"><?php echo $attr_id; ?></strong><br>
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

		<!-- "Go back" button -->
		<div class="su-admin-shortcodes-single-section su-admin-shortcodes-single-back">
			<a href="<?php echo $this->get_component_url(); ?>" class="button"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e( 'Back to shortcodes list', 'shortcodes-ultimate' ); ?></a>
		</div>
		<!-- /"Go back" button -->

	<?php endif; // if ( $shortcode ) ?>

</div>
