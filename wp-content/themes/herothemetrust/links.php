<?php get_header(); ?>

		<div id="pageHead">
			<h1><?php _e('Links', 'themetrust'); ?></h1>
		</div>
						 
		<div id="content" class="twoThirds clearfix">			    
			<div class="post">					
				<ul>
					<?php get_links_list(); ?>
				</ul>				
			</div>						    	
		</div>		
		<?php get_sidebar(); ?>
	
<?php get_footer(); ?>
