<?php
class S2_Form_Widget extends WP_Widget {
	/**
	Declares the Subscribe2 widget class.
	*/
	function __construct() {
		$widget_ops = array(
			'classname' => 's2_form_widget',
			'description' => esc_html__( 'Sidebar Widget for Subscribe2', 'subscribe2' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width' => 250,
			'height' => 300,
		);
		parent::__construct( 's2_form_widget', esc_html__( 'Subscribe2 Widget', 'subscribe2' ), $widget_ops, $control_ops );
	}

	/**
	Displays the Widget
	*/
	function widget( $args, $instance ) {
		$title = empty( $instance['title'] ) ? __( 'Subscribe2', 'subscribe2' ) : $instance['title'];
		$div = empty( $instance['div'] ) ? 'search' : $instance['div'];
		$widgetprecontent = empty( $instance['widgetprecontent'] ) ? '' : $instance['widgetprecontent'];
		$widgetpostcontent = empty( $instance['widgetpostcontent'] ) ? '' : $instance['widgetpostcontent'];
		$textbox_size = empty( $instance['size'] ) ? 20 : $instance['size'];
		$hidebutton = empty( $instance['hidebutton'] ) ? 'none' : $instance['hidebutton'];
		$postto = empty( $instance['postto'] ) ? '' : $instance['postto'];
		$js = empty( $instance['js'] ) ? '' : $instance['js'];
		$noantispam = empty( $instance['noantispam'] ) ? '' : $instance['noantispam'];
		$nowrap = empty( $instance['nowrap'] ) ? '' : $instance['nowrap'];
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
		$size = ' size="' . $textbox_size . '"';
		$nojs = '';
		if ( $js ) {
			$nojs = ' nojs="true"';
		}
		if ( $noantispam ) {
			$noantispam = ' noantispam="true"';
		}
		if ( $nowrap ) {
			$nowrap = ' wrap="false"';
		}
		$shortcode = '[subscribe2' . $hide . $postid . $size . $nojs . $noantispam . $nowrap . ']';
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_attr( $title ) . $args['after_title'];
		}
		echo '<div class="' . esc_attr( $div ) . '">';
		$content = do_shortcode( $shortcode );
		if ( ! empty( $widgetprecontent ) ) {
			echo wp_kses( $widgetprecontent, 'post' );
		}
		echo $content;
		if ( ! empty( $widgetpostcontent ) ) {
			echo wp_kses( $widgetpostcontent, 'post' );
		}
		echo '</div>';
		echo $args['after_widget'];
	}

	/**
	Saves the widgets settings.
	*/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['div'] = strip_tags( stripslashes( $new_instance['div'] ) );
		$instance['widgetprecontent'] = stripslashes( $new_instance['widgetprecontent'] );
		$instance['widgetpostcontent'] = stripslashes( $new_instance['widgetpostcontent'] );
		$instance['size'] = intval( stripslashes( $new_instance['size'] ) );
		$instance['hidebutton'] = strip_tags( stripslashes( $new_instance['hidebutton'] ) );
		$instance['postto'] = stripslashes( $new_instance['postto'] );
		$instance['js'] = stripslashes( $new_instance['js'] );
		$instance['noantispam'] = stripslashes( $new_instance['noantispam'] );
		$instance['nowrap'] = stripslashes( $new_instance['nowrap'] );

		return $instance;
	}

	/**
	Creates the edit form for the widget.
	*/
	function form( $instance ) {
		// set some defaults, getting any old options first
		$options = get_option( 'widget_subscribe2widget' );
		if ( false === $options ) {
			$defaults = array(
				'title' => 'Subscribe2',
				'div' => 'search',
				'widgetprecontent' => '',
				'widgetpostcontent' => '',
				'size' => 20,
				'hidebutton' => 'none',
				'postto' => '',
				'js' => '',
				'noantispam' => '',
				'nowrap' => '',
			);
		} else {
			$defaults = array(
				'title' => $options['title'],
				'div' => $options['div'],
				'widgetprecontent' => $options['widgetprecontent'],
				'widgetpostcontent' => $options['widgetpostcontent'],
				'size' => $options['size'],
				'hidebutton' => $options['hidebutton'],
				'postto' => $options['postto'],
				'js' => $options['js'],
				'noantispam' => $options['noantispam'],
				'nowrap' => $options['nowrap'],
			);
			delete_option( 'widget_subscribe2widget' );
		}
		// code to obtain old settings too
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = htmlspecialchars( $instance['title'], ENT_QUOTES );
		$div = htmlspecialchars( $instance['div'], ENT_QUOTES );
		$widgetprecontent = htmlspecialchars( $instance['widgetprecontent'], ENT_QUOTES );
		$widgetpostcontent = htmlspecialchars( $instance['widgetpostcontent'], ENT_QUOTES );
		$size = htmlspecialchars( $instance['size'], ENT_QUOTES );
		$hidebutton = htmlspecialchars( $instance['hidebutton'], ENT_QUOTES );
		$postto = htmlspecialchars( $instance['postto'], ENT_QUOTES );
		$js = htmlspecialchars( $instance['js'], ENT_QUOTES );
		$noantispam  = htmlspecialchars( $instance['noantispam'], ENT_QUOTES );
		$nowrap = htmlspecialchars( $instance['nowrap'], ENT_QUOTES );

		global $wpdb, $mysubscribe2;
		$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' AND post_status='publish'";

		echo '<div>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . __( 'Title', 'subscribe2' ) . ':' . "\r\n";
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" /></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'div' ) ) . '">' . __( 'Div class name', 'subscribe2' ) . ':' . "\r\n";
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'div' ) ) . '" name="' . esc_attr( $this->get_field_name( 'div' ) ) . '" type="text" value="' . esc_attr( $div ) . '" /></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'widgetprecontent' ) ) . '">' . esc_html__( 'Pre-Content', 'subscribe2' ) . ':' . "\r\n";
		echo '<textarea class="widefat" id="' . esc_attr( $this->get_field_id( 'widgetprecontent' ) ) . '" name="' . esc_attr( $this->get_field_name( 'widgetprecontent' ) ) . '" rows="2" cols="25">' . esc_attr( $widgetprecontent ) . '</textarea></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'widgetpostcontent' ) ) . '">' . __( 'Post-Content', 'subscribe2' ) . ':' . "\r\n";
		echo '<textarea class="widefat" id="' . esc_attr( $this->get_field_id( 'widgetpostcontent' ) ) . '" name="' . esc_attr( $this->get_field_name( 'widgetpostcontent' ) ) . '" rows="2" cols="25">' . esc_attr( $widgetpostcontent ) . '</textarea></label></p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'size' ) ) . '">' . esc_html__( 'Text Box Size', 'subscribe2' ) . ':' . "\r\n";
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'size' ) ) . '" name="' . esc_attr( $this->get_field_name( 'size' ) ) . '" type="text" value="' . esc_attr( $size ) . '" /></label></p>' . "\r\n";
		echo '<p>' . __( 'Display options', 'subscribe2' ) . ':<br />' . "\r\n";
		echo '<label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'complete"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'complete" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="none"' . checked( 'none', $hidebutton, false ) . '/> ' . esc_html__( 'Show complete form', 'subscribe2' ) . '</label>' . "\r\n";
		echo '<br /><label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'subscribe"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'subscribe" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="subscribe"' . checked( 'subscribe', $hidebutton, false ) . '/> ' . esc_html__( 'Hide Subscribe button', 'subscribe2' ) . '</label>' . "\r\n";
		echo '<br /><label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'unsubscribe"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'unsubscribe" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="unsubscribe"' . checked( 'unsubscribe', $hidebutton, false ) . '/> ' . esc_html__( 'Hide Unsubscribe button', 'subscribe2' ) . '</label>' . "\r\n";
		if ( '1' === $mysubscribe2->subscribe2_options['ajax'] ) {
			echo '<br /><label for="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'ajax"><input id="' . esc_attr( $this->get_field_id( 'hidebutton' ) ) . 'ajax" name="' . esc_attr( $this->get_field_name( 'hidebutton' ) ) . '" type="radio" value="link"' . checked( 'link', $hidebutton, false ) . '/> ' . esc_html__( 'Show as link', 'subscribe2' ) . '</label>' . "\r\n";
		}
		echo '</p>' . "\r\n";
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'postto' ) ) . '">' . esc_html__( 'Post form content to page', 'subscribe2' ) . ':' . "\r\n";
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'postto' ) ) . '" name="' . esc_attr( $this->get_field_name( 'postto' ) ) . '">' . "\r\n";
		echo '<option value="' . esc_attr( $mysubscribe2->subscribe2_options['s2page'] ) . '">' . esc_html__( 'Use Subscribe2 Default', 'subscribe2' ) . '</option>' . "\r\n";
		echo '<option value="home"';
		if ( 'home' === $postto ) { echo ' selected="selected"'; }
		echo '>' . esc_html__( 'Use Home Page', 'subscribe2' ) . '</option>' . "\r\n";
		echo '<option value="self"';
		if ( 'self' === $postto ) { echo ' selected="selected"'; }
		echo '>' . esc_html__( 'Use Referring Page', 'subscribe2' ) . '</option>' . "\r\n";
		$mysubscribe2->pages_dropdown( $postto );
		echo '</select></label></p>' . "\r\n";
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
} // End S2_Form_widget class
?>