<?php get_header(); ?>



<?php get_dynamic_widgets(); ?>



<?php get_stylized_title(); ?>
<div id="content" class="<?php echo $current_type; ?>">

	<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
		<div class="entry">
			<ul>
			<?php wp_list_authors('exclude_admin=0'); ?>
			</ul>
		</div>	
	</div>			
</div>



<?php get_footer(); ?>


