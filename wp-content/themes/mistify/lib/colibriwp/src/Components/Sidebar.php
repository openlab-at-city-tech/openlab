<?php
namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\View;

class Sidebar extends ComponentBase {

	public function renderContent( $options = array() ) {
		$id = isset( $options['id'] ) ? $options['id'] : 'post';
		View::partial(
			'sidebar',
			$id,
			array(
				'component' => $this,
			)
		);
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		return array();
	}
}
