<?php
/*
Plugin Name: Meks Smart Social Widget
Plugin URI: https://mekshq.com
Description: Easily display more than 100 social icons inside WordPress widget. Choose from different icon shapes and sizes and quickly connect your website with your social profiles. Aim, Apple, Behance, Blogger, Cargo,
Delicious, DeviantArt, Digg, Dribbble, Envato, Evernote, Facebook, Flickr, Forrst, Github, Google, GooglePlus, GrooveShark, Icloud, Instagram, LastFM, LinkedIN, MySpace, Picasa,
Pinterest, ReddIt, Rss, Skype, Spotify, StumbleUpon, Tumblr, Twitter, Vimeo, Vine, WordPress, Xing, Youtube, Zerply, 500px...
Author: Meks
Version: 1.5
Author URI: http://mekshq.com
Text Domain: meks-smart-social-widget
Domain Path: /languages
*/


/*  Copyright 2013  Meks  (email : support@mekshq.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'MKS_SOCIAL_WIDGET_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'MKS_SOCIAL_WIDGET_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'MKS_SOCIAL_WIDGET_VER', '1.5' );

/* Initialize Widget */
if ( !function_exists( 'mks_social_widget_init' ) ):
    function mks_social_widget_init() {
        require_once MKS_SOCIAL_WIDGET_DIR.'inc/class-social-widget.php';
        register_widget( 'MKS_Social_Widget' );
    }
endif;

add_action( 'widgets_init', 'mks_social_widget_init' );


/* Load text domain */
function mks_load_social_widget_text_domain() {
    load_plugin_textdomain( 'meks-smart-social-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'mks_load_social_widget_text_domain' );

/**
 * Upsell Meks themes with notice info
 *
 * @return void
 */
add_action( 'admin_notices', 'meks_admin_notice__info' );

if ( ! function_exists( 'meks_admin_notice__info' ) ) :

	function meks_admin_notice__info() {

		$meks_themes  = array( 'shamrock', 'awsm', 'safarica', 'seashell', 'sidewalk', 'throne', 'voice', 'herald', 'vlog', 'gridlove', 'pinhole', 'typology', 'trawell', 'opinion', 'johannes', 'megaphone', 'toucan', 'roogan' );
		$active_theme = get_option( 'template' );


		if ( ! in_array( $active_theme, $meks_themes ) ) {

			if ( get_option('has_transient') == 0 ) {
				set_transient( 'meks_admin_notice_time_'. get_current_user_id() , true, WEEK_IN_SECONDS );
				update_option('has_transient', 1);
				update_option('track_transient', 1);
			}
			
			if (  !get_option('meks_admin_notice_info') || ( get_option('track_transient') && !get_transient( 'meks_admin_notice_time_'. get_current_user_id() ) ) ) {

				$all_themes = wp_get_themes();

				?>
				<div class="meks-notice notice notice-info is-dismissible">
					<p>
                         <?php
							echo sprintf( __( 'You are currently using %1$s theme. Did you know that Meks plugins give you more features and flexibility with one of our <a href="%2$s">Meks themes</a>?',  'meks-smart-social-widget' ), $all_themes[ $active_theme ], 'https://1.envato.market/4DE2o' );
						?>
					</p>
				</div>
				<?php

			}
		} else {
			delete_option('meks_admin_notice_info');
			delete_option('has_transient');
			delete_option('track_transient');
		}
	}

endif;


/**
 * Colose/remove info notice with ajax
 *
 * @return void
 */
add_action( 'wp_ajax_meks_remove_notification', 'meks_remove_notification' );
add_action( 'wp_ajax_nopriv_meks_remove_notification', 'meks_remove_notification' );

if ( !function_exists( 'meks_remove_notification' ) ) :
	function meks_remove_notification() {
		add_option('meks_admin_notice_info', 1);
		if ( !get_transient( 'meks_admin_notice_time_'. get_current_user_id() ) ) {
			update_option('track_transient', 0);
		}
	}
endif;
