<?php

/*
  Plugin Name: CM Tooltip Glossary
  Plugin URI: https://www.cminds.com/
  Description:  Easily create a Glossary, Encyclopedia or Dictionary of your custom terms. Plugin parses posts and pages searching for defined glossary terms and adds links to the glossary term page. Hovering over the link shows a tooltip with the definition.
  Version: 3.5.6
  Author: CreativeMindsSolutions
  Author URI: https://www.cminds.com/
 */

if ( !ini_get( 'max_execution_time' ) || ini_get( 'max_execution_time' ) < 300 ) {
	/*
	 * Setup the high max_execution_time to avoid timeouts during lenghty operations like importing big glossaries,
	 * or rebuilding related articles index
	 */
	ini_set( 'max_execution_time', 300 );

	$disabled = explode( ',', ini_get( 'disable_functions' ) );
	if ( !in_array( 'set_time_limit', $disabled ) ) {
		set_time_limit( 300 );
	}
}

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class file.
 * What it does:
 * - checks which part of the plugin should be affected by the query frontend or backend and passes the control to the right controller
 * - manages installation
 * - manages uninstallation
 * - defines the things that should be global in the plugin scope (settings etc.)
 * @author CreativeMindsSolutions - Marcin Dudek
 */
class CMTooltipGlossary {

	public static $calledClassName;
	protected static $instance = NULL;

	/**
	 * Main Instance
	 *
	 * Insures that only one instance of class exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AKRSubscribeNotifications
	 */
	public static function instance() {
		$class = __CLASS__;
		if ( !isset( self::$instance ) && !( self::$instance instanceof $class ) ) {
			self::$instance = new $class;
		}
		return self::$instance;
	}

	public function __construct() {
		if ( empty( self::$calledClassName ) ) {
			self::$calledClassName = __CLASS__;
		}

		self::setupConstants();

		/*
		 * Shared
		 */
		include_once CMTT_PLUGIN_DIR . '/shared/cm-tooltip-glossary-shared.php';
		$CMTooltipGlossarySharedInstance = CMTooltipGlossaryShared::instance();

		include_once CMTT_PLUGIN_DIR . '/package/cminds-free.php';

		if ( is_admin() ) {
			/*
			 * Backend
			 */
			include_once CMTT_PLUGIN_DIR . '/backend/cm-tooltip-glossary-backend.php';
			$CMTooltipGlossaryBackendInstance = CMTooltipGlossaryBackend::instance();
		} else {
			/*
			 * Frontend
			 */
			include_once CMTT_PLUGIN_DIR . '/frontend/cm-tooltip-glossary-frontend.php';
			$CMTooltipGlossaryFrontendInstance = CMTooltipGlossaryFrontend::instance();
		}
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.1
	 * @return void
	 */
	private static function setupConstants() {
		/**
		 * Define Plugin Version
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_VERSION' ) ) {
			define( 'CMTT_VERSION', '3.3.0' );
		}

		/**
		 * Define Plugin Directory
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_PLUGIN_DIR' ) ) {
			define( 'CMTT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Define Plugin URL
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_PLUGIN_URL' ) ) {
			define( 'CMTT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Define Plugin File Name
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_PLUGIN_FILE' ) ) {
			define( 'CMTT_PLUGIN_FILE', __FILE__ );
		}

		/**
		 * Define Plugin Slug name
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_SLUG_NAME' ) ) {
			define( 'CMTT_SLUG_NAME', 'cm-tooltip-glossary' );
		}

		/**
		 * Define Plugin name
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_NAME' ) ) {
			define( 'CMTT_NAME', 'CM Tooltip Glossary' );
		}

		/**
		 * Define Plugin canonical name
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_CANONICAL_NAME' ) ) {
			define( 'CMTT_CANONICAL_NAME', 'CM Tooltip Glossary' );
		}

		/**
		 * Define Plugin license name
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_LICENSE_NAME' ) ) {
			define( 'CMTT_LICENSE_NAME', 'CM Tooltip Glossary' );
		}

		/**
		 * Define Plugin basename
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_PLUGIN' ) ) {
			define( 'CMTT_PLUGIN', plugin_basename( __FILE__ ) );
		}

		/**
		 * Define Plugin URL
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_URL' ) ) {
			define( 'CMTT_URL', 'https://www.cminds.com/store/tooltipglossary/' );
		}

		/**
		 * Define Plugin release notes url
		 *
		 * @since 1.0
		 */
		if ( !defined( 'CMTT_RELEASE_NOTES' ) ) {
			define( 'CMTT_RELEASE_NOTES', 'https://tooltip.cminds.com/cm-tooltip-free-edition-release-notes/' );
		}
	}

	public static function _install() {
		self::checkPHPversion();

		CMTooltipGlossaryShared::tryGenerateGlossaryIndexPage();
		CMTooltipGlossaryShared::tryResetOldOptions();
		self::_resetOptions();
	}

	public static function checkPHPversion() {
		/*
		 * Check for required PHP version (in case we'll need it someday)
		 */
//        if( version_compare(PHP_VERSION, '5.3', '<') )
//        {
//            $message = sprintf('<p>The <strong>%s</strong> plugin requires PHP version 5.3 or greater. You’re still on %s.</p>', CMTT_NAME, PHP_VERSION);
//            deactivate_plugins(CMTT_PLUGIN);
//            wp_die($message, 'Plugin Activation Error', array('response' => 200, 'back_link' => TRUE));
//        }
	}

	private static function _resetOptions() {
		update_option( 'cmtt_afterActivation', 1 );

		update_option( 'cmtt_tooltipIsClickable', 0 );
		update_option( 'cmtt_glossary_addBackLink', 0 );
		update_option( 'cmtt_glossary_backLinkText', '&laquo; Back to Glossary Index' );
		update_option( 'cmtt_glossary_backLinkBottomText', '&laquo; Back to Glossary Index' );
		update_option( 'cmtt_glossaryFilterTooltipA', 0 );
	}

	public static function _uninstall() {
		return;
	}

	public function registerAjaxFunctions() {
		return;
	}

}

/**
 * The main function responsible for returning the one true plugin class
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $marcinPluginPrototype = MarcinPluginPrototypePlugin(); ?>
 *
 * @since 1.0
 * @return object The one true CM_Micropayment_Platform Instance
 */
function CMTooltipGlossaryInit() {
	return CMTooltipGlossary::instance();
}

$CMTooltipGlossary = CMTooltipGlossaryInit();

register_activation_hook( __FILE__, array( 'CMTooltipGlossary', '_install' ) );
register_deactivation_hook( __FILE__, array( 'CMTooltipGlossary', '_uninstall' ) );
