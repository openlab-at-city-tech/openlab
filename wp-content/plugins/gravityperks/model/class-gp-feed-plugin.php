<?php

GFForms::include_feed_addon_framework();

abstract class GP_Feed_Plugin extends GFFeedAddOn {

	protected $_min_gravityforms_version;

	public static $perk_class;

	public $perk;

	/**
	 * Get an instance of the class. Should be overridden using the following sample code.
	 *
	 * if( self::$instance == null ) {
	 *     self::$instance = isset ( self::$perk ) ? new self ( new self::$perk ) : new self();
	 * }
	 *
	 * return self::$instance;
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

		if ( ! $this->check_requirements() ) {
			remove_action( 'init', array( $this, 'init' ) );
		}

	}

	public function init() {

		parent::init();

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
