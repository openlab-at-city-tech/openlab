<?php
/**
 * REST API: WP_REST_Widgets_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.6.0
 */

/**
 * Core class to access widgets via the REST API.
 *
 * @since 5.6.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Widgets_Controller extends WP_REST_Controller {

	/**
	 * Widgets controller constructor.
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'widgets';
	}

	/**
	 * Registers the widget routes for the controller.
	 *
	 * @since 5.6.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema(),
				),
				'allow_batch' => array( 'v1' => true ),
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base . '/(?P<id>[\w\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'description' => __( 'Whether to force removal of the widget, or move it to the inactive sidebar.', 'gutenberg' ),
							'type'        => 'boolean',
						),
					),
				),
				'allow_batch' => array( 'v1' => true ),
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to get widgets.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permissions_check();
	}

	/**
	 * Retrieves a collection of widgets.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$prepared = array();

		foreach ( wp_get_sidebars_widgets() as $sidebar_id => $widget_ids ) {
			if ( isset( $request['sidebar'] ) && $sidebar_id !== $request['sidebar'] ) {
				continue;
			}

			foreach ( $widget_ids as $widget_id ) {
				$response = $this->prepare_item_for_response( compact( 'sidebar_id', 'widget_id' ), $request );

				if ( ! is_wp_error( $response ) ) {
					$prepared[] = $this->prepare_response_for_collection( $response );
				}
			}
		}

		return new WP_REST_Response( $prepared );
	}

	/**
	 * Checks if a given request has access to get a widget.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permissions_check();
	}

	/**
	 * Gets an individual widget.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$widget_id  = $request['id'];
		$sidebar_id = wp_find_widgets_sidebar( $widget_id );

		if ( is_null( $sidebar_id ) ) {
			return new WP_Error(
				'rest_widget_not_found',
				__( 'No widget was found with that id.', 'gutenberg' ),
				array( 'status' => 404 )
			);
		}

		return $this->prepare_item_for_response( compact( 'widget_id', 'sidebar_id' ), $request );
	}

	/**
	 * Checks if a given request has access to create widgets.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permissions_check();
	}

	/**
	 * Creates a widget.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		$sidebar_id = $request['sidebar'];

		$widget_id = $this->save_widget( $request );

		if ( is_wp_error( $widget_id ) ) {
			return $widget_id;
		}

		wp_assign_widget_to_sidebar( $widget_id, $sidebar_id );

		$request['context'] = 'edit';

		$response = $this->prepare_item_for_response( compact( 'sidebar_id', 'widget_id' ), $request );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Checks if a given request has access to update widgets.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permissions_check();
	}

	/**
	 * Updates an existing widget.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$widget_id  = $request['id'];
		$sidebar_id = wp_find_widgets_sidebar( $widget_id );

		// Allow sidebar to be unset or missing when widget is not a WP_Widget.
		$parsed_id     = wp_parse_widget_id( $widget_id );
		$widget_object = gutenberg_get_widget_object( $parsed_id['id_base'] );
		if ( is_null( $sidebar_id ) && $widget_object ) {
			return new WP_Error(
				'rest_widget_not_found',
				__( 'No widget was found with that id.', 'gutenberg' ),
				array( 'status' => 404 )
			);
		}

		if (
			$request->has_param( 'instance' ) ||
			$request->has_param( 'form_data' )
		) {
			$maybe_error = $this->save_widget( $request );
			if ( is_wp_error( $maybe_error ) ) {
				return $maybe_error;
			}
		}

		if ( $request->has_param( 'sidebar' ) ) {
			if ( $sidebar_id !== $request['sidebar'] ) {
				$sidebar_id = $request['sidebar'];
				wp_assign_widget_to_sidebar( $widget_id, $sidebar_id );
			}
		}

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( compact( 'widget_id', 'sidebar_id' ), $request );
	}

	/**
	 * Checks if a given request has access to delete widgets.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permissions_check();
	}

	/**
	 * Deletes a widget.
	 *
	 * @since 5.6.0
	 *
	 * @global array $wp_registered_widget_updates The registered widget update functions.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		global $wp_registered_widget_updates;

		$widget_id  = $request['id'];
		$sidebar_id = wp_find_widgets_sidebar( $widget_id );

		if ( is_null( $sidebar_id ) ) {
			return new WP_Error(
				'rest_widget_not_found',
				__( 'No widget was found with that id.', 'gutenberg' ),
				array( 'status' => 404 )
			);
		}

		$request['context'] = 'edit';

		if ( $request['force'] ) {
			$response = $this->prepare_item_for_response( compact( 'widget_id', 'sidebar_id' ), $request );

			$parsed_id = wp_parse_widget_id( $widget_id );
			$id_base   = $parsed_id['id_base'];

			$original_post    = $_POST;
			$original_request = $_REQUEST;

			$_POST    = array(
				'sidebar'         => $sidebar_id,
				"widget-$id_base" => array(),
				'the-widget-id'   => $widget_id,
				'delete_widget'   => '1',
			);
			$_REQUEST = $_POST;

			$callback = $wp_registered_widget_updates[ $id_base ]['callback'];
			$params   = $wp_registered_widget_updates[ $id_base ]['params'];

			if ( is_callable( $callback ) ) {
				ob_start();
				call_user_func_array( $callback, $params );
				ob_end_clean();
			}

			$_POST    = $original_post;
			$_REQUEST = $original_request;

			wp_assign_widget_to_sidebar( $widget_id, '' );

			$response->set_data(
				array(
					'deleted'  => true,
					'previous' => $response->get_data(),
				)
			);
		} else {
			wp_assign_widget_to_sidebar( $widget_id, 'wp_inactive_widgets' );

			$response = $this->prepare_item_for_response(
				array(
					'sidebar_id' => 'wp_inactive_widgets',
					'widget_id'  => $widget_id,
				),
				$request
			);
		}

		return $response;
	}

	/**
	 * Performs a permissions check for managing widgets.
	 *
	 * @since 5.6.0
	 *
	 * @return true|WP_Error
	 */
	protected function permissions_check() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error(
				'rest_cannot_manage_widgets',
				__( 'Sorry, you are not allowed to manage widgets on this site.', 'gutenberg' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Saves the widget in the request object.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return string The saved widget ID.
	 */
	protected function save_widget( $request ) {
		global $wp_registered_widget_updates;

		require_once ABSPATH . 'wp-admin/includes/widgets.php'; // For next_widget_id_number().

		if ( isset( $request['id'] ) ) {
			// Saving an existing widget.
			$id            = $request['id'];
			$parsed_id     = wp_parse_widget_id( $id );
			$id_base       = $parsed_id['id_base'];
			$number        = isset( $parsed_id['number'] ) ? $parsed_id['number'] : null;
			$widget_object = gutenberg_get_widget_object( $id_base );
		} elseif ( $request['id_base'] ) {
			// Saving a new widget.
			$id_base       = $request['id_base'];
			$widget_object = gutenberg_get_widget_object( $id_base );
			$number        = $widget_object ? next_widget_id_number( $id_base ) : null;
			$id            = $widget_object ? $id_base . '-' . $number : $id_base;
		} else {
			return new WP_Error(
				'rest_invalid_widget',
				__( 'Widget type (id_base) is required.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}

		if ( ! isset( $wp_registered_widget_updates[ $id_base ] ) ) {
			return new WP_Error(
				'rest_invalid_widget',
				__( 'The provided widget type (id_base) cannot be updated.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}

		if ( isset( $request['instance'] ) ) {
			if ( ! $widget_object ) {
				return new WP_Error(
					'rest_invalid_widget',
					__( 'Cannot set instance on a widget that does not extend WP_Widget.', 'gutenberg' ),
					array( 'status' => 400 )
				);
			}

			if ( isset( $request['instance']['raw'] ) ) {
				if ( empty( $widget_object->show_instance_in_rest ) ) {
					return new WP_Error(
						'rest_invalid_widget',
						__( 'Widget type does not support raw instances.', 'gutenberg' ),
						array( 'status' => 400 )
					);
				}
				$instance = $request['instance']['raw'];
			} elseif ( isset( $request['instance']['encoded'], $request['instance']['hash'] ) ) {
				$serialized_instance = base64_decode( $request['instance']['encoded'] );
				if ( ! hash_equals( wp_hash( $serialized_instance ), $request['instance']['hash'] ) ) {
					return new WP_Error(
						'rest_invalid_widget',
						__( 'The provided instance is malformed.', 'gutenberg' ),
						array( 'status' => 400 )
					);
				}
				$instance = unserialize( $serialized_instance );
			} else {
				return new WP_Error(
					'rest_invalid_widget',
					__( 'The provided instance is invalid. Must contain raw OR encoded and hash.', 'gutenberg' ),
					array( 'status' => 400 )
				);
			}

			$form_data = array(
				"widget-$id_base" => array(
					$number => $instance,
				),
			);
		} elseif ( isset( $request['form_data'] ) ) {
			$form_data = $request['form_data'];
		} else {
			$form_data = array();
		}

		$original_post    = $_POST;
		$original_request = $_REQUEST;

		foreach ( $form_data as $key => $value ) {
			$slashed_value    = wp_slash( $value );
			$_POST[ $key ]    = $slashed_value;
			$_REQUEST[ $key ] = $slashed_value;
		}

		$callback = $wp_registered_widget_updates[ $id_base ]['callback'];
		$params   = $wp_registered_widget_updates[ $id_base ]['params'];

		if ( is_callable( $callback ) ) {
			ob_start();
			call_user_func_array( $callback, $params );
			ob_end_clean();
		}

		$_POST    = $original_post;
		$_REQUEST = $original_request;

		if ( $widget_object ) {
			// Register any multi-widget that the update callback just created.
			$widget_object->_set( $number );
			$widget_object->_register_one( $number );

			// WP_Widget sets updated = true after an update to prevent more
			// than one widget from being saved per request. This isn't what we
			// want in the REST API, though, as we support batch requests.
			$widget_object->updated = false;
		}

		return $id;
	}

	/**
	 * Prepares the widget for the REST response.
	 *
	 * @since 5.6.0
	 *
	 * @global array $wp_registered_sidebars        The registered sidebars.
	 * @global array $wp_registered_widgets         The registered widgets.
	 * @global array $wp_registered_widget_controls The registered widget controls.
	 *
	 * @param array           $item    An array containing a widget_id and sidebar_id.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		global $wp_registered_widgets;

		$widget_id  = $item['widget_id'];
		$sidebar_id = $item['sidebar_id'];

		if ( ! isset( $wp_registered_widgets[ $widget_id ] ) ) {
			return new WP_Error(
				'rest_invalid_widget',
				__( 'The requested widget is invalid.', 'gutenberg' ),
				array( 'status' => 500 )
			);
		}

		$widget    = $wp_registered_widgets[ $widget_id ];
		$parsed_id = wp_parse_widget_id( $widget_id );
		$fields    = $this->get_fields_for_response( $request );

		$prepared = array(
			'id'            => $widget_id,
			'id_base'       => $parsed_id['id_base'],
			'sidebar'       => $sidebar_id,
			'rendered'      => '',
			'rendered_form' => null,
			'instance'      => null,
		);

		if (
			rest_is_field_included( 'rendered', $fields ) &&
			'wp_inactive_widgets' !== $sidebar_id
		) {
			$prepared['rendered'] = trim( wp_render_widget( $widget_id, $sidebar_id ) );
		}

		if ( rest_is_field_included( 'rendered_form', $fields ) ) {
			$rendered_form = wp_render_widget_control( $widget_id );
			if ( ! is_null( $rendered_form ) ) {
				$prepared['rendered_form'] = trim( $rendered_form );
			}
		}

		if ( rest_is_field_included( 'instance', $fields ) ) {
			$widget_object = gutenberg_get_widget_object( $parsed_id['id_base'] );
			$instance      = gutenberg_get_widget_instance( $widget_id );

			if ( ! is_null( $instance ) ) {
				$serialized_instance             = serialize( $instance );
				$prepared['instance']['encoded'] = base64_encode( $serialized_instance );
				$prepared['instance']['hash']    = wp_hash( $serialized_instance );

				if ( ! empty( $widget_object->show_instance_in_rest ) ) {
					// Use new stdClass so that JSON result is {} and not [].
					$prepared['instance']['raw'] = empty( $instance ) ? new stdClass : $instance;
				}
			}
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$prepared = $this->add_additional_fields_to_object( $prepared, $request );
		$prepared = $this->filter_response_by_context( $prepared, $context );

		$response = rest_ensure_response( $prepared );

		$response->add_links( $this->prepare_links( $prepared ) );

		/**
		 * Filters the REST API response for a widget.
		 *
		 * @since 5.6.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $widget   The registered widget data.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_widget', $response, $widget, $request );
	}

	/**
	 * Prepares links for the widget.
	 *
	 * @since 5.6.0
	 *
	 * @param array $prepared Widget.
	 * @return array Links for the given widget.
	 */
	protected function prepare_links( $prepared ) {
		$id_base = ! empty( $prepared['id_base'] ) ? $prepared['id_base'] : $prepared['id'];

		return array(
			'self'                      => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $prepared['id'] ) ),
			),
			'collection'                => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'about'                     => array(
				'href'       => rest_url( sprintf( 'wp/v2/widget-types/%s', $id_base ) ),
				'embeddable' => true,
			),
			'https://api.w.org/sidebar' => array(
				'href' => rest_url( sprintf( 'wp/v2/sidebars/%s/', $prepared['sidebar'] ) ),
			),
		);
	}

	/**
	 * Gets the list of collection params.
	 *
	 * @since 5.6.0
	 *
	 * @return array[]
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			'sidebar' => array(
				'description' => __( 'The sidebar to return widgets for.', 'gutenberg' ),
				'type'        => 'string',
			),
		);
	}

	/**
	 * Retrieves the widget's schema, conforming to JSON Schema.
	 *
	 * @since 5.6.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'widget',
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'description' => __( 'Unique identifier for the widget.', 'gutenberg' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'id_base'       => array(
					'description' => __( 'The type of the widget. Corresponds to ID in widget-types endpoint.', 'gutenberg' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'sidebar'       => array(
					'description' => __( 'The sidebar the widget belongs to.', 'gutenberg' ),
					'type'        => 'string',
					'default'     => 'wp_inactive_widgets',
					'required'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'rendered'      => array(
					'description' => __( 'HTML representation of the widget.', 'gutenberg' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'rendered_form' => array(
					'description' => __( 'HTML representation of the widget admin form.', 'gutenberg' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'instance'      => array(
					'description' => __( 'Instance settings of the widget, if supported.', 'gutenberg' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit', 'embed' ),
					'default'     => null,
					'properties'  => array(
						'encoded' => array(
							'description' => __( 'Base64 encoded representation of the instance settings.', 'gutenberg' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'hash'    => array(
							'description' => __( 'Cryptographic hash of the instance settings.', 'gutenberg' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'raw'     => array(
							'description' => __( 'Unencoded instance settings, if supported.', 'gutenberg' ),
							'type'        => 'object',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
					),
				),
				'form_data'     => array(
					'description' => __( 'URL-encoded form data from the widget admin form. Used to update a widget that does not support instance. Write only.', 'gutenberg' ),
					'type'        => 'string',
					'context'     => array(),
					'arg_options' => array(
						'sanitize_callback' => function( $string ) {
							$array = array();
							wp_parse_str( $string, $array );
							return $array;
						},
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}
}
