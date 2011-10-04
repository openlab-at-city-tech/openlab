<?php
/**
 *  This is the widget for the Stout Google Calendar plugin
 */

// Enable internationalisation
$plugin_dir = dirname(__FILE__);
load_plugin_textdomain( 'stout-gc','wp-content/plugins/'.$plugin_dir, $plugin_dir);

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'stout_load_widgets' );

/**
 * Register our widget.
 *
 * @since 0.1
 */
function stout_load_widgets() {
	register_widget( 'Stout_GC_Widget' );
}

/**
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.
 *
 * @since 0.1
 */
class Stout_GC_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Stout_GC_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'stout-gc-widget', 'description' => __('Embed a saved Stout Google Calendar.', 'stout-gc') );

		/* Widget control settings. */
		$control_ops = array( 'width' => '100%', 'height' => 350, 'id_base' => 'stout-gc-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'stout-gc-widget', __('Stout Google Calendar', 'stout-gc'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		global $wpdb;
		$sgc_table = $wpdb->prefix . "stoutgc";
		
		/* Our variables from the widget settings. */
		$id = $instance['id'];
		$calendars = $wpdb->get_results("SELECT id,name FROM $sgc_table WHERE id = $id LIMIT 1");
		foreach ($calendars as $calendar) {
			$title = stripslashes(apply_filters('widget_title', $calendar->name));
		}
		$show_name = isset( $instance['show_name'] ) ? $instance['show_name'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $show_name ) { echo $before_title . $title . $after_title; }

		echo stout_gc($id);

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* No need to strip tags for sex and show_name. */
		$instance['id'] = $new_instance['id'];
		$instance['show_name'] = $new_instance['show_name'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'show_name' => false );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		//build select options
		global $wpdb;
		$sgc_table = $wpdb->prefix . "stoutgc";
		
		$calendars = $wpdb->get_results("SELECT * FROM $sgc_table ORDER BY id");
		$select_options = '';
		foreach ($calendars as $calendar) {
			$select_options .= '<option value='.$calendar->id;
			if ( $instance['id'] == $calendar->id ) { $select_options .= ' selected="selected"'; }
			$select_options .= ' >'.$calendar->id.' - '.stripslashes($calendar->name).'</option>';
		}
		?>

		<!-- Calendar: Select -->
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e('Calendar:', 'stout-gc'); ?></label>
			<select id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $instance['id']; ?> class="widefat" style="width:100%;">
			<option value=""><?php _e('-- Select Calendar --', 'stout-gc');?></option>
			<?php echo $select_options; ?>
			</select>
		</p>
		
		<!-- Show Calendar Name: Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php if ($instance['show_name']) { echo ' checked ';} ?> id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_name' ); ?>">Show Calendar Name?</label>
		</p>

	<?php
	}
}

?>