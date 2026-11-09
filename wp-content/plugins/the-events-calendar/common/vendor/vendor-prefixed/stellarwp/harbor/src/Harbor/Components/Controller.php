<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Components;

use TEC\Common\LiquidWeb\Harbor\View\Contracts\View;

/**
 * Component/View controller made to accept arguments and render
 * them in a view file.
 */
abstract class Controller {

	/**
	 * The View Engine to render views.
	 *
	 * @var View
	 */
	protected $view;

	/**
	 * Render the view file.
	 *
	 * @param mixed[] $args  An optional array of arguments to utilize when rendering.
	 */
	abstract public function render( array $args = [] ): void;

	/**
	 * @param View $view  The View Engine to render views.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Format an array of CSS classes into a string.
	 *
	 * @param string[] $classes The CSS classes to format.
	 *
	 * @return string
	 */
	protected function classes( array $classes ): string {
		if ( ! $classes ) {
			return '';
		}

		$sanitize = static function ( string $css_class ): string {
			return sanitize_html_class( $css_class );
		};
		$classes  = array_unique( array_map( $sanitize, array_filter( $classes ) ) );

		return implode( ' ', $classes );
	}
}
