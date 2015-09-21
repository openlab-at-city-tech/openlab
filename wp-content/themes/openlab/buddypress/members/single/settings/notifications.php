<?php
/**
 * Members settings - email notifications settings
 *
 * */
do_action('bp_before_member_settings_template');
?>
<?php echo openlab_submenu_markup(); ?>

<div id="item-body" role="main">

    <?php do_action('bp_template_content') ?>

    <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form form-panel" id="settings-form">

        <div class="panel panel-default">
            <div class="panel-heading">Email Notifications</div>
            <div class="panel-body">

                <p><?php _e('Send a notification by email when:', 'buddypress'); ?></p>

                <?php do_action('bp_notification_settings'); ?>
                <?php do_action('bp_members_notification_settings_before_submit'); ?>
            </div>
        </div>

        <div class="submit">
            <input type="submit" name="submit" value="<?php _e('Save Changes', 'buddypress'); ?>" id="submit" class="auto btn btn-primary btn-margin btn-margin-top" />
        </div>

        <?php do_action('bp_members_notification_settings_after_submit');
        wp_nonce_field('bp_settings_notifications');
        ?>

    </form>
<?php do_action('bp_after_member_body'); ?>
</div><!-- #item-body -->
<?php
do_action('bp_after_member_settings_template');
