<?php

// Include the Gravity Forms Add-On Framework
GFForms::include_feed_addon_framework();

use Gravity_Forms\Gravity_Forms_Zapier\REST;

class GF_Zapier extends GFFeedAddOn {

	/**
	 * Holds the cached request bodies for the current submission.
	 *
	 * @since 1.9
	 * @since 4.0 Changed default from null to empty array.
	 *
	 * @var array
	 */
	private static $_current_body = array();

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  4.0
	 * @var GF_Zapier $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Gravity Forms Zapier Add-On Add-On.
	 *
	 * @since  4.0
	 * @var string $_version Contains the version.
	 */
	protected $_version = GF_ZAPIER_VERSION;
	/**
	 * Defines the minimum Gravity Forms version required.
	 * @since  4.0
	 * @var string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = GF_ZAPIER_MIN_GF_VERSION;
	/**
	 * Defines the plugin slug.
	 *
	 * @since  4.0
	 * @var string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravityformszapier';
	/**
	 * Defines the main plugin file.
	 *
	 * @since  4.0
	 *
	 * @var string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravityformszapier/zapier.php';
	/**
	 * Defines the full path to this class file.
	 *
	 * @since  4.0
	 *
	 * @var string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;
	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  4.0
	 *
	 * @var string
	 */
	protected $_url = 'https://gravityforms.com';
	/**
	 * Defines the title of this add-on.
	 *
	 * @since  4.0
	 *
	 * @var string $_title The title of the add-on.
	 */
	protected $_title = 'Gravity Forms Zapier Add-On';
	/**
	 * Defines the short title of the add-on.
	 *
	 * @since  4.0
	 *
	 * @var string $_short_title The short title.
	 */
	protected $_short_title = 'Zapier';

	/**
	 * Defines if Add-On should use Gravity Forms server for update data.
	 *
	 * @since 4.0
	 *
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;


	/* Members plugin integration */

	/**
	 * Capabilities required for this add-on.
	 *
	 * @since 4.0
	 *
	 * @var array
	 */
	protected $_capabilities = array( 'gravityforms_zapier', 'gravityforms_zapier_uninstall' );

	/**
	 * Permissions required to uninstall this add-on.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	protected $_capabilities_uninstall = 'gravityforms_zapier_uninstall';


	/**
	 * Permissions required to access the add-on on the Gravity Forms settings page.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	protected $_capabilities_settings_page = 'gravityforms_zapier';

	/**
	 * Permissions required to access the add-on on the form settings page.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	protected $_capabilities_form_settings = 'gravityforms_zapier';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since  4.0
	 *
	 * @return GF_Zapier $_instance An instance of the GF_Zapier class
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GF_Zapier();
		}

		return self::$_instance;
	}


	// # INIT ----------------------------------------------------------------------------------------------------------

	/**
	 * Fired in the front end and admin. Subscribes to appropriate actions/filters in order to initialize the add-on.
	 *
	 * @since 4.0
	 */
	public function init() {

		parent::init();
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 11 );

		add_action( 'gform_pre_validation', array( $this, 'populate_product_inputs_submission' ) );
		add_action( 'gform_post_add_entry', array( $this, 'populate_product_inputs_add_entry' ), 10, 2 );

		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Send feed to Zapier only when payment is received.', 'gravityformszapier' ),
			)
		);
	}

	/**
	 * Includes the GF REST API, if not already included.
	 *
	 * @since 4.1
	 */
	public function init_rest_api() {
		if ( class_exists( 'GF_REST_Controller' ) ) {
			return;
		}

		if ( is_callable( array( 'GFWebAPI', 'get_instance' ) ) ) {
			GFWebAPI::get_instance()->init_v2();
		} else {
			( new GFWebAPI() )->init_v2();
		}
	}

	/**
	 * Registers the REST API endpoints.
	 *
	 * @since 4.0
	 */
	public function register_rest_routes() {
		$this->init_rest_api();

		require_once plugin_dir_path( __FILE__ ) . 'includes/rest/class-zapier-controller.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/rest/class-requirements-controller.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/rest/class-sample-entry-controller.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/rest/class-sample-entries-controller.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/rest/class-transfer-entries-controller.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/rest/class-feeds-controller.php';

		$controllers = array(
			REST\Requirements_Controller::class,
			REST\Sample_Entry_Controller::class,
			REST\Sample_Entries_Controller::class,
			REST\Transfer_Entries_Controller::class,
			REST\Feeds_Controller::class,
		);

		foreach ( $controllers as $controller ) {
			$controller_obj = new $controller();
			$controller_obj->register_routes();
		}
	}

	/**
	 * For form submissions made by Zapier, populates the product name and product price inputs of single product and
	 * hidden product fields.
	 *
	 * @since 4.0
	 *
	 * @param array $form Current form object.
	 *
	 * @return array
	 */
	public function populate_product_inputs_submission( $form ) {

		// Ignore requests that did not come from Zapier.
		if ( ! $this->is_zapier_request() ) {
			return $form;
		}

		$product_inputs = $this->get_product_inputs( $form );

		foreach ( $product_inputs as $field_id => $product ) {

			$_POST["input_{$field_id}_1"] = $product['name'];
			$_POST["input_{$field_id}_2"] = $product['price'];
		}

		return $form;
	}


	/**
	 * For request made by Zapier, populates the product name and product price inputs of single product and hidden product fields, and updates the entry with those new values.
	 *
	 * @since 4.0
	 *
	 * @param array $entry Current entry object.
	 * @param array $form  Current form object.
	 */
	public function populate_product_inputs_add_entry( $entry, $form ) {

		// Ignore requests that did not come from Zapier.
		if ( ! $this->is_zapier_request() ) {
			return;
		}

		$product_inputs = $this->get_product_inputs( $form, $entry );
		if ( empty( $product_inputs ) ) {
			return;
		}

		foreach ( $product_inputs as $field_id => $product ) {

			$entry["{$field_id}.1"] = $product['name'];
			$entry["{$field_id}.2"] = $product['price'];
		}

		GFAPI::update_entry( $entry );
	}

	/**
	 * Determines whether the current request was made by the Zapier App.
	 *
	 * @since 4.0
	 *
	 * @return bool Returns true if the current request was made by the Gravity Forms Zapier App. Returns false otherwise
	 */
	public function is_zapier_request() {

		return rgar( $_SERVER, 'HTTP_X_APPLICATION_SOURCE' ) == 'Zapier Integration';
	}

	/**
	 * Check to see if there are legacy feeds.
	 * Used to filter the feed list, control menu links.
	 *
	 * @since  4.0
	 *
	 * @param null|int $form_id The form ID.
	 *
	 * @return bool
	 */
	public function has_legacy_feeds( $form_id = null ) {
		$feeds = $this->get_feeds( $form_id );
		foreach ( $feeds as $feed ) {
			$is_legacy = $this->is_legacy_feed( $feed );
			$this->log_debug( 'legacy check ' . $is_legacy );
			if ( $is_legacy ) {
				$this->log_debug( 'there are legacy feeds for feed id ' . $feed['id'] . ', returning true' );
				return true;
			}
		}
		$this->log_debug( 'no legacy found' );

		return false;
	}

	/**
	 * Check if a feed (or current feed) is legacy.
	 *
	 * @since 4.0
	 *
	 * @param array $feed GF Feed Array.
	 *
	 * @return bool
	 */
	public function is_legacy_feed( $feed = null ) {
		if ( null === $feed ) {
			$feed = $this->get_current_feed();
		}

		return (bool) rgar( $feed['meta'], 'legacy' );
	}

	/**
	 * Remove items from Feed Table when Feeds are hidden.
	 *
	 * @since 4.0
	 *
	 * @param array $form GF Form array.
	 *
	 * @return GFAddOnFeedsTable
	 */
	public function get_feed_table( $form ) {
		$feeds = $this->get_feeds( rgar( $form, 'id' ) );

		// Disable rendering of feeds unless toggled true.
		if ( ! $this->should_display_feeds() ) {
			$feeds = array();
		}

		$columns               = $this->feed_list_columns();
		$column_value_callback = array( $this, 'get_column_value' );
		$bulk_actions          = $this->get_bulk_actions();
		$action_links          = $this->get_action_links();
		$no_item_callback      = array( $this, 'feed_list_no_item_message' );
		$message_callback      = '__return_false';

		require_once $this->get_base_path() . '/includes/class-feeds-list-table.php';

		return new GF_Zapier_Feeds_List_Table( $feeds, $this->_slug, $columns, $bulk_actions, $action_links, $column_value_callback, $no_item_callback, $message_callback, $this );
	}

	// # FEED SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Restores the previous value of the given field.
	 *
	 * @since 4.1
	 *
	 * @param array $field The current field.
	 *
	 * @return string|null
	 */
	public function restore_previous_value( $field ) {
		$name  = rgar( $field, 'name' );
		$value = rgar( $this->get_previous_settings(), $name );

		if ( ! $this->is_gravityforms_supported( '2.5-rc-1' ) ) {
			global $_gaddon_posted_settings;
			$_gaddon_posted_settings[ $name ] = $value;
		}

		return $value;
	}

	/**
	 * Setup fields for feed settings.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function feed_settings_fields() {
		$feed_name = array(
			'label'    => esc_html__( 'Name', 'gravityformszapier' ),
			'name'     => 'feedName',
			'type'     => 'text',
			'class'    => 'medium',
			'required' => true,
			'tooltip'  => sprintf(
				'<h6>%s</h6>%s',
				esc_html__( 'Name', 'gravityformszapier' ),
				esc_html__( 'This is a friendly name so you know what Zap is run when this form is submitted.', 'gravityformszapier' )
			),
		);

		$zap_url = array(
			'label'    => esc_html__( 'URL', 'gravityformszapier' ),
			'name'     => 'zapURL',
			'type'     => 'text',
			'class'    => 'large',
			'required' => true,
			'tooltip'  => sprintf(
				'<h6>%s</h6>%s',
				esc_html__( 'URL', 'gravityformszapier' ),
				esc_html__( 'This is the URL provided by Zapier when you created your Zap on their website. This is the location to which your form data will be submitted to Zapier for additional processing.', 'gravityformszapier' )
			),
		);

		$admin_labels = array(
			'label'         => esc_html__( 'Use Admin Labels', 'gravityformszapier' ),
			'name'          => 'adminLabels',
			'type'          => 'radio',
			'choices'       => array(
				array(
					'label' => 'Yes',
					'value' => '1',
				),
				array(
					'label' => 'No',
					'value' => '0',
				),
			),
			'horizontal'    => true,
			'default_value' => '0',
			'tooltip'       => sprintf(
				'<h6>%s</h6>%s',
				esc_html__( 'Use Admin Labels', 'gravityformszapier' ),
				esc_html__( 'By default the field labels will be sent to Zapier. Enable this option to send the field admin labels when available.', 'gravityformszapier' )
			),
		);

		if ( ! $this->is_legacy_feed() ) {
			$save_callback                 = array( $this, 'restore_previous_value' );
			$feed_name['readonly']         = 'readonly';
			$feed_name['save_callback']    = $save_callback;
			$zap_url['readonly']           = 'readonly';
			$zap_url['save_callback']      = $save_callback;
			$admin_labels['disabled']      = 'disabled';
			$admin_labels['save_callback'] = $save_callback;
		}

		return array(
			array(
				'fields' => array(
					$feed_name,
					$zap_url,
					$admin_labels,
					array(
						'name'    => 'zapier_conditional_enabled',
						'label'   => __( 'Conditional Logic', 'gravityformszapier' ),
						'type'    => 'feed_condition',
						'tooltip' => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Conditional Logic', 'gravityformszapier' ),
							esc_html__( 'When Conditional Logic is enabled, submissions for this form will only be sent to Zapier when the condition is met. When disabled, all submissions for this form will be sent to Zapier.', 'gravityformszapier' )
						),
					),
					array(
						'name' => 'legacy',
						'type' => 'hidden',
					),
					array(
						'name' => 'legacy_id',
						'type' => 'hidden',
					),
					array(
						'name' => 'zapID',
						'type' => 'hidden',
					),
				),
			),
		);

	}

	/**
	 * Setup columns for feed list table.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName' => esc_html__( 'Name', 'gravityformszapier' ),
			'zapURL'   => esc_html__( 'Zap URL', 'gravityformszapier' ),
		);

	}


	// # FEED PROCESSING -------------------------------------------------------------------------------------------------

	/**
	 * Send trigger request to Zapier.
	 *
	 * @since 4.0
	 *
	 * @param array $feed  The current Feed object.
	 * @param array $entry The current Entry object.
	 * @param array $form  The current Form object.
	 *
	 * @return bool
	 */
	public function process_feed( $feed, $entry, $form ) {
		$body    = $this->get_body( $entry, $form, $feed );
		$headers = array();
		if ( empty( $entry ) ) {
			$headers['X-Hook-Test'] = 'true';
		}

		$json_body = json_encode( $body );
		if ( empty( $body ) ) {
			$this->log_debug( 'There is no field data to send to Zapier.' );

			return false;
		}

		$this->log_debug( 'Posting to url: ' . $feed['meta']['zapURL'] . ' data: ' . print_r( $body, true ) );

		$form_data = array( 'sslverify' => false, 'ssl' => true, 'body' => $json_body, 'headers' => $headers );
		$response  = wp_remote_post( $feed['meta']['zapURL'], $form_data );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'The following error occurred: ' . print_r( $response, true ) );

			return false;
		} else {
			$this->log_debug( 'Successful response from Zap: ' . print_r( $response, true ) );

			if ( ! empty( $entry ) ) {
				$this->log_debug( 'Marking entry #'.$entry['id'].' as fulfilled.' );
				gform_update_meta( $entry['id'], $this->_slug.'_is_fulfilled', true );
			}

			return true;
		}
	}

	/**
	 * Returns the body of the request to be sent to zapier.
	 *
	 * @since 4.0
	 *
	 * @param array      $entry The current Entry array.
	 * @param array      $form  The current Form array.
	 * @param bool|array $feed  The current Feed array.
	 *
	 * @return array Returns the request body to be sent to Zapier as an associative array.
	 */
	public function get_body( $entry, $form, $feed = false ) {
		$admin_labels = is_array( $feed ) ? rgars( $feed, 'meta/adminLabels' ) : false;
		$cache_key    = get_current_blog_id() . '_' . rgar( $form, 'id' ) . '_' . rgar( $entry, 'id', 'sample' ) . '_' . $admin_labels;

		/**
		 * Determines if the Zapier add-on should use the body already stored.
		 *
		 * @since 2.1.1
		 *
		 * @param bool  true   If the current body should be used. Defaults to true.
		 * @param array $entry The Entry array.
		 * @param array $form  The Form array.
		 * @param array $feed  The Feed array.
		 */
		if ( apply_filters( 'gform_zapier_use_stored_body', true, $entry, $form, $feed ) ) {
			$current_body = rgar( self::$_current_body, $cache_key );

			if ( ! empty( $current_body ) ) {
				$this->log_debug( __METHOD__ . "(): Using cached request body ({$cache_key})." );

				return $current_body;
			}
		}

		$use_sample_value = empty( $entry );
		$body             = array();

		$body[ esc_html__( 'Form ID', 'gravityformszapier' ) ]    = rgar( $form, 'id' );
		$body[ esc_html__( 'Form Title', 'gravityformszapier' ) ] = rgar( $form, 'title' );

		$entry_properties = $this->get_entry_properties();
		foreach ( $entry_properties as $property_key => $property_config ) {
			$key = $this->get_body_key( $body, $property_config['label'] );

			if ( $use_sample_value ) {
				$value = $property_config['sample_value'];
			} else {
				$value = rgar( $entry, $property_key );
			}

			$body[ $key ] = $value;
		}

		$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
		foreach ( $entry_meta as $meta_key => $meta_config ) {
			$key = $this->get_body_key( $body, $meta_config['label'] );

			if ( $use_sample_value ) {
				$body[ $key ] = rgar( $meta_config, 'is_numeric' ) ? rand( 0, 10 ) : 'Sample value';
			} else {
				$body[ $key ] = rgar( $entry, $meta_key );
			}
		}

		foreach ( $form['fields'] as $field ) {
			$input_type = GFFormsModel::get_input_type( $field );
			if ( $input_type == 'honeypot' || $field->displayOnly ) {
				// Skip the honeypot and displayOnly fields.
				continue;
			}

			if ( ! $use_sample_value ) {
				$field_value = GFFormsModel::get_lead_field_value( $entry, $field );
				$field_value = apply_filters( 'gform_zapier_field_value', $field_value, $form['id'], $field->id, $entry );
			} else {
				$field_value = $this->get_sample_value( $field );
				$field_value = apply_filters( 'gform_zapier_sample_field_value', $field_value, $form['id'], $field->id );
			}

			$field_label = $this->get_body_label( $admin_labels, $field );

			$inputs = $field instanceof GF_Field ? $field->get_entry_inputs() : rgar( $field, 'inputs' );

			if ( is_array( $inputs ) && ( is_array( $field_value ) || $use_sample_value ) ) {
				// Handling multi-input fields.

				$non_blank_items = array();

				// Field has inputs, complex field like name, address and checkboxes. Get individual inputs.
				foreach ( $inputs as $input ) {
					$input_label = $this->get_body_label( $admin_labels, $field, $input['id'] );
					$key         = $this->get_body_key( $body, $input_label );

					$field_id     = (string) $input['id'];
					$input_value  = rgar( $field_value, $field_id );
					$body[ $key ] = $input_value;

					if ( ! rgblank( $input_value ) ) {
						$non_blank_items[] = $input_value;
					}
				}

				// Also adding an item for the "whole" field, which will be a concatenation of the individual inputs.
				switch ( $input_type ) {
					case 'checkbox' :
						// Checkboxes will create a comma separated list of values.
						$key          = $this->get_body_key( $body, $field_label );
						$body[ $key ] = implode( ', ', $non_blank_items );
						break;

					case 'name' :
					case 'address' :
						// Name and address will separate inputs by a single blank space.
						$key          = $this->get_body_key( $body, $field_label );
						$body[ $key ] = implode( ' ', $non_blank_items );
						break;

					case 'calculation':
					case 'hiddenproduct':
					case 'singleproduct':
						if ( $use_sample_value ) {
							$name     = rgar( $field_value, $field->id.'.1' );
							$price    = rgar( $field_value, $field->id.'.2' );
							$quantity = rgar( $field_value, $field->id.'.3' );

							$body['Products /'][] = array(
								'product_id'                 => $field->id,
								'product_name'               => $name,
								'product_quantity'           => $quantity,
								'product_price'              => $price,
								'product_price_with_options' => $price + 10 + 20,
								'product_subtotal'           => ( $price + 10 + 20 ) * $quantity,
								'product_options'            => 'Option 1, Option 2',
							);
						} else {
							// We get all product fields at once, so skipped if products has been set.
							if ( isset( $body['Products /'] ) ) {
								break;
							}

							$body['Products /'] = $this->get_products_array( $form, $entry );
						}
						break;
				}
			} else {
				$key = $this->get_body_key( $body, $field_label );

				switch ( $input_type ) {
					case 'list' :

						if ( $field->enableColumns ) {

							// Keep for backwards compatibility.
							$body[ $key ] = $field_value;

							// Add line-item support to list.
							$body[ $key.' /' ] = maybe_unserialize( $field_value );

						} else {

							$body[ $key ] = maybe_unserialize( $field_value );

						}

						break;

					default :
						if ( $field->type == 'product' ) {
							// Keep for backwards compatibility.
							$body[ $key ] = $field_value;

							if ( $use_sample_value ) {
								list( $name, $price ) = explode( '|', $field_value );
								$quantity = rand( 1, 10 );

								$body['Products /'][] = array(
									'product_id'                 => $field->id,
									'product_name'               => $name,
									'product_quantity'           => $quantity,
									'product_price'              => $price,
									'product_price_with_options' => $price + 10 + 20,
									'product_subtotal'           => ( $price + 10 + 20 ) * $quantity,
									'product_options'            => 'Option 1, Option 2'
								);
							} else {
								// We get all product fields at once, so skipped if products has been set.
								if ( isset( $body['Products /'] ) ) {
									break;
								}

								$body['Products /'] = $this->get_products_array( $form, $entry );
							}
						} elseif ( $field->type == 'shipping' ) {
							// Keep old shipping value for backward compatibility.
							$body[ $key ] = rgblank( $field_value ) ? '' : $field_value;

							// Set shipping as a faux product.
							if ( $use_sample_value ) {
								if ( $field->get_input_type() !== 'singleshipping' ) {
									list( $name, $price ) = explode( '|', $field_value );
									$name = 'Shipping ('.$name.')';
								} else {
									$name  = 'Shipping';
									$price = $field_value;
								}
								$body['Products /'][] = array(
									'product_id'                 => $field->id,
									'product_name'               => $name,
									'product_quantity'           => 1,
									'product_price'              => $price,
									'product_price_with_options' => $price,
									'product_subtotal'           => $price,
									'product_options'            => '',
								);
							}
						} else {
							$body[ $key ] = rgblank( $field_value ) ? '' : $field_value;
						}
				}
			}
		}

		/**
		 * Allows the request body sent to zapier to be filtered
		 *
		 * @param array $body  An associative array containing the request body that will be sent to Zapier.
		 * @param array $feed  The Feed Object currently being processed.
		 * @param array $entry The Entry Object currently being processed.
		 * @param array $form  The Form Object currently being processed.
		 *
		 * @since 3.1.1
		 */
		self::$_current_body[ $cache_key ] = gf_apply_filters(
			array( 'gform_zapier_request_body', rgar( $form, 'id' ) ),
			$body,
			$feed,
			$entry,
			$form
		);

		$this->log_debug( __METHOD__ . "(): Request body cached ({$cache_key})." );

		return rgar( self::$_current_body, $cache_key );
	}


	/**
	 * Retrieve a sample value for the current field.
	 *
	 * @since 4.0
	 *
	 * @param GF_Field $field The field properties.
	 *
	 * @return array|string
	 */
	public function get_sample_value( $field ) {

		$default_value = 'Sample value';
		$always_text   = array( 'survey', 'quiz', 'poll' );
		$field_id      = absint( $field->id );
		$choice_type   = in_array( $field->type, $always_text ) || ! $field->enableChoiceValue ? 'text' : 'value';

		switch ( $field->get_input_type() ) {
			case 'address' :
				$value[ $field_id.'.1' ] = 'Bag End';
				$value[ $field_id.'.2' ] = 'Bagshot Row';
				$value[ $field_id.'.3' ] = 'Hobbiton';
				$value[ $field_id.'.4' ] = 'Shire';
				$value[ $field_id.'.5' ] = '1234';
				$value[ $field_id.'.6' ] = 'Middle Earth';
				break;

			case 'name' :
				$value[ $field_id.'.2' ] = 'Mr.';
				$value[ $field_id.'.3' ] = 'Bilbo';
				$value[ $field_id.'.4' ] = 'L.';
				$value[ $field_id.'.6' ] = 'Baggins';
				$value[ $field_id.'.8' ] = 'Ring-bearer';

				$inputs = $field->get_entry_inputs();
				if ( ! is_array( $inputs ) ) {
					$value = implode( ' ', $value );
				}

				break;

			case 'calculation' :
				$value[ $field_id.'.1' ] = $field->label;
				$value[ $field_id.'.2' ] = 10;
				$value[ $field_id.'.3' ] = 2;
				break;

			case 'checkbox' :
				$value = array();
				if ( is_array( $field->choices ) ) {
					$choice_number = 1;
					foreach ( $field->choices as $choice ) {
						if ( $choice_number % 10 == 0 ) {
							$choice_number ++;
						}

						$choice_value = rgar( $choice, $choice_type );
						if ( $field->enablePrice ) {
							$price        = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
							$choice_value .= '|'.$price;
						}

						$input_id           = $field_id.'.'.$choice_number ++;
						$value[ $input_id ] = $choice_value;
					}
				}
				break;

			case 'creditcard' :
				$value[ $field_id.'.1' ] = str_repeat( 'X', 16 );
				$value[ $field_id.'.4' ] = 'Visa';
				break;

			case 'date' :
				$value = date( 'Y-m-d' );
				break;

			case 'email' :
				$value = 'test@domain.dev';
				break;

			case 'fileupload' :
			case 'signature' :
				$value = 'http://domain.dev/some_location/file.png';
				break;

			case 'list' :
				if ( ! $field->enableColumns ) {
					$max = 2;
				} else {
					$max = count( $field->choices ) * 2;
				}

				$value = array_fill( 0, $max, $default_value );
				$value = serialize( $field->create_list_array( $value ) );
				break;

			case 'multiselect' :
				$value = rgars( $field->choices, '0/'.$choice_type );
				if ( isset( $field->choices[1] ) ) {
					$value .= ','.rgar( $field->choices[1], $choice_type );
				}
				break;

			case 'number' :
			case 'total' :
				$value = 100;
				break;

			case 'price' :
				$value = $field->label.'|10';
				break;

			case 'phone' :
				$value = '(999) 999-9999';
				break;

			case 'post_image' :
				$title       = $field->displayTitle ? 'The title' : '';
				$caption     = $field->displayCaption ? 'The caption' : '';
				$description = $field->displayDescription ? 'The description' : '';
				$value       = 'http://domain.dev/some_location/image.img|:|'.$title.'|:|'.$caption.'|:|'.$description;
				break;

			case 'hiddenproduct' :
			case 'singleproduct' :
				$value[ $field_id.'.1' ] = $field->label;
				$value[ $field_id.'.2' ] = empty( $field->basePrice ) ? 10 : GFCommon::to_number( $field->basePrice );
				$value[ $field_id.'.3' ] = 2;
				break;

			case 'singleshipping' :
				$value = empty( $field->basePrice ) ? 10 : GFCommon::to_number( $field->basePrice );
				break;

			case 'time' :
				$value = '10:30 am';
				break;

			case 'website' :
				$value = 'http://domain.dev';
				break;

			case 'likert' :
				if ( $field->gsurveyLikertEnableMultipleRows ) {
					$value = array();
					foreach ( $field->inputs as $input ) {
						$value[ $input['id'] ] = $this->get_random_choice( $field->choices, $choice_type );
					}
				} else {
					$value = $this->get_random_choice( $field->choices, $choice_type );
				}
				break;

			case 'rank' :
				$c       = 1;
				$value   = array();
				$choices = $field->choices;
				shuffle( $choices );
				foreach ( $choices as $choice ) {
					$value[] = $c ++.'. '.rgar( $choice, $choice_type );
				}
				$value = implode( ', ', $value );
				break;

			default :
				$inputs = $field->get_entry_inputs();

				if ( $inputs ) {
					$value = array();
					foreach ( $inputs as $input ) {
						$choices = rgar( $input, 'choices' );
						if ( is_array( $choices ) ) {
							$value[ $input['id'] ] = $this->get_random_choice( $choices, $choice_type );
						} else {
							$value[ $input['id'] ] = $default_value;
						}
					}
				} elseif ( is_array( $field->choices ) && count( $field->choices ) > 0 ) {
					$value = $this->get_random_choice( $field->choices, $choice_type, $field->enablePrice );
				} else {
					$value = $default_value;
				}
		}

		return $value;
	}

	/**
	 * Return a random choice.
	 *
	 * @since 4.0
	 *
	 * @param array  $choices       The choices.
	 * @param string $choice_type   The choice property to return; text or value.
	 * @param bool   $price_enabled Is the enablePrice property enabled for the field being processed.
	 *
	 * @return string
	 */
	public function get_random_choice( $choices, $choice_type, $price_enabled = false ) {
		$key    = array_rand( $choices );
		$choice = $choices[ $key ];
		$value  = rgar( $choice, $choice_type );

		if ( $price_enabled ) {
			$price = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
			$value .= '|'.$price;
		}

		return $value;
	}

	/**
	 * Return the product fields in the entry as an array.
	 *
	 * @since 4.0
	 *
	 * @param array $form  The Form Object.
	 * @param array $entry The Entry Object.
	 *
	 * @return array
	 */
	public function get_products_array( $form, $entry ) {
		$product_info = GFCommon::get_product_fields( $form, $entry );
		$products     = array_values( $product_info['products'] );
		$product_ids  = array_keys( $product_info['products'] );
		foreach ( $products as $key => $product ) {
			$products[ $key ]['product_id']   = $product_ids[ $key ];
			$products[ $key ]['product_name'] = $product['name'];
			unset( $products[ $key ]['name'] );
			$products[ $key ]['product_quantity'] = intval( $product['quantity'] );
			unset( $products[ $key ]['quantity'] );

			// Change price to "product price" to be more clear when displaying in Zapier.
			$products[ $key ]['product_price'] = GFCommon::to_number( $product['price'], $entry['currency'] );
			unset( $products[ $key ]['price'] );

			$options = rgar( $product, 'options' );
			// Add unit price.
			$products[ $key ]['product_price_with_options'] = GFCommon::to_number( $product['price'], $entry['currency'] );
			if ( is_array( $options ) && ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$products[ $key ]['product_price_with_options'] += GFCommon::to_number( $option['price'], $entry['currency'] );
				}
			}

			// Add subtotal to product array.
			$products[ $key ]['product_subtotal'] = $products[ $key ]['product_price_with_options'] * $products[ $key ]['product_quantity'];

			// Turn options into product_options.
			unset( $products[ $key ]['options'] );
			$products[ $key ]['product_options'] = ( empty( $options ) ) ? '' : implode( ', ', wp_list_pluck( $options, 'option_name' ) );
		}

		$shipping_field = GFAPI::get_fields_by_type( $form, array( 'shipping' ) );
		if ( ! empty( $shipping_field ) ) {
			// Set shipping as a faux product.
			$products[] = array(
				'product_id'                 => $product_info['shipping']['id'],
				'product_name'               => $product_info['shipping']['name'],
				'product_quantity'           => 1,
				'product_price'              => $product_info['shipping']['price'],
				'product_price_with_options' => $product_info['shipping']['price'],
				'product_subtotal'           => $product_info['shipping']['price'],
				'product_options'            => '',
			);
		}

		return apply_filters( 'gform_zapier_products', $products, $form, $entry );
	}

	/**
	 * Retrieve label to be sent to Zapier.
	 *
	 * @since 4.0
	 *
	 * @param bool     $admin_labels Should the field adminLabel be used.
	 * @param GF_Field $field        The field currently being processed.
	 * @param bool|int $input_id     False or the input ID.
	 *
	 * @return string
	 */
	public function get_body_label( $admin_labels, $field, $input_id = false ) {

		$label = $admin_labels && ! empty( $field->adminLabel ) ? $field->adminLabel : $field->label;

		if ( $input_id ) {
			$input = GFFormsModel::get_input( $field, $input_id );

			if ( ! is_null( $input ) ) {
				if ( $field->get_input_type() == 'checkbox' ) {
					$label = $input['label'];
				} else {
					$label .= ' ('.$input['label'].')';
				}
			}
		}

		if ( empty( $label ) ) {
			return $field->get_form_editor_field_title();
		}

		return $label;
	}

	/**
	 * Ensure the label (array key) is unique.
	 *
	 * @since 4.0
	 *
	 * @param array  $body  The data to be sent to Zapier.
	 * @param string $label The field or entry meta label.
	 *
	 * @return string
	 */
	public function get_body_key( $body, $label ) {

		$count = 1;
		$key   = $label;

		while ( array_key_exists( $key, $body ) ) {
			$key = $label.' - '.$count;
			$count ++;
		}

		return $key;
	}

	/**
	 * Return the entry properties to be sent to Zapier.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_entry_properties() {
		return array(
			'id'             => array(
				'label'        => esc_html__( 'Entry ID', 'gravityforms' ),
				'sample_value' => 0,
			),
			'date_created'   => array(
				'label'        => esc_html__( 'Entry Date', 'gravityforms' ),
				'sample_value' => gmdate( 'Y-m-d H:i:s' ),
			),
			'ip'             => array(
				'label'        => esc_html__( 'User IP', 'gravityforms' ),
				'sample_value' => GFFormsModel::get_ip(),
			),
			'source_url'     => array(
				'label'        => esc_html__( 'Source Url', 'gravityforms' ),
				'sample_value' => RGFormsModel::get_current_page_url(),
			),
			'created_by'     => array(
				'label'        => esc_html__( 'Created By', 'gravityforms' ),
				'sample_value' => 1,
			),
			'transaction_id' => array(
				'label'        => esc_html__( 'Transaction Id', 'gravityforms' ),
				'sample_value' => '1234567890',
			),
			'payment_amount' => array(
				'label'        => esc_html__( 'Payment Amount', 'gravityforms' ),
				'sample_value' => 100,
			),
			'payment_date'   => array(
				'label'        => esc_html__( 'Payment Date', 'gravityforms' ),
				'sample_value' => gmdate( 'Y-m-d H:i:s' ),
			),
			'payment_status' => array(
				'label'        => esc_html__( 'Payment Status', 'gravityforms' ),
				'sample_value' => 'Paid',
			),
			'post_id'        => array(
				'label'        => esc_html__( 'Post Id', 'gravityforms' ),
				'sample_value' => 1,
			),
			'user_agent'     => array(
				'label'        => esc_html__( 'User Agent', 'gravityforms' ),
				'sample_value' => sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'], 0, 250 ) ),
			),
		);
	}

	/**
	 * Upgrades delayed feed settings for the specified payment addon
	 *
	 * @since 4.0
	 *
	 * @param string $payment_addon_slug
	 */
	public function upgrade_delayed_feed_settings( $payment_addon_slug ) {

		$payment_feeds = $this->get_feeds_by_slug( $payment_addon_slug );
		if ( ! empty( $payment_feeds ) ) {

			$this->log_debug( __METHOD__.'(): New feeds found for '.$this->_slug.' - copying over delay settings.' );

			foreach ( $payment_feeds as $feed ) {
				$meta = $feed['meta'];
				if ( ! rgempty( 'delay_zapier_subscription', $meta ) ) {
					$meta[ "delay_{$this->_slug}" ] = $meta[ 'delay_zapier_subscription' ];
					$this->update_feed_meta( $feed['id'], $meta );
				}
			}
		}
	}


	private function __clone() {
	} /* do nothing */


	// # FEED UPGRADE TO FRAMEWORK -------------------------------------------------------------

	/**
	 * Part of the add-on framework upgrade process. This function gets called if an upgrade is required (i.e. version changed).
	 * Upgrade feeds to new addon framework table if previously installed version used the legacy feeds table
	 *
	 * @since 4.0
	 *
	 * @param string $previous_version Previously installed version.
	 */
	public function upgrade( $previous_version ) {

		$this->log_debug( 'Starting upgrade routine' );

		if ( $this->is_pre_addon_framework( $previous_version ) ) {

			// DB table has already been created at this point by add-on framework.
			// Only need to move feeds over.
			$this->upgrade_feeds();
		} else {
			$this->log_debug( 'Previous version on framework, no need to migrate' );
		}
	}

	/**
	 * Migrates feeds from legacy db table to new addon framework feed table.
	 *
	 * @since 4.0
	 */
	public function upgrade_feeds() {

		if ( $this->is_upgrading() ) {
			return;
		}

		$old_feeds = $this->get_old_feeds();

		if ( ! $old_feeds ) {
			$this->log_debug( 'There are no old feeds to migrate.' );
			return;
		}

		// Get new feeds, and only add legacy if it isn't already in the new feed array.
		$current_feeds = $this->get_feeds();
		$this->log_debug( 'Current feed count:  ' . count( $current_feeds ) );

		foreach ( $old_feeds as $old_feed ) {

			$feed_name = $old_feed['name'];
			$form_id   = $old_feed['form_id'];
			$is_active = $old_feed['is_active'];
			$zap_url   = $old_feed['url'];

			// If feed has already been migrated, skip it.
			if ( $this->is_feed_upgraded( $old_feed, $current_feeds ) ) {
				$this->log_debug( 'Feed ' . $zap_url . ' already migrated. Skipping.' );
				continue;
			}

			$new_meta = array(
				'feedName'    => $feed_name,
				'zapURL'      => $zap_url,
				'adminLabels' => rgar( $old_feed['meta'], 'adminLabels' ),
				'legacy'      => '1',
				'legacy_id'   => $old_feed['id'],
			);

			// Add conditional logic, legacy only allowed one condition.
			$conditional_enabled = rgar( $old_feed['meta'], 'zapier_conditional_enabled' );
			if ( $conditional_enabled ) {
				$new_meta['feed_condition_conditional_logic'] = 1;

				$logic = array(
					'actionType' => 'show',
					'logicType'  => 'all',
					'rules'      => array(
						array(
							'fieldId'  => rgar( $old_feed['meta'], 'zapier_conditional_field_id' ),
							'operator' => rgar( $old_feed['meta'], 'zapier_conditional_operator' ),
							'value'    => rgar( $old_feed['meta'], 'zapier_conditional_value' ),
						),
					),
				);

				$logic = apply_filters( 'gform_zapier_feed_conditional_logic', $logic, GFAPI::get_form( $form_id ), $old_feed['meta'] );

				$new_meta['feed_condition_conditional_logic_object'] = array( 'conditionalLogic' => $logic );

			} else {
				$new_meta['feed_condition_conditional_logic'] = 0;
			}

			$this->log_debug( 'Migrating feed ' . $zap_url );
			$this->insert_feed( $form_id, $is_active, $new_meta );
		}

		// Upgrade paypal delayed setting.
		$this->upgrade_paypal_delay_settings();

		//TODO: may need to update Stripe delayed setting as well

		$this->end_upgrade();

		$migrated_feeds = $this->get_feeds();

		/**
		 * Allows custom actions to be performed once the feeds have been migrated to the add-on framework.
		 *
		 * @since 4.0.0
		 *
		 * @param array $migrated_feeds An array of migrated Zapier feeds.
		 * @param array $old_feeds      An array of legacy Zapier feeds from before the migration.
		 */
		do_action( 'gform_zapier_post_migrate_feeds', $migrated_feeds, $old_feeds );
	}

	/**
	 * Returns true if the legacy feed has already been upgraded (i.e. moved to new feed table).
	 *
	 * @since 4.0
	 *
	 * @param array $legacy_feed   The feed to be processed.
	 * @param array $current_feeds An array of all feeds that have been upgraded.
	 *
	 * @return bool
	 */
	public function is_feed_upgraded( $legacy_feed, $current_feeds ) {

		if ( count( $current_feeds ) == 0 ) {
			//no new feeds, not migrated
			return false;
		}

		foreach ( $current_feeds as $new_feed ) {
			if ( isset( $new_feed['meta']['legacy_id'] ) ) {
				if ( $new_feed['meta']['legacy_id'] == $legacy_feed['id'] ) {
					return true;
				}
				continue;
			}

			// Feed was migrated by the first beta; checking the form ID and Zap URL instead.
			if ( ( $new_feed['form_id'] == $legacy_feed['form_id'] ) && ( rgars( $new_feed, 'meta/zapURL' ) == $legacy_feed['url'] ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Determines if the upgrade process is running
	 *
	 * @since 4.0
	 *
	 * @return bool Returns true if the database upgrade is currently running. Returns false otherwise.
	 */
	public function is_upgrading() {

		$option_name = 'gform_zapier_feed_upgrading';

		$timestamp = get_option( $option_name );
		if ( empty( $timestamp ) ) {
			$timestamp = 0;
		}
		// Expires in 15 seconds.
		$is_upgrading = ( time() - $timestamp ) < 15;

		// Marking as upgrading
		if ( ! $is_upgrading ) {
			update_option( $option_name, time(), false );
		}

		return $is_upgrading;
	}

	/**
	 * Marks the upgrade process as completed
	 *
	 * @since 4.0
	 */
	public function end_upgrade() {

		delete_option( 'gform_zapier_feed_upgrading' );

	}

	/**
	 * Migrate the delayed payment setting for the PayPal add-on integration.
	 *
	 * @since 4.0
	 *
	 */
	public function upgrade_paypal_delay_settings() {

		global $wpdb;

		// Log that we are checking for delay settings for migration.
		$this->log_debug( __METHOD__ . '(): Checking to see if there are any delay settings that need to be migrated for PayPal Standard.' );


		//**** test this *******

		// Upgrade PayPal delayed feed settings
		$this->upgrade_delayed_feed_settings( 'gravityformspaypal' );

		// Upgrade Stripe delayed feed settings
		$this->upgrade_delayed_feed_settings( 'gravityformsstripe' );

	}

	/**
	 * Returns the old feeds.
	 *
	 * For some additional context:
	 *    Old Feeds: Stored in wp_rg_zapier
	 *    Legacy Feeds: Stored in wp_gf_addon_feed
	 *
	 * @since 4.0
	 *
	 * @return null|array|bool|object
	 */
	public function get_old_feeds() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rg_zapier';

		$this->log_debug( 'Looking for table ' . $table_name );

		if ( ! $this->table_exists( $table_name ) ) {
			$this->log_debug( 'Table did NOT exist.' );

			return false;
		}

		$form_table_name = RGFormsModel::get_form_table_name();
		$sql             = "SELECT s.id, s.is_active, s.form_id, s.name, s.url, s.meta, f.title as form_title
				FROM $table_name s
				INNER JOIN $form_table_name f ON s.form_id = f.id";

		$results = $wpdb->get_results( $sql, ARRAY_A );

		$count = sizeof( $results );
		for ( $i = 0; $i < $count; $i ++ ) {
			$results[ $i ]['meta'] = maybe_unserialize( $results[ $i ]['meta'] );
		}

		return $results;
	}

	/**
	 * Returns true if the specified version is a legacy (i.e. pre addon-framework, pre 4.0) version. Returns false otherwise.
	 *
	 * @since 4.0
	 *
	 * @param string $version The version to be checked.
	 *
	 * @return bool Returns true if the specified version is a legacy (i.e. pre addon-framework, pre 4.0) version. Returns false otherwise
	 */
	public function is_pre_addon_framework( $version = null ) {

		if ( empty( $version ) ) {
			$version = get_option( 'gf_zapier_version' );
		}
		$is_pre_addon_framework = empty( $version ) || version_compare( $version, '4.0', '<' );

		return $is_pre_addon_framework;
	}

	//-------------------------------------------------------------

	/**
	 * Returns the plugin settings fields.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		// Get Setup instructions view.
		ob_start();
		require $this->get_base_path() . '/includes/setup-instructions.php';
		$instructions = ob_get_clean();

		$fields               = array();
		$instructions_section = array(
			'fields' => array(),
		);

		if ( $this->is_gravityforms_supported( '2.5-beta' ) ) {
			$instructions_section['fields'][] = array(
				'type' => 'html',
				'name' => 'zapier_instructions',
				'html' => $instructions,
			);
		} else {
			$instructions_section['description'] = $instructions;
		}

		return array(
			$instructions_section,
			array(
				'title'  => esc_html__( 'Advanced Settings', 'gravityformszapier' ),
				'fields' => array(
					array(
						'type'    => 'checkbox',
						'name'    => 'toggle_feeds',
						'label'   => esc_html__( 'Zapier Feeds', 'gravityformszapier' ),
						'tooltip' => esc_html__( 'Show or hide the Zapier feed(s) on the Form > Settings > Zapier Feeds screen. This is intended for advanced users and should only be enabled when instructed by support.', 'gravityformszapier' ),
						'choices' => array(
							array(
								'label'         => esc_html__( 'Display Zapier feeds in the form settings', 'gravityformszapier' ),
								'name'          => 'display_feeds',
								'default_value' => intval( $this->should_display_feeds() ),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Overridden so that only forms with legacy zapier feeds have the link to the feeds.
	 *
	 * @since 4.0
	 *
	 * @param array $tabs    Array of Form Settings Tabs.
	 * @param int   $form_id GF Form ID.
	 *
	 * @return array
	 */
	public function add_form_settings_menu( $tabs, $form_id ) {
		if ( $this->should_display_feeds() ) {
			$tabs[] = array(
				'name'         => $this->_slug,
				'label'        => $this->get_short_title(),
				'query'        => array( 'fid' => null ),
				'capabilities' => $this->_capabilities_form_settings,
				'icon'         => $this->get_menu_icon(),
			);
		}

		return $tabs;
	}

	/**
	 * Disables manual creation of feeds.
	 *
	 * @since 4.1
	 *
	 * @return bool
	 */
	public function can_create_feed() {
		return ! empty( $_GET['fid'] );
	}

	/**
	 * Should Zapier feeds be displayed?
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	private function should_display_feeds() {
		// Get Addon Settings field value.
		$state = $this->get_plugin_settings();
		$value = isset( $state['display_feeds'] ) ? (bool) $state['display_feeds'] : null;

		return null === $value ? (bool) $this->has_legacy_feeds() : $value;
	}

	/**
	 * The empty feeds list table message.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function feed_list_no_item_message() {
		return sprintf(
			// Translators: 1. Opening <a> tag for link to Zapier, 2. Closing <a> tag.
			esc_html__( 'Simply %1$screate a zap%2$s on zapier.com.', 'gravityformszapier' ),
			'<a href="' . esc_url( 'https://zapier.com/app/editor' ) . '">',
			'</a>'
		);
	}

	/**
	 * Gets all product fields that have been submitted in the form (i.e. non-blank and non-zero quantities) and returns an array containing their product names and prices as configured in the form object
	 *
	 * @since 4.0
	 *
	 * @param array $form Current form array.
	 *
	 * @return array Returns an associative array containing all submitted products with their respecitve product names and prices, keyed by the field id.
	 */
	private function get_product_inputs( $form, $entry = null ) {

		if ( empty( $entry ) ) {
			$entry = GFFormsModel::create_lead( $form );
		}

		$products = array();
		foreach ( $form['fields'] as $field ) {

			// Ignore any field that is not a single product or hidden product.
			if ( ! in_array( $field->inputType, array( 'singleproduct', 'hiddenproduct' ) ) ) {
				continue;
			}

			$quantity_field = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $field->id );


			if ( sizeof( $quantity_field ) > 0 ) {
				$quantity = ! RGFormsModel::is_field_hidden( $form, $quantity_field[0], array(), $entry ) ? RGFormsModel::get_lead_field_value( $entry, $quantity_field[0] ) : 0;
			} else {
				$entry_value = RGFormsModel::get_lead_field_value( $entry, $field );
				$quantity    = ! $field->disableQuantity ? rgget( "{$field->id}.3", $entry_value ) : 0;
			}

			// Ignore product fields that didn't have a quantity sent in.
			if ( empty( $quantity ) ) {
				continue;
			}

			$products[ $field->id ] = array(
				'name' => $field->label,
				'price' => $field->basePrice,
			);
		}

		return $products;
	}

	/**
	 * Return the Zapier icon for the plugin/form settings menu.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_menu_icon() {

		return $this->is_gravityforms_supported( '2.5-beta-4' ) ? 'gform-icon--zapier' : 'dashicons-admin-generic';

	}

}
