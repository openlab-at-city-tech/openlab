<?php
if ( ! function_exists( 'add_action' ) ) {
	exit();
}

require_once( S2PATH . 'classes/class-s2-forms.php' );
$s2_forms = new S2_Forms;
$s2_forms->init();

// was anything POSTed?
if ( isset( $_POST['s2_admin'] ) && 'user' === $_POST['s2_admin'] ) {
	if ( false === wp_verify_nonce( $_REQUEST['_wpnonce'], 'subscribe2-user_subscribers' . S2VERSION ) ) {
		die( '<p>' . __( 'Security error! Your request cannot be completed.', 'subscribe2' ) . '</p>' );
	}
	do_action( 's2_subscription_submit' );
}

// show our form
echo '<div class="wrap">';

global $user_ID;
$userid = $s2_forms->get_userid();
$user = get_userdata( $userid );

if ( $userid === $user_ID ) {
	echo '<h1>' . __( 'Your Notification Settings', 'subscribe2' ) . "</h1>\r\n";
} else {
	echo '<h1>' . __( 'Notification Settings for user:', 'subscribe2' ) . ' <span style="color: red;">' . $user->display_name . '</span></h1>' . "\r\n";
}


echo '<form method="post">';
wp_nonce_field( 'subscribe2-user_subscribers' . S2VERSION );

echo '<p>';

do_action( 's2_subscription_form', $userid );

// submit
echo '<p class="submit"><input type="submit" class="button-primary" name="submit" value="' . __( 'Update Preferences', 'subscribe2' ) . ' &raquo;" /></p>';
echo '</form>' . "\r\n";

echo '</div>' . "\r\n";

include( ABSPATH . 'wp-admin/admin-footer.php' );
// just to be sure
die;
?>