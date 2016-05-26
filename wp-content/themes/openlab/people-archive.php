<?php
/* Template Name: People Archive */
get_header();
?>

<?php
global $wp_query;
$post_obj = $wp_query->get_queried_object();
?>
<div id="content" class="hfeed row">
    <?php openlab_bp_sidebar('groups',true); ?>
    <div <?php post_class('col-sm-18 col-xs-24'); ?>>
        <div id="openlab-main-content" class="content-wrapper">
            <h1 class="entry-title"><?php echo $post_obj->post_title; ?> on the OpenLab <button data-target="#sidebar" class="mobile-toggle direct-toggle pull-right visible-xs" type="button"><span class="fa fa-binoculars"></span></button></h1>
            <div class="entry-content">
                <div id="people-listing">
                    <?php openlab_list_members('more'); ?>
                </div><!--people-listing-->
            </div><!--entry-content-->
        </div>
    </div><!--hentry-->

</div><!--content-->

<?php
get_footer();
