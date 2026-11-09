<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\View\Contracts;

use TEC\Common\LiquidWeb\Harbor\View\Exceptions\FileNotFoundException;

interface View {

	/**
	 * Renders a view and returns it as a string to be echoed.
	 *
	 * @example If the server path is /app/views, and you wish to load /app/views/admin/notice.php,
	 * pass `admin/notice` as the view name.
	 *
	 * @param string  $name  The relative path/name of the view file without extension.
	 *
	 * @param mixed[] $args  Arguments to be extracted and passed to the view.
	 *
	 * @throws FileNotFoundException If the view file cannot be found.
	 *
	 * @return string
	 */
	public function render( string $name, array $args = [] ): string;

	/**
	 * Renders a view directly to output.
	 *
	 * Use this instead of echo render() to avoid PHPCS escaping warnings
	 * when the view template handles its own escaping.
	 *
	 * @param string  $name  The relative path/name of the view file without extension.
	 * @param mixed[] $args  Arguments to be extracted and passed to the view.
	 *
	 * @throws FileNotFoundException If the view file cannot be found.
	 *
	 * @return void
	 */
	public function display( string $name, array $args = [] ): void;
}
