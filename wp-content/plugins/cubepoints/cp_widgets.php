<?php
/**
 * CubePoints widgets
 */

/** CubePoints Points Widget declaration */
class cp_pointsWidget extends WP_Widget {
 
	// constructor
	function cp_pointsWidget() {
		parent::WP_Widget('cp_pointsWidget', 'CubePoints', array('description' => 'Display the points of the current logged in user.'));	
	}
 
	// widget main
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		if (!(!is_user_logged_in() && $instance['text_alt']=='')) {
			echo $before_widget;
			$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
			if (is_user_logged_in()) {
				// Set default text, in case something messes up and resets the text to display to be null
				if($instance['text'] == '') {
					$instance['text'] = 'Points: %points%';
				}
				$string = str_replace('%points%', '<span class="cp_points_display">'.cp_displayPoints(0,1,1).'</span>', $instance['text']);
			} else {
				$string = $instance['text_alt'];
			}
			
			//start output
			do_action('cp_pointsWidget_before');
			if($instance['html']==''){
			?>
				<ul>
						<li><?php echo $string; ?></li>
						<?php do_action('cp_pointsWidget'); ?>
				</ul>
			<?php
			} else { 
				echo str_replace('%text%',$string,$instance['html']);
			}
			do_action('cp_pointsWidget_after');
			echo $after_widget;
		}
	}
 
	// widget settings update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = trim($new_instance['text']);
		$instance['text_alt'] = trim($new_instance['text_alt']);
		$instance['html'] = trim($new_instance['html']);
		return $instance;
	}
 
	// widget settings form
	function form($instance) {
		$default = 	array( 'title' => __('My Points', 'cp') , 'text' => __('Points', 'cp') . ': %points%' , 'text_alt' => __('You need to be logged in to view your points.', 'cp'), 'advanced' => '' );
		$instance = wp_parse_args( (array) $instance, $default );
 
		$field = 'title';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';
		
		$field = 'text';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Text', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';
		
		echo "\r\n".'<small><strong>'.__('Note', 'cp').':</strong> '.__('%points% would be replaced with the points of the logged in user', 'cp').'</small><br /><br />';
		
		$field = 'text_alt';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Text if user not logged in', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';
		
		echo "\r\n".'<small><strong>'.__('Note', 'cp').':</strong> '.__('Leave this field blank to hide the widget if no user is logged in', 'cp').'</small><br /><br />';
		
		$field = 'html';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
  		if ( !isset($instance[$field]) ) $instance[$field] = '';
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('HTML Code (advanced)', 'cp').': <textarea class="widefat" id="'.$field_id.'" name="'.$field_name.'" >'.esc_attr( $instance[$field] ).'</textarea><label></p>';

		echo "\r\n".'<small><strong>'.__('Note', 'cp').':</strong> '.__('This field should be left blank for most users! You may use this field to customize the appearance of this widget.', 'cp').'<br /><br /><strong>'.__('Shortcode', 'cp').':</strong> %text%</small>';
	}
}

/** CubePoints Top Users Widget */
class cp_topUsersWidget extends WP_Widget {
 
	// constructor
	function cp_topUsersWidget() {
		parent::WP_Widget('cp_topUsersWidget', 'CubePoints Top Users', array('description' => 'Use this widget to showcase the users with the most points.'));	
	}
 
	// widget main
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		echo $before_widget;
		$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

		//set default values
		if($instance['num'] == '' || $instance['num'] == 0) { $instance['num'] = 1; }
		if($instance['text'] == '') { $instance['text'] = '%user% (%points%)';}

		$top = cp_getAllPoints($instance['num'],get_option('cp_topfilter'));
		do_action('cp_topUsersWidget_before');
		echo apply_filters('cp_topUsersWidget_before','<ul>');
		$line = apply_filters('cp_topUsersWidget_line','<li class="cp_topUsersWidget top_%place%" style="%style%">%string%</li>');
		$line = str_replace('%style%', $instance['style'], $line);
		foreach($top as $x=>$y){
			$user = get_userdata($y['id']);
			$string = str_replace('%string%', '', $instance['text']);
			$string = str_replace('%string%',$string,$line);
			$string = apply_filters('cp_displayUserInfo',$string,$y,$x+1);
			echo $string;
		}
		echo apply_filters('cp_topUsersWidget_after','</ul>');
		do_action('cp_topUsersWidget_after');
		echo $after_widget;
	}
 
	// widget settings update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['num'] = ((int) $new_instance['num'] > 0 ) ? (int) $new_instance['num'] : 1 ;
		$instance['text'] = trim($new_instance['text']);
		$instance['style'] = trim($new_instance['style']);
		return $instance;
	}
 
	// widget settings form
	function form($instance) {
		$default = 	array( 'title' => __('Top Users', 'cp') , 'num' => 3 , 'text' => '%user% (%points%)', 'style' => 'list-style:none;' );
		$instance = wp_parse_args( (array) $instance, $default );
 
		$field = 'title';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';
		
		$field = 'num';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Number of top users to show', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';
		
		$field = 'text';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Text', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';

		echo "\r\n".'<small><strong>'.__('Shortcodes', 'cp') . ':</strong><br />';
		echo __('Number of points', 'cp') . ' - %points%' . '<br />';
		echo __('Points (number only)', 'cp') . ' - %npoints%' . '<br />';
		echo __('User display name', 'cp') . ' - %username%' . '<br />';
		echo __('User login ID', 'cp') . ' - %user%' . '<br />';
		echo __('User ID', 'cp') . ' - %userid%' . '<br />';
		echo __('User ranking', 'cp') . ' - %place%' . '<br />';
		echo __('Email MD5 hash', 'cp') . ' - %emailhash%' . '<br />';
		echo '<br /></small>';
		
		$field = 'style';
		$field_id = $this->get_field_id($field);
		$field_name = $this->get_field_name($field);
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Style', 'cp').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance[$field] ).'" /><label></p>';
		echo "\r\n".'<small><strong>'.__('Note', 'cp') . ':</strong> '.__('This adds the following style to the list element. Shortcodes from above may be used here. The %emailhash% shortcode, for example, could be used to display gravatars.', 'cp').'</small><br />';
	}
}

add_action('widgets_init', 'cp_widgets');

function cp_widgets(){	
	// register points widget
	register_widget("cp_pointsWidget");

	// register top users widget
	register_widget("cp_topUsersWidget");
	
}
 
?>