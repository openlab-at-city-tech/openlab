<?php
/**
 * Fetch media information for a post.
 *
 * @category Status Updates
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv3
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

/**
 * Get image binary for passing to API.
 *
 * @param int    $attachment Attachment ID.
 * @param int    $post_ID Post ID being sent.
 * @param string $service Which service needs the binary.
 *
 * @return string|object;
 */
function wpt_image_binary( $attachment, $post_ID, $service = 'twitter' ) {
	$image_sizes = get_intermediate_image_sizes();
	if ( in_array( 'large', $image_sizes, true ) ) {
		$size = 'large';
	} else {
		$size = array_pop( $image_sizes );
	}
	/**
	 * Filter the uploaded image size.
	 *
	 * @hook wpt_upload_image_size
	 *
	 * @param string $size Name of size targeted for upload. Default 'large' if exists.
	 *
	 * @return string
	 */
	$size = apply_filters( 'wpt_upload_image_size', $size );
	if ( 'mastodon' === $service ) {
		$path = wpt_attachment_path( $attachment, $size );
		$mime = wp_get_image_mime( $path );
		$name = basename( $path );
		global $wp_filesystem;
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
		$binary = $wp_filesystem->get_contents( $path );
		$file   = array(
			'file' => $binary,
			'mime' => $mime,
			'name' => $name,
		);

		wpt_mail( 'Media binary fetched (' . $service . ')', 'Path: ' . $path . '; Attachment ID: ' . $attachment, $post_ID );
		if ( ! $binary ) {
			return false;
		}

		return $file;

	} elseif ( 'bluesky' === $service ) {
		$path = wpt_attachment_path( $attachment, $size );
		global $wp_filesystem;
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
		$file = $wp_filesystem->get_contents( $path );
		wpt_mail( 'Media binary fetched (' . $service . ')', 'Path: ' . $path . '; Attachment ID: ' . $attachment, $post_ID );

		return $file;
	} else {
		$upload    = wp_get_attachment_image_src( $attachment, $size );
		$image_url = $upload[0];
		$remote    = wp_remote_get( $image_url );
		if ( ! is_wp_error( $remote ) ) {
			$binary = wp_remote_retrieve_body( $remote );
			wpt_mail( 'Media binary fetched (' . $service . ')', 'Url: ' . $image_url . '; Attachment ID: ' . $attachment, $post_ID );
		} else {
			$binary = false;
		}
		if ( ! $binary ) {
			return false;
		}

		return base64_encode( $binary );
	}
}

/**
 * Fetch an attachment's file path. Recurses to fetch full sized path if an invalid size is passed.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size Requested size.
 *
 * @return string|false
 */
function wpt_attachment_path( $attachment_id, $size = '' ) {
	$file = get_attached_file( $attachment_id, true );
	if ( empty( $size ) || 'full' === $size ) {
		// for the original size get_attached_file is fine.
		return realpath( $file );
	}
	if ( ! wp_attachment_is_image( $attachment_id ) ) {
		return false; // the id is not referring to a media.
	}
	$info = image_get_intermediate_size( $attachment_id, $size );
	if ( ! is_array( $info ) || ! isset( $info['file'] ) ) {
		// If this is invalid due to an invalid size, recurse to fetch full size.
		if ( '' !== $size ) {
			$path = wpt_attachment_path( $attachment_id );

			return $path;
		}
		return false; // probably a bad size argument.
	}

	return realpath( str_replace( wp_basename( $file ), $info['file'], $file ) );
}

/**
 * Identify whether a post should be uploading media. Test settings and verify whether post has images that can be uploaded.
 *
 * @param int $post_ID Post ID.
 *
 * @return boolean
 */
function wpt_post_with_media( $post_ID ) {
	$return = false;
	if ( ! function_exists( 'wpt_pro_exists' ) || ! $post_ID ) {
		return $return;
	}
	$post_info = wpt_post_info( $post_ID );
	if ( isset( $post_info['wpt_image'] ) && 1 === (int) $post_info['wpt_image'] ) {
		// Post settings win over filters.
		return $return;
	}
	if ( ! get_option( 'wpt_media' ) ) {
		// Don't return immediately, this needs to be overrideable for posts.
		$return = false;
	} else {
		if ( has_post_thumbnail( $post_ID ) || wpt_post_attachment( $post_ID ) ) {
			$return = true;
		}
	}
	/**
	 * Filter whether this post should upload media.
	 *
	 * @hook wpt_upload_media
	 * @param {bool} $upload True to allow this post to upload media.
	 * @param {int}  $post_ID Post ID.
	 *
	 * @return {bool}
	 */
	return apply_filters( 'wpt_upload_media', $return, $post_ID );
}
