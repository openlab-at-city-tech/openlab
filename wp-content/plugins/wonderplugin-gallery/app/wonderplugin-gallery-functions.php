<?php

if ( ! defined( 'ABSPATH' ) )
	exit;
	
/**
 * wp_trim_words preserves HTML tags
 */
function wonderplugin_gallery_wp_trim_words( $text, $num_words = 25, $more = null ) {
	if ( null === $more ) {
		$more = __( '&hellip;' );
	}

	$original_text = $text;

	if ( strpos( _x( 'words', 'Word count type. Do not translate!' ), 'characters' ) === 0 && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';
	} else {
		$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
		$sep = ' ';
	}

	if ( count( $words_array ) > $num_words ) {
		array_pop( $words_array );
		$text = implode( $sep, $words_array );
		$text = $text . $more;
	} else {
		$text = implode( $sep, $words_array );
	}

	return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
}

function wonderplugin_gallery_wp_check_filetype_and_ext($data, $file, $filename, $mimes) {

	$filetype = wp_check_filetype( $filename, $mimes );

	return array(
			'ext'             => $filetype['ext'],
			'type'            => $filetype['type'],
			'proper_filename' => $data['proper_filename']
	);
}

function wonderplugin_dirtoarray($dir, $recursive) {

	if (!is_readable($dir) || !file_exists($dir))
		return -1;
	
	$result = array();

	$cdir = scandir($dir);
	foreach ($cdir as $key => $value)
	{
		if (!in_array($value,array(".","..")))
		{
			if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
			{
				if ($recursive)
				{
					$result[$value] = wonderplugin_dirtoarray($dir . DIRECTORY_SEPARATOR . $value, $recursive);
				}
				else
				{
					$result[] = $value;
				}
			}
		}
	}
	 
	return $result;
}

function wonderplugin_gallery_tags_allow( $allowedposttags ) {

	if ( empty($allowedposttags['style']) )
		$allowedposttags['style'] = array();

	$allowedposttags['style']['type'] = true;
	$allowedposttags['style']['id'] = true;

	if ( empty($allowedposttags['input']) )
		$allowedposttags['input'] = array();

	$allowedposttags['input']['type'] = true;
	$allowedposttags['input']['class'] = true;
	$allowedposttags['input']['id'] = true;
	$allowedposttags['input']['name'] = true;
	$allowedposttags['input']['value'] = true;
	$allowedposttags['input']['size'] = true;
	$allowedposttags['input']['checked'] = true;
	$allowedposttags['input']['placeholder'] = true;

	if ( empty($allowedposttags['textarea']) )
		$allowedposttags['textarea'] = array();

	$allowedposttags['textarea']['type'] = true;
	$allowedposttags['textarea']['class'] = true;
	$allowedposttags['textarea']['id'] = true;
	$allowedposttags['textarea']['name'] = true;
	$allowedposttags['textarea']['value'] = true;
	$allowedposttags['textarea']['rows'] = true;
	$allowedposttags['textarea']['cols'] = true;
	$allowedposttags['textarea']['placeholder'] = true;

	if ( empty($allowedposttags['select']) )
		$allowedposttags['select'] = array();

	$allowedposttags['select']['type'] = true;
	$allowedposttags['select']['class'] = true;
	$allowedposttags['select']['id'] = true;
	$allowedposttags['select']['name'] = true;
	$allowedposttags['select']['size'] = true;

	if ( empty($allowedposttags['option']) )
		$allowedposttags['option'] = array();

	$allowedposttags['option']['value'] = true;

	if ( empty($allowedposttags['a']) )
		$allowedposttags['a'] = array();

	$allowedposttags['a']['onclick'] = true;
	$allowedposttags['a']['download'] = true;
	$allowedposttags['a']['data'] = true;

	if ( empty($allowedposttags['source']) )
		$allowedposttags['source'] = array();

	$allowedposttags['source']['src'] = true;
	$allowedposttags['source']['type'] = true;

	if ( empty($allowedposttags['iframe']) )
		$allowedposttags['iframe'] = array();

	$allowedposttags['iframe']['width'] = true;
	$allowedposttags['iframe']['height'] = true;
	$allowedposttags['iframe']['scrolling'] = true;
	$allowedposttags['iframe']['frameborder'] = true;
	$allowedposttags['iframe']['allow'] = true;
	$allowedposttags['iframe']['src'] = true;

	$allowedposttags = apply_filters( 'wonderplugin_gallery_custom_tags_allow', $allowedposttags );

	return $allowedposttags;
}

function wonderplugin_gallery_css_allow($allowed_attr) {

	if ( !is_array($allowed_attr) ) {
		$allowed_attr = array();
	}

	array_push($allowed_attr, 'display', 'position', 'top', 'left', 'bottom', 'right');

	$allowed_attr = apply_filters( 'wonderplugin_gallery_custom_css_allow', $allowed_attr );

	return $allowed_attr;
}