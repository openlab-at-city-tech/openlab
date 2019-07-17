<?php
/**
 * Author:  Andrei Baicus <andrei@themeisle.com>
 * On:      21/06/2018
 *
 * @package    themeisle-onboarding
 * @soundtrack Guess Things Happen That Way - Johnny Cash
 */

/**
 * Class Themeisle_OB_Content_Importer
 *
 * @package themeisle-onboarding
 */
class Themeisle_OB_Content_Importer {
	private $logger = null;

	/**
	 * Themeisle_OB_Content_Importer constructor.
	 */
	public function __construct() {
		$this->load_importer();
		$this->logger = Themeisle_OB_WP_Import_Logger::get_instance();
	}

	/**
	 * Import Remote XML file.
	 *
	 * @param WP_REST_Request $request the async request.
	 *
	 * @return WP_REST_Response
	 */
	public function import_remote_xml( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->logger->log( 'No manage_options permissions' );

			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_permission_err_1',
					'success' => false,
				)
			);
		}

		do_action( 'themeisle_ob_before_xml_import' );

		$params           = $request->get_body_params();
		$body             = $params['data'];
		$content_file_url = $body['contentFile'];

		if ( empty( $content_file_url ) ) {
			$this->logger->log( "No content file to import at url {$content_file_url}" );

			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_remote_err_1',
					'success' => false,
				)
			);
		}

		if ( ! isset( $body['source'] ) || empty( $body['source'] ) ) {
			$this->logger->log( 'No source defined for the import.' );

			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_remote_err_2',
					'success' => false,
				)
			);
		}

		set_time_limit( 0 );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		if ( $body['source'] === 'remote' ) {
			$this->logger->log( 'Saving remote XML', 'progress' );
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			global $wp_filesystem;
			WP_Filesystem();
			$content_file      = $wp_filesystem->get_contents( $content_file_url );
			$content_file_path = $this->save_xhr_return_path( $content_file );
			$this->logger->log( "Saved remote XML at path {$content_file_path}.", 'success' );
		} else {
			$this->logger->log( 'Using local XML.', 'success' );
			$content_file_path = $content_file_url;
		}

		$this->logger->log( 'Starting content import...', 'progress' );
		$import_status = $this->import_file( $content_file_path, $body );

		if ( is_wp_error( $import_status ) ) {
			$this->logger->log( "Import crashed with message: {$import_status->get_error_message()}" );

			return new WP_REST_Response(
				array(
					'data'    => $import_status,
					'success' => false,
				)
			);
		}

		if ( $body['source'] === 'remote' ) {
			unlink( $content_file_path );
		}

		do_action( 'themeisle_ob_after_xml_import' );

		$this->logger->log( 'Busting elementor cache', 'progress' );
		$this->maybe_bust_elementor_cache();

		// Set front page.
		if ( isset( $body['frontPage'] ) ) {
			$frontpage_id = $this->setup_front_page( $body['frontPage'] );
		}
		do_action( 'themeisle_ob_after_front_page_setup' );

		// Set shop pages.
		if ( isset( $body['shopPages'] ) ) {
			$this->setup_shop_pages( $body['shopPages'] );
		}
		do_action( 'themeisle_ob_after_shop_pages_setup' );

		if ( empty( $frontpage_id ) ) {
			$this->logger->log( 'No front page ID.' );

			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_front_page_id_err_1',
					'success' => false,
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success'      => true,
				'frontpage_id' => $frontpage_id,
			)
		);
	}

	/**
	 * Save remote XML file and return the file path.
	 *
	 * @param string $content the content.
	 *
	 * @return string
	 */
	public function save_xhr_return_path( $content ) {
		$wp_upload_dir = wp_upload_dir( null, false );
		$file_path     = $wp_upload_dir['basedir'] . '/themeisle-demo-import.xml';
		ob_start();
		echo $content;
		$result = ob_get_clean();
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		global $wp_filesystem;
		WP_Filesystem();
		$wp_filesystem->put_contents( $file_path, $result );

		return $file_path;
	}

	/**
	 * Set up front page options by `post_name`.
	 *
	 * @param array $args the front page array.
	 *
	 * @return int
	 */
	public function setup_front_page( $args ) {
		if ( ! is_array( $args ) ) {
			return;
		}

		if ( $args['front_page'] === null && $args['blog_page'] === null ) {
			$this->logger->log( 'No front page to set up.', 'success' );

			return;
		}

		update_option( 'show_on_front', 'page' );

		if ( isset( $args['front_page'] ) && $args['front_page'] !== null ) {
			$front_page_obj = get_page_by_path( $args['front_page'] );
			if ( isset( $front_page_obj->ID ) ) {
				update_option( 'page_on_front', $front_page_obj->ID );
			}
		}

		if ( isset( $args['blog_page'] ) && $args['blog_page'] !== null ) {
			$blog_page_obj = get_page_by_path( $args['blog_page'] );
			if ( isset( $blog_page_obj->ID ) ) {
				update_option( 'page_for_posts', $blog_page_obj->ID );
			}
		}

		if ( isset( $front_page_obj->ID ) ) {
			$this->logger->log( "Front page set up with id: {$front_page_obj->ID}.", 'success' );

			return $front_page_obj->ID;
		}
	}

	/**
	 * Set up shop pages options by `post_name`.
	 *
	 * @param array $pages the shop pages array.
	 */
	public function setup_shop_pages( $pages ) {
		$this->logger->log( 'Setting up shop page.', 'progress' );
		if ( ! class_exists( 'WooCommerce' ) ) {
			$this->logger->log( 'No WooCommerce.', 'success' );

			return;
		}
		if ( ! is_array( $pages ) ) {
			$this->logger->log( 'No Shop Pages.', 'success' );

			return;
		}
		foreach ( $pages as $option_id => $slug ) {
			if ( ! empty( $slug ) ) {
				$page_object = get_page_by_path( $slug );
				if ( isset( $page_object->ID ) ) {
					update_option( $option_id, $page_object->ID );
				}
			}
		}
		$this->logger->log( 'Shop pages set up.', 'success' );
	}

	/**
	 * Maybe bust cache for elementor plugin.
	 */
	private function maybe_bust_elementor_cache() {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			wp_remote_post(
				esc_url( admin_url( 'admin-ajax.php' ) ),
				array(
					'body' => array(
						'action' => 'elementor_clear_cache',
					),
				)
			);
		}
	}

	/**
	 * Import file
	 *
	 * @param string $file_path the file path to import.
	 * @param array  $req_body  the request body to be passed to the alterator.
	 *
	 * @return WP_Error|true
	 */
	public function import_file( $file_path, $req_body = array() ) {
		if ( empty( $file_path ) || ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
			return new WP_Error( 'ti__ob_content_err_1', 'No content file' );
		}

		require_once 'helpers/class-themeisle-ob-importer-alterator.php';
		$alterator = new Themeisle_OB_Importer_Alterator( $req_body );

		$importer = new Themeisle_OB_WP_Import();
		$result   = $importer->import( $file_path );

		return $result;
	}

	/**
	 * Load the importer.
	 */
	private function load_importer() {
		require_once dirname( __FILE__ ) . '/helpers/wp-importer/class-themeisle-ob-wordpress-import.php';
		require_once dirname( __FILE__ ) . '/helpers/wp-importer/parsers.php';
	}

}
