<?php get_header(); ?>

<div class="wrapper section-inner">

	<div class="content left">

		<div class="posts">
	
			<div class="post">
			
				<div class="content-inner">
		                
					<div class="post-header">
					        
			        	<h2 class="post-title"><?php _e('Error 404', 'hemingway'); ?></h2>
			        	
			        </div>
				                                                	            
			        <div class="post-content">
			        	            
			            <p><?php _e("It seems like you have tried to open a page that doesn't exist. It could have been deleted, moved, or it never existed at all. You are welcome to search for what you are looking for with the form below.", 'hemingway') ?></p>
			            
			            <?php get_search_form(); ?>
			            
			        </div><!-- .post-content -->
		        
		        </div><!-- .content-inner -->
		            	                        	
			</div><!-- .post -->
		
		</div><!-- .posts -->
	
	</div><!-- .content -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div><!-- .wrapper -->

<?php get_footer(); ?>
