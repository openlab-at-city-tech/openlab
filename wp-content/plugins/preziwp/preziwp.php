<?php
/*
Plugin Name: Prezi WP
Plugin URI: http://teleogistic.net/code/wordpress/prezi-wp
Author: Boone Gorges
Author URI: http://boonebgorges.com
Description: Adds a Prezi shortcode to allow you to easily embed presentations into WordPress.
Version: 1.0.1
*/

class PreziWP {
	/**
	 * Constructor
	 *
	 * @package PreziWP
	 * @since 1.0
	 */
	function preziwp() {
		add_shortcode( 'prezi', array( $this, 'do_shortcode' ) );
	}
	
	/**
	 * Does the shortcode
	 *
	 * @package PreziWP
	 * @since 1.0
	 */
	function do_shortcode( $atts = false ) {
		// Do 'er to 'er
	
		// Parse the args. Default resolution is 550x400
		extract( shortcode_atts( array(
			"width"		=> 550,  
			"height" 	=> 400,
			"id"		=> false
		), $atts) );
		
		$id = $this->check_id( $id );
	
		// Check to make sure an ID was passed.
		if ( !$id )
			return __( 'You must provide a Prezi ID for the embedded presentation to work.', 'preziwp' );
		
		$html = '<div class="prezi-player"><style type="text/css" media="screen">.prezi-player { width: '. $width . 'px; } .prezi-player-links { text-align: center; }</style><object id="prezi_' . $id . '" name="prezi_' . $id . '" width="' . $id . '" height="' . $id . '"><param name="movie" value="http://prezi.com/bin/preziloader.swf"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="bgcolor" value="#ffffff"/><param name="flashvars" value="prezi_id=' . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"/><embed id="preziEmbed_' . $id . '" name="preziEmbed_' . $id . '" src="http://prezi.com/bin/preziloader.swf" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="' . $width . '" height="' . $height . '" bgcolor="#ffffff" flashvars="prezi_id=' . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"></embed></object><div class="prezi-player-links"><p><a title="' . __( 'View Original on Prezi', 'preziwp' ) . '" href="http://prezi.com/' . $id . '/">' . __( 'View Original</a> on <a href="http://prezi.com">Prezi</a>', 'preziwp' ) . '</p></div></div>';
		
		return $html;
	}
	
	/**
	 * Checks the user submitted id
	 *
	 * This function will attempt to parse the Prezi ID from a complete Prezi URL
	 *
	 * @package PreziWP
	 * @since 1.0
	 *
	 * @param string $id The 'id' attribute from the shortcode. Prezi ID or URL
	 * @return string $id The unique Prezi ID
	 */
	function check_id( $id ) {
		// Cross your fingers
		
		if ( empty( $id ) )
			return false;
		
		if ( strpos( $id, 'prezi.com' ) ) {
			// Get everything after the prezi.com bit
			$split	 	= explode( '.com/', $id );
			
			// Break up the remaining goods by slashes
			$split	 	= explode( '/', $split[1] );

			// Take everything before the first slash
			$id		= $split[0];
		}
		
		return $id;
	}
}

$preziwp = new PreziWP;

?>
