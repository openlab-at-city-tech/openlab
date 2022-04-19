<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Api;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Core\Members;
use Inc\Core\Categories;
use Inc\Core\Utillities;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager\CustomFields;

class RestApi {
	function register() {
		
		add_action( 'rest_api_init', function () {

			// /zephyr_project_manager/v1/tasks
			register_rest_route( 'zephyr_project_manager/v1', '/tasks', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'tasks'),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/tasks/subtasks/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/subtasks', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'task_subtasks'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Create new message
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/message', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'new_task_message'),
				'args' => array(
					'task_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'user_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'subject' => array(
						'default' => 'task',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'type' => array(
						'default' => 'message',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'message' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Creat task subtask
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/subtasks/create', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'create_subtask'),
				'args' => array(
					'task' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'name' => array(
						'default' => 'Untitled',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Get task discussion
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/discussion', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'task_discussion'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/tasks/discussion/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/delete', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'delete_task'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/tasks/discussion/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/copy', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'copy_task'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/tasks/discussion/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/convert', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'convert_task'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/tasks/complete/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/complete', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'complete_task'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'complete' => array(
						'default' => '0',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Create a new task
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/create', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'create_task'),
				'args' => array(
					'name' => array(
						'default' => 'Untitled',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'project' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'assignee' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'start' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'end' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'custom_fields' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Projects
			// Get all projects
			register_rest_route( 'zephyr_project_manager/v1', '/projects', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'projects'),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Dashboard projects
			register_rest_route( 'zephyr_project_manager/v1', '/projects/dashboard', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'projects_dashboard'),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Create a new project
			register_rest_route( 'zephyr_project_manager/v1', '/projects/create', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'create_project'),
				'args' => array(
					'name' => array(
						'default' => 'Untitled Project',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'type' => array(
						'default' => 'list',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'user_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/projects/update_status', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'update_project_status'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'status' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'color' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Updates a project
			register_rest_route( 'zephyr_project_manager/v1', '/projects/update', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'update_project'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'name' => array(
						'default' => 'Untitled Project',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'start' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'end' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'categories' => array(
						'default' => '[]',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/projects/settings/user_id=1
			register_rest_route( 'zephyr_project_manager/v1', '/settings', array(
				'methods' => 'GET',
				'callback' => array($this, 'getSettings'),
				'args' => array(
					'user_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/projects/teams/
			register_rest_route( 'zephyr_project_manager/v1', '/teams', array(
				'methods' => 'GET',
				'callback' => array($this, 'getTeams'),
				'args' => array(
					
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/projects/discussion/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/projects/discussion', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'project_discussion'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Create new message
			register_rest_route( 'zephyr_project_manager/v1', '/projects/message', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'new_project_message'),
				'args' => array(
					'task_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'user_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'type' => array(
						'default' => 'message',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'message' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/projects/tasks/id=1
			register_rest_route( 'zephyr_project_manager/v1', '/projects/tasks', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'project_tasks'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/projects/delete', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'delete_project'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/projects/copy', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'copy_project'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/projects/add_to_dashboard', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'add_project_to_dashboard'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// /zephyr_project_manager/v1/users
			register_rest_route( 'zephyr_project_manager/v1', '/users', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'get_users'),
				'permission_callback' => function () {
					return true;
				}
			) );

			// website.com/wp-json/zephyr_project_manager/v1/login/?username=Dylan&password=bob
			register_rest_route( 'zephyr_project_manager/v1', '/authenticate', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'authenticate'),
				'args' => array(
					'username' => array(
						'default' => 'username',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'password' => array(
						'default' => 'password',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'device_id' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'device_name' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'os' => array(
						'default' => 'android',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'one_signal_user_id' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Get categories
			register_rest_route( 'zephyr_project_manager/v1', '/categories', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'categories'),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Tasks
			// Update a task
			register_rest_route( 'zephyr_project_manager/v1', '/tasks/update', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'update_task'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'name' => array(
						'default' => 'Untitled',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'start' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'end' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'project' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'assignee' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'custom_fields' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// General
			// Get general statistics and data
			register_rest_route( 'zephyr_project_manager/v1', '/statistics', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'general_statistics'),
				'permission_callback' => function () {
					return true;
				}
			) );


			// Categories
			register_rest_route( 'zephyr_project_manager/v1', '/categories/create', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'create_category'),
				'args' => array(
					'name' => array(
						'default' => 'Untitled',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'color' => array(
						'default' => '#eeeeee',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/categories/update', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'update_category'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'name' => array(
						'default' => 'Untitled',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'color' => array(
						'default' => '#eeeeee',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/categories/delete', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'delete_category'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// Custom Fields
			// https://zephyr-one.com/wp-json/zephyr_project_manager/v1/custom_fields
			register_rest_route( 'zephyr_project_manager/v1', '/custom_fields', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'get_custom_fields'),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/custom_fields/create', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'create_custom_field'),
				'args' => array(
					'name' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'type' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'default_value' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'field_values' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'required' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/custom_fields/update', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'update_custom_field'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'name' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'type' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'default_value' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'field_values' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'required' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/custom_fields/delete', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'delete_custom_field'),
				'args' => array(
					'id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			// General Endpoints
			// Create new message
			register_rest_route( 'zephyr_project_manager/v1', '/general/save_settings', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'save_settings'),
				'args' => array(
					'user_id' => array(
						'default' => '-1',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'name' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'email' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'description' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'avatar' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'notify_all' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'notify_tasks' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					),
					'notify_weekly' => array(
						'default' => '',
						'validate_callback' => function($param, $request, $key) {
							return is_string( $param );
						}
					)
				),
				'permission_callback' => function () {
					return true;
				}
			) );

			register_rest_route( 'zephyr_project_manager/v1', '/general/status', array(
				'methods' => 'GET',
				'callback' => array('Inc\Api\RestApi', 'check_status'),
				'permission_callback' => function () {
					return true;
				}
			) );

		} );
	}

	public static function check_status( $data ) {

		$response = array(
			'installed' 	   => true,
			'is_pro' 		   => BaseController::is_pro(),
			'code'			   => 'basic_installed',
			'is_valid_version' => true,
			'site_url'		   => get_site_url()
		);

		return $response;
	}

	public static function authenticate($data) {
		$username = $data['username'];
		$password = $data['password'];
		$one_signal_user_id = $data['one_signal_user_id'];

		$user = get_user_by( 'login', $username );

		if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID) ) {

			if ($data['device_id'] !== '') {
				$devices = maybe_unserialize( get_option( 'zpm_devices', array() ) );
				if (!isset( $devices[$data['device_id']] )) {

					$devices[$data['device_id']] = array(
						'id' => $data['device_id'],
						'name' => $data['device_name'],
						'linked_to' => $user->ID,
						'os'	=> $data['os'],
					);

					if ($one_signal_user_id !== "" && !empty($one_signal_user_id)) {
						$devices[$data['device_id']]['one_signal_user_id'] = $one_signal_user_id;
					}
				}
				update_option( 'zpm_devices', serialize( $devices ) );
			}

			$project_manager_user = BaseController::get_project_manager_user( $user->ID );
			$user_settings = Utillities::get_user_settings( $user->ID );

			$user_results = [
				'user_id' 		 => $user->data->ID,
				'user_name' 	 => $user->data->display_name,
				'user_email' 	 => $user->data->user_email,
				'login_name' 	 => $user->data->user_login,
				'login_password' => $user->data->user_pass,
				'user_data'		 => $project_manager_user,
				'status'		 => true,
				'can_zephyr'	 => $user_settings['can_zephyr'],
				'is_admin'		 => user_can( $user->data->ID, 'administrator' ),
				'is_pro'		 => BaseController::is_pro(),
				'site_url'		 => get_site_url()
			];
			return $user_results;
		} else {
			$response = [
				'status' => false
			];

			if (!get_user_by( 'login', $username )) {
				$response['error'] = "invalid_username";
			} else {
				$response['error'] = "invalid_password";
			}

			return $response;
		}
	}

	public static function projects_dashboard( $data ) {
		$dashboard_projects = array();
		$dashboard_project_ids = Projects::get_dashboard_projects(false);

		foreach ($dashboard_project_ids as $project_id) {
			$project = Projects::get_project( $project_id );
			if (!is_object($project)) {
				continue;
			}
			$project->id = $project_id;
			$project->total_tasks = Tasks::get_project_task_count( $project_id );
			$project->completed_tasks = Tasks::get_project_completed_tasks( $project_id );
			$project->pending_tasks = $project->total_tasks - $project->completed_tasks;
			$dashboard_projects[] = $project;
		}
		return $dashboard_projects;
	}

	public static function task_subtasks( $data ) {
		$tasks = Tasks::get_subtasks( $data['id'] );
		return $tasks;
	}

	public static function create_subtask( $data ) {
		$parent_id = $data['task'];
		$name = $data['name'];
		$data = [
			'name' => $name,
			'parent_id' => $parent_id
		];
		$new_task = Tasks::create( $data );
		$subtask = Tasks::get_task( $new_task );
		return $subtask;
	}

	public static function create_category( $data ) {
		$category_id = Categories::create( $data );
		$category = Categories::get_category( $category_id );
		return $category;
	}

	public static function update_category( $data ) {
		$args = array(
			'name' => $data['name'],
			'description' => $data['description']
		);
		$category_id = Categories::update( $data['id'], $args );
		$category = Categories::get_category( $data['id'] );
		return $category;
	}

	public static function delete_category( $data ) {
		Categories::delete( $data['id'] );
		return $data;
	}

	public static function task_discussion( $data ) {
		$comments = Tasks::get_comments( $data['id'] );
		$array = [];

		foreach ($comments as $comment) {
			$attachments = Tasks::get_comment_attachments( $comment->id );
			$user = BaseController::get_project_manager_user( $comment->user_id );
			$comment->message = maybe_unserialize( $comment->message );
			$comment->message = html_entity_decode( $comment->message );
			$comment->username = $user['name'];
			$attachments_array = [];

			foreach ($attachments as $attachment) {
				$this_attachment = wp_get_attachment_url( unserialize( $attachment->message ) );
				array_push( $attachments_array, $this_attachment );
			}

			$comment->attachments = $attachments_array;
			
			array_push( $array, $comment );
		}
		return $array;
	}

	public static function projects( $data ) {
		$projects = Projects::get_projects();
		foreach ($projects as $project) {
			$project->categories = unserialize( $project->categories  );
			$project->total_tasks = Tasks::get_project_task_count( $project->id );
			$project->completed_tasks = Tasks::get_project_completed_tasks( $project->id );
			$project->pending_tasks = $project->total_tasks - $project->completed_tasks;
		}
		return $projects;
	}

	public static function project_discussion( $data ) {
		$comments = Projects::get_comments( $data['id'] );
		$array = [];

		foreach ($comments as $comment) {
			$attachments = Projects::get_comment_attachments( $comment->id );
			$user = BaseController::get_project_manager_user( $comment->user_id );
			$comment->message = maybe_unserialize( $comment->message );
			$comment->message = html_entity_decode( $comment->message );
			$comment->username = $user['name'];
			$attachments_array = [];

			foreach ($attachments as $attachment) {
				$this_attachment = wp_get_attachment_url( unserialize( $attachment->message ) );
				array_push( $attachments_array, $this_attachment );
			}

			$comment->attachments = $attachments_array;
			
			array_push( $array, $comment );
		}
		return $array;
	}

	public static function project_tasks( $data ) {
		$projects = Tasks::get_project_tasks( $data['id'] );
		return $projects;
	}

	public static function complete_task( $data ) {
		Tasks::complete( $data['id'], $data['complete'] );
		return $data;
	}

	public static function get_users( $data ) {
		$users = Utillities::get_users();
		return $users;
	}

	public static function categories( $data ) {
		$results = [];
		$categories = (array) Categories::get_categories();
        foreach((array) $categories as $category) {
            $results[] = $category;
        }
		return $results;
	}

	// Tasks
	public static function tasks( $data ) {
		$tasks = Tasks::get_tasks();

		if (Zephyr::isPro()) {
			foreach ($tasks as $task) {
				$array = [];
				$task->custom_fields = unserialize( $task->custom_fields );

				foreach ((array) $task->custom_fields as $custom_field) {
					$field = isset($custom_field['id'] ) ? CustomFields::get_custom_field( $custom_field['id'] ) : false;
                    if (!is_object($field)) {
                        continue;
                    }
					$custom_field['label'] = $field->name;
					$custom_field['field_values'] = isset( $custom_field['field_values'] ) ? maybe_unserialize( $custom_field['field_values'] ) : array();
					array_push( $array, $custom_field );
				}
				$task->custom_fields = $array;
				$task->description = Utillities::getMentions($task->description);
			}
		}

		$results = [];

		foreach ($tasks as $task) {
			$results[] = $task;
		}

		return $results;
	}

	public function create_task( $data ) {
		$name = $data->get_param('name');
		$description = $data->get_param('description');
		$project = $data->get_param('project');
		$assignee = $data->get_param('assignee');
		$start_date = $data->get_param('start');
		$end_date = $data->get_param('end');
		$start = $start_date !== '' ? date('Y-m-d H:i:s', strtotime($start_date)) : $start_date;
		$end = $end_date !== '' ? date('Y-m-d H:i:s', strtotime($end_date)) : $end_date;
		$custom_fields = $data->get_param('custom_fields') !== "" ? json_decode( $data->get_param('custom_fields'), true ) : array();
		
		foreach ($custom_fields as $key => $value) {
			$custom_fields[$key]["field_values"] = serialize( $custom_fields[$key]["field_values"] );
		}

		$new_task_data = [
			'name' => $name,
			'description' => $description,
			'project' => $project,
			'assignee' => $assignee,
			'date_start' => $start,
			'date_due' => $end
		];
		

		$new_task = Tasks::create( $new_task_data );
		$task = Tasks::get_task( $new_task );
// 		$task->custom_fields = unserialize( $task->custom_fields );

// 		foreach ($task->custom_fields as $key => $value) {
// 			$task->custom_fields[$key]["field_values"] = unserialize( $task->custom_fields[$key]["field_values"] );
// 		}

		return $task;
	}

	public function copy_task( $data ) {
		$id = $data['id'];
		$new_task = Tasks::copy( $id );
		return Tasks::get_task( $new_task );
	}

	public function convert_task( $data ) {
		$id = $data['id'];
		$new_project = Tasks::convert( $id );
		return $new_project;
	}

	public function delete_task( $data ) {
		$task_id = $data['id'];
		Tasks::delete( $task_id );
		$response = [
			'id' => $task_id
		];
		return $response;
	}

	public static function update_task( $data ) {
		$id = $data['id'];
		$task_data = array();
		$task_data['name'] = $data['name'];
		$task_data['description'] = $data['description'];
		$task_data['date_start'] !== '' ? date('Y-m-d H:i:s', strtotime($data['start'])) : $data['start'];
		$task_data['date_due'] !== '' ? date('Y-m-d H:i:s', strtotime($data['end'])) : $data['end'];
		$task_data['assignee'] = $data['assignee'];
		$task_data['project'] = $data['project'];

		$custom_fields = $data['custom_fields'] !== "" ? json_decode( $data['custom_fields'], true ) : array();
		
		foreach ($custom_fields as $key => $value) {
			$custom_fields[$key]["field_values"] = serialize( $custom_fields[$key]["field_values"] );
		}

		$task_data['custom_fields'] = serialize( $custom_fields );

		Tasks::update( $id, $task_data );
		return $task_data;
	}

	public static function new_task_message( $data ) {
		$task_id = $data['task_id'];

		$message_id = Tasks::send_comment( $task_id, $data );
		$message = Tasks::get_comment( $message_id );

		$html = Tasks::new_comment( $message );
		$user = BaseController::get_project_manager_user( $message->user_id );
		$message->message = maybe_unserialize( $message->message );
		$message->message = html_entity_decode( $message->message );
		$message->username = $user['name'];

		// Comment attachments
		$attachments = Tasks::get_comment_attachments( $message->id );
		$attachments_array = [];

		foreach ($attachments as $attachment) {
			$this_attachment = wp_get_attachment_url( unserialize( $attachment->message ) );
			array_push( $attachments_array, $this_attachment );
		}

		$message->attachments = $attachments_array;

		$response = array(
			'html' => $html,
			'subject_object' => Tasks::get_task( $subject_id ),
			'comment' => $message
		);

		return $response;
	}

	public static function new_project_message( $data ) {
		$task_id = $data['task_id'];

		$message_id = Projects::send_comment( $task_id, $data );
		$message = Projects::get_comment( $message_id );

		$user = BaseController::get_project_manager_user( $message->user_id );
		$message->message = maybe_unserialize( $message->message );
		$message->message = html_entity_decode( $message->message );
		$message->username = $user['name'];

		// Comment attachments
		$attachments = Projects::get_comment_attachments( $message->id );
		$attachments_array = [];

		foreach ($attachments as $attachment) {
			$this_attachment = wp_get_attachment_url( unserialize( $attachment->message ) );
			array_push( $attachments_array, $this_attachment );
		}

		$message->attachments = $attachments_array;

		$html = Projects::new_comment($last_comment);
		
		$response = array(
			'html' => $html,
			'subject_object' => Projects::get_project( $subject_id ),
			'comment' => $message
		);

		return $response;
	}

	public static function general_statistics( $data ) {
		$overdue_tasks = Tasks::get_overdue_tasks();
		$total_projects = Projects::project_count();
		$completed_projects = Projects::completed_project_count();

		$total_tasks = Tasks::get_task_count();
		$completed_tasks = Tasks::get_completed_task_count();

		$percent_completed_tasks = ($total_tasks !== 0) ? floor($completed_tasks / $total_tasks * 100): '100';
		$percent_completed_projects = ($total_projects !== 0) ? floor($completed_projects / $total_projects * 100): '100';

		$dashboard_projects = array();
		$dashboard_project_ids = Projects::get_dashboard_projects(false);

		foreach ($dashboard_project_ids as $project_id) {
			$project = Projects::get_project( $project_id );
			if (!is_object($project)) {
				continue;
			}
			$project->id = $project_id;
			$project->total_tasks = Tasks::get_project_task_count( $project_id );
			$project->completed_tasks = Tasks::get_project_completed_tasks( $project_id );
			$project->pending_tasks = $project->total_tasks - $project->completed_tasks;
			$dashboard_projects[] = $project;
		}

		$statistics = [
			'total_projects' => $total_projects,
			'completed_projects' => $completed_projects,
			'percent_completed_projects' => $percent_completed_projects,
			'total_tasks' => $total_tasks,
			'completed_tasks' => $completed_tasks,
			'percent_completed_tasks' => $percent_completed_tasks,
			'overdue_tasks' => $overdue_tasks,
			'dashboard_projects' => $dashboard_projects
		];
		return $statistics;
	}

	public function create_project( $data ) {
		$name = $data['name'];
		$description = $data['description'];
		$type = $data['type'];
		$data = [
			'name' => $name,
			'description' => $description,
			'type' => $type,
			'user_id' => $data['user_id']
		];
		$project_id = Projects::new_project( $data );
		$project = Projects::get_project( $project_id );
		$project->categories = unserialize( $project->categories );
		$project->total_tasks = Tasks::get_project_task_count( $project->id );
		$project->completed_tasks = Tasks::get_project_completed_tasks( $project->id );
		$project->pending_tasks = $project->total_tasks - $project->completed_tasks;
		return $project;
	}

	public function update_project( $data ) {
		$categories = $data['categories'] !== "" ? json_decode( $data['categories'], true ) : array();
		$date_start !== '' ? date('Y-m-d H:i:s', strtotime( $data['start']) ) : '';
		$date_due !== '' ? date('Y-m-d H:i:s', strtotime( $data['end']) ) : '';


		$project_data = [
			'name' => $data['name'],
			'description' => $data['description'],
			'categories' => serialize( $categories ),
			'date_start' => $date_start,
			'date_due' => $date_due
		];

		Projects::update( $data['id'], $project_data );

		$project = Projects::get_project( $data['id'] );
		$project->categories = unserialize( $project->categories );
		$project->total_tasks = Tasks::get_project_task_count( $project->id );
		$project->completed_tasks = Tasks::get_project_completed_tasks( $project->id );
		$project->pending_tasks = $project->total_tasks - $project->completed_tasks;
		$project->dates = $data['start'];
		return $project;
	}

	public function save_settings( $data ) {

		$user_id = isset($data['user_id']) ? $data['user_id'] : '';
		$current_user = get_user_by( 'ID', $user_id );
		$user_name = $current_user->data->display_name;
		$user_email = $current_user->data->user_email;
		$name = (isset($data['name']) && $data['name'] !== '') ? $data['name'] : $user_name;
		$description = isset($data['description']) ? $data['description'] : '';
		$avatar = (isset($data['avatar']) && $data['avatar'] !== '') ? $data['avatar'] : get_avatar_url($user_id);
		$email = (isset($data['email']) && $data['email'] !== '') ? $data['email'] : $user_email;
		$notify_all = isset($data['notify_all']) && $data['notify_all'] == "true" ? 1 : '0';
		$notify_tasks = isset($data['notify_tasks']) && $data['notify_tasks'] == "true" ? 1 : '0';
		$notify_updates = isset($data['notify_weekly'] ) && $data['notify_weekly'] == "true" ? 1 : '0';

		$settings = array(
			'user_id' 		  => $user_id,
			'profile_picture' => $avatar,
			'name' 			  => $name,
			'description' 	  => $description,
			'email' 		  => $email,
			'notify_activity' => $notify_all,
			'notify_tasks' 	  => $notify_tasks,
			'notify_updates'  => $notify_updates
		);

		update_option( 'zpm_user_' . $user_id . '_settings', $settings );

		return $settings;
	}

	// Custom Fields
	public function get_custom_fields( $data ) {
		$custom_fields = CustomFields::get_custom_fields();

		foreach ($custom_fields as $key => $field) {
			$custom_fields[$key]->default_value = unserialize( $field->default_value );
			$custom_fields[$key]->field_values = unserialize( $field->field_values );
		}

		return $custom_fields;
	}

	// Create a custom field
	public function create_custom_field( $data ) {

		$data = array(
	        'name'          => $data['name'],
	        'type'          => $data['type'],
	        'field_values'  => $data['field_values'],
	        'default_value' => $data['default_value'],
	        'required'      => $data['required']
	      );

		$custom_field_id = CustomFields::create( $data );
		$custom_field = CustomFields::get_custom_field( $custom_field_id );
		$custom_field->default_value = unserialize( $custom_field->default_value );
		$custom_field->field_values = unserialize( $custom_field->default_value );
		return $custom_field;
	}

	public function update_custom_field( $data ) {

		$args = array(
	        'name'          => $data['name'],
	        'type'          => $data['type'],
	        'field_values'  => $data['field_values'],
	        'default_value' => $data['default_value'],
	        'required'      => $data['required']
	    );

	    $args['field_values'] = json_decode( $args['field_values']);

		CustomFields::update( $data['id'], $args );
		$custom_field = CustomFields::get_custom_field( $data['id'] );
		//$custom_field->default_value = unserialize( $custom_field->default_value );
		$custom_field->field_values = unserialize( $custom_field->field_values );
		return $custom_field;
	}

	// Deletes a custom field
	public function delete_custom_field( $data ) {
		CustomFields::delete_field( $data['id'] );
		return $data;
	}

	public function copy_project( $data ) {
		$original = Projects::get_project( $data['id'] );
		$args = [
			'project_id' => $data['id'],
			'project_name' => $original->name . " (Copy)",
			'copy_options' => array(
				'tasks',
				'description',
			),
		];

		$project = Projects::copy_project( $args );
		$project->categories = unserialize( $project->categories );
		$project->total_tasks = Tasks::get_project_task_count( $project->id );
		$project->completed_tasks = Tasks::get_project_completed_tasks( $project->id );
		$project->pending_tasks = $project->total_tasks - $project->completed_tasks;
		return $project;
	}

	public function delete_project( $data ) {
		Projects::delete_project( $data['id'] );
	}

	public function add_project_to_dashboard( $data ) {
		Projects::add_to_dashboard( $data['id'] );
	}

	public function update_project_status( $data ) {
		Projects::update_project_status( $data['id'], $data['status'], $data['color'] );
	}

	public function getSettings( $data ) {
		$userId = $data['user_id'];
		$settings = Utillities::getLocalizedData();
		$settings['can_create_tasks'] = Utillities::can_create_tasks($userId);
		$settings['can_create_projects'] = Utillities::can_create_projects($userId);
		return $settings;
	}

	public function getTeams( $data ) {
		$teams = Members::get_teams();
		return $teams;
	}
}