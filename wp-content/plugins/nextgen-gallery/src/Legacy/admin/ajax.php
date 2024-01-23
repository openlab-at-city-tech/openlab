<?php
add_action( 'wp_ajax_ngg_ajax_operation', 'ngg_ajax_operation' );

/**
 * Image edit functions via AJAX
 *
 * @author Alex Rabe
 *
 * @return void
 */
function ngg_ajax_operation() {

	// if nonce is not correct it returns -1.
	check_ajax_referer( 'ngg-ajax' );

	// check for correct capability.
	if ( ! is_user_logged_in() ) {
		die( '-1' );
	}

	if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'ngg-ajax' ) ) {
		die( '-1' );
	}

	// check for correct NextGEN capability.
	if ( ! current_user_can( 'NextGEN Upload images' ) && ! current_user_can( 'NextGEN Manage gallery' ) ) {
		die( '-1' );
	}

	// include the ngg function.
	include_once __DIR__ . '/functions.php';

	// Get the image id.
	if ( isset( $_POST['image'] ) ) {
		$id = (int) $_POST['image'];
		// let's get the image data.
		$picture = nggdb::find_image( $id );
		// what do you want to do ?
		switch ( $_POST['operation'] ) {
			case 'create_thumbnail':
				$result = nggAdmin::create_thumbnail( $picture );
				break;
			case 'resize_image':
				$result = nggAdmin::resize_image( $picture );
				break;
			case 'rotate_cw':
				$result = nggAdmin::rotate_image( $picture, 'CW' );
				nggAdmin::create_thumbnail( $picture );
				break;
			case 'rotate_ccw':
				$result = nggAdmin::rotate_image( $picture, 'CCW' );
				nggAdmin::create_thumbnail( $picture );
				break;
			case 'set_watermark':
				$result = nggAdmin::set_watermark( $picture );
				break;
			case 'recover_image':
				$result = nggAdmin::recover_image( $id ) ? '1' : '0';
				break;
			case 'import_metadata':
				$result = \Imagely\NGG\DataMappers\Image::get_instance()->reimport_metadata( $id ) ? '1' : '0';
				break;
			case 'get_image_ids':
				$result = nggAdmin::get_image_ids( $id );
				break;

			// This will read the EXIF and then write it with the Orientation tag reset.
			case 'strip_orientation_tag':
				$storage      = \Imagely\NGG\DataStorage\Manager::get_instance();
				$image_path   = $storage->get_image_abspath( $id );
				$backup_path  = $image_path . '_backup';
				$exif_abspath = @file_exists( $backup_path ) ? $backup_path : $image_path;
				$exif_iptc    = @\Imagely\NGG\DataStorage\EXIFWriter::read_metadata( $exif_abspath );
				foreach ( $storage->get_image_sizes( $id ) as $size ) {
					if ( $size === 'backup' ) {
						continue;
					}
					@\Imagely\NGG\DataStorage\EXIFWriter::write_metadata( $storage->get_image_abspath( $id, $size ), $exif_iptc );
				}
				$result = '1';
				break;
			default:
				do_action( 'ngg_ajax_' . $_POST['operation'] );
				die( '-1' );
			break;
		}
		// A success should return a '1'.
		die( $result );
	}

	// The script should never stop here.
	die( '0' );
}

add_action( 'wp_ajax_createNewThumb', 'createNewThumb' );

function createNewThumb() {

	// check for correct capability.
	if ( ! is_user_logged_in() ) {
		die( '-1' );
	}

	// check for correct NextGEN capability.
	if ( ! current_user_can( 'NextGEN Manage gallery' ) ) {
		die( '-1' );
	}

	if ( ! wp_verify_nonce( $_POST['nonce'], 'ngg_update_thumbnail' ) ) {
		die( '-1' );
	}

	$id = (int) $_POST['id'];

	$x          = round( $_POST['x'] * $_POST['rr'], 0 );
	$y          = round( $_POST['y'] * $_POST['rr'], 0 );
	$w          = round( $_POST['w'] * $_POST['rr'], 0 );
	$h          = round( $_POST['h'] * $_POST['rr'], 0 );
	$crop_frame = [
		'x'      => $x,
		'y'      => $y,
		'width'  => $w,
		'height' => $h,
	];

	$storage = \Imagely\NGG\DataStorage\Manager::get_instance();

	// XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility.
	$params = [
		'watermark'  => false,
		'reflection' => false,
		'crop'       => true,
		'crop_frame' => $crop_frame,
	];
	$result = $storage->generate_thumbnail( $id, $params );

	if ( $result ) {
		echo 'OK';
	} else {
		header( 'HTTP/1.1 500 Internal Server Error' );
		echo 'KO';
	}

	exit();
}

add_action( 'wp_ajax_rotateImage', 'ngg_rotateImage' );

function ngg_rotateImage() {

	// check for correct capability.
	if ( ! is_user_logged_in() ) {
		die( '-1' );
	}

	if ( ! wp_verify_nonce( $_POST['nonce'], 'ngg-rotate-image' ) ) {
		die( '-1' );
	}

	// check for correct NextGEN capability.
	if ( ! current_user_can( 'NextGEN Manage gallery' ) ) {
		die( '-1' );
	}

	require_once dirname( __DIR__ ) . '/ngg-config.php';

	// include the ngg function.
	include_once __DIR__ . '/functions.php';

	$id     = (int) $_POST['id'];
	$result = '-1';

	switch ( $_POST['ra'] ) {
		case 'cw':
			$result = nggAdmin::rotate_image( $id, 'CW' );
			break;
		case 'ccw':
			$result = nggAdmin::rotate_image( $id, 'CCW' );
			break;
		case 'fv':
			// Note: H/V have been inverted here to make it more intuitive.
			$result = nggAdmin::rotate_image( $id, 0, 'H' );
			break;
		case 'fh':
			// Note: H/V have been inverted here to make it more intuitive.
			$result = nggAdmin::rotate_image( $id, 0, 'V' );
			break;
	}

	// recreate the thumbnail.
	nggAdmin::create_thumbnail( $id );

	if ( $result == 1 ) {
		die( '1' );
	}

	header( 'HTTP/1.1 500 Internal Server Error' );
	die( $result );
}
