<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Transaction manager for batching database writes.
 *
 * @package broken-link-checker
 */

/**
 * Manages nested MySQL transactions via a depth counter.
 *
 * A BEGIN is only issued when the depth goes from 0 → 1.
 * A COMMIT is only issued when the depth goes from 1 → 0, so inner
 * callers that call commit() do not flush an outer transaction prematurely.
 */
class TransactionManager {

	/**
	 * Current nesting depth.
	 *
	 * @var int
	 */
	private int $depth = 0;

	/**
	 * Singleton instance.
	 *
	 * @var TransactionManager|null
	 */
	private static $instance;

	/**
	 * Open a transaction (or increment the nesting depth if one is already open).
	 *
	 * @return void
	 */
	public function start(): void {
		global $wpdb;

		if ( 0 === $this->depth ) {
			$wpdb->query( 'BEGIN' ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}
		++$this->depth;
	}

	/**
	 * Decrement the nesting depth and commit when the outermost caller is done.
	 *
	 * @return void
	 */
	public function commit(): void {
		global $wpdb, $blclog;

		if ( $this->depth <= 0 ) {
			return;
		}

		--$this->depth;

		if ( 0 === $this->depth ) {
			$blclog->debug( 'Committing transaction.' );
			$wpdb->query( 'COMMIT' ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}
	}

	/**
	 * Roll back the current transaction and reset the nesting depth.
	 *
	 * @return void
	 */
	public function rollback(): void {
		global $wpdb;

		$this->depth = 0;
		$wpdb->query( 'ROLLBACK' ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Return the singleton instance.
	 *
	 * @return TransactionManager
	 */
	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new TransactionManager();
		}

		return self::$instance;
	}
}
