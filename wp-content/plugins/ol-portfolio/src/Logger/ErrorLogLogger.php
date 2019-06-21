<?php

namespace OpenLab\Portfolio\Logger;

class ErrorLogLogger extends Logger {
	public function log( $level, $message, array $context = [] ) {
		switch ( $level ) {
			case 'emergency':
			case 'alert':
			case 'critical':
			case 'error':
			case 'warning':
			case 'notice':
			case 'info':
				error_log( $level . ' : ' . $message );
				break;

			case 'debug':
				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					error_log( $level . ' : ' . $message );
					break;
				}
				break;
		}
	}
}
