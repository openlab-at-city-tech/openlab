<?php
namespace AIOSEO\TableOfContents\Main;

use AIOSEO\TableOfContents\Highlighter;
use AIOSEO\TableOfContents\Links;
use AIOSEO\TableOfContents\LinkStatus;
use AIOSEO\TableOfContents\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class where core features are handled/registered.
 *
 * @since 1.0.0
 */
class Main {
	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		new Activate();
	}
}