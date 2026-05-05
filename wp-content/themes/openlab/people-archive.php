<?php
/* Template Name: People Archive */
get_header();

global $wp_query;
$post_obj = $wp_query->get_queried_object();

// Register the people archive mobile drawer.
$drawer_id = 'archive-people-drawer';
$drawer_title = 'Find People';
openlab_register_archive_mobile_drawer( $drawer_id, $drawer_title );
?>
<div id="content" class="hfeed row">
    <?php openlab_bp_sidebar('groups',true); ?>
    <div <?php post_class('col-sm-18 col-xs-24'); ?>>
        <div id="openlab-main-content" class="content-wrapper" tabindex="-1">
            <h1 class="entry-title"><?php echo $post_obj->post_title; ?> on the OpenLab <button class="drawer-toggle mobile-toggle pull-right visible-xs" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $drawer_id ); ?>" data-drawer-toggle="<?php echo esc_attr( $drawer_id ); ?>"><span class="toggle-icon"></span><span class="sr-only">Search</span></button></h1>
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
