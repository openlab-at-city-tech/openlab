<?php
/**
 * The Schema for Rest endpoint
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Rest_Endpoints\Settings
 */

namespace WPMUDEV_BLC\App\Rest_Endpoints\Settings\Includes;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Schema {
	/**
	 * Get Schema for Rest Endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action A string that contains action that endpoint performs.
	 *
	 * @param array $args An array containing several options that we can use for returning specific schema properties.
	 *
	 * @return array An array containing Schema.
	 */
	public static function get_schema( string $action = null, array $args = array() ) {
		if ( \is_null( $action ) ) {
			return array();
		}

		$poperties_keys = array();
		$schema         = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => isset( $args['rest_base'] ) ? $args['rest_base'] : '',
			'type'       => 'object',
			'properties' => array(),
		);

		switch ( $action ) {
			case 'save':
				$poperties_keys = array( 'message', 'status_code' );
				break;
			case 'get':
				$poperties_keys = array( 'message', 'status_code', 'settings' );
				break;
		}

		$schema['properties'] = self::get_schema_properties( $poperties_keys );

		return $schema;
	}

	/**
	 * Get Schema properties for Rest Response.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties_keys An array containing field keys for properties needed.
	 *
	 * @return array An array of schema properties.
	 */
	protected static function get_schema_properties( array $properties_keys = array() ) {
		$return_properties = array();
		$schema_properties = array(
			'settings'          => array(
				'description' => esc_html__( 'All BLC settings.', 'broken-link-checker' ),
				'type'        => 'object',
				'properties'  => array(
					'activation_modal_shown'          => array(
						'description' => esc_html__( 'Activation modal shown or not.', 'broken-link-checker' ),
						'type'        => 'string',
					),

					'use_legacy_blc_version'          => array(
						'description' => esc_html__( 'Use legacy BLC', 'broken-link-checker' ),
						'type'        => 'string',
					),

					'userRolesAllowed'  => array(
						'type'       => 'object',
						'properties' => array(
							'name'  => array(
								'description' => esc_html__( 'User role name', 'broken-link-checker' ),
								'type'        => 'string',
							),
							'label' => array(
								'description' => esc_html__( 'User role label', 'broken-link-checker' ),
								'type'        => 'string',
							),
						),
					),
				),
			),

			'message'           => array(
				'description' => esc_html__( 'Response message.', 'broken-link-checker' ),
				'type'        => 'string',
			),

			'status_code'       => array(
				'description' => esc_html__( 'Response status code.', 'broken-link-checker' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array(
					'200',
					'400',
					'401',
					'403',
				),
				'readonly'    => true,
			),

			'instructions' => array(
				'description' => esc_html__( 'Response instructions.', 'broken-link-checker' ),
				'type' => 'object',
			),
		);

		if ( empty( $properties_keys ) ) {
			$return_properties = $schema_properties;
		} else {
			$return_properties = \array_filter(
				$schema_properties,
				function( string $property_key = '' ) use ( $properties_keys ) {
					return in_array( $property_key, $properties_keys );
				},
				ARRAY_FILTER_USE_KEY
			);
		}

		return apply_filters( 'wpmudev_blc_rest_enpoints_settings_schema_properties', $return_properties );
	}
}