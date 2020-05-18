<?php
/**
 * Provides AJAX actions for JSON API interface
 * @mixin C_Ajax_Controller
 * @adapts I_Ajax_Controller
 */
class A_NextGen_API_Ajax extends Mixin
{
    var $nextgen_api = NULL;
    var $_nextgen_api_locked = false;
    var $_shutdown_registered = false;
    var $_error_handler_registered = false;
    var $_error_handler_old = null;
    function get_nextgen_api()
    {
        if (is_null($this->nextgen_api)) {
            $this->nextgen_api = C_NextGen_API::get_instance();
        }
        return $this->nextgen_api;
    }
    function _authenticate_user($regenerate_token = false)
    {
        $api = $this->get_nextgen_api();
        $username = $this->object->param('q');
        $password = $this->object->param('z');
        $token = $this->object->param('tok');
        return $api->authenticate_user($username, $password, $token, $regenerate_token);
    }
    function get_nextgen_api_token_action()
    {
        $regen = $this->object->param('regenerate_token') ? true : false;
        $user_obj = $this->_authenticate_user($regen);
        $response = array();
        if ($user_obj != null) {
            $response['result'] = 'ok';
            $response['result_object'] = array('token' => get_user_meta($user_obj->ID, 'nextgen_api_token', true));
        } else {
            $response['result'] = 'error';
            $response['error'] = array('code' => C_NextGen_API::ERR_NOT_AUTHENTICATED, 'message' => __('Authentication Failed.', 'nggallery'));
        }
        return $response;
    }
    function get_nextgen_api_path_list_action()
    {
        $api = $this->get_nextgen_api();
        $app_config = $this->object->param('app_config');
        $user_obj = $this->_authenticate_user();
        $response = array();
        if ($user_obj != null && !is_a($user_obj, 'WP_Error')) {
            wp_set_current_user($user_obj->ID);
            $ftp_method = isset($app_config['ftp_method']) ? $app_config['ftp_method'] : 'ftp';
            $creds = array('connection_type' => $ftp_method == 'sftp' ? 'ssh' : 'ftp', 'hostname' => $app_config['ftp_host'], 'port' => $app_config['ftp_port'], 'username' => $app_config['ftp_user'], 'password' => $app_config['ftp_pass']);
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $wp_filesystem = $api->create_filesystem_access($creds);
            $root_path = null;
            $base_path = null;
            $plugin_path = null;
            if ($wp_filesystem) {
                $root_path = $wp_filesystem->wp_content_dir();
                $base_path = $wp_filesystem->abspath();
                $plugin_path = $wp_filesystem->wp_plugins_dir();
            } else {
                // fallbacks when unable to connect, try to see if we know the path already
                $root_path = get_option('ngg_ftp_root_path');
                if (defined('FTP_BASE')) {
                    $base_path = FTP_BASE;
                }
                if ($root_path == null && defined('FTP_CONTENT_DIR')) {
                    $root_path = FTP_CONTENT_DIR;
                }
                if (defined('FTP_PLUGIN_DIR')) {
                    $plugin_path = FTP_PLUGIN_DIR;
                }
                if ($base_path == null && $root_path != null) {
                    $base_path = dirname($root_path);
                }
                if ($root_path == null && $base_path != null) {
                    $root_path = rtrim($base_path, '/\\') . '/wp-content/';
                }
                if ($plugin_path == null && $base_path != null) {
                    $plugin_path = rtrim($base_path, '/\\') . '/wp-content/plugins/';
                }
            }
            if ($root_path != NULL) {
                $response['result'] = 'ok';
                $response['result_object'] = array('root_path' => $root_path, 'wp_content_path' => $root_path, 'wp_base_path' => $base_path, 'wp_plugin_path' => $plugin_path);
            } else {
                if ($wp_filesystem != null) {
                    $response['result'] = 'error';
                    $response['error'] = array('code' => C_NextGen_API::ERR_FTP_NO_PATH, 'message' => __('Could not determine FTP path.', 'nggallery'));
                } else {
                    $response['result'] = 'error';
                    $response['error'] = array('code' => C_NextGen_API::ERR_FTP_NOT_CONNECTED, 'message' => __('Could not connect to FTP to determine path.', 'nggallery'));
                }
            }
        } else {
            $response['result'] = 'error';
            $response['error'] = array('code' => C_NextGen_API::ERR_NOT_AUTHENTICATED, 'message' => __('Authentication Failed.', 'nggallery'));
        }
        return $response;
    }
    function _get_max_upload_size()
    {
        static $max_size = -1;
        if ($max_size < 0) {
            $post_max_size = $this->_parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }
            $upload_max = $this->_parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }
    function _parse_size($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
    function _get_max_upload_files()
    {
        return intval(ini_get('max_file_uploads'));
    }
    function enqueue_nextgen_api_task_list_action()
    {
        $api = $this->get_nextgen_api();
        $user_obj = $this->_authenticate_user();
        $response = array();
        if ($user_obj != null && !is_a($user_obj, 'WP_Error')) {
            wp_set_current_user($user_obj->ID);
            $app_config = $this->object->param('app_config');
            $task_list = $this->object->param('task_list');
            $extra_data = $this->object->param('extra_data');
            if (is_string($app_config)) {
                $app_config = json_decode($app_config, true);
            }
            if (is_string($task_list)) {
                $task_list = json_decode($task_list, true);
            }
            if (is_string($extra_data)) {
                $extra_data = json_decode($extra_data, true);
            }
            foreach ($_FILES as $key => $file) {
                if (substr($key, 0, strlen('file_data_')) == 'file_data_') {
                    $extra_data[substr($key, strlen('file_data_'))] = $file;
                }
            }
            if ($task_list != null) {
                $task_count = count($task_list);
                $auth_count = 0;
                foreach ($task_list as &$task_item) {
                    $task_id = isset($task_item['id']) ? $task_item['id'] : null;
                    $task_name = isset($task_item['name']) ? $task_item['name'] : null;
                    $task_type = isset($task_item['type']) ? $task_item['type'] : null;
                    $task_query = isset($task_item['query']) ? $task_item['query'] : null;
                    $type_parts = explode('_', $task_name);
                    $type_context = array_pop($type_parts);
                    $type_action = array_pop($type_parts);
                    $task_auth = false;
                    switch ($task_type) {
                        case 'gallery_add':
                            $task_auth = M_Security::is_allowed('nextgen_edit_gallery');
                            break;
                        case 'gallery_remove':
                        case 'gallery_edit':
                            $query_id = $api->get_query_id($task_query['id'], $task_list);
                            $gallery = null;
                            // The old NextGEN XMLRPC API had this logic so replicating it here for safety
                            if ($query_id) {
                                $gallery_mapper = C_Gallery_Mapper::get_instance();
                                $gallery = $gallery_mapper->find($query_id);
                            }
                            if ($gallery != null) {
                                $task_auth = wp_get_current_user()->ID == $gallery->author || M_Security::is_allowed('nextgen_edit_gallery_unowned');
                            } else {
                                $task_auth = M_Security::is_allowed('nextgen_edit_gallery');
                            }
                            break;
                        case 'album_add':
                            $task_auth = M_Security::is_allowed('nextgen_edit_album');
                            break;
                        case 'album_remove':
                            $task_auth = M_Security::is_allowed('nextgen_edit_album');
                            break;
                        case 'album_edit':
                            $task_auth = M_Security::is_allowed('nextgen_edit_album');
                            break;
                        case 'image_list_move':
                            break;
                    }
                    if ($task_auth) {
                        $auth_count++;
                    }
                    $task_item['auth'] = $task_auth ? 'allow' : 'forbid';
                }
                if ($task_count == $auth_count) {
                    $job_id = $api->add_job(array('user' => $user_obj->ID, 'clientid' => $this->object->param('clientid')), $app_config, $task_list);
                    if ($job_id != null) {
                        $post_back = $api->get_job_post_back($job_id);
                        $handler_delay = defined('NGG_API_JOB_HANDLER_DELAY') ? intval(NGG_API_JOB_HANDLER_DELAY) : 0;
                        $handler_delay = $handler_delay > 0 ? $handler_delay : 30;
                        /* in seconds */
                        $handler_maxsize = defined('NGG_API_JOB_HANDLER_MAXSIZE') ? intval(NGG_API_JOB_HANDLER_MAXSIZE) : 0;
                        $handler_maxsize = $handler_maxsize > 0 ? $handler_maxsize : $this->_get_max_upload_size();
                        /* in bytes */
                        $handler_maxfiles = $this->_get_max_upload_files();
                        $response['result'] = 'ok';
                        $response['result_object'] = array('job_id' => $job_id, 'job_post_back' => $post_back, 'job_handler_url' => home_url('?photocrati_ajax=1&action=execute_nextgen_api_task_list'), 'job_handler_delay' => $handler_delay, 'job_handler_maxsize' => $handler_maxsize, 'job_handler_maxfiles' => $handler_maxfiles);
                        if (!defined('NGG_API_SUPPRESS_QUICK_EXECUTE') || NGG_API_SUPPRESS_QUICK_EXECUTE == false) {
                            if (!$api->is_execution_locked()) {
                                $this->_start_locked_execute();
                                try {
                                    $result = $api->handle_job($job_id, $api->get_job_data($job_id), $app_config, $api->get_job_task_list($job_id), $extra_data);
                                    $response['result_object']['job_result'] = $api->get_job_task_list($job_id);
                                    if ($result) {
                                        // everything was finished, remove job
                                        $api->remove_job($job_id);
                                    }
                                } catch (Exception $e) {
                                }
                                $this->_stop_locked_execute();
                            }
                        }
                    } else {
                        $response['result'] = 'error';
                        $response['error'] = array('code' => C_NextGen_API::ERR_JOB_NOT_ADDED, 'message' => __('Job could not be added.', 'nggallery'));
                    }
                } else {
                    $response['result'] = 'error';
                    $response['error'] = array('code' => C_NextGen_API::ERR_NOT_AUTHORIZED, 'message' => __('Authorization Failed.', 'nggallery'));
                }
            } else {
                $response['result'] = 'error';
                $response['error'] = array('code' => C_NextGen_API::ERR_NO_TASK_LIST, 'message' => __('No task list was specified.', 'nggallery'));
            }
        } else {
            $response['result'] = 'error';
            $response['error'] = array('code' => C_NextGen_API::ERR_NOT_AUTHENTICATED, 'message' => __('Authentication Failed.', 'nggallery'));
        }
        return $response;
    }
    function _do_shutdown()
    {
        if ($this->_nextgen_api_locked) {
            $this->get_nextgen_api()->set_execution_locked(false);
        }
    }
    function _error_handler($errno, $errstr, $errfile, $errline)
    {
        return false;
    }
    function _start_locked_execute()
    {
        $api = $this->get_nextgen_api();
        if (!$this->_shutdown_registered) {
            register_shutdown_function(array($this, '_do_shutdown'));
            $this->_shutdown_registered = true;
        }
        if (!$this->_error_handler_registered) {
            //$this->_error_handler_old = set_error_handler(array($this, '_error_handler'));
            $this->_error_handler_registered = true;
        }
        $api->set_execution_locked(true);
        $this->_nextgen_api_locked = true;
    }
    function _stop_locked_execute()
    {
        $api = $this->get_nextgen_api();
        $api->set_execution_locked(false);
        $this->_nextgen_api_locked = false;
        if ($this->_error_handler_registered) {
            //set_error_handler($this->_error_handler_old);
            $this->_error_handler_registered = false;
        }
    }
    function execute_nextgen_api_task_list_action()
    {
        $api = $this->get_nextgen_api();
        $job_list = $api->get_job_list();
        $response = array();
        if ($api->is_execution_locked()) {
            $response['result'] = 'ok';
            $response['info'] = array('code' => C_NextGen_API::INFO_EXECUTION_LOCKED, 'message' => __('Job execution is locked.', 'nggallery'));
        } else {
            if ($job_list != null) {
                $this->_start_locked_execute();
                try {
                    $extra_data = $this->object->param('extra_data');
                    $job_count = count($job_list);
                    $done_count = 0;
                    $client_result = array();
                    if (is_string($extra_data)) {
                        $extra_data = json_decode($extra_data, true);
                    }
                    foreach ($_FILES as $key => $file) {
                        if (substr($key, 0, strlen('file_data_')) == 'file_data_') {
                            $extra_data[substr($key, strlen('file_data_'))] = $file;
                        }
                    }
                    foreach ($job_list as $job) {
                        $job_id = $job['id'];
                        $job_data = $job['data'];
                        $result = $api->handle_job($job_id, $job_data, $job['app_config'], $job['task_list'], $extra_data);
                        if (isset($job_data['clientid']) && $job_data['clientid'] == $this->object->param('clientid')) {
                            $client_result[$job_id] = $api->get_job_task_list($job_id);
                        }
                        if ($result) {
                            $done_count++;
                            // everything was finished, remove job
                            $api->remove_job($job_id);
                        }
                        if ($api->should_stop_execution()) {
                            break;
                        }
                    }
                } catch (Exception $e) {
                }
                $this->_stop_locked_execute();
                if ($done_count == $job_count) {
                    $response['result'] = 'ok';
                    $response['info'] = array('code' => C_NextGen_API::INFO_JOB_LIST_FINISHED, 'message' => __('Job list is finished.', 'nggallery'));
                } else {
                    $response['result'] = 'ok';
                    $response['info'] = array('code' => C_NextGen_API::INFO_JOB_LIST_UNFINISHED, 'message' => __('Job list is unfinished.', 'nggallery'));
                }
                if (!defined('NGG_API_SUPPRESS_QUICK_SUMMARY') || NGG_API_SUPPRESS_QUICK_SUMMARY == false) {
                    $response['result_object'] = $client_result;
                }
            } else {
                $response['result'] = 'ok';
                $response['info'] = array('code' => C_NextGen_API::INFO_NO_JOB_LIST, 'message' => __('Job list is empty.', 'nggallery'));
            }
        }
        return $response;
    }
}
/**
 * Class C_NextGen_API
 * @implements I_NextGen_API
 */
class C_NextGen_API extends C_Component
{
    const CRON_KEY = 'nextgen.api.task_list';
    /* NOTE: these constants' numeric values MUST remain the same, do NOT change the values */
    const ERR_NO_TASK_LIST = 1001;
    const ERR_NOT_AUTHENTICATED = 1002;
    const ERR_NOT_AUTHORIZED = 1003;
    const ERR_JOB_NOT_ADDED = 1004;
    const ERR_FTP_NOT_AUTHENTICATED = 1101;
    const ERR_FTP_NOT_CONNECTED = 1102;
    const ERR_FTP_NO_PATH = 1103;
    const INFO_NO_JOB_LIST = 6001;
    const INFO_JOB_LIST_FINISHED = 6002;
    const INFO_JOB_LIST_UNFINISHED = 6003;
    const INFO_EXECUTION_LOCKED = 6004;
    public static $_instances = array();
    var $_start_time;
    /**
     * @param bool|string $context
     * @return C_NextGen_API
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_NextGen_API($context);
        }
        return self::$_instances[$context];
    }
    function define($context = false)
    {
        parent::define($context);
        $this->implement('I_NextGen_API');
        $this->_start_time = time();
    }
    function should_stop_execution()
    {
        $timeout = defined('NGG_API_JOB_HANDLER_TIMEOUT') ? intval(NGG_API_JOB_HANDLER_TIMEOUT) : intval(ini_get('max_execution_time')) - 3;
        $timeout = $timeout > 0 ? $timeout : 27;
        /* most hosts have a limit of 30 seconds execution time, so 27 should be a safe default */
        return time() - $this->_start_time >= $timeout;
    }
    function is_execution_locked()
    {
        $lock_time = get_option('ngg_api_execution_lock', 0);
        if ($lock_time == 0) {
            return false;
        }
        $lock_max = defined('NGG_API_EXECUTION_LOCK_MAX') ? intval(NGG_API_EXECUTION_LOCK_MAX) : 0;
        $lock_max = $lock_max > 0 ? $lock_max : 60 * 5;
        /* if the lock is 5 minutes old assume something went wrong and the lock couldn't be unset */
        $time_diff = time() - $lock_time;
        if ($time_diff > $lock_max) {
            return false;
        }
        return true;
    }
    function set_execution_locked($locked)
    {
        if ($locked) {
            update_option('ngg_api_execution_lock', time(), false);
        } else {
            update_option('ngg_api_execution_lock', 0, false);
        }
    }
    function get_job_list()
    {
        return get_option('ngg_api_job_list');
    }
    function add_job($job_data, $app_config, $task_list)
    {
        $job_list = $this->get_job_list();
        $job_id = uniqid();
        while (isset($job_list[$job_id])) {
            $job_id = uniqid();
        }
        $job = array('id' => $job_id, 'post_back' => array('token' => md5($job_id)), 'data' => $job_data, 'app_config' => $app_config, 'task_list' => $task_list);
        $job_list[$job_id] = $job;
        update_option('ngg_api_job_list', $job_list, false);
        return $job_id;
    }
    function _update_job($job_id, $job)
    {
        $job_list = $this->get_job_list();
        if (isset($job_list[$job_id])) {
            $job_list[$job_id] = $job;
            update_option('ngg_api_job_list', $job_list, false);
        }
    }
    function remove_job($job_id)
    {
        $job_list = $this->get_job_list();
        if (isset($job_list[$job_id])) {
            unset($job_list[$job_id]);
            update_option('ngg_api_job_list', $job_list, false);
        }
    }
    function get_job($job_id)
    {
        $job_list = $this->get_job_list();
        if (isset($job_list[$job_id])) {
            return $job_list[$job_id];
        }
        return null;
    }
    function get_job_data($job_id)
    {
        $job = $this->get_job($job_id);
        if ($job != null) {
            return $job['data'];
        }
        return null;
    }
    function get_job_task_list($job_id)
    {
        $job = $this->get_job($job_id);
        if ($job != null) {
            return $job['task_list'];
        }
        return null;
    }
    function set_job_task_list($job_id, $task_list)
    {
        $job = $this->get_job($job_id);
        if ($job != null) {
            $job['task_list'] = $task_list;
            $this->_update_job($job_id, $job);
            return true;
        }
        return false;
    }
    function get_job_post_back($job_id)
    {
        $job = $this->get_job($job_id);
        if ($job != null) {
            return $job['post_back'];
        }
        return null;
    }
    function authenticate_user($username, $password, $token, $regenerate_token = false)
    {
        $user_obj = null;
        if ($token != null) {
            $users = get_users(array('meta_key' => 'nextgen_api_token', 'meta_value' => $token));
            if ($users != null && count($users) > 0) {
                $user_obj = $users[0];
            }
        }
        if ($user_obj == null) {
            if ($username != null && $password != null) {
                $user_obj = wp_authenticate($username, $password);
                $token = get_user_meta($user_obj->ID, 'nextgen_api_token', true);
                if ($token == null) {
                    $regenerate_token = true;
                }
            }
        }
        if (is_a($user_obj, 'WP_Error')) {
            $user_obj = null;
        }
        if ($regenerate_token) {
            if ($user_obj != null) {
                $token = '';
                if (function_exists('random_bytes')) {
                    $token = bin2hex(random_bytes(16));
                } else {
                    if (function_exists('openssl_random_pseudo_bytes')) {
                        $token = bin2hex(openssl_random_pseudo_bytes(16));
                    } else {
                        for ($i = 0; $i < 16; $i++) {
                            $token .= bin2hex(mt_rand(0, 15));
                        }
                    }
                }
                update_user_meta($user_obj->ID, 'nextgen_api_token', $token);
            }
        }
        return $user_obj;
    }
    function create_filesystem_access($args, $method = null)
    {
        // taken from wp-admin/includes/file.php but with modifications
        if (!$method && isset($args['connection_type']) && 'ssh' == $args['connection_type'] && extension_loaded('ssh2') && function_exists('stream_get_contents')) {
            $method = 'ssh2';
        }
        if (!$method && extension_loaded('ftp')) {
            $method = 'ftpext';
        }
        if (!$method && (extension_loaded('sockets') || function_exists('fsockopen'))) {
            $method = 'ftpsockets';
        }
        //Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread
        if (!$method) {
            return false;
        }
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        if (!class_exists("WP_Filesystem_{$method}")) {
            /**
             * Filter the path for a specific filesystem method class file.
             *
             * @since 2.6.0
             *
             * @see get_filesystem_method()
             *
             * @param string $path   Path to the specific filesystem method class file.
             * @param string $method The filesystem method to use.
             */
            $abstraction_file = apply_filters('filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . $method . '.php', $method);
            if (!file_exists($abstraction_file)) {
                return false;
            }
            require_once $abstraction_file;
        }
        $method_class = "WP_Filesystem_{$method}";
        $wp_filesystem = new $method_class($args);
        //Define the timeouts for the connections. Only available after the construct is called to allow for per-transport overriding of the default.
        if (!defined('FS_CONNECT_TIMEOUT')) {
            define('FS_CONNECT_TIMEOUT', 30);
        }
        if (!defined('FS_TIMEOUT')) {
            define('FS_TIMEOUT', 30);
        }
        if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
            return false;
        }
        if (!$wp_filesystem->connect()) {
            if ($method == 'ftpext') {
                // attempt connecting with alternative method
                return $this->create_filesystem_access($args, 'ftpsockets');
            }
            return false;
            //There was an error connecting to the server.
        }
        // Set the permission constants if not already set.
        if (!defined('FS_CHMOD_DIR')) {
            define('FS_CHMOD_DIR', fileperms(ABSPATH) & 0777 | 0755);
        }
        if (!defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', fileperms(ABSPATH . 'index.php') & 0777 | 0644);
        }
        return $wp_filesystem;
    }
    // returns an actual scalar ID based on parametric ID (e.g. a parametric ID could represent the query ID from another task)
    function get_query_id($id, &$task_list)
    {
        $task_id = $id;
        if (is_object($task_id) || is_array($task_id)) {
            $id = null;
            // it was specified that the query ID is referencing the query ID from another task
            if (isset($task_id['target']) && $task_id['target'] == 'task') {
                if (isset($task_id['id']) && isset($task_list[$task_id['id']])) {
                    $target_task = $task_list[$task_id['id']];
                    if (isset($target_task['query']['id'])) {
                        $id = $target_task['query']['id'];
                    }
                }
            }
        }
        return $id;
    }
    // returns an actual scalar ID based on parametric ID (e.g. a parametric ID could represent the resulting object ID from another task)
    function get_object_id($id, &$result_list)
    {
        $task_id = $id;
        if (is_object($task_id) || is_array($task_id)) {
            $id = null;
            // it was specified that the query ID is referencing the result from another task
            if (isset($task_id['target']) && $task_id['target'] == 'task') {
                if (isset($task_id['id']) && isset($result_list[$task_id['id']])) {
                    $target_result = $result_list[$task_id['id']];
                    if (isset($target_result['object_id'])) {
                        $id = $target_result['object_id'];
                    }
                }
            }
        }
        return $id;
    }
    function _array_find_by_entry(array $array_target, $entry_key, $entry_value)
    {
        foreach ($array_target as $key => $value) {
            $item = $value;
            if (isset($item[$entry_key]) && $item[$entry_key] == $entry_value) {
                return $key;
            }
        }
        return null;
    }
    function _array_filter_by_entry(array $array_target, array $array_source, $entry_key)
    {
        foreach ($array_source as $key => $value) {
            $item = $value;
            if (isset($item[$entry_key])) {
                $find_key = $this->_array_find_by_entry($array_target, $entry_key, $item[$entry_key]);
                if ($find_key !== null) {
                    unset($array_target[$find_key]);
                }
            }
        }
        return $array_target;
    }
    // Note: handle_job only worries about processing the job, it does NOT remove finished jobs anymore, the responsibility is on the caller to remove the job when handle_job returns true, this is to allow calling get_job_*() methods after handle_job has been called
    function handle_job($job_id, $job_data, $app_config, $task_list, $extra_data = null)
    {
        $job_user = $job_data['user'];
        $task_count = count($task_list);
        $done_count = 0;
        $skip_count = 0;
        $task_list_result = array();
        wp_set_current_user($job_user);
        /* This block does all of the filesystem magic:
         * - determines web paths based on FTP paths
         * - initializes the WP_Filesystem mechanism in case this host doesn't support direct file access
         *   (this might not be 100% reliable right now due to NG core not making use of WP_Filesystem)
         */
        // $ftp_path is assumed to be WP_CONTENT_DIR as accessed through the FTP mount point
        $ftp_path = rtrim($app_config['ftp_path'], '/\\');
        $full_path = rtrim($app_config['full_path'], '/\\');
        $root_path = rtrim(WP_CONTENT_DIR, '/\\');
        $creds = true;
        // WP_Filesystem(true) requests direct filesystem access
        $fs_sep = DIRECTORY_SEPARATOR;
        $wp_fs = null;
        require_once ABSPATH . 'wp-admin/includes/file.php';
        if (get_filesystem_method() !== 'direct') {
            $fs_sep = '/';
            $ftp_method = isset($app_config['ftp_method']) ? $app_config['ftp_method'] : 'ftp';
            $creds = array('connection_type' => $ftp_method == 'sftp' ? 'ssh' : 'ftp', 'hostname' => $app_config['ftp_host'], 'port' => $app_config['ftp_port'], 'username' => $app_config['ftp_user'], 'password' => $app_config['ftp_pass']);
        }
        if (WP_Filesystem($creds)) {
            $wp_fs = $GLOBALS['wp_filesystem'];
            $path_prefix = $full_path;
            if ($wp_fs->method === 'direct') {
                if (trim($ftp_path, " \t\n\r\v\\") == '') {
                    // Note: if ftp_path is empty, we assume the FTP account home dir is on wp-content
                    $path_prefix = $root_path . $full_path;
                } else {
                    $path_prefix = str_replace($ftp_path, $root_path, $full_path);
                }
            }
        }
        foreach ($task_list as &$task_item) {
            $task_id = isset($task_item['id']) ? $task_item['id'] : null;
            $task_name = isset($task_item['name']) ? $task_item['name'] : null;
            $task_type = isset($task_item['type']) ? $task_item['type'] : null;
            $task_auth = isset($task_item['auth']) ? $task_item['auth'] : null;
            $task_query = isset($task_item['query']) ? $task_item['query'] : null;
            $task_object = isset($task_item['object']) ? $task_item['object'] : null;
            $task_status = isset($task_item['status']) ? $task_item['status'] : null;
            $task_result = isset($task_item['result']) ? $task_item['result'] : null;
            // make sure we don't repeat execution of already finished tasks
            if ($task_status == 'done') {
                $done_count++;
                // for previously finished tasks, store the result as it may be needed by future tasks
                if ($task_id != null && $task_result != null) {
                    $task_list_result[$task_id] = $task_result;
                }
                continue;
            }
            // make sure only valid and authorized tasks are executed
            if ($task_status == 'error' || $task_auth != 'allow') {
                $skip_count++;
                continue;
            }
            // the task query ID can be a simple (integer) ID or more complex ID that gets converted to a simple ID, for instance to point to an object that is the result of a previously finished task
            if (isset($task_query['id'])) {
                $task_query['id'] = $this->get_object_id($task_query['id'], $task_list_result);
            }
            $task_error = null;
            switch ($task_type) {
                case 'gallery_add':
                    $mapper = C_Gallery_Mapper::get_instance();
                    $gallery = null;
                    $gal_errors = '';
                    if (isset($task_query['id'])) {
                        $gallery = $mapper->find($task_query['id'], true);
                    }
                    if ($gallery == null) {
                        $title = isset($task_object['title']) ? $task_object['title'] : '';
                        $gallery = $mapper->create(array('title' => $title));
                        if (!$gallery || !$gallery->save()) {
                            if ($gallery != null) {
                                $gal_errors = $gallery->get_errors();
                                if ($gal_errors != null) {
                                    $gal_errors = ' [' . json_encode($gal_errors) . ']';
                                }
                            }
                            $gallery = null;
                        }
                    }
                    if ($gallery != null) {
                        $task_status = 'done';
                        $task_result['object_id'] = $gallery->id();
                    } else {
                        $task_status = 'error';
                        $task_error = array('level' => 'fatal', 'message' => sprintf(__('Gallery creation failed for "%1$s"%2$s.', 'nggallery'), $title, $gal_errors));
                    }
                    break;
                case 'gallery_remove':
                case 'gallery_edit':
                    if (isset($task_query['id'])) {
                        $mapper = C_Gallery_Mapper::get_instance();
                        $gallery = $mapper->find($task_query['id'], true);
                        $error = null;
                        if ($gallery != null) {
                            if ($task_type == 'gallery_remove') {
                                /**
                                 * @var $mapper Mixin_Gallery_Mapper
                                 */
                                if (!$mapper->destroy($gallery, true)) {
                                    $error = __('Failed to remove gallery (%1$s).', 'nggallery');
                                }
                            } else {
                                if ($task_type == 'gallery_edit') {
                                    if (isset($task_object['name'])) {
                                        $gallery->name = $task_object['name'];
                                    }
                                    if (isset($task_object['title'])) {
                                        $gallery->title = $task_object['title'];
                                    }
                                    if (isset($task_object['description'])) {
                                        $gallery->galdesc = $task_object['description'];
                                    }
                                    if (isset($task_object['preview_image'])) {
                                        $gallery->previewpic = $task_object['preview_image'];
                                    }
                                    if (isset($task_object['property_list'])) {
                                        $properties = $task_object['property_list'];
                                        foreach ($properties as $key => $value) {
                                            $gallery->{$key} = $value;
                                        }
                                    }
                                    // this is used to determine whether the task is complete
                                    $image_list_unfinished = false;
                                    if (isset($task_object['image_list']) && $wp_fs != null) {
                                        $storage_path = isset($task_object['storage_path']) ? $task_object['storage_path'] : null;
                                        $storage_path = trim($storage_path, '/\\');
                                        $storage = C_Gallery_Storage::get_instance();
                                        $image_mapper = C_Image_Mapper::get_instance();
                                        $creds = true;
                                        $images_folder = $path_prefix . $fs_sep . $storage_path . $fs_sep;
                                        $images_folder = str_replace(array('\\', '/'), $fs_sep, $images_folder);
                                        $images = $task_object['image_list'];
                                        $result_images = isset($task_result['image_list']) ? $task_result['image_list'] : array();
                                        $images_todo = array_values($this->_array_filter_by_entry($images, $result_images, 'localId'));
                                        $image_count = count($images);
                                        $result_image_count = count($result_images);
                                        foreach ($images_todo as $image_index => $image) {
                                            $image_id = isset($image['id']) ? $image['id'] : null;
                                            $image_filename = isset($image['filename']) ? $image['filename'] : null;
                                            $image_path = isset($image['path']) ? $image['path'] : null;
                                            $image_data_key = isset($image['data_key']) ? $image['data_key'] : null;
                                            $image_action = isset($image['action']) ? $image['action'] : null;
                                            $image_status = isset($image['status']) ? $image['status'] : 'skip';
                                            if ($image_filename == null) {
                                                $image_filename = basename($image_path);
                                            }
                                            $ngg_image = $image_mapper->find($image_id, TRUE);
                                            // ensure that we don't transpose the image from one gallery to another in case a remoteId is passed in for the image but the gallery associated to the collection cannot be found
                                            if ($ngg_image && $ngg_image->galleryid != $gallery->id()) {
                                                $ngg_image = null;
                                                $image_id = null;
                                            }
                                            $image_error = null;
                                            if ($image_action == "delete") {
                                                // image was deleted
                                                if ($ngg_image != null) {
                                                    $settings = C_NextGen_Settings::get_instance();
                                                    $delete_fine = true;
                                                    if ($settings->deleteImg) {
                                                        if (!$storage->delete_image($ngg_image)) {
                                                            $image_error = __('Could not delete image file(s) from disk (%1$s).', 'nggallery');
                                                        }
                                                    } else {
                                                        if (!$image_mapper->destroy($ngg_image)) {
                                                            $image_error = __('Could not remove image from gallery (%1$s).', 'nggallery');
                                                        }
                                                    }
                                                    if ($image_error == null) {
                                                        do_action('ngg_delete_picture', $ngg_image->{$ngg_image->id_field}, $ngg_image);
                                                        $image_status = 'done';
                                                    }
                                                } else {
                                                    $image_error = __('Could not remove image because image was not found (%1$s).', 'nggallery');
                                                }
                                            } else {
                                                /* image was added or edited and needs updating */
                                                $image_data = null;
                                                if ($image_data_key != null) {
                                                    if (!isset($extra_data['__queuedImages'][$image_data_key])) {
                                                        if (isset($extra_data[$image_data_key])) {
                                                            $image_data_arr = $extra_data[$image_data_key];
                                                            $image_data = file_get_contents($image_data_arr['tmp_name']);
                                                        }
                                                        if ($image_data == null) {
                                                            $image_error = __('Could not obtain data for image (%1$s).', 'nggallery');
                                                        }
                                                    } else {
                                                        $image_status = 'queued';
                                                    }
                                                } else {
                                                    $image_path = $images_folder . $image_path;
                                                    if ($image_path != null && $wp_fs->exists($image_path)) {
                                                        $image_data = $wp_fs->get_contents($image_path);
                                                    } else {
                                                        if (is_multisite()) {
                                                            $image_error = __('Could not find image file for image (%1$s). Using FTP Upload Method in Multisite is not recommended.', 'nggallery');
                                                        } else {
                                                            $image_error = __('Could not find image file for image (%1$s).', 'nggallery');
                                                        }
                                                    }
                                                    // delete temporary image
                                                    $wp_fs->delete($image_path);
                                                }
                                                if ($image_data != null) {
                                                    try {
                                                        $ngg_image = $storage->upload_base64_image($gallery, $image_data, $image_filename, $image_id, true);
                                                        $image_mapper->reimport_metadata($ngg_image);
                                                        if ($ngg_image != null) {
                                                            $image_status = 'done';
                                                            $image_id = is_int($ngg_image) ? $ngg_image : $ngg_image->{$ngg_image->id_field};
                                                        }
                                                    } catch (E_NoSpaceAvailableException $e) {
                                                        $image_error = __('No space available for image (%1$s).', 'nggallery');
                                                    } catch (E_UploadException $e) {
                                                        $image_error = $e->getMessage . __(' (%1$s).', 'nggallery');
                                                    } catch (E_No_Image_Library_Exception $e) {
                                                        $image_error = __('No image library present, image uploads will fail (%1$s).', 'nggallery');
                                                        // no point in continuing if the image library is not present but we don't break here to ensure that all images are processed (otherwise they'd be processed in further fruitless handle_job calls)
                                                    } catch (E_InsufficientWriteAccessException $e) {
                                                        $image_error = __('Inadequate system permissions to write image (%1$s).', 'nggallery');
                                                    } catch (E_InvalidEntityException $e) {
                                                        $image_error = __('Requested image with id (%2$s) doesn\'t exist (%1$s).', 'nggallery');
                                                    } catch (E_EntityNotFoundException $e) {
                                                        // gallery doesn't exist - already checked above so this should never happen
                                                    }
                                                }
                                            }
                                            if ($image_error != null) {
                                                $image_status = 'error';
                                                $image['error'] = array('level' => 'fatal', 'message' => sprintf($image_error, $image_filename, $image_id));
                                            }
                                            if ($image_id) {
                                                $image['id'] = $image_id;
                                            }
                                            if ($image_status) {
                                                $image['status'] = $image_status;
                                            }
                                            if ($image_status != 'queued') {
                                                // append processed image to result image_list array
                                                $result_images[] = $image;
                                            }
                                            if ($this->should_stop_execution()) {
                                                break;
                                            }
                                        }
                                        $task_result['image_list'] = $result_images;
                                        $image_list_unfinished = count($result_images) < $image_count;
                                        // if images have finished processing, remove the folder used to store the temporary images (the folder should be empty due to delete() calls above)
                                        if (!$image_list_unfinished && $storage_path != null && $storage_path != $fs_sep && $path_prefix != null && $path_prefix != $fs_sep) {
                                            $wp_fs->rmdir($images_folder);
                                        }
                                    } else {
                                        if ($wp_fs == null) {
                                            $error = __('Could not access file system for gallery (%1$s).', 'nggallery');
                                        }
                                    }
                                    if (!$gallery->save()) {
                                        if ($error == null) {
                                            $gal_errors = '[' . json_encode($gallery->get_errors()) . ']';
                                            $error = __('Failed to save modified gallery (%1$s). ' . $gal_errors, 'nggallery');
                                        }
                                    }
                                }
                            }
                        } else {
                            $error = __('Could not find gallery (%1$s).', 'nggallery');
                        }
                        // XXX workaround for $gallery->save() returning false even if successful
                        if (isset($task_result['image_list']) && $gallery != null) {
                            $task_result['object_id'] = $gallery->id();
                        }
                        if ($error == null) {
                            $task_status = 'done';
                            $task_result['object_id'] = $gallery->id();
                        } else {
                            $task_status = 'error';
                            $task_error = array('level' => 'fatal', 'message' => sprintf($error, (string) $task_query['id']));
                        }
                        if ($image_list_unfinished) {
                            // we override the status of the task when the image list has not finished processing
                            $task_status = 'unfinished';
                        }
                    } else {
                        $task_status = 'error';
                        $task_error = array('level' => 'fatal', 'message' => __('No gallery was specified to edit.', 'nggallery'));
                    }
                    break;
                case 'album_add':
                    $mapper = C_Album_Mapper::get_instance();
                    $name = isset($task_object['name']) ? $task_object['name'] : '';
                    $desc = isset($task_object['description']) ? $task_object['description'] : '';
                    $previewpic = isset($task_object['preview_image']) ? $task_object['preview_image'] : 0;
                    $sortorder = isset($task_object['sort_order']) ? $task_object['sort_order'] : '';
                    $page_id = isset($task_object['page_id']) ? $task_object['page_id'] : 0;
                    $album = null;
                    if (isset($task_query['id'])) {
                        $album = $mapper->find($task_query['id'], true);
                    }
                    if ($album == null) {
                        $album = $mapper->create(array('name' => $name, 'previewpic' => $previewpic, 'albumdesc' => $desc, 'sortorder' => $sortorder, 'pageid' => $page_id));
                        if (!$album || !$album->save()) {
                            $album = null;
                        }
                    }
                    if ($album != null) {
                        $task_status = 'done';
                        $task_result['object_id'] = $album->id();
                    } else {
                        $task_status = 'error';
                        $task_error = array('level' => 'fatal', 'message' => __('Album creation failed.', 'nggallery'));
                    }
                    break;
                case 'album_remove':
                case 'album_edit':
                    if (isset($task_query['id'])) {
                        $mapper = C_Album_Mapper::get_instance();
                        $album = $mapper->find($task_query['id'], true);
                        $error = null;
                        if ($album) {
                            if ($task_type == 'album_remove') {
                                if (!$album->destroy()) {
                                    $error = __('Failed to remove album (%1$s).', 'nggallery');
                                }
                            } else {
                                if ($task_type == 'album_edit') {
                                    if (isset($task_object['name'])) {
                                        $album->name = $task_object['name'];
                                    }
                                    if (isset($task_object['description'])) {
                                        $album->albumdesc = $task_object['description'];
                                    }
                                    if (isset($task_object['preview_image'])) {
                                        $album->previewpic = $task_object['preview_image'];
                                    }
                                    if (isset($task_object['property_list'])) {
                                        $properties = $task_object['property_list'];
                                        foreach ($properties as $key => $value) {
                                            $album->{$key} = $value;
                                        }
                                    }
                                    if (isset($task_object['item_list'])) {
                                        $item_list = $task_object['item_list'];
                                        $sortorder = $album->sortorder;
                                        $count = count($sortorder);
                                        $album_items = array();
                                        for ($index = 0; $index < $count; $index++) {
                                            $album_items[$sortorder[$index]] = $index;
                                        }
                                        foreach ($item_list as $item_info) {
                                            $item_id = isset($item_info['id']) ? $item_info['id'] : null;
                                            $item_type = isset($item_info['type']) ? $item_info['type'] : null;
                                            $item_index = isset($item_info['index']) ? $item_info['index'] : null;
                                            // translate ID in case this gallery has been created as part of this job
                                            $item_id = $this->get_object_id($item_id, $task_list_result);
                                            if ($item_id != null) {
                                                if ($item_type == 'album') {
                                                    $item_id = 'a' . $item_id;
                                                }
                                                $album_items[$item_id] = $count + $item_index;
                                            }
                                        }
                                        asort($album_items);
                                        $album->sortorder = array_keys($album_items);
                                    }
                                    if (!$mapper->save($album)) {
                                        $error = __('Failed to save modified album (%1$s).', 'nggallery');
                                    }
                                }
                            }
                        } else {
                            $error = __('Could not find album (%1$s).', 'nggallery');
                        }
                        if ($error == null) {
                            $task_status = 'done';
                            $task_result['object_id'] = $album->id();
                        } else {
                            $task_status = 'error';
                            $task_error = array('level' => 'fatal', 'message' => sprintf($error, (string) $task_query['id']));
                        }
                    } else {
                        $task_status = 'error';
                        $task_error = array('level' => 'fatal', 'message' => __('No album was specified to edit.', 'nggallery'));
                    }
                    break;
                case 'gallery_list_get':
                    $mapper = C_Gallery_Mapper::get_instance();
                    $gallery_list = $mapper->find_all();
                    $result_list = array();
                    foreach ($gallery_list as $gallery) {
                        $gallery_result = array('id' => $gallery->id(), 'name' => $gallery->name, 'title' => $gallery->title, 'description' => $gallery->galdesc, 'preview_image' => $gallery->previewpic);
                        $result_list[] = $gallery_result;
                    }
                    $task_status = 'done';
                    $task_result['gallery_list'] = $result_list;
                    break;
                case 'image_list_move':
                    break;
            }
            $task_item['result'] = $task_result;
            $task_item['status'] = $task_status;
            $task_item['error'] = $task_error;
            // for previously finished tasks, store the result as it may be needed by future tasks
            if ($task_id != null && $task_result != null) {
                $task_list_result[$task_id] = $task_result;
            }
            // if the task has finished, either successfully or unsuccessfully, increase count for done tasks
            if ($task_status != 'unfinished') {
                $done_count++;
            }
            if ($this->should_stop_execution()) {
                break;
            }
        }
        $this->set_job_task_list($job_id, $task_list);
        if ($task_count > $done_count + $skip_count) {
            // unfinished tasks, return false
            return false;
        } else {
            $upload_method = isset($app_config['upload_method']) ? $app_config['upload_method'] : 'ftp';
            if ($upload_method == 'ftp') {
                // everything was finished, write status file
                $status_file = '_ngg_job_status_' . strval($job_id) . '.txt';
                $status_content = json_encode($task_list);
                if ($wp_fs != null) {
                    $status_path = $path_prefix . $fs_sep . $status_file;
                    $status_path = str_replace(array('\\', '/'), $fs_sep, $status_path);
                    $wp_fs->put_contents($status_path, $status_content);
                } else {
                    // if WP_Filesystem failed try one last desperate attempt at direct file writing
                    $status_path = str_replace($ftp_path, $root_path, $full_path) . DIRECTORY_SEPARATOR . $status_file;
                    $status_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $status_path);
                    file_put_contents($status_path, $status_content);
                }
            }
            return true;
        }
    }
}
/**
 * Class C_NextGen_API_XMLRPC
 * @implements I_NextGen_API_XMLRPC
 */
class C_NextGen_API_XMLRPC extends C_Component
{
    public static $_instances = array();
    function define($context = false)
    {
        parent::define($context);
        $this->implement('I_NextGen_API_XMLRPC');
    }
    /**
     * @param bool|string $context
     * @return C_NextGen_API_XMLRPC
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_NextGen_API_XMLRPC($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Gets the version of NextGEN Gallery installed
     * @return array
     */
    function get_version()
    {
        return array('version' => NGG_PLUGIN_VERSION);
    }
    /**
     * Login a user
     * @param $username
     * @param $password
     * @return bool|WP_Error|WP_User
     */
    function _login($username, $password, $blog_id = 1)
    {
        $retval = FALSE;
        if (!is_a($user_obj = wp_authenticate($username, $password), 'WP_Error')) {
            wp_set_current_user($user_obj->ID);
            $retval = $user_obj;
            if (is_multisite()) {
                switch_to_blog($blog_id);
            }
        }
        return $retval;
    }
    function _can_manage_gallery($gallery_id_or_obj, $check_upload_capability = FALSE)
    {
        $retval = FALSE;
        // Get the gallery object, if we don't have it already
        $gallery = NULL;
        if (is_int($gallery_id_or_obj)) {
            $gallery_mapper = C_Gallery_Mapper::get_instance();
            $gallery = $gallery_mapper->find($gallery_id_or_obj);
        } else {
            $gallery = $gallery_id_or_obj;
        }
        if ($gallery) {
            $security = $this->get_registry()->get_utility('I_Security_Manager');
            $actor = $security->get_current_actor();
            if ($actor->get_entity_id() == $gallery->author) {
                $retval = TRUE;
            } elseif ($actor->is_allowed('nextgen_edit_gallery_unowned')) {
                $retval = TRUE;
            }
            // Optionally, check if the user can upload to this gallery
            if ($retval && $check_upload_capability) {
                $retval = $actor->is_allowed('nextgen_upload_image');
            }
        }
        return $retval;
    }
    function _add_gallery_properties($gallery)
    {
        if (is_object($gallery)) {
            $image_mapper = C_Image_Mapper::get_instance();
            $storage = C_Gallery_Storage::get_instance();
            // Vladimir's Lightroom plugins requires the 'id' to be a string
            // Ask if he can accept integers as well. Currently, integers break
            // his plugin
            $gallery->gid = (string) $gallery->gid;
            // Set other gallery properties
            $tmp = $image_mapper->select('DISTINCT COUNT(*) as counter')->where(array("galleryid = %d", $gallery->gid))->run_query(FALSE, FALSE, TRUE);
            $image_counter = array_pop($tmp);
            $gallery->counter = $image_counter->counter;
            $gallery->abspath = $storage->get_gallery_abspath($gallery);
        } else {
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Returns a single image object
     * @param array $args (blog_id, username, password, pid)
     * @param bool $return_model (optional)
     * @return object|IXR_Error
     */
    function get_image($args, $return_model = FALSE)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $image_id = intval($args[3]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Try to find the image
            $image_mapper = C_Image_Mapper::get_instance();
            if ($image = $image_mapper->find($image_id, TRUE)) {
                // Try to find the gallery that the image belongs to
                $gallery_mapper = C_Gallery_Mapper::get_instance();
                if ($gallery = $gallery_mapper->find($image->galleryid)) {
                    // Does the user have sufficient capabilities?
                    if ($this->_can_manage_gallery($gallery)) {
                        $storage = C_Gallery_Storage::get_instance();
                        $image->imageURL = $storage->get_image_url($image, 'full', TRUE);
                        $image->thumbURL = $storage->get_thumb_url($image, TRUE);
                        $image->imagePath = $storage->get_image_abspath($image);
                        $image->thumbPath = $storage->get_thumb_abspath($image);
                        $retval = $return_model ? $image : $image->get_entity();
                    } else {
                        $retval = new IXR_Error(403, "You don't have permission to manage gallery #{$image->galleryid}");
                    }
                } else {
                    // No gallery found
                    $retval = new IXR_Error(404, "Gallery not found (with id #{$image->gallerid})");
                }
            } else {
                // No image found
                $retval = new IXR_Error(404, "Image not found (with id #{$image_id})");
            }
        }
        return $retval;
    }
    /**
     * Returns a collection of images
     * @param array $args (blog_id, username, password, gallery_id
     * @return array|IXR_Error
     */
    function get_images($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $gallery_id = intval($args[3]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Try to find the gallery
            $mapper = C_Gallery_Mapper::get_instance();
            if ($gallery = $mapper->find($gallery_id, TRUE)) {
                // Does the user have sufficient capabilities?
                if ($this->_can_manage_gallery($gallery)) {
                    $retval = $gallery->get_images();
                } else {
                    $retval = new IXR_Error(403, "You don't have permission to manage gallery #{$image->galleryid}");
                }
            } else {
                $retval = new IXR_Error(404, "Gallery not found (with id #{$image->gallerid}");
            }
        }
        return $retval;
    }
    /**
     * Uploads an image to a particular gallery
     * @param $args (blog_id, username, password, data)
     *
     * Data is an assoc array:
     *			  o string name
     *			  o string type (optional)
     *			  o base64 bits
     *			  o bool overwrite (optional)
     *			  o int gallery
     *			  o int image_id  (optional)
     * @return object|IXR_Error
     */
    function upload_image($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $data = $args[3];
        $gallery_id = isset($data['gallery_id']) ? $data['gallery_id'] : $data['gallery'];
        if (!isset($data['override'])) {
            $data['override'] = FALSE;
        }
        if (!isset($data['overwrite'])) {
            $data['overwrite'] = FALSE;
        }
        if (!isset($data['image_id'])) {
            $data['image_id'] = FALSE;
        }
        $data['override'] = $data['overwrite'];
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Try to find the gallery
            $mapper = C_Gallery_Mapper::get_instance();
            if ($gallery = $mapper->find($gallery_id, TRUE)) {
                // Does the user have sufficient capabilities?
                if ($this->_can_manage_gallery($gallery, TRUE)) {
                    // Upload the image
                    $storage = C_Gallery_Storage::get_instance();
                    try {
                        $image = $storage->upload_base64_image($gallery, $data['bits'], $data['name'], $data['image_id'], $data['override']);
                        if ($image) {
                            $image = is_int($image) ? C_Image_Mapper::get_instance()->find($image, TRUE) : $image;
                            $storage = C_Gallery_Storage::get_instance();
                            $image->imageURL = $storage->get_image_url($image);
                            $image->thumbURL = $storage->get_thumb_url($image);
                            $image->imagePath = $storage->get_image_abspath($image);
                            $image->thumbPath = $storage->get_thumb_abspath($image);
                            $retval = $image->get_entity();
                        } else {
                            $retval = new IXR_Error(500, "Could not upload image");
                        }
                    } catch (Exception $exception) {
                        $retval = new IXR_Error(500, 'Could not upload image: ' . $exception->getMessage());
                    }
                } else {
                    $retval = new IXR_Error(403, "You don't have permission to upload to gallery #{$gallery_id}");
                }
            } else {
                // No gallery found
                $retval = new IXR_Error(404, "Gallery not found (with id #{$gallery_id}");
            }
        }
        return $retval;
    }
    /**
     * Edits an image object
     * @param $args (blog_id, username, password, image_id, alttext, description, exclude, other_properties
     * @return IXR_Error|object
     */
    function edit_image($args)
    {
        $alttext = strval($args[4]);
        $description = strval($args[5]);
        $exclude = intval($args[6]);
        $properties = isset($args[7]) ? (array) $args[7] : array();
        $retval = $this->get_image($args, TRUE);
        if (!$retval instanceof IXR_Error) {
            $retval->alttext = $alttext;
            $retval->description = $description;
            $retval->exclude = $exclude;
            // Other properties can be specified using an associative array
            foreach ($properties as $key => $value) {
                $retval->{$key} = $value;
            }
            // Unset any dynamic properties not part of the schema
            foreach (array('imageURL', 'thumbURL', 'imagePath', 'thumbPath') as $key) {
                unset($retval->{$key});
            }
            $retval = $retval->save();
        }
        return $retval;
    }
    /**
     * Deletes an existing image from a gallery
     * @param array $args (blog_id, username, password, image_id)
     * @return bool
     */
    function delete_image($args)
    {
        $retval = $this->get_image($args, TRUE);
        if (!$retval instanceof IXR_Error) {
            $retval = $retval->destroy();
        }
        return $retval;
    }
    /**
     * Creates a new gallery
     * @param array $args (blog_id, username, password, title)
     * @return int|IXR_Error
     */
    function create_gallery($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $title = strval($args[3]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            $security = $this->get_registry()->get_utility('I_Security_Manager');
            if ($security->is_allowed('nextgen_edit_gallery')) {
                $mapper = C_Gallery_Mapper::get_instance();
                if (($gallery = $mapper->create(array('title' => $title))) && $gallery->save()) {
                    $retval = $gallery->id();
                } else {
                    $retval = new IXR_Error(500, "Unable to create gallery");
                }
            } else {
                $retval = new IXR_Error(403, "Sorry, but you must be able to manage galleries. Check your roles/capabilities.");
            }
        }
        return $retval;
    }
    /**
     * Edits an existing gallery
     * @param array $args (blog_id, username, password, gallery_id, name, title, description, preview_pic_id)
     * @return int|bool|IXR_Error
     */
    function edit_gallery($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $gallery_id = intval($args[3]);
        $name = strval($args[4]);
        $title = strval($args[5]);
        $galdesc = strval($args[6]);
        $image_id = intval($args[7]);
        $properties = isset($args[8]) ? (array) $args[8] : array();
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            $mapper = C_Gallery_Mapper::get_instance();
            if ($gallery = $mapper->find($gallery_id, TRUE)) {
                if ($this->_can_manage_gallery($gallery)) {
                    $gallery->name = $name;
                    $gallery->title = $title;
                    $gallery->galdesc = $galdesc;
                    $gallery->previewpic = $image_id;
                    foreach ($properties as $key => $value) {
                        $gallery->{$key} = $value;
                    }
                    // Unset dynamic properties not part of the schema
                    unset($gallery->counter);
                    unset($gallery->abspath);
                    $retval = $gallery->save();
                } else {
                    $retval = new IXR_Error(403, "You don't have permission to modify this gallery");
                }
            } else {
                $retval = new IXR_Error(404, "Gallery #{$gallery_id} doesn't exist");
            }
        }
        return $retval;
    }
    /**
     * Returns all galleries
     * @param array $args (blog_id, username, password)
     * @return array|IXR_Error
     */
    function get_galleries($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Do we have permission?
            $security = $this->get_registry()->get_utility('I_Security_Manager');
            if ($security->is_allowed('nextgen_edit_gallery')) {
                $mapper = C_Gallery_Mapper::get_instance();
                $retval = array();
                foreach ($mapper->find_all() as $gallery) {
                    $this->_add_gallery_properties($gallery);
                    $retval[$gallery->{$gallery->id_field}] = (array) $gallery;
                }
            } else {
                $retval = new IXR_Error(401, __('Sorry, you must be able to manage galleries'));
            }
        }
        return $retval;
    }
    /**
     * Gets a single gallery instance
     * @param array $args (blog_id, username, password, gallery_id)
     * @param bool $return_model
     * @return object|bool|IXR_Error
     */
    function get_gallery($args, $return_model = FALSE)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $gallery_id = intval($args[3]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            $mapper = C_Gallery_Mapper::get_instance();
            if ($gallery = $mapper->find($gallery_id, TRUE)) {
                if ($this->_can_manage_gallery($gallery)) {
                    $this->_add_gallery_properties($gallery);
                    $retval = $return_model ? $gallery : $gallery->get_entity();
                } else {
                    $retval = new IXR_Error(403, "Sorry, but you don't have permission to manage gallery #{$gallery->gid}");
                }
            } else {
                $retval = FALSE;
            }
        }
        return $retval;
    }
    /**
     * Deletes a gallery
     * @param array $args (blog_id, username, password, gallery_id)
     * @return bool
     */
    function delete_gallery($args)
    {
        $retval = $this->get_gallery($args, TRUE);
        if (!$retval instanceof IXR_Error and is_object($retval)) {
            $retval = $retval->destroy();
        }
        return $retval;
    }
    /**
     * Creates a new album
     * @param array $args (blog_id, username, password, title, previewpic, description, galleries
     * @return int|IXR_Error
     */
    function create_album($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $title = strval($args[3]);
        $previewpic = isset($args[4]) ? intval($args[4]) : 0;
        $desc = isset($args[5]) ? strval($args[5]) : '';
        $sortorder = isset($args[6]) ? $args[6] : '';
        $page_id = isset($args[7]) ? intval($args[7]) : 0;
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Is request allowed?
            $security = $this->get_registry()->get_utility('I_Security_Manager');
            if ($security->is_allowed('nextgen_edit_album')) {
                $mapper = C_Album_Mapper::get_instance();
                $album = $mapper->create(array('name' => $title, 'previewpic' => $previewpic, 'albumdesc' => $desc, 'sortorder' => $sortorder, 'pageid' => $page_id));
                if ($album->save()) {
                    $retval = $album->id();
                } else {
                    $retval = new IXR_Error(500, "Unable to create album");
                }
            }
        }
        return $retval;
    }
    /**
     * Returns all albums
     * @param $args (blog_id, username, password)
     * @return IXR_Error
     */
    function get_albums($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Are we allowed?
            $security = $this->get_registry()->get_utility('I_Security_Manager');
            if ($security->is_allowed('nextgen_edit_album')) {
                // Fetch all albums
                $mapper = C_Album_Mapper::get_instance();
                $retval = array();
                foreach ($mapper->find_all() as $album) {
                    // Vladimir's Lightroom plugins requires the 'id' to be a string
                    // Ask if he can accept integers as well. Currently, integers break
                    // his plugin
                    $album->id = (string) $album->id;
                    $album->galleries = $album->sortorder;
                    $retval[$album->{$album->id_field}] = (array) $album;
                }
            } else {
                $retval = new IXR_Error(403, "Sorry, you must be able to manage albums");
            }
        }
        return $retval;
    }
    /**
     * Gets a single album
     * @param array $args (blog_id, username, password, album_id)
     * @param bool $return_model (optional)
     * @return object|bool|IXR_Error
     */
    function get_album($args, $return_model = FALSE)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $album_id = intval($args[3]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            // Are we allowed?
            $security = $this->get_registry()->get_utility('I_Security_Manager');
            if ($security->is_allowed('nextgen_edit_album')) {
                $mapper = C_Album_Mapper::get_instance();
                if ($album = $mapper->find($album_id, TRUE)) {
                    // Vladimir's Lightroom plugins requires the 'id' to be a string
                    // Ask if he can accept integers as well. Currently, integers break
                    // his plugin
                    $album->id = (string) $album->id;
                    $album->galleries = $album->sortorder;
                    $retval = $return_model ? $album : $album->get_entity();
                } else {
                    $retval = FALSE;
                }
            } else {
                $retval = new IXR_Error(403, "Sorry, you must be able to manage albums");
            }
        }
        return $retval;
    }
    /**
     * Deletes an existing album
     * @param array $args (blog_id, username, password, album_id)
     * @return bool
     */
    function delete_album($args)
    {
        $retval = $this->get_album($args, TRUE);
        if (!$retval instanceof IXR_Error) {
            $retval = $retval->destroy();
        }
        return $retval;
    }
    /**
     * Edit an existing album
     * @param array $args (blog_id, username, password, album_id, name, preview pic id, description, galleries)
     * @return object|IXR_Error
     */
    function edit_album($args)
    {
        $retval = $this->get_album($args, TRUE);
        if (!$retval instanceof IXR_Error) {
            $retval->name = strval($args[4]);
            $retval->previewpic = intval($args[5]);
            $retval->albumdesc = strval($args[6]);
            $retval->sortorder = $args[7];
            $properties = isset($args[8]) ? $args[8] : array();
            foreach ($properties as $key => $value) {
                $retval->{$key} = $value;
            }
            unset($retval->galleries);
            $retval = $retval->save();
        }
        return $retval;
    }
    /**
     * Sets the post thumbnail for a post to a NextGEN Gallery image
     * @param $args (blog_id, username, password, post_id, image_id)
     *
     * @return IXR_Error|int attachment id
     */
    function set_post_thumbnail($args)
    {
        $retval = new IXR_Error(403, 'Invalid username or password');
        $blog_id = intval($args[0]);
        $username = strval($args[1]);
        $password = strval($args[2]);
        $post_ID = intval($args[3]);
        $image_id = intval($args[4]);
        // Authenticate the user
        if ($this->_login($username, $password, $blog_id)) {
            if (current_user_can('edit_post', $post_ID)) {
                $retval = C_Gallery_Storage::get_instance()->set_post_thumbnail($post_ID, $image_id);
            } else {
                $retval = new IXR_Error(403, "Sorry but you need permission to do this");
            }
        }
        return $retval;
    }
}