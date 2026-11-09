<?php

namespace TEC\Common\StellarWP\Uplink\API;

use stdClass;
use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Messages;
use TEC\Common\StellarWP\Uplink\Resources\Resource;

class Validation_Response {
	/**
	 * Validation response message.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $api_response_message;

	/**
	 * ContainerInterface instance.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Current resource key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $current_key;

	/**
	 * Daily limit from the validation.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $daily_limit;

	/**
	 * Expiration from the validation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $expiration;

	/**
	 * Is response valid.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $is_valid = true;

	/**
	 * License key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Replacement key.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $replacement_key;

	/**
	 * Resource instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Resource
	 */
	protected $resource;

	/**
	 * Validation response.
	 *
	 * @since 1.0.0
	 *
	 * @var stdClass
	 */
	protected $response;

	/**
	 * Result of the validation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $result = 'success';

	/**
	 * Validation type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $validation_type;

	/**
	 * Version from validation response.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null             $key             License key.
	 * @param string                  $validation_type Validation type (local or network).
	 * @param stdClass|null           $response        Validation response.
	 * @param Resource                $resource        Resource instance.
	 * @param ContainerInterface|null $container       Container instance.
	 */
	public function __construct( $key, string $validation_type, $response, Resource $resource, $container = null ) {
		$this->key             = $key ?: '';
		$this->validation_type = 'network' === $validation_type ? 'network' : 'local';
		$this->response        = $response;

		if ( isset( $this->response->results ) ) {
			$this->response = is_array( $this->response->results ) ? reset( $this->response->results ) : $this->response->results;
		}

		$this->resource        = $resource;
		$this->container       = $container ?: Config::get_container();

		$this->parse();
	}

	/**
	 * Gets the daily limit from the validation response.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_daily_limit() : int {
		return $this->daily_limit;
	}

	/**
	 * Gets the validation response key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_key() : string {
		return ! empty( $this->replacement_key ) ? $this->replacement_key : $this->key;
	}

	/**
	 * Gets the message from the validation response.
	 *
	 * @since 1.0.0
	 *
	 * @return Messages\Message_Abstract
	 */
	public function get_message() {
		switch ( $this->result ) {
			case 'unreachable':
				$message = new Messages\Unreachable();
				break;
			case 'expired':
				$message = new Messages\Expired_Key();
				break;
			case 'invalid':
			case 'upgrade':
				$message = new Messages\API( $this->api_response_message, $this->version, $this->resource );
				break;
			case 'success':
			case 'new':
				$message = $this->get_success_message();
				break;
			default:
				$message = new Messages\Update_Available( $this->resource );
		}

		return $message;
	}

	/**
	 * Gets the network level message from the validation response.
	 *
	 * @since 1.0.0
	 *
	 * @return Messages\Message_Abstract
	 */
	public function get_network_message() {
		if ( $this->is_valid() ) {
			return new Messages\Network_Licensed();
		}

		if ( 'expired' === $this->result ) {
			return new Messages\Network_Expired();
		}

		return new Messages\Network_Unlicensed();
	}

	/**
	 * Gets the raw response from the validation request.
	 *
	 * @since 1.0.0
	 *
	 * @return stdClass
	 */
	public function get_raw_response() {
		return $this->response;
	}

	/**
	 * Gets the validation response result.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_result() : string {
		return $this->result;
	}

	/**
	 * Gets the success message of the validation response.
	 *
	 * @since 1.0.0
	 *
	 * @return Messages\Message_Abstract
	 */
	private function get_success_message() {
		if ( ! empty( $this->api_response_message ) ) {
			return new Messages\API( $this->api_response_message, $this->version, $this->resource );
		}

		return new Messages\Valid_Key( $this->expiration );
	}

	/**
	 * Get update details from the validation response.
	 *
	 * @since 1.0.0
	 *
	 * @return stdClass
	 */
	public function get_update_details() {
		$update = new stdClass;

		if ( ! empty( $this->response->api_invalid ) ) {
			return $this->handle_api_errors();
		}

		$id     = $this->response->id ?? '';
		$plugin = $this->response->plugin ?? '';
		$slug   = $this->response->slug ?? '';

		if ( empty( $id ) ) {
			$id = sprintf(
				'stellarwp/plugins/%s',
				empty( $slug ) ? $this->resource->get_slug() : $slug
			);
		}

		if ( empty( $plugin ) ) {
			$plugin = $this->resource->get_path();
		}

		$update->id          = $id;
		$update->plugin      = $plugin;
		$update->slug        = $slug;
		$update->new_version = $this->response->version ?? '';
		$update->url         = $this->response->homepage ?? '';
		$update->tested      = $this->response->tested ?? '';
		$update->requires    = $this->response->requires ?? '';
		$update->package     = $this->response->download_url ? $this->response->download_url . '&key=' . urlencode( $this->get_key() ) : '';

		if ( ! empty( $this->response->upgrade_notice ) ) {
			$update->upgrade_notice = $this->response->upgrade_notice;
		}

		// Support custom $update properties coming straight from PUE
		if ( ! empty( $this->response->custom_update ) ) {
			$custom_update = get_object_vars( $this->response->custom_update );

			foreach ( $custom_update as $field => $custom_value ) {
				if ( is_object( $custom_value ) ) {
					$custom_value = get_object_vars( $custom_value );
				}

				$update->$field = $custom_value;
			}
		}

		return $update;
	}

	/**
	 * @return stdClass
	 */
	public function handle_api_errors() : stdClass {
		$update      = new stdClass;
		$copy_fields = [
			'id',
			'slug',
			'version',
			'homepage',
			'download_url',
			'upgrade_notice',
			'sections',
			'plugin',
			'api_expired',
			'api_upgrade',
			'api_invalid',
			'api_invalid_message',
			'api_inline_invalid_message',
			'custom_update',
		];

		foreach ( $copy_fields as $field ) {
			if ( ! isset( $this->response->$field ) ) {
				continue;
			}

			$update->$field = $this->response->$field;
		}

		$update->license_error = $this->get_message()->get();
		$update->slug          = $this->resource->get_slug();
		$update->new_version   = $this->response->version ?? '';
		$update->package       = 'invalid_license';

		return $update;
	}

	/**
	 * Get expiration details from response
	 *
	 * @return stdClass
	 */
	public function get_expire_details() : stdClass {
		$update = new stdClass;

		$update->version        = $this->response->version ?: '';
		$update->message        = $this->response->api_invalid_message ?: '';
		$update->inline_message = $this->response->api_inline_invalid_message ?: '';
		$update->api_expired    = $this->response->api_expired ?: '';
		$update->sections       = $this->response->sections ?: new stdClass;

		return $update;
	}

	/**
	 * Gets the version from the validation response.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_version() : string {
		return $this->version ?: '';
	}

	/**
	 * Returns where or not the response has a replacement key.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_replacement_key() : bool {
		return ! empty( $this->replacement_key );
	}

	/**
	 * Returns where or not the license key was valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() : bool {
		return $this->is_valid;
	}

	/**
	 * Set the is_valid value.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $is_valid Whether the validation response should be set as valid or not.
	 *
	 * @return void
	 */
	public function set_is_valid( bool $is_valid ) {
		$this->is_valid = $is_valid;
	}

	/**
	 * Parses the response from the API.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function parse() {
		$this->current_key = $this->resource->get_license_key( $this->validation_type );
		$this->expiration  = isset( $this->response->expiration ) ? $this->response->expiration : __( 'unknown date', 'tribe-common' );

		if ( ! empty( $this->response->api_inline_invalid_message ) ) {
			$this->api_response_message = wp_kses( $this->response->api_inline_invalid_message, 'post' );
		}

		if ( ! empty( $this->response->home_url ) ) {
			$this->resource->set_home_url( $this->response->home_url );
		}

		if ( ! empty( $this->response->version ) ) {
			$this->version = sanitize_text_field( $this->response->version );
		}

		$this->version = $this->version ?: $this->resource->get_version();

		if ( null === $this->response ) {
			$this->result = 'unreachable';
		} elseif ( isset( $this->response->api_expired ) && 1 === (int) $this->response->api_expired ) {
			$this->result = 'expired';
			$this->set_is_valid( false );
		} elseif ( isset( $this->response->api_upgrade ) && 1 === (int) $this->response->api_upgrade ) {
			$this->result = 'upgrade';
			$this->set_is_valid( false );
		} elseif ( isset( $this->response->api_invalid ) && 1 === (int) $this->response->api_invalid ) {
			$this->result = 'invalid';
			$this->set_is_valid( false );
		} else {
			if ( isset( $this->response->api_message ) ) {
				$this->api_response_message = wp_kses( $this->response->api_message, 'data' );
			}

			if ( isset( $this->response->daily_limit ) ) {
				$this->daily_limit = intval( $this->response->daily_limit );
			}

			// If the license key is new or not the same as the one we have, mark it as a new key.
			if ( ! ( $this->current_key && $this->current_key === $this->key ) ) {
				$this->result = 'new';
			}
		}

		if ( ! empty( $this->response->replacement_key ) ) {
			$this->replacement_key = $this->response->replacement_key;
		}
	}

	/**
	 * Transform plugin info into the format used by the native WordPress.org API
	 *
	 * @return object
	 */
	public function to_wp_format() {
		$info = new StdClass;

		// The custom update API is built so that many fields have the same name and format
		// as those returned by the native WordPress.org API. These can be assigned directly.
		$same_format = [
			'name',
			'slug',
			'version',
			'requires',
			'tested',
			'rating',
			'upgrade_notice',
			'num_ratings',
			'downloaded',
			'homepage',
			'last_updated',
			'api_expired',
			'api_upgrade',
			'api_invalid',
		];

		foreach ( $same_format as $field ) {
			if ( isset( $this->$field ) ) {
				$info->$field = $this->$field;
			} else {
				$info->$field = null;
			}
		}

		//Other fields need to be renamed and/or transformed.
		$info->download_link = isset( $this->response->download_url ) ? $this->response->download_url : '';

		if ( ! empty( $this->author_homepage ) && ! empty( $this->response->author ) ) {
			$info->author = sprintf( '<a href="%s">%s</a>', esc_url( $this->author_homepage ), $this->response->author );
		} else {
			$info->author = $this->response->author ?? '';
		}

		if ( isset( $this->response->sections ) && is_object( $this->response->sections ) ) {
			$info->sections = get_object_vars( $this->response->sections );
		} elseif ( isset( $this->response->sections ) && is_array( $this->response->sections ) ) {
			$info->sections = $this->response->sections;
		} else {
			$info->sections = [ 'description' => '' ];
		}
		return $info;
	}

	/**
	 * Magic getter for the response properties.
	 *
	 * @param string $key Response value to fetch.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( ! isset( $this->response->$key ) ) {
			return null;
		}

		return $this->response->$key;
	}

	/**
	 * Magic isset for the response properties.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->response->$key );
	}
}
