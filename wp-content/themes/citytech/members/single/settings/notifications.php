<?php

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_delete_profile' );

function cuny_delete_profile(){

do_action( 'bp_before_member_settings_template' ); 
?>
	<h1 class="entry-title"><?php bp_displayed_user_fullname() ?>'s Profile</h1>
	<div class="submenu">My Settings: <?php echo openlab_profile_settings_submenu(); ?></div>

	<div id="item-body" role="main">

		<?php do_action( 'bp_before_member_body' ); ?>

		<?php do_action( 'bp_template_content' ) ?>

		<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
			<p><?php _e( 'Send a notification by email when:', 'buddypress' ); ?></p>

			<?php do_action( 'bp_notification_settings' ); do_action( 'bp_members_notification_settings_before_submit' ); ?>

			<div class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
			</div>

			<?php do_action( 'bp_members_notification_settings_after_submit' ); wp_nonce_field('bp_settings_notifications'); ?>

		</form>
		<?php do_action( 'bp_after_member_body' ); ?>
	</div><!-- #item-body -->
<?php 
do_action( 'bp_after_member_settings_template' );

}

add_action( 'genesis_before_sidebar_widget_area', create_function( '', 'include( get_stylesheet_directory() . "/members/single/sidebar.php" );' ) );

genesis();
?>