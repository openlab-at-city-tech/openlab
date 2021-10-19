<?php

namespace Gravity_Forms\Gravity_Forms_Zapier\REST;

defined( 'ABSPATH' ) || die();

use GF_REST_Controller;
use GFAPI;
use WP_Error;
use WP_REST_Request;

abstract class Zapier_Controller extends GF_REST_Controller {

	/**
	 * Checks that the specified form exists.
	 *
	 * @since 4.1
	 *
	 * @param WP_REST_Request $request The full data for the request.
	 *
	 * @return bool|WP_Error
	 */
	protected function is_url_form_id_valid( $request ) {
		if ( ! GFAPI::form_id_exists( rgar( $request->get_url_params(), 'form_id' ) ) ) {
			return new WP_Error( 'form_not_found', __( 'Form not found.', 'gravityformszapier' ), array( 'status' => 404 ) );
		}

		return true;
	}

	/**
	 * Determines if the user has the required capability to get the item.
	 *
	 * @since 4.1
	 *
	 * @param WP_REST_Request $request The full data for the request.
	 *
	 * @return bool
	 */
	public function get_item_permissions_check( $request ) {
		return $this->current_user_can_any( 'gravityforms_edit_forms', $request );
	}

	/**
	 * Prepares the data for the sample entry/entries response.
	 *
	 * @since 4.1
	 *
	 * @param array      $form         The form the Zapier feed is assigned to.
	 * @param bool       $admin_labels Should admin labels be used instead of front end labels or not?
	 * @param null|array $entry        The entry to use when preparing the data or null to use the default sample values.
	 *
	 * @return array
	 */
	protected function get_sample_data( $form, $admin_labels = false, $entry = null ) {
		return gf_zapier()->get_body( $entry, $form, array( 'meta' => array( 'adminLabels' => $admin_labels ) ) );
	}

}
