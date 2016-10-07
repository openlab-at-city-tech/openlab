<?php //Template Name:Page With Left Sidebar
get_header(); 
get_template_part('breadcrums'); ?>
<div class="container">
	<div class="row enigma_blog_wrapper">
	<?php get_sidebar(); ?>	
	<div class="col-md-8">
	<?php get_template_part('post','page'); ?>	
	</div>	
	</div>
</div>	
<?php get_footer(); ?>