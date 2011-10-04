<?php
/**
 * Adds the Page Navigation Menu widget.
 *
 * @package Genesis
 */

add_action('widgets_init', 'register_genesis_menu_pages_widget');
function register_genesis_menu_pages_widget() {
	//unregister_widget('WP_Widget_Pages');
	register_widget('Genesis_Menu_Pages_Widget');
}

class Genesis_Menu_Pages_Widget extends WP_Widget {

	function Genesis_Menu_Pages_Widget() {
		$widget_ops = array( 'classname' => 'menupages', 'description' => __('Display page navigation for your header', 'genesis') );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'menu-pages' );
		$this->WP_Widget( 'menu-pages', __('Genesis - Page Navigation Menu', 'genesis'), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		extract($args);

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'include' => array(),
			'order' => ''
		) );

		echo $before_widget;

		if ($instance['title']) echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;

		echo '<ul class="nav">'."\n";

		// Empty fallback (default)
		if(empty($instance['include'])) :
			$instance['include'][] = 'home';
			$pages = get_pages();
			foreach((array)$pages as $page) {
				$instance['include'][] = $page->ID;
			}
		endif;

		// Show Home Link?
		if(in_array('home', (array)$instance['include'])) {
			$active = (is_front_page()) ? 'class="current_page_item"' : '';
			echo '<li '.$active.'><a href="'. trailingslashit( home_url() ).'">'.__('Home', 'genesis').'</a></li>';
		}
		// Show Page Links?
		wp_list_pages(array('title_li' => '', 'include' => implode(',', $instance['include']), 'sort_column' => $instance['order']));

		echo '</ul>'."\n";

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'include' => array(),
			'order' => ''
		) );

		// Empty fallback (default)
		if(empty($instance['include'])) {
			$instance['include'][] = 'home';
			$pages = get_pages();
			foreach((array)$pages as $page) {
				$instance['include'][] = $page->ID;
			}
		}
		?>

		<p><?php _e('NOTE: Leave title blank if using this widget in the header', 'genesis'); ?></p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'genesis'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p><?php _e('Choose the order by which you would like to display your pages', 'genesis'); ?>:</p>

		<p><select name="<?php echo $this->get_field_name('order'); ?>">
			<option value="menu_order" <?php selected('menu_order', $instance['order']); ?>>Menu Order</option>
			<option value="ID" <?php selected('ID', $instance['order']); ?>>ID</option>
			<option value="post_title" <?php selected('post_title', $instance['order']); ?>>Title</option>
			<option value="post_date" <?php selected('post_date', $instance['order']); ?>>Date Created</option>
			<option value="post_modified" <?php selected('post_modified', $instance['order']); ?>>Date Modified</option>
			<option value="post_author" <?php selected('post_author', $instance['order']); ?>>Author</option>
			<option value="post_name" <?php selected('post_name', $instance['order']); ?>>Slug</option>
		</select></p>

		<p><?php _e('Use the checklist below to choose which pages (and subpages) you want to include in your Navigation Menu', 'genesis'); ?></p>

		<p>
		<ul class="genesis-pagechecklist">
		<?php genesis_page_checklist($this->get_field_name('include'), $instance['include']); ?>
		</ul>
		</p>

	<?php
	}
}
?>