<?php get_header(); ?>

<div id="content" class="hfeed row">
    <?php
    global $wp_query;
    $post = $wp_query->post;
    $postID = $post->ID;
    $parent = $post->post_parent;

    $about_page_obj = get_page_by_path('about');
    $calendar_page_obj = get_page_by_path('about/calendar');

    if ($postID == $about_page_obj->ID || $parent == $about_page_obj->ID || $parent == $calendar_page_obj->ID) {
        openlab_bp_mobile_sidebar('about');
    }
    ?>

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <?php //my-<group> pages should not be displaying this ?>
            <?php if (!strstr(get_the_title(), 'My')): ?>
                <div <?php post_class('col-sm-18 col-xs-24'); ?>>
                    <div id="openlab-main-content"  class="content-wrapper">
                        <h1 class="entry-title"><span class="profile-name"><?php the_title(); ?></span>
                            <?php if ($postID == $about_page_obj->ID || $parent == $about_page_obj->ID || $parent == $calendar_page_obj->ID): ?>
                                <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            <?php endif; ?>
							<?php if ( \OpenLab\PrintThisPage\show_for_post( get_the_ID() ) ) : ?>
								<span class="print-link pull-right hidden-xs"><a class="print-page" href="#"><span class="fa fa-print"></span> Print this page</a></span></h1>
							<?php endif; ?>
                        </h1>
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
    if ($postID == $about_page_obj->ID || $parent == $about_page_obj->ID || $parent == $calendar_page_obj->ID) {
        openlab_bp_sidebar('about');
    }
    ?>

</div><!--#content-->

<?php get_footer(); ?>
