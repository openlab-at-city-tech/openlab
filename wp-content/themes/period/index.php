<?php get_header();

get_template_part( 'content/archive-header' );

do_action( 'after_archive_header' ); ?>

<div id="loop-container" class="loop-container">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ct_period_get_content_template();
        endwhile;
    endif;
    ?>
</div><?php

ct_period_pagination();

get_footer();