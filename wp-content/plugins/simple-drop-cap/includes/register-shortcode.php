<?php

// action for registering shortcode
add_action( 'init', 'wpsdc_register_shortcode' );

// function for registering shortcode
function wpsdc_register_shortcode()
{
	// register shortcode [dropcap]word[/dropcap]
	add_shortcode( 'dropcap', 'wpsdc_shortcode' );
}

// main function for wpsdc shortcode
function wpsdc_shortcode( $args, $content )
{
	// left trim $content
	$wpsdc_shortcoded_content = ltrim ( $content );

	// select first letter of shortcoded $content
	$wpsdc_first_letter_of_shortcoded_content = mb_substr( $wpsdc_shortcoded_content, 0, 1 );

	// select remaining letters of shortcoded content
	$wpsdc_remaining_letters_of_shortcoded_content = mb_substr( $wpsdc_shortcoded_content, 1 );

	// add <span class="wpsdc"> to the first letter for shortcoded content
	$wpsdc_spanned_first_letter = '<span class="wpsdc-drop-cap">' . $wpsdc_first_letter_of_shortcoded_content . '</span>';

	// return the spanned first letter and remaining letters
	return $wpsdc_spanned_first_letter . $wpsdc_remaining_letters_of_shortcoded_content;
}

// add filter to include shortcode to every post, page, and custom page
$wpsdc_options = get_option( 'wpsdc_options' );
if ( isset( $wpsdc_options['option_enable_all_posts'] ) && $wpsdc_options['option_enable_all_posts'] == '1' ) {
	add_filter( 'the_content', 'wpsdc_filter_content', 9 ); // add priority to 9 since 1.1.1 to make the_content drop capped first

	function wpsdc_filter_content( $content )
	{
		if ( preg_match( '#(^)(?:\s?)*(([A-Z]|[a-z]|[0-9]|[\p{L}])(.*\R)*(\R)*.*)#m', $content, $matches ) ) {

			$top_content = str_replace( $matches[2], '', $content );

			$bottom_content = ltrim( $matches[2] );

			$wpsdc_first_letter_of_filtered_content = mb_substr( $bottom_content, 0, 1 );

			if ( mb_substr( $bottom_content, 1, 1 ) === ' ' ) {
				$wpsdc_remaining_letters_of_filtered_content = ' ' . mb_substr( $bottom_content, 2 );
			} else {
				$wpsdc_remaining_letters_of_filtered_content = mb_substr( $bottom_content, 1);
			}

			$wpsdc_dropcapped_first_letter = '[dropcap]' . $wpsdc_first_letter_of_filtered_content . '[/dropcap]';
			
			$bottom_content = $wpsdc_dropcapped_first_letter . $wpsdc_remaining_letters_of_filtered_content;

			return $top_content . $bottom_content;			
		} 		
		return $content;		
	}
}

// do shortcode in a text widget
add_filter( 'widget_text', 'do_shortcode' );

// set priority to custom wp_trim_excerpt()
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'wpsdc_wp_trim_excerpt' );

// Copied from wp-includes/post-formatting.php
function wpsdc_wp_trim_excerpt($text = '') {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = str_replace( '[dropcap]', '', $text ); 
		$text = str_replace( '[/dropcap]', '', $text );
		$text = strip_shortcodes( $text );
		$text = apply_filters( 'the_content', $text );
		$text = str_replace(']]>', ']]&gt;', $text);		
		$excerpt_length = apply_filters( 'excerpt_length', 55 );		
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
}