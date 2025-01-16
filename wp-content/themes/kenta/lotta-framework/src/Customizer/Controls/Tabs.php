<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Utils;

class Tabs extends ContainerControl {

	public function __construct( $id = null ) {
		parent::__construct( $id ?? Utils::rand_key() );
		$this->solidStyle();
	}

	public function getType(): string {
		return 'lotta-tabs';
	}

	/**
	 * @return Tabs
	 */
	public function solidStyle() {
		return $this->setOption( 'style', 'solid' );
	}

	/**
	 * @return Tabs
	 */
	public function ghostStyle() {
		return $this->setOption( 'style', 'ghost' );
	}

	/**
	 * Get sub controls path
	 *
	 * @return array
	 */
	public function getSubControlsPath(): array {
		return [ 'tabs.[].controls' => true ];
	}

	/**
	 * Add new tab
	 *
	 * @param $slug
	 * @param $label
	 * @param array $controls
	 *
	 * @return Tabs
	 */
	public function addTab( $slug, $label, array $controls = [] ) {
		$tabs = $this->options['tabs'] ?? [];
		if ( empty( array_filter( $tabs, function ( $item ) use ( $slug ) {
			return $item['id'] === $slug;
		} ) ) ) {
			$tabs[] = [
				'id'       => $slug,
				'label'    => $label,
				'controls' => $this->parseControls( $controls ),
			];
		}

		return $this->setOption( 'tabs', $tabs );
	}

	/**
	 * Set active tab
	 *
	 * @param $tab
	 *
	 * @return Tabs
	 */
	public function setActiveTab( $tab ) {
		return $this->setOption( 'active', $tab );
	}
}