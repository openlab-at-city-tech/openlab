<?php

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_delete_profile' );

function cuny_delete_profile(){

do_action( 'bp_before_member_settings_template' );
?>
	<div class="submenu">My Settings: <?php echo openlab_profile_settings_submenu(); ?></div>

	<div id="item-body" role="main">

		<?php do_action( 'bp_before_member_body' ); ?>

		<?php do_action( 'bp_template_content' ) ?>

		<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">
			<div class="settings-section username-section">
				<label for="username">Username (required)</label>
				<input type="text" id="username" disabled="disabled" value="<?php bp_displayed_user_username() ?>" />
				<p class="description">Your username cannot be changed</p>
			</div>

			<div class="settings-section email-section">
				<label for="email_visible">Account Email Address (required)</label>
				<input type="text" name="email_visible" id="email_visible" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" disabled="disabled" />
				<input type="hidden" name="email" value="<?php echo bp_get_displayed_user_email() ?>" />
				<p class="description">Your email address cannot be changed. If your City Tech email address has changed, contact us for assistance.</p>
			</div>

			<div class="settings-section current-pw-section">
				<label for="pwd">Current Password</label>
				<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" />
				<p class="description">Required to update email or change current password. <a href="<?php echo site_url( add_query_arg( array( 'action' => 'lostpassword' ), 'wp-login.php' ), 'login' ); ?>" title="<?php _e( 'Password Lost and Found', 'buddypress' ); ?>"><?php _e( 'Lost your password?', 'buddypress' ); ?></a></p>
			</div>

			<div class="settings-section change-pw-section">
				<label for="pass1">Change Password</label>
				<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" />

				<label for="pass1">Confirm Change Password</label>
				<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" />

				<p class="description">Leave blank for no change</p>
			</div>

			<div class="settings-section name-section">
				<label for="fname">First Name (required)</label>
				<input type="text" name="fname" id="fname" value="<?php echo bp_get_profile_field_data( array( 'field' => 'First Name' ) ) ?>" />

				<label for="lname">Last Name (required)</label>
				<input type="text" name="lname" id="lname" value="<?php echo bp_get_profile_field_data( array( 'field' => 'Last Name' ) ) ?>" />
			</div>

			<?php do_action( 'bp_core_general_settings_before_submit' ); ?>

			<div class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
			</div>

			<?php do_action( 'bp_core_general_settings_after_submit' ); wp_nonce_field( 'bp_settings_general' ); ?>
		</form>
		<?php do_action( 'bp_after_member_body' ); ?>
	</div><!-- #item-body -->
<?php
do_action( 'bp_after_member_settings_template' );
}
remove_action('genesis_sidebar','genesis_do_sidebar');
add_action( 'genesis_before_sidebar_widget_area', create_function( '', 'include( get_stylesheet_directory() . "/members/single/sidebar.php" );' ) );

genesis();
?>