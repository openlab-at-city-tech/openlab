<?php get_header(); ?>

<div class="container">

<?php 
	if ( !is_dynamic_sidebar('Archive Sidebar') ) : 
		
		?>
		
		<?php
	
		ListPosts::widget($args =array(), array('title' => 'Posts', 
															'auto_hide' => true, 
															'position' => 'left', 
															'order_by' => 'ID', 
															'order_type' => 'DESC', 
															'categorize' => false, 
															'categories' => null, 
															'show_category_titles' => false));
	else:
		get_dynamic_widgets();
	endif;
?>

<div id="content" class="<?php echo $current_type; ?>">

<?php if (have_posts()) : 

		$post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Author Archive</h2>
	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Archives</h2>
	  <?php } 

while (have_posts()) : the_post(); ?>


			<div <?php if(function_exists('post_class')){ post_class(); } ?> id="post-<?php the_ID(); ?>">
				<div class="entry">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

					<?php the_excerpt('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>

				</div>	
			</div>			

	<?php endwhile;?>

	<div class="navigation">
		<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
		<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
	</div>

<?php endif; ?>

</div>
</div>
<?php get_footer(); ?>


