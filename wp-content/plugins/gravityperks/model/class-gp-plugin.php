<?php

GFForms::include_addon_framework();

abstract class GP_Plugin extends GFAddOn {

	public static $perk_class;

	public $perk;

	/**
	 * Get an instance of the class. Should be overridden using the following sample code.
	 *
	 * if( self::$_instance == null ) {
	 *     self::$_instance = isset ( self::$perk ) ? new self ( new self::$perk ) : new self();
	 * }
	 *
	 * return self::$_instance;
	 */
	public static function get_instance() {
		_doing_it_wrong( __METHOD__, 'This function must be extended. Clay said so.', null );
	}

	public static function includes() { }

	/**
	 * @var array Array to track Perks that are initializing. This is a key piece in preventing recursion caused by
	 *   GFAddOn::meets_minimum_requirements() when Add-ons are required by Perks. Without this, get_instance() will
	 *   recurse as we call GP_Plugin::check_requirements() in __construct().
	 *
	 * @see GFAddOn::meets_minimum_requirements()
	 * @see GP_Plugin::check_requirements()
	 * @see GP_Plugin::__construct
	 */
	static $initializing_perks = array();

	public function __construct( $perk = null ) {

		if ( ! $this->perk ) {
			$this->perk = $perk ? $perk : new GP_Perk( $this->_path, $this );
		}

		if ( isset( self::$initializing_perks[ $this->_slug ] ) ) {
			return $this;
		}

		/* Set flag to prevent potential recursion from GP_Plugin::check_requirements(). */
		self::$initializing_perks[ $this->_slug ] = true;

		/**
		 * While GP_Plugin::check_requirements() runs a risk of recursion, we check it here so we can prevent pre_init()
		 * from running on Perks that have not met all requirements.
		 */
		if ( $this->check_requirements() ) {
			parent::__construct();
		} else {
			if ( ! has_filter( 'init', array( $this, 'disable_init' ) ) ) {
				add_action( 'init', array( $this, 'disable_init' ), 1 );
			}

			/**
			 * GFAddOn::failed_requirements_init() is called in GFAddOn::init_admin() which is bypassed thanks to our
			 * requirements checker.
			 *
			 * That said, it's still useful to call failed_requirements_init() when running GF 2.5 or higher as
			 * regular WP notifications are suppressed on Gravity Forms pages.
			 *
			 * @see GFAddOn::init_admin()
			 * @see GFAddOn::failed_requirements_init()
			 */
			if (
				method_exists( $this, 'failed_requirements_init' )
				&& version_compare( GFForms::$version, '2.5-beta-1', '>=' )
			) {
				$this->failed_requirements_init();
			}
		}

	}

	/**
	 * Prevent plugin from initializing. Use in tandem with GP_Plugin::check_requirements().
	 */
	public function disable_init() {
		remove_action( 'init', array( $this, 'init' ), 10 ); /* GF <=2.4 */
		remove_action( 'init', array( $this, 'init' ), 15 );
	}

	public function init() {

		parent::init();

		/**
		 * Remove row after Perks in plugins tab that Gravity Forms provides since Gravity Perks already checks
		 * requirements, license, etc.
		 */
		remove_action( 'after_plugin_row_' . $this->get_path(), array( $this, 'plugin_row' ), 10 );

		$this->perk->init();

	}

	public function check_requirements() {

		$requirements   = $this->minimum_requirements();
		$min_gf_version = rgars( $requirements, 'gravityforms/version' );

		if ( $min_gf_version ) {
			$this->_min_gravityforms_version = $min_gf_version;
		}

		return $this->perk->check_requirements();

	}

	public function meets_minimum_requirements() {

		$min = is_callable( 'parent::meets_minimum_requirements' ) ? parent::meets_minimum_requirements() : array();

		return array_merge_recursive( $min, $this->perk->check_gf_requirements_plugins_array() );

	}

	public function minimum_requirements() {
		return array();
	}

	public function log( $message, $is_error = false ) {
		if ( $is_error ) {
			$this->log_error( $message );
		} else {
			$this->log_debug( $message );
		}
	}

}
