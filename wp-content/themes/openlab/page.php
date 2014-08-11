<?php get_header(); ?>

<div id="content" class="hfeed row">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <div <?php post_class('col-sm-18'); ?>>
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <div class="entry-content"><?php the_content(); ?></div>
            </div><!--hentry-->

        <?php
        endwhile;
    endif;
    ?>

    <?php
    global $wp_query;
    $post = $wp_query->post;
    $postID = $post->ID;
    $parent = $post->post_parent;

//add the about-page sidebar to just the about page and any child about page
    if ($postID == "49" || $parent == "49") {
        openlab_bp_sidebar('about');
    }
    ?>

</div><!--#content-->

<?php get_footer(); ?>
