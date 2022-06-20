<?php
namespace ElementsKit_Lite\Compatibility\Data_Migration;

defined( 'ABSPATH' ) || exit;

class Settings_Db {
	public function __construct() {

		$widget_list = \ElementsKit_Lite\Config\Widget_List::instance()->get_list();
		$this->migrate( $widget_list, 'widget' );
		
		$module_list = \ElementsKit_Lite\Config\Module_List::instance()->get_list();
		$this->migrate( $module_list, 'module' );
	}

	private function migrate( $list, $type ) {
		$list_db       = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( $type . '_list', array() );
		$list_prepared = array();
		
		if ( empty( $list_db[0] ) || is_array( $list_db[0] ) ) {
			return;
		}

		foreach ( $list as $slug => $info ) {
			if ( isset( $info['package'] ) && $info['package'] == 'pro-disabled' ) {
				continue;
			}

			if ( isset( $info['attributes'] ) && in_array( 'new', $info['attributes'] ) ) {
				continue;
			}

			$info['status'] = ( in_array( $slug, $list_db ) ? 'active' : 'inactive' );

			$list_prepared[ $slug ] = $info;
		}

		\ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->save_option( $type . '_list', $list_prepared );
	}
}
