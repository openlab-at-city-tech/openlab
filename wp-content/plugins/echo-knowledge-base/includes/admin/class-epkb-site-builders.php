<?php

/**
 * Various KB Core utility functions
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Site_Builders {

	public $kb_post_types = [];
	const ELEMENTOR_OPTION_NAME = 'epkb_elementor_settings';
	const DIVI_OPTION_NAME      = 'epkb_divi_settings';
	const VC_OPTION_NAME        = 'epkb_vc_settings';
	const WPB_OPTION_NAME       = 'epkb_wpb_settings';
	const BEAVER_OPTION_NAME    = 'epkb_beaver_settings';
	const SITE_ORIGIN_OPTION_NAME    = 'epkb_so_settings';

	function __construct() {
		add_action( 'admin_init', [ $this, 'add_notices' ] );
	}

	/**
	 * Add notices on EPKB admin pages
	 */

	public function add_notices() {

		// show notices only for admins
		if ( function_exists( 'wp_get_current_user' ) && ! current_user_can( 'administrator' ) ) {
			return;
		}

		// check only on KB pages only
		$is_kb_request = EPKB_KB_Handler::is_kb_request();
		if ( ! $is_kb_request ) {
			return;
		}

		// get epkb post types
		$this->get_kb_post_types();

		// try to remove messages about activate post types for all editors
		$this->remove_old_messages();

		// Don't show on new article page
		global $pagenow;
		if ( 'post-new.php' == $pagenow ) {
			return;
		}

		// check elementor
		if ( ! $this->check_elementor_cpt() ) {
			$this->show_builder_notice( esc_html__( 'Elementor', 'echo-knowledge-base' ), '?page=elementor', esc_html__( 'Post types', 'echo-knowledge-base' ), self::ELEMENTOR_OPTION_NAME );
		}

		// check divi
		if ( ! $this->check_divi_cpt() ) {
			$this->show_builder_notice( esc_html__( 'Divi', 'echo-knowledge-base' ), 'admin.php?page=et_divi_options', esc_html__( 'Builder tab', 'echo-knowledge-base' ), self::DIVI_OPTION_NAME );
		}

		// check Visual Composer
		if ( ! $this->check_vc_cpt() ) {
			$this->show_builder_notice( esc_html__( 'Visual Composer', 'echo-knowledge-base' ), 'admin.php?page=vcv-role-manager', esc_html__( 'Role Manager', 'echo-knowledge-base' ), self::VC_OPTION_NAME );
		}

		// check WPBakery
		if ( ! $this->check_wpb_cpt() ) {
			$this->show_builder_notice( esc_html__( 'WPBakery Page Builder', 'echo-knowledge-base' ), 'admin.php?page=vc-roles', esc_html__( 'Role Manager', 'echo-knowledge-base' ), self::WPB_OPTION_NAME );
		}

		// check Beaver
		if ( ! $this->check_beaver_cpt() ) {
			$this->show_builder_notice( esc_html__( 'Beaver Builder', 'echo-knowledge-base' ), 'options-general.php?page=fl-builder-settings#post-types', esc_html__( 'Settings -> Beaver Builder -> Post Types', 'echo-knowledge-base' ), self::BEAVER_OPTION_NAME );
		}

		// check SiteOrigin
		if ( ! $this->check_so_cpt() ) {
			$this->show_builder_notice( esc_html__( 'SiteOrigin	Builder', 'echo-knowledge-base' ), 'options-general.php?page=siteorigin_panels', esc_html__( 'General -> Post Types', 'echo-knowledge-base' ), self::SITE_ORIGIN_OPTION_NAME );
		}
	}

	/**
	 * Show notice on admin EPKB pages
	 *
	 * @param $builder_name
	 * @param $builder_admin_url
	 * @param $place
	 * @param $option_name
	 */
	private function show_builder_notice( $builder_name, $builder_admin_url, $place, $option_name ) {

		// title
		$link       = '<a href="' . esc_url( admin_url( $builder_admin_url ) ) . '" target="_blank">' . esc_html__( 'here', 'echo-knowledge-base' ) . '</a>';
		$title      = esc_html( sprintf( esc_html__( 'Please enable KB Articles for %s.', 'echo-knowledge-base' ), $builder_name ) ) . ' ' . $link;

		// message
		$reason     = esc_html__( 'Ensure that your Knowledge Base name is checked.', 'echo-knowledge-base' );
		$message    = sprintf( esc_html__( 'Please go to the %s settings, and then go to the %s.', 'echo-knowledge-base' ), $builder_name, $place ) . ' ' . $reason;

		EPKB_Admin_Notices::add_ongoing_notice( 'large-notice', $option_name, $message, $title, '<i class="epkbfa epkbfa-exclamation-triangle"></i>' );
	}

	/**
	 * Add post types to class var
	 *
	 */
	private function get_kb_post_types() {
		$this->kb_post_types = [];
		foreach ( get_post_types() as $post_type ) {
			if ( EPKB_KB_Handler::is_kb_post_type( $post_type ) ) {
				$this->kb_post_types[] = $post_type;
			}
		}
	}

	/**
	 * Remove notices if they were added but not need now
	 */
	private function remove_old_messages() {
		EPKB_Admin_Notices::remove_ongoing_notice( self::ELEMENTOR_OPTION_NAME );
		EPKB_Admin_Notices::remove_ongoing_notice( self::DIVI_OPTION_NAME );

		$vc_builder_enabled = self::is_vc_enabled();
		if ( empty( $vc_builder_enabled ) ) {
			EPKB_Admin_Notices::remove_ongoing_notice( self::VC_OPTION_NAME );
		}

		EPKB_Admin_Notices::remove_ongoing_notice( self::WPB_OPTION_NAME );

		$beaver_builder_enabled = self::is_beaver_enabled();
		if ( empty( $beaver_builder_enabled ) ) {
			EPKB_Admin_Notices::remove_ongoing_notice( self::BEAVER_OPTION_NAME );
		}

		$so_builder_enabled = self::is_so_enabled();
		if ( empty( $so_builder_enabled ) ) {
			EPKB_Admin_Notices::remove_ongoing_notice( self::SITE_ORIGIN_OPTION_NAME );
		}

	}

	/**
	 * Check if Elementor is enabled and has activated all epkb post types
	 *
	 * @return bool
	 */
	private function check_elementor_cpt() {

		$builder_enabled = self::is_elementor_enabled();
		if ( empty( $builder_enabled ) ) {
			return true;
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			return true;
		}

		$elementor_cpt_support = get_option( 'elementor_cpt_support', array() );
		if ( $elementor_cpt_support === false ) {
			return true;
		}

		foreach ( $this->kb_post_types as $post_type ) {
			//Check for all KB Post Type
			if ( ! in_array( $post_type, $elementor_cpt_support ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Check if DIVI is enabled and has activated all epkb post types
	 *
	 * @return bool
	 */
	private function check_divi_cpt() {

		$builder_enabled = self::is_divi_enabled();
		if ( empty( $builder_enabled ) ) {
			return true;
		}

		// since DIVI 3.1
		if ( ! function_exists( 'et_builder_get_enabled_builder_post_types' ) ) {
			return true;
		}

		// should always be an array of strings with enabled post types
		$divi_cpt_support = et_builder_get_enabled_builder_post_types();
		if ( empty( $divi_cpt_support ) || ! is_array( $divi_cpt_support ) ) {
			return true;
		}

		foreach ( $this->kb_post_types as $post_type ) {
			// Check for all KB Post Type
			if ( ! in_array( $post_type, $divi_cpt_support ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Check if Visual Composer is enabled and has activated all epkb post types
	 *
	 * @return bool
	 */
	private function check_vc_cpt() {

		$builder_enabled = self::is_vc_enabled();
		if ( empty( $builder_enabled ) ) {
			return true;
		}

		/*
		if ( ! function_exists( 'vchelper' ) ) {
			return true;
		}

		 foreach ( $this->kb_post_types as $post_type ) {

			$post_type_object = get_post_type_object( $post_type );

			// something wrong with post type. This condition should never be true
			if ( empty( $post_type_object ) ) {
				continue;
			}

			// show notice only for users that can edit epkb posts
			if ( ! current_user_can( $post_type_object->cap->create_posts ) ) {
				continue;
			}

			// VC special function
			if ( ! vchelper( 'AccessUserCapabilities' )->isEditorEnabled( $post_type ) ) {
				return false;
			}
		} */

		// show one time notice
		if ( EPKB_Core_Utilities::is_kb_flag_set( 'vc_notice_shown' ) ) {
			return true;
		}

		EPKB_Core_Utilities::add_kb_flag( 'vc_notice_shown' );

		return false;
	}

	/**
	 * Check if WPBakery is enabled and has activated all epkb post types
	 *
	 * @return bool
	 */
	private function check_wpb_cpt() {

		$builder_enabled = self::is_wpb_enabled();
		if ( empty( $builder_enabled ) ) {
			return true;
		}

		if ( ! function_exists( 'vc_editor_post_types' ) ) {
			return true;
		}

		$wpb_cpt_support = vc_editor_post_types();
		if ( empty( $wpb_cpt_support) || ! is_array( $wpb_cpt_support ) ) {
			return true;
		}

		foreach ( $this->kb_post_types as $post_type ) {
			if ( ! in_array( $post_type, $wpb_cpt_support ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if Beaver is enabled and has activated all epkb post types
	 *
	 * @return bool
	 */
	private function check_beaver_cpt() {

		$builder_enabled = self::is_beaver_enabled();
		if ( empty( $builder_enabled ) ) {
			return true;
		}

		/* $beaver_class = 'FLBuilderModel';
		if ( ! class_exists( $beaver_class ) || ! method_exists( $beaver_class, 'get_post_types' ) ) {
			return true;
		} */

		/** @noinspection PhpUndefinedMethodInspection */
		/* $beaver_cpt_support = $beaver_class::get_post_types();
		if ( ! is_array( $beaver_cpt_support ) ) {
			return true;
		}

		foreach ( $this->kb_post_types as $post_type ) {
			if ( ! in_array( $post_type, $beaver_cpt_support ) ) {
				return false;
			}
		} */

		// show one time notice
		if ( EPKB_Core_Utilities::is_kb_flag_set( 'beaver_notice_shown' ) ) {
			return true;
		}

		EPKB_Core_Utilities::add_kb_flag( 'beaver_notice_shown' );

		return false;
	}

	/**
	 * Check if SiteOrigin Page Builder is enabled and has activated all epkb post types
	 *
	 * @return bool
	 */
	private function check_so_cpt() {

		$builder_enabled = self::is_so_enabled();
		if ( empty( $builder_enabled ) ) {
			return true;
		}

		// show one time notice
		if ( EPKB_Core_Utilities::is_kb_flag_set( 'so_notice_shown' ) ) {
			return true;
		}

		EPKB_Core_Utilities::add_kb_flag( 'so_notice_shown' );

		return false;
	}

	public static function is_elementor_enabled() {
		return defined( 'ELEMENTOR_VERSION' );
	}

	public static function is_divi_enabled() {
		return class_exists( 'ET_Theme_Builder_Request' );
	}

	public static function is_wpb_enabled() {
		return defined( 'WPB_VC_VERSION' );
	}

	public static function is_vc_enabled() {
		return defined( 'VCV_VERSION' );
	}

	public static function is_beaver_enabled() {
		return defined( 'FL_BUILDER_LITE' );
	}

	public static function is_so_enabled() {
		return defined( 'SITEORIGIN_PANELS_VERSION' );
	}
}