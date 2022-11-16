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

		if ( ! $this->perk ) {
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

		/**
		 * Exporting happens prior to init, hence the need to add these hooks during pre_init.
		 *
		 * It is worth noting that theme's functions.php will not have fired at this point, so the conditional to
		 * check if exporting feeds is enabled is in the individual callbacks below.
		 */
		add_filter( 'gform_export_form', array( $this, 'export_feeds' ) );
		add_action( 'gform_forms_post_import', array( $this, 'import_feeds' ) );

	}

	/**
	 * Prevent plugin from initializing if requirements are not met.
	 */
	public function disable_init_when_requirements_unmet() {
		if ( ! $this->check_requirements() ) {
			$priority = GravityPerks::is_gf_version_gte( '2.5-beta-2' ) ? 15 : 10;
			remove_action( 'init', array( $this, 'init' ), $priority );
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

	/**
	 * Filter whether or not feed exporting/importing is enabled for this Perk.
	 *
	 * @return boolean
	 */
	public function is_feed_exporting_enabled() {
		return apply_filters( 'gravityperks_export_feeds_' . $this->_slug, true );
	}

	/**
	 * Export feeds for the current Perk during form export.
	 *
	 * Note, this is not something Gravity Forms currently does by default.
	 *
	 * @param $form array
	 *
	 * @return array
	 */
	public function export_feeds( $form ) {
		if ( ! $this->is_feed_exporting_enabled() ) {
			return $form;
		}

		if ( ! isset( $form['feeds'] ) ) {
			$form['feeds'] = array();
		}

		$feeds = $this->get_feeds( $form['id'] );

		if ( $feeds ) {
			$form['feeds'][ $this->_slug ] = $feeds;
		}

		return $form;
	}

	/**
	 * Import feeds during form import if they are present in the JSON.
	 *
	 * Note, this is not something Gravity Forms currently does by default.
	 *
	 * @param $forms array
	 */
	public function import_feeds( $forms ) {
		if ( ! $this->is_feed_exporting_enabled() ) {
			return $forms;
		}

		foreach ( $forms as $form ) {
			if ( ! rgars( $form, 'feeds/' . $this->_slug ) ) {
				continue;
			}

			foreach ( $form['feeds'][ $this->_slug ] as $feed ) {
				$this->insert_feed( $form['id'], $feed['is_active'], $feed['meta'] );
			}

			// Remove feeds object if no other feeds are defined.
			if ( empty( $form['feeds'] ) ) {
				unset( $form['feeds'] );
			}

			GFAPI::update_form( $form );
		}
	}

}
