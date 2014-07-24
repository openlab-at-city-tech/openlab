<?php get_header(); ?>

<div id="content" class="hfeed row">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <?php the_content(); ?>

            <?php
        endwhile;
    endif;
    ?>

</div><!--#content-->

<?php get_footer(); ?>
