<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class BaseController {

	public function __construct() {
		$this->add_user_roles();
	}

	/**
	* Adds the custom project manager user roles
	*
	* The following user roles are added
	* ZPM Client User - Client Users can only view projects and tasks on the front-end and mark tasks as complete/incomplete. Cannot edit or delete anything or log into the Dashboard
	* ZPM User - Project Users have a bit more control than Clients and can edit tasks and projects as well as create new ones, but access is a bit more restricted than a Project Manager
	* ZPM Project Manager - Project Managers have full access and can create, edit, view, delete, and change settings as they please
	*/
	public function add_user_roles() {
		// remove_role( 'zpm_user' );
		// remove_role( 'zpm_client_user' );
		// remove_role( 'zpm_manager' );
		// remove_role( 'zpm_admin' );

		add_role(
	        'zpm_user',
	        'Zephyr - User',
	        [
	            'read'         => true,
                'upload_files' => true,
	        ]
	    );

	    add_role(
	        'zpm_manager',
	        'Zephyr - Manager',
	        [
	        	'read'         => true,
                'upload_files' => true,
                'manage_options' => true,
	        ]
	    );

	    add_role(
	        'zpm_admin',
	        'Zephyr - Administrator',
	        [
	        	'read'         => true,
                'upload_files' => true,
                'manage_options' => true
	        ]
	    );

	    $user = get_role( 'zpm_user' );

	    if ( !$user->has_cap( 'zpm_caps_init' ) ) {
	    	$user->add_cap('zpm_view_tasks');
		    $user->add_cap('zpm_view_projects');
		    $user->add_cap('zpm_view_project_manager');
		    $user->add_cap('zpm_caps_init');
	    }

	    $manager = get_role( 'zpm_manager' );

	    if ( !$manager->has_cap( 'zpm_caps_init' ) ) {
		    $manager->add_cap('zpm_view_tasks');
		    $manager->add_cap('zpm_view_projects');
		    $manager->add_cap('zpm_create_tasks');
		    $manager->add_cap('zpm_create_projects');
		    $manager->add_cap('zpm_edit_tasks');
		    $manager->add_cap('zpm_edit_projects');
		    $manager->add_cap('zpm_view_project_manager');
		    $manager->add_cap('zpm_caps_init');
		    $manager->add_cap('zpm_delete_tasks');
		    $manager->add_cap('zpm_delete_projects');
		    $manager->add_cap('zpm_create_teams');
		}

	    $admin = get_role( 'zpm_admin' );

	    if ( !$admin->has_cap( 'zpm_caps_init' ) ) {
		    $admin->add_cap('zpm_view_tasks');
		    $admin->add_cap('zpm_view_projects');
		    $admin->add_cap('zpm_create_tasks');
		    $admin->add_cap('zpm_create_projects');
		    $admin->add_cap('zpm_edit_tasks');
		    $admin->add_cap('zpm_edit_projects');
		    $admin->add_cap('zpm_view_project_manager');
		    $admin->add_cap('zpm_caps_init');
		    $admin->add_cap('zpm_access_backend');
		    $admin->add_cap('zpm_delete_projects');
		    $admin->add_cap('zpm_delete_tasks');
		    $admin->add_cap('zpm_create_teams');
		    $admin->add_cap('zpm_create_users');
		}
	}

	/**
	* Returns the user data for a user. If no ID is provided, it will return the data for the current user
	* @param int $id The ID of the user
	* @return object
	*/
	public function get_user_info( $id = null ) {
		if ($id == null) {
			return wp_get_current_user();
		} else {
			return (is_object(get_userdata($id))) ? get_userdata($id)->data : '';
		}
		
	}

	/** 
	* Returns the ID for the current user 
	* @return int
	*/
	function get_user_id() {
		return $this->get_user_info()->data->ID;
	}

	/** 
	* Returns the email for the current user 
	* @return string
	*/
	function get_user_email() {
		return $this->get_user_info()->data->user_email;
	}

	/** 
	* Returns the username for the current user 
	* @return string
	*/
	function get_username() {
		return $this->get_user_info()->data->display_name;
	}

	/** 
	* Returns the username of a user with the given ID
	* @param int $id The ID of the user. If no ID is provided, the function will return false
	* @return string
	*/
	public static function get_username_by_id( $id ) {
		if ( is_object( get_userdata($id) ) ) {
			return get_userdata($id)->data->display_name;
		} else {
			return false;
		}
	}

	/** 
	* Returns the Zephyr profile info for a user that is set in the Zephyr settings page
	* @param int $id The ID of the user.
	* @return array
	*/
	public function get_profile_info( $id ) {
		$user_settings = get_option('zpm_user_' . $id . '_settings');
		return $user_settings;
	}

	/** 
	* Returns all of the users
	* @return object
	*/
	public static function get_users() {
		return get_users();
	}

	/**
	* Gets the custom details of a project manager/project user. It returns the custom profile picture, name, bio, and email
	* @param int $user_id The ID of the user
	* @return array
	*/
	public static function get_project_manager_user( $user_id ) {
		$user_id = is_array($user_id) && isset( $user_id['id'] ) ? $user_id['id'] : $user_id;
		$current_user = get_user_by('ID', $user_id);
		$preferences = get_option( 'zpm_user_' . $user_id . '_settings' );
		$can_zephyr = isset($preferences['can_zephyr']) ? $preferences['can_zephyr'] : "true";

		$notify_activity = isset($preferences['notify_activity']) ? $preferences['notify_activity'] : "0";
		$notify_tasks = isset($preferences['notify_tasks']) ? $preferences['notify_tasks'] : "1";
		$notify_updates = isset($preferences['notify_updates']) ? $preferences['notify_updates'] : "0";
		$notify_task_assigned = isset($preferences['notify_task_assigned']) ? $preferences['notify_task_assigned'] : "1";
		
		$notification_preferences = [
			'notify_activity' => $notify_activity,
			'notify_tasks' 	  => $notify_tasks,
			'notify_updates'  => $notify_updates,
			'notify_task_assigned' => $notify_task_assigned
		];

		if ($user_id !== '-1' && is_object($current_user)) {
			$user_settings_option = get_option('zpm_user_' . $user_id . '_settings');
			$avatar = isset($user_settings_option['profile_picture']) ? $user_settings_option['profile_picture'] : get_avatar_url($user_id);
			$name = isset($user_settings_option['name']) ? $user_settings_option['name'] : $current_user->display_name;
			$description = isset($user_settings_option['description']) ? $user_settings_option['description'] : '';
			$email = isset($user_settings_option['email']) ? $user_settings_option['email'] : $current_user->user_email;

			$user_info = array(
				'id'		  => $user_id,
				'email' 	  => $email,
				'name' 		  => $name,
				'description' => $description,
				'avatar' 	  => $avatar,
				'preferences' => $notification_preferences,
				'can_zephyr'  => $can_zephyr
			);

			return $user_info;
		} else {
			return array(
				'id'		  => '',
				'email' 	  => '',
				'name' 		  => '',
				'description' => '',
				'avatar' 	  => '',
				'preferences' => $notification_preferences,
				'can_zephyr'  => $can_zephyr
			);
		}
	}

	public static function get_member( $id ) {
		return BaseController::get_project_manager_user( $id );
	}

	/**
	* Returns all attachments
	* @return array
	*/
	public static function get_attachments() {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name ORDER BY id DESC";
		$attachments = $wpdb->get_results($query);
		$attachments_array = [];

		foreach($attachments as $attachment) {
			if (unserialize($attachment->type) == 'attachment') {
				$attachments_array[] = array(
					'id' 	  => $attachment->id,
					'user_id' => $attachment->user_id,
					'subject' => $attachment->subject,
					'subject_id' => $attachment->subject_id,
					'message' => unserialize($attachment->message),
					'date_created' => $attachment->date_created
				);
			}
		}

		$attachments_array = apply_filters( 'zpm_files', $attachments_array );

		return $attachments_array;
	}

	/**
	* Gets the data for a certain attachment
	* @param int $attachment_id
	* @return object
	*/
	public static function get_attachment( $attachment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE id = '$attachment_id'";
		$attachments = $wpdb->get_row($query);
		return $attachments;
	}

	// Check if user has access
	public static function user_has_access( $action ) {
		$settings = get_option('zpm_frontend_settings');

		if (!isset($settings[$action]) || $settings[$action] == '0' || current_user_can('administrator')) {
			return true;
		} else if ($settings[$action] == '1') {
			if (current_user_can('zpm_manager')) {
				return true;
			}
		} else if ($settings[$action] == '2') {
			if (current_user_can('zpm_client_user') || current_user_can('zpm_user') || current_user_can('zpm_manager')) {
				return true;
			}
		} else if ($settings[$action] == '3') {
			if (current_user_can('zpm_client_user') || current_user_can('zpm_user') || current_user_can('zpm_manager')) {
				return true;
			}
		} else if ($settings[$action] == '4') {
			if (current_user_can('zpm_client_user') || current_user_can('zpm_frontend_user') || current_user_can('zpm_manager')) {
				return true;
			}
		}
		return false;
	}

	public static function is_pro() {
		if (class_exists('Inc\\ZephyrProjectManager\\Plugin')) {
			return true;
		}

		return false;
	}
}