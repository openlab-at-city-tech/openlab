<?php
namespace FileBird\Classes;

defined( 'ABSPATH' ) || exit;

class Schedule {
	public function __construct() {
        add_action( 'filebird_remove_zip_files', array( $this, 'actionRemoveZipFiles' ) );
		add_action( 'filebird_every_12_hours_jobs', array( $this, 'backupFileBird' ) );
	}

	public static function registerSchedule() {
		if ( ! wp_next_scheduled( 'filebird_remove_zip_files' ) ) {
			wp_schedule_event( time(), 'daily', 'filebird_remove_zip_files' );
		}
		self::syncBackupSchedule();
	}

	/** Whether folders are backed up automatically (every 12 hours and on plugin update). */
	public static function isAutoBackupEnabled() {
		return apply_filters( 'fbv_enable_auto_backup', get_option( 'njt_fbv_auto_backup', '1' ) === '1' );
	}

	/** Keep the backup cron event in sync with the setting. */
	public static function syncBackupSchedule() {
		$scheduled = wp_next_scheduled( 'filebird_every_12_hours_jobs' );

		if ( self::isAutoBackupEnabled() ) {
			if ( ! $scheduled ) {
				wp_schedule_event( time(), 'twicedaily', 'filebird_every_12_hours_jobs' );
			}
		} elseif ( $scheduled ) {
			wp_clear_scheduled_hook( 'filebird_every_12_hours_jobs' );
		}
	}

	public static function clearSchedule() {
		wp_clear_scheduled_hook( 'filebird_remove_zip_files' );
		wp_clear_scheduled_hook( 'filebird_every_12_hours_jobs' );
	}

	public function actionRemoveZipFiles() {
		$saved_downloads = get_option( 'filebird_saved_downloads', array() );
		if ( ! is_array( $saved_downloads ) ) {
			$saved_downloads = array();
		}
		foreach ( $saved_downloads as $time => $path ) {
			if ( ( time() - $time ) >= ( 24 * 60 * 60 ) ) {
				$wp_dir = wp_upload_dir();
				if ( file_exists( $wp_dir['basedir'] . $path ) ) {
					unlink( $wp_dir['basedir'] . $path );
				}
				unset( $saved_downloads[ $time ] );
			}
		}
		update_option( 'filebird_saved_downloads', $saved_downloads );
	}
	public function backupFileBird() {
		if ( ! self::isAutoBackupEnabled() ) {
			return;
		}
		Helpers::backupFileBird();
	}
}