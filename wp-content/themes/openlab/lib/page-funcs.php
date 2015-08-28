<?php
/**
 * 	Home page functionality
 *
 */

/**
 * 	Home page login box layout
 *
 */
function cuny_home_login() {

    if (is_user_logged_in()) :

        echo '<div id="open-lab-login" class="log-box">';
        echo '<h2 class="title">Welcome, ' . bp_core_get_user_displayname(bp_loggedin_user_id()) . '</h2>';
        do_action('bp_before_sidebar_me')
        ?>

        <div id="sidebar-me" class="clearfix">
            <div id="user-info">
                <a class="avatar" href="<?php echo bp_loggedin_user_domain() ?>">
                    <img class="img-responsive" src="<?php bp_loggedin_user_avatar(array('type' => 'full', 'html' => false)); ?>" alt="Avatar for <?php echo bp_core_get_user_displayname(bp_loggedin_user_id()); ?>" />
                </a>

                <ul class="content-list">
                    <li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url(bp_get_root_domain()) ?>">Not <?php echo bp_core_get_username(bp_loggedin_user_id()); ?>?</a></li>
                    <li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url(bp_get_root_domain()) ?>"><?php _e('Log Out', 'buddypress') ?></a></li>
                </ul>
                </span><!--user-info-->
            </div>
            <?php do_action('bp_sidebar_me') ?>
        </div><!--sidebar-me-->

        <?php do_action('bp_after_sidebar_me') ?>

        <?php echo '</div>'; ?>

        <div id="login-help" class="log-box">
            <h4 class="title">Need Help?</h4>
            <p class="font-size font-14">Visit the <a class="roll-over-loss" href="<?php echo site_url(); ?>/blog/help/openlab-help/">Help section</a> or <a class="roll-over-loss" href='<?php echo site_url(); ?>/about/contact-us/'>contact us</a> with a question.</p>
        </div><!--login-help-->

    <?php else : ?>
        <?php echo '<div id="open-lab-join" class="log-box">'; ?>
        <?php echo '<h2 class="title"><span class="fa fa-plus-circle flush-left"></span> Join OpenLab</h2>'; ?>
        <?php _e('<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="' . site_url() . '/register/">Sign up</a> <span class="font-size font-14">Need an account?<br />Sign Up to become a member!</span></p>', 'buddypress') ?>
        <?php echo '</div>'; ?>

        <?php echo '<div id="open-lab-login" class="log-box">'; ?>
        <?php do_action('bp_after_sidebar_login_form') ?>
        <?php echo '</div>'; ?>

        <div id="user-login" class="log-box">

            <?php echo '<h2 class="title"><span class="fa fa-arrow-circle-right"></span> Log in</h2>'; ?>
            <?php do_action('bp_before_sidebar_login_form') ?>

            <form name="login-form" class="standard-form" action="<?php echo site_url('wp-login.php', 'login_post') ?>" method="post">
                <input class="form-control" type="text" name="log" id="sidebar-user-login" class="input" value="" placeholder="Username" tabindex="97" />

                <input class="form-control" type="password" name="pwd" id="sidebar-user-pass" class="input" value="" placeholder="Password" tabindex="98" />

                <div id="keep-logged-in" class="small-text clearfix">
                    <div class="password-wrapper">
                        <a class="forgot-password-link small-text roll-over-loss" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>">Forgot Password?</a>
                        <span class="keep-logged-in-checkbox"><input class="no-margin no-margin-top" name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /><?php _e('Keep me logged in', 'buddypress') ?></span>
                    </div>
                    <input class="btn btn-default btn-primary link-btn pull-right semibold" type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
                </div>
                <input type="hidden" name="redirect_to" value="<?php echo bp_get_root_domain(); ?>" />

                <?php do_action('bp_sidebar_login_form') ?>

            </form>
        </div>
    <?php
    endif;
}

/**
 * 	Home page new members box
 *
 */
function cuny_home_new_members() {
    global $wpdb, $bp;
    echo '<div id="new-members" class="box-1 left-box last">';
    echo '<h2 class="title uppercase">New OpenLab Members</h2>';
    echo '<div class="left-block-content new-members-wrapper">'
    ?>
    <div id="new-members-top-wrapper">
        <div id="new-members-text">
            <p><span class="new-member-navigation pull-right">
                    <a class="prev btn" href="#">
                        <i class="fa fa-chevron-circle-left"></i></a>
                    <a class="next btn" href="#">
                        <i class="fa fa-chevron-circle-right"></i></a>
                </span>
                Browse through and say "Hello!" to the<br />newest members of OpenLab.</p>
        </div>
        <div class="clearfloat"></div>
    </div><!--members-top-wrapper-->
    <?php
    if (bp_has_members('type=newest&max=5')) :
        $avatar_args = array(
            'type' => 'full',
            'width' => 121,
            'height' => 121,
            'class' => 'avatar',
            'id' => false,
            'alt' => __('Member avatar', 'buddypress')
        );
        echo '<div id="home-new-member-wrap"><ul>';
        while (bp_members()) : bp_the_member();
            $user_id = bp_get_member_user_id();
            $firstname = xprofile_get_field_data('Name', $user_id);
            ?>
            <li class="home-new-member">
                <div class="home-new-member-avatar">
                    <a href="<?php bp_member_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $user_id, 'object' => 'member', 'type' => 'full', 'html' => false)) ?>" alt="<?= $firstname ?>"/></a>
                </div>
                <div class="home-new-member-info">
                    <h2 class="truncate-on-the-fly load-delay" data-basevalue="16" data-minvalue="11" data-basewidth="164"><?= $firstname ?></h2>
                    <span class="original-copy hidden"><?= $firstname ?></span>
                    <div class="registered timestamp"><?php bp_member_registered() ?></div>
                </div>
            </li>
            <?php
        endwhile;
        echo '</ul></div>';
    endif;
    echo '</div></div>';
}

/**
 * 	Home page Who's Online box
 *
 */
function cuny_whos_online() {
    global $wpdb, $bp;
    $avatar_args = array(
        'type' => 'full',
        'width' => 45,
        'height' => 45,
        'class' => 'avatar',
        'id' => false,
        'alt' => __('Member avatar', 'buddypress')
    );

    $sql = "SELECT user_id FROM wp_usermeta where meta_key='last_activity' and meta_value >= DATE_SUB( UTC_TIMESTAMP(), INTERVAL 1 HOUR ) order by meta_value desc limit 20";

    $rs = $wpdb->get_results($sql);
    //print_r($rs);
    $ids = "9999999";
    foreach ((array) $rs as $r)
        $ids.= "," . $r->user_id;
    $x = 0;
    if (bp_has_members('type=active&include=' . $ids)) :
        $x+=1;
        ?>

        <div class="avatar-block left-block-content clearfix">
            <?php
            while (bp_members()) : bp_the_member();
                global $members_template;
                $member = $members_template->member;
                ?>

                <?php ?>
                <div class="cuny-member">
                    <div class="item-avatar">
                        <a href="<?php bp_member_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $member->ID, 'object' => 'member', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo $group->name; ?>"/></a>
                    </div>
                    <div class="cuny-member-info">
                        <a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a><br />
                        <?php
                        do_action('bp_directory_members_item');
                        bp_member_profile_data('field=Account Type');
                        ?>,
                        <?php bp_member_last_active() ?>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
        <?php
    endif;
}

/**
 * 	Home page latest group columns
 *
 */
function cuny_home_square($type) {
    global $wpdb, $bp;

    if (!bp_is_active('groups')) {
        return;
    }

    $meta_filter = new BP_Groups_Meta_Filter(array(
        'wds_group_type' => $type
    ));

    $i = 1;

    $groups_args = array(
        'max' => 4,
        'type' => 'active',
        'user_id' => 0,
        'show_hidden' => false
    );

    if (bp_has_groups($groups_args)) :
        ?>

        <?php
        /* Let's save some queries and get the most recent activity in one fell swoop */

        global $groups_template;

        $group_ids = array();
        foreach ($groups_template->groups as $g) {
            $group_ids[] = $g->id;
        }
        $group_ids_sql = implode(',', $group_ids);
        ?>


        <div class="col-sm-6 activity-list <?php echo $type; ?>-list">
            <div class="activity-wrapper">
                <div class="title-wrapper">
                    <h2 class="title activity-title"><a class="no-deco" href="<?php echo site_url() . '/' . strtolower($type); ?>s"><?php echo ucfirst($type); ?>s<span class="fa fa-chevron-circle-right"></span></a></h2>
                </div><!--title-wrapper-->
                <?php
                while (bp_groups()) : bp_the_group();
                    $group = $groups_template->group;

                    // Showing descriptions for now. http://openlab.citytech.cuny.edu/redmine/issues/291
                    // $activity = !empty( $group_activity_items[$group->id] ) ? $group_activity_items[$group->id] : stripslashes( $group->description );
                    $activity = stripslashes($group->description);
                    echo '<div class="box-1 row-' . $i . ' activity-item type-' . $type . '">';
                    ?>
                    <div class="item-avatar">
                        <a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $group->id, 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo $group->name; ?>"/></a>
                    </div>
                    <div class="item-content-wrapper">
                        <h4 class="group-title overflow-hidden">
                            <a class="no-deco truncate-on-the-fly hyphenate" href="<?= bp_get_group_permalink() ?>" data-basevalue="40" data-minvalue="15" data-basewidth="145"><?= bp_get_group_name() ?></a>
                            <span class="original-copy hidden"><?= bp_get_group_name() ?></span>
                        </h4>

                        <p class="hyphenate overflow-hidden">
                            <?= bp_create_excerpt($activity, 150, array('ending' => __('&hellip;', 'buddypress'), 'html' => false)) ?>
                        </p>
                        <p class="see-more">
                            <a class="semibold" href="<?= bp_get_group_permalink() ?>">See More</a>
                        </p>
                        </div>
                        </div>
                    <?php
                        $i++;
                    endwhile;
                    ?>
                </div>
            </div><!--activity-list-->

            <?php
        endif;

        $meta_filter->remove_filters();
    }

    /**
     * 	openlab_groups_filter_clause()
     *
     */
    function openlab_groups_filter_clause($sql) {
        global $openlab_group_type, $bp;

        // Join to groupmeta table for group type
        $ex = explode(" WHERE ", $sql);
        $ex[0] .= ", " . $bp->groups->table_name_groupmeta . " gt";
        $ex = implode(" WHERE ", $ex);

        // Add the necessary where clause
        $ex = explode(" AND ", $ex);
        array_splice($ex, 1, 0, "g.status = 'public' AND gt.group_id = g.id AND gt.meta_key = 'wds_group_type' AND ( gt.meta_value = '" . ucwords($openlab_group_type) . "' OR gt.meta_value = '" . strtolower($openlab_group_type) . "' )");
        $ex = implode(" AND ", $ex);

        return $ex;
    }

    /**
     * 	Registration page layout
     *
     */
    function openlab_registration_page() {
        do_action('bp_before_register_page')
        ?>

        <div class="page" id="register-page">

            <h1 class="entry-title"><?php _e('Create an Account', 'buddypress') ?></h1>

            <form action="" name="signup_form" id="signup_form" class="standard-form form-panel" method="post" enctype="multipart/form-data">

                <?php if ('request-details' == bp_get_current_signup_step()) : ?>

                    <div class="panel panel-default">
                        <div class="panel-heading semibold">Account Details</div>
                        <div class="panel-body">

                            <?php do_action('template_notices') ?>

                            <p><?php _e('Registering for the City Tech OpenLab is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'buddypress') ?></p>
                            <p>Because the OpenLab is a space for collaboration between members of the City Tech community, a City Tech email address is required to use the site.</p>
                            <?php do_action('bp_before_account_details_fields') ?>

                            <div class="register-section" id="basic-details-section">

                                <?php /*                                 * *** Basic Account Details ***** */ ?>

                                <label for="signup_username"><?php _e('Username', 'buddypress') ?> <?php _e('(required)', 'buddypress') ?> (lowercase & no special characters)</label>
                                <?php do_action('bp_signup_username_errors') ?>
                                <input class="form-control" type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />

                                <label for="signup_email"><?php _e('Email Address (required) <div class="email-requirements">Please use your City Tech email address to register</div>', 'buddypress') ?> </label>
                                <?php do_action('bp_signup_email_errors') ?>
                                <input class="form-control" type="text" name="signup_email" id="signup_email" value="<?php echo openlab_post_value('signup_email') ?>" />

                                <label for="signup_email_confirm">Confirm Email Address (required)</label>
                                <input class="form-control" type="text" name="signup_email_confirm" id="signup_email_confirm" value="<?php echo openlab_post_value('signup_email_confirm') ?>" />

                                <label for="signup_password"><?php _e('Choose a Password', 'buddypress') ?> <?php _e('(required)', 'buddypress') ?></label>
                                <?php do_action('bp_signup_password_errors') ?>
                                <input class="form-control" type="password" name="signup_password" id="signup_password" value="" />

                                <label for="signup_password_confirm"><?php _e('Confirm Password', 'buddypress') ?> <?php _e('(required)', 'buddypress') ?></label>
                                <?php do_action('bp_signup_password_confirm_errors') ?>
                                <input class="form-control" type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />

                            </div><!-- #basic-details-section -->
                        </div>
                    </div><!--.panel-->

                    <?php do_action('bp_after_account_details_fields') ?>

                    <?php /*                     * *** Extra Profile Details ***** */ ?>

                    <?php if (bp_is_active('xprofile')) : ?>

                        <div class="panel panel-default">
                            <div class="panel-heading semibold">Public Profile Details</div>
                            <div class="panel-body">

                                <?php do_action('bp_before_signup_profile_fields') ?>

                                <div class="register-section" id="profile-details-section">

                                    <p>Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.</p>

                                    <?php echo wds_get_register_fields('Base'); ?>

                                    <?php do_action('bp_after_signup_profile_fields') ?>

                                </div><!-- #profile-details-section -->
                            </div>
                        </div><!--.panel-->



                    <?php endif; ?>

                    <?php do_action('bp_before_registration_submit_buttons') ?>

                    <p class="sign-up-terms">
                        By clicking "Complete Sign Up", I agree to the <a class="underline" href="<?php echo home_url('about/terms-of-service') ?>" target="_blank">OpenLab Terms of Use</a> and <a class="underline" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a>.
                    </p>

                    <div class="submit">
                        <input style="display:none;" type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary" value="<?php _e('Complete Sign Up', 'buddypress') ?>" />
                    </div>

                    <?php do_action('bp_after_registration_submit_buttons') ?>

                    <?php wp_nonce_field('bp_new_signup') ?>

                <?php endif; // request-details signup step    ?>

                <?php if ('completed-confirmation' == bp_get_current_signup_step()) : ?>

                    <div class="panel panel-default">
                        <div class="panel-heading semibold"><?php _e('Sign Up Complete!', 'buddypress') ?></div>
                        <div class="panel-body">

                            <?php do_action('template_notices') ?>

                            <?php if (bp_registration_needs_activation()) : ?>
                                <p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e('You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress') ?></p>
                            <?php else : ?>
                                <p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e('You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress') ?></p>
                            <?php endif; ?>

                        </div>
                    </div><!--.panel-->

                <?php endif; // completed-confirmation signup step    ?>

                <?php do_action('bp_custom_signup_steps') ?>

            </form>

        </div>

        <?php do_action('bp_after_register_page') ?>

        <?php do_action('bp_after_directory_activity_content') ?>

        <script type="text/javascript">
            jQuery(document).ready(function () {
                if (jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show'))
                    jQuery('div#blog-details').toggle();

                jQuery('input#signup_with_blog').click(function () {
                    jQuery('div#blog-details').fadeOut().toggle();
                });
            });
        </script>
        <?php
    }
    