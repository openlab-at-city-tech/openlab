<?php


add_action('widgets_init', create_function('', 'return register_widget("ListPostsWithCommentCount");'));
//add_action('widgets_init', create_function('', 'return register_widget("ListUsersWithCommentCount");'));
//add_action('widgets_init', create_function('', 'return register_widget("LiveContentSearch");'));



class LiveContentSearch extends WP_Widget {
	function LiveContentSearch() {
		parent::WP_Widget(false, $name = 'Live Content Search');	
	}

	function widget($args = array(), $defaults) {		
		extract( $args );
		global $post;
		?>
		<div id="searchform">
			<input id="live-content-search" class="ajax-live live-content-search comment-field-area" type="text" value="<?php _e('Search'); ?>">
		</div>
		<?php
	}

}


class ListPostsWithCommentCount extends WP_Widget {
	function ListPostsWithCommentCount() {
		parent::WP_Widget(false, $name = 'List Posts with Comment Count');	
	}

	function widget($args = array(), $defaults) {		
		extract( $args );
		global $post;
		$currentpost = $post;
		
		//var_dump($args);
		$options = get_option('digressit');
		
		if($defaults['categorize']){
			$categories=  get_categories(); 
		}
		else{
			$categories = array('1');
		}
		
		//var_dump($categories)
		?>
		<div id="digress-it-list-posts">
		<a href="<?php echo get_option('siteurl'); ?>">
			<div class="rule-dashboard">
				<div class="rule-home-text">
					<?php echo $defaults['title']; ?>
				</div>
			</div>
		</a>
		
		<div class="sidebar-optional-graphic"></div>
		<div class="sidebar-pullout"></div>

		<?php
		$section_number = 1;
		foreach ($categories as $key => $cat) {
			if(isset($cat->name) && $cat->name == 'Uncategorized'){
				//continue;
			}
			?>

			<?php $cat_id = null; ?>
			<?php if(isset($cat->name)): ?>
			<h3><?php echo $cat->name; ?></h3>
			<?php $cat_id = $cat->cat_ID; ?>
			<?php endif; ?>
			
			<?php

			//var_dump($cat);
			$args = array(
				'numberposts' => -1,
				'post_type' => 'post',
				'post_status' => 'publish',
				'post_type' => 'post',
				'order_by' => $options['front_page_order_by'],
				'order' => $options['front_page_order'],
				'category' => $cat_id,
				);
								
			$posts = get_posts($args);
			//var_dump($posts);
			?>
			<?php foreach($posts as $post ): ?>
			<?php 
			
			setup_postdata($post); 

			//TODO
			$rule_discussion_status = 'upcoming';
			if($currentpost->post_name == $post->post_name){
				$rule_discussion_status = 'current';
			}
			
			?>
			
			<?php 
			
			$sidebar_number = null;
			if(isset($options['show_comment_count_in_sidebar'] ) && (int)$options['show_comment_count_in_sidebar'] == 0){
				$sidebar_number = $section_number;

				$commentcountclass  = 'section-number';					

				$section_number++;
			}
			else{			
				$commentcount = null;
				$commentcount = get_post_comment_count($post->ID);
						
				$commentbubblecolor = ($rule_discussion_status == 'current') ? '-dark' : '-grey';
			
				if($commentcount < 10){
					$commentcountclass  = 'commentcount commentcount1 sidebar-comment-count-single'.$commentbubblecolor;
				}
				else if($commentcount < 100 && $commentcount > 9){
					$commentcountclass  = 'commentcount commentcount2 sidebar-comment-count-double'.$commentbubblecolor;
				}
				else{
					$commentcountclass  = 'commentcount commentcount3 sidebar-comment-count-triple'.$commentbubblecolor;					
				}
				$sidebar_number = $commentcount;
			}
			?>
			<div id="sidebar-item-<?php echo $post->ID; ?>" class="sidebar-item sidebar-<?php echo $rule_discussion_status; ?>">
				
				<span class="<?php echo $commentcountclass; ?>"><?php echo $sidebar_number; ; ?></span>
				
				<span class="sidebar-text"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></span>
				
			</div>
			<?php endforeach; ?>
		<?php
		}
		?>
			</div>
		<?php

    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//var_dump($new_instance);
		$instance['title'] = $new_instance['title'];
		$instance['auto_hide'] = $new_instance['auto_hide'];
		$instance['position'] = $new_instance['position'];
		$instance['order_by'] = $new_instance['order_by'];
		$instance['order_type'] = $new_instance['order_type'];
		$instance['categorize'] = $new_instance['categorize'];
		$instance['categories'] = $new_instance['categories'];
		$instance['show_category_titles'] = $new_instance['show_category_titles'];

		return $instance;
	}



	function form($instance) {				
		global $blog_id, $wpdb;

		
		$defaults = array( 	
			'title' => 'Posts',
			'auto_hide' => true,
			'position' => 'left',
			'order_by' => 'ID',
			'order_type' => 'DESC',
			'categorize' => true,
			'categories' => null,
			'show_category_titles' => true	
		);
		
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		//var_dump($instance);
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e('Title:', 'digressit'); ?></strong></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo $instance['title']; ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" style="width:100%;" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'categorize' ); ?>"><?php _e('Categorize?', 'digressit'); ?></label>
			<input class="checkbox" type="checkbox" <?php echo ($instance['categorize'] == 'on') ? " checked " : ""; ?> id="<?php echo $this->get_field_id( 'categorize' ); ?>" name="<?php echo $this->get_field_name( 'categorize' ); ?>" /> 
		</p>


		
		<?php
		

	}

}


?>