<?php

/**
 * @author Deanna Schneider & Jason Lemahieu
 * @copyright 2008
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class cets_EmbedGmaps_shortcodes {
		
	// register the new shortcodes
	function __construct() {
	
		add_shortcode( 'cetsEmbedGmap', array(&$this, 'show_Gmap') );
			
	}

	function show_Gmap( $atts ) {
	
		global $cets_EmbedGmaps;
	
		extract(shortcode_atts(array(
			'src' 		=> 'http://maps.google.com/?ie=UTF8&ll=37.0625,-95.677068&spn=55.586984,107.138672&t=h&z=4',
			'height' => 425,
			'width' => 350,
			'frameborder' => 0,
			'marginheight' => 0,
			'marginwidth' => 0,
			'scrolling' =>'no'
		), $atts ));
		
		// clean up the url
		$src = str_replace("'", "\\'", esc_url($src));
			
		//if it's not a link to maps.google.com, don't allow it
		if (substr_count($src, 'http://maps.google', 0) == 0 && substr_count($src, 'https://maps.google', 0) == 0) return;
		
		// makes sure all the other attributes are valid
		if (!is_numeric($height)) $height = 425;
		if (!is_numeric($width)) $width = 350;
		if (!is_numeric($frameborder)) $frameborder = 0;
		if (!is_numeric($marginheight)) $marginheight = 0;
		if (!is_numeric($marginwidth)) $marginwidth = 0;
		if ($scrolling != 'auto' && $scrolling != 'yes') $scrolling = 'no';
		
		// take the link and make the iframe embed stuff.
		$return = '<iframe width="' . $width . '" height="' . $height . '" frameborder="' . $frameborder . '" scrolling="' . $scrolling . '" marginheight="' . $marginheight . '" marginwidth="' . $marginwidth . '" src="' . $src . '&amp;output=embed"></iframe><br /><small><a href="' . $src . '&amp;source=embed" target="_new" style="color:#0000FF;text-align:left">View larger map</a> </small>';
		
		return $return;
		
	}	
}

// let's use it
$cets_EmbedGmapsShortcodes = new cets_EmbedGmaps_Shortcodes;
