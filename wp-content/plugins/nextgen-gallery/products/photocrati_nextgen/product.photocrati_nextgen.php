<?php

define( 'NGG_CHANGE_OPTIONS_CAP', 'NextGEN Manage gallery' );

class P_Photocrati_NextGen extends C_Base_Product {

	public $object;

	public static $modules_provided = [
		'photocrati-mvc'                     => 'always',
		'photocrati-ajax'                    => 'always',
		'photocrati-datamapper'              => 'always',
		'photocrati-nextgen_gallery_display' => 'always',
		'photocrati-nextgen_pagination'      => 'always',
		'imagely-legacy_compat'              => 'always',
		'photocrati-nextgen_pro_upgrade'     => 'backend',
		'imagely-displaytype_admin'          => 'always',
		'photocrati-nextgen_basic_album'     => 'always',
		'photocrati-nextgen_admin'           => 'always',
		'photocrati-marketing'               => 'backend',
		'photocrati-nextgen_addgallery_page' => 'backend',
		'photocrati-nextgen_other_options'   => 'backend',
	];

	public function get_modules_provided() {
		return array_keys( self::$modules_provided );
	}

	public function get_modules_to_load() {
		$retval = [];

		foreach ( self::$modules_provided as $module_name => $condition ) {
			switch ( $condition ) {
				case 'always':
					$retval[] = $module_name;
					break;

				// Hack. If this is a photocrati ajax request, is_admin() will evaluate to false. But
				// we probably want to load the module if the ajax request is initiated from a wp-admin page.
				//
				// This only determines which modules to load based on the URL; nonce verification is not necessary here.
				//
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				case ( 'backend' === $condition && ( is_admin() || false !== strpos( $_SERVER['REQUEST_URI'], 'ajax' ) || isset( $_REQUEST['photocrati_ajax'] ) ) ):
					$retval[] = $module_name;
					break;

				case 'frontend':
					if ( ! is_admin() ) {
						$retval[] = $module_name;
					}
					break;
			}
		}

		$retval = apply_filters( 'ngg_get_modules_to_load', $retval, self::$modules_provided );

		return $retval;
	}

	public function define(
		$id = 'pope-product',
		$name = 'Pope Product',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = false
	) {
		parent::define(
			'photocrati-nextgen',
			'NextGen Gallery',
			'NextGen Gallery',
			NGG_PLUGIN_VERSION,
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);

		$module_path = implode( DIRECTORY_SEPARATOR, [ __DIR__, 'modules' ] );
		$this->get_registry()->set_product_module_path( $this->module_id, $module_path );
		foreach ( $this->get_modules_to_load() as $module_name ) {
			$this->_get_registry()->load_module( $module_name );
		}

		include_once 'class.nextgen_product_installer.php';
		\Imagely\NGG\Util\Installer::add_handler( $this->module_id, 'C_NextGen_Product_Installer' );
	}
}

new P_Photocrati_NextGen();
