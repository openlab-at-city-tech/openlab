<?php
namespace AIOSEO\TableOfContents\Standalone;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the standalone components.
 *
 * @since 1.0.0
 */
class Standalone {
	/**
	 * The frontend engine instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Frontend
	 */
	public $frontend = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// The global constants (TOC_VERSION, TOC_POSITION_*, etc.) and the toc_get_index() API
		// function are part of the plugin's public surface, so they stay global and are loaded
		// explicitly (they are not PSR-4 autoloaded like the classes).
		require_once __DIR__ . '/globals.php';
		require_once __DIR__ . '/functions.php';

		$this->frontend = new Frontend();

		// Preserve the historical global and class name so themes and snippets that reference
		// $toc_plus or the TOC_Plus class keep working.
		$GLOBALS['toc_plus'] = $this->frontend; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		if ( ! class_exists( 'TOC_Plus' ) ) {
			class_alias( Frontend::class, 'TOC_Plus' );
		}
	}
}