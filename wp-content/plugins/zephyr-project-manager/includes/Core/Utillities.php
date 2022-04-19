<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Api\Emails;
use Inc\Core\Project;
use Inc\Core\Utillities;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager;

class Utillities {
	public function __construct() {

	}

	/**
	* Returns all of the users
	* @return object
	*/
	public static function get_users( $formatted = true ) {
		$users = get_users();
		$results = [];
		$user_array = [];
		$currentUserId = get_current_user_id();
		$canView = Utillities::canViewMembers();
		
		if ( $formatted ) {
			foreach ($users as $user) {
				if ($canView || $user->ID == $currentUserId) {
					$user = Members::get_member( $user->ID );
					array_push( $user_array, $user );
				}
			}
		} else {
			foreach ($users as $user) {

				if ($canView || $user->ID == $currentUserId) {
					$user_array[] = $user;
				}
			}

		}

		foreach ($user_array as $user) {
			if (apply_filters( 'zpm_should_show_user', true, $user )) {
				$results[] = $user;
			}
		}

		return $results;
	}

	/**
	* Gets the custom details of a project manager/project user. It returns the custom profile picture, name, bio, and email
	* @param int $id The ID of the user
	* @return array
	*/
	public static function get_user( $id ) {
		$current_user = get_user_by('ID', $id);
		$preferences = get_option( 'zpm_user_' . $id . '_settings' );
		$notify_activity = isset( $preferences['notify_activity'] ) ? $preferences['notify_activity'] : false;
		$notify_tasks = isset( $preferences['notify_tasks'] ) ? $preferences['notify_tasks'] : false;
		$notify_updates = isset( $preferences['notify_updates'] ) ? $preferences['notify_updates'] : false;

		$notification_preferences = [
			'notify_activity' => $notify_activity,
			'notify_tasks' 	  => $notify_updates,
			'notify_updates'  => $notify_updates
		];

		if ($id !== '-1' && is_object($current_user)) {
			$user_settings_option = get_option('zpm_user_' . $id . '_settings');
			$avatar = isset($user_settings_option['profile_picture']) ? $user_settings_option['profile_picture'] : get_avatar_url($id);
			$name = isset($user_settings_option['name']) ? $user_settings_option['name'] : $current_user->display_name;
			$description = isset($user_settings_option['description']) ? $user_settings_option['description'] : '';
			$email = isset($user_settings_option['email']) ? $user_settings_option['email'] : $current_user->user_email;
			$user_info = array(
				'id'		  => $id,
				'email' 	  => $email,
				'name' 		  => $name,
				'description' => $description,
				'avatar' 	  => $avatar,
				'preferences' => $notification_preferences
			);
			return $user_info;
		} else {
			return array(
				'id'		  => '',
				'email' 	  => '',
				'name' 		  => '',
				'description' => '',
				'avatar' 	  => '',
				'preferences' => $notification_preferences
			);
		}
	}


	/* Convert hexdec color string to rgb(a) string */
	public static function hex2rgba( $color, $opacity = false ) {

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
	          return $default;

			//Sanitize $color if "#" is provided
	        if ($color[0] == '#' ) {
	        	$color = substr( $color, 1 );
	        }

	        //Check if color has 6 or 3 characters and get values
	        if (strlen($color) == 6) {
	                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	        } elseif ( strlen( $color ) == 3 ) {
	                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	        } else {
	                return $default;
	        }

	        //Convert hexadec to rgb
	        $rgb =  array_map('hexdec', $hex);

	        //Check if opacity is set(rgba or rgb)
	        if($opacity){
	        	if(abs($opacity) > 1)
	        		$opacity = 1.0;
	        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	        } else {
	        	$output = 'rgb('.implode(",",$rgb).')';
	        }

	        //Return rgb(a) color string
	        return $output;
	}

	public static function get_access_level() {
		$access_settings = get_option('zpm_access_settings');
      	$access_level = $access_settings ? $access_settings : 'manage_options';

      	switch ($access_level) {
      		case 'zpm_user':
      			$access_level = 'read';
      			break;
      		case 'zpm_manager':
      			$access_level = 'edit_pages';
      			break;
      		default:
      			$access_level = 'manage_options';
      			break;
      	}
      	$access_level = 'read';
      	return $access_level;
	}

	// Retrieve all general settings
	public static function general_settings() {
		$args = get_option('zpm_general_settings');
		$defaults = [
			'project_access' 	  		  => '0',
			'create_tasks'		  		  => '0',
			'view_tasks'		  		  => '0',
			'create_projects'	  		  => '0',
			'view_files'		  		  => '0',
			'can_complete_tasks'		  => '0',
			'primary_color' 	  		  => '#14aaf5',
			'primary_color_dark'  		  => '#147be2',
			'primary_color_light' 		  => '#60bbe9',
			'first_day'			  		  => '1',
			'date_format'		     	  => 'd M Y',
			'show_time'					  => false,
			'kanban_priority_highlight'   => '0',
			'hide_default_task_fields' 	  => '0',
			'show_custom_field_task_list' => '0',
			'email_from_name'			  => 'Zephyr Project Manager',
			'email_from_email'			  => 'no-reply@zephyr-one.com',
			'display_project_id'		  => '0',
			'display_database_project_id' => '0',
			'display_task_id'			  => '0',
			'custom_css'				  => '',
			'enable_category_grouping'	  => false,
			'projects_per_page' 		  => 12,
			'tasks_per_page'			  => 20,
			'email_mentions_subject'	  =>  __( 'You have been mentioned in a comment', 'zephyr-project-manager' ),
			'email_mentions_content'	  =>  __( 'You have been mentioned in a comment: {messageText}', 'zephyr-project-manager' ),
			'node_enabled'				  => true,
			'view_own_files'			  => false,
			'view_members'				  => true,
			'override_default_emails'	  => false,
			'default_project'			  => '-1',
			'default_assignee'			  => '-1'
		];
		$defaults = apply_filters( 'zpm_settings_defaults', $defaults );

		return wp_parse_args( $args, $defaults );
	}

	public static function save_general_settings( $args ) {
		$defaults = Utillities::general_settings();
		update_option( 'zpm_general_settings', wp_parse_args( $args, $defaults ) );
	}

	public static function check_save_general_settings() {
		if (isset($_POST['zpm_save_general_settings'])) {
			check_admin_referer('zpm_save_general_settings');

			$settings = array();

			if (isset($_POST['zpm_view_projects'])) {
				$settings['project_access'] = $_POST['zpm_view_projects'];
			}
			if (isset($_POST['zpm_backend_primary_color'])) {
				$settings['primary_color'] = $_POST['zpm_backend_primary_color'];
			}
			if (isset($_POST['zpm_backend_primary_color_dark'])) {
				$settings['primary_color_dark'] = $_POST['zpm_backend_primary_color_dark'];
			}
			if (isset($_POST['zpm_backend_primary_color_light'])) {
				$settings['primary_color_light'] = $_POST['zpm_backend_primary_color_light'];
			}
			if (isset($_POST['zpm-settings-first-day'])) {
				$settings['first_day'] = $_POST['zpm-settings-first-day'];
			}
			if (isset($_POST['zpm-settings-date-format'])) {
				$settings['date_format'] = $_POST['zpm-settings-date-format'];
			}
			if (isset($_POST['zpm-settings-create-tasks'])) {
				$settings['create_tasks'] = $_POST['zpm-settings-create-tasks'];
			}
			if (isset($_POST['zpm-settings-create-projects'])) {
				$settings['create_projects'] = $_POST['zpm-settings-create-projects'];
			}
			if (isset($_POST['zpm-settings-view-tasks'])) {
				$settings['view_tasks'] = $_POST['zpm-settings-view-tasks'];
			}
			if (isset($_POST['zpm-settings-view-files'])) {
				$settings['view_files'] = $_POST['zpm-settings-view-files'];
			}
			if (isset($_POST['zpm-settings__default-project'])) {
				$settings['default_project'] = $_POST['zpm-settings__default-project'];
			}
			if (isset($_POST['zpm-settings__default-assignee'])) {
				$settings['default_assignee'] = $_POST['zpm-settings__default-assignee'];
			}

			if (isset($_POST['zpm-settings-display-project-id'])) {
				$settings['display_project_id'] = $_POST['zpm-settings-display-project-id'];
			} else {
				$settings['display_project_id'] = '0';
			}

			if (isset($_POST['zpm-settings-display-database-project-id'])) {
				$settings['display_database_project_id'] = $_POST['zpm-settings-display-database-project-id'];
			} else {
				$settings['display_database_project_id'] = '0';
			}

			if (isset($_POST['zpm-settings__display-task-id'])) {
				$settings['display_task_id'] = '1';
			} else {
				$settings['display_task_id'] = '0';
			}

			if (isset($_POST['zpm-settings-email-from-name'])) {
				$settings['email_from_name'] = $_POST['zpm-settings-email-from-name'];
			}

			if (isset($_POST['zpm-settings-email-from-email'])) {
				$settings['email_from_email'] = $_POST['zpm-settings-email-from-email'];
			}

			if (isset($_POST['zpm-settings__enable-category-grouping'])) {
				$settings['enable_category_grouping'] = true;
			} else {
				$settings['enable_category_grouping'] = false;
			}

			if (isset($_POST['zpm-setting__show-time'])) {
				$settings['show_time'] = true;
			} else {
				$settings['show_time'] = false;
			}

			if (isset($_POST['zpm-settings__projects-per-page'])) {
				$settings['projects_per_page'] = sanitize_text_field( $_POST['zpm-settings__projects-per-page'] );
			}

			if (isset($_POST['zpm-settings__tasks-per-page'])) {
				$settings['tasks_per_page'] = sanitize_text_field( $_POST['zpm-settings__tasks-per-page'] );
			}

			if (isset($_POST['zpm-settings__can-complete-tasks'])) {
				$settings['can_complete_tasks'] = $_POST['zpm-settings__can-complete-tasks'];
			}

			if (isset($_POST['zpm-settings__email-mentions-content'])) {
				$settings['email_mentions_content'] = sanitize_text_field( $_POST['zpm-settings__email-mentions-content'] );
			}

			if (isset($_POST['zpm_view_own_files'])) {
		    	$settings['view_own_files'] = true;
		    } else {
		    	$settings['view_own_files'] = false;
		    }

		    if (isset($_POST['zpm-settings__view-members'])) {
		    	$settings['view_members'] = true;
		    } else {
		    	$settings['view_members'] = false;
		    }

		    if (isset($_POST['zpm-settings__override-default-emails'])) {
		    	$settings['override_default_emails'] = true;
		    } else {
		    	$settings['override_default_emails'] = false;
		    }

		    if (isset($_POST['zpm-settings__enable-node'])) {
		    	$settings['node_enabled'] = true;
		    } else {
		    	$settings['node_enabled'] = false;
		    }

			// Save custom user roles
			if (Utillities::is_admin()) {
				$user_caps = Utillities::get_caps();
				$zpm_roles = Utillities::get_roles();

				foreach ($zpm_roles as $key => $role) {
					if (isset($_POST['zpm-settings-user-caps-' . $key])) {
						$postCaps = $_POST['zpm-settings-user-caps-' . $key];

						foreach ($user_caps as $cap) {
							$role['role']->add_cap( $cap );

							if (in_array($cap, $postCaps)) {
								$role['role']->add_cap( $cap );
							} else {
								$role['role']->remove_cap( $cap );
							}
						}
					}
				}
			}


			Utillities::save_general_settings( $settings );
		}

		if (isset($_POST['zpm-settings__advanced-submit'])) {
			$settings = [];
			if (isset($_POST['zpm-settings__custom-css'])) {
				$settings['custom_css'] = trim( sanitize_textarea_field( $_POST['zpm-settings__custom-css'] ) );
			}
			$settings = apply_filters( 'zpm_advanced_settings_data', $settings );
			Utillities::save_general_settings( $settings );
		}
	}

	public static function generate_random_string($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public static function update_user_access( $user_id, $access = false ) {
		$settings = get_option( 'zpm_user_' . $user_id . '_settings' );
		$settings['can_zephyr'] = $access;
	    update_option( 'zpm_user_' . $user_id . '_settings', $settings );
	}

	public static function dismiss_notice( $notice_id ) {
		$notices = maybe_unserialize( get_option( 'zpm_notices', array() ) );
		$notices[$notice_id] = 'dismissed';
		update_option( 'zpm_notices', serialize( $notices ) );
	}

	public static function can_zephyr( $user_id ) {
		$settings = Utillities::get_user_settings( $user_id );
		$settings['can_zephyr'] = isset($settings['can_zephyr']) ? $settings['can_zephyr'] : "true";
		return $settings['can_zephyr'];
	}

	public static function notice_is_dismissed( $notice_id ) {
		$notices = maybe_unserialize( get_option( 'zpm_notices', array() ) );
		if ( isset( $notices[$notice_id] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_user_settings( $user_id ) {
		if ( empty( $user_id ) ) {
			return [];
		}
		$settings = BaseController::get_project_manager_user( $user_id );
		$settings['can_zephyr'] = isset($settings['can_zephyr']) ? $settings['can_zephyr'] : "true";
		return (array) $settings;
	}

	public static function save_user_settings( $user_id, $args ) {
		$defaults = Utillities::get_user_settings( $user_id );
		$settings = wp_parse_args( $args, $defaults );
		update_option( 'zpm_user_' . $user_id . '_settings', $settings );
	}

	public static function get_one_signal_device_ids() {
		$devices = maybe_unserialize( get_option( 'zpm_devices', array() ) );
		$device_ids = [];

		foreach ( (array) $devices as $device) {
			if ( isset( $device['one_signal_user_id'] ) ) {
				$device_ids[] = $device['one_signal_user_id'];
			}
		}

		return $device_ids;
	}

	public static function table_column_exists( $table_name, $column_name ) {
		global $wpdb;

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return true;
		}
		$column = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
			DB_NAME, $table_name, $column_name
		) );
		if ( ! empty( $column ) ) {
			return true;
		}
		return false;
	}

	public static function get_project_url() {
		$settings = get_option('zpm_frontend_settings', array());
		$front_page = isset($settings['front_page']) ? $settings['front_page'] : -1;
		return get_permalink( $front_page );
	}

	public static function get_frontend_url($query = '') {
		$base = Utillities::get_project_url();
		if (empty($query)) {
			return $base;
		} else {
			$permalink_structure = get_option( 'permalink_structure' );
			$query_prefix = !empty( $permalink_structure ) ? $base . '?' : $base . '&';
			return  $query_prefix . $query;
		}

	}

	public static function adjust_brightness($hex, $steps) {
	    // Steps should be between -255 and 255. Negative = darker, positive = lighter
	    $steps = max(-255, min(255, $steps));

	    // Normalize into a six character long hex string
	    $hex = str_replace('#', '', $hex);
	    if (strlen($hex) == 3) {
	        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
	    }

	    // Split into three parts: R, G and B
	    $color_parts = str_split($hex, 2);
	    $return = '#';

	    foreach ($color_parts as $color) {
	        $color   = hexdec($color); // Convert to decimal
	        $color   = max(0,min(255,$color + $steps)); // Adjust color
	        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
	    }

	    return $return;
	}

	public static function get_priorities() {
	    $priorities = array(
	    	'priority_none' => __( 'No Priority', 'zephyr-project-manager' ),
	    	'priority_low' => __( 'Low', 'zephyr-project-manager' ),
	    	'priority_medium' => __( 'Medium', 'zephyr-project-manager' ),
	    	'priority_high' => __( 'High', 'zephyr-project-manager' ),
	    	'priority_critical' => __( 'Critical', 'zephyr-project-manager' ),
	    );

	    return $priorities;
	}

	public static function get_priority_label( $priority ) {
	    $priorities = Utillities::get_status( $priority );
	    $label = isset( $priority['name'] ) ? $priority['name'] : __( 'None', 'zephyr-project-manager' );
	    return $label;
	}

	public static function can_create_tasks( $user_id = null ) {
		$general_settings = Utillities::general_settings();
		$current_user = get_current_user_id();

		if (!apply_filters( 'zpm_override_can_create_tasks', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_create_tasks' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		if (Utillities::is_zephyr_role($current_user)) {
			return false;
		} else {
			return true;
		}

		// Deprecated below

		if ( Utillities::user_has_role( get_current_user_id(), 'zpm_user' ) || Utillities::user_has_role( $current_user, 'zpm_manager' ) ) {
			if ( !current_user_can( 'zpm_create_tasks' ) ) {
				return false;
			}
		}

		if ( isset( $general_settings['create_tasks'] ) ) {

			switch ($general_settings['create_tasks']) {
				case '0':
					return true;
					break;
				case '1':
					if (!current_user_can( 'administrator' )) {
							return false;
					}
					break;
				case 'zpm_manager':
					if ( !zpm_user_has_role( $current_user, 'zpm_manager' ) && !current_user_can( 'administrator' ) ) {
						if ( !current_user_can( 'zpm_create_tasks' ) ) {
							return false;
						}
					}
					break;
				case 'zpm_user':
					if ( !zpm_user_has_role( $current_user, 'zpm_user' ) && !zpm_user_has_role( $current_user, 'zpm_manager' ) && !current_user_can( 'administrator' ) ) {
						if ( !current_user_can( 'zpm_create_tasks' ) ) {
							return false;
						}
					}
					break;
				default:
					return true;
					break;
			}
			return true;

		}

		return true;
	}

	public static function can_edit_tasks( $user_id = null ) {
		$general_settings = Utillities::general_settings();
		$current_user = get_current_user_id();

		if ( Utillities::user_has_role( $current_user, 'zpm_user' ) || Utillities::user_has_role( $current_user, 'zpm_manager' ) ) {
			if ( !current_user_can( 'zpm_edit_tasks' ) ) {
				return false;
			}
		}

		return true;
	}

	public static function can_edit_projects( $user_id = null ) {
		$general_settings = Utillities::general_settings();
		$current_user = get_current_user_id();

		if ( Utillities::user_has_role( $current_user, 'zpm_user' ) || Utillities::user_has_role( $current_user, 'zpm_manager' ) ) {
			if ( !current_user_can( 'zpm_edit_projects' ) ) {
				return false;
			}
		}

		return true;
	}

	public static function can_create_projects() {

		$general_settings = Utillities::general_settings();
		$user_roles = Utillities::get_user_roles();
		$current_user = get_current_user_id();

		if (!apply_filters( 'zpm_override_can_create_projects', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_create_projects' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		if (Utillities::is_zephyr_role($current_user)) {
			return false;
		} else {
			return true;
		}

		// Deprecated below

		if (Utillities::is_zephyr_role($current_user)) {
			if ( !current_user_can( 'zpm_create_projects' ) ) {
				return false;
			}
		}

		if ( isset($general_settings['create_projects'])) {

			switch ($general_settings['create_projects']) {
				case '0':
					return true;
					break;
				case '1':
					if (!current_user_can( 'administrator' )) {
							return false;
					}
					break;
				case 'zpm_manager':
					if ( !zpm_user_has_role( $current_user, 'zpm_manager' ) && !current_user_can( 'administrator' ) ) {
							return false;
					}
					break;
				case 'zpm_user':
					if ( !zpm_user_has_role( $current_user, 'zpm_user' ) && !zpm_user_has_role( $current_user, 'zpm_manager' ) && !current_user_can( 'administrator' ) ) {
							return false;
					}
					break;
				default:
					return true;
					break;
			}
			return true;
		}

		return true;
	}

	public static function can_view_task( $task ) {

		if ( !is_object($task) ) {
			return false;
		}

		return apply_filters( 'zpm_can_view_task', true, $task );
	}

	public static function can_view_file( $message ) {
		$general_settings = Utillities::general_settings();
		$current_user = (int) get_current_user_id();

		if ( isset($general_settings['view_files'])) {

			switch ($general_settings['view_files']) {
				case '0':
					return true;
					break;
				case '1':
					if ($current_user !== (int) $message['user_id'] && !current_user_can( 'administrator' )) {
						return false;
					}
					break;
				default:
					return true;
					break;
			}
			return true;
		}
		return true;
	}

	public static function canViewMembers() {
		if (current_user_can( 'administrator' )) {
			return true;
		}

		$generalSettings = Utillities::general_settings();

		if ($generalSettings['view_members'] == true) {
			return true;
		}

		return false;
	}

	public static function get_localized_strings() {
		return include(ZPM_PLUGIN_PATH . 'includes/strings.php');
	}

	public static function get_new_features() {
	    $features = array(
	    	array(
	    		'title' => 'Set specific hours and minutes for tasks',
	    		'description' => 'You can now set the start and end minutes and hours for a task as well as the date.'
	    	),
	    	array(
	    		'title' => 'New WooCommerce Integration',
	    		'description' => 'There is a new WooCommerce integration which allows you to integrate with WooCommerce and create tasks and projects when orders are placed or payments are made and lots more. You can get it <a href="https://zephyr-one.com/woocommerce-integration">here</a>'
	    	)
	    );

	    $coming_soon_features = array(
	    	array(
	    		'title' => 'Google Calendar and Drive integration',
	    		'description' => 'New Google Calendar and Drive integrations are coming soon to sync your tasks with your Google Calendar and upload files from Google Drive.'
	    	),
	    	array(
	    		'title' => 'Even more custom fields and custom field templates',
	    		'description' => 'More custom fields and predefined templates are also being developed to be released soon'
	    	)
	    );

	    ob_start(); ?>

	    <div id="zpm-info__features">
	    	<?php foreach ($features as $feature) : ?>
	    		<div class="zpm-info__feature">
	    			<p class="zpm-feature__title"><?php echo $feature['title']; ?></p>
	    			<p class="zpm-feature__description">
	    				<?php echo $feature['description']; ?>
	    			</p>
	    			<?php if (isset($feature['links'])) : ?>
	    				<div class="zpm-feature__links">
	    					<?php foreach ( $feature['links'] as $link ) : ?>
	    						<a href="<?php echo $link['url'] ?>" target="_blank"><?php echo $link['text']; ?></a>
	    					<?php endforeach; ?>
	    				</div>
	    			<?php endif; ?>
	    		</div>
	    	<?php endforeach; ?>

	    	<p class="zpm-info__subtitle"><?php _e( 'Coming Soon...', 'zephyr-project-manager' ); ?></p>
	    	<?php foreach ($coming_soon_features as $feature) : ?>
	    		<div class="zpm-info__feature">
	    			<p class="zpm-feature__title"><?php echo $feature['title']; ?></p>
	    			<p class="zpm-feature__description"><?php echo $feature['description']; ?></p>
	    		</div>
	    	<?php endforeach; ?>
	    </div>

	    <?php $html = ob_get_clean();

	    echo $html;
	}

	public static function get_pro_link() {
		return 'https://zephyr-one.com/purchase-pro';
	}

	public static function get_page_url( $page ) {
		$link = '/admin.php?page=' . $page;
		return esc_url(admin_url( $link ));
	}

	public static function getTaskLink($taskId) {
		$base = Utillities::get_page_url('zephyr_project_manager_tasks');
		$link = $base . '&action=view_task&task_id=' . $taskId;
		return $link;
	}

	public static function get_custom_admin_css() {
		// Build this function
	}

	public static function get_project_chart_data( $project_id ) {
		$chart_data = [];
		$return_data = [];

		if ( $project_id !== "-1") {

			$current_chart_data = get_option('zpm_chart_data');
			$project = Projects::get_project( $project_id );
			if (!is_object($project)) {
				return [];
			}
			$prev_data = array();
			$return_data = array();
			$is_empty = true;

			$total_tasks = Tasks::get_project_task_count( $project_id );
			$completed_tasks = Tasks::get_project_completed_tasks( $project_id );
			$pending_tasks = $total_tasks - $completed_tasks;
			$overdue_tasks = $args = array(
				'project_id' => $project->id
			);
			$overdue_tasks = sizeof( Tasks::get_overdue_tasks( $args ) );

			if ( is_object( $project ) ) {
				$chart_data = isset($current_chart_data[$project->id]) ? $current_chart_data[$project->id] : array();
			}

			for ($i = 0; $i < 30; $i++) {
				$day = date("d M", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-" . $i . " day" ) );

				if (Utillities::chart_data_has_date( $chart_data, $day)) {
					$prev_data = Utillities::get_chart_entry_by_date( $chart_data, $day);
					$return_data[] = $prev_data;
				} else {
					$prev_data['date'] = $day;
					$chart_data[] = $prev_data;
					$return_data[] = $prev_data;
				}

			}

			foreach ($return_data as $data) {
				if (isset($data['completed_tasks']) || isset($data['pending_tasks']) || isset($data['overdue_tasks'])) {
					$is_empty = false;
				}
			}

			if ($is_empty) {
				foreach ($return_data as $key => $value) {
					$return_data[$key]['completed_tasks'] = $completed_tasks;
					$return_data[$key]['pending_tasks'] = $pending_tasks;
					$return_data[$key]['overdue_tasks'] = $overdue_tasks;
				}
			}
		} else {
		}

		return array_reverse( $return_data );;
	}

	public static function chart_data_has_date( $chart_data, $date ) {
		$has_date = false;
		foreach ($chart_data as $key => $value) {
			if (isset($chart_data[$key])) {
				if ($chart_data[$key]['date'] == $date) {
					return true;
				}
			}
		}
		return $has_date;
	}

	public static function get_chart_entry_by_date( $chart_data, $date ) {
		$data = array();

		foreach ($chart_data as $key => $value) {
			if (isset($chart_data[$key])) {
				if ($chart_data[$key]['date'] == $date) {
					return $chart_data[$key];
				}
			}
		}
		return $data;
	}

	public static function get_default_statuses( $type = 'priority' ) {
		$statuses = array(
			'not_started' => array(
				'name' => __( 'Not Started', 'zephyr-project-manager' ),
				'color' => '#eee',
				'type' => 'status'
			),
			'in_progress' => array(
				'name' => __( 'In Progress', 'zephyr-project-manager' ),
				'color' => '#ffa73a',
				'type' => 'status'
			),
			'completed' => array(
				'name' => __( 'Completed', 'zephyr-project-manager' ),
				'color' => '#0ed98e',
				'type' => 'status'
			)
		);
		$priorities = array(
			'low' => array(
				'name' => __( 'Low', 'zephyr-project-manager' ),
				'color' => '#0ed98e',
				'type' => 'priority'
			),
			'medium' => array(
				'name' => __( 'Medium', 'zephyr-project-manager' ),
				'color' => '#ffe000',
				'type' => 'priority'
			),
			'high' => array(
				'name' => __( 'High', 'zephyr-project-manager' ),
				'color' => '#ffa73a',
				'type' => 'priority'
			),
			'critical' => array(
				'name' => __( 'Critical', 'zephyr-project-manager' ),
				'color' => '#ff0047',
				'type' => 'priority'
			)
		);

		if ($type == 'priority') {
			$results = $priorities;
		} else {
			$results = $statuses;
		}
		return $results;
	}

	// This function is to facilitate new users moving to the independently managed statuses and priorities now
	public static function check_statuses() {
		$statuses = Utillities::get_statuses( 'all' );

		foreach ($statuses as $key => $value) {
			if ( !isset( $value['type'] ) ) {
				$statuses[$key]['type'] = 'priority';
			}
		}

		update_option( 'zpm_statuses', serialize( $statuses ) );
	}

	public static function get_statuses( $type = 'priority' ) {
		$statuses = (array) maybe_unserialize( get_option( 'zpm_statuses', array() ) );

		foreach ($statuses as $key => $value) {
			if ( !isset( $value['type'] ) ) {
				$statuses[$key]['type'] = 'priority';
			}
		}

		update_option( 'zpm_statuses', serialize( $statuses ) );

		$defaults = Utillities::get_default_statuses();
		$defaults_statuses = Utillities::get_default_statuses( 'status' );

		if ( empty( $statuses ) ) {
			update_option( 'zpm_statuses', serialize( wp_parse_args( $defaults, $defaults_statuses ) ) );
			return $defaults;
		}

		$statuses = (array) maybe_unserialize( get_option( 'zpm_statuses', array() ) );
		$results = [];

		foreach ( $statuses as $slug => $status ) {
			if ( !isset( $status['type'] ) || $status['type'] == $type || $type == "all" ) {
				$results[$slug] = $status;
			}
		}

		return (array) $results;
	}

	public static function get_status( $slug ) {
		$statuses = maybe_unserialize( get_option( 'zpm_statuses', array() ) );
		$defaults = Utillities::get_default_statuses();
		$statuses = wp_parse_args( $statuses, $defaults );

		switch ($slug) {
			case 'priority_low':
				$slug = 'low';
				break;
			case 'priority_high':
				$slug = 'high';
				break;
			case 'priority_medium':
				$slug = 'medium';
				break;
			case 'priority_critical':
				$slug = 'critical';
				break;
			default:
				break;
		}

		if (isset($statuses[$slug])) {
			return $statuses[$slug];
		} else {
			return array(
				'name' => __( 'None', 'zephyr-project-manager' ),
				'color' => '',
				'type' => 'priority'
			);
		}
	}

	public static function getStatusSlug($statusName) {

		if (ctype_lower($statusName)) {
			return $statusName;
		}

		$statuses = Utillities::get_statuses('status');

		foreach ($statuses as $slug => $status) {
			if (strtolower($status['name']) == strtolower($statusName) || $slug == strtolower($statusName)) {
				return $slug;
			}
		}
		return '';
	}

	public static function create_status( $name, $color, $type = 'priority' ) {
		$slug = Utillities::slugify( $name );
		$status = array(
			'name' => $name,
			'color' => $color,
			'type' => $type
		);

		$statuses = Utillities::get_statuses( 'all' );
		$statuses[$slug] = $status;
		update_option( 'zpm_statuses', serialize( $statuses ) );

		$status['slug'] = $slug;
		return $status;
	}

	public static function update_status( $slug_id, $name, $color, $type = 'priority' ) {
		$slug = Utillities::slugify( $name );
		$status = array(
			'name' => $name,
			'color' => $color,
			'type' => $type
		);

		Utillities::delete_status( $slug_id );
		$statuses = Utillities::get_statuses( 'all' );
		$statuses[$slug_id] = $status;
		update_option( 'zpm_statuses', serialize( $statuses ) );
		$status['slug'] = $slug_id;
		return $status;
	}

	public static function delete_status( $slug, $type = 'priority' ) {
		$slug = Utillities::slugify( $slug );
		$statuses = Utillities::get_statuses( 'all' );
		$deleted = false;

		foreach ($statuses as $key => $value) {
			if ($deleted) {
				continue;
			}
			if ( ( $key == $slug && $type == $value['type'] ) || ( $key == $slug && $type == '' ) ) {
				unset( $statuses[$key] );
				$deleted = true;
			}
		}

		update_option( 'zpm_statuses', serialize( $statuses ) );

		return $statuses;
	}

	public static function slugify($text) {
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		$text = trim($text, '-');
		$text = preg_replace('~-+~', '-', $text);
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	public static function zephyr_modal( $id, $title, $content, $buttons ) {
		?>
		<div id="<?php echo $id; ?>" class="zpm-modal zephyr-modal">
			<div class="zpm-modal-inner">

				<h3 class="zpm-modal-header"><?php echo $title; ?></h3>

				<?php echo $content; ?>

			</div>

			<div class="zpm-modal-footer">
				<?php foreach( $buttons as $button ) : ?>
					<button class="zpm_button" name="<?php echo $button['id']; ?>" id="<?php echo $button['id']; ?>" <?php echo isset( $button['form'] ) ? 'form="' . $button['form'] . '" ' : ''; ?>><?php echo $button['text']; ?></button>
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}

	public static function enable_errors() {
		error_reporting( E_ALL );
		ini_set( 'display_errors', '1' );
	}

	public static function generate_random_number() {
		$six_digit_random_number = mt_rand(100000, 999999);
		return $six_digit_random_number;
	}

	public static function is_admin() {
		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_manager' ) || current_user_can( 'zpm_admin' )) {
			return true;
		}
		return false;
	}

	public static function get_user_roles( $user_id = null ) {
		$user_id = !is_null( $user_id ) ? $user_id : get_current_user_id();
		$user_meta = get_userdata( $user_id );
		$user_roles = $user_meta->roles;
		return $user_roles;
	}

	public static function get_roles() {
		$administrator_role = get_role( 'zpm_admin' );
		$manager_role = get_role( 'zpm_manager' );
		$user_role = get_role( 'zpm_user' );

		$roles = array(
			'zpm_administrator' => array(
				'name' => 'ZPM Administrator',
				'role' => $administrator_role
			),
			'zpm_manager' => array(
				'name' => 'ZPM Manager',
				'role' => $manager_role
			),
			'zpm_user' => array(
				'name' => 'ZPM User',
				'role' => $user_role
			),
		);
		$results = apply_filters( 'zpm_roles', $roles );
		return $results;
	}

	public static function get_caps() {
		global $wpdb;
		$roles = get_option($wpdb->prefix . 'user_roles');
		$user_caps = array(
			'zpm_all_zephyr_capabilities',
			'manage_options',
			'zpm_view_tasks',
			'zpm_view_assigned_tasks',
			'zpm_view_projects',
			'zpm_view_assigned_projects',
			'zpm_create_tasks',
			'zpm_edit_tasks',
			'zpm_delete_tasks',
			'zpm_create_projects',
			'zpm_edit_projects',
			'zpm_delete_projects',
			'zpm_edit_assigned_projects',
			'zpm_upload_files',
			'zpm_create_teams',
			'zpm_create_users',
			'zpm_access_backend'
		);

		$user_caps = apply_filters( 'zpm_caps', $user_caps );

		// foreach ($roles as $key => $value) {
		// 	foreach ($value['capabilities'] as $key => $cap) {
		// 		if ( !in_array($key, $user_caps) ) {
		// 			$user_caps[] = $key;
		// 		}
		// 	}
		// }
		return $user_caps;
	}

	public static function update_user_project_settings( $user_id, $project_id, $settings ) {
		$project_settings = get_user_meta( $user_id, 'zpm_project_settings', true );
		$project_settings = (array) maybe_unserialize( $project_settings );

		$defaults = Utillities::getDefaultProjectUserSettings();

		$settings = wp_parse_args( $settings, $defaults );
		$project_settings[$project_id] = $settings;
		update_user_meta( $user_id, 'zpm_project_settings', serialize( $project_settings ) );
	}

	public static function get_user_project_settings( $user_id, $project_id ) {
		$project_settings = get_user_meta( $user_id, 'zpm_project_settings', true );
		$project_settings = (array) maybe_unserialize( $project_settings );

		$defaults = Utillities::getDefaultProjectUserSettings();

		if ( isset( $project_settings[$project_id] ) ) {
			return wp_parse_args( $project_settings[$project_id], $defaults );
		} else {
			return $defaults;
		}
	}

	public static function getDefaultProjectUserSettings() {
		$defaultArgs = array(
			'weekly_update_email' 		   => '0',
			'task_completed_email' 		   => '1',
			'new_task_email' 			   => '1',
			'new_subtask_email' 		   => '0',
			'task_assignee_comments_email' => '0',
			'task_comments_email' 		   => '0',
			'project_comments_email' 	   => '0',
		);

		$defaults = apply_filters( 'zpm_project_user_defaults', $defaultArgs );
		return $defaults;
	}

	public static function check_user_project_setting( $user_id, $project_id, $setting ) {
		$settings = Utillities::get_user_project_settings( $user_id, $project_id );

		if ( !isset( $settings[$setting] ) ) {
			return false;
		}

		if ( $project_id == '-1' ) {
			return true;
		} else {
			if ( !Projects::is_project_member( $project_id, $user_id ) ) {
				return false;
			}
		}

		if ( $settings[$setting] == '1' ) {
			return true;
		} else {
			return false;
		}
		return false;
	}

	public static function is_past_date( $date ) {

		$now = new DateTime();

		$target = new DateTime($date);

		if ($date == '0000-00-00 00:00:00') {
			return false;
		}

		if ($target->format('m-d-Y') < $now->format('m-d-Y')) {
			return true;
		}

		return false;
	}

	public static function get_wp_user_roles( $user_id ) {
	    $user = get_userdata( (int) $user_id );
	    $roles = empty( $user ) ? array() : $user->roles;
	    return (array) $roles;
	}

	public static function user_has_role( $user_id, $role  ) {
	    return in_array( $role, Utillities::get_wp_user_roles( $user_id ) );
	}

	public static function get_editable_roles() {
	    global $wp_roles;

	    $all_roles = $wp_roles->roles;
	    $editable_roles = apply_filters('editable_roles', $all_roles);

	    return $editable_roles;
	}

	public static function can_access_page( $page_slug ) {
		$user_id = get_current_user_id();
		$cap = 'zpm_' . $page_slug . '_page';

		// Hide page if overriden from another extension or setting
		if (apply_filters( 'zpm_hide_page', false, $page_slug )) {
			return false;
		}

		if ( current_user_can( 'zpm_all_zephyr_capabilities' ) || current_user_can( $cap ) || current_user_can( 'administrator', $user_id ) ) {
			return true;
		}

		if ( !Utillities::is_zephyr_role( $user_id ) && Utillities::can_zephyr( $user_id ) ) {
			return true;
		}

		return false;
	}

	public static function canEditProject($project) {
		$userId = get_current_user_id();
		$projectInstance = new Project($project);

		if (current_user_can('zpm_all_zephyr_capabilities') || current_user_can('administrator')) {
			return true;
		}
		if (Utillities::is_zephyr_role($userId)) {
			if (current_user_can('zpm_edit_projects')) {
				return true;
			} elseif (current_user_can('zpm_edit_assigned_projects') && $projectInstance->hasAssignee($userId)) {
				return true;
			}
		}
		return false;
	}

	public static function canDeleteProject($userId = null, $project = null) {
		$userId = !is_null($userId) ? $userId : get_current_user_id();
		$projectInstance = new Project($project);

		if (current_user_can('zpm_all_zephyr_capabilities') || current_user_can('administrator')) {
			return true;
		}
		if (Utillities::is_zephyr_role($userId)) {
			if (current_user_can('zpm_delete_projects')) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
		return false;
	}

	public static function canEditTask($task = null) {
		$userId = get_current_user_id();
		if (current_user_can('zpm_all_zephyr_capabilities') || current_user_can('administrator')) {
			return true;
		}
		if (Utillities::is_zephyr_role($userId)) {
			if (current_user_can('zpm_edit_tasks')) {
				return true;
			}
		}
		return false;
	}

	public static function canDeleteTask($userId = null, $task = null) {
		$userId = !is_null($userId) ? $userId : get_current_user_id();

		if (current_user_can('zpm_all_zephyr_capabilities') || current_user_can('administrator')) {
			return true;
		}
		if (Utillities::is_zephyr_role($userId)) {
			if (current_user_can('zpm_delete_tasks')) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
		return false;
	}

	public static function canAccessBackend() {
		$userId = get_current_user_id();

		if (current_user_can('zpm_all_zephyr_capabilities') || current_user_can('administrator')) {
			return true;
		}

		if (Utillities::is_zephyr_role($userId)) {
			if (!current_user_can('zpm_access_backend')) {
				return false;
			}
		}
		return true;
	}

	public static function is_zephyr_role( $user_id ) {
		if ( Utillities::user_has_role( $user_id, 'zpm_admin') || Utillities::user_has_role( $user_id, 'zpm_manager') || Utillities::user_has_role( $user_id, 'zpm_user') ||  Utillities::user_has_role( $user_id, 'zpm_frontend_user') ) {
			return true;
		}
		return false;
	}

	public static function auto_gradient_css( $color, $important = false ) {
		if ( is_null( $color ) || empty( $color ) ) {
			$color = zpm_get_primary_color();
		}
		$important = $important ? '!important' : '';
		$color_dark = Utillities::adjust_brightness( $color, -40 );

		$css = "background: " . $color . ";
				background: -moz-linear-gradient(-45deg, " . $color . " 0%, " . $color_dark . " 100%) " . $important . ";
				background: -webkit-linear-gradient(-45deg, " . $color . " 0%," . $color_dark . " 100%) " . $important . ";
				background: linear-gradient(135deg, " . $color . " 0%," . $color_dark . " 100%) " . $important . ";
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $color . "', endColorstr='" . $color_dark . "',GradientType=1 ); " . $important . "";
		return $css;
	}

	public static function install_missing_columns() {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		if ( !Utillities::table_column_exists( $table_name, 'archived' ) ) {
			$wpdb->query("ALTER TABLE $table_name ADD archived BOOLEAN DEFAULT 0;");
		}
	}

	public static function attachment_html( $attachment ) {
		$attachment_id = unserialize( $attachment->message );
		$attachment = wp_get_attachment_url( $attachment_id );
		if (wp_attachment_is_image( $attachment_id )) {
			// Image preview
			$html = '<li class="zpm_comment_attachment"><a class="zpm_link" href="' . $attachment . '" download><img class="zpm-image-attachment-preview" src="' . $attachment . '"></a></li>';
		} else {
			// Attachment link
			$html = '<li class="zpm_comment_attachment"><a class="zpm_link" href="' . $attachment . '" download>' . $attachment . '</a></li>';
		}
		return $html;
	}

	public static function getMentions($string) {
		$mentionRegex = '/@\[[^\]]*\]\((.*?)\)/i'; // mention regrex to get all @texts
		if (preg_match_all($mentionRegex, $string, $matches)) {
			foreach ($matches[1] as $key => $match) {
				$userId = str_replace('user:', '', $match);
				$userData = Members::get_member($userId);

				if (!empty($userData)) {
					$matchSearch = $matches[0][$key];
					$matchReplace = '@' . $userData['name'];
					$string = str_replace($matchSearch, $matchReplace, $string);
				}
			}
		}
		return $string;
	}

	public static function sendMentionEmails($string, $subjectType = '', $object = '') {
		$settings = Utillities::general_settings();
		$mentionRegex = '/@\[[^\]]*\]\((.*?)\)/i'; // mention regrex to get all @texts
		if (preg_match_all($mentionRegex, $string, $matches)) {
			foreach ($matches[1] as $key => $match) {
				$userId = str_replace('user:', '', $match);
				$userData = Members::get_member($userId);
				$subject = $settings['email_mentions_subject'];
				$matchSearch = $matches[0][$key];
				$matchReplace = '<span class="zpm-message__mention">@' . $userData['name'] . '</span>';
				$content = str_replace($matchSearch, $matchReplace, $string);
				$message = $settings['email_mentions_content'];
				$message = str_replace('{messageText}', $content, $message);
				$message .= ': ' . $content;

				if (!empty($subjectType)) {
					if ($subjectType == 'task') {
						$url = Tasks::task_url($object->id, true);
						$message .= '<br/><a href="' . $url . '">View Task</a>';
					}

					if ($subjectType == 'project') {
						$base_url = get_frontend_url('action=project&id=' . $object->id);
						$message .= '<br/><a href="' . $url . '">View Project</a>';
					}
				}

				Emails::send_email($userData['email'], $subject, $message);
			}
		}
		return $string;
	}

	public static function hasZephyrRole() {
		$roles = Utillities::get_roles();
		$userId = get_current_user_id();
		foreach ($roles as $key => $role) {
			if (Utillities::user_has_role( $userId, $key )) {
				return true;
			}
		}
		return false;
	}

	public static function getLocalizedData() {
		$localized_strings = Utillities::get_localized_strings();
		$device_ids = Utillities::get_one_signal_device_ids();
		$general_settings = Utillities::general_settings();
		$current_project_id = isset($_GET['project']) ? $_GET['project'] : '-1';
		$statuses = Utillities::get_statuses( 'all' );
		$rest_url = get_rest_url();
		$user = Members::get_member(get_current_user_id());
		$username = isset($user['name']) ? $user['name'] : '';
		$data = array(
			'rest_url' 	 	  => $rest_url . 'zephyr_project_manager/v1/',
			'plugin_url' 	  => ZPM_PLUGIN_URL,
			'tasks_url'  	  => esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')),
			'projects_url'    => esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')),
			'ajaxurl' 	 	  => admin_url( 'admin-ajax.php' ),
			'user_id' 	 	  => get_current_user_id(),
            'user_name'  	  => $username,
            'zpm_nonce'	 	  => wp_create_nonce('zpm_nonce'),
            'strings'	 	  => $localized_strings,
            'is_admin'	 	  => true,
           	'device_ids' 	  => $device_ids,
           	'website'    	  => get_site_url(),
           	'current_project' => $current_project_id,
           	'settings'        => $general_settings,
           	'statuses' 		  => $statuses
		);
		return $data;
	}

	public static function getDateFormats() {
		$formats = array(
			'M d'  	  => date_i18n('M d'),
			'F j, Y'  => date_i18n('F j, Y'),
			'Y. F j.' => date_i18n('Y. F j.'),
			'd M Y'   => date_i18n('d M Y'),
			'D M j'   => date_i18n('D M j'),
			'M/D/Y'	  => date_i18n('M/D/Y'),
			'j, n, Y' => date_i18n('j, n, Y'),
			'm.d.y'   => date_i18n('m.d.y'),
			'j-m-y'   => date_i18n('j-m-y'),
			'd-m-Y'   => date_i18n('d-m-Y'),
			'Y-m-d'   => date_i18n('Y-m-d')
		);
		return $formats;
	}

	public static function getDaysOfWeek() {
		$days = array(
			0 => __( 'Sunday', 'zephyr-project-manager' ),
			1 => __( 'Monday', 'zephyr-project-manager' ),
			2 => __( 'Tuesday', 'zephyr-project-manager' ),
			3 => __( 'Wednesday', 'zephyr-project-manager' ),
			4 => __( 'Thursday', 'zephyr-project-manager' ),
			5 => __( 'Friday', 'zephyr-project-manager' ),
			6 => __( 'Saturday', 'zephyr-project-manager' )
		);
		return $days;
	}

	public static function updateProjectProgress($projectId) {
		$progress = maybe_unserialize( get_option( 'zpm_project_progress', array() ) );
		$date = date('Y-m-d');
		$percent = Projects::percent_complete($projectId);
		$progress[$projectId][$date] = $percent;
		update_option( 'zpm_project_progress', serialize($progress) );
	}

	public static function getProjectProgress($projectId, $format = 'Y-m-d') {
		$results = [];
		$progress = maybe_unserialize( get_option( 'zpm_project_progress', array() ) );
		$projectProgress = isset($progress[$projectId]) ? $progress[$projectId] : [];

		foreach ($projectProgress as $date => $entry) {
			if ($format !== 'Y-m-d') {
				$time = strtotime($date);
				$formattedDate = date($format, $time);
				$results[$formattedDate] = $entry;
			} else {
				$results[$date] = $entry;
			}
		}

		$date = date($format);

		$projectProgress[$date] = Projects::percent_complete($projectId);
		return $results;
	}

	public static function addButtonHtml() {
		ob_start();
		?>
		<button id="zpm_add_new_btn" zpm-ripple="ripple" class="" data-zpm-dropdown-toggle="zpm_add_new_dropdown"><img src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon_plus.png'; ?>"/></button>

		<ul id="zpm_add_new_dropdown" class="zpm_fancy_dropdown">
			<?php ob_start(); ?>

				<?php if (Utillities::can_create_projects()) : ?>
					<li class="zpm_fancy_item zpm_fancy_divider" id="zpm_create_quickproject"><?php _e( 'New Project', 'zephyr-project-manager' ); ?></li>
				<?php endif; ?>

				<?php if (Utillities::can_create_tasks()) : ?>
					<li class="zpm_fancy_item" id="zpm_quickadd_task"><?php _e( 'New Task', 'zephyr-project-manager' ); ?></li>
				<?php endif; ?>

				<li class="zpm_fancy_item" id="zpm_new_quick_category"><?php _e( 'New Category', 'zephyr-project-manager' ); ?></li>
				<?php if (Utillities::canUploadFiles()) : ?>
					<li class="zpm_fancy_item" id="zpm_new_quick_file"><?php _e( 'New File', 'zephyr-project-manager' ); ?></li>
				<?php endif; ?>
				<li class="zpm_fancy_item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_settings')); ?>" title="<?php _e( 'Settings', 'zephyr-project-manager' ); ?>"><?php _e( 'Settings', 'zephyr-project-manager' ); ?></a></li>
				<?php if (!BaseController::is_pro()) : ?>
					<li id="zpm_premium_link" class="zpm_fancy_item zpm_fancy_divider_top"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_purchase_premium')); ?>" title="<?php _e( 'Premium', 'zephyr-project-manager' ); ?>"><?php _e( 'Get Premium', 'zephyr-project-manager' ); ?></a></li>
				<?php endif; ?>
			<?php
			$html = ob_get_clean();
			echo apply_filters('zpm_quickmenu_options', $html);
			echo apply_filters('zpm_after_quickmenu', '');
			?>
		</ul>
		<?php
		return ob_get_clean();
	}

	public static function htmlFromFile($path) {
		ob_start();
		if (file_exists($path)) {
			require($path);
		}
		$html = ob_get_clean();
		return $html;
	}

	public static function getTaskSettings($taskId = null) {
		$settings = maybe_unserialize( get_option( 'zpm_task_settings', array() ) );
		if (is_null($taskId)) {
			return $settings;
		}
		if (isset($settings[$taskId])) {
			return $settings[$taskId];
		} else {
			return [];
		}
	}

	public static function getTaskSetting($taskId, $name) {
		$settings = Utillities::getTaskSettings($taskId);
		if (isset($settings[$name])) {
			return $settings[$name];
		} else {
			return '';
		}
	}

	public static function saveTaskSetting($taskId, $name, $value) {
		$settings = Utillities::getTaskSettings();
		$settings[$taskId][$name] = $value;
		update_option( 'zpm_task_settings', serialize($settings) );
	}

	public static function getPermissions() {
		$permissions = [];
		$caps = Utillities::get_caps();

		foreach ($caps as $cap) {
			$name = str_replace('zpm_', '', $cap);
			$name = str_replace('_', ' ', $name);
			$name = ucwords($name);
			$permissions[] = [
				'id' => $cap,
				'slug' => $cap,
				'name' => $name
			];
		}

		return $permissions;
	}

	public static function canCreateUsers() {

		if (!apply_filters( 'zpm_override_can_create_users', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_create_users' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		return false;
	}

	public static function canCreateTeams() {

		if (!apply_filters( 'zpm_override_can_create_teams', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_create_teams' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		return false;
	}

	public static function canUploadFiles() {

		if (!apply_filters( 'zpm_override_can_upload_files', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_upload_files' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		return false;
	}

	public static function canCreateMilestones() {

		if (!apply_filters( 'zpm_override_can_create_milestones', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_create_milestone' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		return false;
	}

	public static function canViewMilestones() {
		if (!apply_filters( 'zpm_override_can_view_milestones', true )) {
			return false;
		}

		if (current_user_can( 'administrator' ) || current_user_can( 'zpm_view_milestones' ) || current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		return false;
	}

	public static function getTables() {
		$tables = [
			ZPM_PROJECTS_TABLE,
			ZPM_TASKS_TABLE,
			ZPM_MESSAGES_TABLE,
			ZPM_CATEGORY_TABLE,
			ZPM_ACTIVITY_TABLE
		];
		$tables = apply_filters( 'zpm_tables', $tables );
		return $tables;
	}

	public static function truncateTable($table) {
		global $wpdb;
		$delete = $wpdb->query("TRUNCATE TABLE $table");
	}

	public static function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number%100) <= 13))
	        return $number. 'th';
	    else
	        return $number. $ends[$number % 10];
	}

	public static function canViewAllProjects( $userId = false ) {
		$canView = true;

		if ($userId == false) {
			$userId = get_current_user_id();
		}

	    if ( Utillities::is_zephyr_role($userId) ) {
			if ( current_user_can( 'zpm_view_assigned_projects' ) && !current_user_can( 'zpm_view_projects' ) ) {
				$canView = false;
			}
		}

		return $canView;
	}
}
