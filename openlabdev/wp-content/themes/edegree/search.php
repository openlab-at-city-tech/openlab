<?php get_header(); ?>

<div class="page">
	<?php if (have_posts()) : ?>
    
        <h1 class="catheader"><?php _e('Search')?></h1>
        
        <?php while (have_posts()) : the_post(); ?>
            <div class="posts">
            <h2><a href="<?php the_permalink() ?>" title="<?php _e('Click to read ')?><?php the_title(); ?>"><?php the_title(); ?></a></h2>
            <div class="meta">
                        <?php _e('By')?> <?php the_author() ?>
                    </div>
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
