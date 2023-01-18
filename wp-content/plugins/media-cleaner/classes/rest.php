<?php

class Meow_WPMC_Rest
{
	private $core = null;
	private $namespace = 'media-cleaner/v1';

	public function __construct( $core, $admin ) {
		$this->core = $core;
		$this->admin = $admin;
		$this->engine = $core->engine;
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	function rest_api_init() {
		try {
			// SETTINGS
			register_rest_route( $this->namespace, '/update_options', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_update_options' )
			) );
			register_rest_route( $this->namespace, '/all_settings', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_all_settings' ),
			) );

			// STATS & LISTING
			register_rest_route( $this->namespace, '/count', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_count' )
			) );
			register_rest_route( $this->namespace, '/all_ids', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_all_ids' ),
			) );
			register_rest_route( $this->namespace, '/stats', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_get_stats' ),
				'args' => array(
					'search' => array( 'required' => false ),
				)
			) );
			register_rest_route( $this->namespace, '/entries', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_entries' ),
				'args' => array(
					'limit' => array( 'required' => false, 'default' => 10 ),
					'skip' => array( 'required' => false, 'default' => 20 ),
					'filterBy' => array( 'required' => false, 'default' => 'all' ),
					'orderBy' => array( 'required' => false, 'default' => 'id' ),
					'order' => array( 'required' => false, 'default' => 'desc' ),
					'search' => array( 'required' => false ),
				)
			) );

			// ACTIONS
			register_rest_route( $this->namespace, '/set_ignore', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_set_ignore' )
			) );
			register_rest_route( $this->namespace, '/delete', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_delete' )
			) );
			register_rest_route( $this->namespace, '/recover', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_recover' )
			) );
			register_rest_route( $this->namespace, '/reset_db', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_reset_db' )
			) );

			// SCAN
			register_rest_route( $this->namespace, '/reset_issues', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_reset_issues' )
			) );
			register_rest_route( $this->namespace, '/extract_references', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_extract_references' )
			) );
			register_rest_route( $this->namespace, '/retrieve_medias', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_retrieve_medias' )
			) );
			register_rest_route( $this->namespace, '/retrieve_files', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_retrieve_files' )
			) );
			register_rest_route( $this->namespace, '/check_targets', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_check_targets' )
			) );

			// LOGS
			register_rest_route( $this->namespace, '/refresh_logs', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'refresh_logs' )
			) );
			register_rest_route( $this->namespace, '/clear_logs', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'clear_logs' )
			) );
		} 
		catch (Exception $e) {
			var_dump($e);
		}
	}

	/**
   * Validates certain option values
   * @param string $option Option name
   * @param mixed $value Option value
   * @return mixed|WP_Error Validated value if no problem
   */
  function validate_option( $option, $value ) {
    switch ( $option ) {
    case 'wpmc_dirs_filter':
    case 'wpmc_files_filter':
      if ( $value && @preg_match( $value, '' ) === false ) return new WP_Error( 'invalid_option', __( "Invalid Regular-Expression", 'media-cleaner' ) );
      break;
    }
    return $value;
  }

	function rest_reset_issues() {
		$this->core->reset_issues();
		return new WP_REST_Response( [ 'success' => true, 'message' => 'Issues were reset.' ], 200 );
	}

	function rest_count( $request ) {
		$params = $request->get_json_params();
		$src = isset( $params['source'] ) ? $params['source'] : null;
		$num = 0;
		if ( $src === 'posts' ) {
			$num = count( $this->engine->get_posts_to_check() );
		}
		else if ( $src === 'medias' ) {
			$num = count( $this->engine->get_media_entries() );
		}
		else {
			return new WP_REST_Response( [ 
				'success' => false, 
				'message' => 'No source was mentioned while calling count.'
			], 200 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $num ], 200 );
	}

	function rest_all_ids( $request ) {
		$params = $request->get_json_params();
		$src = isset( $params['source'] ) ? $params['source'] : null;
		$search = isset( $params['search'] ) ? $params['search'] : null;
		$ids = [];
		if ( $src === 'issues' ) {
			$ids = $this->get_issues_ids($search);
		}
		else if ( $src === 'ignored' ) {
			$ids = $this->get_ignored_ids($search);
		}
		else if ( $src === 'trash' ) {
			$ids = $this->get_trash_ids($search);
		}
		else {
			return new WP_REST_Response( [ 
				'success' => false, 
				'message' => 'No source was mentioned while calling all_ids.'
			], 200 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $ids ], 200 );
	}

	function rest_extract_references( $request ) {
		$params = $request->get_json_params();
		$limit = isset( $params['limit'] ) ? $params['limit'] : 0;
		$source = isset( $params['source'] ) ? $params['source'] : null;
		$limitsize = $this->core->get_option( 'posts_buffer' );
		$finished = false;
		$message = ""; // will be filled by extractRefsFrom...

		// Randomly throw an exception
		// if ( rand( 0, 2 ) !== 1 ) {
		// 	throw new Exception( 'Random Exception' );
		// }

		if ( $source === 'content' ) {
			$finished = $this->engine->extractRefsFromContent( $limit, $limitsize, $message );
		}
		else if ( $source === 'media' ) {
			$finished = $this->engine->extractRefsFromLibrary( $limit, $limitsize, $message );
		}
		else {
			return new WP_REST_Response( [ 
				'success' => false, 
				'message' => 'No source was mentioned while calling the extract_references action.'
			], 200 );
		}

		return new WP_REST_Response( [ 
			'success' => true, 
			'message' => $message,
			'data' => [
				'limit' => $limit + $limitsize, 
				'finished' => $finished,
			]	
		], 200 );
	}

	function rest_retrieve_files( $request ) {
		$params = $request->get_json_params();
		$path = isset( $params['path'] ) ? $params['path'] : null;
		$files = $this->engine->get_files( $path );
		$files_count = count( $files );
		$message = null;
		if ( $files_count === 0 ) {
			$message = sprintf( __( "No files for this path (%s).", 'media-cleaner' ), $path );
		}
		else {
			$message = sprintf( __( "Retrieved %d targets.", 'media-cleaner' ), $files_count );
		}
		return new WP_REST_Response( [ 
			'success' => true, 
			'message' => $message,
			'data' => [
				'results' => $files
			],
		], 200 );
	}

	function rest_retrieve_medias( $request ) {
		$params = $request->get_json_params();
		$limit = isset( $params['limit'] ) ? $params['limit'] : 0;
		$limitsize = $this->core->get_option( 'medias_buffer' );
		$unattachedOnly = $this->core->get_option( 'attach_is_use' );
		$results = $this->engine->get_media_entries( $limit, $limitsize, $unattachedOnly );
		$finished = count( $results ) < $limitsize;
		$message = sprintf( __( "Retrieved %d targets.", 'media-cleaner' ), count( $results ) );
		return new WP_REST_Response( [ 
			'success' => true, 
			'message' => $message,
			'data' => [
				'limit' => $limit + $limitsize,
				'finished' => $finished,
				'results' => $results
			]	
		], 200 );
	}

	function rest_check_targets( $request ) {
		$params = $request->get_json_params();
		// DEBUG: Simulate a timeout
		// $this->core->deepsleep(10); header("HTTP/1.0 408 Request Timeout"); exit;

		//ob_start();
		$data = $params['targets'];
		$method = $this->core->current_method;

		$this->core->timeout_check_start( count( $data ) );
		$success = 0;
		if ( $method == 'files' ) {
			do_action( 'wpmc_check_file_init' ); // Build_CroppedFile_Cache() in pro core.php
		}
		foreach ( $data as $piece ) {
			$this->core->timeout_check();
			if ( $method == 'files' ) {
				$this->core->log( "🔎 Checking: {$piece}..." );
				$result = ( $this->engine->check_file( $piece ) ? 1 : 0 );
				if ( $result ) {
					$success += $result;
				}
				// else {
				// 	$this->core->log( "👻 Nothing found." );
				// }
			}
			else if ( $method == 'media' ) {
				$this->core->log( "🔎 Checking #{$piece}..." );
				$result = ( $this->engine->check_media( $piece ) ? 1 : 0 );
				if ( $result ) {
					$success += $result;
				}
				// else {
				// 	$this->core->log( "👻 Nothing found." );
				// }
			}
			//$this->core->log();
			$this->core->timeout_check_additem();
		}
		//ob_end_clean();
		$elapsed = $this->core->timeout_get_elapsed();
		$issues_found = count( $data ) - $success;
		$message = sprintf(
			// translators: %1$d is a number of targets, %2$d is a number of issues, %3$s is elapsed time in milliseconds
			__( 'Checked %1$d targets and found %2$d issues in %3$s.', 'media-cleaner' ),
			count( $data ), $issues_found, $elapsed
		);

		return new WP_REST_Response( [ 
			'success' => true, 
			'message' => $message,
			'results' => $success,
		], 200 );
	}

	function refresh_logs() {
		$data = "No data.";
		if ( file_exists( WPMC_PATH . '/logs/media-cleaner.log' ) ) {
			$data = file_get_contents( WPMC_PATH . '/logs/media-cleaner.log' );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	function clear_logs() {
		unlink( WPMC_PATH . '/logs/media-cleaner.log' );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	function rest_all_settings() {
		return new WP_REST_Response( [
			'success' => true,
			'data' => array_merge( $this->core->get_all_options(), [
				'incompatible_plugins' => !class_exists( 'MeowPro_WPMC_Core' ) ? Meow_WPMC_Support::get_issues() : []
			])
		], 200 );
	}

	function rest_update_options( $request ) {
		try {
			$params = $request->get_json_params();
			$value = $params['options'];
			$options = $this->core->update_options( $value );
			$success = !!$options;
			$message = __( $success ? 'OK' : "Could not update options.", 'media-cleaner' );
			return new WP_REST_Response([ 'success' => $success, 'message' => $message, 'options' => $options ], 200 );
		} 
		catch ( Exception $e ) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

	function rest_reset_db() {
		wpmc_reset();
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	function rest_entries( $request ) {
		global $wpdb;
		$limit = sanitize_text_field( $request->get_param('limit') );
		$skip = sanitize_text_field( $request->get_param('skip') );
		$filterBy = sanitize_text_field( $request->get_param('filterBy') );
		$orderBy = sanitize_text_field( $request->get_param('orderBy') );
		$order = sanitize_text_field( $request->get_param('order') );
		$search = sanitize_text_field( $request->get_param('search') );
		$table_scan = $wpdb->prefix . "mclean_scan";
		$total = 0;

		$whereSql = '';
		if ( $filterBy == 'issues' ) {
			$whereSql = 'WHERE ignored = 0 AND deleted = 0';
			$total = $this->count_issues($search);
		}
		else if ( $filterBy == 'ignored' ) {
			$whereSql = 'WHERE ignored = 1';
			$total = $this->count_ignored($search);
		}
		else if ( $filterBy == 'trash' ) {
			$whereSql = 'WHERE deleted = 1';
			$total = $this->count_trash($search);
		}
		else {
			$whereSql = 'WHERE deleted = 0';
		}

		$orderSql = 'ORDER BY id DESC';
		if ($orderBy === 'type') {
			$orderSql = 'ORDER BY postId ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ($orderBy === 'postId') {
			$orderSql = 'ORDER BY postId ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ($orderBy === 'path') {
			$orderSql = 'ORDER BY path ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ($orderBy === 'size') {
			$orderSql = 'ORDER BY size ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}

		$entries = [];
		if ( empty( $search ) ) {
			$entries = $wpdb->get_results( 
				$wpdb->prepare( "SELECT id, type, postId, path, size, ignored, deleted, issue
					FROM $table_scan
					$whereSql
					$orderSql
					LIMIT %d, %d", $skip, $limit
				)
			);
		}
		else {
			$entries = $wpdb->get_results( 
				$wpdb->prepare( "SELECT id, type, postId, path, size, ignored, deleted, issue
					FROM $table_scan
					$whereSql
					AND path LIKE %s
					$orderSql
					LIMIT %d, %d", ( '%' . $search . '%' ), $skip, $limit
				)
			);
		}

		$base = '/' . ( $filterBy == 'trash' ? $this->core->get_trashurl() : $this->core->upload_url );
		foreach ( $entries as $entry ) {
			// FILESYSTEM
			if ( $entry->type == 0 ) {
				$entry->thumbnail_url = htmlspecialchars( trailingslashit( $base ) . $entry->path, ENT_QUOTES );
				$entry->image_url = $entry->thumbnail_url;
			}
			// MEDIA
			else {
				$attachment_src = wp_get_attachment_image_src( $entry->postId, 'thumbnail' );
				$attachment_src_large = wp_get_attachment_image_src( $entry->postId, 'large' );
				$thumbnail = empty( $attachment_src ) ? null : $attachment_src[0];
				$image = empty( $attachment_src_large ) ? null : $attachment_src_large[0];
				// This was working when the Post Type" was attachment"
				if ( $filterBy == 'trash' && !empty( $thumbnail ) ) {
					$new_url = $this->core->clean_url( $thumbnail );
					$thumbnail = htmlspecialchars( trailingslashit( $base ) . $new_url, ENT_QUOTES );
				}
				if ( $filterBy == 'trash' && empty( $thumbnail ) ) {
					$file = get_post_meta( $entry->postId, '_wp_attached_file', true );
					$featured_image = wp_get_attachment_metadata( $entry->postId );
					$thumbnail = "";
					$image = htmlspecialchars( trailingslashit( $base ) . $file, ENT_QUOTES );
					if ( isset( $featured_image['sizes']['thumbnail']['file'] ) ) {
						$path = pathinfo( $file );
						$thumbnail = $featured_image['sizes']['thumbnail']['file'];
						$thumbnail = htmlspecialchars( trailingslashit( $base ) .
							trailingslashit( $path['dirname'] ) . $thumbnail, ENT_QUOTES );
					}
					else {
						$thumbnail = $image;
					}
				}
				$entry->thumbnail_url = $thumbnail;
				$entry->image_url = $image;
				$entry->title = html_entity_decode( get_the_title( $entry->postId ) );
			}
		}

		return new WP_REST_Response( [ 'success' => true, 'data' => $entries, 'total' => $total ], 200 );
	}

	function rest_set_ignore( $request ) {
		$params = $request->get_json_params();
		$ignore = (boolean)$params['ignore'];
		$entryIds = isset( $params['entryIds'] ) ? (array)$params['entryIds'] : null;
		$entryId = isset( $params['entryId'] ) ? (int)$params['entryId'] : null;
		$data = null;
		if ( !empty( $entryIds ) ) {
			foreach ( $entryIds as $entryId ) {
				$this->core->ignore( $entryId, $ignore );
			}
			$data = 'N/A';
		}
		else if ( !empty( $entryId ) ) {
			$data = $this->core->ignore( $entryId, $ignore );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	function rest_delete( $request ) {
		$params = $request->get_json_params();
		$entryIds = isset( $params['entryIds'] ) ? (array)$params['entryIds'] : null;
		$entryId = isset( $params['entryId'] ) ? (int)$params['entryId'] : null;
		$data = null;
		if ( !empty( $entryIds ) ) {
			foreach ( $entryIds as $entryId ) {
				$this->core->delete( $entryId );
			}
			$data = 'N/A';
		}
		else if ( !empty( $entryId ) ) {
			$data = $this->core->delete( $entryId );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	function rest_recover( $request ) {
		$params = $request->get_json_params();
		$entryIds = isset( $params['entryIds'] ) ? (array)$params['entryIds'] : null;
		$entryId = isset( $params['entryId'] ) ? (int)$params['entryId'] : null;
		$data = null;
		if ( !empty( $entryIds ) ) {
			foreach ( $entryIds as $entryId ) {
				$this->core->recover( $entryId );
			}
			$data = 'N/A';
		}
		else if ( !empty( $entryId ) ) {
			$data = $this->core->recover( $entryId );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	function get_issues_ids($search) {
		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		return $wpdb->get_col( "SELECT ID FROM $table_scan WHERE ignored = 0 AND deleted = 0 $whereSql" );
	}

	function get_ignored_ids($search) {
		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		return $wpdb->get_col( "SELECT ID FROM $table_scan WHERE ignored = 1 $whereSql" );
	}

	function get_trash_ids($search) {
		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		return $wpdb->get_col( "SELECT ID FROM $table_scan WHERE deleted = 1 $whereSql" );
	}

	function count_issues($search) {
		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		return (int)$wpdb->get_var( "SELECT COUNT(*) FROM $table_scan WHERE ignored = 0 AND deleted = 0 $whereSql" );
	}

	function count_ignored($search) {
		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		return (int)$wpdb->get_var( "SELECT COUNT(*) FROM $table_scan WHERE ignored = 1 $whereSql" );
	}

	function count_trash($search) {
		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		return (int)$wpdb->get_var( "SELECT COUNT(*) FROM $table_scan WHERE deleted = 1 $whereSql" );
	}

	function rest_get_stats($request) {
		$search = sanitize_text_field( $request->get_param('search') );

		global $wpdb;
		$whereSql = empty($search) ? '' : $wpdb->prepare("AND path LIKE %s", ( '%' . $search . '%' ));
		$table_scan = $wpdb->prefix . "mclean_scan";
		$issues = $wpdb->get_row( "SELECT COUNT(*) as entries, SUM(size) as size
			FROM $table_scan WHERE ignored = 0 AND deleted = 0 $whereSql" );
		$ignored = (int)$wpdb->get_var( "SELECT COUNT(*) 
			FROM $table_scan WHERE ignored = 1 $whereSql" );
		$trash = $wpdb->get_row( "SELECT COUNT(*) as entries, SUM(size) as size
			FROM $table_scan WHERE deleted = 1 $whereSql" );

		return new WP_REST_Response( [ 'success' => true, 'data' => array(
			'issues' => $issues->entries,
			'issues_size' => $issues->size,
			'ignored' => $ignored,
			'trash' => $trash->entries,
			'trash_size' => $trash->size
		) ], 200 );
	}
}
