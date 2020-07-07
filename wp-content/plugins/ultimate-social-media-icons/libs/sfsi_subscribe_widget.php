<?php
// Creating the widget 
class subscriber_widget extends WP_Widget
{

	function __construct()
	{
		parent::__construct(
			// Base ID of your widget
			'subscriber_widget',

			// Widget name will appear in UI
			__('Ultimate Social Subscribe Form', 'subscriber_widget_domain'),

			// Widget description
			array('description' => __('Ultimate Social Subscribe Form', 'subscriber_widget_domain'),)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Call subscriber form
		echo do_shortcode("[USM_form]");

		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form($instance)
	{
		if (isset($instance['title'])) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<?php
			}

			// Updating widget replacing old instances with new
			public function update($newInstance, $oldInstance)
			{
				$instance = array();
				$instance['title'] = (!empty($newInstance['title'])) ? strip_tags($newInstance['title']) : '';
				return $instance;
			}
		}
		// Class wpb_widget ends here

		// Register and load the widget
		function subscriber_load_widget()
		{
			register_widget('subscriber_widget');
		}
		add_action('widgets_init', 'subscriber_load_widget');
		?><?php
	add_shortcode("USM_form", "sfsi_get_subscriberForm");
	function sfsi_get_subscriberForm()
	{
		$option8 = unserialize(get_option('sfsi_section8_options', false));
		$sfsi_feediid = sanitize_text_field(get_option('sfsi_feed_id'));
		if ($sfsi_feediid == "") {
			$url = "https://api.follow.it/subscribe";
		} else {
			$url = "https://api.follow.it/subscription-form/";
			$url = $url . $sfsi_feediid . '/8/';
		}
		$return = '';
		$return .= '<div class="sfsi_subscribe_Popinner">
					<form method="post" onsubmit="return sfsi_processfurther(this);" target="popupwindow" action="' . $url . '">
						<h5>' . trim(sanitize_text_field($option8['sfsi_form_heading_text'])) . '</h5>
						<div class="sfsi_subscription_form_field">
						<input type="hidden" name="action" value="followPub">
							<input type="email" name="email" value="" placeholder="' . trim($option8['sfsi_form_field_text']) . '"/>
						</div>
						<div class="sfsi_subscription_form_field">
							<input type="submit" name="subscribe" value="'.sanitize_text_field($option8['sfsi_form_button_text']).'"/>
						</div>
					</form>
				</div>';
		return $return;
	}
	?>