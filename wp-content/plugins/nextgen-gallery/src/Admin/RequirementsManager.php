<?php

namespace Imagely\NGG\Admin;

use Imagely\NGG\Admin\Notifications\Manager as NotificationsManager;

class RequirementsManager {

	private static $instance = null;

	protected $requirements  = [];
	protected $groups        = [];
	protected $notifications = [];

	public function __construct() {
		$this->set_initial_groups();
	}

	/**
	 * @return RequirementsManager
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new RequirementsManager();
		}
		return self::$instance;
	}

	protected function set_initial_groups() {
		// Requirements can be added with any group key desired but only registered groups will be displayed.
		$this->groups = apply_filters(
			'ngg_admin_requirements_manager_groups',
			[
				'phpext'   => esc_html__( 'NextGen Gallery requires the following PHP extensions to function correctly. Please contact your hosting provider or systems admin and ask them for assistance:', 'nggallery' ),
				'phpver'   => esc_html__( 'NextGen Gallery has degraded functionality because of your PHP version. Please contact your hosting provider or systems admin and ask them for assistance:', 'nggallery' ),
				'dirperms' => esc_html__( 'NextGen Gallery has found an issue trying to access the following files or directories. Please ensure the following locations have the correct permissions:', 'nggallery' ),
			]
		);
	}

	public static function register_requirements() {
		$manager = self::get_instance();
		$manager->add(
			'nextgen_data_sanitation',
			'phpext',
			function () {
				return class_exists( 'DOMDocument' );
			},
			[ 'message' => esc_html__( 'XML is strongly encouraged for safely editing image data', 'nggallery' ) ]
		);

		$manager->add(
			'nextgen_data_gd_requirement',
			'phpext',
			function () {
				return function_exists( 'gd_info' );
			},
			[
				'message'     => esc_html__( 'GD is required for generating image thumbnails, resizing images, and generating watermarks', 'nggallery' ),
				'dismissable' => false,
			]
		);

		$manager->add(
			'nextgen_data_ctypes_requirement',
			'phpext',
			function () {
				return function_exists( 'ctype_lower' );
			},
			[
				'message'     => esc_html__( 'ctype methods are required for securing user submitted data', 'nggallery' ),
				'dismissable' => false,
			]
		);
	}

	/**
	 * @param string   $name Unique notification ID
	 * @param string   $group Choose one of phpext | phpver | dirperms
	 * @param callable $callback Method that determines whether the notification should display
	 * @param array    $data Possible keys: className, message, dismissable
	 */
	public function add( $name, $group, $callback, $data ) {
		$this->requirements[ $group ][ $name ] = new RequirementsNotice( $name, $callback, $data );
	}

	/**
	 * @param string $name
	 */
	public function remove( $name ) {
		unset( $this->notifications[ $name ] );
	}

	public function create_notification() {
		foreach ( $this->groups as $groupID => $groupLabel ) {

			if ( empty( $this->requirements[ $groupID ] ) ) {
				continue;
			}

			$dismissable = true;
			$notices     = [];

			foreach ( $this->requirements[ $groupID ] as $key => $requirement ) {
				$passOrFail = $requirement->run_callback();

				if ( ! $passOrFail ) {
					// If any of the notices can't be dismissed then all notices in that group can't be dismissed.
					if ( ! $requirement->is_dismissable() ) {
						// Add important notices to the beginning of the list.
						$dismissable = false;
						array_unshift( $notices, $requirement );
					} else {
						$notices[] = $requirement;
					}
				}
			}

			// Don't display empty group notices.
			if ( empty( $notices ) ) {
				continue;
			}

			// Generate the combined message for this group.
			$message = '<p>' . $this->groups[ $groupID ] . '</p><ul>';
			foreach ( $notices as $requirement ) {
				// Make non-dismissable notifications bold.
				$string   = $requirement->is_dismissable() ? $requirement->get_message() : '<strong>' . $requirement->get_message() . '</strong>';
				$message .= '<li>' . $string . '</li>';
			}
			$message .= '</ul>';

			// Generate the notice object.
			$name   = 'ngg_requirement_notice_' . $groupID . '_' . md5( $message );
			$notice = new RequirementsNotice(
				$name,
				'__return_true',
				[
					'dismissable' => $dismissable,
					'message'     => $message,
				]
			);
			NotificationsManager::get_instance()->add( $name, $notice );
		}
	}
}
