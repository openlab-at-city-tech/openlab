<div class="enigma_blog_area ">
<?php $wl_theme_options = weblizar_get_options();
if($wl_theme_options['blog_title'] !='') { ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="enigma_heading_title">
					<h3><?php echo esc_attr($wl_theme_options['blog_title']); ?></h3>		
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="container">	
	<div class="row" id="enigma_blog_section">
	<?php 	if ( have_posts()) : 			
			$posts_count =wp_count_posts()->publish;
			$args = array( 'post_type' => 'post','posts_per_page' => $posts_count ,'ignore_sticky_posts' => 1);		
			$post_type_data = new WP_Query( $args );
			while($post_type_data->have_posts()):
			$post_type_data->the_post(); ?>
			<div class="col-md-4 col-sm-12 scrollimation scale-in d2 pull-left">
			<div class="enigma_blog_thumb_wrapper">
				<div class="enigma_blog_thumb_wrapper_showcase">					
					<?php $img = array('class' => 'enigma_img_responsive');
							if(has_post_thumbnail()): 
							the_post_thumbnail('home_post_thumb',$img);
					endif; ?>
					<div class="enigma_blog_thumb_wrapper_showcase_overlay">
						<div class="enigma_blog_thumb_wrapper_showcase_overlay_inner ">
							<div class="enigma_blog_thumb_wrapper_showcase_icons">
								<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><i class="fa fa-link"></i></a>
							</div>
						</div>
					</div>
				</div>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php if(get_the_tag_list() != '') { ?>
				<p class="enigma_tags"><?php the_tags('Tags :&nbsp;', '', '<br />'); ?></p>
				<?php } ?>
				<?php the_excerpt( __( 'Read More' , 'enigma' ) ); ?>
				<a href="<?php the_permalink(); ?>" class="enigma_blog_read_btn"><i class="fa fa-plus-circle"></i><?php _e('Read More','enigma'); ?></a>
				<div class="enigma_blog_thumb_footer">
					<ul class="enigma_blog_thumb_date">
						<li><i class="fa fa-user"></i><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author(); ?></a></li>
						<li><i class="fa fa-clock-o"></i>
						<?php if ( ('d M  y') == get_option( 'date_format' ) ) : ?>
						<?php echo get_the_date('F d ,Y'); ?>
						<?php else : ?>
						<?php echo get_the_date(); ?>
						<?php endif; ?>
						</li>
						<li><i class="fa fa-comments-o"></i><?php comments_popup_link( '0', '1', '%', '', '-'); ?></li>
					</ul>
				</div>
			</div>
			</div>
			<?php  endwhile; 
			else : ?>
			<div class="col-md-4 col-sm-12 scrollimation scale-in d2 pull-left">
			<div class="enigma_blog_thumb_wrapper">
				<div class="enigma_blog_thumb_wrapper_showcase">
					<img  alt="Enigma" src="<?php echo WL_TEMPLATE_DIR_URI ?>/images/wall/img(11).jpg">
					<div class="enigma_blog_thumb_wrapper_showcase_overlay">
						<div class="enigma_blog_thumb_wrapper_showcase_overlay_inner ">
							<div class="enigma_blog_thumb_wrapper_showcase_icons">
								<a title="Enigma" href="#"><i class="fa fa-link"></i></a>
							</div>
						</div>
					</div>
				</div>
				<h2><a href="#"><?php _e('NO Post','enigma'); ?></a></h2>
				
				<div class="enigma_tags">
					<?php _e('Tags :&nbsp;','enigma'); ?>
					<a href="#"><?php _e('Bootstrap','enigma'); ?></a>
					<a href="#"><?php _e('HTML5','enigma'); ?></a>
				   
				</div>
				<p><?php _e('Add You Post To show post here..','enigma'); ?></p>
				<a href="#" class="enigma_blog_read_btn"><i class="fa fa-plus-circle"></i><?php _e('Read More','enigma'); ?></a>
				<div class="enigma_blog_thumb_footer">
					<ul class="enigma_blog_thumb_date">
						<li><i class="fa fa-user"></i><a href="#"><?php _e('By Admin','enigma'); ?></a></li>
						<li><i class="fa fa-clock-o"></i><?php _e(' November 9 2013','enigma'); ?></li>
						<li><i class="fa fa-comments-o"></i><a href="#"><?php _e('10','enigma'); ?></a></li>
					</ul>
				</div>
			</div>
			</div>
		<?php endif; ?>
		
	</div>
	<div class="enigma_carousel-navi">
				<div id="port-next" class="enigma_carousel-prev" ><i class="fa fa-arrow-left"></i></div>
				<div id="port-prev" class="enigma_carousel-next" ><i class="fa fa-arrow-right"></i></div>
	</div>
	</div>
</div>