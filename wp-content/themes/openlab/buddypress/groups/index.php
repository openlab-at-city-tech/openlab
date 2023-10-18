<?php
/* Template Name: My Group Template */
get_header();
global $bp;
?>

<div id="content" class="hfeed row">

    <?php
    openlab_bp_mobile_sidebar('members');
    $account_type = openlab_get_user_member_type_label( bp_loggedin_user_id() );
    ?>

    <div class="col-sm-18 col-xs-24 my-groups-grid" role="main">
        <h1 class="entry-title mol-title">
            <span class="profile-name"><?php echo $bp->loggedin_user->fullname . '&rsquo;s'; ?> Profile</span>
            <span class="profile-type pull-right hidden-xs"><?php echo esc_html( $account_type ); ?></span>
            <button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </h1>
        <div class="clearfix visible-xs">
            <span class="profile-type pull-left"><?php echo esc_html( $account_type ); ?></span>
        </div>
        <?php bp_get_template_part('groups/groups', 'loop'); ?>
    </div>

    <?php openlab_bp_sidebar('members'); ?>
</div><!--content-->

<?php
get_footer();
