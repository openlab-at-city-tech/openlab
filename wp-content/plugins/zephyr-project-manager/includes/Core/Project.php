<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
use Inc\Core\Members;
use Inc\Core\Utillities;
use Inc\ZephyrProjectManager;

class Project {
	private $id;
	private $name;
	private $description;
	private $members;
	private $managers;

	public function __construct( $data ) {
		if (empty($data->name)) {
			$data->name = __( 'Untitled Project', 'zephyr-project-manager' );
		}
		$this->id = $data->id;
		$this->name = $data->name;
		$this->description = $data->description;
		$this->managers = property_exists($data, 'managers') ? explode(',', $data->managers) : array();
		$this->assignees = property_exists($data, 'assignees') ? explode(',', $data->assignees) : array();
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function addFile( $fileId, $parentId, $type, $userId ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');
		$settings = [
			'user_id' => $userId,
			'subject' => 'project',
			'subject_id' => $this->id,
			'parent_id' => $parentId,
			'type' => serialize($type),
			'message' => serialize($fileId)
		];

		$wpdb->insert($table_name, $settings);
		return $wpdb->insert_id;
	}

	// Gets all files
	public function getFiles() {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'project' AND subject_id = '" . $this->id . "'";
		$attachments = $wpdb->get_results($query);
		$attachmentsArray = [];

		foreach($attachments as $attachment) {
			if (unserialize($attachment->type) == 'attachment') {
				$attachmentsArray[] = array(
					'id' 	  => $attachment->id,
					'user_id' => $attachment->user_id,
					'subject' => $attachment->subject,
					'subject_id' => $attachment->subject_id,
					'message' => unserialize($attachment->message),
					'date_created' => $attachment->date_created,
				);
			}
		}

		return $attachmentsArray;
	}

	public function getMentions() {
		$mentionRegex = '/@\[[^\]]*\]\((.*?)\)/i'; // mention regrex to get all @texts
		if (preg_match_all($mentionRegex, $this->description, $matches)) {
			foreach ($matches[1] as $key => $match) {
				$userId = str_replace('user:', '', $match);
				$userData = Members::get_member($userId);

				if (!empty($userData)) {
					$matchSearch = $matches[0][$key];
					$userInfoHtml = '<span class="zpm-message__mention-info"><img class="zpm-mention-info__avatar" src="' . $userData['avatar'] . '">' . $userData['name'] . ' (' . $userData['email'] . ')' . '</span>';
					$matchReplace = '<span class="zpm-message__mention">@' . $userData['name'] . ' ' . $userInfoHtml . '</span>';
					$this->description = str_replace($matchSearch, $matchReplace, $this->description);
				}
			}
		}
		return $this->description;
	}

	public function hasManager($userId) {
		if (in_array($userId, $this->managers)) {
			return true;
		}
		return false;
	}

	public function hasAssignee($userId) {
		if (in_array($userId, $this->assignees)) {
			return true;
		}
		return false;
	}
}