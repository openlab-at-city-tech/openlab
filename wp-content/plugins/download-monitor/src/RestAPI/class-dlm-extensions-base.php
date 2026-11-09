<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Local extensions catalog for the Lite-only fallback (no DLM Pro active).
 * Ported 1:1 from Modula_Extensions_Base.
 */
class DLM_Extensions_Base {

	private static $instance;

	/**
	 * @var array
	 */
	public $extensions = array();

	private $active_extensions = 'dlm_pro_active_extensions';
	private $current_plan      = 'dlm_pro_current_plan';

	private static $active_extensions_cache = array();

	/**
	 * @var array
	 */
	private $plan_map = array();

	public function __construct() {
		$this->create_plan_map();
		add_action( 'init', array( $this, 'add_default_extensions' ) );
	}

	private function create_plan_map() {
		$free = array();

		$basic = array(
			'dlm-enhanced-metrics',
			'dlm-captcha',
		);

		$popular = array_merge(
			$basic,
			array(
				'dlm-email-notification',
				'dlm-csv-importer',
				'dlm-csv-exporter',
				'dlm-page-addon',
				'dlm-downloading-page',
				'dlm-amazon-s3',
				'dlm-google-drive',
			)
		);

		$complete = array_merge(
			$popular,
			array(
				'dlm-advanced-access-manager',
				'dlm-ninja-forms',
				'dlm-gravity-forms',
				'dlm-mailchimp-lock',
				'dlm-email-lock',
				'dlm-wpforms-lock',
				'dlm-cf7-lock',
			)
		);

		$this->plan_map = array(
			'free'     => $free,
			'basic'    => $basic,
			'popular'  => $popular,
			'complete' => $complete,
		);
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) || ! ( self::$instance instanceof DLM_Extensions_Base ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function add_default_extensions() {
		$this->extensions = array(
			'dlm-enhanced-metrics'        => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Enhanced Metrics', 'download-monitor' ),
				'slug'        => 'dlm-enhanced-metrics',
				'description' => __( 'Deeper analytics and reporting on your downloads.', 'download-monitor' ),
			),
			'dlm-captcha'                 => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Anti-spam (Captcha)', 'download-monitor' ),
				'slug'        => 'dlm-captcha',
				'description' => __( 'Protect your downloads from bots with reCAPTCHA or Turnstile.', 'download-monitor' ),
			),
			'dlm-email-notification'      => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Email Notification', 'download-monitor' ),
				'slug'        => 'dlm-email-notification',
				'description' => __( 'Send email notifications whenever a file is downloaded.', 'download-monitor' ),
			),
			'dlm-csv-importer'            => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'CSV Importer', 'download-monitor' ),
				'slug'        => 'dlm-csv-importer',
				'description' => __( 'Bulk-import downloads from a CSV file.', 'download-monitor' ),
			),
			'dlm-csv-exporter'            => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'CSV Exporter', 'download-monitor' ),
				'slug'        => 'dlm-csv-exporter',
				'description' => __( 'Export your download data and logs as CSV.', 'download-monitor' ),
			),
			'dlm-page-addon'              => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Document Library Manager (Page Addon)', 'download-monitor' ),
				'slug'        => 'dlm-page-addon',
				'description' => __( 'Easily show off your downloads in a clean table or stylish grid.', 'download-monitor' ),
			),
			'dlm-downloading-page'        => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Downloading Page', 'download-monitor' ),
				'slug'        => 'dlm-downloading-page',
				'description' => __( 'Show a dedicated "please wait" page while a download starts.', 'download-monitor' ),
			),
			'dlm-amazon-s3'               => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Amazon S3', 'download-monitor' ),
				'slug'        => 'dlm-amazon-s3',
				'description' => __( 'Serve your downloadable files directly from an Amazon S3 bucket.', 'download-monitor' ),
			),
			'dlm-google-drive'            => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Google Drive', 'download-monitor' ),
				'slug'        => 'dlm-google-drive',
				'description' => __( 'Serve your downloadable files directly from Google Drive.', 'download-monitor' ),
			),
			'dlm-advanced-access-manager' => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Rule-Based Access Management', 'download-monitor' ),
				'slug'        => 'dlm-advanced-access-manager',
				'description' => __( 'Fine-grained rules for who can access which downloads.', 'download-monitor' ),
			),
			'dlm-ninja-forms'             => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Ninja Forms Lock', 'download-monitor' ),
				'slug'        => 'dlm-ninja-forms',
				'description' => __( 'Require a Ninja Forms submission before granting access to a download.', 'download-monitor' ),
			),
			'dlm-gravity-forms'           => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Gravity Forms Lock', 'download-monitor' ),
				'slug'        => 'dlm-gravity-forms',
				'description' => __( 'Require a Gravity Forms submission before granting access to a download.', 'download-monitor' ),
			),
			'dlm-mailchimp-lock'          => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'MailChimp Lock', 'download-monitor' ),
				'slug'        => 'dlm-mailchimp-lock',
				'description' => __( 'Require visitors to subscribe to a Mailchimp audience before downloading.', 'download-monitor' ),
			),
			'dlm-email-lock'              => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Email Lock', 'download-monitor' ),
				'slug'        => 'dlm-email-lock',
				'description' => __( 'Require visitors to submit their email address before they can download a file.', 'download-monitor' ),
			),
			'dlm-wpforms-lock'            => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'WPForms Form Lock', 'download-monitor' ),
				'slug'        => 'dlm-wpforms-lock',
				'description' => __( 'Require a WPForms submission before granting access to a download.', 'download-monitor' ),
			),
			'dlm-cf7-lock'                => array(
				'available'   => false,
				'enabled'     => false,
				'name'        => __( 'Contact Form 7 Lock', 'download-monitor' ),
				'slug'        => 'dlm-cf7-lock',
				'description' => __( 'Require a Contact Form 7 submission before granting access to a download.', 'download-monitor' ),
			),
		);
	}

	/**
	 * @return array
	 */
	public function get_extensions() {
		$active_extensions = get_option( $this->active_extensions, array() );
		$current_plan      = 'free';

		$this->update_extension_status( $active_extensions, $current_plan );
		$this->sort_extensions_by_complete_order();

		$divider_slugs      = $this->find_divider_positions( $current_plan );
		$ordered_extensions = $this->build_ordered_extensions();
		$this->extensions   = $this->insert_dividers( $ordered_extensions, $divider_slugs );

		return $this->extensions;
	}

	/**
	 * @param array  $active_extensions
	 * @param string $current_plan
	 */
	private function update_extension_status( $active_extensions, $current_plan ) {
		foreach ( $this->extensions as $extension => $data ) {
			$this->extensions[ $extension ]['enabled']   = in_array( $extension, $active_extensions, true );
			$this->extensions[ $extension ]['available'] = false;

			if ( $current_plan && isset( $this->plan_map[ $current_plan ] ) ) {
				if ( in_array( $extension, $this->plan_map[ $current_plan ], true ) ) {
					$this->extensions[ $extension ]['available'] = true;
				}
			}
		}
	}

	private function sort_extensions_by_complete_order() {
		$complete_order = isset( $this->plan_map['complete'] ) ? array_values( $this->plan_map['complete'] ) : array();
		$index_map      = array_flip( $complete_order );

		uasort(
			$this->extensions,
			function ( $a, $b ) use ( $index_map ) {
				$a_in = isset( $index_map[ $a['slug'] ] );
				$b_in = isset( $index_map[ $b['slug'] ] );

				if ( $a_in && $b_in ) {
					return $index_map[ $a['slug'] ] - $index_map[ $b['slug'] ];
				}
				if ( $a_in ) {
					return -1;
				}
				if ( $b_in ) {
					return 1;
				}

				if ( $a['available'] !== $b['available'] ) {
					return intval( $b['available'] ) - intval( $a['available'] );
				}

				return strcmp( $a['slug'], $b['slug'] );
			}
		);
	}

	/**
	 * @return array
	 */
	private function get_plan_upgrade_orders() {
		return array(
			'free'     => array( 'basic', 'popular', 'complete' ),
			'basic'    => array( 'popular', 'complete' ),
			'popular'  => array( 'complete' ),
			'complete' => array(),
		);
	}

	/**
	 * @param string $current_plan
	 *
	 * @return array
	 */
	private function find_divider_positions( $current_plan ) {
		$plan_orders   = $this->get_plan_upgrade_orders();
		$divider_slugs = array();

		if ( empty( $plan_orders[ $current_plan ] ) ) {
			return $divider_slugs;
		}

		foreach ( $plan_orders[ $current_plan ] as $next_plan ) {
			if ( empty( $this->plan_map[ $next_plan ] ) ) {
				continue;
			}

			$next_plan_order = array_values( $this->plan_map[ $next_plan ] );
			$exclude_order   = $this->get_lower_tier_map( $next_plan );

			$first_unique_slug = $this->find_first_unique_extension( $next_plan_order, $exclude_order );
			if ( $first_unique_slug ) {
				$divider_slugs[ $first_unique_slug ] = array(
					'divider_key' => 'divider-before-' . $first_unique_slug,
					'plan'        => $next_plan,
				);
			}
		}

		return $divider_slugs;
	}

	/**
	 * Extensions already unlocked by the tier directly below $next_plan —
	 * used to find the first NEW extension a given tier unlocks.
	 *
	 * @param string $next_plan
	 *
	 * @return array
	 */
	private function get_lower_tier_map( $next_plan ) {
		$order = array( 'basic', 'popular', 'complete' );
		$index = array_search( $next_plan, $order, true );

		if ( false === $index || 0 === $index ) {
			return array();
		}

		$lower_plan = $order[ $index - 1 ];

		return isset( $this->plan_map[ $lower_plan ] ) ? array_values( $this->plan_map[ $lower_plan ] ) : array();
	}

	/**
	 * @param array $next_plan_order
	 * @param array $exclude_order
	 *
	 * @return string|null
	 */
	private function find_first_unique_extension( $next_plan_order, $exclude_order ) {
		foreach ( $next_plan_order as $next_slug ) {
			if ( 0 === strpos( $next_slug, 'divider-' ) ) {
				continue;
			}

			if ( isset( $this->extensions[ $next_slug ] ) && ! in_array( $next_slug, $exclude_order, true ) ) {
				return $next_slug;
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	private function build_ordered_extensions() {
		$complete_order     = isset( $this->plan_map['complete'] ) ? array_values( $this->plan_map['complete'] ) : array();
		$ordered_extensions = array();

		foreach ( $complete_order as $slug ) {
			if ( $this->is_divider_string( $slug ) ) {
				continue;
			}

			if ( isset( $this->extensions[ $slug ] ) ) {
				$ordered_extensions[ $slug ] = $this->extensions[ $slug ];
			}
		}

		foreach ( $this->extensions as $slug => $data ) {
			if ( ! isset( $ordered_extensions[ $slug ] ) ) {
				$ordered_extensions[ $slug ] = $data;
			}
		}

		return $ordered_extensions;
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	private function is_divider_string( $slug ) {
		return 0 === strpos( $slug, 'divider-' );
	}

	/**
	 * @param array $ordered_extensions
	 * @param array $divider_slugs
	 *
	 * @return array
	 */
	private function insert_dividers( $ordered_extensions, $divider_slugs ) {
		$extensions_with_divider = array();

		foreach ( $ordered_extensions as $slug => $ext ) {
			if ( isset( $divider_slugs[ $slug ] ) ) {
				$divider_data = $divider_slugs[ $slug ];
				$extensions_with_divider[ $divider_data['divider_key'] ] = array(
					'is_divider' => true,
					'slug'       => $divider_data['divider_key'],
					'plan'       => $divider_data['plan'],
					'url'        => 'https://download-monitor.com/pricing/?utm_source=dlm-pro&utm_medium=extensions&utm_campaign=upgrade-to-' . $divider_data['plan'],
				);
			}
			$extensions_with_divider[ $slug ] = $ext;
		}

		return $extensions_with_divider;
	}

	/**
	 * @return array
	 */
	public function get_active_extensions() {
		if ( empty( self::$active_extensions_cache ) ) {
			self::$active_extensions_cache = get_option( $this->active_extensions, array() );
		}
		return self::$active_extensions_cache;
	}

	/**
	 * @param string $extension
	 *
	 * @return bool
	 */
	public function extension_enabled( $extension ) {
		$active_extensions = $this->get_active_extensions();
		return in_array( $extension, $active_extensions, true );
	}

	/**
	 * @param string $addon
	 *
	 * @return bool
	 */
	public function is_upgradable_addon( $addon = null ) {
		if ( ! $addon ) {
			return false;
		}

		$current_plan = get_option( $this->current_plan, 'free' );
		if ( ! isset( $this->plan_map[ $current_plan ] ) ) {
			$current_plan = 'free';
		}

		if ( ! defined( 'DLM_PRO_VERSION' ) ) {
			return true;
		}

		if ( 'dlm-pro' === $addon ) {
			return false;
		}

		$owned_extensions = isset( $this->plan_map[ $current_plan ] ) ? $this->plan_map[ $current_plan ] : array();

		return ! in_array( $addon, $owned_extensions, true );
	}
}
