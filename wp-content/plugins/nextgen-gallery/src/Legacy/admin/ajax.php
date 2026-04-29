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
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- check_ajax_referer handles nonce verification internally
	check_ajax_referer( 'ngg-ajax', 'nonce' );

	// check for correct capability.
	if ( ! is_user_logged_in() ) {
		die( '-1' );
	}

	if ( ! wp_verify_nonce( isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '', 'ngg-ajax' ) ) {
		die( '-1' );
	}

	// check for correct NextGEN capability.
 // phpcs:ignore WordPress.WP.Capabilities.Unknown
	if ( ! current_user_can( 'NextGEN Upload images' ) && ! current_user_can( 'NextGEN Manage gallery' ) ) {
		die( '-1' );
	}

	// include the ngg function.
	include_once __DIR__ . '/functions.php';

	// Get the image id.
	if ( isset( $_POST['image'] ) ) {
		$id = (int) sanitize_text_field( wp_unslash( $_POST['image'] ) );
		// let's get the image data.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- $_POST['image'] is sanitized on line 36
		$picture = nggdb::find_image( $id );
		// what do you want to do ?
		$operation = isset( $_POST['operation'] ) ? sanitize_text_field( wp_unslash( $_POST['operation'] ) ) : '';
		switch ( $operation ) {
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
				$storage     = \Imagely\NGG\DataStorage\Manager::get_instance();
				$image_path  = $storage->get_image_abspath( $id );
				$backup_path = $image_path . '_backup';
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$exif_abspath = @file_exists( $backup_path ) ? $backup_path : $image_path;
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$exif_iptc = @\Imagely\NGG\DataStorage\EXIFWriter::read_metadata( $exif_abspath );
				foreach ( $storage->get_image_sizes( $id ) as $size ) {
					if ( $size === 'backup' ) {
						continue;
					}
					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					@\Imagely\NGG\DataStorage\EXIFWriter::write_metadata( $storage->get_image_abspath( $id, $size ), $exif_iptc );
				}
				$result = '1';
				break;
			default:
				do_action( 'ngg_ajax_' . $operation );
				die( '-1' );
		}
		// A success should return a '1'.
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $result contains safe response data for AJAX
		die( $result );
	}   // The script should never stop here.
	die( '0' );
}

add_action( 'wp_ajax_createNewThumb', 'createNewThumb' );

function createNewThumb() {

	// check for correct capability.
	if ( ! is_user_logged_in() ) {
		die( '-1' );
	}

	// check for correct NextGEN capability.
 // phpcs:ignore WordPress.WP.Capabilities.Unknown
	if ( ! current_user_can( 'NextGEN Manage gallery' ) ) {
		die( '-1' );
	}

	if ( ! wp_verify_nonce( isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '', 'ngg_update_thumbnail' ) ) {
		die( '-1' );
	}

	$id = (int) ( isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : 0 );

	$x          = round( ( isset( $_POST['x'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['x'] ) ) ) : 0 ) * ( isset( $_POST['rr'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['rr'] ) ) ) : 1 ), 0 );
	$y          = round( ( isset( $_POST['y'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['y'] ) ) ) : 0 ) * ( isset( $_POST['rr'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['rr'] ) ) ) : 1 ), 0 );
	$w          = round( ( isset( $_POST['w'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['w'] ) ) ) : 0 ) * ( isset( $_POST['rr'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['rr'] ) ) ) : 1 ), 0 );
	$h          = round( ( isset( $_POST['h'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['h'] ) ) ) : 0 ) * ( isset( $_POST['rr'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['rr'] ) ) ) : 1 ), 0 );
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

	if ( ! wp_verify_nonce( isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '', 'ngg-rotate-image' ) ) {
		die( '-1' );
	}

	// check for correct NextGEN capability.
 // phpcs:ignore WordPress.WP.Capabilities.Unknown
	if ( ! current_user_can( 'NextGEN Manage gallery' ) ) {
		die( '-1' );
	}

	require_once dirname( __DIR__ ) . '/ngg-config.php';

	// include the ngg function.
	include_once __DIR__ . '/functions.php';

	$id     = (int) ( isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : 0 );
	$result = '-1';

	$ra = isset( $_POST['ra'] ) ? sanitize_text_field( wp_unslash( $_POST['ra'] ) ) : '';
	switch ( $ra ) {
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
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $result contains safe error response data
	die( $result );
}
