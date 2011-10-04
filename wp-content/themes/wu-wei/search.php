<?php get_header(); ?>

	<?php if (have_posts()) : ?>

		<div class="pagetitle">Search results for: <span>"<?php the_search_query(); ?>"</span></div>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
				<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
				<div class="clearboth"><!-- --></div>
			</div>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class() ?>>

			<div class="post-info">

				<h1 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<div class="timestamp"><?php the_time('F j, y') ?> <!-- by <?php the_author() ?> --> //</div> <div class="comment-bubble"><?php comments_popup_link('0', '1', '%'); ?></div>
				<div class="clearboth"><!-- --></div>

				<p><?php edit_post_link('Edit this entry', '', ''); ?></p>

			</div>

			<div class="post-content">
				<?php the_content() ?>
			</div>

			<div class="clearboth"><!-- --></div>

		</div>

		<?php endwhile; ?>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
				<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
				<div class="clearboth"><!-- --></div>
			</div>

	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>No posts found.</h2>");
		}
		get_search_form();

	endif;
?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
