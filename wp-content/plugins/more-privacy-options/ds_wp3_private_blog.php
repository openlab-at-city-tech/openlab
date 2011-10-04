<?php
/*
Plugin Name: More Privacy Options
Plugin URI:	http://dsader.snowotherway.org/wordpress-plugins/more-privacy-options/
Description: WP3.0 multisite "mu-plugin" to add more privacy options to the options-privacy and ms-blogs pages. Sitewide "Users Only" switch at SuperAdmin-->Options page. Just drop in mu-plugins.
Version: 3.0.1.3
Author: D. Sader
Author URI: http://dsader.snowotherway.org/

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

TODO
To allow everyone who is on-campus into the blog, while requiring those off-campus to log in. Modify function ds_users_authenticator().

Like this:

if (     (strncmp('155.47.', $_SERVER['REMOTE_ADDR'], 7 ) == 0)  || (is_user_logged_in())                  ) {
        // user is either logged in or at campus 
                }
else {
        // user is either not logged in or at campus      

      if( is_feed() ) {
...
}

Similarily:
A plugin to allow login from only certain ip:
http://w-shadow.com/blog/2008/11/07/restrict-login-by-ip-a-wordpress-plugin/
A plugin to ban certain ip:
http://wordpress.org/extend/plugins/wp-ban/

TODO protect files/attachments/images uploaded to protected blogs(.htaccess rewrites needed)
Pluginspiration: http://plugins.svn.wordpress.org/private-files/trunk/privatefiles.php



Extras to redirect login_url and wp_signup_location page

function ds_my_login_page() {
	$page = 'http://mysite.tld/login/';
	return $page;
}
add_filter( 'login_url', 'ds_my_login_page' );
//add_filter( 'logout_url', 'ds_my_login_page' ); // same or similar function for logout

function ds_my_signup_page() {
	$page = 'http://mysite.tld/signup/';
	return $page;
}
add_filter( 'wp_signup_location', 'ds_my_signup_page' );

*/

class ds_more_privacy_options {

	function ds_more_privacy_options() {
	}
	function ds_mail_super_admin() {
		global $wpdb, $blogname, $current_blog;
			$blog_id = $wpdb->blogid;
			$blog_public_old = $current_blog->public;
			$blog_public_new = get_blog_option($blog_id,'blog_public');
			
			$from_old = $this->ds_mail_super_admin_messages($blog_public_old);
						
			$to_new = $this->ds_mail_super_admin_messages($blog_public_new);			

			$email =  stripslashes( get_site_option('admin_email') );
			$subject = 'Blog '.$blogname.'('.$blog_id.') changed privacy setting from '.$from_old.' to '.$to_new;
			$message = 'Blog '.$blogname.'('.$blog_id.') changed privacy setting from '.$from_old.' to '.$to_new;
     	mail($email, $subject , $message);
	}
	function ds_mail_super_admin_messages($blog_public) {
			if ( '1' == $blog_public ) {
				return 'Visible(1)';
			}
			if ( '0' == $blog_public ) {
				return 'No Search(0)';
			}
			if ( '-1' == $blog_public ) {
				return 'Network Users Only(-1)';
			}
			if ( '-2' == $blog_public ) {
				return 'Site Members Only(-2)';
			}
			if ( '-3' == $blog_public ) {
				return 'Site Admins Only(-3)';
			}
	}
		
	
	function do_robots() {
		remove_action('do_robots', 'do_robots');

		header( 'Content-Type: text/plain; charset=utf-8' );

		do_action( 'do_robotstxt' );

		if ( '1' != get_option( 'blog_public' ) ) {
			echo "User-agent: *\n";
			echo "Disallow: /\n";
		} else {
			echo "User-agent: *\n";
			echo "Disallow:\n";
			echo "Disallow: /wp-admin\n";
			echo "Disallow: /wp-includes\n";
			echo "Disallow: /wp-login.php\n";
			echo "Disallow: /wp-content/plugins\n";
			echo "Disallow: /wp-content/cache\n";
			echo "Disallow: /wp-content/themes\n";
			echo "Disallow: /trackback\n";
			echo "Disallow: /comments\n";
		}
	}	
	function noindex() {
		remove_action( 'login_head', 'noindex' );
		remove_action( 'wp_head', 'noindex',1 );//priority 1

		// If the blog is not public, tell robots to go away.
		if ( '1' != get_option('blog_public') )
			echo "<meta name='robots' content='noindex,nofollow' />\n";
	}
	function privacy_ping_filter($sites) {
		remove_filter( 'option_ping_sites', 'privacy_ping_filter' );
		if ( '1' == get_option('blog_public') )
			return $sites;
		else
			return '';
	}
	//------------------------------------------------------------------------//
	//---Functions hooked into wpmu-blogs.php---------------------------------//
	//---TODO add messages to wpmu-blogs.php table----------------------------//
	function wpmu_blogs_add_privacy_options() { 
		global $details,$options;
		?>
		<h3 class="hndle"><span><?php _e( 'More Privacy Options' ); ?></span></h3>
			<input type='radio' name='blog[public]' value='1' <?php if( $details->public == '1' ) echo " checked"?>> <?php _e('Google-able') ?>&nbsp;&nbsp;
		<br />
	    	<input type='radio' name='blog[public]' value='0' <?php if( $details->public == '0' ) echo " checked"?>> <?php _e('No Google') ?> &nbsp;&nbsp;	    
		<br />
	    	<input type='radio' name='blog[public]' value='-1' <?php if( $details->public == '-1' ) echo " checked"?>> <?php _e('Network Registered Users Only') ?> &nbsp;&nbsp;
		<br />
	    	<input type='radio' name='blog[public]' value='-2' <?php if( $details->public == '-2' ) echo " checked"?>> <?php _e('Blog Members Only') ?> &nbsp;&nbsp;
		<br />
		    <input type='radio' name='blog[public]' value='-3' <?php if( $details->public == '-3' ) echo " checked"?>> <?php _e('Blog Admins Only') ?> &nbsp;&nbsp;
<p class="description"></p>		

		<?php
	}
	function wpmu_blogs_add_privacy_options_messages() {
		global $blog;
			if ( '1' == $blog[ 'public' ] ) {
				_e('Visible(1)');
			}
			if ( '0' == $blog[ 'public' ] ) {
				_e('No Search(0)');
			}
			if ( '-1' == $blog[ 'public' ] ) {
				_e('Users Only(-1)');
			}
			if ( '-2' == $blog[ 'public' ] ) {
				_e('Members Only(-2)');
			}
			if ( '-3' == $blog[ 'public' ] ) {
				_e('Admins Only(-3)');
			}
			echo '<br class="clear" />';
	}

	//------------------------------------------------------------------------//
	//---Functions hooked into blog privacy selector(options-privacy.php)-----//
	//------------------------------------------------------------------------//

 function add_privacy_options($options) { 
		global $blogname,$current_site; 
		$blog_name = get_bloginfo('name', 'display');
?>
<br/>
			<input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked('-1', get_option('blog_public')); ?> />
			<label for="blog-private-1"><?php _e('I would like my blog to be visible only to registered users of '); ?><?php echo esc_attr( $current_site->site_name ) ?></label>
<br/>
			<input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', get_option('blog_public')); ?> />
			<label for="blog-private-2"><?php _e('I would like my blog to be visible only to <a href="users.php">registered users I add</a> to '); ?>"<?php echo $blog_name; ?>"</label>
<br/>
			<input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', get_option('blog_public')); ?> />
			<label for="blog-private-3">I would like "<?php echo $blog_name; ?>" to be visible only to Admins.</label>
	<?php 
	}

	//------------------------------------------------------------------------//
	//---Functions for Registered Community Users Only Blog-------------------//
	//------------------------------------------------------------------------//
	function ds_feed_login() {
    	 global $current_blog, $blog_id;
    	       	$credentials = array();
        	   	$credentials['user_login'] = $_SERVER['PHP_AUTH_USER'];
        	   	$credentials['user_password'] = $_SERVER['PHP_AUTH_PW'];

       		    $user = wp_signon( $credentials ); //if this creates WP_User, the next 3 lines are redundant
       		    
				$user_id = get_user_id_from_string( $user->user_login );


	   	       if ( is_wp_error( $user ) ||
	   	       // "Members Only"
	   	        ( ( '-2' == $current_blog->public ) && ( !is_user_member_of_blog( $user_id, $blog_id ) ) && !is_super_admin( $user_id ) ) ||
	   	       // TODO "Admins Only" - members still see feeds need a new ms-function is_site_admin( $user_id, $blog_id )
   	        ( ( '-3' == $current_blog->public )
   	        //&&  !is_site_admin( $user_id, $blog_id ) //this function doesn't exist
  			&& ( !is_user_member_of_blog( $user_id, $blog_id ) )
   	        && !is_super_admin( $user_id ) )
	   	        )
    	       {
                header( 'WWW-Authenticate: Basic realm="' . $_SERVER['SERVER_NAME'] . '"' );
                header( 'HTTP/1.0 401 Unauthorized' );
                die();
    	       }   	       
	       }
	
	function ds_users_authenticator () {
			if ( !is_user_logged_in() ) {
	      		if( is_feed() ) {
    	       	$this->ds_feed_login();
	       } else {
			nocache_headers();
			header("HTTP/1.1 302 Moved Temporarily");
			header('Location: ' . wp_login_url());
        	header("Status: 302 Moved Temporarily");
			exit();
			}
		}
	}
	function registered_users_login_message () {
		global $current_site;
		echo '<p>';
		echo '' . bloginfo(name) . ' can be viewed by <a href="' . apply_filters( 'wp_signup_location', network_home_url( 'wp-signup.php' ) ) . '">Registered Network Users of ' . $current_site->site_name .'</a>.';
		echo '</p><br/>';
	}
	function registered_users_header_title () {
		global $current_site;
		echo 'Visible Only to Registered Users of '. esc_attr( $current_site->site_name );
	}
	function registered_users_header_link () {
		global $current_site;
		echo 'Visible Only to Registered Network Users';
	}

	//------------------------------------------------------------------------//
	//---Shortcut Function for logged in users to add timed "refresh"--------//
	//------------------------------------------------------------------------//
	function ds_login_header() {
	global $error, $is_iphone, $interim_login, $current_site;
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<title><?php _e("Private Blog Message"); ?></title>
				<meta http-equiv="refresh" content="8;URL=<?php echo wp_login_url(); ?>" />
				<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<?php
		wp_admin_css( 'login', true );
		wp_admin_css( 'colors-fresh', true );

	if ( $is_iphone ) { ?>
	<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
	<style type="text/css" media="screen">
	form { margin-left: 0px; }
	#login { margin-top: 20px; }
	</style>
<?php
	} elseif ( isset($interim_login) && $interim_login ) { ?>
	<style type="text/css" media="all">
	.login #login { margin: 20px auto; }
	</style>
<?php
	}

	do_action('login_head'); ?>
</head>
			<body class="login">
				<div id="login">
					<h1><a href="<?php echo apply_filters('login_headerurl', 'http://' . $current_site->domain . $current_site->path ); ?>" title="<?php echo apply_filters('login_headertitle', $current_site->site_name ); ?>"><span class="hide"><?php bloginfo('name'); ?></span></a></h1>
	<?php
	}

	//------------------------------------------------------------------------//
	//---Functions for Members Only Blog---------------------------------------//
	//------------------------------------------------------------------------//
	function ds_members_authenticator() {
		global $current_user, $blog_id;
		if( is_user_member_of_blog( $current_user->id, $blog_id ) || is_super_admin() ) {
			 return;
		} else {
				if ( is_user_logged_in() ) {	      	
					$this->ds_login_header(); ?>
					<form name="loginform" id="loginform" />
						<p>Wait 8 seconds or 
							<a href="<?php echo wp_login_url(); ?>">click</a> to continue.</p>
							<?php $this->registered_members_login_message (); ?>
					</form>
				</div>
			</body>
		</html>
		<?php 
			exit();
		} else {
					if( is_feed()) {
    	       	$this->ds_feed_login();
    	       	
		   	    } else {
			nocache_headers();
			header("HTTP/1.1 302 Moved Temporarily");
			header('Location: ' . wp_login_url());
        	header("Status: 302 Moved Temporarily");
			exit();
				}
			}
		}
	}
	function registered_members_login_message () {
		global $current_site;
		echo '<p>';
		if(!is_user_logged_in()) {
			echo '' . get_bloginfo('name') . __(' can be viewed by members of this blog only.');
			echo '<br /><a href="' . apply_filters( 'wp_signup_location', network_home_url( 'wp-signup.php' ) ) . '">Register first as a Network User of ' . $current_site->site_name .'</a>.';
		}
		if(is_user_logged_in()) {
		echo 'To become a member of the ' . get_bloginfo('name') . ' blog, contact <a href="mailto:' . str_replace( '@', ' AT ', get_option('admin_email')) . '?subject=' . get_bloginfo('name') . ' Blog Membership at ' . $current_site->site_name .'">' . str_replace( '@', ' AT ', get_option('admin_email')) . '</a>';

		}
		echo '</p><br/>';
	}
	function registered_members_header_title () {
		echo __(' Visible only to users added to this blog');
	}
	function registered_members_header_link () {
		echo __(' Visible only to users added to this blog');
	}

	//-----------------------------------------------------------------------//
	//---Functions for Admins Only Blog--------------------------------------//
	//---WARNING: member users, if they exist, still see the backend---------//
	function ds_admins_authenticator () {
		if( current_user_can( 'manage_options' ) || is_super_admin() ) {
			 return;
		} else {
		
		if (( is_user_logged_in() )) {
			$this->ds_login_header(); ?>
						<form name="loginform" id="loginform" />
							<p>Wait 8 seconds or 
								<a href="<?php echo wp_login_url(); ?>">click</a> to continue.</p>
								<?php $this->registered_admins_login_message (); ?>
						</form>
					</div>
				</body>
			</html>
			<?php 
			exit();
		} else {
					if( is_feed()) {
    	       	$this->ds_feed_login();
		   	    } else {
			nocache_headers();
			header("HTTP/1.1 302 Moved Temporarily");
			header('Location: ' . wp_login_url());
        	header("Status: 302 Moved Temporarily");
			exit();
				}
			}
		}
	}
	function registered_admins_login_message () {
		echo '<p>';
		echo '' . bloginfo(name) . __(' can be viewed by administrators only.');
		echo '</p><br/>';
	}	
	function registered_admins_header_title () {
		echo __(' Visible Only to Admins - most privacy');
	}
	function registered_admins_header_link () {
		echo __(' Visible Only to Admins');
	}

//-----------------------------------------------------------------------//
//---Functions for SiteAdmins Options--------------------------------------//
//---WARNING: member users, if they exist, still see the backend---------//
	function sitewide_privacy_options_page() {
		$number = intval(get_site_option('ds_sitewide_privacy'));
		if ( !isset($number) ) {
			$number = '1';
		}
		echo '<h3>Network Privacy Selector</h3>';
		echo '
		<table class="form-table">
		<tr valign="top"> 
			<th scope="row">' . __('Blog Privacy') . '</th>';
			$checked = ( $number == "-1" ) ? " checked=''" : "";
		echo '<td><input type="radio" name="ds_sitewide_privacy" id="ds_sitewide_privacy" value="-1" ' . $checked . '/>
			<br />
			<small>
			' . __('Blog network can be viewed by registered users of this community only.') . '
			</small></td>';
			$checked = ( $number == "1" ) ? " checked=''" : "";
		echo '<td><input type="radio" name="ds_sitewide_privacy" id="ds_sitewide_privacy_1" value="1" ' . $checked . '/>
			<br />
			<small>
			' . __('Default: privacy managed per blog.') . '
			</small></td>
		</tr>
		</table>'; 
	}
	function sitewide_privacy_update() {
		update_site_option('ds_sitewide_privacy', $_POST['ds_sitewide_privacy']);
	}
}

if (class_exists("ds_more_privacy_options")) {
	$ds_more_privacy_options = new ds_more_privacy_options();	
	}

if (isset($ds_more_privacy_options)) {
//------------------------------------------------------------------------//
//---Hooks-----------------------------------------------------------------//
//------------------------------------------------------------------------//
// SuperAdmin->Options
add_action( 'update_wpmu_options', array(&$ds_more_privacy_options, 'sitewide_privacy_update'));
add_action( 'wpmu_options', array(&$ds_more_privacy_options, 'sitewide_privacy_options_page'));

// hooks into Misc Blog Actions in SuperAdmin->Sites->Edit
add_action('wpmueditblogaction', array(&$ds_more_privacy_options, 'wpmu_blogs_add_privacy_options'),999);
// hooks into Blog Columns views SiteAdmin->Blogs
// add_action('wpmublogsaction', array(&$ds_more_privacy_options, 'wpmu_blogs_add_privacy_options_messages') );

// hook into options-privacy.php Dashboard->Settings->Privacy.
add_action('blog_privacy_selector', array(&$ds_more_privacy_options, 'add_privacy_options'));
// hook into signup form
// add_action('signup_blogform', array(&$ds_more_privacy_options, 'add_privacy_options'));

// all three add_privacy_option get a redirect and a message in the Login form
		$number = intval(get_site_option('ds_sitewide_privacy'));

if (( '-1' == $current_blog->public ) || ($number == '-1')) { // add exclusion of main blog if desired
	add_action('template_redirect', array(&$ds_more_privacy_options, 'ds_users_authenticator'));
	add_action('login_form', array(&$ds_more_privacy_options, 'registered_users_login_message')); 
	add_filter('privacy_on_link_title', array(&$ds_more_privacy_options, 'registered_users_header_title'));
	add_filter('privacy_on_link_text', array(&$ds_more_privacy_options, 'registered_users_header_link') );
	}
if ( '-2' == $current_blog->public ) {
	add_action('template_redirect', array(&$ds_more_privacy_options, 'ds_members_authenticator'));
	add_action('login_form', array(&$ds_more_privacy_options, 'registered_members_login_message')); 
	add_filter('privacy_on_link_title', array(&$ds_more_privacy_options, 'registered_members_header_title'));
	add_filter('privacy_on_link_text', array(&$ds_more_privacy_options, 'registered_members_header_link') );

}
if ( '-3' == $current_blog->public ) {
	add_action('template_redirect', array(&$ds_more_privacy_options, 'ds_admins_authenticator'));
	add_action('login_form', array(&$ds_more_privacy_options, 'registered_admins_login_message'));
	add_filter('privacy_on_link_title', array(&$ds_more_privacy_options, 'registered_admins_header_title'));
	add_filter('privacy_on_link_text', array(&$ds_more_privacy_options, 'registered_admins_header_link') );
}
// fixes robots.txt rules 
add_action('do_robots', array(&$ds_more_privacy_options, 'do_robots'),1);

// fixes noindex meta as well
add_action('wp_head', array(&$ds_more_privacy_options, 'noindex'),0);
add_action('login_head', array(&$ds_more_privacy_options, 'noindex'),1);

//no pings unless public either
add_filter('option_ping_sites', array(&$ds_more_privacy_options, 'privacy_ping_filter'),1);
//email SuperAdmin when privacy changes
add_action( 'update_blog_public', array(&$ds_more_privacy_options,'ds_mail_super_admin'));

}

?>