<?php get_header(); ?>

<div id="content" class="hfeed row">
    <?php
    global $wp_query;
    $post = $wp_query->post;
    $postID = $post->ID;
    $parent = $post->post_parent;

    if ($postID == "49" || $parent == "49") {
        openlab_bp_mobile_sidebar('about');
    }
    ?>

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    
            <?php //my-<group> pages should not be displaying this ?>
            <?php if (!strstr(get_the_title(),'My')): ?>
                <div <?php post_class('col-sm-18 col-xs-24'); ?>>
                    <div class="content-wrapper">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-content"><?php the_content(); ?></div>
                    </div>
                </div><!--hentry-->
            <?php endif; ?>

            <?php
        endwhile;
    endif;
    ?>

    <?php
//add the about-page sidebar to just the about page and any child about page
    if ($postID == "49" || $parent == "49") {
        openlab_bp_sidebar('about');
    }
    ?>

</div><!--#content-->

<?php get_footer(); ?>
