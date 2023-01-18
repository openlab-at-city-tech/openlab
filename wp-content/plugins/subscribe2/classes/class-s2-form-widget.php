<?php
class S2_Form_Widget extends WP_Widget {

	/**
	 * Display the widget’s instance in the REST API.
	 *
	 * @var bool
	 */
	public $show_instance_in_rest = true;

	/**
	 * Declares the Subscribe2 widget class.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 's2_form_widget',
			'description'                 => esc_html__( 'Sidebar Widget for Subscribe2', 'subscribe2' ),
			'show_instance_in_rest'       => true,
			'customize_selective_refresh' => true,
		);

		// add_filter( 'widget_text', 'shortcode_unautop' );
		// add_filter( 'widget_text', 'do_shortcode' );

		$control_ops = array(
			'width'  => 250,
			'height' => 300,
		);

		parent::__construct(
			's2_form_widget',
			esc_html__( 'Subscribe2 Widget', 'subscribe2' ),
			$widget_ops,
			$control_ops
		);
	}

	/**
	 * Display subscribe2 Widget.
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$js                = empty( $instance['js'] ) ? '' : $instance['js'];
		$div               = empty( $instance['div'] ) ? 'search' : $instance['div'];
		$title             = empty( $instance['title'] ) ? __( 'Subscribe2', 'subscribe2' ) : $instance['title'];
		$nowrap            = empty( $instance['nowrap'] ) ? '' : $instance['nowrap'];
		$postto            = empty( $instance['postto'] ) ? '' : $instance['postto'];
		$noantispam        = empty( $instance['noantispam'] ) ? '' : $instance['noantispam'];
		$hidebutton        = empty( $instance['hidebutton'] ) ? 'none' : $instance['hidebutton'];
		$textbox_size      = empty( $instance['size'] ) ? 20 : $instance['size'];
		$widgetprecontent  = empty( $instance['widgetprecontent'] ) ? '' : $instance['widgetprecontent'];
		$widgetpostcontent = empty( $instance['widgetpostcontent'] ) ? '' : $instance['widgetpostcontent'];

		$hide = '';
		if ( 'subscribe' === $hidebutton || 'unsubscribe' === $hidebutton ) {
			$hide = ' hide="' . $hidebutton . '"';
		} elseif ( 'link' === $hidebutton ) {
			$hide = ' link="' . __( '(Un)Subscribe to Posts', 'subscribe2' ) . '"';
		}

		$postid = '';
		if ( ! empty( $postto ) ) {
			$postid = ' id="' . $postto . '"';
		}

		$nojs = '';
		$size = ' size="' . $textbox_size . '"';
		if ( $js ) {
			$nojs = ' nojs="true"';
		}

		if ( $noantispam ) {
			$noantispam = ' noantispam="true"';
		}

		if ( $nowrap ) {
			$nowrap = ' wrap="false"';
		}

		$shortcode = '[subscribe2' . $hide . $postid . $size . $nojs . $noantispam . $nowrap . ' widget="true"]';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_attr( $title ) . wp_kses_post( $args['after_title'] );
		}
		
		echo '<div class="' . esc_attr( $div ) . '">';
		if ( ! empty( $widgetprecontent ) ) {
			echo wp_kses_post( $widgetprecontent );
		}

		echo do_shortcode( $shortcode );

		if ( ! empty( $widgetpostcontent ) ) {
			echo wp_kses_post( $widgetpostcontent );
		}

		echo '</div>';
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
		$instance                      = $old_instance;
		$instance['js']                = stripslashes( $new_instance['js'] );
		$instance['div']               = wp_strip_all_tags( stripslashes( $new_instance['div'] ) );
		$instance['size']              = intval( stripslashes( $new_instance['size'] ) );
		$instance['title']             = wp_strip_all_tags( stripslashes( $new_instance['title'] ) );
		$instance['postto']            = stripslashes( $new_instance['postto'] );
		$instance['nowrap']            = stripslashes( $new_instance['nowrap'] );
		$instance['noantispam']        = stripslashes( $new_instance['noantispam'] );
		$instance['hidebutton']        = wp_strip_all_tags( stripslashes( $new_instance['hidebutton'] ) );
		$instance['widgetprecontent']  = stripslashes( $new_instance['widgetprecontent'] );
		$instance['widgetpostcontent'] = stripslashes( $new_instance['widgetpostcontent'] );

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
		// Set some defaults, getting any old options first.
		$options = get_option( 'widget_subscribe2widget' );

		if ( false === $options ) {
			$defaults = array(
				'js'                => '',
				'div'               => 'search',
				'size'              => 20,
				'title'             => 'Subscribe2',
				'postto'            => '',
				'nowrap'            => '',
				'hidebutton'        => 'none',
				'noantispam'        => '',
				'widgetprecontent'  => '',
				'widgetpostcontent' => '',
			);
		} else {
			$defaults = array(
				'js'                => $options['js'],
				'div'               => $options['div'],
				'size'              => $options['size'],
				'title'             => $options['title'],
				'postto'            => $options['postto'],
				'nowrap'            => $options['nowrap'],
				'hidebutton'        => $options['hidebutton'],
				'noantispam'        => $options['noantispam'],
				'widgetprecontent'  => $options['widgetprecontent'],
				'widgetpostcontent' => $options['widgetpostcontent'],
			);

			delete_option( 'widget_subscribe2widget' );
		}

		// Code to obtain old settings too.
		$instance = wp_parse_args( (array) $instance, $defaults );

		$js                = htmlspecialchars( $instance['js'], ENT_QUOTES );
		$div               = htmlspecialchars( $instance['div'], ENT_QUOTES );
		$size              = htmlspecialchars( $instance['size'], ENT_QUOTES );
		$title             = htmlspecialchars( $instance['title'], ENT_QUOTES );
		$postto            = htmlspecialchars( $instance['postto'], ENT_QUOTES );
		$nowrap            = htmlspecialchars( $instance['nowrap'], ENT_QUOTES );
		$hidebutton        = htmlspecialchars( $instance['hidebutton'], ENT_QUOTES );
		$noantispam        = htmlspecialchars( $instance['noantispam'], ENT_QUOTES );
		$widgetprecontent  = htmlspecialchars( $instance['widgetprecontent'], ENT_QUOTES );
		$widgetpostcontent = htmlspecialchars( $instance['widgetpostcontent'], ENT_QUOTES );

		global $mysubscribe2;

		echo '<div>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . esc_html__( 'Title', 'subscribe2' ) . ':' . "\r\n";
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" /></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'div' ) ) . '">' . esc_html__( 'Div class name', 'subscribe2' ) . ':' . "\r\n";
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'div' ) ) . '" name="' . esc_attr( $this->get_field_name( 'div' ) ) . '" type="text" value="' . esc_attr( $div ) . '" /></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'widgetprecontent' ) ) . '">' . esc_html__( 'Pre-Content', 'subscribe2' ) . ':' . "\r\n";
		echo '<textarea class="widefat" id="' . esc_attr( $this->get_field_id( 'widgetprecontent' ) ) . '" name="' . esc_attr( $this->get_field_name( 'widgetprecontent' ) ) . '" rows="2" cols="25">' . esc_attr( $widgetprecontent ) . '</textarea></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'widgetpostcontent' ) ) . '">' . esc_html__( 'Post-Content', 'subscribe2' ) . ':' . "\r\n";
		echo '<textarea class="widefat" id="' . esc_attr( $this->get_field_id( 'widgetpostcontent' ) ) . '" name="' . esc_attr( $this->get_field_name( 'widgetpostcontent' ) ) . '" rows="2" cols="25">' . esc_attr( $widgetpostcontent ) . '</textarea></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'size' ) ) . '">' . esc_html__( 'Text Box Size', 'subscribe2' ) . ':' . "\r\n";
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'size' ) ) . '" name="' . esc_attr( $this->get_field_name( 'size' ) ) . '" type="text" value="' . esc_attr( $size ) . '" /></label></p>' . "\r\n";
		echo '<p>' . esc_html__( 'Display options', 'subscribe2' ) . ':<br>' . "\r\n";
		echo '<label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'complete"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'complete" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="none"' . checked( 'none', $hidebutton, false ) . '/> ' . esc_html__( 'Show complete form', 'subscribe2' ) . '</label>' . "\r\n";
		echo '<br><label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'subscribe"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'subscribe" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="subscribe"' . checked( 'subscribe', $hidebutton, false ) . '/> ' . esc_html__( 'Hide Subscribe button', 'subscribe2' ) . '</label>' . "\r\n";
		echo '<br><label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'unsubscribe"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'unsubscribe" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="unsubscribe"' . checked( 'unsubscribe', $hidebutton, false ) . '/> ' . esc_html__( 'Hide Unsubscribe button', 'subscribe2' ) . '</label>' . "\r\n";

		if ( '1' === $mysubscribe2->subscribe2_options['ajax'] ) {
			echo '<br><label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'ajax"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'ajax" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="link"' . checked( 'link', $hidebutton, false ) . '/> ' . esc_html__( 'Show as link', 'subscribe2' ) . '</label>' . "\r\n";
		}

		echo '</p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'postto' ) ) . '">' . esc_html__( 'Post form content to page', 'subscribe2' ) . ':' . "\r\n";
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'postto' ) ) . '" name="' . esc_attr( $this->get_field_name( 'postto' ) ) . '">' . "\r\n";
		echo '<option value="' . esc_attr( $mysubscribe2->subscribe2_options['s2page'] ) . '">' . esc_html__( 'Use Subscribe2 Default', 'subscribe2' ) . '</option>' . "\r\n";
		echo '<option value="home"';

		if ( 'home' === $postto ) {
			echo ' selected="selected"';
		}

		echo '>' . esc_html__( 'Use Home Page', 'subscribe2' ) . '</option>' . "\r\n";
		echo '<option value="self"';

		if ( 'self' === $postto ) {
			echo ' selected="selected"';
		}

		echo '>' . esc_html__( 'Use Referring Page', 'subscribe2' ) . '</option>' . "\r\n";
		echo '</select></label></p>' . "\r\n";

		$mysubscribe2->pages_dropdown( $postto );

		echo '<p><label for="' . esc_attr( $this->get_field_id( 'js' ) ) . '">' . esc_html__( 'Disable JavaScript', 'subscribe2' ) . ':' . "\r\n";
		echo '<input id="' . esc_attr( $this->get_field_id( 'js' ) ) . '" name ="' . esc_attr( $this->get_field_name( 'js' ) ) . '" value="true" type="checkbox"' . checked( 'true', $js, false ) . '/>';
		echo '</label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'noantispam' ) ) . '">' . esc_html__( 'Disable Anti-spam measures', 'subscribe2' ) . ':' . "\r\n";
		echo '<input id="' . esc_attr( $this->get_field_id( 'noantispam' ) ) . '" name ="' . esc_attr( $this->get_field_name( 'noantispam' ) ) . '" value="true" type="checkbox"' . checked( 'true', $noantispam, false ) . '/>';
		echo '</label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'nowrap' ) ) . '">' . esc_html__( 'Disable wrapping of form buttons', 'subscribe2' ) . ':' . "\r\n";
		echo '<input id="' . esc_attr( $this->get_field_id( 'nowrap' ) ) . '" name ="' . esc_attr( $this->get_field_name( 'nowrap' ) ) . '" value="true" type="checkbox"' . checked( 'true', $nowrap, false ) . '/>';
		echo '</label></p>' . "\r\n";
		echo '</div>' . "\r\n";
	}

} // End S2_Form_widget class.
