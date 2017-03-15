<?php
/*
Plugin Name: Allow LaTeX Uploads
Description: Allows LaTeX and related file uploads.
Version: 0.3
Author: mitcho (Michael Yoshitaka Erlewine)
Author URI: http://mitcho.com
*/

add_filter('upload_mimes','add_tex_mime');
function add_tex_mime($mimes) {
  $mimes['latex'] = 'application/x-latex';
  $mimes['tex'] = 'application/x-tex';
  $mimes['dvi'] = 'application/x-dvi';
  $mimes['ps'] = 'application/postscript';
  return $mimes;
}

add_filter('wp_mime_type_icon', 'tex_mime_type_icon', 10, 3);
function tex_mime_type_icon($icon, $mime, $post_id) {
	if ( $mime == 'application/x-latex' || $mime == 'application/x-tex' )
		return wp_mime_type_icon('text');
	if ( $mime == 'application/x-dvi' || $mime == 'application/postscript' )
		return wp_mime_type_icon('document');
	return $icon;
}