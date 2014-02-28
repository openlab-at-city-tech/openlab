<?php $i=1; $c=0;?>
<div class="thumbs masonry">			
<?php  while (have_posts()) : the_post(); ?>
    <?php $c++; ?>			    
	<?php
		global $p;				
		$p = "";
		$skills = get_the_terms( $post->ID, 'skill');
		if ($skills) {
		   foreach ($skills as $skill) {				
		      $p .= $skill->slug . " ";						
		   }
		}
	?> 
	<div class="project small clearfix <?php echo " ". $p; ?>" id="project-<?php echo $post->post_name;?>">						
		<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_threeColumn', array('class' => 'thumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>			    	
		<h1><a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_title(); ?></a></h1>																								
 	</div>				

<?php $i++; endwhile; ?>
<?php wp_reset_query();?>
</div>