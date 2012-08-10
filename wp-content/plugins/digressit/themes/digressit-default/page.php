<?php get_header(); ?>

<div id="container">
<?php get_dynamic_widgets(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<?php get_stylized_title(); ?>
		<div id="content" class="<?php echo $current_type; ?>">

			<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
				<div class="entry">
					<?php get_stylized_content_header(); ?>
					<?php the_content(); ?>
					<?php dynamic_sidebar('Page Content'); ?>					
					<?php edit_post_link(); ?>					
				</div>	
			</div>			
		</div>

	<?php endwhile;?>
<?php endif; ?>


</div>

<?php get_footer(); ?>

