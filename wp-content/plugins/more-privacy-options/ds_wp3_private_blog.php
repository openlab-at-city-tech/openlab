<?php
/*
Plugin Name: More Privacy Options
Plugin URI:	http://wordpress.org/extend/plugins/more-privacy-options/
Version: 3.9.1.1
Description: Add more privacy(visibility) options to a WordPress Multisite Network. Settings->Reading->Visibility:Network Users, Blog Members, or Admins Only. Network Settings->Network Visibility Selector: All Blogs Visible to Network Users Only or Visibility managed per blog as default.
Author: D. Sader
Author URI: http://dsader.snowotherway.org/
Network: true

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

*/
/*
Tips:
To allow everyone who is on-campus into the blog, while requiring those off-campus to log in. Modify function ds_users_authenticator().

Such as this:

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

To protect files/attachments/images uploaded to protected blogs(.htaccess rewrites needed)
Pluginspiration: http://plugins.svn.wordpress.org/private-files/trunk/privatefiles.php

?????????? Notes/Questions about allowing wp-activate.php on a private site ????????????????????

First, but using string matching is dumb and easily bypasses login page. Adding "?wp-activate.php" to any url

//if( strpos($_SERVER['REQUEST_URI'], 'wp-activate.php')) return;

Second, allow activate.php on the main page, but PHP_SELF url string matching may have the same drawbacks as REQUEST_URI. Could be done to main page with a plugin/function.

Also, the appearance of wp-activate on a subsite depends on which action is hooked: send_headers, or template_redirect. I prefer the template_redirect so activations can occur on subsites, too.

add_action('send_headers','ds_more_privacy_options_activate',10);
	function ds_more_privacy_options_activate() {
		global $ds_more_privacy_options;

		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && is_main_site()) {
			remove_action('send_headers', array($ds_more_privacy_options, 'ds_users_authenticator'),10);
			remove_action('send_headers', array($ds_more_privacy_options, 'ds_members_authenticator'),10);
			remove_action('send_headers', array($ds_more_privacy_options, 'ds_admins_authenticator'),10);
		}
	}

Third, you could redirect any url match to the main page wp-activate.php, but the redirection doesn't take the activation key with it - just not ideal to use any site but the main for activation IMHO.

// at any rate using url matching was dumb - adding wp-activate.php to any url bypassed the login auth - so a redirect to main site may help - provided the main site isn't also private.
//		if( strpos($_SERVER['REQUEST_URI'], 'wp-activate.php')  && !is_main_site()) { //DO NOT DO THIS!
	
So, better may be PHP_SELF since we can wait for script to execute before deciding to auth_redirect it.
	
//		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php')  && !is_main_site()) {
//			$destination = network_home_url('wp-activate.php');
//			wp_redirect( $destination );
//		exit();
//		}

Finally, changing the hook to fire at send_headers rather than template_redirect allows the activation page on every site. Still shows template pages with headers/sidebars etc - so not ideal either. Hence my preference to redirect to the main site.

//			//	add_action('template_redirect', array(&$this, 'ds_users_authenticator'));
				add_action('send_headers', array(&$this, 'ds_users_authenticator'));

So, I have the private functions the way I actually use them on my private sites/networks. I also do many activations as the SiteAdmin manually using other plugins.
Therefore, the code in this revision may make blogs more private, but somewhat more inconvenient to activate, both features I desire.

We'll see how the feedback trickles in on this issue. 

Oh, and is it even necessary to show a robots.txt to spiders if the blog is private anyway?

*/

class ds_more_privacy_options {
		var $l10n_prefix;

	function ds_more_privacy_options() {
		global  $current_blog;

		$this->l10n_prefix = 'more-privacy-options';

		//------------------------------------------------------------------------//
		//---Hooks-----------------------------------------------------------------//
		//------------------------------------------------------------------------//
				add_action( 'init', array(&$this, 'ds_localization_init' ));
			// Network->Settings
				add_action( 'update_wpmu_options', array(&$this, 'sitewide_privacy_update'));
				add_action( 'wpmu_options', array(&$this, 'sitewide_privacy_options_page'));

			// hooks into Misc Blog Actions in Network->Sites->Edit
				add_action('wpmueditblogaction', array(&$this, 'wpmu_blogs_add_privacy_options'),-999);
			// hooks into Blog Columns views Network->Sites
				//add_filter( 'manage_sites-network_columns', array( &$this, 'add_sites_column' ), 10, 1);
				//add_action( 'manage_sites_custom_column', array( &$this, 'manage_sites_custom_column' ), 10, 3);

			// hook into options-reading.php Dashboard->Settings->Reading.
				add_action('blog_privacy_selector', array(&$this, 'add_privacy_options'));

			// all three add_privacy_option get a redirect and a message in the Login form
		$number = intval(get_site_option('ds_sitewide_privacy'));

		if (( '-1' == $current_blog->public ) || ($number == '-1')) {
			
			//wp_is_mobile() ? is send_headers or template_redirect better for mobiles?
				add_action('template_redirect', array(&$this, 'ds_users_authenticator'));
			//	add_action('send_headers', array(&$this, 'ds_users_authenticator'));
				add_action('login_form', array(&$this, 'registered_users_login_message')); 
				add_filter('privacy_on_link_title', array(&$this, 'registered_users_header_title'));
				add_filter('privacy_on_link_text', array(&$this, 'registered_users_header_link') );
		}
		if ( '-2' == $current_blog->public ) {
				add_action('template_redirect', array(&$this, 'ds_members_authenticator'));
			//	add_action('send_headers', array(&$this, 'ds_members_authenticator'));
				add_action('login_form', array(&$this, 'registered_members_login_message')); 
				add_filter('privacy_on_link_title', array(&$this, 'registered_members_header_title'));
				add_filter('privacy_on_link_text', array(&$this, 'registered_members_header_link') );

		}
		if ( '-3' == $current_blog->public ) {
				add_action('template_redirect', array(&$this, 'ds_admins_authenticator'));
			//	add_action('send_headers', array(&$this, 'ds_admins_authenticator'));
				add_action('login_form', array(&$this, 'registered_admins_login_message'));
				add_filter('privacy_on_link_title', array(&$this, 'registered_admins_header_title'));
				add_filter('privacy_on_link_text', array(&$this, 'registered_admins_header_link') );
		}

			// fixes robots.txt rules 
				add_action('do_robots', array(&$this, 'do_robots'),1);

			// fixes noindex meta as well
				add_action('wp_head', array(&$this, 'noindex'),0);
				add_action('login_head', array(&$this, 'noindex'),1);

			//no pings unless public either
				add_filter('option_ping_sites', array(&$this, 'privacy_ping_filter'),1);
			//email SuperAdmin when privacy changes
				add_action( 'update_blog_public', array(&$this,'ds_mail_super_admin'));
			// hook into signup form?
				 add_action('signup_blogform', array(&$this, 'add_privacy_options'));

	}
	
	function ds_localization_init() {
				load_plugin_textdomain( $this->l10n_prefix, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
	
	function ds_mail_super_admin() {
		global $wpdb, $blogname, $current_blog;
			$blog_id = $wpdb->blogid;
			$blog_public_old = $current_blog->public;
			$blog_public_new = get_blog_option($blog_id,'blog_public');
			
			$from_old = $this->ds_mail_super_admin_messages($blog_public_old);
						
			$to_new = $this->ds_mail_super_admin_messages($blog_public_new);			

			$email =  stripslashes( get_site_option('admin_email') );
			$subject = __('Site ', $this->l10n_prefix).$blogname.'('.$blog_id.'), http://'.$current_blog->domain.$current_blog->path . ', '. __('changed reading visibility setting from ', $this->l10n_prefix) . $from_old . __(' to ', $this->l10n_prefix) . $to_new;
			$message = __('Site ', $this->l10n_prefix).$blogname.'('.$blog_id.'), http://'.$current_blog->domain.$current_blog->path . ', '.__('changed reading visibility setting from ', $this->l10n_prefix) .$from_old. __(' to ', $this->l10n_prefix) .$to_new;
			$headers = 'Auto-Submitted: auto-generated';
 		wp_mail($email, $subject, $message, $headers);
	}

	function ds_mail_super_admin_messages($blog_public) {
			if ( '1' == $blog_public ) {
				return __('Visible(1)', $this->l10n_prefix);
			}
			if ( '0' == $blog_public ) {
				return __('No Search(0)', $this->l10n_prefix);
			}
			if ( '-1' == $blog_public ) {
				return __('Network Users Only(-1)', $this->l10n_prefix);
			}
			if ( '-2' == $blog_public ) {
				return __('Site Members Only(-2)', $this->l10n_prefix);
			}
			if ( '-3' == $blog_public ) {
				return __('Site Admins Only(-3)', $this->l10n_prefix);
			}
	}	

	function do_robots2() {
		remove_action('do_robots', 'do_robots');

		header( 'Content-Type: text/plain; charset=utf-8' );

		do_action( 'do_robotstxt' );

		$output = "User-agent: *\n";
		$public = get_option( 'blog_public' );
		if ( '1' != $public ) {
			$output .= "Disallow: /\n";
		} else {
			$site_url = parse_url( site_url() );
			$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			$output .= "Disallow: $path/wp-admin/\n";
			$output .= "Disallow: $path/wp-includes/\n";
	}

	echo apply_filters('robots_txt', $output, $public);
	}
	
	
	function do_robots() {
		remove_action( 'do_robots', 'do_robots' );

		header( 'Content-Type: text/plain; charset=utf-8' );

		do_action( 'do_robotstxt' );

		$output = "User-agent: *\n";
		$public = get_option( 'blog_public' );
		if ( '1' != $public ) {
			$output .= "Disallow: /\n";
		} else {
			if ( WP_CONTENT_DIR ) {
     			$dirs = pathinfo( WP_CONTENT_DIR );
     			$dir = $dirs['basename'];
			} else {
     			$dir = 'wp-content';
			}
			$output .= "Disallow:\n";
			$output .= "Disallow: /wp-admin\n";
			$output .= "Disallow: /wp-includes\n";
			$output .= "Disallow: /wp-login.php\n";
			$output .= "Disallow: /$dir/plugins\n";
			$output .= "Disallow: /$dir/cache\n";
			$output .= "Disallow: /$dir/themes\n";
			$output .= "Disallow: /trackback\n";
			$output .= "Disallow: /comments\n";
		}

	      echo apply_filters('robots_txt', $output, $public);
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
	//---Functions hooked into site_settings.php---------------------------------//
	function wpmu_blogs_add_privacy_options() { 
		global $details,$options;
		?>
		<tr>
			<th><?php _e( 'More Privacy Options', $this->l10n_prefix); ?></th>
			<td>
				<input type='radio' name='option[blog_public]' value='1' <?php if( $details->public == '1' ) echo " checked"?>> <?php _e('Visible(1)', $this->l10n_prefix) ?>
				<br />
	    		<input type='radio' name='option[blog_public]' value='0' <?php if( $details->public == '0' ) echo " checked"?>> <?php _e('No Search(0)', $this->l10n_prefix) ?>    
				<br />
	    		<input type='radio' name='option[blog_public]' value='-1' <?php if( $details->public == '-1' ) echo " checked"?>> <?php _e('Network Users Only(-1)', $this->l10n_prefix) ?>
				<br />
	    		<input type='radio' name='option[blog_public]' value='-2' <?php if( $details->public == '-2' ) echo " checked"?>> <?php _e('Site Members Only(-2)', $this->l10n_prefix) ?>
				<br />
		   		<input type='radio' name='option[blog_public]' value='-3' <?php if( $details->public == '-3' ) echo " checked"?>> <?php _e('Site Admins Only(-3)', $this->l10n_prefix) ?>
			</td>
		</tr>
		<?php
	}

    function add_sites_column( $column_details ) {
        $column_details['blog_visibility'] = _x( '<nobr>Visibility</nobr>', 'column name' );
        return $column_details;
    }

    function manage_sites_custom_column( $column_name, $blog_id ) {
        if ( $column_name != 'blog_visibility' ) {
            return;
        }
		$details = get_blog_details($blog_id);

			if ( '1' == $details->public ) {
				_e('Visible(1)', $this->l10n_prefix);
			}
			if ( '0' == $details->public ) {
				_e('No Search(0)', $this->l10n_prefix);
			}
			if ( '-1' == $details->public ) {
				_e('Network Users Only(-1)', $this->l10n_prefix);
			}
			if ( '-2' == $details->public ) {
				_e('Site Members Only(-2)', $this->l10n_prefix);
			}
			if ( '-3' == $details->public ) {
				_e('Site Admins Only(-3)', $this->l10n_prefix);
			}
			echo '<br class="clear" />';
	}

	function wpmu_blogs_add_privacy_options_messages() {
		global $blog;
			if ( '1' == $blog[ 'public' ] ) {
				_e('Visible(1)', $this->l10n_prefix);
			}
			if ( '0' == $blog[ 'public' ] ) {
				_e('No Search(0)', $this->l10n_prefix);
			}
			if ( '-1' == $blog[ 'public' ] ) {
				_e('Network Users Only(-1)', $this->l10n_prefix);
			}
			if ( '-2' == $blog[ 'public' ] ) {
				_e('Site Members Only(-2)', $this->l10n_prefix);
			}
			if ( '-3' == $blog[ 'public' ] ) {
				_e('Site Admins Only(-3)', $this->l10n_prefix);
			}
			echo '<br class="clear" />';
	}

	//------------------------------------------------------------------------//
	//---Functions hooked into blog visibility selector(options-reading.php)-----//
	//------------------------------------------------------------------------//
	function add_privacy_options($options) { 
		global $blogname,$current_site; 
		$blog_name = get_bloginfo('name', 'display');
		?>
			<label class="checkbox" for="blog-private-1">
				<input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked('-1', get_option('blog_public')); ?> /><?php _e('Visible only to registered users of this network', $this->l10n_prefix); ?>
			</label>
			<br/>
			<label class="checkbox" for="blog-private-2">
				<input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', get_option('blog_public')); ?> /><?php _e('Visible only to registered users of this site', $this->l10n_prefix); ?>
			</label>
			<br/>
			<label class="checkbox" for="blog-private-3">
				<input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', get_option('blog_public')); ?> /><?php _e('Visible only to administrators of this site', $this->l10n_prefix); ?>
			</label>
			<?php 
	}

	//------------------------------------------------------------------------//
	//---Functions for Registered Community Users Only Blog-------------------//
	//------------------------------------------------------------------------//
	function ds_feed_login() {
		//December 2012 tested with "Free RSS" for iPhone. Google Reader does not authenticate locked feeds. Tough to find a free reader that does authenticate.
		global $current_blog, $blog_id;
			$credentials = array();
        	$credentials['user_login'] = $_SERVER['PHP_AUTH_USER'];
        	$credentials['user_password'] = $_SERVER['PHP_AUTH_PW'];
			$credentials['remember'] = true;
			$user = wp_signon( $credentials, false ); //if this creates WP_User, the next 3 lines are redundant
			$user_id = get_user_id_from_string( $user->user_login );

			if ( is_wp_error( $user ) ||
				// "Members Only"
				( ( '-2' == $current_blog->public ) && ( !is_user_member_of_blog( $user_id, $blog_id ) ) && !is_super_admin( $user_id ) ) ||
				// TODO "Admins Only" - members still see feeds need a new ms-function is_super_admin( $user_id, $blog_id )
				( ( '-3' == $current_blog->public )
				//&&  !is_super_admin( $user_id, $blog_id ) //this function doesn't exist
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
		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && is_main_site()) return;		
		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && !is_main_site()) {
			$destination = network_home_url('wp-activate.php');
			wp_redirect( $destination );
		exit();
		}


		if ( !is_user_logged_in() ) {
			if( is_feed() ) {
				$this->ds_feed_login();
			} else {
				auth_redirect();
			}
		}
	}
	
	function registered_users_login_message () {
		global $current_site;
		echo '<p>';
		echo __('Visible only to registered users of this network', $this->l10n_prefix);
		echo '</p><br/>';
	}
	
	function registered_users_header_title () {
		return __('Visible only to registered users of this network', $this->l10n_prefix);
	}
	
	function registered_users_header_link () {
		return __('Visible only to registered users of this network', $this->l10n_prefix);
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
			<title><?php _e('Site Visibility', $this->l10n_prefix); ?></title>
			<!--<meta http-equiv="refresh" content="8;URL=<?php echo wp_login_url(); ?>" /> -->
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
		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && is_main_site()) return;		
		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && !is_main_site()) {
			$destination = network_home_url('wp-activate.php');
			wp_redirect( $destination );
		exit();
		}

		if( is_user_member_of_blog( $current_user->ID, $blog_id ) || is_super_admin() ) {
			 return;
		} else {
				if ( is_user_logged_in() ) {	      	
					$this->ds_login_header(); ?>
					<form name="loginform" id="loginform" />
						<p><a href="<?php if (!is_user_logged_in()) { echo wp_login_url(); } else { echo network_home_url(); } ?>"><?php echo __('Click', $this->l10n_prefix).'</a>'. __(' to continue', $this->l10n_prefix); ?>.</p>
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
		auth_redirect();
				}
			}
		}
	}
	
	function registered_members_login_message() {
		global $current_site;
		echo '<p>';
		if(!is_user_logged_in()) {
			echo __('Visible only to registered users of this site', $this->l10n_prefix);
		}
		if(is_user_logged_in()) {
		echo __('To become a member of this site, contact', $this->l10n_prefix).' <a href="mailto:' . str_replace( '@', ' AT ', get_option('admin_email')) . '?subject=' . get_bloginfo('name') . __(' Site Membership at ', $this->l10n_prefix) . $current_site->site_name .'">' . str_replace( '@', ' AT ', get_option('admin_email')) . '</a>';

		}
		echo '</p><br/>';
	}
	
	function registered_members_header_title() {
		return __('Visible only to registered users of this site', $this->l10n_prefix);
	}
	
	function registered_members_header_link() {
		return __ ('Visible only to registered users of this site', $this->l10n_prefix);
	}

	//-----------------------------------------------------------------------//
	//---Functions for Admins Only Blog--------------------------------------//
	//---WARNING: member users, if they exist, still see the backend---------//
	function ds_admins_authenticator() {
		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && is_main_site()) return;
		if( strpos($_SERVER['PHP_SELF'], 'wp-activate.php') && !is_main_site()) {
			$destination = network_home_url('wp-activate.php');
			wp_redirect( $destination );
		exit();
		}

		if( current_user_can( 'manage_options' ) || is_super_admin() ) {
			 return;
		} else {
		
		if (( is_user_logged_in() )) {
			$this->ds_login_header(); ?>
						<form name="loginform" id="loginform" />
							<?php $this->registered_admins_login_message (); ?>
							<p><?php echo __('Visit', $this->l10n_prefix); ?> <a href="<?php echo network_home_url(); ?>"><?php echo network_home_url(); ?></a> <?php __('to continue', $this->l10n_prefix); ?>.</p>
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
		auth_redirect();
				}
			}
		}
	}
	
	function registered_admins_login_message() {
		echo '<p>';
		echo __('Visible only to administrators of this site', $this->l10n_prefix);
		echo '</p><br/>';
	}	
	
	function registered_admins_header_title() {
		return __('Visible only to administrators of this site', $this->l10n_prefix);
	}
	
	function registered_admins_header_link() {
		return __('Visible only to administrators of this site', $this->l10n_prefix);
	}

//-----------------------------------------------------------------------//
//---Functions for SiteAdmins Options--------------------------------------//
//---WARNING: member users, if they exist, still see the backend---------//
	function sitewide_privacy_options_page() {
		$number = intval(get_site_option('ds_sitewide_privacy'));
		if ( !isset($number) ) {
			$number = '1';
		}
		echo '<h3>'. __('Network Visibility Selector', $this->l10n_prefix).'</h3>';
		echo '
		<table class="form-table">
		<tr valign="top"> 
			<th scope="row">' . __('Site Visibility', $this->l10n_prefix) . '</th><td>';

			$checked = ( $number == "-1" ) ? " checked=''" : "";
		echo '<label><input type="radio" name="ds_sitewide_privacy" id="ds_sitewide_privacy" value="-1" ' . $checked . '/>
			' . __ ('Visible only to registered users of this network', $this->l10n_prefix) . '
			</label><br />';

			$checked = ( $number == "1" ) ? " checked=''" : "";
		echo '<label><input type="radio" name="ds_sitewide_privacy" id="ds_sitewide_privacy_1" value="1" ' . $checked . '/>
			' . __('Default: visibility managed per site.', $this->l10n_prefix) . '
			</label><br />';

		echo '</td>			
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
?>