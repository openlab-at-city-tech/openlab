<?php

/**
 * Subscribe counter widget class.
 */
class S2_Counter_Widget extends WP_Widget {

	/**
	 * Declares the S2_Counter_widget constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$widget_options = array(
			'classname'                   => 's2_counter',
			'description'                 => esc_html__( 'Subscriber Counter widget for Subscribe2', 'subscribe2' ),
			'customize_selective_refresh' => true,
		);

		$control_options = array(
			'width'  => 250,
			'height' => 500,
		);

		parent::__construct( 's2_counter', esc_html__( 'Subscribe2 Counter', 'subscribe2' ), $widget_options, $control_options );
	}

	/**
	 * Displays the Widget.
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title      = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Subscriber Count', 'subscribe2' );
		$s2w_bg     = ! empty( $instance['s2w_bg'] ) ? $instance['s2w_bg'] : '#e3dacf';
		$s2w_fg     = ! empty( $instance['s2w_fg'] ) ? $instance['s2w_fg'] : '#345797';
		$s2w_width  = ! empty( $instance['s2w_width'] ) ? $instance['s2w_width'] : '82';
		$s2w_height = ! empty( $instance['s2w_height'] ) ? $instance['s2w_height'] : '16';
		$s2w_font   = ! empty( $instance['s2w_font'] ) ? $instance['s2w_font'] : '11';

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		global $mysubscribe2;

		$registered = $mysubscribe2->get_registered();
		$confirmed  = $mysubscribe2->get_public();
		$count      = ( count( $registered ) + count( $confirmed ) );

		echo wp_kses_post( '<ul><div style="text-align:center; background-color:' . $s2w_bg . '; color:' . $s2w_fg . '; width:' . $s2w_width . 'px; height:' . $s2w_height . 'px; font:' . $s2w_font . 'pt Verdana, Arial, Helvetica, sans-serif; vertical-align:middle; padding:3px; border:1px solid #444;">' );
		echo esc_html( $count );
		echo '</div></ul>';
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Saves the widgets settings.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = wp_strip_all_tags( stripslashes( $new_instance['title'] ) );
		$instance['s2w_bg']     = wp_strip_all_tags( stripslashes( $new_instance['s2w_bg'] ) );
		$instance['s2w_fg']     = wp_strip_all_tags( stripslashes( $new_instance['s2w_fg'] ) );
		$instance['s2w_width']  = wp_strip_all_tags( stripslashes( $new_instance['s2w_width'] ) );
		$instance['s2w_height'] = wp_strip_all_tags( stripslashes( $new_instance['s2w_height'] ) );
		$instance['s2w_font']   = wp_strip_all_tags( stripslashes( $new_instance['s2w_font'] ) );

		return $instance;
	}

	/**
	 * Creates the edit form for the widget.
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {
		// Set some defaults.
		$options = get_option( 'widget_s2counter', array() );

		if ( empty( $options ) ) {
			$defaults = array(
				'title'      => 'Subscriber Count',
				's2w_bg'     => '#e3dacf',
				's2w_fg'     => '#345797',
				's2w_width'  => '82',
				's2w_height' => '16',
				's2w_font'   => '11',
			);
		} else {
			$defaults = array(
				'title'      => $options['title'],
				's2w_bg'     => $options['s2w_bg'],
				's2w_fg'     => $options['s2w_fg'],
				's2w_width'  => $options['s2w_width'],
				's2w_height' => $options['s2w_height'],
				's2w_font'   => $options['s2w_font'],
			);

			delete_option( 'widget_s2counter' );
		}

		$instance = wp_parse_args( (array) $instance, $defaults );

		// Be sure you format your options to be valid HTML attributes.
		$s2w_title  = htmlspecialchars( $instance['title'], ENT_QUOTES );
		$s2w_bg     = htmlspecialchars( $instance['s2w_bg'], ENT_QUOTES );
		$s2w_fg     = htmlspecialchars( $instance['s2w_fg'], ENT_QUOTES );
		$s2w_width  = htmlspecialchars( $instance['s2w_width'], ENT_QUOTES );
		$s2w_height = htmlspecialchars( $instance['s2w_height'], ENT_QUOTES );
		$s2w_font   = htmlspecialchars( $instance['s2w_font'], ENT_QUOTES );

		echo '<div>' . "\r\n";
		echo '<fieldset><legend><label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . esc_html__( 'Widget Title', 'subscribe2' ) . '</label></legend>' . "\r\n";
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" value="' . esc_attr( $s2w_title ) . '" />' . "\r\n";
		echo '</fieldset>' . "\r\n";

		echo '<fieldset>' . "\r\n";
		echo '<legend>' . esc_html__( 'Color Scheme', 'subscribe2' ) . '</legend>' . "\r\n";
		echo '<label>' . "\r\n";
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 's2w_bg' ) ) . '" id="' . esc_attr( $this->get_field_id( 's2w_bg' ) ) . '" maxlength="6" value="' . esc_attr( $s2w_bg ) . '" class="colorpickerField" style="width:60px;" /> ' . esc_html__( 'Body', 'subscribe2' ) . '</label><br>' . "\r\n";
		echo '<label>' . "\r\n";
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 's2w_fg' ) ) . '" id="' . esc_attr( $this->get_field_id( 's2w_fg' ) ) . '" maxlength="6" value="' . esc_attr( $s2w_fg ) . '" class="colorpickerField" style="width:60px;" /> ' . esc_html__( 'Text', 'subscribe2' ) . '</label><br>' . "\r\n";
		echo '<div class="s2_colorpicker" id ="' . esc_attr( $this->get_field_id( 's2_colorpicker' ) ) . '"></div>';
		echo '</fieldset>';

		echo '<fieldset>' . "\r\n";
		echo '<legend>' . esc_html__( 'Width, Height and Font Size', 'subscribe2' ) . '</legend>' . "\r\n";
		echo '<table style="border:0; padding:0; margin:0 0 12px 0; border-collapse:collapse;" align="center">' . "\r\n";
		echo '<tr><td><label for="' . esc_attr( $this->get_field_id( 's2w_width' ) ) . '">' . esc_html__( 'Width', 'subscribe2' ) . '</label></td>' . "\r\n";
		echo '<td><input type="text" name="' . esc_attr( $this->get_field_name( 's2w_width' ) ) . '" id="' . esc_attr( $this->get_field_id( 's2w_width' ) ) . '" value="' . esc_attr( $s2w_width ) . '" /></td></tr>' . "\r\n";
		echo '<tr><td><label for="' . esc_attr( $this->get_field_id( 's2w_height' ) ) . '">' . esc_html__( 'Height', 'subscribe2' ) . '</label></td>' . "\r\n";
		echo '<td><input type="text" name="' . esc_attr( $this->get_field_name( 's2w_height' ) ) . '" id="' . esc_attr( $this->get_field_id( 's2w_height' ) ) . '" value="' . esc_attr( $s2w_height ) . '" /></td></tr>' . "\r\n";
		echo '<tr><td><label for="' . esc_attr( $this->get_field_id( 's2w_font' ) ) . '">' . esc_html__( 'Font', 'subscribe2' ) . '</label></td>' . "\r\n";
		echo '<td><input type="text" name="' . esc_attr( $this->get_field_name( 's2w_font' ) ) . '" id="' . esc_attr( $this->get_field_id( 's2w_font' ) ) . '" value="' . esc_attr( $s2w_font ) . '" /></td></tr>' . "\r\n";
		echo '</table></fieldset></div>' . "\r\n";
	}
} // End S2_Counter_widget class.
