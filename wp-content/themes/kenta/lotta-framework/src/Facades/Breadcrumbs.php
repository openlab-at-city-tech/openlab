<?php

namespace LottaFramework\Facades;

/**
 * @method static setSep( $sep )
 * @method static setLinkFormat( $format )
 * @method static setItemFormat( $format )
 * @method static generate(): array
 * @method static get(): string
 * @method static render( $before = '', $after = '' )
 */
class Breadcrumbs extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \LottaFramework\Extensions\Breadcrumbs::class;
	}
}