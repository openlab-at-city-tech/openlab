<?php
add_action( 'wp_ajax_ngg_ajax_operation', 'ngg_ajax_operation' );

/**
 * Whether the current user holds any one of the given NextGEN capabilities.
 *
 * Used where no owner can be resolved, so there is nothing for the ownership half of the rule to
 * compare against and only the operation's own capability is left to check.
 *
 * @param string|string[] $capabilities Capability name, or names any one of which suffices.
 * @return bool
 */
function ngg_ajax_user_has_any_capability( $capabilities ) {
	foreach ( (array) $capabilities as $capability ) {
		if ( \Imagely\NGG\Util\Security::is_allowed( $capability ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether the current user may act on the images of the given gallery.
 *
 * Shared by the per-image gate below and by the operations whose posted id is a gallery id rather
 * than an image id, so the rule cannot drift between them.
 *
 * @param int             $gallery_id   NGG gallery id (gid).
 * @param string|string[] $capabilities Capability the operation requires of its caller, or names
 *                                      any one of which suffices.
 * @return bool
 */
function ngg_ajax_user_can_edit_gallery( $gallery_id, $capabilities = 'nextgen_edit_gallery' ) {
	$gallery_id = (int) $gallery_id;

	$gallery = $gallery_id > 0
		? \Imagely\NGG\DataMappers\Gallery::get_instance()->find( $gallery_id )
		: null;

	// A gallery that does not resolve has no author, so an id of 0 leaves the gate on the
	// cross-author capability - as before, now with the operation's own capability required
	// alongside it.
	$author_id = ( $gallery && isset( $gallery->author ) ) ? $gallery->author : 0;

	foreach ( (array) $capabilities as $capability ) {
		if ( \Imagely\NGG\Util\Security::can_manage_owned( $author_id, $capability ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Resolve an NGG image id to its parent gallery's author and check current user can edit.
 *
 * Ownership is not authorization on its own. This route admits "NextGEN Upload images" as well as
 * "NextGEN Manage gallery", and a gallery's stored author id outlives the capability that let the
 * user create it, so the owner branch used to pass a caller whose "NextGEN Manage gallery" had
 * been revoked straight through to create_thumbnail / rotate_cw / rotate_ccw / set_watermark /
 * recover_image / resize_image / import_metadata / strip_orientation_tag over that gallery's image
 * files. Every gate now goes through Security::can_manage_owned(), which requires the operation's
 * own capability first and only then lets ownership stand in for "NextGEN Manage others gallery",
 * matching the gallery and album gates. #1120, #1111.
 *
 * A missing image still returns true so the caller emits its own not-found response shape, but
 * only for a caller holding the gallery-management capability - see the branch itself.
 *
 * @param int             $image_id     NGG picture id (pid).
 * @param string|string[] $capabilities Capability the operation requires of its caller, or names
 *                                      any one of which suffices.
 * @return bool
 */
function ngg_ajax_user_can_edit_image( $image_id, $capabilities = 'nextgen_edit_gallery' ) {
	$image_id = (int) $image_id;
	if ( $image_id <= 0 ) {
		return false;
	}

	$image = nggdb::find_image( $image_id );
	if ( ! $image ) {
		// Nothing resolved, so there is no owner to compare against. The caller's own not-found
		// response is still allowed to render, but only for a holder of the management
		// capability - never on a narrower one a future caller might pass.
		return ngg_ajax_user_has_any_capability( 'nextgen_edit_gallery' );
	}

	return ngg_ajax_user_can_edit_gallery(
		isset( $image->galleryid ) ? (int) $image->galleryid : 0,
		$capabilities
	);
}

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

		// what do you want to do ? Read before authorizing: which capability the caller needs, and
		// whether the posted id names an image or a gallery, both depend on the operation.
		$operation = isset( $_POST['operation'] ) ? sanitize_text_field( wp_unslash( $_POST['operation'] ) ) : '';

		// Every operation on this route requires "NextGEN Manage gallery", which is what closes
		// the route's "NextGEN Upload images" half.
		//
		// create_thumbnail does NOT need an exception for the upload pipeline. The only code that
		// fires it over this route is nggAdmin::import_gallery(), whose sole caller is the
		// "Rescan folder" branch of the legacy Manage Gallery screen (manage.php), and that branch
		// already bails on can_user_manage_gallery() - so an upload-only caller never reaches it.
		// The import such a caller *can* reach is the POPE Add Gallery page, which runs
		// Manager::import_gallery_from_fs() and generates each thumbnail server-side inside
		// import_image_file(); it never posts here. The other two emitters
		// (`create_thumbnail` and `gallery_create_thumbnail` in manage.php) are on that same
		// Manage-gallery-gated screen.
		$required_capabilities = 'nextgen_edit_gallery';

		// get_image_ids() is handed a *gallery* id, not an image id: ngg.ajax.js rewrites every
		// "gallery_<op>" bulk operation into a get_image_ids call on the gallery and then runs
		// <op> over the ids it returns (src/Legacy/admin/js/ngg.ajax.js). Resolving that id as a
		// picture id authorized against the wrong row - and against no row at all whenever no
		// picture happened to carry the gallery's id, which passed outright - so it is checked
		// against the gallery it actually names.
		$authorized = ( 'get_image_ids' === $operation )
			? ngg_ajax_user_can_edit_gallery( $id, $required_capabilities )
			: ngg_ajax_user_can_edit_image( $id, $required_capabilities );

		if ( ! $authorized ) {
			die( '-1' );
		}

		// let's get the image data.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- $_POST['image'] is sanitized on line 36
		$picture = nggdb::find_image( $id );

		// Bounds the dynamic ngg_ajax_<operation> hook to addon-registered op names.
		$allowed_dynamic_operations = (array) apply_filters(
			'ngg_ajax_operation_allowlist',
			[]
		);

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
				if ( $operation !== '' && in_array( $operation, $allowed_dynamic_operations, true ) ) {
					do_action( 'ngg_ajax_' . $operation, $id );
				}
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

	if ( ! ngg_ajax_user_can_edit_image( $id ) ) {
		die( '-1' );
	}

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

	$id = (int) ( isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : 0 );

	if ( ! ngg_ajax_user_can_edit_image( $id ) ) {
		die( '-1' );
	}

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
