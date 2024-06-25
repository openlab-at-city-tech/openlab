<div class="ab-sub-wrapper">
    <div class="ab-submenu">
        <form name="login-form" style="display:none;" id="sidebar-login-form" class="standard-form form" action="<?php echo site_url("wp-login.php", "login_post") ?>" method="post">
            <label><?php _e("Username", "buddypress") ?><br /><input type="text" name="log" id="dropdown-user-login" class="input form-control" value="" /></label><br />
            <label><?php _e("Password", "buddypress") ?><br /><input class="form-control" type="password" name="pwd" id="dropdown-user-pass" class="input" value="" /></label>
            <p class="forgetmenot checkbox"><label><input name="rememberme" type="checkbox" id="dropdown-rememberme" value="forever" /> <?php _e("Keep Me Logged In", "buddypress") ?></label></p>
            <input type="hidden" name="redirect_to" value="<?php echo bp_get_root_domain() . esc_url( $request_uri ); ?>" />
            <input type="submit" name="wp-submit" id="dropdown-wp-submit" class="btn btn-primary sidebar-wp-submit" value="<?php _e("Log In"); ?>" tabindex="0" />
            <span class="exit"><a href="<?php echo wp_lostpassword_url(); ?>" class="lost-pw">Forgot Password?</a></span>
        </form>
    </div>
</div>

