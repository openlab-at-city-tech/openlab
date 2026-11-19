<?php
namespace AIOSEO\TableOfContents\Main;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class contains pre-updates necessary for the main Updates class to run.
 *
 * @since 1.0.0
 */
class PreUpdates {
	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// We don't want an AJAX request check here since the plugin might be installed/activated for the first time via AJAX.
		// If that's the case, the cache table needs to be created before the cron job runs.
		if ( wp_doing_cron() ) {
			return;
		}

		$lastActiveVersion = aioseoTableOfContents()->internalOptions->internal->lastActiveVersion;
		if ( version_compare( $lastActiveVersion, '202607', '<' ) ) {
			// Delete the cache table transient BEFORE migration so that
			// isCacheTableAvailable() will re-verify the table structure.
			delete_transient( 'aioseo_plugin_cache_table_exists' );

			$this->createCacheTable();
			aioseoTableOfContents()->core->cache->delete( 'db_schema' );
		}
	}

	/**
	 * Creates the cache table.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function createCacheTable() {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$tableName = aioseoTableOfContents()->core->db->db->prefix . 'aioseo_table_of_contents_cache';

		// Use dbDelta to create the cache table based on schema definition
		$schema = aioseoTableOfContents()->dbSchema->getCacheTableSchema();
		dbDelta( $schema );

		// Clear any existing cache rows now that the table is guaranteed to exist.
		// (Running this before dbDelta errors on a fresh install where the table doesn't exist yet.)
		aioseoTableOfContents()->core->db->execute(
			"DELETE FROM {$tableName}"
		);

		// Clear the transient so isCacheTableAvailable() re-checks on the next call.
		delete_transient( 'aioseo_plugin_cache_table_exists' );
	}
}