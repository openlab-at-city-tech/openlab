<?php
namespace AIOSEO\TableOfContents\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Options;
use AIOSEO\TableOfContents\Utils;

/**
 * Loads core classes.
 *
 * @since 1.0.0
 */
class Core {
	/**
	 * DB class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Database
	 */
	public $db = null;

	/**
	 * Filesystem class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Filesystem
	 */
	public $fs = null;

	/**
	 * Assets class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Assets
	 */
	public $assets = null;

	/**
	 * Cache class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Cache
	 */
	public $cache = null;

	/**
	 * NetworkCache class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var NetworkCache
	 */
	public $networkCache = null;

	/**
	 * Options Cache class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var \AIOSEO\TableOfContents\Options\Cache
	 */
	public $optionsCache = null;

	/**
	 * Uninstall class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Uninstall
	 */
	public $uninstall = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->db           = new Database();
		$this->fs           = new Filesystem( $this );
		$this->assets       = new Assets( $this );
		$this->cache        = new Cache();
		$this->networkCache = new NetworkCache();
		$this->optionsCache = new Options\Cache();
		$this->uninstall    = new Uninstall();
	}
}