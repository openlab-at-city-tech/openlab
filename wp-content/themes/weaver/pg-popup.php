<?php
/**
 * Template Name: Pop Up
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 */

get_header('popup'); ?>
<!-- Weaver Wrapper Only -->
<?php
if (have_posts()) {
    the_post();
    weaver_page_content();
}
    // edit_post_link( __( 'Edit', WEAVER_TRANS ), '<span class="edit-link">', '</span>' ); ?>
<?php get_footer(); ?>
