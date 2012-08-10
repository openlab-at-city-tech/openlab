<?php
global $wpdb, $current_user, $post, $current_page_template;


//add_action('wp_print_styles', 'lightboxes_wp_print_styles', 1000);
//add_action('wp_print_scripts', 'lightboxes_wp_print_scripts' );

add_action('add_lightbox', 'lightbox_login');
//add_action('add_lightbox', 'lightbox_register');
//add_action('add_lightbox', 'lightbox_site_register');
//add_action('add_lightbox', 'lightbox_registering');
add_action('add_lightbox', 'lightbox_generic_response');





function lightboxes_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/lightboxes.css'); ?>" type="text/css" media="screen" />
<?php
}

/*
function lightboxes_wp_print_scripts(){	
	wp_enqueue_script('digressit.lightboxes', get_digressit_media_uri('js/digressit.lightboxes.js'), 'jquery', false, true );
}
*/


function get_lightboxes(){
	?>
	<div class="lightbox-transparency"></div>	
	<?php
	do_action('add_lightbox');
}


function lightbox_login(){ ?>
	
	<?php  /******* THIS IS LOGIN. LOAD ON EVERY PAGE *******/ ?>
	<?php if(!is_user_logged_in()): ?>
	<div class="lightbox-content" id="lightbox-login">
		<div class="ribbon">
			<div class="ribbon-left"></div>
			<div class="ribbon-title">Login</div>
			<div class="ribbon-right"></div>
		</div>
	
		<?php

		global $password_just_reset;
	
		$referer_url = parse_url($_SERVER['HTTP_REFERER']);
	
		//var_dump($referer_url);
		//  && $referer_url['scheme']."//".$referer_url['host'] == get_root_domain() 
		?>
		<?php if($_GET['account-enabled'] == '0'): ?>
			<p>Your account has not been enabled. Please check your inbox for your activation code</p>
		<?php endif; ?>
	
		<?php if($_GET['password_reset_key'] && $password_just_reset): ?>
			<p>Your password was reset.<br>Check your email for your new password</p>
		<?php endif; ?>
	
		<form method="post" action="<?php echo wp_login_url() ?>" id="login-form" name="loginform">
			<p>
				<label>Username<br />
				<input type="text" name="log" id="user_login" class="input required" value="" size="25" tabindex="10" /></label>
			</p>

			<p>
				<label>Password<br />
				<input type="password" name="pwd" id="user_pass" class="input required" value="" size="25" tabindex="20" /></label>
			</p>
			
			<?php if(has_action('custom_register_links')) :?>
				<?php do_action('custom_register_links'); ?>
			<?php else: ?>
				<p><a href="<?php echo get_bloginfo('home'); ?>/wp-signup.php"   title="Register Account"><?php _e('Register account'); ?></a></p>
				<p><a href="<?php echo wp_login_url(); ?>?action=lostpassword" title="Lost Password"><?php _e('Lost Password?'); ?></a></p>
				
			<?php endif; ?>

			<!--<input type="submit" name="wp-submit" id="wp-submit" value="Log In" tabindex="100" />-->
		
			<input type="hidden" name="wp-submit" value="Log In" id="wp-submit">
			<input type="hidden" name="redirect_to" value="<?php echo 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>#login-success" />
			<input type="hidden" name="testcookie" value="1" />

			<?php if (function_exists('sfc_version')): ?>
			<p><fb:login-button onlogin="sfc_login_check();" perms="email" v="2" class="FB_login_button FB_ElementReady fb_login_not_logged_in"><a class="fbconnect_login_button FBConnectButton FBConnectButton_Medium" id="RES_ID_fb_login"><span class="FBConnectButton_Text" id="RES_ID_fb_login_text"><fb:intl>Connect with Facebook</fb:intl></span></a></fb:login-button></p>
			<?php endif; ?>


			<span id="login-submit" class="lightbox-submit button-disabled"><span class="loading-bars"></span><?php _e('Login'); ?></span>
			<span class="lightbox-close"></span>
		
		</form>
	</div>
	<?php endif; ?>
<?php } 



function lightbox_register(){ ?>
<?php if(!is_user_logged_in()): ?>
<div class="lightbox-content" id="lightbox-register">	
	<div class="ribbon">
		<div class="ribbon-left"></div>
		<div class="ribbon-title"><?php _e('Register'); ?></div>
		<div class="ribbon-right"></div>
	</div>
	<form name="registerform" id="registerform" action="<?php echo wp_login_url(); ?>?action=register" method="post">		
		<div class="status-message error"></div>

		<div class="lightbox-slider">
		<div class="lightbox-slot">
			<p>
			<label><?php _e('Choose a username (required)'); ?><br>
			<input name="user_login" id="user_login" class="input required" value="" size="25" type="text">
			<div class="status"></div>
			</label>
			</p>
			<p>
			<label><?php _e('E-mail (required)'); ?><br>
			<input name="user_email" id="user_email" class="input required" value="" size="25" type="text"></label>
			</p>

			<?php do_action('lightbox_registration_extra_fields_slot_1'); ?>
		
		</div>

		<?php if(has_action('lightbox_registration_extra_fields')): ?>
		<div class="lightbox-slot">
		<?php do_action('lightbox_registration_extra_fields'); ?>
		</div>
		<?php endif; ?>
		</div>

		
		
		<input type="hidden" value="1" name="register-event">
		<div class="status"></div>
		<span class="lightbox-close"></span>
		
		<span class="lightbox-button lightbox-previous"><?php _e('Previous'); ?></span>
		<span id="register-submit" class="lightbox-submit button-disabled"><span class="loading-bars"></span><?php _e('Register'); ?></span>
		<span class="lightbox-button lightbox-next"><?php _e('Next'); ?></span>

	</form>

</div>
<?php endif; ?>
<?php } 




function lightbox_site_register(){ ?>
<?php if(!is_user_logged_in()): ?>
<div class="lightbox-content" id="lightbox-register">	
	<div class="ribbon">
		<div class="ribbon-left"></div>
		<div class="ribbon-title"><?php _e('Register'); ?></div>
		<div class="ribbon-right"></div>
	</div>
	<form name="registerform" id="registerform" action="<?php echo wp_login_url(); ?>?action=register" method="post">		
		<div class="status-message error"></div>

		<div class="lightbox-slider">
		<?php if(has_action('lightbox_registration_extra_fields')): ?>
		<div class="lightbox-slot">
		<?php do_action('lightbox_registration_extra_fields'); ?>
		</div>
		<?php endif; ?>
		</div>

		
		
		<input type="hidden" value="1" name="register-event">
		<div class="status"></div>
		<span class="lightbox-close"></span>
		
		<span class="lightbox-button lightbox-previous"><?php _e('Previous'); ?></span>
		<span id="register-submit" class="lightbox-submit button-disabled"><span class="loading-bars"></span><?php _e('Register'); ?></span>
		<span class="lightbox-button lightbox-next"><?php _e('Next'); ?></span>

	</form>

</div>
<?php endif; ?>
<?php } 




function lightbox_registering(){ ?>
<div class="lightbox-content" id="lightbox-registering">
	<p><?php _e('Your account has been created. Check your email for further instructions on how to log in.'); ?></p>
	<span class="lightbox-close"></span>
</div>
<?php } 


function lightbox_login_success(){ ?>
<div class="lightbox-content" id="lightbox-login-success">
	<p><?php _e('Login Successful'); ?></p>
	<span class="lightbox-delay-close"></span>
</div>
<?php } 

function lightbox_register_status(){ ?>
<div class="lightbox-content" id="lightbox-register-status">
	<p></p>
	<span class="lightbox-close"></span>
</div>
<?php
}

function lightbox_generic_response(){ ?>

<div class="lightbox-content" id="lightbox-generic-response">
	<span class="lightbox-close"></span>
	<p></p>
	
</div>
<?php 
}

?>