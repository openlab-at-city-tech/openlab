<?php

/*------------------------------------------*/
/* Academia: Recent Posts           */
/*------------------------------------------*/
 
class academia_recent_posts extends WP_Widget {
	
	public function __construct() {

		parent::__construct(
			'academia-widget-recent-posts',
			esc_html__( 'Academia: Recent Posts', 'bradbury' ),
			array(
				'classname'   => 'widget-recent-posts',
				'description' => esc_html__( 'Displays the most recent blog posts, optionally filtered by category.', 'bradbury' )
			)
		);

	}
	
	public function widget( $args, $instance ) {
		
		extract( $args );

		/* User-selected settings. */
		$title 			= apply_filters( 'widget_title', empty($instance['widget_title']) ? '' : $instance['widget_title'], $instance );
		$category 		= isset($instance['category']) ? $instance['category'] : 1;
		$show_num 		= isset($instance['show_num']) ? $instance['show_num'] : 3;
		$show_thumb 	= isset($instance['show_thumb']) ? $instance['show_thumb'] : false;
		$show_date 		= isset($instance['datetime']) ? $instance['datetime'] : false;
		$show_excerpt 	= isset($instance['show_excerpt']) ? $instance['show_excerpt'] : false;

		if ( !isset($show_num) && ( $show_num != 2 && $show_num != 3 && $show_num != 4 ) ) {
			$show_num = 3;
		}

		if ( isset($category) ) {
			$categoryLink = get_category_link($category);
		}

		if ( $args['id'] == 'homepage-content-widgets' ) {
			$thumb_name = 'thumb-featured-page';
			$thumb_width = 530;
			$thumb_height = 350;
		} else {
			$thumb_name = 'post-thumbnail';
			$thumb_width = 190;
			$thumb_height = 130;
		}

		/* Before widget (defined by themes). */
		echo $before_widget;
		
		if ( $args['id'] == 'homepage-content-widgets' ) {
			if ( isset($category) && $category != 0 ) { ?>
				<span class="site-readmore-span"><a href="<?php echo esc_url($categoryLink); ?>" class="site-readmore-anchor"><?php esc_html_e('More in ','bradbury'); echo esc_html(get_the_category_by_ID($category)); ?></a></span>
			<?php }
		}

		/* Title of widget (before and after defined by themes). */
		if ( $title ) {
			echo $before_title;
			echo $title;
			echo $after_title;
		}

		echo '<div class="custom-widget-featured-pages custom-widget-featured-posts">';
		echo '<ul class="site-columns site-columns-' . esc_attr( $show_num ) . ' site-columns-widget">';

		$loop = new WP_Query( array( 'posts_per_page' => absint($show_num), 'orderby' => 'date', 'order' => 'DESC', 'cat' => absint($category) ) );

		$i = 0; 

		while ( $loop->have_posts() ) : $loop->the_post(); $i++;

			global $post;

			$classes = array('site-column', 'site-column-' . esc_attr($i), 'site-column-widget', 'site-archive-post'); 
			
			if ( has_post_thumbnail() && $show_thumb == 'on' ) {
				$classes[] = 'has-post-thumbnail';
			} else {
				$classes[] = 'post-nothumbnail';
			}

		?><li <?php post_class($classes); ?>>

			<div class="site-column-widget-wrapper">
				<?php if ( has_post_thumbnail() && $show_thumb == 'on' ) { ?>
				<div class="entry-thumbnail">
					<div class="entry-thumbnail-wrapper"><?php 

						echo '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
						the_post_thumbnail($thumb_name);
						echo '</a>';

						if ($show_date == 'on') { echo academiathemes_helper_display_datetime($post); }

					?></div><!-- .entry-thumbnail-wrapper -->
				</div><!-- .entry-thumbnail --><?php } ?><!-- ws fix
				--><div class="entry-preview">
					<div class="entry-preview-wrapper">
						<?php 
						if ( ( !has_post_thumbnail() || $show_thumb != 'on' ) && $show_date == 'on' ) { echo academiathemes_helper_display_datetime($post); }
						echo academiathemes_helper_display_entry_title($post);
						if ($show_excerpt == 'on') { echo academiathemes_helper_display_excerpt($post); }
						?>
						
					</div><!-- .entry-preview-wrapper -->
				</div><!-- .entry-preview -->
			</div><!-- .site-column-widget-wrapper -->

			</li><!-- .site-column .site-column-<?php echo esc_attr($i); ?> .site-column-widget .site-archive-post --><?php

			endwhile; 
			
			//Reset query_posts
			wp_reset_query();			
		echo '</ul><!-- .site-columns .site-columns-' . esc_attr( $show_num ) . ' .site-columns-widget -->';

		if ( $args['id'] == 'sidebar' ) {
			if ( isset($category) && $category != 0 ) { ?>
				<span class="site-readmore-span"><a href="<?php echo esc_url($categoryLink); ?>" class="site-readmore-anchor"><?php esc_html_e('More in ','bradbury'); echo get_the_category_by_ID($category); ?></a></span>
			<?php }
		}

		echo '</div><!-- .custom-widget-featured-pages -->';

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['widget_title'] 		= sanitize_text_field ( $new_instance['widget_title'] );
		$instance['category'] 			= (int) $new_instance['category'];
		$instance['show_num'] 			= (int) $new_instance['show_num'];
		$instance['show_thumb'] 		= isset( $new_instance['show_thumb'] ) ? (bool) $new_instance['show_thumb'] : false;
		$instance['datetime'] 			= isset( $new_instance['datetime'] ) ? (bool) $new_instance['datetime'] : false;
		$instance['show_excerpt'] 		= isset( $new_instance['show_excerpt'] ) ? (bool) $new_instance['show_excerpt'] : false;

		return $instance;
	}
	
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'widget_title' 		=> __('Widget Title','bradbury'), 
			'category' 			=> 0, 
			'show_num' 			=> 3, 
			'show_thumb' 		=> 'on', 
			'show_excerpt' 		=> 'on', 
			'datetime' 			=> 'on'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Widget Title', 'bradbury'); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" value="<?php echo esc_attr($instance['widget_title']); ?>" type="text" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Category of posts', 'bradbury'); ?>:</label>
				<select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" class="widefat">
					<option value="0"><?php esc_html_e('- show from all categories -', 'bradbury'); ?></option>
					<?php
					
					$cats = get_categories('hide_empty=0');
					foreach ($cats as $cat) {
						$option = '<option value="'.esc_attr($cat->term_id);
						if ($cat->term_id == $instance['category']) { $option .='" selected="selected';}
						$option .= '">';
						$option .= esc_html($cat->cat_name);
						$option .= ' (' . esc_html($cat->category_count) . ')';
						$option .= '</option>';
						echo $option;
					}
				?>
				</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show_num' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Number of posts to display', 'bradbury'); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'show_num' ); ?>" name="<?php echo $this->get_field_name( 'show_num' ); ?>" class="widefat">
				<option value="2"<?php if (!$instance['show_num'] || $instance['show_num'] == 2) { echo ' selected="selected"';} ?>><?php esc_html_e('2', 'bradbury'); ?></option>
				<option value="3"<?php if ($instance['show_num'] == 3) { echo ' selected="selected"';} ?>><?php esc_html_e('3', 'bradbury'); ?></option>
				<option value="4"<?php if ($instance['show_num'] == 4) { echo ' selected="selected"';} ?>><?php esc_html_e('4', 'bradbury'); ?></option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_thumb'); ?>" name="<?php echo $this->get_field_name('show_thumb'); ?>" <?php if ($instance['show_thumb'] == 'on') { echo ' checked="checked"';  } ?> /> 
			<label for="<?php echo $this->get_field_id('show_thumb'); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display thumbnail', 'bradbury'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" <?php if ($instance['show_excerpt'] == 'on') { echo ' checked="checked"';  } ?> /> 
			<label for="<?php echo $this->get_field_id('show_excerpt'); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display excerpt', 'bradbury'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('datetime'); ?>" name="<?php echo $this->get_field_name('datetime'); ?>" <?php if ($instance['datetime'] == 'on') { echo ' checked="checked"';  } ?> /> 
			<label for="<?php echo $this->get_field_id('datetime'); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display date', 'bradbury'); ?></label>
		</p>
		
		<?php
	}
}