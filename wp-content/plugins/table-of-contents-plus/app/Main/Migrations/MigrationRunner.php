<?php
namespace AIOSEO\TableOfContents\Main\Migrations;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discovers, executes, and logs {@see Migration} instances.
 *
 * The runner closes the half-applied failure mode: the version flag is only
 * advanced when EVERY registered migration's verify() returns true. A
 * concurrent-request lock loser bails without touching state; a lock holder
 * that dies mid-migration leaves the relevant log entry at status = 0 (or
 * absent), and the runner retries on the next request until verify() actually
 * passes.
 *
 * @since 202607
 */
class MigrationRunner {
	/**
	 * Registered migrations, in registration order.
	 *
	 * @since 202607
	 *
	 * @var Migration[]
	 */
	private $migrations = [];

	/**
	 * Log accessor.
	 *
	 * @since 202607
	 *
	 * @var MigrationLog
	 */
	private $log;

	/**
	 * Lock name. MySQL GET_LOCK is per-connection, so this serializes across
	 * concurrent PHP processes for the same site.
	 *
	 * @since 202607
	 *
	 * @var string
	 */
	private $lockName = 'aioseo_table_of_contents_migration_runner';

	/**
	 * @since 202607
	 */
	public function __construct() {
		$this->log = new MigrationLog();
	}

	/**
	 * Register a migration. Order is preserved — later registrations run
	 * after earlier ones within the same request.
	 *
	 * @since 202607
	 *
	 * @param  Migration $migration The migration to register.
	 * @return void
	 */
	public function register( Migration $migration ) {
		$this->migrations[] = $migration;
	}

	/**
	 * Execute pending migrations.
	 *
	 * Short-circuits early when the recorded schema version matches the
	 * current plugin version — healthy sites never touch the log option.
	 * Lock losers exit silently and do NOT advance any version flag, so a
	 * future-bumped lastSchemaVersion can't strand work.
	 *
	 * @since 202607
	 *
	 * @return void
	 */
	public function run() {
		if ( aioseoTableOfContents()->internalOptions->internal->lastSchemaVersion === aioseoTableOfContents()->version ) {
			return;
		}

		if ( empty( $this->migrations ) ) {
			aioseoTableOfContents()->internalOptions->internal->lastSchemaVersion = aioseoTableOfContents()->version;

			return;
		}

		if ( ! aioseoTableOfContents()->core->db->acquireLock( $this->lockName, 0 ) ) {
			return;
		}

		try {
			$log     = $this->log->read();
			$allDone = true;

			foreach ( $this->migrations as $migration ) {
				$name = $migration->name();

				if ( $this->verifySafely( $migration ) ) {
					if ( ! isset( $log[ $name ] ) || 1 !== ( $log[ $name ]['status'] ?? 0 ) ) {
						$log[ $name ] = $this->successEntry( $migration, $log );
					}
					continue;
				}

				try {
					$migration->up();
					$verified = $this->verifySafely( $migration );

					$log[ $name ] = $verified
						? $this->successEntry( $migration, $log )
						: $this->failureEntry( $migration, $log, 'verify() returned false after up()' );

					if ( ! $verified ) {
						$allDone = false;
					}
				} catch ( \Throwable $e ) {
					$log[ $name ] = $this->failureEntry( $migration, $log, $e->getMessage() );
					$allDone = false;
				}
			}

			$this->log->write( $log );

			if ( $allDone ) {
				aioseoTableOfContents()->internalOptions->internal->lastSchemaVersion = aioseoTableOfContents()->version;
			}
		} finally {
			// Explicit release shrinks the hold window to the migration work itself.
			// acquireLock() also registers a shutdown function as belt-and-braces in
			// case execution exits before we reach this point.
			aioseoTableOfContents()->core->db->releaseLock( $this->lockName );
		}
	}

	/**
	 * Wrap verify() so a throwing implementation degrades to "not verified"
	 * instead of bringing down the runner. The error surfaces via the log's
	 * failureEntry on the next up()/verify() cycle.
	 *
	 * @since 202607
	 *
	 * @param  Migration $migration The migration to verify.
	 * @return bool
	 */
	private function verifySafely( Migration $migration ) {
		try {
			return (bool) $migration->verify();
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * Build a success log entry, preserving the prior attempts counter so
	 * support can see how many retries it took to land.
	 *
	 * @since 202607
	 *
	 * @param  Migration $migration The migration.
	 * @param  array     $log       The current log state.
	 * @return array
	 */
	private function successEntry( Migration $migration, array $log ) {
		$name     = $migration->name();
		$attempts = isset( $log[ $name ]['attempts'] ) ? (int) $log[ $name ]['attempts'] : 0;

		return [
			'version'    => $migration->version(),
			'ran_at'     => aioseoTableOfContents()->helpers->timeToMysql( time() ),
			'status'     => 1,
			'attempts'   => $attempts + 1,
			'last_error' => null
		];
	}

	/**
	 * Build a failure log entry, incrementing attempts so retry pressure is
	 * visible in the log.
	 *
	 * @since 202607
	 *
	 * @param  Migration $migration The migration.
	 * @param  array     $log       The current log state.
	 * @param  string    $error     Reason for the failure.
	 * @return array
	 */
	private function failureEntry( Migration $migration, array $log, $error ) {
		$name     = $migration->name();
		$attempts = isset( $log[ $name ]['attempts'] ) ? (int) $log[ $name ]['attempts'] : 0;

		return [
			'version'    => $migration->version(),
			'ran_at'     => aioseoTableOfContents()->helpers->timeToMysql( time() ),
			'status'     => 0,
			'attempts'   => $attempts + 1,
			'last_error' => $error
		];
	}
}
