<?php
/*
 *  Weaver Widgets and "plugins"
 */

/**
 * Text widget class
 *
 * @since 2.8.0
 */
class Weaver_Widget_Text extends WP_Widget {

	function Weaver_Widget_Text() {
		$widget_ops = array('classname' => 'weaver_widget_text',
		 'description' => __('Text Widget with Two Columns - with HTML and shortcode support. Also adds shortcodes to standard Text widget.',WEAVER_TRANSADMIN));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('weaver_text', __('Weaver Text 2',WEAVER_TRANSADMIN), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters( 'weaver_text', $instance['text'], $instance );
		$text2 = apply_filters( 'weaver_text', $instance['text2'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><div style="float: left; width: 48%; padding-right: 2%;">
			<?php
			if ($instance['filter']) {
				echo(wpautop($text)); echo('</div><div style="float: left; width: 48%; padding-left: 2%;">');
				echo(wpautop($text2)); echo('</div><div style="clear: both;"></div>');
			} else {
			    echo($text); echo('</div><div style="float: left; width: 48%; padding-left: 2%;">');
			    echo($text2); echo('</div><div style="clear: both;"></div>');
			}
			?>
			</div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['text'] =  $new_instance['text'];
			$instance['text2'] =  $new_instance['text2'];
		}
		else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
			$instance['text2'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text2']) ) );
		}
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'text2' => '',  'filter' => 0) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
		$text2 = format_to_edit($instance['text2']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',WEAVER_TRANSADMIN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('text2'); ?>" name="<?php echo $this->get_field_name('text2'); ?>"><?php echo $text2; ?></textarea>
		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />
			&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs',WEAVER_TRANSADMIN); ?></label></p>
<?php
	}
}

/**
 * Weaver login
 */
class Weaver_Widget_Login extends WP_Widget {

	function Weaver_Widget_Login() {
		$widget_ops = array('classname' => 'weaver_widget_login', 'description' => __( "Log in/out, admin",WEAVER_TRANSADMIN) );
		$this->WP_Widget('weaver_login', __('Weaver Login',WEAVER_TRANSADMIN), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Login', WEAVER_TRANSADMIN) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
			global $current_user;
			get_currentuserinfo();
			if (isset($current_user->display_name))
			echo '<span class="wvr-welcome-user" style="padding-left:15px;">' . __('Welcome', WEAVER_TRANS) . ' ' . $current_user->display_name . ".</span><br />\n";
?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			</ul>
<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',WEAVER_TRANSADMIN); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
	}
}


add_action("widgets_init", "weaver_load_widgets");
add_filter('weaver_text', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');		// add to standard text widget, too.


function weaver_load_widgets() {
	register_widget("Weaver_Widget_Text");
	register_widget("Weaver_Widget_Login");
}

?>
