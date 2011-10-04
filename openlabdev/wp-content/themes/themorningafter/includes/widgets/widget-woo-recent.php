<?php
/*---------------------------------------------------------------------------------*/
/* WooTabs widget */
/*---------------------------------------------------------------------------------*/

class Woo_Recent extends WP_Widget {

   function Woo_Recent() {
  	   $widget_ops = array('description' => 'This widget is the Tabs that classicaly goes into the sidebar. It contains the Popular posts, Latest Posts, Recent comments and a Tag cloud.' );
       parent::WP_Widget(false, $name = __('Woo - Recent Posts', 'woothemes'), $widget_ops);    
   }


   function widget($args, $instance) {        
		extract( $args );
        $title = $instance['title']; if ($title == '') $title = 'Recent Posts';
		$number = $instance['number']; if ($number == '') $number = 5;
		$thumb_size = $instance['thumb_size']; if ($thumb_size == '') $thumb_size = 35;
		echo $before_widget;
		echo $before_title; ?>
		<?php echo $title; ?>
        <?php echo $after_title; ?> 
					
		<?php $the_query = new WP_Query("showposts=$number&offset=1");
			
		while ($the_query->have_posts()) : $the_query->the_post();

			$do_not_duplicate = $post->ID; ?>
					
			<div class="home_recent_post">
					
				<?php woo_image('width=65&height=65');?>
	
						
				<div class="home_recent_title" id="post-<?php the_ID(); ?>">
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a>
				</div>
						
				<div class="home_recent_date">
				<?php the_time('F j, Y'); ?>
				</div>
						
				<div class="home_recent_auth">
					By <?php the_author(); ?>
				</div>
						
			</div>
					
			<?php endwhile; ?>
			
			<?php echo $after_widget; ?>
    
         <?php
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {     
       $title = esc_attr($instance['title']);
       $number = esc_attr($instance['number']);
       $thumb_size = esc_attr($instance['thumb_size']);
	   
       ?> 
       <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
       </label>
       </p>     
       <p>
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts:','woothemes'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
       </label>
       </p>  
       <p>
       <label for="<?php echo $this->get_field_id('thumb_size'); ?>"><?php _e('Thumbnail Size (0=disable):','woothemes'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('thumb_size'); ?>" name="<?php echo $this->get_field_name('thumb_size'); ?>" type="text" value="<?php echo $thumb_size; ?>" />
       </label>
       </p>  
       <?php 
   }

} 
register_widget('Woo_Recent');
?>