<?php
/**
 * REST API endpoints for NextGEN Gallery settings.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\Settings\GalleryPathValidation;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Settings\GlobalSettings;
use Imagely\NGG\Util\Security;
use Imagely\NGG\DynamicThumbnails\Manager as DynamicThumbnailsManager;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

/**
 * Class SettingsREST
 * Handles REST API endpoints for NextGEN Gallery settings.
 *
 * @package Imagely\NGG\REST\DataMappers
 */
class SettingsREST {
	/**
	 * Register the REST API routes for settings
	 */
	public static function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/settings',
			[
				[
					'methods'             => 'GET',
					'callback'            => [ self::class, 'get_settings' ],
					'permission_callback' => [ self::class, 'check_read_permission' ],
				],
				[
					'methods'             => 'PUT',
					'callback'            => [ self::class, 'update_settings' ],
					'permission_callback' => [ self::class, 'check_edit_permission' ],
				],
			]
		);

		// Get and update global settings (multisite).
		register_rest_route(
			'imagely/v1',
			'/settings/global',
			[
				[
					'methods'             => 'GET',
					'callback'            => [ self::class, 'get_global_settings' ],
					'permission_callback' => [ self::class, 'check_network_permission' ],
				],
				[
					'methods'             => 'PUT',
					'callback'            => [ self::class, 'update_global_settings' ],
					'permission_callback' => [ self::class, 'check_network_permission' ],
				],
			]
		);

		// Generate watermark preview.
		register_rest_route(
			'imagely/v1',
			'/settings/watermark-preview',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'get_watermark_preview' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'watermark_options' => [
						'type'              => 'object',
						'required'          => false,
						'description'       => 'Watermark configuration options',
						'validate_callback' => function ( $param ) {
							return is_array( $param ) || is_object( $param );
						},
					],
				],
			]
		);

		// Reset settings to defaults.
		register_rest_route(
			'imagely/v1',
			'/settings/reset',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'reset_settings' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'confirm'       => [
						'type'              => 'boolean',
						'required'          => true,
						'description'       => 'Confirmation flag to prevent accidental resets',
						'validate_callback' => function ( $param ) {
							return is_bool( $param ) && $param;
						},
					],
					'settings_type' => [
						'type'              => 'string',
						'required'          => false,
						'default'           => 'all',
						'description'       => 'Type of settings to reset: all, non_ecommerce, or ecommerce',
						'enum'              => [ 'all', 'non_ecommerce', 'ecommerce' ],
						'validate_callback' => function ( $param ) {
							return in_array( $param, [ 'all', 'non_ecommerce', 'ecommerce' ], true );
						},
					],
				],
			]
		);

		// Clear all cache.
		register_rest_route(
			'imagely/v1',
			'/cache/clear',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'clear_cache' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
			]
		);

		// Regenerate all thumbnails.
		register_rest_route(
			'imagely/v1',
			'/thumbnails/regenerate',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'regenerate_thumbnails' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
			]
		);

		// Get system information.
		register_rest_route(
			'imagely/v1',
			'/system-info',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_system_info' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
			]
		);

		// Check if gtag.js is loaded on the site frontend (for GA4 integration).
		register_rest_route(
			'imagely/v1',
			'/settings/gtag-status',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_gtag_status' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'force' => [
						'type'    => 'boolean',
						'default' => false,
					],
				],
			]
		);
	}

	/**
	 * Get the value type for a setting based on the example JSON.
	 * Handles nested objects and stringified numbers/booleans.
	 *
	 * @param mixed $value The value to check.
	 * @return string
	 */
	protected static function get_value_type( $value ) {
		if ( is_null( $value ) ) {
			// For null values, allow mixed types (string, integer, or null)
			return [ 'string', 'integer', 'null' ];
		}
		if ( is_array( $value ) ) {
			// Check if associative (object) or sequential (array)
			return self::is_assoc( $value ) ? 'object' : 'array';
		}
		if ( is_bool( $value ) ) {
			return 'boolean';
		}
		if ( is_int( $value ) ) {
			return 'integer';
		}
		if ( is_float( $value ) ) {
			return 'number';
		}
		if ( is_string( $value ) ) {
			// Only detect stringified types if they are obvious candidates
			// This prevents over-aggressive type conversion
			if ( 'true' === $value || 'false' === $value ) {
				return 'boolean';
			}
			// Be more conservative with numeric detection
			// Only convert if it's a simple numeric string without leading zeros
			if ( is_numeric( $value ) && (string) (int) $value === $value && false === strpos( $value, '.' ) ) {
				return 'integer';
			}
			if ( is_numeric( $value ) && false !== strpos( $value, '.' ) && (string) (float) $value === $value ) {
				return 'number';
			}
			return 'string';
		}
		return 'string'; // Default to string for unknown types.
	}

	/**
	 * Helper to check if an array is associative (object-like).
	 *
	 * @param array $arr The array to check.
	 * @return bool
	 */
	protected static function is_assoc( array $arr ) {
		if ( [] === $arr ) {
			return false;
		}
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

	/**
	 * Sanitize a setting value for security.
	 * Simple approach - detect if content needs HTML/newlines preserved.
	 *
	 * @param mixed  $value The value to sanitize.
	 * @param string $key The setting key (to determine appropriate sanitization).
	 * @return mixed
	 */
	protected static function sanitize_setting( $value, $key = '' ) {
		// The slug is interpolated into patterns and used as an explode()
		// separator, so anything not a usable string becomes the default.
		if ( 'router_param_slug' === $key ) {
			$slug = is_scalar( $value ) && ! is_bool( $value ) ? (string) $value : '';
			$slug = trim( trim( sanitize_text_field( $slug ) ), '/' );
			return '' === $slug ? 'nggallery' : $slug;
		}

		// Handle arrays and objects recursively
		if ( is_array( $value ) ) {
			return array_map(
				function ( $v ) use ( $key ) {
					return self::sanitize_setting( $v, $key );
				},
				$value
			);
		}

		// Settings class will convert objects to arrays
		if ( is_object( $value ) ) {
			return (array) $value;
		}

		// Let null, boolean, and numeric values pass through as-is
		if ( is_null( $value ) || is_bool( $value ) || is_numeric( $value ) ) {
			return $value;
		}

		// Convert to string for processing
		$string_value = (string) $value;

		// Fields that should preserve HTML and newlines
		if ( self::should_preserve_html( $key, $string_value ) ) {
			return wp_kses_post( $string_value );
		}

		// Fields that should preserve newlines but strip HTML
		if ( self::should_preserve_newlines( $key, $string_value ) ) {
			return sanitize_textarea_field( $string_value );
		}

		// GA4 Measurement ID: must start with G- followed by alphanumeric characters (e.g. G-XXXXXXXXXX)
		if ( 'ga4_measurement_id' === $key ) {
			$trimmed = trim( $string_value );
			if ( empty( $trimmed ) ) {
				return '';
			}
			if ( preg_match( '/^G-[A-Za-z0-9_-]+$/', $trimmed ) ) {
				return sanitize_text_field( $trimmed );
			}
			return '';
		}

		// Default: strip HTML and newlines for security
		return sanitize_text_field( $string_value );
	}

	/**
	 * Check if a field should preserve HTML content.
	 *
	 * @param string $key The setting key.
	 * @param string $value The setting value.
	 * @return bool
	 */
	protected static function should_preserve_html( $key, $value ) {
		// Fields that commonly contain HTML or special placeholders
		$html_fields = [
			'ecommerce_cheque_instructions',
			'proofing_user_confirmation_template',
			'proofing_email_template', // Contains %% placeholders that need protection
			'relatedHeading',
		];

		// Check if field name indicates HTML content
		if ( in_array( $key, $html_fields, true ) ) {
			return true;
		}

		// Auto-detect: if value contains HTML tags, preserve them
		if ( wp_strip_all_tags( $value ) !== $value ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a field should preserve newlines.
	 *
	 * @param string $key The setting key.
	 * @param string $value The setting value.
	 * @return bool
	 */
	protected static function should_preserve_newlines( $key, $value ) {
		// Fields that commonly contain newlines (email templates, etc.)
		$newline_fields = [
			'ecommerce_email_notification_body',
			'ecommerce_email_receipt_body',
			'proofing_user_email_template',
		];

		// Check if field name indicates newline content
		if ( in_array( $key, $newline_fields, true ) ) {
			return true;
		}

		// Auto-detect: if value contains newlines, preserve them
		if ( strpos( $value, "\n" ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if user has permission to read settings
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return Security::is_allowed( 'NextGEN Change options' );
	}

	/**
	 * Check if user has permission to edit settings
	 *
	 * @return bool
	 */
	public static function check_edit_permission() {
		return Security::is_allowed( 'NextGEN Change options' );
	}

	/**
	 * Check if user has permission to read/write network-wide global settings.
	 *
	 * The /settings/global route writes the single network-wide ngg_options row
	 * (GlobalSettings::save() uses update_site_option()), so it is network-admin
	 * territory — not the per-site 'NextGEN Change options' capability that
	 * Installer::set_role_caps() grants to every subsite administrator. Gating
	 * the write on anything less lets a subsite admin rewrite the shared row and
	 * flip the flag guarding the capability-remapping route.
	 *
	 * @return bool
	 */
	public static function check_network_permission() {
		return is_multisite() ? is_super_admin() : current_user_can( 'manage_options' );
	}

	/**
	 * Get all settings
	 *
	 * @return \WP_REST_Response
	 */
	public static function get_settings() {
		$settings = Settings::get_instance();
		return new WP_REST_Response( $settings->to_array(), 200 );
	}

	/**
	 * Validate a gallery-path request value against the galleries root.
	 *
	 * @param mixed $gallerypath Raw request value.
	 * @return true|\WP_Error
	 */
	protected static function validate_gallerypath_rest_value( $gallerypath ) {
		$result = GalleryPathValidation::validate_relative_under_galleries_root( $gallerypath );
		if ( is_wp_error( $result ) ) {
			$data = $result->get_error_data();
			if ( ! is_array( $data ) ) {
				$data = [];
			}
			$data['status'] = 400;
			return new WP_Error( $result->get_error_code(), $result->get_error_message(), $data );
		}
		return true;
	}

	/**
	 * Update settings via REST API request.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function update_settings( WP_REST_Request $request ) {
		$new_settings = $request->get_json_params();
		if ( ! is_array( $new_settings ) ) {
			$new_settings = [];
		}
		if ( is_multisite() && isset( $new_settings['gallerypath'] ) && ! is_main_site() && ! is_super_admin() ) {
			unset( $new_settings['gallerypath'] );
		}
		if ( isset( $new_settings['gallerypath'] ) ) {
			$gallerypath_validation = self::validate_gallerypath_rest_value( $new_settings['gallerypath'] );
			if ( is_wp_error( $gallerypath_validation ) ) {
				return $gallerypath_validation;
			}
		}
		$settings = Settings::get_instance();

		foreach ( $new_settings as $key => $value ) {
			// Security sanitization with context-aware handling
			$sanitized_value = self::sanitize_setting( $value, $key );
			$settings->set( $key, $sanitized_value );
		}

		try {
			$settings->save();

			/**
			 * Fires after settings saved via the REST API have been persisted.
			 *
			 * Lets addons (e.g. NextGEN Pro payment gateways) react to a settings change made through the
			 * React admin, mirroring behavior they already trigger on the legacy admin form save.
			 *
			 * @param array    $new_settings The settings keys/values that were sent in this request.
			 * @param Settings $settings     The settings instance, already saved.
			 */
			do_action( 'ngg_settings_updated', $new_settings, $settings );

			return new WP_REST_Response( $settings->to_array(), 200 );
		} catch ( \Exception $e ) {
			return new WP_Error(
				'settings_update_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Get global settings
	 *
	 * @return \WP_REST_Response | \WP_Error
	 */
	public static function get_global_settings() {
		if ( ! is_multisite() ) {
			return new WP_Error(
				'not_multisite',
				__( 'Global settings are only available in multisite installations', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		$settings = GlobalSettings::get_instance();
		return new WP_REST_Response( $settings->to_array(), 200 );
	}

	/**
	 * Update global settings
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function update_global_settings( WP_REST_Request $request ) {
		if ( ! is_multisite() ) {
			return new WP_Error(
				'not_multisite',
				__( 'Global settings are only available in multisite installations', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		$new_settings = $request->get_json_params();
		if ( ! is_array( $new_settings ) ) {
			$new_settings = [];
		}

		// Run the same gallery-path validation the sibling /settings route applies,
		// so the network-wide row cannot be written an out-of-root gallery path.
		if ( isset( $new_settings['gallerypath'] ) ) {
			$gallerypath_validation = self::validate_gallerypath_rest_value( $new_settings['gallerypath'] );
			if ( is_wp_error( $gallerypath_validation ) ) {
				return $gallerypath_validation;
			}
		}

		$settings = GlobalSettings::get_instance();

		foreach ( $new_settings as $key => $value ) {
			// Security sanitization with context-aware handling
			$sanitized_value = self::sanitize_setting( $value, $key );
			$settings->set( $key, $sanitized_value );
		}

		try {
			$settings->save();
			return new WP_REST_Response( $settings->to_array(), 200 );
		} catch ( \Exception $e ) {
			return new WP_Error(
				'settings_update_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Get watermark preview
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_watermark_preview( WP_REST_Request $request ) {
		$watermark_options = $request->get_param( 'watermark_options' );

		// Check if watermark options are provided
		if ( empty( $watermark_options ) ) {
			return new WP_Error(
				'missing_watermark_options',
				__( 'Watermark options are required', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Validate watermark type
		$wm_type = $watermark_options['wmType'] ?? '';
		if ( empty( $wm_type ) || ! in_array( $wm_type, [ 'text', 'image' ], true ) ) {
			return new WP_Error(
				'invalid_watermark_type',
				__( 'Invalid watermark type. Must be "text" or "image".', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Validate text watermark requirements
		if ( 'text' === $wm_type ) {
			$wm_text = $watermark_options['wmText'] ?? '';
			if ( empty( trim( $wm_text ) ) ) {
				return new WP_Error(
					'empty_watermark_text',
					__( 'Watermark text cannot be empty when using text watermarks.', 'nggallery' ),
					[ 'status' => 400 ]
				);
			}
		}

		// Validate image watermark requirements
		if ( 'image' === $wm_type ) {
			$wm_path = trim( $watermark_options['wmPath'] ?? '' );
			if ( empty( $wm_path ) ) {
				return new WP_Error(
					'missing_watermark_image',
					__( 'Watermark image path is required when using image watermarks.', 'nggallery' ),
					[ 'status' => 400 ]
				);
			}

			// Allow either absolute URLs, absolute filesystem paths, or server-relative paths.
			$is_url = is_string( $wm_path ) && preg_match( '/^https?:\/\//i', $wm_path );
			$is_abs = is_string( $wm_path ) && ( 0 === strpos( $wm_path, '/' ) || preg_match( '/^[A-Za-z]:[\\\\\/]*/', $wm_path ) );
			// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf -- URL case has no file check.
			if ( $is_url ) {
				// URL will be downloaded later; no file_exists check here.
			} elseif ( $is_abs ) {
				// Absolute filesystem path must exist
				if ( ! file_exists( $wm_path ) ) {
					return new WP_Error(
						'invalid_watermark_image',
						__( 'Watermark image file does not exist.', 'nggallery' ),
						[ 'status' => 400 ]
					);
				}
			} else {
				// Treat as server-relative path under ABSPATH
				$full_path = ABSPATH . ltrim( $wm_path, '/' );
				if ( ! file_exists( $full_path ) ) {
					return new WP_Error(
						'invalid_watermark_image',
						__( 'Watermark image file does not exist.', 'nggallery' ),
						[ 'status' => 400 ]
					);
				}
			}
		}

		try {
			// Get NextGen managers
			$mapper  = ImageMapper::get_instance();
			$storage = StorageManager::get_instance();

			// Find the first available NextGen image
			$images = $mapper->find_all();

			if ( empty( $images ) ) {
				return new WP_Error(
					'no_images_found',
					__( 'No images found in NextGen Gallery. Please add images to a gallery first.', 'nggallery' ),
					[ 'status' => 400 ]
				);
			}

			$original_image = null;

			// Look for the first usable image
			foreach ( $images as $image ) {
				// Skip dynamic thumbnails and thumbs
				if ( strpos( $image->filename, '-nggid' ) === false && strpos( $image->filename, 'thumbs-' ) !== 0 ) {
					// Verify the image file actually exists
					$image_path = $storage->get_image_abspath( $image );
					if ( $image_path && file_exists( $image_path ) ) {
						$original_image = $image;
						break;
					}
				}
			}

			if ( ! $original_image ) {
				return new WP_Error(
					'no_usable_images',
					__( 'No usable images found for preview generation.', 'nggallery' ),
					[ 'status' => 400 ]
				);
			}

			// Prepare watermark path for generation
			$final_wm_path     = trim( $watermark_options['wmPath'] ?? '' );
			$downloaded_tmp    = false;
			$tmp_download_path = '';

			if ( 'image' === ( $watermark_options['wmType'] ?? '' ) && ! empty( $final_wm_path ) ) {
				$is_url = is_string( $final_wm_path ) && preg_match( '/^https?:\/\//i', $final_wm_path );
				$is_abs = is_string( $final_wm_path ) && ( 0 === strpos( $final_wm_path, '/' ) || preg_match( '/^[A-Za-z]:[\\\\\/]*/', $final_wm_path ) );
				if ( $is_url ) {
					// Download remote watermark image to uploads folder for preview use
					$upload_dir  = wp_upload_dir();
					$preview_dir = trailingslashit( $upload_dir['basedir'] ) . 'ngg-watermark-previews/';
					if ( ! is_dir( $preview_dir ) ) {
						wp_mkdir_p( $preview_dir );
					}

					// SSRF hardening: validate URL parts and block private/loopback/link-local hosts before fetch.
					$parsed_url = wp_parse_url( $final_wm_path );
					$scheme     = isset( $parsed_url['scheme'] ) ? strtolower( $parsed_url['scheme'] ) : '';
					$host       = isset( $parsed_url['host'] ) ? strtolower( $parsed_url['host'] ) : '';
					if ( ! in_array( $scheme, [ 'http', 'https' ], true ) || '' === $host ) {
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Invalid watermark image URL.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}
					// Reject hostnames/IP literals (incl. bracketed IPv6) that resolve to non-public IPs (loopback, RFC1918, link-local, unique-local, cloud metadata).
					$host_unbracketed = preg_replace( '/^\[|\]$/', '', $host ); // Strip IPv6 brackets for filter_var validation.
					if ( filter_var( $host_unbracketed, FILTER_VALIDATE_IP ) ) {
						// Direct IP literal (v4 or v6) — validate public-range inline; skip DNS to avoid gethostbyname IPv4-only gap.
						if ( ! filter_var(
							$host_unbracketed,
							FILTER_VALIDATE_IP,
							FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
						) ) {
							return new WP_Error(
								'invalid_watermark_image',
								__( 'Watermark URL host is not allowed.', 'nggallery' ),
								[ 'status' => 400 ]
							);
						}
					} else {
						// Hostname — resolve A and AAAA records; reject if any resolved IP is private/reserved.
						$resolved_a = gethostbyname( $host_unbracketed );
						$resolved_a = ( $resolved_a !== $host_unbracketed ) ? $resolved_a : null;

						// gethostbyname() only resolves IPv4 (A records); also check IPv6 (AAAA) to prevent SSRF bypass.
						$aaaa_records  = dns_get_record( $host_unbracketed, DNS_AAAA );
						$resolved_aaaa = ( is_array( $aaaa_records ) && ! empty( $aaaa_records ) && isset( $aaaa_records[0]['ipv6'] ) ) ? $aaaa_records[0]['ipv6'] : null;

						if ( null === $resolved_a && null === $resolved_aaaa ) {
							return new WP_Error(
								'invalid_watermark_image',
								__( 'Unable to resolve watermark URL host.', 'nggallery' ),
								[ 'status' => 400 ]
							);
						}

						foreach ( array_filter( [ $resolved_a, $resolved_aaaa ] ) as $resolved_ip ) {
							if ( ! filter_var(
								$resolved_ip,
								FILTER_VALIDATE_IP,
								FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
							) ) {
								return new WP_Error(
									'invalid_watermark_image',
									__( 'Watermark URL host is not allowed.', 'nggallery' ),
									[ 'status' => 400 ]
								);
							}
						}
					}

					// Force safe image extension rather than trusting attacker-controlled URL path extension (RCE hardening).
					$url_ext      = strtolower( (string) pathinfo( wp_parse_url( $final_wm_path, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
					$allowed_exts = [ 'png', 'jpg', 'jpeg', 'gif', 'webp' ];
					$ext          = in_array( $url_ext, $allowed_exts, true ) ? $url_ext : 'png';
					$tmp_name     = 'wm_' . uniqid( '', true ) . '.' . $ext;
					$tmp_path     = $preview_dir . $tmp_name;

					$response = wp_safe_remote_get( $final_wm_path, [ 'timeout' => 15 ] ); // Use wp_safe_remote_get to honor HTTP request safe-host filters.
					if ( is_wp_error( $response ) || (int) wp_remote_retrieve_response_code( $response ) !== 200 ) {
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Unable to fetch watermark image from the provided URL.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}
					$body = wp_remote_retrieve_body( $response );
					if ( empty( $body ) ) {
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Empty response when fetching watermark image URL.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}

					// Write file to disk
					file_put_contents( $tmp_path, $body ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
					if ( ! file_exists( $tmp_path ) ) {
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Failed to store downloaded watermark image.', 'nggallery' ),
							[ 'status' => 500 ]
						);
					}

					// Verify downloaded bytes are an actual image; prevents storing PHP/HTML payload under ngg-watermark-previews/.
					$image_info = @getimagesize( $tmp_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					if ( false === $image_info ) {
						@unlink( $tmp_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Downloaded file is not a valid image.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}

					// Use the absolute filesystem path directly — downstream ngglegacy_thumbnail checks is_file() first, so absolute paths work without conversion. str_replace(ABSPATH) breaks when uploads dir lives outside ABSPATH.
					$final_wm_path     = $tmp_path;
					$downloaded_tmp    = true;
					$tmp_download_path = $tmp_path;
				} elseif ( $is_abs ) {
					// Path-traversal hardening: resolve symlinks/.. segments and constrain absolute paths to ABSPATH or the uploads dir only.
					$real_wm_path  = realpath( $final_wm_path ); // realpath normalizes ../ and resolves symlinks before allowlist check.
					$upload_dir    = wp_upload_dir();
					$real_abspath  = realpath( ABSPATH ); // Normalize ABSPATH too so string prefix match is apples-to-apples.
					$real_uploads  = isset( $upload_dir['basedir'] ) ? realpath( $upload_dir['basedir'] ) : false; // Uploads dir may live outside ABSPATH on some installs.
					$under_abspath = ( $real_wm_path && $real_abspath && 0 === strpos( trailingslashit( $real_wm_path ), trailingslashit( $real_abspath ) ) ); // trailingslashit prevents partial dir-name match (e.g. /var/www/html_secret matching /var/www/html).
					$under_uploads = ( $real_wm_path && $real_uploads && 0 === strpos( trailingslashit( $real_wm_path ), trailingslashit( $real_uploads ) ) ); // Allow uploads dir when outside ABSPATH.
					if ( ! $real_wm_path || ( ! $under_abspath && ! $under_uploads ) ) {
						// Reject arbitrary filesystem reads (e.g. /etc/passwd) via watermark path — prior code copied any readable file into a public previews dir.
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Watermark image path is not allowed.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}
					// Extension allowlist: prevents non-image files (e.g. .php) from being referenced as watermark source.
					$abs_ext      = strtolower( (string) pathinfo( $real_wm_path, PATHINFO_EXTENSION ) );
					$allowed_exts = [ 'png', 'jpg', 'jpeg', 'gif', 'webp' ];
					if ( ! in_array( $abs_ext, $allowed_exts, true ) ) {
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Watermark image must be png, jpg, jpeg, gif, or webp.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}
					// Verify file is a real image before downstream uses it, not just extension match.
					if ( false === @getimagesize( $real_wm_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						return new WP_Error(
							'invalid_watermark_image',
							__( 'Watermark file is not a valid image.', 'nggallery' ),
							[ 'status' => 400 ]
						);
					}
					if ( $real_abspath && 0 === strpos( trailingslashit( $real_wm_path ), trailingslashit( $real_abspath ) ) ) {
						// Under ABSPATH: convert to web-root-relative form expected by downstream renderer.
						$final_wm_path = ltrim( str_replace( $real_abspath, '/', $real_wm_path ), '/' );
					} else {
						// Under uploads (outside ABSPATH): copy into previews dir so downstream code can serve from a known location.
						$preview_dir = trailingslashit( $upload_dir['basedir'] ) . 'ngg-watermark-previews/';
						if ( ! is_dir( $preview_dir ) ) {
							wp_mkdir_p( $preview_dir );
						}
						$tmp_name = 'wm_' . uniqid( '', true ) . '.' . $abs_ext; // Use validated extension, not attacker-controlled.
						$tmp_path = $preview_dir . $tmp_name;
						// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.copy_copy
						if ( ! @copy( $real_wm_path, $tmp_path ) ) { // Source already constrained to uploads dir above.
							return new WP_Error(
								'invalid_watermark_image',
								__( 'Failed to copy watermark image for preview.', 'nggallery' ),
								[ 'status' => 500 ]
							);
						}
						// Use absolute path directly — downstream is_file() check handles it; str_replace(ABSPATH) breaks when uploads is outside ABSPATH.
						$final_wm_path     = $tmp_path;
						$downloaded_tmp    = true;
						$tmp_download_path = $tmp_path;
					}
				}
			}

			// Prepare size parameters for watermarked preview
			$size_params = [
				'width'     => 250,
				'height'    => 250,
				'quality'   => 100,
				'watermark' => 1,
				'wmType'    => sanitize_text_field( $watermark_options['wmType'] ?? 'text' ),
				'wmText'    => sanitize_text_field( $watermark_options['wmText'] ?? 'Preview' ),
				'wmPos'     => sanitize_text_field( $watermark_options['wmPos'] ?? 'midCenter' ),
				'wmXpos'    => intval( $watermark_options['wmXpos'] ?? 0 ),
				'wmYpos'    => intval( $watermark_options['wmYpos'] ?? 0 ),
				'wmPath'    => sanitize_text_field( $final_wm_path ),
				'wmFont'    => sanitize_text_field( $watermark_options['wmFont'] ?? 'arial.ttf' ),
				'wmSize'    => intval( $watermark_options['wmSize'] ?? 30 ),
				'wmColor'   => sanitize_text_field( $watermark_options['wmColor'] ?? 'ffffff' ),
				'wmOpaque'  => intval( $watermark_options['wmOpaque'] ?? 33 ),
			];

			// Generate a unique size name for this watermark preview
			$imagegen  = DynamicThumbnailsManager::get_instance();
			$size_name = $imagegen->get_size_name( $size_params );

			// Generate the watermarked image using NextGen's system
			$result = $storage->generate_image_size( $original_image, $size_name, $size_params );

			if ( $result ) {
				// Get the URL for the generated watermarked image
				$preview_url = $storage->get_image_url( $original_image, $size_name );

				if ( $preview_url ) {
					// Add timestamp to prevent caching
					$preview_url_with_timestamp = $preview_url . '?' . time();
					// Cleanup temporary download, if any
					if ( ! empty( $downloaded_tmp ) && $tmp_download_path && file_exists( $tmp_download_path ) ) {
						@unlink( $tmp_download_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
					}

					return new WP_REST_Response(
						[
							'success'       => true,
							'thumbnail_url' => $preview_url_with_timestamp,
						],
						200
					);
				}
			}

			// If we get here, generation failed
			return new WP_Error(
				'generation_failed',
				__( 'Could not generate watermarked preview. Please check that watermark settings are valid.', 'nggallery' ),
				[ 'status' => 500 ]
			);

		} catch ( \Exception $e ) {
			// Cleanup on error as well
			if ( ! empty( $downloaded_tmp ) && $tmp_download_path && file_exists( $tmp_download_path ) ) {
				@unlink( $tmp_download_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
			}
			return new WP_Error(
				'preview_error',
				// translators: %s is the error message
				sprintf( __( 'An error occurred while generating the watermark preview: %s', 'nggallery' ), $e->getMessage() ),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Reset settings to defaults
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function reset_settings( WP_REST_Request $request ) {
		$confirm       = $request->get_param( 'confirm' );
		$settings_type = $request->get_param( 'settings_type' ) ?? 'all';

		// Double-check the confirmation
		if ( ! $confirm ) {
			return new WP_Error(
				'reset_not_confirmed',
				__( 'Settings reset must be confirmed by setting "confirm" to true.', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		try {
			global $wpdb;

			switch ( $settings_type ) {
				case 'all':
					// Complete reset - same as old admin reset_action
					self::perform_complete_reset( $wpdb );
					$message = __( 'All settings have been reset to default values.', 'nggallery' );
					break;

				case 'non_ecommerce':
					// Reset non-ecommerce settings - same as old admin reset_non_ecommerce_settings_action
					self::perform_non_ecommerce_reset( $wpdb );
					$message = __( 'All non-ecommerce settings have been reset to default values.', 'nggallery' );
					break;

				case 'ecommerce':
					// Reset only ecommerce settings - same as old admin reset_ecommerce_settings_action
					self::perform_ecommerce_reset();
					$message = __( 'Ecommerce settings have been reset to default values.', 'nggallery' );
					break;

				default:
					return new WP_Error(
						'invalid_settings_type',
						__( 'Invalid settings type. Must be "all", "non_ecommerce", or "ecommerce".', 'nggallery' ),
						[ 'status' => 400 ]
					);
			}

			return new WP_REST_Response(
				[
					'success'       => true,
					'message'       => $message,
					'settings_type' => $settings_type,
				],
				200
			);

		} catch ( \Exception $e ) {
			return new WP_Error(
				'reset_failed',
				// translators: %s is the error message
				sprintf( __( 'Failed to reset settings: %s', 'nggallery' ), $e->getMessage() ),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Perform complete reset - same as old admin reset_action
	 *
	 * @param \wpdb $wpdb WordPress database object.
	 * @throws \Exception If reset fails.
	 */
	private static function perform_complete_reset( $wpdb ) {
		// Flush the cache
		\Imagely\NGG\Util\Transient::flush();

		// Uninstall the plugins in the correct order
		$settings = \Imagely\NGG\Settings\Settings::get_instance();

		if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) || defined( 'NEXTGEN_GALLERY_PRO_VERSION' ) ) {
			\Imagely\NGG\Util\Installer::uninstall( 'photocrati-nextgen-pro' );
		}

		if ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) ) {
			\Imagely\NGG\Util\Installer::uninstall( 'photocrati-nextgen-plus' );
		}

		\Imagely\NGG\Util\Installer::uninstall( 'photocrati-nextgen' );

		// Remove all ngg_options entry in wp_options
		$settings->reset();
		$settings->destroy();

		// Remove duplicate display types and lightboxes from database
		// (fixes issues from 1.9x to 2.0x upgrades)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'display_type' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'lightbox_library' ) );

		// Trigger reinstallation by making a request to plugins.php
		wp_remote_get(
			admin_url( 'plugins.php' ),
			[
				'timeout'   => 180,
				'blocking'  => true,
				'sslverify' => false,
			]
		);
	}

	/**
	 * Perform non-ecommerce reset - same as old admin reset_non_ecommerce_settings_action
	 *
	 * @param \wpdb $wpdb WordPress database object.
	 * @throws \Exception If reset fails.
	 */
	private static function perform_non_ecommerce_reset( $wpdb ) {
		// Flush the cache
		\Imagely\NGG\Util\Transient::flush();

		// Get Pro installers and identify ecommerce ones
		$installers       = apply_filters( 'ngg_pro_settings_reset_installers', [] );
		$ecomm_installers = [];

		foreach ( $installers as $installer_classname ) {
			if ( class_exists( $installer_classname ) ) {
				$installer = new $installer_classname();
				$actions   = $installer->get_groups();
				if ( in_array( 'ecommerce', $actions, true ) ) {
					$ecomm_installers[] = $installer;
				}
			}
		}

		// Load current ecommerce settings to preserve them
		foreach ( $ecomm_installers as $installer ) {
			$installer->load_current_settings();
		}

		// Wipe out all settings (same as complete reset)
		if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) || defined( 'NEXTGEN_GALLERY_PRO_VERSION' ) ) {
			\Imagely\NGG\Util\Installer::uninstall( 'photocrati-nextgen-pro' );
		}

		if ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) ) {
			\Imagely\NGG\Util\Installer::uninstall( 'photocrati-nextgen-plus' );
		}

		\Imagely\NGG\Util\Installer::uninstall( 'photocrati-nextgen' );

		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		$settings->reset();
		$settings->destroy();

		// Remove duplicate display types and lightboxes from database
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'display_type' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'lightbox_library' ) );

		// Trigger reinstallation
		wp_remote_get(
			admin_url( 'plugins.php' ),
			[
				'timeout'   => 180,
				'blocking'  => true,
				'sslverify' => false,
			]
		);

		// Reload settings and restore ecommerce settings
		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		$settings->load();

		foreach ( $ecomm_installers as $installer ) {
			$installer->set_current_settings();
		}

		$settings->save();
	}

	/**
	 * Perform ecommerce-only reset - same as old admin reset_ecommerce_settings_action
	 *
	 * @throws \Exception If reset fails.
	 */
	private static function perform_ecommerce_reset() {
		// Flush the cache
		\Imagely\NGG\Util\Transient::flush();

		// Get Pro installers and reset only ecommerce ones
		$installers = apply_filters( 'ngg_pro_settings_reset_installers', [] );

		foreach ( $installers as $installer_classname ) {
			if ( class_exists( $installer_classname ) ) {
				$installer = new $installer_classname();
				$actions   = $installer->get_groups();
				if ( in_array( 'ecommerce', $actions, true ) ) {
					$installer->reset();
				}
			}
		}
	}

	/**
	 * Clear all cache
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function clear_cache() {
		try {
			$storage_manager = StorageManager::get_instance();
			$gallery_mapper  = GalleryMapper::get_instance();

			// Get all galleries
			$galleries = $gallery_mapper->find_all();

			// Clear cache for each gallery
			foreach ( $galleries as $gallery ) {
				$storage_manager->flush_cache( $gallery );
			}

			// Clear WordPress transients
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ngg_%'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_ngg_%'" );

			// Clear NextGen specific transients
			\Imagely\NGG\Util\Transient::flush();

			return new WP_REST_Response(
				[
					'success' => true,
					'message' => __( 'Cache cleared successfully', 'nggallery' ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'cache_clear_failed',
				// translators: %s is the error message
				sprintf( __( 'Failed to clear cache: %s', 'nggallery' ), $e->getMessage() ),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Regenerate all thumbnails
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function regenerate_thumbnails() {
		try {
			$image_mapper    = ImageMapper::get_instance();
			$storage_manager = StorageManager::get_instance();

			// Get all images
			$images = $image_mapper->find_all();

			if ( empty( $images ) ) {
				return new WP_REST_Response(
					[
						'success' => true,
						'message' => __( 'No images found to regenerate', 'nggallery' ),
						'count'   => 0,
					],
					200
				);
			}

			$success_count = 0;
			$error_count   = 0;

			// Regenerate thumbnails for each image
			foreach ( $images as $image ) {
				try {
					$result = $storage_manager->generate_thumbnail( $image );
					if ( $result ) {
						++$success_count;
					} else {
						++$error_count;
					}
				} catch ( \Exception $e ) {
					++$error_count;
					continue;
				}
			}

			$total_count = count( $images );

			if ( $error_count > 0 ) {
				return new WP_REST_Response(
					[
						'success'       => true,
						'message'       => sprintf(
							// translators: %1$d is success count, %2$d is total count, %3$d is error count
							__( 'Thumbnail regeneration completed: %1$d of %2$d images processed successfully, %3$d errors', 'nggallery' ),
							$success_count,
							$total_count,
							$error_count
						),
						'total_count'   => $total_count,
						'success_count' => $success_count,
						'error_count'   => $error_count,
					],
					200
				);
			}

			return new WP_REST_Response(
				[
					'success'       => true,
					'message'       => sprintf(
						// translators: %d is the number of thumbnails regenerated
						__( 'All %d thumbnails regenerated successfully', 'nggallery' ),
						$success_count
					),
					'total_count'   => $total_count,
					'success_count' => $success_count,
					'error_count'   => 0,
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'thumbnail_regeneration_failed',
				// translators: %s is the error message
				sprintf( __( 'Failed to regenerate thumbnails: %s', 'nggallery' ), $e->getMessage() ),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Check if gtag.js is actually loaded on the site frontend by fetching the home page HTML.
	 * This is the only reliable way to confirm gtag is present — a plugin being active does not
	 * guarantee it outputs gtag.js (it must also be configured with a Measurement ID).
	 *
	 * Pass ?force=true to skip the transient cache and immediately re-check (used by the
	 * "Refresh Status" button in the admin UI).
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return WP_REST_Response { detected: bool }
	 */
	public static function get_gtag_status( \WP_REST_Request $request ) {
		$transient_key = 'ngg_gtag_status_detected';
		$force         = (bool) $request->get_param( 'force' );

		if ( $force ) {
			delete_transient( $transient_key );
		} else {
			$cached = get_transient( $transient_key );
			if ( false !== $cached ) {
				return new WP_REST_Response( [ 'detected' => (bool) $cached ], 200 );
			}
		}

		$home_url = home_url( '/' );
		$args     = [
			// 5s is safe here because cookies=>[] strips the admin session, preventing PHP-FPM self-deadlocks.
			'timeout'    => 5,
			'cookies'    => [],
			'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
		];

		// Allow self-signed certs for local/dev environments (Docker with HTTPS, etc.).
		$host = wp_parse_url( $home_url, PHP_URL_HOST );
		if ( $host && ( 'localhost' === $host || '127.0.0.1' === $host || 'host.docker.internal' === $host || false !== strpos( (string) $host, '.localhost' ) ) ) {
			$args['sslverify'] = false;
		}

		$response = wp_remote_get( $home_url, $args );

		if ( is_wp_error( $response ) ) {
			// Cache the failure briefly so repeated admin page loads don't each trigger a fresh timeout.
			set_transient( $transient_key, '0', 2 * MINUTE_IN_SECONDS );
			return new WP_REST_Response( [ 'detected' => false ], 200 );
		}

		$body = wp_remote_retrieve_body( $response );
		$code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== (int) $code || empty( $body ) ) {
			// Same short-TTL cache for non-200 responses (e.g. maintenance mode, redirects).
			set_transient( $transient_key, '0', 2 * MINUTE_IN_SECONDS );
			return new WP_REST_Response( [ 'detected' => false ], 200 );
		}

		// Match any of the patterns that confirm gtag.js is actively loaded and configured.
		$detected = (
			false !== strpos( $body, 'googletagmanager.com/gtag/js' ) || // gtag.js script src
			false !== strpos( $body, 'gtag("config"' ) || // double-quote variant
			false !== strpos( $body, "gtag('config'" ) || // single-quote variant
			false !== strpos( $body, 'gtag("config"' ) || // escaped double-quote
			false !== strpos( $body, 'gtag/js?id=G-' )                    // GA4 Measurement ID in src
		);

		// Cache for 15 minutes — gtag.js installation status changes infrequently.
		set_transient( $transient_key, $detected ? '1' : '0', 15 * MINUTE_IN_SECONDS );

		return new WP_REST_Response( [ 'detected' => $detected ], 200 );
	}

	/**
	 * Get system information
	 *
	 * @return WP_REST_Response System information
	 */
	public static function get_system_info() {
		// Use the existing method from Admin\App class
		if ( class_exists( '\Imagely\NGG\Admin\App' ) ) {
			$system_info = \Imagely\NGG\Admin\App::get_system_info();
			return new WP_REST_Response( $system_info, 200 );
		}

		return new WP_Error(
			'system_info_unavailable',
			__( 'System information is not available', 'nggallery' ),
			[ 'status' => 500 ]
		);
	}
}
