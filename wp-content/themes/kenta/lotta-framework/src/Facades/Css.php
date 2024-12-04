<?php

namespace LottaFramework\Facades;

/**
 * @method static desktop(): string
 * @method static tablet(): string
 * @method static mobile(): string
 * @method static parse( $css_output = [], $beauty = false ): string
 * @method static fontFaces( $font_faces_input = [], $beauty = false ): string
 * @method static keyframes( $keyframes_output = [], $beauty = false ): string
 * @method static dimensions( $value, $selector = 'margin' ): array
 * @method static background( $background ): array
 * @method static border( $border, $selector = 'border' ): array
 * @method static shadow( $shadow, $selector = 'box-shadow' ): array
 * @method static filters( $filters ): array
 * @method static typography( $typography ): array
 * @method static colors( $colors, $map, $css = [] ): array
 */
class Css extends Facade {

	/**
	 * Css initial value
	 */
	const INITIAL_VALUE = '__INITIAL_VALUE__';

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \LottaFramework\Css::class;
	}
}
