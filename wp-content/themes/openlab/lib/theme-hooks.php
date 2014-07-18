<?php
/**
 * Theme based hooks
 */
add_action('bp_header', 'openlab_header_bar', 10);

function openlab_header_bar() {
    ?>

    <div id="header-wrap">
        <div id="title-area">
            <h1 id="title"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
        </div>

        <?php openlab_site_wide_bp_search(); ?>
        <div class="clearfloat"></div>
        <?php
        //this adds the main menu, controlled through the WP menu interface
        $args = array(
            'theme_location' => 'main',
            'container' => 'div',
            'container_class' => 'navbar navbar-collapse',
            'menu_class' => 'nav navbar-nav',
        );

        wp_nav_menu($args);
        if (is_user_logged_in()) {
            ?>
            <div id="extra-border"></div>
            <ul class="nav" id="openlab-link">
                <li<?php if (bp_is_my_profile()) : ?> class="current-menu-item"<?php endif ?>>
                    <a href="<?php echo bp_loggedin_user_domain() ?>">My OpenLab</a>
                </li>
            </ul>
    <?php } ?>
        <div class="clearfloat"></div>
    </div><!--#wrap-->
<?php
}
