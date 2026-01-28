<?php
/**
 * Favorites flyout for main site nav.
 */

?>

<div class="flyout-menu flyout-menu-login" id="login-flyout">
	<div class="flyout-menu-login-login">
		<form name="navbar-login-form" class="standard-form" action="<?php echo esc_attr( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
			<label for="navbar-user-login">Username</label>
			<input class="form-control input" type="text" name="log" id="navbar-user-login" value="" />

			<label for="navbar-user-pass">Password</label>
			<input class="form-control input" type="password" name="pwd" id="navbar-user-pass" value="" />

			<div class="navbar-forgot-password">
				<a class="forgot-password-link roll-over-loss" href="<?php echo esc_attr( site_url( 'wp-login.php?action=lostpassword', 'login' ) ); ?>">Forgot Password?</a>
			</div>

			<input class="btn btn-block link-btn semibold" type="submit" name="wp-submit" id="navbar-wp-submit" value="Sign In" tabindex="0" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( bp_get_requested_url() ); ?>" />
		</form>
	</div>

	<div class="flyout-menu-login-register">
		Need an account? <strong><a href="<?php echo esc_url( site_url( 'register' ) ); ?>" class="register-link">Sign Up</a></strong>
	</div>

	<button class="flyout-close-button sr-only sr-only-focusable" data-flyout-close="login-flyout" aria-label="Close Sign In menu">
		Close
	</button>
</div>
