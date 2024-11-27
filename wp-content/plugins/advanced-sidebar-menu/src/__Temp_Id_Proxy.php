<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Widget\Widget_Abstract;

/**
 * Temporary proxy to translate the `id` property to `id_base` and `number`.
 *
 * @todo Switch `Widget_Abstract` to `Widget` when minimum PRO version is 9.6.0+.
 * @todo Remove this class when the minimum PRO version is switched to `WidgetWithId` (TBD).
 *
 * @internal
 */
class __Temp_Id_Proxy {
	/**
	 * @var Widget_Abstract<array{}, array{}>
	 */
	protected Widget_Abstract $widget;


	/**
	 * Constructor.
	 *
	 * @phpstan-param Widget_Abstract<array{}, array{}> $widget
	 *
	 * @param Widget_Abstract                           $widget - Widget instance.
	 */
	final protected function __construct( Widget_Abstract $widget ) {
		$this->widget = $widget;
	}


	/**
	 * Get the base id from the `WP_Widget` class.
	 *
	 * @return string
	 */
	public function get_id_base(): string {
		if ( $this->widget instanceof Widget\WidgetId ) {
			return $this->widget->get_id_base();
		}
		// @phpstan-ignore-next-line -- This is expected to always be true, but we'll be sure.
		if ( $this->widget instanceof \WP_Widget ) {
			return $this->widget->id_base;
		}
		// @phpstan-ignore-next-line -- Should never reach here, but want to be sure.
		return '';
	}


	/**
	 * @param Widget_Abstract $widget
	 *
	 * @return __Temp_Id_Proxy
	 */
	//phpcs:disable
	// @phpstan-ignore-next-line -- Intentionally not adding generic values.
	public static function factory( Widget_Abstract $widget ): __Temp_Id_Proxy {
		return new static( $widget );
	}
}
