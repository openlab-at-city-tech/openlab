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
			<h2>Deleting your account</h2>

			<p>You may wish to export your work before deleting it so you can save a copy for future use. Find out more about <a href="https://openlab.citytech.cuny.edu/blog/help/exporting-your-work">exporting your work</a>.</p>

			<p><strong>When you delete your account, the following will be deleted:</strong></p>

			<ul>
				<li>Your OpenLab profile and account information</li>
				<li>Pages on OpenLab sites created by you</li>
				<li>Discussion topics and replies created by you</li>
				<li>Docs created by you</li>
				<li>Files and other media posted by you.</li>
			</ul>

			<p><strong>Site posts and comments written by you will not be deleted, but the author will be changed to "Account Deleted."</strong> This is because posts and comments may be part of a conversation with other OpenLab members. If you want to delete these items, you should first delete them and then delete your account.</p>

			<p><strong>Courses, projects, clubs, and portfolios created by you will not be deleted.</strong> This is because they may include important work created by you and others. If you want to delete these items, you should first delete them and then delete your account.</p>

			<p><strong>Please consider other OpenLab members.</strong> For instance, if you created a group project, others may still need the work they did there. Instead of deleting the whole project, you could just delete your own work.</p>

			<p><strong>Questions?</strong> <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us/">Contact us.</a> We're happy to help!</p>

            <div class="bp-template-notice error margin-bottom">
				<p>WARNING: Deleting your account will remove the information described above. There is no way back, please be careful with this option.</p>
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
