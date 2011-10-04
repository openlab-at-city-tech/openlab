<?php get_header(); ?>
<div class="page">
    <h2 class="catheader catcenter">
		<?php single_cat_title(); ?>
    </h2>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
    <div class="page-content">
		<h3><a href="<?php the_permalink() ?>" title="Click to read <?php the_title(); ?>"><?php the_title(); ?></a></h3>
		<div class="meta">
					<?php _e("Posted by"); ?> <?php the_author_posts_link(); ?> <?php _e("on"); ?> <?php the_time(get_option('date_format')); ?> <?php _e("at"); ?> <?php the_time('g:i a'); ?> <?php edit_post_link('Edit'); ?>
				</div>
					<?php getImage('1'); ?>

		<?php the_excerpt(); ?>
    </div>
    
    <?php endwhile; ?>

		<?php if(isset($paged)):?>
        <div class="navigation">
            <p class="alignleft"><?php previous_posts_link('&laquo; Previous Page'); ?></p>
            <p class="alignright"><?php next_posts_link('Next Page &raquo;'); ?></p>
            <div class="recover"></div>
        </div>
        <?php endif; ?>
            
    <?php else : ?>
        <h2 class="catheader"><?php _e("We're sorry - that page was not found (Error 404)")?></h2>
        <p><?php _e('Make sure the URL is correct. Try searching for it.')?></p>
        <?php include('searchform.php') ?>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
