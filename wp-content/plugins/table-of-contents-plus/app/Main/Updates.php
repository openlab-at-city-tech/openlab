<?php
namespace AIOSEO\TableOfContents\Main;

use AIOSEO\TableOfContents\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles update migrations.
 *
 * @since 1.0.0
 */
class Updates {
	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		add_action( 'init', [ $this, 'runUpdates' ], 1002 );
		add_action( 'init', [ $this, 'updateLatestVersion' ], 3000 );
	}

	/**
	 * Runs our migrations.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function runUpdates() {
		$lastActiveVersion = aioseoTableOfContents()->internalOptions->internal->lastActiveVersion;
		// Don't run updates if the last active version is the same as the current version.
		if ( aioseoTableOfContents()->version === $lastActiveVersion ) {
			return;
		}

		if ( version_compare( $lastActiveVersion, '1.0.0', '<' ) ) {
		}

		$this->migrateAppearanceDefaults();

		// Sync database schema with dbDelta - this will create tables and add missing columns automatically
		$this->updateDbSchema();

		// Re-sync role capabilities so plugin caps are only granted to administrators,
		// stripping them from any other role they were previously added to.
		aioseoTableOfContents()->access->removeCapabilities();
	}

	/**
	 * Updates the latest version after all migrations and updates have run.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function updateLatestVersion() {
		if ( aioseoTableOfContents()->internalOptions->internal->lastActiveVersion === aioseoTableOfContents()->version ) {
			return;
		}

		aioseoTableOfContents()->internalOptions->internal->lastActiveVersion = aioseoTableOfContents()->version;

		aioseoTableOfContents()->core->db->bustCache();
		aioseoTableOfContents()->core->cache->delete( 'db_schema' );
	}

	/**
	 * Preserves the pre-existing appearance behaviour for sites that already had the plugin.
	 *
	 * New installs default to a left-aligned title and a +/- collapse toggle. Existing
	 * installs are migrated to the previous centered title and bracketed show/hide link
	 * so the update doesn't visibly change their table of contents. Runs once, and only
	 * fills in settings the user never had (never overrides an explicit choice).
	 *
	 * @since 202607.1
	 *
	 * @return void
	 */
	private function migrateAppearanceDefaults() {
		if ( aioseoTableOfContents()->internalOptions->internal->migratedAppearanceDefaults ) {
			return;
		}

		// An install counts as pre-existing if it ran an earlier version (lastActiveVersion
		// is set) or already saved settings (the option row exists). A brand-new install has
		// neither and keeps the new defaults. lastActiveVersion is read before
		// updateLatestVersion() runs later on init, so it still holds the prior version here.
		$options           = get_option( 'toc-options' );
		$isExistingInstall = '0.0' !== aioseoTableOfContents()->internalOptions->internal->lastActiveVersion || false !== $options;

		if ( $isExistingInstall ) {
			if ( ! is_array( $options ) ) {
				$options = [];
			}
			if ( ! array_key_exists( 'heading_alignment', $options ) ) {
				$options['heading_alignment'] = 'center';
			}
			if ( ! array_key_exists( 'toggle_style', $options ) ) {
				$options['toggle_style'] = 'brackets';
			}

			update_option( 'toc-options', $options );
		}

		aioseoTableOfContents()->internalOptions->internal->migratedAppearanceDefaults = true;
	}

	/**
	 * Synchronizes database schema with defined schema using dbDelta.
	 *
	 * This method uses WordPress's dbDelta() function to automatically:
	 * - Create tables that don't exist
	 * - Add missing columns to existing tables
	 * - Modify column definitions that have changed
	 *
	 * Note: dbDelta CANNOT drop columns or rename columns. Those operations
	 * must be handled separately with custom SQL in version-gated migrations.
	 *
	 * @since 202607
	 *
	 * @return void
	 */
	private function updateDbSchema() {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$tableName = aioseoTableOfContents()->core->db->db->prefix . 'aioseo_table_of_contents_cache';
		aioseoTableOfContents()->core->db->execute(
			"DELETE FROM {$tableName}"
		);

		// Get all schema definitions and run dbDelta
		$schemas = aioseoTableOfContents()->dbSchema->getSchema();
		dbDelta( $schemas );

		// Clear schema cache so columnExists/tableExists work correctly
		aioseoTableOfContents()->core->cache->delete( 'db_schema' );
	}
}