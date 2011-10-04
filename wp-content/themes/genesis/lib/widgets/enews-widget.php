<?php
/**
 * Adds the eNews and Updates widget.
 *
 * @package Genesis
 */

add_action('widgets_init', create_function('', "register_widget('Genesis_eNews_Updates');"));
class Genesis_eNews_Updates extends WP_Widget {

	function Genesis_eNews_Updates() {
		$widget_ops = array( 'classname' => 'enews-widget', 'description' => __('Displays Feedburner email subscribe form', 'genesis') );
		$this->WP_Widget( 'enews', __('Genesis - eNews and Updates', 'genesis'), $widget_ops );
	}

	function widget($args, $instance) {
		extract($args);

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'text' => '',
			'id' => '',
			'input_text' => '',
			'button_text' => ''
		) );

		echo $before_widget.'<div class="enews">';

			if (!empty($instance['title']))
				echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;

			global $_genesis_formatting_allowedtags;
			echo wpautop( wp_kses( $instance['text'], $_genesis_formatting_allowedtags ) );

			if(!empty($instance['id'])) { ?>
			<form id="subscribe" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo esc_js( $instance['id'] ); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true"><input type="text" value="<?php echo esc_attr( $instance['input_text'] ); ?>" id="subbox" onfocus="if (this.value == '<?php echo esc_js( $instance['input_text'] ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo esc_js( $instance['input_text'] ); ?>';}" name="email"/><input type="hidden" value="<?php echo esc_attr( $instance['id'] ); ?>" name="uri"/><input type="hidden" name="loc" value="en_US"/><input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" id="subbutton" /></form>
			<?php }

		echo '</div>'.$after_widget;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'text' => '',
			'id' => '',
			'input_text' => '',
			'button_text' => ''
		) );

?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'genesis'); ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text To Show', 'genesis'); ?>:</label><br />
		<textarea id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" class="widefat" rows="6" cols="4"><?php echo htmlspecialchars( $instance['text'] ); ?></textarea>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Google/Feedburner ID', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" value="<?php echo esc_attr( $instance['id'] ); ?>" class="widefat" />
		</p>

		<p>
		<?php $input_text = empty($instance['input_text']) ? __('Enter your email address...', 'genesis') : $instance['input_text']; ?>
		<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Input Text', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('input_text'); ?>" name="<?php echo $this->get_field_name('input_text'); ?>" value="<?php echo esc_attr( $input_text ); ?>" class="widefat" />
		</p>

		<p>
		<?php $button_text = empty($instance['button_text']) ? __('Go', 'genesis') : $instance['button_text']; ?>
		<label for="<?php echo $this->get_field_id('button_text'); ?>"><?php _e('Button Text', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" value="<?php echo esc_attr( $button_text ); ?>" class="widefat" />
		</p>

	<?php
	}
}