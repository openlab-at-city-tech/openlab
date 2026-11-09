<?php

class DLM_Log_Filters {

	/**
	 * Setup filters for log items
	 */
	public function setup() {
		add_filter( 'dlm_log_item', array( $this, 'filter_ip_address' ), 1, 1 );
		add_filter( 'dlm_log_item', array( $this, 'filter_ua' ), 1, 1 );
	}

	/**
	 * Filter IP address in log item based on settings
	 *
	 * @param DLM_Log_Item $log_item
	 *
	 * @return DLM_Log_Item
	 */
	public function filter_ip_address( $log_item ) {

		$logging = new DLM_Logging();
		$ip_type = $logging->get_ip_logging_type();


		switch ( $ip_type ) {
			case 'anonymized':
				$ip = $log_item->get_user_ip();

				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
					// expand to 8 full groups (handles "::"), keep the first 3 (/48)
					$ip_parts   = array_slice( str_split( bin2hex( inet_pton( $ip ) ), 4 ), 0, 3 );
					$ip_parts[] = 'xxxx';

					$log_item->set_user_ip( implode( ':', $ip_parts ) );
					break;
				}

				// replace last part of IP address with xxx
				$ip_parts = explode( ".", $ip );

				// remove last chunk and add new last chunk with xxx
				array_pop( $ip_parts );
				$ip_parts[] = "xxx";

				$log_item->set_user_ip( implode( '.', $ip_parts ) );
				break;
			case 'none':
				// set an empty string
				$log_item->set_user_ip( '' );
				break;
			case 'full':
			default:
				// do nothing
				break;
		}

		return $log_item;
	}

	/**
	 * Filter user agent in log item based on settings
	 *
	 * @param DLM_Log_Item $log_item
	 *
	 * @return DLM_Log_Item
	 */
	public function filter_ua( $log_item ) {

		$logging = new DLM_Logging();

		if ( ! $logging->is_ua_logging_enabled() ) {
			$log_item->set_user_agent( '' );
		}

		return $log_item;
	}
}