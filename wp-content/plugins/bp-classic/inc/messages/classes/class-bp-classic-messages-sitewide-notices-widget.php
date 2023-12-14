<?php
/**
 * BP Classic Messages Notice Widget class.
 *
 * @package bp-classic\inc\messages\classes
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A widget that displays sitewide notices.
 *
 * @since 1.0.0
 */
class BP_Classic_Messages_Sitewide_Notices_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'bp_messages_sitewide_notices_widget',
			__( '(BuddyPress) Sitewide Notices', 'bp-classic' ),
			array(
				'classname'                   => 'widget_bp_core_sitewide_messages buddypress widget',
				'description'                 => __( 'Display Sitewide Notices posted by the site administrator', 'bp-classic' ),
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
			)
		);

		if ( is_customize_preview() || bp_is_widget_block_active( '', $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'bp-classic-widget-styles' );
	}

	/**
	 * Render the widget.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::widget() for a description of parameters.
	 *
	 * @param array $args     See {@WP_Widget::widget()}.
	 * @param array $instance See {@WP_Widget::widget()}.
	 */
	public function widget( $args, $instance ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Don't display the widget if there are no Notices to show.
		$notices = BP_Messages_Notice::get_active();
		if ( empty( $notices ) ) {
			return;
		}

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/**
		 * Filters the title of the Messages widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'] . $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div class="bp-site-wide-message">
			<?php bp_message_get_notices(); ?>
		</div>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Process the saved settings for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::update() for a description of parameters and
	 *      return values.
	 *
	 * @param array $new_instance See {@WP_Widget::update()}.
	 * @param array $old_instance See {@WP_Widget::update()}.
	 * @return array $instance See {@WP_Widget::update()}.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Render the settings form for Appearance > Widgets.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::form() for a description of parameters.
	 *
	 * @param array $instance See {@WP_Widget::form()}.
	 */
	public function form( $instance ) {
		$instance = bp_parse_args(
			(array) $instance,
			array(
				'title' => '',
			)
		);

		$title = wp_strip_all_tags( $instance['title'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-classic' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
}
