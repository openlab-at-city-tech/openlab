<?php
/**
 * Media RSS presenting the pictures in counter chronological order.
 *
 * @author Vincent Prat (http://www.vincentprat.info)
 *
 * @param mode The content we want to display (last_pictures|gallery|album).
 *             Defaults to last_pictures.
 *
 * Parameters for mode = last_pictures
 *
 *   @param page The current picture ID (defaults to 0)
 *   @param show The number of pictures to include in one field (default 10)
 *
 * Parameters for mode = gallery
 *
 *   @param gid The gallery ID to show (defaults to first gallery)
 *   @param prev_next Whether to link to previous and next galleries (true|false).
 *                    Default to false.
 *
 * Parameters for mode = album
 *
 *   @param aid The album ID to show
 */

use Imagely\NGG\Util\URL;

// Load required files and set some useful variables
require_once __DIR__ . '/../ngg-config.php';
require_once __DIR__ . '/../lib/media-rss.php';

// Check we have the required GET parameters
$mode = isset( $_GET['mode'] ) ? $_GET['mode'] : 'last_pictures';

// Act according to the required mode
$rss = '';

if ( $mode == 'last_pictures' ) {

	// Get additional parameters
	$page = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
	$show = isset( $_GET['show'] ) ? (int) $_GET['show'] : 10;

	$rss = nggMediaRss::get_last_pictures_mrss( $page, $show );

} elseif ( $mode == 'gallery' ) {

	// Get all galleries
	$galleries = \Imagely\NGG\DataMappers\Gallery::get_instance()->find_all();

	if ( count( $galleries ) == 0 ) {
		header( 'content-type:text/plain;charset=utf-8' );
		print esc_html( __( 'No galleries have been yet created.', 'nggallery' ) );
		exit;
	}

	// Get additional parameters
	$gid = isset( $_GET['gid'] ) ? (int) $_GET['gid'] : 0;

	// if no gid is present, take the first gallery
	if ( $gid == 0 ) {
		$first = current( $galleries );
		$gid   = $first->gid;
	}

	// account for the the odd logic used in selecting galleries here
	if ( $gid == 1 ) {
		$gid = 0;
	} elseif ( $gid > 1 ) {
		--$gid;
	}

	// Set the main gallery object
	$gallery = $galleries[ $gid ];

	if ( ! isset( $gallery ) || $gallery == null ) {
		header( 'content-type:text/plain;charset=utf-8' );
		print esc_html( sprintf( __( 'The gallery ID=%s does not exist.', 'nggallery' ), intval( $gid ) ) );
		exit;
	}

	// show other galleries if needed
	$prev_next    = 'true' === $_GET['prev_next'];
	$prev_gallery = $next_gallery = null;

	// Get previous and next galleries if required
	if ( $prev_next ) {
		reset( $galleries );
		while ( current( $galleries ) ) {
			if ( key( $galleries ) == $gid ) {
				break;
			}
			next( $galleries );
		}

		// one step back
		$prev_gallery = prev( $galleries );
		// two step forward... Could be easier ? How ?
		next( $galleries );
		$next_gallery = next( $galleries );
	}

	$rss = nggMediaRss::get_gallery_mrss( $gallery, $prev_gallery, $next_gallery );

} elseif ( $mode == 'album' ) {

	// Get additional parameters
	$aid = isset( $_GET['aid'] ) ? (int) $_GET['aid'] : 0;

	if ( $aid == 0 ) {
		header( 'content-type:text/plain;charset=utf-8' );
		print esc_html( __( 'No album ID has been provided as parameter', 'nggallery' ) );
		exit;
	}

	// Get the album object
	$nggdb = new nggdb();
	$album = $nggdb->find_album( $aid );
	if ( ! isset( $album ) || $album == null ) {
		header( 'content-type:text/plain;charset=utf-8' );
		printf( __( 'The album ID=%s does not exist.', 'nggallery' ), intval( $aid ) );
		exit;
	}

	$rss = nggMediaRss::get_album_mrss( $album );
} else {
	header( 'content-type:text/plain;charset=utf-8' );
	echo __( 'Invalid MediaRSS command', 'nggallery' );
	exit;
}

// Output header for media RSS
header( 'content-type:text/xml;charset=utf-8' );
echo "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n";
echo $rss;
