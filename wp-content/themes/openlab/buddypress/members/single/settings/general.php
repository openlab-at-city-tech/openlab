<?php
/**
 * Members settings - general
 *
 * */
do_action('bp_before_member_settings_template');

$account_type = openlab_get_user_member_type( bp_displayed_user_id() );

?>

<?php echo openlab_submenu_markup(); ?>


<div id="item-body" role="main">

    <?php do_action('bp_template_content') ?>

    <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form form-panel" id="settings-form">

        <div class="panel panel-default">
            <div class="panel-heading">Account Settings</div>
                <div class="panel-body">
	            	<?php do_action( 'template_notices' ); ?>

        <div class="form-group settings-section username-section">
            <label for="username">Username</label>
            <input class="form-control" type="text" id="username" disabled="disabled" value="<?php bp_displayed_user_username() ?>" />
            <p class="description">Your username cannot be changed. If you need to change your username, <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us/">contact us</a> for assistance.</p>
        </div>

        <div class="form-group settings-section email-section">
            <label for="email_visible">Account Email Address</label>
            <input class="form-control" type="text" name="email_visible" id="email_visible" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" disabled="disabled" />
            <input type="hidden" name="email" value="<?php echo bp_get_displayed_user_email() ?>" />
            <p class="description">Your email address cannot be changed. If your City Tech email address has changed, <a class="underline" href="<?php bp_get_root_domain(); ?>/about/contact-us">contact us</a> for assistance.</p>
        </div>

        <div class="form-group settings-section name-section">
            <label for="fname">First Name (required)</label>
            <input class="form-control" type="text" name="fname" id="fname" value="<?php echo bp_get_profile_field_data(array('field' => 'First Name')) ?>" />

            <label for="lname">Last Name (required)</label>
            <input class="form-control" type="text" name="lname" id="lname" value="<?php echo bp_get_profile_field_data(array('field' => 'Last Name')) ?>" />
        </div>

        <?php if ( in_array( $account_type, array('student', 'alumni') ) ) : ?>
            <div class="form-group settings-section account-type-section">
                <label for="openlab-account-type">Account Type</label>
                <select class="form-control" name="openlab-account-type" id="openlab-account-type">
                    <option value="student" <?php selected( 'student', $account_type ) ?>>Student</option>
                    <option value="alumni" <?php selected( 'alumni', $account_type ) ?>>Alumni</option>
                </select>
            </div>
        <?php endif ?>

        <div class="form-group settings-section change-pw-section">
            <label for="pass1">Change Password</label>

			<p>To change your password, visit the <a href="<?php echo site_url(add_query_arg(array('action' => 'lostpassword'), 'wp-login.php'), 'login'); ?>" title="<?php _e('Password Lost and Found', 'buddypress'); ?>">Reset Password</a> page.</p>
        </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Two-factor Authentication</div>

			<div class="panel-body">
				<?php openlab_2fa_settings(); ?>
			</div>
		</div>


        <?php do_action('bp_core_general_settings_before_submit'); ?>

        <div class="submit">
            <input class="btn btn-primary btn-margin btn-margin-top" type="submit" name="submit" value="<?php _e('Save Changes', 'buddypress'); ?>" id="submit" class="auto" />
        </div>

        <?php do_action('bp_core_general_settings_after_submit');
        wp_nonce_field('bp_settings_general');
        ?>
    </form>
<?php do_action('bp_after_member_body'); ?>
</div><!-- #item-body -->
<?php
do_action('bp_after_member_settings_template');
