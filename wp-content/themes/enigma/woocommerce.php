<?php get_header(); ?>
<div class="enigma_header_breadcrum_title">	
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1><?php global $wp;
				echo $current_url = (add_query_arg(array(),$wp->request)); ?></h1>
				<!-- BreadCrumb -->
                <?php if (function_exists('weblizar_breadcrumbs')) weblizar_breadcrumbs(); ?>
                <!-- BreadCrumb -->
			</div>
		</div>
	</div>	
</div>
<div class="container">
	<div class="row enigma_blog_wrapper">
	<div class="col-md-8">
	<?php woocommerce_content(); ?>
	</div>
	<?php get_sidebar(); ?>	
	</div>
</div>	
<?php get_footer(); ?>