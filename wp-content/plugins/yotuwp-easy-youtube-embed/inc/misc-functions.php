<?php

function yotuwp_doing_cron() {

	// Bail if not doing WordPress cron (>4.8.0)
	if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
		return true;

	// Bail if not doing WordPress cron (<4.8.0)
	} elseif ( defined( 'DOING_CRON' ) && ( true === DOING_CRON ) ) {
		return true;
	}

	// Default to false
	return false;
}

function yotuwp_video_title( $video ) {
	return apply_filters('yotuwp_video_title', $video->snippet->title, $video );
}

function yotuwp_video_description( $video ) {
	$desc = apply_filters( 'yotuwp_video_description', nl2br(strip_tags($video->snippet->description)), $video );
	return $desc;
}

function yotuwp_video_thumb( $video ) {
	$url = (isset( $video->snippet->thumbnails) && isset( $video->snippet->thumbnails->standard) )? $video->snippet->thumbnails->standard->url : $video->snippet->thumbnails->high->url;
	return apply_filters( 'yotuwp_video_thumbnail', $url, $video );
}