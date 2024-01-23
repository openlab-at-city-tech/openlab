<?php
/**
 * Functions for building out the Reading Time settings page.
 *
 * @package Reading_Time_WP
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $reading_time_wp;

$rt_reading_time_options = get_option( 'rt_reading_time_options' );

$rtwp_post_type_args = array(
	'public' => true,
);

$rtwp_post_type_args = apply_filters( 'rtwp_post_type_args', $rtwp_post_type_args );

$rtwp_post_types = get_post_types( $rtwp_post_type_args, 'object' );

if ( isset( $_POST['rt_reading_time_hidden'] ) && check_admin_referer( 'reading_time_settings' ) && 'Y' == $_POST['rt_reading_time_hidden'] ) {
	// Form data sent.
	$reading_time_label            = isset( $_POST['rt_reading_time_label'] ) ? wp_kses( wp_unslash( $_POST['rt_reading_time_label'] ), $reading_time_wp->rtwp_kses ) : '';
	$reading_time_postfix          = isset( $_POST['rt_reading_time_postfix'] ) ? wp_kses( wp_unslash( $_POST['rt_reading_time_postfix'] ), $reading_time_wp->rtwp_kses ) : '';
	$reading_time_postfix_singular = isset( $_POST['rt_reading_time_postfix_singular'] ) ? wp_kses( wp_unslash( $_POST['rt_reading_time_postfix_singular'] ), $reading_time_wp->rtwp_kses ) : '';
	$reading_time_wpm              = isset( $_POST['rt_reading_time_wpm'] ) ? sanitize_text_field( wp_unslash( $_POST['rt_reading_time_wpm'] ) ) : '';
	$reading_time_check            = isset( $_POST['rt_reading_time_check'] ) ? true : false;
	$reading_time_check_excerpt    = isset( $_POST['rt_reading_time_check_excerpt'] ) ? true : false;
	$reading_time_exclude_images   = isset( $_POST['rt_reading_time_images'] ) ? true : false;
	$reading_time_shortcodes       = isset( $_POST['rt_reading_time_shortcodes'] ) ? true : false;

	if ( isset( $_POST['rt_reading_time_post_types'] ) ) {
		foreach ( $_POST['rt_reading_time_post_types'] as $key => $value ) {
			if ( $value ) {
				$reading_time_post_types[ sanitize_text_field( $key ) ] = true;
			}
		}
	}

	$update_options = array(
		'label'              => $reading_time_label,
		'postfix'            => $reading_time_postfix,
		'postfix_singular'   => $reading_time_postfix_singular,
		'wpm'                => (float) $reading_time_wpm,
		'before_content'     => $reading_time_check,
		'before_excerpt'     => $reading_time_check_excerpt,
		'exclude_images'     => $reading_time_exclude_images,
		'post_types'         => $reading_time_post_types,
		'include_shortcodes' => $reading_time_shortcodes,
	);

	update_option( 'rt_reading_time_options', $update_options );

	?>
	<div class="updated"><p><strong><?php echo esc_html( __( 'Options saved.', 'reading-time-wp' ) ); ?></strong></p></div>
	<?php
} else {
	// Normal page display.
	$reading_time_label            = isset( $rt_reading_time_options['label'] ) ? esc_html( $rt_reading_time_options['label'] ) : '';
	$reading_time_postfix          = isset( $rt_reading_time_options['postfix'] ) ? esc_html( $rt_reading_time_options['postfix'] ) : '';
	$reading_time_postfix_singular = isset( $rt_reading_time_options['postfix_singular'] ) ? esc_html( $rt_reading_time_options['postfix_singular'] ) : '';
	$reading_time_wpm              = isset( $rt_reading_time_options['wpm'] ) ? esc_html( $rt_reading_time_options['wpm'] ) : '';
	$reading_time_check            = isset( $rt_reading_time_options['before_content'] ) ? $this->rt_convert_boolean( $rt_reading_time_options['before_content'] ) : false;
	$reading_time_check_excerpt    = isset( $rt_reading_time_options['before_excerpt'] ) ? $this->rt_convert_boolean( $rt_reading_time_options['before_excerpt'] ) : false;
	$reading_time_exclude_images   = isset( $rt_reading_time_options['exclude_images'] ) ? $rt_reading_time_options['exclude_images'] : false;

	if ( isset( $rt_reading_time_options['post_types'] ) ) {
		$reading_time_post_types = $rt_reading_time_options['post_types'];
    } elseif ( !isset( $rt_reading_time_options['post_types'] ) || NULL === $rt_reading_time_options['post_types'] ) {
		$reading_time_post_types = array();
	} else {
		// set defaults that have always been there for backwards compat until users set their own.
		$reading_time_post_types = array();

		foreach ( $rtwp_post_types as $post_type_option ) {
			if ( 'attachment' === $post_type_option->name ) {
				continue;
			}
			$reading_time_post_types[ $post_type_option->name ] = true;
		}
	}
	if ( isset( $rt_reading_time_options['include_shortcodes'] ) ) {
		$reading_time_shortcodes = $rt_reading_time_options['include_shortcodes'];
	} else {
		$reading_time_shortcodes = false;
	}
}
?>

<div class="wrap">
	<?php echo '<h2>' . esc_html__( 'Reading Time WP Settings', 'reading-time-wp' ) . '</h2>'; ?>

	<form name="rt_reading_time_form" method="POST">
		<input type="hidden" name="rt_reading_time_hidden" value="Y">
		<?php wp_nonce_field( 'reading_time_settings' ); ?>
		<?php echo '<h4>' . esc_html__( 'Reading Time Settings', 'reading-time-wp' ) . '</h4>'; ?>

		<p><?php esc_html_e( 'Reading time label: ', 'reading-time-wp' ); ?><input type="text" name="rt_reading_time_label" value="<?php echo esc_attr( $reading_time_label ); ?>" size="20"><?php esc_html_e( ' This value appears before the reading time. Leave blank for none.', 'reading-time-wp' ); ?></p>

		<p><?php esc_html_e( 'Reading time postfix: ', 'reading-time-wp' ); ?><input type="text" name="rt_reading_time_postfix" value="<?php echo esc_attr( $reading_time_postfix ); ?>" size="20"><?php esc_html_e( ' This value appears after the reading time. Leave blank for none.', 'reading-time-wp' ); ?></p>
		<p><?php esc_html_e( 'Reading time postfix singular: ', 'reading-time-wp' ); ?><input type="text" name="rt_reading_time_postfix_singular" value="<?php echo esc_attr( $reading_time_postfix_singular ); ?>" size="20"><?php esc_html_e( ' This value appears after the reading time, when lecture time is 1 minute.', 'reading-time-wp' ); ?></p>

		<p><?php esc_html_e( 'Words per minute: ', 'reading-time-wp' ); ?><input type="number" name="rt_reading_time_wpm" value="<?php echo esc_attr( (float) $reading_time_wpm ); ?>" size="20"><?php esc_html_e( ' (defaults to 300, the average reading speed for adults)', 'reading-time-wp' ); ?></p>

		<p><?php esc_html_e( 'Insert Reading Time before content: ', 'reading-time-wp' ); ?><input type="checkbox" name="rt_reading_time_check" <?php echo ( true == $reading_time_check ) ? 'checked' : ''; ?> size="20"></p>
		<p><?php esc_html_e( 'Insert Reading Time before excerpt: ', 'reading-time-wp' ); ?><input type="checkbox" name="rt_reading_time_check_excerpt" <?php echo ( true == $reading_time_check_excerpt ) ? 'checked' : ''; ?> size="20"></p>
		<p><?php esc_html_e( 'Exclude images from the reading time: ', 'reading-time-wp' ); ?><input type="checkbox" name="rt_reading_time_images" <?php echo ( true === $reading_time_exclude_images ) ? 'checked' : ''; ?> size="20"></p>
		<p><?php esc_html_e( 'Include shortcodes in the reading time: ', 'reading-time-wp' ); ?><input type="checkbox" name="rt_reading_time_shortcodes" <?php echo ( true === $reading_time_shortcodes ) ? 'checked' : ''; ?> size="20"></p>

		<h3><?php esc_html_e( 'Select Post Types to Display Reading Time On', 'reading-time-wp' ); ?></h3>

		<?php foreach ( $rtwp_post_types as $rtwp_post_type ) : ?>
			<p><?php echo esc_html__( 'Display on ', 'reading-time-wp' ) . esc_html( $rtwp_post_type->label ) . ': '; ?><input type="checkbox" name="rt_reading_time_post_types[<?php echo esc_attr( $rtwp_post_type->name ); ?>]" <?php echo ( isset( $reading_time_post_types[ $rtwp_post_type->name ] ) && true === $reading_time_post_types[ $rtwp_post_type->name ] ) ? 'checked' : ''; ?> size="20"></p>
		<?php endforeach; ?>

		<p class="submit">
		<input type="submit" name="Submit" value="<?php esc_html_e( 'Update Options', 'reading-time-wp' ); ?>" />
		</p>
	</form>

	<div class="rt-shortcode-hint">
		<p><?php echo wp_kses_post( __( 'Shortcode: <code>[rt_reading_time label="Reading Time:" postfix="minutes" postfix_singular="minute"]</code>', 'reading-time-wp' ) ); ?></p>
		<p><?php echo wp_kses_post( __( 'Or simply use <code>[rt_reading_time]</code> to return the number with no labels.', 'reading-time-wp' ) ); ?></p>
		<p><?php echo wp_kses_post( __( 'Want to insert the reading time into your theme? Use <code>do_shortcode(\'[rt_reading_time]\')</code>.', 'reading-time-wp' ) ); ?></p>
		<p><?php echo wp_kses_post( __( 'The shortcode shows the reading time for the current page/post by default. To show the reading time for another one, use the optional "post_id" attribute. For example - to show the reading time for post ID 123: <code>[rt_reading_time post_id="123"]</code>.', 'reading-time-wp' ) ); ?></p>
	</div>
</div>
