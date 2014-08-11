<?php
/* Template Name: Group Archive */
/* * begin layout* */
get_header();
?>

<div id="content" class="hfeed row">
    <div <?php post_class('col-sm-18'); ?>>
        <h1 class="entry-title"><?php echo ucfirst(openlab_page_slug_to_grouptype()) . 's'; ?> on the OpenLab</h1>

        <div class="entry-content">
            <?php openlab_group_archive(); ?>
        </div><!--entry-content-->
    </div><!--hentry-->

    <?php openlab_bp_sidebar('groups'); ?>
</div><!--content-->

<?php
get_footer();
/**end layout**/