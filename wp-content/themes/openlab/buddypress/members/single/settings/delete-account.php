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

        <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" name="account-delete-form" id="account-delete-form" class="standard-form" method="post">
            <div class="bp-template-notice error margin-bottom">
                <p><?php _e('WARNING: Deleting your account will completely remove ALL content associated with it. There is no way back, please be careful with this option.', 'buddypress'); ?></p>
            </div>
            <div class="checkbox no-margin no-margin-bottom">
                <label>
                    <input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if (this.checked) {
                                document.getElementById('delete-account-button').disabled = '';
                            } else {
                                document.getElementById('delete-account-button').disabled = 'disabled';
                            }" /> 
                            <?php _e('I understand the consequences of deleting my account.', 'buddypress'); ?>
                    </label>
                    </div>
            <?php do_action('bp_members_delete_account_before_submit'); ?>
            <div class="submit">
                <input type="submit" disabled="disabled" value="<?php _e('Delete My Account', 'buddypress') ?>" id="delete-account-button" class="btn btn-primary btn-margin btn-margin-top" name="delete-account-button" />
            </div>

    <?php do_action('bp_members_delete_account_after_submit');
    wp_nonce_field('delete-account'); ?>
        </form>

    <?php do_action('bp_after_member_body'); ?>
    </div><!-- #item-body -->
    <?php
    do_action('bp_after_member_settings_template');