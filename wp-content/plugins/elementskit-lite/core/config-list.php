<?php 
namespace ElementsKit_Lite\Core;

abstract class Config_List {

	use \ElementsKit_Lite\Traits\Singleton;

	private $full_list   = array();
	private $active_list = array();
	
	protected $optional_list = array();
	protected $required_list = array();

	protected $type;

	public function __construct() {
		$this->set_optional_list();
		$this->set_required_list();
		$this->set_full_list();
		$this->set_active_list();
	}

	public function get_list( $data = 'full', $module = null ) {
		if ( $module != null ) {
			return ( $this->{$data . '_list'}[ $module ] ?? false );
		}

		return $this->{$data . '_list'};
	}

	public function is_active( $item ) {

		$item = ( $this->active_list[ $item ] ?? array() );
		
		return empty( $item['package'] ) ? false : ( ( $item['package'] == 'free' || $item['package'] == 'pro' ) );
	}

	private function set_active_list() {
		$database_list = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( $this->type . '_list', array() );

		foreach ( $this->full_list as $key => $item ) {

			if ( isset( $database_list[ $key ]['status'] ) && $database_list[ $key ]['status'] == 'inactive' && ! key_exists( $key, $this->required_list ) ) {
				continue;
			} 

			if ( isset( $item['package'] ) && $item['package'] == 'pro-disabled' ) {
				continue;
			}
	
			$this->active_list[ $key ] = $item;
		}
	}

	private function set_full_list() {
		$this->full_list = array_merge( $this->required_list, $this->optional_list );
	}

	abstract protected function set_required_list();

	abstract protected function set_optional_list();

}
