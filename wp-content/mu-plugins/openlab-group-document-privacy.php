<?php

define( 'BP_GROUP_DOCUMENTS_SECURE_PATH', ABSPATH . '/wp-content/uploads/group-documents/' );

function cac_filter_doc_url( $doc_url, $group_id, $file ) {
	if( 'link' == openlab_get_document_type( $file ) ) {
		return $file;
	}

	$url = bp_get_root_domain() . '?get_group_doc=' . $group_id . '/' . $file;
	return $url;
}
add_filter( 'bp_group_documents_file_url', 'cac_filter_doc_url', 10, 3 );

function cac_catch_group_doc_request() {
	$error = false;

	if ( empty( $_GET['get_group_doc'] ) ) {
		return;
	}

	$doc_id = $_GET['get_group_doc'];

	// Sanity and security checks on passed data.
	$file_deets = explode( '/', $doc_id );

	// File paths containing slashes should be ignored.
	if ( 2 < count( $file_deets ) ) {
		status_header( 404 );
		nocache_headers();
		die();
	}

	// Group ID should be an integer.
	$group_id = $file_deets[0];
	if ( ! is_numeric( $group_id ) ) {
		status_header( 404 );
		nocache_headers();
		die();
	}

	// Check to see whether the current user has access to the doc in question
	$group = new BP_Groups_Group( $group_id );

	if ( empty( $group->id ) ) {
		$error = array(
			'message' => 'That group does not exist.',
			'redirect' => bp_get_root_domain(),
		);
	} else {
		$doc_filename = $file_deets[1];
		$document     = new BP_Group_Documents();
		$document->populate_by_file( $doc_filename );

		$document_id = ! empty( $document->id ) ? $document->id : 0;

		// First, check the file-specific privacy settings.
		$group_privacy = groups_get_groupmeta( $group->id, 'group_document_privacy_settings' );
		$doc_privacy   = isset( $group_privacy[ $document_id ] ) ? $group_privacy[ $document_id ] : 'public';

		$user_can_download = true;

		if ( 'admins' === $doc_privacy ) {
			$user_can_download = groups_is_user_admin( bp_loggedin_user_id(), $group_id );
		} elseif ( 'members' === $doc_privacy ) {
			$user_can_download = groups_is_user_member( bp_loggedin_user_id(), $group_id );
		} elseif ( 'public' !== $group->status ) {
			// If the group is not public, then the user must be logged in and
			// a member of the group to download the document
			if ( ! is_user_logged_in() || ! groups_is_user_member( bp_loggedin_user_id(), $group_id ) ) {
				$user_can_download = false;
			}
		}

		if ( ! $user_can_download ) {
			$error = array(
				'message' => sprintf( 'You must be a logged-in member of the group %s to access this document. If you are a member of the group, please log into the site and try again.', $group->name ),
				'redirect' => bp_get_group_permalink( $group ),
			);
		}

		// If we have gotten this far without an error, then the download can go through
		if ( ! $error ) {
			$doc_path = BP_GROUP_DOCUMENTS_SECURE_PATH . $doc_id;

			if ( file_exists( $doc_path ) ) {
				$mime_type = mime_content_type( $doc_path );
				$doc_size = filesize( $doc_path );

				header( 'Cache-Control: public, must-revalidate, post-check=0, pre-check=0' );
				header( 'Pragma: hack' );

				header( "Content-Type: $mime_type; name='" . $file_deets[1] . "'" );
				header( 'Content-Length: ' . $doc_size );

				header( 'Content-Disposition: attachment; filename="' . $file_deets[1] . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				ob_clean();
				flush();
				readfile( $doc_path );
				die();

			} else {
				// File does not exist
				$error = array(
					'message' => 'The file could not be found.',
					'redirect' => bp_get_group_permalink( $group ) . '/documents',
				);
			}
		}
	}

	// If we have gotten this far, there was an error. Add a message and redirect
	bp_core_add_message( $error['message'], 'error' );
	bp_core_redirect( $error['redirect'] );
}
add_filter( 'wp', 'cac_catch_group_doc_request', 1 );

// http://www.php.net/manual/en/function.mime-content-type.php#87856
if ( ! function_exists( 'mime_content_type' ) ) {

	function mime_content_type( $filename ) {
		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = strtolower( array_pop( explode( '.',$filename ) ) );
		if ( array_key_exists( $ext, $mime_types ) ) {
			return $mime_types[ $ext ];
		} elseif ( function_exists( 'finfo_open' ) ) {
			$finfo = finfo_open( FILEINFO_MIME );
			$mimetype = finfo_file( $finfo, $filename );
			finfo_close( $finfo );
			return $mimetype;
		} else {
			return 'application/octet-stream';
		}
	}
}

