<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
use Inc\Core\Task;
use Inc\Core\Tasks;
use Inc\Core\Members;
use Inc\Core\Utillities;
use Inc\ZephyrProjectManager;

class Task {
	public $id;
	public $name;
	public $description;
	public $assignees;
	public $completed;
	public $dateDue;
	public $dateStart;
	public $parentId;

	public function __construct( $task ) {
		if (!is_object($task)) {
			$task = Tasks::get_task((int)$task);
		}

		if (empty($task->name)) {
			$task->name = __( 'Untitled Task', 'zephyr-project-manager' );
		}

		$this->id = $task->id;
		$this->name = $task->name;
		$this->description = $task->description;
		$this->completed = $task->completed;
		$this->dateDue = $task->date_due;
		$this->dateStart = $task->date_start;
		$this->parentId = $task->parent_id;
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function getDueDate($format = '') {
		global $zpm_settings;
		$dateTime = new \DateTime($this->dateDue);
		$date = '';
		if ($dateTime->format('Y-m-d') !== '-0001-11-30') {
			if (empty($format)) {
				$date = date_i18n($zpm_settings['date_format'], strtotime($this->dateDue));
			} else {
				$date = date_i18n($format, strtotime($this->dateDue));
			}
		}
		return $date;
	}

	public function getStartDate($format = '') {
		global $zpm_settings;
		$dateTime = new \DateTime($this->dateStart);
		$date = '';
		if ($dateTime->format('Y-m-d') !== '-0001-11-30') {
			if (empty($format)) {
				$date = date_i18n($zpm_settings['date_format'], strtotime($this->dateStart));
			} else {
				$date = date_i18n($format, strtotime($this->dateStart));
			}
		}
		return $date;
	}

	public function addFile( $fileId, $parentId, $type, $userId ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');
		$settings = [
			'user_id' => $userId,
			'subject' => 'task',
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
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'task' AND subject_id = '" . $this->id . "'";
		$attachments = $wpdb->get_results($query);
		$attachmentsArray = [];

		foreach($attachments as $attachment) {
			if (unserialize($attachment->type) == 'attachment') {
				$attachmentsArray[] = array(
					'id' 	  	   => $attachment->id,
					'user_id' 	   => $attachment->user_id,
					'subject' 	   => $attachment->subject,
					'subject_id'   => $attachment->subject_id,
					'message' 	   => unserialize($attachment->message),
					'date_created' => $attachment->date_created,
				);
			}
		}

		return $attachmentsArray;
	}

	public function getComments() {
		global $wpdb;
		$results = [];
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'task' AND subject_id = '$this->id' ORDER BY date_created DESC";
		$comments = $wpdb->get_results($query);

		foreach ($comments as $comment) {
			$message = new Message($comment);
			$results[] = $message;
		}

		return $results;
	}

	public function getMentions() {
		$mentionRegex = '/@\[[^\]]*\]\((.*?)\)/i'; // mention regrex to get all @texts
		if (preg_match_all($mentionRegex, $this->content, $matches)) {
			foreach ($matches[1] as $key => $match) {
				$userId = str_replace('user:', '', $match);
				$userData = Members::get_member($userId);

				if (!empty($userData)) {
					$matchSearch = $matches[0][$key];
					$userInfoHtml = '<span class="zpm-message__mention-info"><img class="zpm-mention-info__avatar" src="' . $userData['avatar'] . '">' . $userData['name'] . ' (' . $userData['email'] . ')' . '</span>';
					$matchReplace = '<span class="zpm-message__mention">@' . $userData['name'] . ' ' . $userInfoHtml . '</span>';
					$this->content = str_replace($matchSearch, $matchReplace, $this->content);
				}
			}
		}
		return $this->content;
	}

	public function getSubtasks() {
		global $wpdb;
		$results = [];
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE parent_id = '$this->id'";
		$subtasks = $wpdb->get_results($query);
		foreach ($subtasks as $subtask) {
			$results[] = new Task($subtask);
		}
		return $results;
	}

	public function isCompleted() {
		if ($this->completed == '1') {
			return true;
		}

		return false;
	}
}