<?php
class MostLikedPostsWidget extends WP_Widget
{
     function __construct() {
	     load_plugin_textdomain( 'wti-like-post', false, 'wti-like-post/lang' );
          $widget_ops = array('description' => __('Widget to display most liked posts for a given time range.', 'wti-like-post'));
          parent::__construct(false, $name = __('Most Liked Posts', 'wti-like-post'), $widget_ops);
     }

     /** @see WP_Widget::widget */
     function widget($args, $instance) {
          global $MostLikedPosts;
          $MostLikedPosts->widget($args, $instance); 
     }
    
     function update($new_instance, $old_instance) {         
          if ( $new_instance['title'] == '' ) {
               $new_instance['title'] = __('Most Liked Posts', 'wti-like-post');
          }
		
		if ( $new_instance['time_range'] == '' ) {
               $new_instance['time_range'] = 'all';
          }
		
		if ( empty( $new_instance['number']) ) {
               $new_instance['number'] = 10;
          }
		
		if ( !isset( $new_instance['show_count'] ) ) {
               $new_instance['show_count'] = 0;
          }
		
          return $new_instance;
     }
    
     function form($instance) {
          global $MostLikedPosts;
		
		/**
		* Define the array of defaults
		*/ 
		$defaults = array(
					'title' => __('Most Liked Posts', 'wti-like-post'),
					'number' => 10,
					'time_range' => 'all',
					'show_count' => ''
				);
		
		$instance = wp_parse_args( $instance, $defaults );
		extract( $instance, EXTR_SKIP );
		
		$time_range_array = array(
							'all' => __('All time', 'wti-like-post'),
							'1' => __('Last one day', 'wti-like-post'),
							'2' => __('Last two days', 'wti-like-post'),
							'3' => __('Last three days', 'wti-like-post'),
							'7' => __('Last one week', 'wti-like-post'),
							'14' => __('Last two weeks', 'wti-like-post'),
							'21' => __('Last three weeks', 'wti-like-post'),
							'1m' => __('Last one month', 'wti-like-post'),
							'2m' => __('Last two months', 'wti-like-post'),
							'3m' => __('Last three months', 'wti-like-post'),
							'6m' => __('Last six months', 'wti-like-post'),
							'1y' => __('Last one year', 'wti-like-post')
						);
		
		$show_types = array('most_liked' => __('Most Liked', 'wti-like-post'), 'recent_liked' => __('Recently Liked', 'wti-like-post'));
		
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
      	?>
		<p>
           	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wti-like-post'); ?>:<br />
           	<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_html($title);?>" /></label>
      	</p>
		<p>
           	<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show', 'wti-like-post'); ?>:<br />
           	<input type="number" class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" size="3" value="<?php echo $instance['number'];?>" /></label>
      		<small>(<?php echo __('Default', 'wti-like-post'); ?> 10)</small>
      	</p>
		<p>
           	<label for="<?php echo $this->get_field_id('time_range'); ?>"><?php _e('Time range', 'wti-like-post'); ?>:<br />
			<select name="<?php echo $this->get_field_name('time_range'); ?>" id="<?php echo $this->get_field_id('time_range'); ?>">
			<?php
			foreach ( $time_range_array as $time_range_key => $time_range_value ) {
				$selected = ($time_range_key == $instance['time_range']) ? 'selected' : '';
				echo '<option value="' . $time_range_key . '" ' . $selected . '>' . $time_range_value . '</option>';
			}
			?>
			</select>
      	</p>
		<p>
           	<label for="<?php echo $this->get_field_id('show_count'); ?>"><input type="checkbox" id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" value="1" <?php if(isset($instance['show_count']) && $instance['show_count'] == '1') echo 'checked="checked"'; ?> /> <?php _e('Show like count', 'wti-like-post'); ?></label>
      	</p>
		<input type="hidden" id="wti-most-submit" name="wti-submit" value="1" />	   
      	<?php
     }
}

class WtiMostLikedPosts
{
     function __construct() {
          add_action( 'widgets_init', array(&$this, 'init') );
     }
    
     function init() {
          register_widget("MostLikedPostsWidget");
     }
     
     function widget($args, $instance = array() ) {
		global $wpdb;
		extract($args);
	    
		$where = '';
		$limit = '';
		$title = $instance['title'];
		$show_count = $instance['show_count'];
		$time_range = $instance['time_range'];
		//$show_type = $instance['show_type'];
		$order_by = 'ORDER BY like_count DESC, post_title';
		$num_posts = intval($instance['number']);

		if( $num_posts > 0 ) {
			$limit = "LIMIT " . $num_posts;
		}
		
		$widget_data  = $before_widget;
		$widget_data .= $before_title . esc_html($title) . $after_title;
		$widget_data .= '<ul class="wti-most-liked-posts">';
	
		$show_excluded_posts = get_option('wti_like_post_show_on_widget');
		$excluded_posts = esc_html(get_option('wti_like_post_excluded_posts'));
		$excluded_post_ids = explode(',', $excluded_posts);
		
		if( !$show_excluded_posts && count( $excluded_post_ids ) > 0 ) {
			$where = "AND post_id NOT IN (" . $excluded_posts . ")";
		}
		
		if ( $time_range != 'all' ) {
			$last_date = GetWtiLastDate($time_range);
			$where .= " AND date_time >= '$last_date'";
		}
		
		// Getting the most liked posts
		$query = "SELECT post_id, SUM(value) AS like_count, post_title
				FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P 
				WHERE L.post_id = P.ID AND post_status = 'publish' AND value > 0
				$where GROUP BY post_id $order_by $limit";
		
		/*$query = $wpdb->prepare(
					"SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P 
					WHERE L.post_id = P.ID AND post_status = 'publish' AND value > 0 %s GROUP BY post_id %s %d",
					$where, $order_by, $limit
				);*/
		
		$posts = $wpdb->get_results($query);

		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				$post_title = esc_html($post->post_title);
				$permalink = get_permalink($post->post_id);
				$like_count = $post->like_count;
				
				$widget_data .= '<li><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a>';
				$widget_data .= $show_count == '1' ? ' (' . $like_count . ')' : '';
				$widget_data .= '</li>';
			}
		} else {
			$widget_data .= '<li>';
			$widget_data .= __('No posts liked yet.', 'wti-like-post');
			$widget_data .= '</li>';
		}
   
		$widget_data .= '</ul>';
		$widget_data .= $after_widget;
   
		echo $widget_data;
     }
}

$MostLikedPosts = new WtiMostLikedPosts();

//recently like posts
class RecentlyLikedPostsWidget extends WP_Widget
{
     function __construct() {
	     load_plugin_textdomain( 'wti-like-post', false, 'wti-like-post/lang' );
          $widget_ops = array('description' => __('Widget to show recently liked posts.', 'wti-like-post'));
          parent::__construct(false, $name = __('Recently Liked Posts', 'wti-like-post'), $widget_ops);
     }

     function widget($args, $instance) {
          global $RecentlyLikedPosts;
          $RecentlyLikedPosts->widget($args, $instance); 
     }
    
     function update($new_instance, $old_instance) {         
          if($new_instance['title'] == ''){
               $new_instance['title'] = __('Recently Liked Posts', 'wti-like-post');
          }
         
          if($new_instance['number'] == ''){
               $new_instance['number'] = 10;
          }
         
          return $new_instance;
     }
    
     function form($instance) {
		global $RecentlyLikedPosts;
		
		/**
		* Define the array of defaults
		*/ 
		$defaults = array(
			'title' => __('Recently Liked Posts', 'wti-like-post'),
			'number' => 10
		);
		
		$instance = wp_parse_args( $instance, $defaults );
		extract( $instance, EXTR_SKIP );
      	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wti-like-post'); ?>:<br />
           	<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_html($instance['title']);?>" /></label>
      	</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of entries to show', 'wti-like-post'); ?>:<br />
			<input type="number" class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" style="width: 40px;" value="<?php echo $instance['number'];?>" /> <small>(<?php echo __('Default', 'wti-like-post'); ?> 10)</small></label>
      	</p>
		<input type="hidden" id="wti-recent-submit" name="wti-submit" value="1" />	   
      	<?php
     }
}

class RecentlyLikedPosts
{
     function __construct() {
          add_action( 'widgets_init', array(&$this, 'init') );
     }
    
     function init() {
          register_widget("RecentlyLikedPostsWidget");
     }
     
     function widget( $args, $instance = array() ) {
		global $wpdb;
		extract($args);
		
		$recent_id = array();
		$where = '';
		$title = $instance['title'];
		$number = intval($instance['number']);
		
		$widget_data  = $before_widget;
		$widget_data .= $before_title . esc_html($title) . $after_title;
		$widget_data .= '<ul class="wti-most-liked-posts wti-user-liked-posts">';
	
		$show_excluded_posts = get_option('wti_like_post_show_on_widget');
		$excluded_posts = esc_html(get_option('wti_like_post_excluded_posts'));
		
		if ( !$show_excluded_posts && !empty( $excluded_posts ) ) {
			$where = "AND post_id NOT IN (" . $excluded_posts . ")";
		}
		
		// Get the post IDs recently voted
		$recent_ids = $wpdb->get_col(
							"SELECT DISTINCT(post_id) FROM `{$wpdb->prefix}wti_like_post`
							WHERE value > 0 $where GROUP BY post_id ORDER BY MAX(date_time) DESC"
						);

		if(count($recent_ids) > 0) {
			$where = "AND post_id IN(" . implode(",", $recent_ids) . ")";
			
			// Getting the most liked posts
			$query = "SELECT post_id, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P 
					WHERE L.post_id = P.ID AND post_status = 'publish' $where GROUP BY post_id
					ORDER BY FIELD(post_id, " . implode(",", $recent_ids) . ") ASC LIMIT $number";

			$posts = $wpdb->get_results($query);
		 	
			if(count($posts) > 0) {
				foreach ($posts as $post) {
					$post_title = esc_html($post->post_title);
					$permalink = get_permalink($post->post_id);
					
					$widget_data .= '<li><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a></li>';
				}
			}
		} else {
			$widget_data .= '<li>';
			$widget_data .= __('No posts liked yet.', 'wti-like-post');
			$widget_data .= '</li>';
		}

		$widget_data .= '</ul>';
		$widget_data .= $after_widget;

		echo $widget_data;
     }
}

$RecentlyLikedPosts = new RecentlyLikedPosts();
?>