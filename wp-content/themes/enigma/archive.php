<?php get_header(); ?>
<div class="enigma_header_breadcrum_title">	
	<div class="container">
		<div class="row">
		<?php if(have_posts()) :?>
			<div class="col-md-12">
			<h1><?php if ( is_day() ) :
						printf( __( 'Daily Archives: %s', 'enigma' ), '<span>' . get_the_date() . '</span>' );
					elseif ( is_month() ) :
						printf( __( 'Monthly Archives: %s', 'enigma' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'enigma' ) ) . '</span>' );
					elseif ( is_year() ) :
						printf( __( 'Yearly Archives: %s', 'enigma' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'enigma' ) ) . '</span>' );
					else :
						_e( 'Archives', 'enigma' );
					endif; ?>
			</h1></div>
		<?php endif; ?>	
		</div>
	</div>	
</div>
<div class="container">	
	<div class="row enigma_blog_wrapper">
	<div class="col-md-8">
	<?php 
	if ( have_posts()): 
	while ( have_posts() ): the_post();
	get_template_part('post','content'); ?>		
	<?php endwhile; 
	endif; 
	weblizar_navigation(); ?>
	</div>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>
	