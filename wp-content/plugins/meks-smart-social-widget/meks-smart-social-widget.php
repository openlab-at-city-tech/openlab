<?php
/*
Plugin Name: Meks Smart Social Widget
Plugin URI: https://mekshq.com
Description: Easily display more than 100 social icons inside WordPress widget. Choose from different icon shapes and sizes and quickly connect your website with your social profiles. Aim, Apple, Behance, Blogger, Cargo,
Delicious, DeviantArt, Digg, Dribbble, Envato, Evernote, Facebook, Flickr, Forrst, Github, Google, GooglePlus, GrooveShark, Icloud, Instagram, LastFM, LinkedIN, MySpace, Picasa,
Pinterest, ReddIt, Rss, Skype, Spotify, StumbleUpon, Tumblr, Twitter, Vimeo, Vine, WordPress, Xing, Youtube, Zerply, 500px...
Author: Meks
Version: 1.6.3
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
define( 'MKS_SOCIAL_WIDGET_VER', '1.6.3' );

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