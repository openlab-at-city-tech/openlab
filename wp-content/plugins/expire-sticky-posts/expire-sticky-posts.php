<?php
/**
 * Plugin Name: Expire Sticky Posts
 * Plugin URL: http://andyv.me
 * Description: A simple plugin that allows you to set an expiration date on posts. Once a post is expired, it will no longer be sticky.
 * Version: 1.0
 * Author: Andy von Dohren
 * Author URI: http://andyv.me
 * Contributors: avondohren, mordauk, rzen, pippinsplugins
 * Text Domain: pw-esp
 * Domain Path: languages
 *
 * Expire Sticky Posts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Expire Sticky Posts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Expire Sticky Posts. If not, see <http://www.gnu.org/licenses/>.
*/

define( 'PW_ESP_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets' ) ;

if( is_admin() ) {

	require_once dirname( __FILE__ ) . '/includes/metabox.php';

}

/**
 * Load our plugin's text domain to allow it to be translated
 *
 * @access  public
 * @since   1.0
*/
function pw_esp_text_domain() {

	// Load the default language files
	load_plugin_textdomain( 'pw-esp' );

}
add_action( 'init', 'pw_esp_text_domain' );

/**
 * Determines if a post is expired
 *
 * @access public
 * @since 1.0
 * @return bool
 */
function pw_esp_is_expired( $post_id = 0 ) {

	$expires = get_post_meta( $post_id, 'pw_esp_expiration', true );

	if( ! empty( $expires ) ) {

		// Get the current time and the post's expiration date
		$current_time = current_time( 'timestamp' );
		$expiration   = strtotime( $expires, current_time( 'timestamp' ) );

		// Determine if current time is greater than the expiration date
		if( $current_time >= $expiration ) {

			return true;

		}

	}

	return false;

}

/**
 * Unstick Posts
 *
 * @access public
 * @since 1.0
 * @return void
 */
function pw_esp_unstick( $title = '', $post_id = 0 ) {

	if( pw_esp_is_expired( $post_id ) ) {

		// Post is expired so unstick
		unstick_post ( $post_id );

	}
  
  return $title;

}
add_filter( 'the_title', 'pw_esp_unstick', 100, 2 );
