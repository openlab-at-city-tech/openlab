<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core\Controllers;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Base\BaseController;

class MessageController {
	public function __construct() {

	}

	public function getUnreadMessages() {

	}

	public function getUserReadMessages() {
		$userId = get_current_user_id();
		$messages = maybe_unserialize( get_user_meta( $userId, 'zpm_read_msg', true ) );

		if (!$messages) {
			$messages = [];
		}

		return (array) $messages;
	}

	public function addReadMessage( $msgId ) {
		$userId = get_current_user_id();
		$messages = $this->getUserReadMessages();
		$added = false;

		foreach($messages as $msg) {
			if ($msg == $msgId) {
				$added = true;
			}
		}

		if (!$added) {
			$messages[] = $msgId;
			$added = update_user_meta( $userId, 'zpm_read_msg', serialize($messages));
		}
	}

	public function isRead( $msgId ) {
		$messages = $this->getUserReadMessages();

		foreach($messages as $msg) {
			if ($msg == $msgId) {
				return true;
			}
		}

		return false;
	}
}