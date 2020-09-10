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

	public function __construct( $perk = null ) {

		if( ! $this->perk ) {
			$this->perk = $perk ? $perk : new GP_Perk( $this->_path, $this );
		}

		parent::__construct();

	}

	public function pre_init() {

		parent::pre_init();

		// Since pre_init() is called from the __construct() and checking for add-on-specific requirements will call the
		// constructor again, we can't check for requirements in pre_init(). Recursion is coming for us all.
		if ( ! has_filter( 'init', array( $this, 'disable_init_when_requirements_unmet' ) ) ) {
			add_action( 'init', array( $this, 'disable_init_when_requirements_unmet' ), 1 );
		}

	}

	/**
	 * Prevent plugin from initializing if requirements are not met.
	 */
	public function disable_init_when_requirements_unmet() {
		if ( ! $this->check_requirements() ) {
			remove_action( 'init', array( $this, 'init' ) );
		}
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

		$requirements = $this->minimum_requirements();

		if ($min_gf_version = rgars($requirements, 'gravityforms/version')) {
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
		if( $is_error ) {
			$this->log_error( $message );
		} else {
			$this->log_debug( $message );
		}
	}

}
