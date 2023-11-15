<?php
/**
 * front page
 *
 * Note to themers: home-right appears before home-left in this template file,
 * to make responsive styling easier
 */
get_header();
?>

<h1 class="site-title sr-only"><?php bloginfo( 'name' ); ?></h1>

<div id="openlab-main-content" class="clearfix row-home-top" role="main">
    <div class="no-gutter no-gutter-right login">
        <div id="cuny_openlab_jump_start">
            <?php cuny_home_login(); ?>
        </div>
    </div>
    <div class="fill-gutter fill-gutter-left slider">
        <?php echo openlab_get_home_slider(); ?>
    </div>

</div>
<div class="row row-home-bottom" role="complementary">
    <div id="home-left" class="col-sm-8">
        <?php dynamic_sidebar('cac-featured') ?>

        <div class="box-1 left-box">
            <h2 class="title uppercase clearfix">Stay Up-To-Date With The OpenLab</h2>
            <div id="stay-up-to-date" class="left-block-content stay-up-to-date-wrapper">
                <?php echo openlab_stay_up_to_date(); ?>
            </div>
        </div>

        <div class="box-1 left-box">
            <h2 class="title uppercase clearfix"><i id="refreshActivity" class="fa fa-refresh pull-right" aria-hidden="true"></i>What's Happening On The OpenLab?</h2>
            <div id="whatsHappening" class="left-block-content whats-happening-wrapper">
                <?php echo openlab_whats_happening(); ?>
            </div>
        </div>

        <div class="box-1 left-box" id="whos-online">
            <h2 class="title uppercase">Who's Online?</h2>
            <?php cuny_whos_online(); ?>
        </div>
    </div>
    <div id="home-right" class="col-sm-16">
        <div id="home-group-list-wrapper" class="row">
            <?php cuny_home_square('course'); ?>
            <?php cuny_home_square('project'); ?>
            <?php cuny_home_square('club'); ?>
            <?php cuny_home_square('portfolio'); ?>
            <div class="clearfloat"></div>
            <script type='text/javascript'>(function ($) {
                    $('.activity-list').css('visibility', 'hidden');
                    $('#home-new-member-wrap').css('visibility', 'hidden');
                })(jQuery);</script>
        </div>
    </div>
</div>

<?php
get_footer();

