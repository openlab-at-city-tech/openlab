<?php
/**
 * Creative Commons widget class.
 *
 * @since 0.1.0
 */

/**
 * Creative Commons site widget.
 *
 * @since 0.1.0
 */
class CAC_Creative_Commons_Widget extends WP_Widget {
	/**
	 * Whether or not the widget has been registered yet.
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		parent::__construct(
			false,
			esc_html__( 'Creative Commons License', 'cac-creative-commons' ),
			array(
				'description'                 => esc_html__( 'Displays your chosen Creative Commons site license.', 'cac-creative-commons' ),
				'classname'                   => 'widget_cac_creative_commons',
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Do something when registering all widget instances of this widget class.
	 *
	 * @since 0.1.0
	 *
	 * @param integer $number Optional. The unique order number of this widget instance
	 *                        compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		parent::_register_one( $number );
		if ( $this->registered ) {
			return;
		}
		$this->registered = true;

		// Note this action is used to ensure the help text is added to the end.
		add_action( 'admin_head-widgets.php', array( __CLASS__, 'add_help_text' ) );
	}

	/**
	 * Display widget method.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget options.
	 */
	public function widget( $args, $instance ) {
		// Require our core functions.
		require_once __DIR__ . '/functions.php';

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$size  = ! empty( $instance['size'] ) ? $instance['size'] : 'normal';
		$text  = ! empty( $instance['text'] ) ? $instance['text'] : '';

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		cac_cc_license_link( array(
			'use_logo'  => true,
			'logo_size' => $size
		) );

		if ( ! empty( $text ) ) {
			$text = str_replace( '%license_link%', cac_cc_get_license_link(), $text );
			echo wpautop( strip_tags( $text, '<a>' ) );
		}

		echo $args['after_widget'];
	}

	/**
	 * Save widget options.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $new_instance New settings for this widget.
	 * @param  array $old_instance Old settings for this widget.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['size']  = in_array( $new_instance['size'], array( 'compact', 'normal' ) ) ? $new_instance['size'] : 'normal';
		$instance['text']  = sanitize_textarea_field( $new_instance['text'] );
		return $instance;
	}

	/**
	 * Widget options.
	 *
	 * @since 0.1.0
	 *
	 * @param array $instance Settings for this widget.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'size' => 'normal', 'text' => null ) );
		$title = $instance['title'];

		// Default text.
		if ( is_null( $instance['text'] ) ) {
			$instance['text'] = __( 'Except where otherwise noted, content on this site is licensed under a Creative Commons %license_link% license.', 'cac-creative-commons' );
		}

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php esc_html_e( 'Icon size:', 'cac-creative-commons' ); ?></label>

			<select class="widefat" name="<?php echo $this->get_field_name( 'size' ); ?>" id="<?php echo $this->get_field_id( 'size' ); ?>">
				<option value="normal" <?php selected( $instance['size'], 'normal' ); ?>><?php esc_html_e( 'Normal (88px x 31px)', 'cac-creative-commons' ); ?></option>
				<option value="compact" <?php selected( $instance['size'], 'compact' ); ?>><?php esc_html_e( 'Compact (80px x 15px)', 'cac-creative-commons' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php esc_html_e( 'Text:', 'cac-creative-commons' ); ?></label>
			<textarea class="large-text" rows="6" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
			<br />
			<small><?php _e( "<strong>%license_link%</strong> is replaced with a link to the site's license when the widget is rendered in the sidebar", 'cac-creative-commons' ); ?></small></p>

		<?php
	}

	/**
	 * Add help text to widgets admin screen.
	 *
	 * @since 0.1.0
	 */
	public static function add_help_text() {
		$screen = get_current_screen();

		/* translators: %s is the "Settings > Writing" admin page */
		$set_license_text = __( 'To set your site license, visit the "%s" page.', 'cac-creative-commons' );
		$settings_text    = __( 'Settings > Writing', 'cac-creative-commons' );

		$content = '<p>';
		$content .= __( 'Use the Creative Commons widget to display your site license in any sidebar.' );
		$content .= '</p>';

		$content .= '<p>';
		$content .= sprintf( $set_license_text, sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'options-writing.php' ), $settings_text ) );
		$content .= '</p>';

		$screen->add_help_tab( array(
			'id' => 'cac_cc_widget',
			'title' => __( 'Creative Commons License Widget', 'cac-creative-commons' ),
			'content' => $content,
		) );
	}
}
