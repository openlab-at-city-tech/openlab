<?php
/**
Plugin Name: CAC Featured Content
Plugin URI: https://github.com/cuny-academic-commons/cac-featured-content
Version: 1.0.7
Author: Dominic Giglio, Boone Gorges
Description: Allows site authors to choose what content is to be featured on the home page.
Text Domain: cac-featured-content
*/

/**
Original Author: Michael McManus, Cast Iron Coding
Original Author URI: castironcoding.com

Widget skeleton by: RedRokk Interactive Media http://www.redrokk.com
Widget skeleton url: https://gist.github.com/1229641
*/

/**
 * Because of the way that WordPress loads plugins, it’s possible that our plugin
 * could load before or after BuddyPress. We only want to load our plugin AFTER
 * buddypress has been loaded.
 */
function cac_featured_content_init() {
  require( dirname( __FILE__ ) . '/cac-featured-main.php' );
}
add_action( 'bp_include', 'cac_featured_content_init' );

?>
