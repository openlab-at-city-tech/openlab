<?php
/**
 * Elementor Meta import handler.
 *
 * This is needed because by default, the importer breaks our JSON meta.
 *
 * @package    themeisle-onboarding
 * @soundtrack All Apologies (Live) - Nirvana
 */

/**
 * Class Themeisle_OB_Elementor_Meta_Handler
 *
 * @package themeisle-onboarding
 */
class Themeisle_OB_Elementor_Meta_Handler {
	/**
	 * Elementor meta key.
	 *
	 * @var string
	 */
	private $meta_key = '_elementor_data';

	/**
	 * Meta value.
	 *
	 * @var null
	 */
	private $value = null;

	/**
	 * Imported site url.
	 *
	 * @var null
	 */
	private $import_url = null;

	/**
	 * A list of allowed mimes.
	 *
	 * @var array
	 */
	protected $extensions = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
		'svg'          => 'image/svg+xml',
	);

	/**
	 * Themeisle_OB_Elementor_Meta_Handler constructor.
	 *
	 * @param string $unfiltered_value the unfiltered meta value.
	 */
	public function __construct( $unfiltered_value, $site_url ) {
		$this->value      = $unfiltered_value;
		$this->import_url = $site_url;
	}

	/**
	 * Filter the meta to allow escaped JSON values.
	 */
	public function filter_meta() {
		add_filter( 'sanitize_post_meta_' . $this->meta_key, array( $this, 'allow_escaped_json_meta' ), 10, 3 );
	}

	/**
	 * Allow JSON escaping.
	 *
	 * @param string $val  meta value.
	 * @param string $key  meta key.
	 * @param string $type meta type.
	 *
	 * @return array|string
	 */
	public function allow_escaped_json_meta( $val, $key, $type ) {
		if ( empty( $this->value ) ) {
			return $val;
		}

		$this->replace_image_urls();
		$this->replace_link_urls();

		return $this->value;
	}

	/**
	 * Replace link urls.
	 *
	 * @return void
	 */
	private function replace_link_urls() {
		$decoded_meta = json_decode( $this->value, true );
		if ( ! is_array( $decoded_meta ) ) {
			return;
		}

		$site_url  = get_site_url();
		$url_parts = parse_url( $site_url );

		array_walk_recursive(
			$decoded_meta,
			function ( &$value, $key ) use ( $site_url, $url_parts ) {
				if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) {
					return;
				}
				$url = parse_url( $value );

				if ( $url['host'] !== $url_parts['host'] ) {
					$value = str_replace( $this->import_url, $site_url, $value );
				}
			}
		);

		$this->value = json_encode( $decoded_meta );
	}


	/**
	 * Replace demo urls in meta with site urls.
	 */
	private function replace_image_urls() {
		// Get all slashed and un-slashed urls.
		$old_urls = $this->get_urls_to_replace();
		// Create an associative array.
		$urls = array_combine( $old_urls, $old_urls );
		// Unslash values of associative array.
		$urls = array_map( 'wp_unslash', $urls );
		// Remap host and directory path.
		$urls = array_map( array( $this, 'remap_host' ), $urls );
		// Replace image urls in meta.
		$this->value = str_replace( array_keys( $urls ), array_values( $urls ), $this->value );
	}

	/**
	 * Get url replace array.
	 *
	 * @return array
	 */
	private function get_urls_to_replace() {
		$regex = '/(?:http(?:s?):)(?:[\/\\\\\\\\|.|\w|\s|-])*\.(?:' . implode( '|', array_keys( $this->extensions ) ) . ')/m';
		preg_match_all( $regex, $this->value, $urls );

		$urls = array_map(
			function ( $value ) {
					return rtrim( html_entity_decode( $value ), '\\' );
			},
			$urls[0]
		);

		$urls = array_unique( $urls );

		return array_values( $urls );
	}

	/**
	 * Remap URLs host.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	private function remap_host( $url ) {
		$old_url   = $url;
		$url_parts = parse_url( $url );

		if ( ! isset( $url_parts['host'] ) ) {
			return $url;
		}
		if ( $url_parts['host'] !== 'demo.themeisle.com' ) {
			return $url;
		}
		$url_parts['path'] = preg_split( '/\//', $url_parts['path'] );
		$url_parts['path'] = array_slice( $url_parts['path'], - 3 );

		$uploads_dir = wp_get_upload_dir();
		$uploads_url = $uploads_dir['baseurl'];

		$new_url = esc_url( $uploads_url . '/' . join( '/', $url_parts['path'] ) );

		return str_replace( $old_url, $new_url, $url );
	}
}
