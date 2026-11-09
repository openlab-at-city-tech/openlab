<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Revives the dlm_extensions / dlm_extensions_action_{slug} bridge removed from DLM,
// so old bundled extensions (e.g. DLM Pro < 1.1.0) still get their update-checker fired.

if ( ! class_exists( 'DLM_Product_License' ) ) {
	class DLM_Product_License {

		private $product_id;
		private $key;
		private $email;
		private $status;
		private $license_status;

		public function __construct( $product_id ) {
			$this->product_id = $product_id;

			$db_license = wp_parse_args(
				get_option( $product_id . '-license', array() ),
				array(
					'key'            => '',
					'email'          => get_option( 'admin_email', '' ),
					'status'         => 'inactive',
					'license_status' => '',
				)
			);

			$this->key            = $db_license['key'];
			$this->email          = $db_license['email'];
			$this->status         = $db_license['status'];
			$this->license_status = $db_license['license_status'];
		}

		public function get_key() {
			return $this->key;
		}

		public function set_key( $key ) {
			$this->key = $key;
		}

		public function get_email() {
			return $this->email;
		}

		public function set_email( $email ) {
			$this->email = $email;
		}

		public function get_status() {
			return $this->status;
		}

		public function get_license_status() {
			return $this->license_status;
		}

		public function set_status( $status ) {
			$this->status = $status;
		}

		public function set_license_status( $license_status ) {
			$this->license_status = $license_status;
		}

		public function is_active() {
			return 'active' === $this->status;
		}

		public function store() {
			update_option(
				$this->product_id . '-license',
				array(
					'key'            => $this->get_key(),
					'email'          => $this->get_email(),
					'status'         => $this->get_status(),
					'license_status' => $this->get_license_status(),
				)
			);
		}
	}
}

if ( ! class_exists( 'DLM_Product_Error_Handler' ) ) {
	class DLM_Product_Error_Handler {

		private static $instance;
		private $errors = array();

		public static function get() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function add( $error ) {
			$this->errors[] = $error;
		}

		public function get_errors() {
			return $this->errors;
		}
	}
}

if ( ! class_exists( 'DLM_Product' ) ) {
	class DLM_Product {

		const STORE_URL             = 'https://download-monitor.com/';
		const PRODUCT_DOWNLOAD_URL  = 'https://download-monitor.com/';
		const ENDPOINT_ACTIVATION   = 'wp_plugin_licencing_activation_api';
		const ENDPOINT_STATUS_CHECK = 'license_wp_api_status_check';
		const ENDPOINT_GET_PACKAGES = 'get_packages_request';
		const ENDPOINT_UPDATE       = '?wc-api=wp_plugin_licencing_update_api';

		private $product_id;
		private $product_name = '';
		private $plugin_name;
		private $version = false;
		private $license = null;

		public function __construct( $product_id, $version = false, $product_name = '' ) {
			$this->product_id   = $product_id;
			$this->plugin_name  = $this->product_id . '/' . $this->product_id . '.php';
			$this->product_name = $product_name ? $product_name : $this->product_id;
			$this->version      = $version;
		}

		public function get_product_id() {
			return $this->product_id;
		}

		public function get_product_name() {
			return $this->product_name;
		}

		public function get_plugin_name() {
			return $this->plugin_name;
		}

		public function get_license() {
			if ( null === $this->license ) {
				$this->license = new DLM_Product_License( $this->product_id );
			}

			return $this->license;
		}

		public function set_license( $license ) {
			$this->license = $license;
			$this->license->store();
		}

		public function get_version() {
			return $this->version;
		}

		public function handle_errors( $errors ) {
			foreach ( $errors as $error_key => $error ) {
				DLM_Product_Manager::get()->error_handler()->add( $error );

				if ( 'no_activation' === $error_key ) {
					$this->get_license()->set_status( 'inactive' );
					$this->get_license()->set_license_status( 'inactive' );
					$this->get_license()->store();
				}
			}
		}

		public function get_tracking_url( $link_identifier = '' ) {
			$tracking_vars = array(
				'utm_campaign' => $this->get_product_name() . '_licensing',
				'utm_medium'   => 'link',
				'utm_source'   => $this->get_product_name(),
				'utm_content'  => $link_identifier,
			);

			$tracking_vars = urlencode_deep( $tracking_vars );
			$query_string  = build_query( $tracking_vars );

			return 'https://www.download-monitor.com/pricing?' . $query_string;
		}
	}
}

if ( ! class_exists( 'DLM_Product_Manager' ) ) {
	class DLM_Product_Manager {

		private static $instance = null;
		private $products = array();
		private $error_handler;
		private $loaded = false;

		private function __construct() {
			$this->error_handler = DLM_Product_Error_Handler::get();
		}

		public static function get() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function error_handler() {
			return $this->error_handler;
		}

		public function load_extensions() {
			if ( $this->loaded ) {
				return;
			}
			$this->loaded = true;

			$registered_extensions = apply_filters( 'dlm_extensions', array() );

			if ( count( $registered_extensions ) > 0 ) {
				add_filter( 'block_local_requests', '__return_false' );
				$this->load_products( $registered_extensions );
			}
		}

		private function load_products( $extensions ) {
			foreach ( $extensions as $extension ) {
				if ( ! is_array( $extension ) ) {
					$extension = array(
						'file'    => $extension,
						'version' => false,
						'name'    => '',
					);
				}

				$product = new DLM_Product( $extension['file'], $extension['version'], $extension['name'] );

				do_action( 'dlm_extensions_action_' . $extension['file'], $extension, $product );

				$this->products[ $extension['file'] ] = $product;
			}
		}

		public function get_products() {
			return $this->products;
		}
	}
}

add_action(
	'admin_init',
	function () {
		DLM_Product_Manager::get()->load_extensions();
	},
	1
);
