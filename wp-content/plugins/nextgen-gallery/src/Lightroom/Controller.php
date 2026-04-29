<?php

namespace Imagely\NGG\Lightroom;

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\Util\{Filesystem, Security};

/**
 * Controller for Lightroom integration.
 */
class Controller {

	/**
	 * NextGEN API instance.
	 *
	 * @var object|null
	 */
	protected $nextgen_api = null;

	/**
	 * Whether NextGEN API is locked.
	 *
	 * @var bool
	 */
	protected $nextgen_api_locked = false;

	/**
	 * Whether shutdown is registered.
	 *
	 * @var bool
	 */
	protected $shutdown_registered = false;

	// Nonce verification not possible: the Lightroom client never requests or sends back a nonce. All actions are
	// authenticated.
	//
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	// phpcs:disable WordPress.Security.NonceVerification.Recommended

	public static function run() {
		define( 'DOING_AJAX', true );
		ob_start();

		$self   = new Controller();
		$action = $self->param( 'action' ) . '_action';

		$response = [];

		// The following could be dynamic but is written this way to prevent warnings that the methods aren't in use.
		if ( 'enqueue_nextgen_api_task_list_action' === $action ) {
			$response = $self->enqueue_nextgen_api_task_list_action();
		} elseif ( 'execute_nextgen_api_task_list_action' === $action ) {
			$response = $self->execute_nextgen_api_task_list_action();
		} elseif ( 'get_nextgen_api_path_list_action' === $action ) {
			$response = $self->get_nextgen_api_path_list_action();
		} elseif ( 'get_nextgen_api_token_action' === $action ) {
			$response = $self->get_nextgen_api_token_action();
		}

		// Flush the buffer.
		$buffer_limit = 0;
		$zlib         = ini_get( 'zlib.output_compression' );
		if ( ! is_numeric( $zlib ) && $zlib == 'On' ) {
			$buffer_limit = 1;
		} elseif ( is_numeric( $zlib ) && $zlib > 0 ) {
			$buffer_limit = 1;
		}

		while ( ob_get_level() != $buffer_limit ) {
			ob_end_clean();
		}

		wp_send_json( $response );
	}

	/**
	 * Gets a request parameter value.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function param( $key ) {
		if ( isset( $_REQUEST[ $key ] ) ) {
			return $this->recursive_stripslashes( sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) );
		}
	}

	/**
	 * Gets a JSON parameter value, undoes WordPress magic-quoting, and decodes it.
	 *
	 * Unlike param(), this method calls wp_unslash() exactly once (to undo WordPress's
	 * wp_magic_quotes() which runs addslashes() on $_REQUEST at startup), but does NOT
	 * call recursive_stripslashes() a second time. That second call in param() corrupts
	 * JSON-encoded Windows paths: C:\\Users\\ becomes C:\Users\, leaving invalid JSON
	 * escape sequences (\U, \A, etc.) that cause json_decode() to return null.
	 *
	 * @param string $key
	 * @return mixed Decoded value, or null if the parameter is absent or not valid JSON.
	 */
	public function param_json( $key ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : null;
		if ( is_string( $raw ) ) {
			// WordPress's wp_magic_quotes() applies addslashes() to $_REQUEST at startup,
			// escaping all " to \" and \ to \\. We must call wp_unslash() once to undo
			// that and restore valid JSON. We must NOT call recursive_stripslashes() a
			// second time (as param() does), because that strips the path backslashes again
			// (C:\\Users\\ -> C:\Users\), leaving invalid escape sequences (\U, \A, etc.)
			// that cause json_decode() to return null on Windows paths.
			return json_decode( wp_unslash( $raw ), true );
		}
		return $raw;
	}

	/**
	 * Recursively calls stripslashes() on strings, arrays, and objects
	 *
	 * Copied here from RoutingApp to maintain compatibility with Lightroom
	 *
	 * @TODO Move this to a better place or find a better solution
	 * @param string|array|\stdClass $value Value to be processed
	 * @return string|array|\stdClass Resulting value
	 */
	public function recursive_stripslashes( $value ) {
		if ( is_string( $value ) ) {
			$value = stripslashes( $value );
		} elseif ( is_array( $value ) ) {
			foreach ( $value as &$tmp ) {
				$tmp = $this->recursive_stripslashes( $tmp );
			}
		} elseif ( is_object( $value ) ) {
			foreach ( get_object_vars( $value ) as $key => $data ) {
				$value->{$key} = $this->recursive_stripslashes( $data );
			}
		}

		return $value;
	}

	/**
	 * Enqueues a NextGEN API task list action.
	 *
	 * @return array
	 */
	public function enqueue_nextgen_api_task_list_action() {
		$api      = $this->get_nextgen_api();
		$user_obj = $this->authenticate_user();
		$response = [];

		if ( $user_obj != null && ! is_a( $user_obj, 'WP_Error' ) ) {
			wp_set_current_user( $user_obj->ID );
			// Use param_json() instead of param() for JSON fields: param() applies wp_unslash()/
			// stripslashes() which converts \\ to \ in JSON strings, leaving invalid escape
			// sequences (e.g. \U, \J) that cause json_decode() to return null on Windows paths.
			$app_config = $this->param_json( 'app_config' );
			$task_list  = $this->param_json( 'task_list' );
			$extra_data = $this->param_json( 'extra_data' ) ?? [];

			foreach ( $_FILES as $key => $file ) {
				if ( substr( $key, 0, strlen( 'file_data_' ) ) == 'file_data_' ) {
					$extra_data[ substr( $key, strlen( 'file_data_' ) ) ] = $file;
				}
			}

			if ( $task_list != null ) {
				$task_count = count( $task_list );
				$auth_count = 0;

				foreach ( $task_list as &$task_item ) {
					$task_name  = isset( $task_item['name'] ) ? $task_item['name'] : null;
					$task_type  = isset( $task_item['type'] ) ? $task_item['type'] : null;
					$task_query = isset( $task_item['query'] ) ? $task_item['query'] : null;

					$task_auth = false;

					switch ( $task_type ) {
						case 'gallery_add':
							$task_auth = Security::is_allowed( 'nextgen_edit_gallery' );
							break;
						case 'gallery_remove':
						case 'gallery_edit':
							$query_id = $api->get_query_id( $task_query['id'], $task_list );
							$gallery  = null;

							// The old NextGEN XMLRPC API had this logic so replicating it here for safety.
							if ( $query_id ) {
								$gallery_mapper = GalleryMapper::get_instance();
								$gallery        = $gallery_mapper->find( $query_id );
							}

							if ( $gallery != null ) {
								$task_auth = ( wp_get_current_user()->ID == $gallery->author || Security::is_allowed( 'nextgen_edit_gallery_unowned' ) );
							} else {
								$task_auth = Security::is_allowed( 'nextgen_edit_gallery' );
							}

							break;
						case 'album_remove':
						case 'album_edit':
						case 'album_add':
							$task_auth = Security::is_allowed( 'nextgen_edit_album' );
							break;
						case 'image_list_move':
							break;
					}

					if ( $task_auth ) {
						++$auth_count;
					}

					$task_item['auth'] = $task_auth ? 'allow' : 'forbid';
				}

				if ( $task_count == $auth_count ) {
					$job_id = $api->add_job(
						[
							'user'     => $user_obj->ID,
							'clientid' => $this->param( 'clientid' ),
						],
						$app_config,
						$task_list
					);

					if ( $job_id != null ) {
						$post_back        = $api->get_job_post_back( $job_id );
						$handler_delay    = defined( 'NGG_API_JOB_HANDLER_DELAY' ) ? intval( NGG_API_JOB_HANDLER_DELAY ) : 0;
						$handler_delay    = $handler_delay > 0 ? $handler_delay : 30; /* in seconds */
						$handler_maxsize  = defined( 'NGG_API_JOB_HANDLER_MAXSIZE' ) ? intval( NGG_API_JOB_HANDLER_MAXSIZE ) : 0;
						$handler_maxsize  = $handler_maxsize > 0 ? $handler_maxsize : $this->get_max_upload_size(); /* in bytes */
						$handler_maxfiles = $this->get_max_upload_files();

						$response['result']        = 'ok';
						$response['result_object'] = [
							'job_id'               => $job_id,
							'job_post_back'        => $post_back,
							'job_handler_url'      => home_url( '?photocrati_ajax=1&action=execute_nextgen_api_task_list' ),
							'job_handler_delay'    => $handler_delay,
							'job_handler_maxsize'  => $handler_maxsize,
							'job_handler_maxfiles' => $handler_maxfiles,
						];

						if ( ! defined( 'NGG_API_SUPPRESS_QUICK_EXECUTE' ) || NGG_API_SUPPRESS_QUICK_EXECUTE == false ) {
							if ( ! $api->is_execution_locked() ) {
								$this->start_locked_execute();

								try {
									$result = $api->handle_job( $job_id, $api->get_job_data( $job_id ), $app_config, $api->get_job_task_list( $job_id ), $extra_data );

									$response['result_object']['job_result'] = $api->get_job_task_list( $job_id );

									if ( $result ) {
										// everything was finished, remove job.
										$api->remove_job( $job_id );
									}
								} catch ( \Exception $e ) {
									// Exception is silently caught here as job execution errors are handled by the API.
									unset( $e );
								}

								$this->stop_locked_execute();
							}
						}
					} else {
						$response['result'] = 'error';
						$response['error']  = [
							'code'    => API::ERR_JOB_NOT_ADDED,
							'message' => __( 'Job could not be added.', 'nggallery' ),
						];
					}
				} else {
					$response['result'] = 'error';
					$response['error']  = [
						'code'    => API::ERR_NOT_AUTHORIZED,
						'message' => __( 'Authorization Failed.', 'nggallery' ),
					];
				}
			} else {
				$response['result'] = 'error';
				$response['error']  = [
					'code'    => API::ERR_NO_TASK_LIST,
					'message' => __( 'No task list was specified.', 'nggallery' ),
				];
			}
		} else {
			$response['result'] = 'error';
			$response['error']  = [
				'code'    => API::ERR_NOT_AUTHENTICATED,
				'message' => __( 'Authentication Failed.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Executes a NextGEN API task list action.
	 *
	 * @return array
	 */
	public function execute_nextgen_api_task_list_action() {
		$api      = $this->get_nextgen_api();
		$job_list = $api->get_job_list();
		$response = [];

		if ( $api->is_execution_locked() ) {
			$response['result'] = 'ok';
			$response['info']   = [
				'code'    => API::INFO_EXECUTION_LOCKED,
				'message' => __( 'Job execution is locked.', 'nggallery' ),
			];
		} elseif ( $job_list != null ) {
			$this->start_locked_execute();

			try {
				$extra_data    = $this->param_json( 'extra_data' ) ?? [];
				$job_count     = count( $job_list );
				$done_count    = 0;
				$client_result = [];

				foreach ( $_FILES as $key => $file ) {
					if ( substr( $key, 0, strlen( 'file_data_' ) ) == 'file_data_' ) {
						$extra_data[ substr( $key, strlen( 'file_data_' ) ) ] = $file;
					}
				}

				foreach ( $job_list as $job ) {
					$job_id   = $job['id'];
					$job_data = $job['data'];
					$result   = $api->handle_job( $job_id, $job_data, $job['app_config'], $job['task_list'], $extra_data );

					if ( isset( $job_data['clientid'] ) && $job_data['clientid'] == $this->param( 'clientid' ) ) {
						$client_result[ $job_id ] = $api->get_job_task_list( $job_id );
					}

					if ( $result ) {
						++$done_count;

						// everything was finished, remove job.
						$api->remove_job( $job_id );
					}

					if ( $api->should_stop_execution() ) {
						break;
					}
				}
			} catch ( \Exception $e ) {
				// Exception is silently caught here as job execution errors are handled by the API.
				unset( $e );
			}

			$this->stop_locked_execute();

			if ( $done_count == $job_count ) {
				$response['result'] = 'ok';
				$response['info']   = [
					'code'    => API::INFO_JOB_LIST_FINISHED,
					'message' => __( 'Job list is finished.', 'nggallery' ),
				];
			} else {
				$response['result'] = 'ok';
				$response['info']   = [
					'code'    => API::INFO_JOB_LIST_UNFINISHED,
					'message' => __( 'Job list is unfinished.', 'nggallery' ),
				];
			}

			if ( ! defined( 'NGG_API_SUPPRESS_QUICK_SUMMARY' ) || NGG_API_SUPPRESS_QUICK_SUMMARY == false ) {
				$response['result_object'] = $client_result;
			}
		} else {
			$response['result'] = 'ok';
			$response['info']   = [
				'code'    => API::INFO_NO_JOB_LIST,
				'message' => __( 'Job list is empty.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Gets the NextGEN API path list action.
	 *
	 * @return array
	 */
	public function get_nextgen_api_path_list_action() {
		$api        = $this->get_nextgen_api();
		$app_config = $this->param( 'app_config' );
		$user_obj   = $this->authenticate_user();
		$response   = [];

		if ( $user_obj != null && ! is_a( $user_obj, 'WP_Error' ) ) {
			wp_set_current_user( $user_obj->ID );

			$ftp_method = isset( $app_config['ftp_method'] ) ? $app_config['ftp_method'] : 'ftp';
			$creds      = [
				'connection_type' => $ftp_method == 'sftp' ? 'ssh' : 'ftp',
				'hostname'        => $app_config['ftp_host'],
				'port'            => $app_config['ftp_port'],
				'username'        => $app_config['ftp_user'],
				'password'        => $app_config['ftp_pass'],
			];

			require_once ABSPATH . 'wp-admin/includes/file.php';

			$wp_filesystem = $api->create_filesystem_access( $creds );
			$root_path     = null;
			$base_path     = null;
			$plugin_path   = null;

			if ( $wp_filesystem ) {
				$root_path   = $wp_filesystem->wp_content_dir();
				$base_path   = $wp_filesystem->abspath();
				$plugin_path = $wp_filesystem->wp_plugins_dir();
			} else {
				// fallbacks when unable to connect, try to see if we know the path already.
				$root_path = get_option( 'ngg_ftp_root_path' );

				if ( defined( 'FTP_BASE' ) ) {
					$base_path = FTP_BASE;
				}

				if ( $root_path == null && defined( 'FTP_CONTENT_DIR' ) ) {
					$root_path = FTP_CONTENT_DIR;
				}

				if ( defined( 'FTP_PLUGIN_DIR' ) ) {
					$plugin_path = FTP_PLUGIN_DIR;
				}

				if ( $base_path == null && $root_path != null ) {
					$base_path = dirname( $root_path );
				}

				if ( $root_path == null && $base_path != null ) {
					$root_path = rtrim( $base_path, '/\\' ) . '/wp-content/';
				}

				if ( $plugin_path == null && $base_path != null ) {
					$plugin_path = rtrim( $base_path, '/\\' ) . '/wp-content/plugins/';
				}
			}

			if ( $root_path != null ) {
				$response['result']        = 'ok';
				$response['result_object'] = [
					'root_path'       => $root_path,
					'wp_content_path' => $root_path,
					'wp_base_path'    => $base_path,
					'wp_plugin_path'  => $plugin_path,
				];
			} elseif ( $wp_filesystem != null ) {

					$response['result'] = 'error';
					$response['error']  = [
						'code'    => API::ERR_FTP_NO_PATH,
						'message' => __( 'Could not determine FTP path.', 'nggallery' ),
					];
			} else {
				$response['result'] = 'error';
				$response['error']  = [
					'code'    => API::ERR_FTP_NOT_CONNECTED,
					'message' => __( 'Could not connect to FTP to determine path.', 'nggallery' ),
				];
			}
		} else {
			$response['result'] = 'error';
			$response['error']  = [
				'code'    => API::ERR_NOT_AUTHENTICATED,
				'message' => __( 'Authentication Failed.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Gets the NextGEN API token action.
	 *
	 * @return array
	 */
	public function get_nextgen_api_token_action() {
		$regen    = $this->param( 'regenerate_token' ) ? true : false;
		$user_obj = $this->authenticate_user( $regen );
		$response = [];

		if ( $user_obj != null ) {
			$response['result']        = 'ok';
			$response['result_object'] = [
				'token' => get_user_meta( $user_obj->ID, 'nextgen_api_token', true ),
			];
		} else {
			$response['result'] = 'error';
			$response['error']  = [
				'code'    => API::ERR_NOT_AUTHENTICATED,
				'message' => __( 'Authentication Failed.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Gets the NextGEN API instance.
	 *
	 * @return API
	 */
	protected function get_nextgen_api() {
		if ( is_null( $this->nextgen_api ) ) {
			$this->nextgen_api = API::get_instance();
		}

		return $this->nextgen_api;
	}

	/**
	 * Authenticates a user for the API.
	 *
	 * @param bool $regenerate_token
	 * @return object|null
	 */
	protected function authenticate_user( $regenerate_token = false ) {
		$api      = $this->get_nextgen_api();
		$username = $this->param( 'q' );
		$password = $this->param( 'z' );
		$token    = $this->param( 'tok' );

		return $api->authenticate_user( $username, $password, $token, $regenerate_token );
	}

	/**
	 * Gets the maximum upload size.
	 *
	 * @return int
	 */
	protected function get_max_upload_size() {
		static $max_size = -1;

		if ( $max_size < 0 ) {
			$post_max_size = $this->parse_size( ini_get( 'post_max_size' ) );
			if ( $post_max_size > 0 ) {
				$max_size = $post_max_size;
			}

			$upload_max = $this->parse_size( ini_get( 'upload_max_filesize' ) );
			if ( $upload_max > 0 && $upload_max < $max_size ) {
				$max_size = $upload_max;
			}
		}
		return $max_size;
	}

	/**
	 * Parses a size string to bytes.
	 *
	 * @param string $size
	 * @return int
	 */
	protected function parse_size( $size ) {
		$unit = preg_replace( '/[^bkmgtpezy]/i', '', $size );
		$size = preg_replace( '/[^0-9\.]/', '', $size );
		if ( $unit ) {
			return round( $size * pow( 1024, stripos( 'bkmgtpezy', $unit[0] ) ) );
		} else {
			return round( $size );
		}
	}

	/**
	 * Gets the maximum number of upload files.
	 *
	 * @return int
	 */
	protected function get_max_upload_files() {
		return intval( ini_get( 'max_file_uploads' ) );
	}

	/**
	 * Handles shutdown callback.
	 */
	public function do_shutdown() {
		if ( $this->nextgen_api_locked ) {
			$this->get_nextgen_api()->set_execution_locked( false );
		}
	}

	/**
	 * Starts locked execution mode.
	 */
	protected function start_locked_execute() {
		if ( ! $this->shutdown_registered ) {
			\register_shutdown_function( [ $this, 'do_shutdown' ] );
			$this->shutdown_registered = true;
		}

		$this->get_nextgen_api()->set_execution_locked( true );
		$this->nextgen_api_locked = true;
	}

	/**
	 * Stops locked execution mode.
	 */
	protected function stop_locked_execute() {
		$this->get_nextgen_api()->set_execution_locked( false );
		$this->nextgen_api_locked = false;
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * Lightroom API class.
 */
class API {

	// NOTE: these constants' numeric values MUST remain the same, do NOT change the values.
	const ERR_NO_TASK_LIST      = 1001;
	const ERR_NOT_AUTHENTICATED = 1002;
	const ERR_NOT_AUTHORIZED    = 1003;
	const ERR_JOB_NOT_ADDED     = 1004;

	const ERR_FTP_NOT_AUTHENTICATED = 1101;
	const ERR_FTP_NOT_CONNECTED     = 1102;
	const ERR_FTP_NO_PATH           = 1103;

	const INFO_NO_JOB_LIST         = 6001;
	const INFO_JOB_LIST_FINISHED   = 6002;
	const INFO_JOB_LIST_UNFINISHED = 6003;
	const INFO_EXECUTION_LOCKED    = 6004;

	/**
	 * Instances cache.
	 *
	 * @var array
	 */
	public static $_instances = [];

	/**
	 * Start time for execution tracking.
	 *
	 * @var int
	 */
	public $_start_time;

	/**
	 * Gets an API instance.
	 *
	 * @param bool|string $context
	 * @return API
	 */
	public static function get_instance( $context = false ) {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new API( $context );
		}
		return self::$_instances[ $context ];
	}

	/**
	 * Constructs the API instance.
	 *
	 * @param string|bool $context
	 */
	public function __construct( $context ) {
		$this->_start_time = time();
	}

	/**
	 * Determines if execution should stop.
	 *
	 * @return bool
	 */
	public function should_stop_execution() {
		$timeout = defined( 'NGG_API_JOB_HANDLER_TIMEOUT' ) ? intval( NGG_API_JOB_HANDLER_TIMEOUT ) : ( intval( ini_get( 'max_execution_time' ) ) - 3 );
		$timeout = $timeout > 0 ? $timeout : 27; /* most hosts have a limit of 30 seconds execution time, so 27 should be a safe default */

		return ( time() - $this->_start_time >= $timeout );
	}

	/**
	 * Checks if execution is locked.
	 *
	 * @return bool
	 */
	public function is_execution_locked() {
		$lock_time = get_option( 'ngg_api_execution_lock', 0 );

		if ( $lock_time == 0 ) {
			return false;
		}

		$lock_max = defined( 'NGG_API_EXECUTION_LOCK_MAX' ) ? intval( NGG_API_EXECUTION_LOCK_MAX ) : 0;
		$lock_max = $lock_max > 0 ? $lock_max : 60 * 5; /* if the lock is 5 minutes old assume something went wrong and the lock couldn't be unset */

		$time_diff = time() - $lock_time;

		if ( $time_diff > $lock_max ) {
			return false;
		}

		return true;
	}

	/**
	 * Sets the execution locked state.
	 *
	 * @param bool $locked
	 */
	public function set_execution_locked( $locked ) {
		if ( $locked ) {
			update_option( 'ngg_api_execution_lock', time(), false );
		} else {
			update_option( 'ngg_api_execution_lock', 0, false );
		}
	}

	/**
	 * Gets the job list.
	 *
	 * @return array|null
	 */
	public function get_job_list() {
		return get_option( 'ngg_api_job_list' );
	}

	/**
	 * Adds a job to the job list.
	 *
	 * @param array $job_data
	 * @param array $app_config
	 * @param array $task_list
	 * @return string|null
	 */
	public function add_job( $job_data, $app_config, $task_list ) {
		$job_list = $this->get_job_list();
		$job_id   = uniqid();

		while ( isset( $job_list[ $job_id ] ) ) {
			$job_id = uniqid();
		}

		$job = [
			'id'         => $job_id,
			'post_back'  => [
				'token' => md5( $job_id ),
			],
			'data'       => $job_data,
			'app_config' => $app_config,
			'task_list'  => $task_list,
		];

		$job_list[ $job_id ] = $job;

		update_option( 'ngg_api_job_list', $job_list, false );

		return $job_id;
	}

	/**
	 * Updates a job in the job list.
	 *
	 * @param string $job_id
	 * @param array  $job
	 */
	public function _update_job( $job_id, $job ) {
		$job_list = $this->get_job_list();

		if ( isset( $job_list[ $job_id ] ) ) {
			$job_list[ $job_id ] = $job;

			update_option( 'ngg_api_job_list', $job_list, false );
		}
	}

	/**
	 * Removes a job from the job list.
	 *
	 * @param string $job_id
	 */
	public function remove_job( $job_id ) {
		$job_list = $this->get_job_list();

		if ( isset( $job_list[ $job_id ] ) ) {
			unset( $job_list[ $job_id ] );

			update_option( 'ngg_api_job_list', $job_list, false );
		}
	}

	/**
	 * Gets a job by ID.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job( $job_id ) {
		$job_list = $this->get_job_list();

		if ( isset( $job_list[ $job_id ] ) ) {
			return $job_list[ $job_id ];
		}

		return null;
	}

	/**
	 * Gets job data by job ID.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job_data( $job_id ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			return $job['data'];
		}

		return null;
	}

	/**
	 * Gets the task list for a job.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job_task_list( $job_id ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			return $job['task_list'];
		}

		return null;
	}

	/**
	 * Sets the task list for a job.
	 *
	 * @param string $job_id
	 * @param array  $task_list
	 * @return bool
	 */
	public function set_job_task_list( $job_id, $task_list ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			$job['task_list'] = $task_list;

			$this->_update_job( $job_id, $job );

			return true;
		}

		return false;
	}

	/**
	 * Gets the post-back data for a job.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job_post_back( $job_id ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			return $job['post_back'];
		}

		return null;
	}

	/**
	 * Authenticates a user for the API.
	 *
	 * @param string      $username
	 * @param string      $password
	 * @param string|null $token
	 * @param bool        $regenerate_token
	 * @return object|null
	 */
	public function authenticate_user( $username, $password, $token, $regenerate_token = false ) {
		$user_obj = null;

		if ( $token != null ) {
			$users = get_users(
				[
					'meta_key'   => 'nextgen_api_token',
					'meta_value' => $token,
				]
			);

			if ( $users != null && count( $users ) > 0 ) {
				$user_obj = $users[0];
			}
		}

		if ( $user_obj == null ) {
			if ( $username != null && $password != null ) {
				$user_obj = wp_authenticate( $username, $password );
				$token    = get_user_meta( $user_obj->ID, 'nextgen_api_token', true );

				if ( $token == null ) {
					$regenerate_token = true;
				}
			}
		}

		if ( is_a( $user_obj, 'WP_Error' ) ) {
			$user_obj = null;
		}

		if ( $regenerate_token ) {
			if ( $user_obj != null ) {
				$token = '';

				if ( function_exists( 'random_bytes' ) ) {
					$token = bin2hex( random_bytes( 16 ) );
				} elseif ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
					$token = bin2hex( openssl_random_pseudo_bytes( 16 ) );
				} else {
					for ( $i = 0; $i < 16; $i++ ) {
						$token .= bin2hex( wp_rand( 0, 15 ) );
					}
				}

				update_user_meta( $user_obj->ID, 'nextgen_api_token', $token );
			}
		}

		return $user_obj;
	}

	/**
	 * Creates filesystem access for FTP/SSH operations.
	 *
	 * @param array       $args
	 * @param string|null $method
	 * @return object|false
	 */
	public function create_filesystem_access( $args, $method = null ) {
		// taken from wp-admin/includes/file.php but with modifications.
		if ( ! $method && isset( $args['connection_type'] ) && 'ssh' == $args['connection_type'] && extension_loaded( 'ssh2' ) && function_exists( 'stream_get_contents' ) ) {
			$method = 'ssh2';
		}
		if ( ! $method && extension_loaded( 'ftp' ) ) {
			$method = 'ftpext';
		}
		if ( ! $method && ( extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) ) {
			$method = 'ftpsockets'; // Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread.
		}

		if ( ! $method ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';

		if ( ! class_exists( "WP_Filesystem_$method" ) ) {

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
			$abstraction_file = apply_filters( 'filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . $method . '.php', $method );

			if ( ! file_exists( $abstraction_file ) ) {
				return false;
			}

			require_once $abstraction_file;
		}

		$method_class = "WP_Filesystem_$method";

		$wp_filesystem = new $method_class( $args );

		// Define the timeouts for the connections. Only available after the construct is called to allow for per-transport overriding of the default.
		if ( ! defined( 'FS_CONNECT_TIMEOUT' ) ) {
			define( 'FS_CONNECT_TIMEOUT', 30 );
		}
		if ( ! defined( 'FS_TIMEOUT' ) ) {
			define( 'FS_TIMEOUT', 30 );
		}

		if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
			return false;
		}

		if ( ! $wp_filesystem->connect() ) {
			if ( $method == 'ftpext' ) { // attempt connecting with alternative method.
				return $this->create_filesystem_access( $args, 'ftpsockets' );
			}

			return false; // There was an error connecting to the server.
		}

		// Set the permission constants if not already set.
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
		}
		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
		}

		return $wp_filesystem;
	}

	/**
	 * Returns an actual scalar ID based on parametric ID (e.g. a parametric ID could represent the query ID from another task).
	 *
	 * @param mixed $id
	 * @param array $task_list
	 * @return mixed
	 */
	public function get_query_id( $id, &$task_list ) {
		$task_id = $id;

		if ( is_object( $task_id ) || is_array( $task_id ) ) {
			$id = null;

			// it was specified that the query ID is referencing the query ID from another task.
			if ( isset( $task_id['target'] ) && $task_id['target'] == 'task' ) {
				if ( isset( $task_id['id'] ) && isset( $task_list[ $task_id['id'] ] ) ) {
					$target_task = $task_list[ $task_id['id'] ];

					if ( isset( $target_task['query']['id'] ) ) {
						$id = $target_task['query']['id'];
					}
				}
			}
		}

		return $id;
	}

	/**
	 * Returns an actual scalar ID based on parametric ID (e.g. a parametric ID could represent the resulting object ID from another task).
	 *
	 * @param mixed $id
	 * @param array $result_list
	 * @return mixed
	 */
	public function get_object_id( $id, &$result_list ) {
		$task_id = $id;

		if ( is_object( $task_id ) || is_array( $task_id ) ) {
			$id = null;

			// it was specified that the query ID is referencing the result from another task.
			if ( isset( $task_id['target'] ) && $task_id['target'] == 'task' ) {
				if ( isset( $task_id['id'] ) && isset( $result_list[ $task_id['id'] ] ) ) {
					$target_result = $result_list[ $task_id['id'] ];

					if ( isset( $target_result['object_id'] ) ) {
						$id = $target_result['object_id'];
					}
				}
			}
		}

		return $id;
	}

	/**
	 * Finds an array entry by key and value.
	 *
	 * @param array  $array_target
	 * @param string $entry_key
	 * @param mixed  $entry_value
	 * @return int|string|null
	 */
	public function _array_find_by_entry( array $array_target, $entry_key, $entry_value ) {
		foreach ( $array_target as $key => $value ) {
			$item = $value;

			if ( isset( $item[ $entry_key ] ) && $item[ $entry_key ] == $entry_value ) {
				return $key;
			}
		}

		return null;
	}

	/**
	 * Filters an array by entry key.
	 *
	 * @param array  $array_target
	 * @param array  $array_source
	 * @param string $entry_key
	 * @return array
	 */
	public function _array_filter_by_entry( array $array_target, array $array_source, $entry_key ) {
		foreach ( $array_source as $key => $value ) {
			$item = $value;

			if ( isset( $item[ $entry_key ] ) ) {
				$find_key = $this->_array_find_by_entry( $array_target, $entry_key, $item[ $entry_key ] );

				if ( $find_key !== null ) {
					unset( $array_target[ $find_key ] );
				}
			}
		}

		return $array_target;
	}

	/**
	 * Checks if a filename is valid and safe.
	 *
	 * @param string $filename
	 * @return bool
	 */
	public function is_valid_filename( string $filename ): bool {
		$fs = Filesystem::get_instance();

		$root           = $fs->get_document_root( 'galleries' );
		$upload_tmp_dir = ini_get( 'upload_tmp_dir' );
		$tmp            = $upload_tmp_dir ? $upload_tmp_dir : sys_get_temp_dir();
		$filename       = str_replace( '\\', '/', $filename );

		// Do not allow phar:// streams, and block ".phar" filenames as well.
		if ( false !== strpos( $filename, '.phar' ) || false !== strpos( $filename, 'phar://' ) ) {
			return false;
		}

		// Also block all streams for good measure.
		if ( false !== strpos( $filename, '://' ) ) {
			return false;
		}

		// And prevent all "../".
		if ( false !== strpos( $filename, '../' ) ) {
			return false;
		}

		// Bitnami stores files in /opt/bitnami, but PHP's ReflectionClass->getFileName() can report /bitnami
		// which causes this method to reject files for being outside the server document root.
		if ( 0 === strpos( $filename, '/bitnami', 0 ) ) {
			$filename = '/opt' . $filename;
		}

		if ( '/tmp' === $tmp || '/tmp/' === $tmp ) {
			$filename = strstr( $filename, '/tmp' );
		}

		if ( 0 === strpos( $filename, '/' ) && ( strncmp( $filename, $root, strlen( $root ) ) !== 0 && strncmp( $filename, $tmp, strlen( $tmp ) ) !== 0 ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Handles a job execution.
	 *
	 * Note: handle_job only worries about processing the job, it does NOT remove finished jobs anymore, the responsibility is on the caller to remove the job when handle_job returns true, this is to allow calling get_job_*() methods after handle_job has been called.
	 *
	 * @param string     $job_id
	 * @param array      $job_data
	 * @param array      $app_config
	 * @param array      $task_list
	 * @param array|null $extra_data
	 * @return bool
	 */
	public function handle_job( $job_id, $job_data, $app_config, $task_list, $extra_data = null ) {
		$job_user         = $job_data['user'];
		$task_count       = count( $task_list );
		$done_count       = 0;
		$skip_count       = 0;
		$task_list_result = [];

		wp_set_current_user( $job_user );

		// Prevent PHP warnings about accessing undefined array keys.
		$app_config['ftp_path']  = isset( $app_config['ftp_path'] ) ? $app_config['ftp_path'] : '';
		$app_config['full_path'] = isset( $app_config['full_path'] ) ? $app_config['full_path'] : '';

		/*
		This block does all of the filesystem magic:
		 * - determines web paths based on FTP paths
		 * - initializes the WP_Filesystem mechanism in case this host doesn't support direct file access
		 *   (this might not be 100% reliable right now due to NG core not making use of WP_Filesystem)
		 */
		// $ftp_path is assumed to be WP_CONTENT_DIR as accessed through the FTP mount point.
		$ftp_path  = rtrim( $app_config['ftp_path'], '/\\' );
		$full_path = rtrim( $app_config['full_path'], '/\\' );
		$root_path = rtrim( WP_CONTENT_DIR, '/\\' );

		$creds  = true; // WP_Filesystem(true) requests direct filesystem access.
		$fs_sep = DIRECTORY_SEPARATOR;
		$wp_fs  = null;

		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( get_filesystem_method() !== 'direct' ) {
			$fs_sep     = '/';
			$ftp_method = isset( $app_config['ftp_method'] ) ? $app_config['ftp_method'] : 'ftp';

			$creds = [
				'connection_type' => $ftp_method == 'sftp' ? 'ssh' : 'ftp',
				'hostname'        => $app_config['ftp_host'],
				'port'            => $app_config['ftp_port'],
				'username'        => $app_config['ftp_user'],
				'password'        => $app_config['ftp_pass'],
			];
		}

		if ( WP_Filesystem( $creds ) ) {
			$wp_fs = $GLOBALS['wp_filesystem'];

			$path_prefix = $full_path;

			if ( $wp_fs->method === 'direct' ) {
				if ( trim( $ftp_path, " \t\n\r\x0B\\" ) == '' ) {
					// Note: if ftp_path is empty, we assume the FTP account home dir is on wp-content.
					$path_prefix = $root_path . $full_path;
				} else {
					$path_prefix = str_replace( $ftp_path, $root_path, $full_path );
				}
			}
		} else {
			include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			if ( ! $wp_fs ) {
				$wp_fs = new \WP_Filesystem_Direct( $creds );
			}
		}

		foreach ( $task_list as &$task_item ) {
			$task_id     = isset( $task_item['id'] ) ? $task_item['id'] : null;
			$task_name   = isset( $task_item['name'] ) ? $task_item['name'] : null;
			$task_type   = isset( $task_item['type'] ) ? $task_item['type'] : null;
			$task_auth   = isset( $task_item['auth'] ) ? $task_item['auth'] : null;
			$task_query  = isset( $task_item['query'] ) ? $task_item['query'] : null;
			$task_object = isset( $task_item['object'] ) ? $task_item['object'] : null;
			$task_status = isset( $task_item['status'] ) ? $task_item['status'] : null;
			$task_result = isset( $task_item['result'] ) ? $task_item['result'] : null;

			// make sure we don't repeat execution of already finished tasks.
			if ( $task_status == 'done' ) {
				++$done_count;

				// for previously finished tasks, store the result as it may be needed by future tasks.
				if ( $task_id != null && $task_result != null ) {
					$task_list_result[ $task_id ] = $task_result;
				}

				continue;
			}

			// make sure only valid and authorized tasks are executed.
			if ( $task_status == 'error' || $task_auth != 'allow' ) {
				++$skip_count;

				continue;
			}

			// the task query ID can be a simple (integer) ID or more complex ID that gets converted to a simple ID, for instance to point to an object that is the result of a previously finished task.
			if ( isset( $task_query['id'] ) ) {
				$task_query['id'] = $this->get_object_id( $task_query['id'], $task_list_result );
			}

			$task_error = null;

			switch ( $task_type ) {
				case 'gallery_add':
					$mapper     = GalleryMapper::get_instance();
					$gallery    = null;
					$gal_errors = '';

					if ( isset( $task_query['id'] ) ) {
						$gallery = $mapper->find( $task_query['id'], true );
					}

					if ( $gallery == null ) {
						$title   = isset( $task_object['title'] ) ? $task_object['title'] : '';
						$gallery = $mapper->create( [ 'title' => $title ] );

						if ( ! $gallery || ! $gallery->save() ) {
							if ( $gallery != null ) {
								$gal_errors = $gallery->validation();

								if ( is_array( $gal_errors ) ) {
									$gal_errors = ' [' . wp_json_encode( $gal_errors ) . ']';
								}
							}

							$gallery = null;
						}
					}

					if ( $gallery != null ) {
						$task_status              = 'done';
						$task_result['object_id'] = $gallery->id();
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							/* translators: 1: gallery title, 2: error details */
							'message' => sprintf( __( 'Gallery creation failed for "%1$s"%2$s.', 'nggallery' ), $title, $gal_errors ),
						];
					}

					break;
				case 'gallery_remove':
				case 'gallery_edit':
					if ( isset( $task_query['id'] ) ) {
						$mapper  = GalleryMapper::get_instance();
						$gallery = $mapper->find( $task_query['id'], true );
						$error   = null;

						if ( $gallery != null ) {
							if ( $task_type == 'gallery_remove' ) {
								/**
								 * Gallery mapper instance.
								 *
								 * @var GalleryMapper $mapper.
								 */
								if ( ! $mapper->destroy( $gallery, true ) ) {
									/* translators: %1$s: gallery ID */
									$error = __( 'Failed to remove gallery (%1$s).', 'nggallery' );
								}
							} elseif ( $task_type == 'gallery_edit' ) {
								if ( isset( $task_object['name'] ) ) {
									$gallery->name = $task_object['name'];
								}

								if ( isset( $task_object['title'] ) ) {
									$gallery->title = $task_object['title'];
								}

								if ( isset( $task_object['description'] ) ) {
									$gallery->galdesc = $task_object['description'];
								}

								if ( isset( $task_object['preview_image'] ) ) {
									$gallery->previewpic = $task_object['preview_image'];
								}

								if ( isset( $task_object['property_list'] ) ) {
									$properties = $task_object['property_list'];

									foreach ( $properties as $key => $value ) {
										$gallery->$key = $value;
									}
								}

								// this is used to determine whether the task is complete.
								$image_list_unfinished = false;

								if ( isset( $task_object['image_list'] ) && $wp_fs != null ) {
									$storage_path = isset( $task_object['storage_path'] ) ? $task_object['storage_path'] : null;
									$storage_path = trim( $storage_path, '/\\' );

									$storage      = StorageManager::get_instance();
									$image_mapper = ImageMapper::get_instance();
									$creds        = true;

									$images_folder = $path_prefix . $fs_sep . $storage_path . $fs_sep;
									$images_folder = str_replace( [ '\\', '/' ], $fs_sep, $images_folder );

									$images             = $task_object['image_list'];
									$result_images      = isset( $task_result['image_list'] ) ? $task_result['image_list'] : [];
									$images_todo        = array_values( $this->_array_filter_by_entry( $images, $result_images, 'localId' ) );
									$image_count        = count( $images );
									$result_image_count = count( $result_images );

									foreach ( $images_todo as $image_index => $image ) {
										$image_id       = isset( $image['id'] ) ? $image['id'] : null;
										$image_filename = isset( $image['filename'] ) ? $image['filename'] : null;
										$image_path     = isset( $image['path'] ) ? $image['path'] : null;
										$image_data_key = isset( $image['data_key'] ) ? $image['data_key'] : null;
										$image_action   = isset( $image['action'] ) ? $image['action'] : null;
										$image_status   = isset( $image['status'] ) ? $image['status'] : 'skip';

										if ( $image_filename == null ) {
											$image_filename = basename( $image_path );
										}

										$ngg_image = $image_mapper->find( $image_id, true );
										// ensure that we don't transpose the image from one gallery to another in case a remoteId is passed in for the image but the gallery associated to the collection cannot be found.
										if ( $ngg_image && $ngg_image->galleryid != $gallery->id() ) {
											$ngg_image = null;
											$image_id  = null;
										}

										$image_error = null;

										if ( $image_action == 'delete' ) {
											// image was deleted.
											if ( $ngg_image != null ) {
												$settings    = \Imagely\NGG\Settings\Settings::get_instance();
												$delete_fine = true;

												if ( $settings->get( 'deleteImg' ) ) {
													if ( ! $storage->delete_image( $ngg_image ) ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'Could not delete image file(s) from disk (%1$s).', 'nggallery' );
													}
												} elseif ( ! $image_mapper->destroy( $ngg_image ) ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'Could not remove image from gallery (%1$s).', 'nggallery' );
												}

												if ( $image_error == null ) {
													do_action( 'ngg_delete_picture', $ngg_image->{$ngg_image->id_field}, $ngg_image );

													$image_status = 'done';
												}
											} else {
												/* translators: %1$s: image filename */
												$image_error = __( 'Could not remove image because image was not found (%1$s).', 'nggallery' );
											}
										} else {
											// image was added or edited and needs updating.
											$image_data = null;

											if ( $image_data_key != null ) {
												if ( ! isset( $extra_data['__queuedImages'][ $image_data_key ] ) ) {
													if ( isset( $extra_data[ $image_data_key ] ) ) {
														$image_data_arr = $extra_data[ $image_data_key ];

														if ( $this->is_valid_filename( $image_data_arr['tmp_name'] ) ) {
															$image_data = file_get_contents( $image_data_arr['tmp_name'] );
														}
													}

													if ( $image_data == null ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'Could not obtain data for image (%1$s).', 'nggallery' );
													}
												} else {
													$image_status = 'queued';
												}
											} else {
												$image_path = $images_folder . $image_path;

												if ( $image_path !== null && $this->is_valid_filename( $image_path ) && $wp_fs->exists( $image_path ) ) {
													$image_data = $wp_fs->get_contents( $image_path );
													// delete temporary image.
													$wp_fs->delete( $image_path );
												} elseif ( is_multisite() ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'Could not find image file for image (%1$s). Using FTP Upload Method in Multisite is not recommended.', 'nggallery' );
												} else {
													/* translators: %1$s: image filename */
													$image_error = __( 'Could not find image file for image (%1$s).', 'nggallery' );
												}
											}

											if ( $image_data != null ) {
												try {
													$ngg_image = $storage->upload_base64_image( $gallery, $image_data, $image_filename, $image_id, true );
													$image_mapper->reimport_metadata( $ngg_image );

													if ( $ngg_image != null ) {
														$image_status = 'done';
														$image_id     = is_int( $ngg_image ) ? $ngg_image : $ngg_image->{$ngg_image->id_field};
													}
												} catch ( \E_NoSpaceAvailableException $e ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'No space available for image (%1$s).', 'nggallery' );
												} catch ( \E_UploadException $e ) {
													/* translators: %1$s: image filename */
													$upload_error_message = str_replace( '%', '%%', $e->getMessage() );
													$image_error          = $upload_error_message . __( ' (%1$s).', 'nggallery' );
												} catch ( \E_No_Image_Library_Exception $e ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'No image library present, image uploads will fail (%1$s).', 'nggallery' );

													// no point in continuing if the image library is not present but we don't break here to ensure that all images are processed (otherwise they'd be processed in further fruitless handle_job calls).
												} catch ( \E_InsufficientWriteAccessException $e ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'Inadequate system permissions to write image (%1$s).', 'nggallery' );
												} catch ( \E_InvalidEntityException $e ) {
													/* translators: 1: image filename, 2: image ID */
													$image_error = __( 'Requested image with id (%2$s) doesn\'t exist (%1$s).', 'nggallery' );
												} catch ( \E_EntityNotFoundException $e ) {
													// Gallery doesn't exist - already checked above so this should never happen.
													unset( $e );
												}
											}
										}                                       if ( $image_error != null ) {
											$image_status = 'error';

											$image['error'] = [
												'level'   => 'fatal',
												'message' => sprintf( $image_error, $image_filename, $image_id ),
											];
										}

										if ( $image_id ) {
											$image['id'] = $image_id;
										}

										if ( $image_status ) {
											$image['status'] = $image_status;
										}

										if ( $image_status != 'queued' ) {
											// append processed image to result image_list array.
											$result_images[] = $image;
										}

										if ( $this->should_stop_execution() ) {
											break;
										}
									}

									$task_result['image_list'] = $result_images;
									$image_list_unfinished     = count( $result_images ) < $image_count;

									// if images have finished processing, remove the folder used to store the temporary images (the folder should be empty due to delete() calls above).
									if ( ! $image_list_unfinished && $storage_path != null && $storage_path != $fs_sep && $path_prefix != null && $path_prefix != $fs_sep ) {
										$wp_fs->rmdir( $images_folder );
									}
								} elseif ( $wp_fs == null ) {
									/* translators: %1$s: gallery ID */
									$error = __( 'Could not access file system for gallery (%1$s).', 'nggallery' );
								}

								if ( ! $gallery->save() ) {
									// wpdb->update() returns 0 (falsy) when no columns changed,
									// even though the save succeeded. Only treat as error when
									// validation failed (array) or a real DB error occurred.
									// flush_query_cache() after save_entity() clears only a PHP
									// array, so $wpdb->last_error still reflects the DB result.
									global $wpdb;
									if ( $error == null && ( is_array( $gallery->validation() ) || ! empty( $wpdb->last_error ) ) ) {
										$gal_errors = '[' . wp_json_encode( $gallery->validation() ) . ']';
										/* translators: %1$s: gallery ID */
										$error = __( 'Failed to save modified gallery (%1$s). ', 'nggallery' ) . $gal_errors;
									}
								}
							}
						} else {
							/* translators: %1$s: gallery ID */
							$error = __( 'Could not find gallery (%1$s).', 'nggallery' );
						}
						if ( isset( $task_result['image_list'] ) && $gallery != null ) {
							$task_result['object_id'] = $gallery->id();
						}

						if ( $error == null ) {
							$task_status              = 'done';
							$task_result['object_id'] = $gallery->id();
						} else {
							$task_status = 'error';
							$task_error  = [
								'level'   => 'fatal',
								'message' => sprintf( $error, (string) $task_query['id'] ),
							];
						}

						if ( $image_list_unfinished ) {
							// we override the status of the task when the image list has not finished processing.
							$task_status = 'unfinished';
						}
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'No gallery was specified to edit.', 'nggallery' ),
						];
					}

					break;
				case 'album_add':
					$mapper = AlbumMapper::get_instance();

					$name       = isset( $task_object['name'] ) ? $task_object['name'] : '';
					$desc       = isset( $task_object['description'] ) ? $task_object['description'] : '';
					$previewpic = isset( $task_object['preview_image'] ) ? $task_object['preview_image'] : 0;
					$sortorder  = isset( $task_object['sort_order'] ) ? $task_object['sort_order'] : '';
					$page_id    = isset( $task_object['page_id'] ) ? $task_object['page_id'] : 0;

					$album = null;

					if ( isset( $task_query['id'] ) ) {
						$album = $mapper->find( $task_query['id'], true );
					}

					if ( $album == null ) {
						$album = $mapper->create(
							[
								'name'       => $name,
								'previewpic' => $previewpic,
								'albumdesc'  => $desc,
								'sortorder'  => $sortorder,
								'pageid'     => $page_id,
							]
						);

						if ( ! $album || ! $album->save() ) {
							$album = null;
						}
					}

					if ( $album != null ) {
						$task_status              = 'done';
						$task_result['object_id'] = $album->id();
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'Album creation failed.', 'nggallery' ),
						];
					}

					break;
				case 'album_remove':
				case 'album_edit':
					if ( isset( $task_query['id'] ) ) {
						$mapper = AlbumMapper::get_instance();
						$album  = $mapper->find( $task_query['id'], true );
						$error  = null;

						if ( $album ) {
							if ( $task_type == 'album_remove' ) {
								if ( ! $mapper->destroy( $album ) ) {
									/* translators: %1$s: album ID */
									$error = __( 'Failed to remove album (%1$s).', 'nggallery' );
								}
							} elseif ( $task_type == 'album_edit' ) {
								if ( isset( $task_object['name'] ) ) {
									$album->name = $task_object['name'];
								}

								if ( isset( $task_object['description'] ) ) {
									$album->albumdesc = $task_object['description'];
								}

								if ( isset( $task_object['preview_image'] ) ) {
									$album->previewpic = $task_object['preview_image'];
								}

								if ( isset( $task_object['property_list'] ) ) {
									$properties = $task_object['property_list'];

									foreach ( $properties as $key => $value ) {
										$album->$key = $value;
									}
								}

								if ( isset( $task_object['item_list'] ) ) {
									$item_list   = $task_object['item_list'];
									$sortorder   = $album->sortorder;
									$count       = count( $sortorder );
									$album_items = [];

									for ( $index = 0; $index < $count; $index++ ) {
										$album_items[ $sortorder[ $index ] ] = $index;
									}

									foreach ( $item_list as $item_info ) {
										$item_id    = isset( $item_info['id'] ) ? $item_info['id'] : null;
										$item_type  = isset( $item_info['type'] ) ? $item_info['type'] : null;
										$item_index = isset( $item_info['index'] ) ? $item_info['index'] : null;
										// translate ID in case this gallery has been created as part of this job.
										$item_id = $this->get_object_id( $item_id, $task_list_result );

										if ( $item_id != null ) {
											if ( $item_type == 'album' ) {
												$item_id = 'a' . $item_id;
											}

											$album_items[ $item_id ] = $count + $item_index;
										}
									}

									asort( $album_items );

									$album->sortorder = array_keys( $album_items );
								}

								if ( ! $mapper->save( $album ) ) {
									/* translators: %1$s: album ID */
									$error = __( 'Failed to save modified album (%1$s).', 'nggallery' );
								}
							}
						} else {
							/* translators: %1$s: album ID */
							$error = __( 'Could not find album (%1$s).', 'nggallery' );
						}

						if ( $error == null ) {
							$task_status              = 'done';
							$task_result['object_id'] = $album->id();
						} else {
							$task_status = 'error';
							$task_error  = [
								'level'   => 'fatal',
								'message' => sprintf( $error, (string) $task_query['id'] ),
							];
						}
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'No album was specified to edit.', 'nggallery' ),
						];
					}

					break;
				case 'gallery_list_get':
					$mapper       = GalleryMapper::get_instance();
					$gallery_list = $mapper->find_all();
					$result_list  = [];

					foreach ( $gallery_list as $gallery ) {
						$gallery_result = [
							'id'            => $gallery->id(),
							'name'          => $gallery->name,
							'title'         => $gallery->title,
							'description'   => $gallery->galdesc,
							'preview_image' => $gallery->previewpic,
						];

						$result_list[] = $gallery_result;
					}

					$task_status                 = 'done';
					$task_result['gallery_list'] = $result_list;

					break;
				case 'image_list_move':
					break;
			}

			$task_item['result'] = $task_result;
			$task_item['status'] = $task_status;
			$task_item['error']  = $task_error;

			// for previously finished tasks, store the result as it may be needed by future tasks.
			if ( $task_id != null && $task_result != null ) {
				$task_list_result[ $task_id ] = $task_result;
			}

			// if the task has finished, either successfully or unsuccessfully, increase count for done tasks.
			if ( 'unfinished' != $task_status ) {
				++$done_count;
			}

			if ( $this->should_stop_execution() ) {
				break;
			}
		}

		$this->set_job_task_list( $job_id, $task_list );

		if ( $task_count > $done_count + $skip_count ) {
			// unfinished tasks, return false.
			return false;
		} else {
			$upload_method = isset( $app_config['upload_method'] ) ? $app_config['upload_method'] : 'ftp';

			if ( 'ftp' == $upload_method ) {
				// everything was finished, write status file.
				$status_file    = '_ngg_job_status_' . strval( $job_id ) . '.txt';
				$status_content = wp_json_encode( $task_list );

				if ( null != $wp_fs ) {
					$status_path = $path_prefix . $fs_sep . $status_file;
					$status_path = str_replace( [ '\\', '/' ], $fs_sep, $status_path );
					$wp_fs->put_contents( $status_path, $status_content );
				} else {
					// if WP_Filesystem failed try one last desperate attempt at direct file writing.
					$status_path = str_replace( $ftp_path, $root_path, $full_path ) . DIRECTORY_SEPARATOR . $status_file;
					$status_path = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $status_path );
					file_put_contents( $status_path, $status_content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
				}
			}

			return true;
		}
	}
}
