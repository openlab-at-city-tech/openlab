<?php

namespace ElementsKit_Lite\Libs\Xs_Migration;

use ElementsKit_Lite\Traits\Singleton;

class Initiator {

	use Singleton;

	const OK_MIGRATION_LOGGER = 'ekit_migration_log';


	public function init() {

		add_filter( 'heartbeat_send', array( $this, 'send_heartbeat' ), 10, 2 );
		//add_filter('heartbeat_received', [$this, 'receive_heartbeat'], 10, 2);
		//add_filter( 'heartbeat_settings', [$this, 'heartbeat_settings'] );
	}


	public function send_heartbeat( $response, $screen_id ) {

		$txtDomain = 'elementskit-lite';
		$optionKey = 'data_migration_' . $txtDomain . '_log';

		$option = get_option( $optionKey, array() );

		if ( empty( $option['_last_version_scanned'] ) ) {

			/**
			 * Migration never ran in this domain
			 *
			 */
			$migration = new Migration();

			$ret = $migration->input( $txtDomain, '1.3.1', \ElementsKit_Lite::version() );

			$response['migration_log'] = $ret;

			return $response;
		}

		/**
		 * We have last version of migration run
		 * checking if it is same as current version
		 */
		if ( $option['_last_version_scanned'] == \ElementsKit_Lite::version() ) {

			$data[] = 'Migration has already run for this version - ' . \ElementsKit_Lite::version();
			$data[] = $screen_id;

			$response['migration_push'] = $data;

			return $response;
		}

		/**
		 * We have started running migration for this version
		 * Or this is an update version that need to check migration run again
		 *
		 */
		$migration = new Migration();

		$ret = $migration->input( $txtDomain, '1.3.1', \ElementsKit_Lite::version() );

		$response['migration_log'] = $ret;

		return $response;
	}

	public function receive_heartbeat() {
	}


	public function heartbeat_settings() {

		$settings['interval'] = 15; //Anything between 15-120
		//$settings['autostart'] = false;

		return $settings;
	}

}
