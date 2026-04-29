<?php
/**
 * The abstract migration class for ThemeIsle SDK.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2024, Themeisle
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.50
 */

namespace ThemeisleSDK\Modules;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract base class for SDK migrations.
 *
 * Migration files should return an anonymous class instance extending this class:
 *
 *   return new class extends \ThemeisleSDK\Modules\Abstract_Migration {
 *       public function up() { ... }
 *   };
 */
abstract class Abstract_Migration {
	/**
	 * WordPress database object.
	 *
	 * @var \wpdb
	 */
	protected $wpdb;

	/**
	 * WordPress table prefix.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * WordPress charset and collation string.
	 *
	 * @var string
	 */
	protected $charset_collate;

	/**
	 * Constructor. Populates database helpers.
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb            = $wpdb;
		$this->prefix          = $wpdb->prefix;
		$this->charset_collate = $wpdb->get_charset_collate();
	}

	/**
	 * Run the migration.
	 */
	abstract public function up();

	/**
	 * Reverse the migration.
	 *
	 * Override in concrete migrations to undo what up() did. Called by
	 * Migrator::rollback() — never invoked automatically.
	 *
	 * @return void
	 */
	public function down() {
		// No-op by default. Override to implement rollback logic.
	}

	/**
	 * Determine whether this migration should run.
	 *
	 * Override to add a custom idempotency check beyond name-based tracking.
	 * Return false to skip the migration without recording it.
	 *
	 * @return bool
	 */
	public function should_run() {
		return true;
	}
}
