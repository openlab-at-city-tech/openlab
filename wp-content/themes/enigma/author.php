<?php get_header(); ?>
<div class="enigma_header_breadcrum_title">	
	<div class="container">
		<div class="row">
		<?php if(have_posts()) :?>
			<div class="col-md-12">
			<h1><?php printf( __( 'Author Archives: %s', 'enigma' ), '<span class="vcard">'. get_the_author() .'</span>' ) ; ?>
			</h1>
			</div>
		<?php endif; ?>
		<?php rewind_posts(); ?>
		</div>
	</div>	
</div>
<div class="container">	
	<div class="row enigma_blog_wrapper">
	<div class="col-md-8">
	<?php if ( have_posts()): while ( have_posts() ): the_post();
	get_template_part('post','content'); ?>
	<?php endwhile; 
	endif; 
	weblizar_navigation();
	?>
	</div>		
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>	