<?php

namespace Imagely\NGG\DynamicThumbnails;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\StaticAssets;

/**
 * Dynamic thumbnails controller.
 */
class Controller {

	public function index_action( $return_output = false ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@set_time_limit( 0 );
		wp_raise_memory_limit();

		$dynthumbs = Manager::get_instance();

		$uri            = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$params         = $dynthumbs->get_params_from_uri( $uri );
		$request_params = $params;

		if ( $params != null ) {
			$storage = StorageManager::get_instance();

			$image_id = $params['image'];
			$size     = $dynthumbs->get_size_name( $params );

			/*
			 * The signature is validated for every request, not only for requests whose
			 * size has no file behind it yet.
			 *
			 * This check used to be skipped whenever get_image_abspath() resolved to an
			 * existing file, on the reasoning that an already-generated thumbnail needs
			 * no further gate. But the path it resolves comes from the size's stored
			 * meta_data, so "the file exists" was a statement about a database value
			 * rather than about the request being legitimate: a poisoned meta_data entry
			 * pointing at any existing file on disk skipped the signature entirely and
			 * had its contents returned by render_image() to an unauthenticated caller.
			 *
			 * Every URL this route is reachable from is produced by
			 * Manager::get_image_uri(), which always appends wp_hash(), so requiring the
			 * signature costs legitimate requests nothing.
			 */
			$uri_plain = $dynthumbs->get_uri_from_params( $request_params );
			$hash      = \wp_hash( $uri_plain );
			$valid     = strpos( $uri, $hash ) !== false;

			/*
			 * MERGE NOTE (#932 + #933): #933 kept the "already generated needs no
			 * signature" escape hatch, on the argument that wp_hash() is salt-derived so a
			 * mandatory signature would break every URL sitting in a page cache or CDN edge
			 * after a salt rotation or a site clone. That cost turned out to be almost nil:
			 * once a derivative exists on disk, Manager::get_image_url() returns a direct
			 * static /wp-content/gallery/... URL and never touches this route, so the URLs a
			 * page cache actually holds are static ones. Only a not-yet-generated size is
			 * signed. The mandatory check is therefore kept, and #933's containment in
			 * get_computed_image_abspath() stands behind it rather than in place of it -
			 * neither fix depends on the other being airtight.
			 */
			if ( ! $valid ) {
				/*
				 * This branch used to need both a null abspath and a signature mismatch, so
				 * it was near-unreachable. Now that the signature is mandatory it is the
				 * answer to every already-generated derivative whose URL was signed with
				 * older keys, so it has to be a well-formed response: without a content type
				 * a browser sent `nosniff`, or asked for the URL directly, renders a broken
				 * image rather than the placeholder.
				 */
				$this->serve_placeholder( $uri );

				return;
			}

			if ( ! $storage->render_image( $image_id, $size ) ) {
				/*
				 * A correctly signed URL can still fail to produce bytes: the source file
				 * may be gone, or generate_image_size() may refuse the destination (an
				 * image row whose stored filename still carries a script extension, say
				 * an older "photo.php_.jpg"). render_image() then returns false having
				 * written nothing, and the request used to fall out of here as a
				 * zero-byte 200 with the page's own content type - a permanently broken
				 * image with only a WP_DEBUG line to explain it, which is precisely the
				 * symptom the destination guard in Manager::generate_image_size() was
				 * narrowed to avoid.
				 *
				 * Answering with the placeholder makes readme.txt's documented outcome
				 * true for the refused population as well, and does not weaken the
				 * signature gate: this branch is only reached for a valid signature and
				 * still serves no gallery bytes.
				 *
				 * This supersedes #933's bare `status_header( 404 )` for the same case:
				 * that stopped browsers and CDNs caching the zero-byte body as a valid
				 * image, but still answered with no bytes, so the image stayed visibly
				 * broken. serve_placeholder() sends the same 404 and a real PNG with it.
				 */
				$this->serve_placeholder( $uri );
			}
		}
	}

	/**
	 * Answers the request with the invalid-image placeholder.
	 *
	 * Shared by the invalid-signature branch and by a correctly signed request whose
	 * image could not be rendered, so both populations get an actual image back
	 * instead of an empty body.
	 *
	 * @param string $uri The requested URI, for the diagnostic log line.
	 * @return void
	 */
	private function serve_placeholder( $uri ) {
		$headers_sent = headers_sent();

		if ( ! $headers_sent ) {
			status_header( 404 );
			header( 'Content-Type: image/png' );
			nocache_headers();
		}

		$filename = StaticAssets::get_abspath( 'DynamicThumbnails/invalid_image.png' );

		// readfile() sits in disable_functions on some hosts.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$readable = ( $filename && function_exists( 'readfile' ) && @file_exists( $filename ) );
		$written  = false;

		if ( $readable ) {
			// Anything already buffered belongs to the page, not to this image.
			while ( ob_get_level() > 0 ) {
				ob_end_clean();
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
			$written = ( false !== readfile( $filename ) );
		}

		if ( ! $written ) {
			/*
			 * Never answer with an empty body. Without this the response is a 404
			 * carrying an image content type and no bytes -- a broken image with
			 * nothing to diagnose it by, for the whole population readme.txt tells
			 * to expect placeholders after a key rotation, clone or address change.
			 * The fallback is a 1x1 transparent PNG built in-process.
			 */
			echo base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYGBgAAAABQABafFPUAAAAABJRU5ErkJggg==' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		}

		if ( ( ! $written || $headers_sent ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log(
				sprintf(
					'NextGEN Gallery: could not serve the dynamic-thumbnail placeholder for "%1$s" (%2$s).',
					$uri,
					$headers_sent
						? 'headers were already sent, so the 404 and content type could not be set'
						: 'the placeholder file is missing or readfile() is unavailable'
				)
			);
		}
	}
}
