<?php

namespace Gravity_Forms\Gravity_Forms\License;

use Gravity_Forms\Gravity_Forms\External_API\GF_API_Response;
use Gravity_Forms\Gravity_Forms\Transients\GF_Transient_Strategy;

/**
 * Class GF_License_API_Response
 *
 * Concrete Response class for the GF License API.
 *
 * @since 2.5
 *
 * @package Gravity_Forms\Gravity_Forms\License
 */
class GF_License_API_Response extends GF_API_Response {

	/**
	 * @var GF_Transient_Strategy
	 */
	private $transient_strategy;

	/**
	 * GF_License_API_Response constructor.
	 *
	 * @param mixed $data The data from the API connector.
	 * @param bool $validate Whether to validate the data passed.
	 * @param GF_Transient_Strategy $transient_strategy The Transient Strategy used to store things in transients.
	 */
	public function __construct( $data, $validate, GF_Transient_Strategy $transient_strategy ) {
		$this->transient_strategy = $transient_strategy;

		// Data is a wp_error, parse it to get the correct code and message.
		if ( is_wp_error( $data ) ) {
			/**
			 * @var \WP_Error $data
			 */
			$this->set_status( $data->get_error_code() );
			$this->add_error( $data->get_error_message() );

			if ( empty( $data->get_error_data() ) ) {
				return;
			}

			$error_data = $data->get_error_data();

			if ( rgar( $error_data, 'license' ) ) {
				$error_data = rgar( $error_data, 'license' );
			}

			$this->add_data_item( $error_data );

			return;
		}

		// Data is somehow broken; set a status for Invalid license keys and bail.
		if ( ! is_array( $data ) ) {
			$this->set_status( GF_License_Statuses::INVALID_LICENSE_KEY );
			$this->add_error( GF_License_Statuses::get_message_for_code( GF_License_Statuses::INVALID_LICENSE_KEY ) );

			return;
		}

		// Set is_valid to true since we are bypassing validation.
		if ( ! $validate ) {
			$data['is_valid'] = true;
		}

		// Data is formatted properly, but the `is_valid` param is false. Return an invalid license key error.
		if ( isset( $data['is_valid'] ) && ! $data['is_valid'] ) {
			$this->set_status( GF_License_Statuses::INVALID_LICENSE_KEY );
			$this->add_error( GF_License_Statuses::get_message_for_code( GF_License_Statuses::INVALID_LICENSE_KEY ) );

			return;
		}

		// Finally, the data is correct, so store it and set our status to valid.
		$this->add_data_item( $data );
		$this->set_status( GF_License_Statuses::VALID_KEY );
	}

	/**
	 * Get the stored error for this site license.
	 *
	 * @return \WP_Error|false
	 */
	private function get_stored_error() {
		return $this->transient_strategy->get( 'rg_gforms_registration_error' );
	}

	/**
	 * Whether this license key is valid.
	 *
	 * @return bool
	 */
	public function is_valid() {
		if ( empty( $this->data ) || $this->get_status() === GF_License_Statuses::NO_DATA ) {
			return false;
		}

		if ( ! $this->has_errors() ) {
			return (bool) $this->get_data_value( 'is_valid' );
		}

		return $this->get_status() !== GF_License_Statuses::INVALID_LICENSE_KEY;
	}

	/**
	 * Get the error message for the response, either the first one by default, or at a specific index.
	 *
	 * @param int $index The array index to use if mulitple errors exist.
	 *
	 * @return mixed|string
	 */
	public function get_error_message( $index = 0 ) {
		if ( ! $this->has_errors() ) {
			return '';
		}

		return $this->errors[ $index ];
	}

	/**
	 * Get the human-readable display status for the response.
	 *
	 * @return string|void
	 */
	public function get_display_status() {
		switch ( $this->get_status() ) {
			case GF_License_Statuses::INVALID_LICENSE_KEY:
				return __( 'Invalid', 'gravityforms' );
			case GF_License_Statuses::EXPIRED_LICENSE_KEY:
				return __( 'Expired', 'gravityforms' );
			case GF_License_Statuses::MAX_SITES_EXCEEDED:
				return __( 'Sites Exceeded', 'gravityforms' );
			case GF_License_Statuses::VALID_KEY:
			default:
				return __( 'Active', 'gravityforms' );
		}
	}

	/**
	 * Licenses can be valid and usable, technically-invalid but still usable, or invalid and unusable.
	 * This will return the correct usability value for this license key.
	 *
	 * @return string
	 */
	public function get_usability() {
		if ( $this->get_status() === GF_License_Statuses::VALID_KEY || $this->get_status() === GF_License_Statuses::NO_DATA ) {
			return GF_License_Statuses::USABILITY_VALID;
		}

		if ( $this->get_status() === GF_License_Statuses::INVALID_LICENSE_KEY || $this->get_status() === GF_License_Statuses::SITE_REVOKED ) {
			return GF_License_Statuses::USABILITY_NOT_ALLOWED;
		}

		return GF_License_Statuses::USABILITY_ALLOWED;
	}

	//----------------------------------------
	//---------- Helpers/Utils ---------------
	//----------------------------------------

	/**
	 * Whether this response has any errors stored as a transient.
	 *
	 * @return bool
	 */
	private function has_stored_error() {
		return (bool) $this->get_stored_error();
	}

	/**
	 * Get a properly-formatted link to the Upgrade page for this license key.
	 *
	 * @return string
	 */
	private function get_upgrade_link() {
		$key  = $this->get_data_value( 'license_key_md5' );
		$type = $this->get_data_value( 'product_code' );

		return sprintf( 'https://www.gravityforms.com/my-account/licenses/?action=upgrade&license_key=%s&license_code=%s&utm_source=gf-admin&utm_medium=upgrade-button&utm_campaign=license-enforcement', $key, $type );
	}

	/**
	 * Get the CTA information for this license key, if applicable.
	 *
	 * @return mixed
	 */
	public function get_cta() {
		switch ( $this->get_status() ) {
			case GF_License_Statuses::EXPIRED_LICENSE_KEY:
				return array(
					'label' => __( 'Manage', 'gravityforms' ),
					'link'  => 'https://www.gravityforms.com/my-account/licenses/?utm_source=gf-admin&utm_medium=manage-button&utm_campaign=license-enforcement',
					'class' => 'cog',
				);
			case GF_License_Statuses::MAX_SITES_EXCEEDED:
				return array(
					'label' => __( 'Upgrade', 'gravityforms' ),
					'link'  => $this->get_upgrade_link(),
					'class' => 'product',
				);
			default:
				return $this->get_data_value( 'days_to_expire' );
		}
	}

	/**
	 * Some statuses are invalid, but get treated as usable. This determines if they should be displayed as
	 * though they are valid.
	 *
	 * @return bool
	 */
	public function display_as_valid() {
		switch ( $this->get_status() ) {
			case GF_License_Statuses::INVALID_LICENSE_KEY:
			case GF_License_Statuses::EXPIRED_LICENSE_KEY:
			case GF_License_Statuses::MAX_SITES_EXCEEDED:
				return false;
			case GF_License_Statuses::VALID_KEY:
			default:
				return true;
		}
	}

	/**
	 * Whether the license key can be used.
	 *
	 * @return bool
	 */
	public function can_be_used() {
		return $this->get_usability() !== GF_License_Statuses::USABILITY_NOT_ALLOWED;
	}

	/**
	 * Get the CTA type to display.
	 *
	 * @return string
	 */
	public function cta_type() {
		if ( is_array( $this->get_cta() ) ) {
			return 'button';
		}

		return 'text';
	}

	/**
	 * Determine if the contained License Key has an expiration date.
	 *
	 * @return bool
	 */
	public function has_expiration() {
		$expiration = $this->get_data_value( 'date_expires' );

		if ( empty( $expiration ) ) {
			return false;
		}

		$y = (int) gmdate( 'Y', strtotime( $expiration ) );

		// 2038 is the latest timestamp we ever assign to a license; if it's present, this key doesn't expire.
		return $y < 2038;
	}

	public function renewal_text() {
		if (  $this->get_status() === GF_License_Statuses::EXPIRED_LICENSE_KEY ) {
			return __( 'Expired On', 'gravityforms' );
		}

		$cancelled = $this->get_data_value( 'is_subscription_canceled' );

		if ( ! $this->has_expiration() || $cancelled ) {
			return __( 'Expires On', 'gravityforms' );
		}

		return __( 'Renews On', 'gravityforms' );
	}

	/**
	 * Whether the license has max seats exceeded.
	 *
	 * @return bool
	 */
	public function max_seats_exceeded() {
		return $this->get_status() === GF_License_Statuses::MAX_SITES_EXCEEDED;
	}

	//----------------------------------------
	//---------- Serialization ---------------
	//----------------------------------------

	/**
	 * Custom serialization method for this response type.
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize(
			array(
				'data'   => $this->data,
				'errors' => $this->errors,
				'status' => $this->status,
				'meta'   => $this->meta,
				'strat'  => $this->transient_strategy,
			)
		);
	}

	/**
	 * Custom unserialization method for this response type.
	 *
	 * @param string $serialized
	 */
	public function unserialize( $serialized ) {
		$parsed = unserialize( $serialized );

		$this->data               = $parsed['data'];
		$this->errors             = $parsed['errors'];
		$this->status             = $parsed['status'];
		$this->meta               = $parsed['meta'];
		$this->transient_strategy = $parsed['strat'];
	}

}
