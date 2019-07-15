<?php
/**
 * Multipage table of contents widget.
 *
 * @package Multipage
 * @subpackage Widgets
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Multipage table of contents widget.
 *
 * @since 1.4
 */
class MPP_Table_of_Contents_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 *
	 * @since 1.4
	 */
	public function __construct() {
		parent::__construct(
			false,
			_x( 'Multipage Table of Contents', 'Title of the table of contents widget', 'sgr-nextpage-titles' ),
			array(
				'description'                 => __( 'Show the Multipage table of contents on posts that have multiple pages.', 'sgr-nextpage-titles' ),
				'classname'                   => 'widget_mpp_table_of_contents_widget multipage widget',
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Display the table of contents widget.
	 *
	 * @since 1.4
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';

		/**
		 * Filters the title of the table of contents widget.
		 *
		 * @since 1.4
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];

		echo $args['before_title'] . esc_html( $title ) . $args['after_title']; ?>

		<p><?php // echo Multipage::page; ?></p>
		
		<?php

		/**
		 * Fires after the display of widget content if logged out.
		 *
		 * @since 1.9.0
		 */
		do_action( 'mpp_after_login_widget_loggedout' );

		echo $args['after_widget'];
	}

	/**
	 * Update the table of contents widget options.
	 *
	 * @since 1.4
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 * @return array $instance The parsed options to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	/**
	 * Output the table of contents widget options.
	 *
	 * @since 1.4
	 *
	 * @param array $instance Settings for this widget.
	 * @return void
	 */
	public function form( $instance = array() ) {

		$settings = wp_parse_args( $instance, array(
			'title' => '',
		) ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'sgr-nextpage-titles' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $settings['title'] ); ?>" /></label>
		</p>

		<?php
	}
}