<?php
query_posts( array(
	'ignore_sticky_posts' => 1,
    'meta_key' => '_ttrust_in_slideshow_value',
	'meta_value' => 'true',
    'posts_per_page' => 20,
    'post_type' => array(
		'page',
		'post',
		'projects'		
	)
));
?>

<?php if(have_posts()) :?>
<div class="slideshow">
<div class="flexslider">		
	<ul class="slides">			
		
		<?php $i = 1; while (have_posts()) : the_post(); ?>			
		<li id="slide<?php echo $i; ?>">		
			<?php $deactivate_links = of_get_option('ttrust_slide_deactivate_links'); ?>
			<?php $slide_img = get_post_meta($post->ID, "_ttrust_slideshow_img_value", true); ?>
			<?php $slide_text = get_post_meta($post->ID, "_ttrust_home_slideshow_text_value", true); ?>
			<?php if($deactivate_links) : ?>
				<img src="<?php echo $slide_img; ?>" alt="<?php the_title(); ?>" />	    		
			<?php else :?>							
				<a href="<?php the_permalink() ?>" rel="bookmark" ><img src="<?php echo $slide_img; ?>" alt="<?php the_title(); ?>" /></a>		    		
			<?php endif; ?>
			<?php if($slide_text) : ?>
					<div class="flex-caption">
						<p><?php echo $slide_text; ?></p>
					</div>
			<?php endif; ?>						
		</li>				
		<?php $i++; ?>			
		
		<?php endwhile; ?>
				
	</ul>
</div>	
</div>	

<?php endif; ?>
<?php wp_reset_query();?>