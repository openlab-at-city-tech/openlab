<?php
/**
 * This is a wrapper class to the LegacyImage class which provides all the necessary logic for retrieving attributes as needed.
 *
 * NOTE: it isn't possible yet (as of PHP 5.4) to use overloaded properties in any language constructs other than
 * isset(). To work around this with the wrapper class (which uses overloaded properties) we make a copy of any
 * attributes set to this object.
 */
#[AllowDynamicProperties]
class nggImage {

	public $_ngiw;
	public $_propogate = true;

	public $thumbURL;

	function __construct( $image ) {
		$image->meta_data = \Imagely\NGG\Util\Serializable::unserialize( $image->meta_data );
		$this->_ngiw      = new \Imagely\NGG\DataTypes\LegacyImage( $image, null, true );
	}

	public function __set( $name, $value ) {
		$this->$name = $value;
		if ($this->_propogate) {
			$this->_ngiw->__set( $name, $value );
		}
	}

	public function __isset( $name ) {
		return $this->_ngiw->__isset( $name );
	}

	public function __unset( $name ) {
		return $this->_ngiw->__unset( $name );
	}

	public function __get( $name ) {
		$this->_propogate = false;
		$this->$name      = $this->_ngiw->__get( $name );
		$this->_propogate = true;
		return $this->$name;
	}

	function get_thumbcode( $galleryname = '' ) {
		return $this->_ngiw->get_thumbcode( $galleryname );
	}

	function get_href_link() {
		return $this->_ngiw->get_href_link();
	}

	function get_href_thumb_link() {
		return $this->_ngiw->get_href_thumb_link();
	}

	function cached_singlepic_file( $width = '', $height = '', $mode = '' ) {
		return $this->_ngiw->cached_singlepic_file( $width, $height, $mode );
	}

	function get_tags() {
		return $this->_ngiw->get_tags();
	}

	function get_permalink() {
		return $this->_ngiw->get_permalink();
	}
}
