<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Widget\Widget;
use Advanced_Sidebar_Menu\Widget\WidgetId;

/**
 * Temporary proxy to translate the `id` property to `id_base` and `number`.
 *
 * @todo Remove this class when the minimum PRO version is 9.9.0+.
 *
 * @internal
 */
class __Temp_Id_Proxy {
	/**
	 * @var Widget<array{}, array{}>
	 */
	protected Widget $widget;


	/**
	 * Constructor.
	 *
	 * @phpstan-param Widget<array{}, array{}> $widget
	 *
	 * @param Widget                           $widget - Widget instance.
	 */
	final protected function __construct( Widget $widget ) {
		$this->widget = $widget;
	}


	/**
	 * Get the base id from the `WP_Widget` class.
	 *
	 * @return string
	 */
	public function get_id_base(): string {
		if ( $this->widget instanceof WidgetId ) {
			return $this->widget->get_id_base();
		}
		if ( $this->widget instanceof \WP_Widget ) {
			return $this->widget->id_base;
		}
		return '';
	}


	/**
	 * @phpstan-param Widget<array{}, array{}> $widget
	 *
	 * @param Widget                           $widget - Widget to proxy.
	 *
	 * @return __Temp_Id_Proxy
	 */
	public static function factory( Widget $widget ): __Temp_Id_Proxy {
		return new static( $widget );
	}
}
