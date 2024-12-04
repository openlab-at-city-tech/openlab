<?php

namespace LottaFramework\Facades;

/**
 * @method static dynamic( $id, $css )
 * @method static valueMapper( $maps, $selector = 'value' )
 * @method static unescape( $script )
 * @method static encode( $script )
 * @method static dimensions( $selector = 'margin' )
 * @method static background()
 * @method static border( $selector = 'border' )
 * @method static shadow( $selector = 'box-shadow' )
 * @method static typography()
 * @method static filters()
 * @method static colors( $maps, $css = [] )
 */
class AsyncCss extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \LottaFramework\Async\Css::class;
	}
}