<?php

/*------------------------------------------*/
/* Academia: Featured Pages		            */
/*------------------------------------------*/
 
class academia_widget_featured_pages extends WP_Widget {
	
	public function __construct() {

		parent::__construct(
			'academia-widget-featured-pages',
			esc_html__( 'Academia: Featured Pages', 'bradbury' ),
			array(
				'classname'   => 'widget-promoted-pages',
				'description' => esc_html__( 'Displays in a grid and links to multiple static pages. Best used in the \'Homepage: Content Widgets\' widgetized area.', 'bradbury' )
			)
		);

	}

	public function widget( $args, $instance ) {
		
		extract( $args );

		$i = 0;
		$z = 0;
		$max = 4;

		while ( $z < $max ) {
			$z++;
			$page_temp_id = 'page' . $z;
			if ( !isset($instance[$page_temp_id]) || $instance[$page_temp_id] == 0 ) {
				continue;
			}
			$page_settings[$i]['page_id'] = (int) $instance[$page_temp_id];
			$i++;
		}

		/* User-selected settings. */
		$title 				= apply_filters( 'widget_title', empty($instance['widget_title']) ? '' : $instance['widget_title'], $instance );
		$show_title 		= isset($instance['show_title']) ? $instance['show_title'] : false;
		$show_excerpt 		= isset($instance['show_excerpt']) ? $instance['show_excerpt'] : false;
		$show_photo 		= isset($instance['show_photo']) ? $instance['show_photo'] : false;
		$show_button 		= isset($instance['show_button']) ? $instance['show_button'] : false;
		$show_num 			= isset($page_settings) ? count($page_settings) : 0;
		
		if ( !isset($show_num) && ( $show_num != 3 ) ) {
			$show_num = 3;
		}
		
		$thumb_name = 'thumb-featured-page';
		
		$i = 0;
		$max = count($page_settings);
		$z = 0;

		/* Before widget (defined by themes). */
		echo $before_widget;
		
			?>

			<div class="custom-widget-featured-pages">
			
				<?php
				/* Title of widget (before and after defined by themes). */
				if ( $title ) {
					echo $before_title;
					echo $title;
					echo $after_title;
				}
				?>

				<ul class="site-columns site-columns-<?php echo esc_attr($max); ?> site-columns-widget">
				
				<?php
				while ($i < $max) {

					if ( $page_settings[$i]['page_id'] != 0 ) {
						$loop = new WP_Query( array( 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC', 'page_id' => $page_settings[$i]['page_id'] ) );

						while ( $loop->have_posts() ) : $loop->the_post(); 

							global $post;

							$z++;

						?><li class="site-column site-column-<?php echo esc_attr($z); ?> site-column-widget site-archive-post<?php 
						if ( $show_photo == 'on' && has_post_thumbnail() ) { echo ' has-post-thumbnail'; } ?>">
								<div class="site-column-widget-wrapper">
									<?php if ( $show_photo == 'on' && has_post_thumbnail() ) { ?>
									<div class="entry-thumbnail">
										<div class="entry-thumbnail-wrapper"><?php 
											echo '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
											the_post_thumbnail($thumb_name);
											echo '</a>';
										?></div><!-- .entry-thumbnail-wrapper -->
									</div><!-- .entry-thumbnail --><?php } ?><!-- ws fix
									--><div class="entry-preview">
										<div class="entry-preview-wrapper">
											<?php 
											if ($show_title == 'on') { 
													echo academiathemes_helper_display_entry_title($post);
											}
											if ($show_excerpt == 'on') { echo academiathemes_helper_display_excerpt($post); }
											if ($show_button == 'on') { echo academiathemes_helper_display_button_readmore($post); } 
											?>
											
										</div><!-- .entry-preview-wrapper -->
									</div><!-- .entry-preview -->
								</div><!-- .site-column-widget-wrapper -->
							</li><!-- .site-column .site-column-1 .site-column-widget --><!-- ws fix 
						--><?php $i++; endwhile;
						} // if 
					} // while ?>

				</ul><!-- .site-columns .site-columns-<?php echo esc_attr($show_num); ?> .site-columns-widget-->
			
			</div><!-- .custom-widget-featured-pages -->
			
			<?php
		wp_reset_postdata();

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['widget_title'] 	= sanitize_text_field ( $new_instance['widget_title'] );
		$instance['page1'] 			= absint( $new_instance['page1'] );
		$instance['page2'] 			= absint( $new_instance['page2'] );
		$instance['page3'] 			= absint( $new_instance['page3'] );
		$instance['page4'] 			= absint( $new_instance['page4'] );
		$instance['show_title'] 	= isset( $new_instance['show_title'] ) ? (bool) $new_instance['show_title'] : false;
		$instance['show_photo'] 	= isset( $new_instance['show_photo'] ) ? (bool) $new_instance['show_photo'] : false;
		$instance['show_button'] 	= isset( $new_instance['show_button'] ) ? (bool) $new_instance['show_button'] : false;
		$instance['show_excerpt'] 	= isset( $new_instance['show_excerpt'] ) ? (bool) $new_instance['show_excerpt'] : false;

		return $instance;
	}
	
	public function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'widget_title' 		=> __('Widget Title','bradbury'), 
			'page1' 			=> 0, 
			'page2' 			=> 0, 
			'page3' 			=> 0, 
			'page4' 			=> 0, 
			'show_title' 		=> 1, 
			'show_excerpt' 		=> 1, 
			'show_photo' 		=> 1, 
			'show_button' 		=> 0
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Widget Title', 'bradbury'); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" value="<?php echo esc_attr($instance['widget_title']); ?>" type="text" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page1' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Select page:', 'bradbury'); ?></label>
			<select id="<?php echo $this->get_field_id('page1'); ?>" name="<?php echo $this->get_field_name('page1'); ?>">
				<option value="0"><?php esc_html_e('Choose page:', 'bradbury'); ?></option>
				<?php
				$pages = get_pages();
				
				foreach ($pages as $pag) {
					$option = '<option value="'.esc_attr($pag->ID);
					if ($pag->ID == $instance['page1']) { $option .='" selected="selected';}
					$option .= '">';
					$option .= esc_html($pag->post_title);
					$option .= '</option>';
					echo $option;
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page2' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Select page:', 'bradbury'); ?></label>
			<select id="<?php echo $this->get_field_id('page2'); ?>" name="<?php echo $this->get_field_name('page2'); ?>">
				<option value="0"><?php esc_html_e('Choose page:', 'bradbury'); ?></option>
				<?php
				$pages = get_pages();
				
				foreach ($pages as $pag) {
					$option = '<option value="'.esc_attr($pag->ID);
					if ($pag->ID == $instance['page2']) { $option .='" selected="selected';}
					$option .= '">';
					$option .= esc_html($pag->post_title);
					$option .= '</option>';
					echo $option;
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page3' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Select page:', 'bradbury'); ?></label>
			<select id="<?php echo $this->get_field_id('page3'); ?>" name="<?php echo $this->get_field_name('page3'); ?>">
				<option value="0"><?php esc_html_e('Choose page:', 'bradbury'); ?></option>
				<?php
				$pages = get_pages();
				
				foreach ($pages as $pag) {
					$option = '<option value="'.esc_attr($pag->ID);
					if ($pag->ID == $instance['page3']) { $option .='" selected="selected';}
					$option .= '">';
					$option .= esc_html($pag->post_title);
					$option .= '</option>';
					echo $option;
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page4' ); ?>" style="display: block; font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Select page:', 'bradbury'); ?></label>
			<select id="<?php echo $this->get_field_id('page4'); ?>" name="<?php echo $this->get_field_name('page4'); ?>">
				<option value="0"><?php esc_html_e('Choose page:', 'bradbury'); ?></option>
				<?php
				$pages = get_pages();
				
				foreach ($pages as $pag) {
					$option = '<option value="'.esc_attr($pag->ID);
					if ($pag->ID == $instance['page4']) { $option .='" selected="selected';}
					$option .= '">';
					$option .= esc_html($pag->post_title);
					$option .= '</option>';
					echo $option;
				}
			?>
			</select>
		</p>

		<hr style="height: 1px; line-height: 1px; font-size: 1px; border: none; border-top: solid 1px #aaa; margin: 20px 0;" />

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $instance['show_photo'] ); ?> id="<?php echo $this->get_field_id('show_photo'); ?>" name="<?php echo $this->get_field_name('show_photo'); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_photo' ); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display Featured Image', 'bradbury'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $instance['show_title'] ); ?> id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_title' ); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display Title', 'bradbury'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $instance['show_excerpt'] ); ?> id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display Page Excerpt', 'bradbury'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $instance['show_button'] ); ?> id="<?php echo $this->get_field_id('show_button'); ?>" name="<?php echo $this->get_field_name('show_button'); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_button' ); ?>" style="font-size: 14px; font-weight: bold; margin: 0 0 6px;"><?php esc_html_e('Display a Read More button', 'bradbury'); ?></label>
		</p>

		<?php
	}
}
?>