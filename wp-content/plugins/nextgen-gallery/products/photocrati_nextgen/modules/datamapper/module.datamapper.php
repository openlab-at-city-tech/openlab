<?php

class M_DataMapper extends C_Base_Module {

	public $object;

	public function define(
		$id = 'pope-module',
		$name = 'Pope Module',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = false
	) {
		parent::define(
			'photocrati-datamapper',
			'DataMapper',
			'Provides a database abstraction layer following the DataMapper pattern',
			'3.1.19',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public function _register_adapters() {
		$this->get_registry()->add_adapter( 'I_Component_Factory', 'A_DataMapper_Factory' );
	}

	/**
	 * Deserializes data
	 *
	 * @deprecated Used only by the Pro Lightbox
	 * @param string $value
	 * @return mixed
	 * @throws Exception
	 */
	public static function unserialize( $value ) {
		return \Imagely\NGG\Util\Serializable::unserialize( $value );
	}

	/**
	 * Serializes the data
	 *
	 * @deprecated Used only by the Pro Lightbox
	 * @param mixed $value
	 * @return string
	 */
	public static function serialize( $value ) {
		return \Imagely\NGG\Util\Serializable::serialize( $value );
	}

	public function get_type_list() {
		return [
			'A_Datamapper_Factory'            => 'adapter.datamapper_factory.php',
			'C_Custompost_Datamapper_Driver'  => 'class.custompost_datamapper_driver.php',
			'C_Customtable_Datamapper_Driver' => 'class.customtable_datamapper_driver.php',
			'C_Datamapper_Driver_Base'        => 'class.datamapper_driver_base.php',
			'C_Datamapper_Model'              => 'class.datamapper_model.php',
		];
	}
}

new M_DataMapper();
