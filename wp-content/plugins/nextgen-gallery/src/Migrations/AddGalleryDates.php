<?php
/**
 * Migration to add date columns to gallery and album tables.
 *
 * @package Imagely\NGG\Migrations
 */

// phpcs:disable WordPress.DB.DirectDatabaseQuery
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Table names are properly escaped, and complex JOIN query cannot use prepare.

namespace Imagely\NGG\Migrations;

/**
 * Migration to add date columns to gallery and album tables.
 */
class AddGalleryDates {

	/**
	 * Performs the migration to add and populate date columns.
	 *
	 * @param bool $force Force the migration to run even if already completed.
	 * @return bool True on success.
	 */
	public static function migrate( $force = false ) {
		if ( ! $force && get_option( 'imagely_dates_migrated' ) ) {
			return true;
		}

		global $wpdb;

		$gallery_table = $wpdb->prefix . 'ngg_gallery';
		$album_table   = $wpdb->prefix . 'ngg_album';
		$posts_table   = $wpdb->posts;

		// First, check if the columns exist in gallery table, if not create them.
		$columns = $wpdb->get_col( 'SHOW COLUMNS FROM `' . esc_sql( $gallery_table ) . '`' );

		// If columns don't exist, add them.
		if ( ! in_array( 'date_created', $columns, true ) ) {
			$wpdb->query( 'ALTER TABLE `' . esc_sql( $gallery_table ) . '` ADD COLUMN `date_created` DATETIME' );
		}

		if ( ! in_array( 'date_modified', $columns, true ) ) {
			$wpdb->query( 'ALTER TABLE `' . esc_sql( $gallery_table ) . '` ADD COLUMN `date_modified` DATETIME' );
		}

		// Check if the columns exist in album table, if not create them.
		$columns = $wpdb->get_col( 'SHOW COLUMNS FROM `' . esc_sql( $album_table ) . '`' );

		if ( ! in_array( 'date_created', $columns, true ) ) {
			$wpdb->query( 'ALTER TABLE `' . esc_sql( $album_table ) . '` ADD COLUMN `date_created` DATETIME' );
		}

		if ( ! in_array( 'date_modified', $columns, true ) ) {
			$wpdb->query( 'ALTER TABLE `' . esc_sql( $album_table ) . '` ADD COLUMN `date_modified` DATETIME' );
		}

		// Update gallery dates - track if we actually migrated any data.
		$sql = 'UPDATE `' . esc_sql( $gallery_table ) . '` g ' .
			'INNER JOIN `' . esc_sql( $posts_table ) . '` p ON p.ID = g.extras_post_id ' .
			'SET g.date_created = CASE ' .
			'WHEN p.post_modified_gmt = "0000-00-00 00:00:00" THEN p.post_modified ' .
			'ELSE p.post_modified_gmt END, ' .
			'g.date_modified = CASE ' .
			'WHEN p.post_modified_gmt = "0000-00-00 00:00:00" THEN p.post_modified ' .
			'ELSE p.post_modified_gmt END';

		$galleries_updated = $wpdb->query( $sql );

		// Update album dates.
		$sql = 'UPDATE `' . esc_sql( $album_table ) . '` a ' .
			'INNER JOIN `' . esc_sql( $posts_table ) . '` p ON p.ID = a.extras_post_id ' .
			'SET a.date_created = CASE ' .
			'WHEN p.post_modified_gmt = "0000-00-00 00:00:00" THEN p.post_modified ' .
			'ELSE p.post_modified_gmt END, ' .
			'a.date_modified = CASE ' .
			'WHEN p.post_modified_gmt = "0000-00-00 00:00:00" THEN p.post_modified ' .
			'ELSE p.post_modified_gmt END';

		$albums_updated = $wpdb->query( $sql );

		// Update proofing dates stored as post meta for 'nextgen_proof' CPT.
		$postmeta_table = $wpdb->postmeta;

		// Remove existing date meta to avoid duplicates, then re-insert with correct values.
		$sql = 'DELETE pm FROM `' . esc_sql( $postmeta_table ) . '` pm '
			. 'INNER JOIN `' . esc_sql( $posts_table ) . '` p ON p.ID = pm.post_id '
			. 'WHERE p.post_type = "nextgen_proof" '
			. 'AND pm.meta_key IN ("date_created","date_modified")';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $sql );

		// Insert date_created meta from post modified timestamps.
		$sql = 'INSERT INTO `' . esc_sql( $postmeta_table ) . '` (post_id, meta_key, meta_value) '
			. 'SELECT p.ID, "date_created", '
			. 'CASE WHEN p.post_modified_gmt = "0000-00-00 00:00:00" THEN p.post_modified ELSE p.post_modified_gmt END '
			. 'FROM `' . esc_sql( $posts_table ) . '` p WHERE p.post_type = "nextgen_proof"';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $sql );

		// Insert date_modified meta from post modified timestamps.
		$sql = 'INSERT INTO `' . esc_sql( $postmeta_table ) . '` (post_id, meta_key, meta_value) '
			. 'SELECT p.ID, "date_modified", '
			. 'CASE WHEN p.post_modified_gmt = "0000-00-00 00:00:00" THEN p.post_modified ELSE p.post_modified_gmt END '
			. 'FROM `' . esc_sql( $posts_table ) . '` p WHERE p.post_type = "nextgen_proof"';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $sql );

		update_option( 'imagely_dates_migrated', true );

		// Only mark as 'existing' if we actually migrated data from galleries or albums.
		// If no rows were updated, this is a fresh installation with no existing data.
		if ( $galleries_updated > 0 || $albums_updated > 0 ) {
			$settings = \Imagely\NGG\Settings\Settings::get_instance();
			$settings->set( 'ngg_installation_type', 'existing' );
			$settings->save();
		}

		return true;
	}
}

// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
// phpcs:enable WordPress.DB.DirectDatabaseQuery
