<?php
/**
 * Adds the User Profile Widget.
 *
 * @package Genesis
 *
 * @todo Document this file
 * @since 0.1.8
 */

add_action('widgets_init', create_function('', "register_widget('Genesis_User_Profile_Widget');"));
class Genesis_User_Profile_Widget extends WP_Widget {

	function Genesis_User_Profile_Widget() {
		$widget_ops = array( 'classname' => 'user-profile', 'description' => __('Displays user profile block with Gravatar', 'genesis') );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'user-profile' );
		$this->WP_Widget( 'user-profile', __('Genesis - User Profile', 'genesis'), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		extract($args);

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'alignment' => 'left',
			'user' => '',
			'size' => '45',
			'author_info' => '',
			'bio_text' => '',
			'page' => '',
			'posts_link' => ''
		) );

		echo $before_widget;

			if ( !empty($instance['title']) )
				echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;

			$text = '';

			if ( !empty($instance['alignment']) )
				$text .= '<span class="align'.esc_attr( $instance['alignment'] ).'">';

				$text .= get_avatar($instance['user'], $instance['size']);

			if( !empty($instance['alignment']) )
				$text .= '</span>';

			if ( $instance['author_info'] == 'text' ) {
				global $_genesis_formatting_allowedtags;
				$text .= wp_kses( $instance['bio_text'], $_genesis_formatting_allowedtags );
			}
			else {
				$text .= get_the_author_meta('description', $instance['user']);
			}

			$text .= $instance['page'] ? sprintf( ' <a class="pagelink" href="%s">%s</a>', get_page_link($instance['page']), sprintf( __('[Read More %s]', 'genesis'), g_ent('&hellip;') ) ) : '';

			$text .= $instance['posts_link'] ? sprintf( '<div class="posts_link"><a href="%s">%s</a></div>', get_author_posts_url($instance['user']), __('View My Blog Posts', 'genesis') ) : '';

			echo wpautop( $text );

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'alignment' => 'left',
			'user' => '',
			'size' => '45',
			'author_info' => '',
			'bio_text' => '',
			'page' => '',
			'posts_link' => ''
		) );

	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title"); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" /></p>

		<p><?php _e('Select a user. The email address for this account will be used to pull the Gravatar image.', 'genesis'); ?></p>
		<p><?php wp_dropdown_users(array('name' => $this->get_field_name('user'), 'selected' => $instance['user'])); ?></p>

		<p><label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Gravatar Size'); ?>:</label>
			<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">

				<?php
				$sizes = array(__('Small', 'genesis') => 45, __('Medium', 'genesis') => 65, __('Large', 'genesis') => 85, __('Extra Large', 'genesis') => 125);
				$sizes = apply_filters('genesis_gravatar_sizes', $sizes);
				foreach ( (array)$sizes as $label => $size ) { ?>
					<option value="<?php echo $size; ?>" <?php selected($size, $instance['size']); ?>><?php printf('%s (%spx)', $label, $size); ?></option>
				<?php } ?>
			</select></p>

		<p><label for="<?php echo $this->get_field_id('alignment'); ?>"><?php _e('Gravatar Alignment'); ?>:</label>
			<select id="<?php echo $this->get_field_id('alignment'); ?>" name="<?php echo $this->get_field_name('alignment'); ?>">
				<option value="">- None -</option>
				<option value="left" <?php selected('left', $instance['alignment']); ?>><?php _e('Left', 'genesis'); ?></option>
				<option value="right" <?php selected('right', $instance['alignment']); ?>><?php _e('Right', 'genesis'); ?></option>
			</select></p>

		<p><?php _e('Select which text you would like to use as the author description', 'genesis'); ?></p>
		<p>
			<input type="radio" name="<?php echo $this->get_field_name('author_info'); ?>" id="<?php echo $this->get_field_id('author_info'); ?>_val1" value="bio" <?php checked('bio', $instance['author_info']); ?>/> <label for="<?php echo $this->get_field_id('author_info'); ?>_val1"><?php _e('Author Bio', 'genesis'); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name('author_info'); ?>" id="<?php echo $this->get_field_id('author_info'); ?>_val2" value="text" <?php checked('text', $instance['author_info']); ?>/> <label for="<?php echo $this->get_field_id('author_info'); ?>_val2"><?php _e('Custom Text (below)', 'genesis'); ?></label><br />
			<textarea id="<?php echo $this->get_field_id('bio_text'); ?>" name="<?php echo $this->get_field_name('bio_text'); ?>" class="widefat" rows="6" cols="4"><?php echo htmlspecialchars( $instance['bio_text'] ); ?></textarea>
		</p>

		<p><?php _e('Choose your extended "About Me" page from the list below. This will be the page linked to at the end of the about me section.', 'genesis'); ?></p>
		<p><?php wp_dropdown_pages(array('name' => $this->get_field_name('page'), 'show_option_none' => '- None -', 'selected' => $instance['page'])); ?></p>

		<p><input id="<?php echo $this->get_field_id('posts_link'); ?>" type="checkbox" name="<?php echo $this->get_field_name('posts_link'); ?>" value="1" <?php checked(1, $instance['posts_link']); ?>/> <label for="<?php echo $this->get_field_id('posts_link'); ?>"><?php _e('Show Author Archive Link?', 'genesis'); ?></label></p>
	<?php
	}
}