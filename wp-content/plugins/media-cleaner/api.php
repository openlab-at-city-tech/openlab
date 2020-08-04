<?php

class Meow_WPMC_API {
	private $core;
	private $admin;
	private $engine;

	// Error code enums
	// Should be able to referred from the JS client
	const E = array (
		'INVALID_NONCE' => 1,
		'INVALID_PARAMS' => 2,
		'FILE_OPEN_FAILURE' => 10,
		'FILE_WRITE_FAILURE' => 11
	);

	/**
	 * @param Meow_WPMC_Core   $core
	 * @param Meow_WPMC_Admin  $admin
	 * @param Meow_WPMC_Engine $engine
	 */
	function __construct( $core, $admin, $engine ) {
		$this->core = $core;
		$this->engine = $engine;
		$this->admin = $admin;
		add_action( 'wp_ajax_wpmc_define', array( $this, 'wp_ajax_wpmc_define' ) );
		add_action( 'wp_ajax_wpmc_get_num_posts', array( $this, 'wp_ajax_wpmc_get_num_posts' ) );
		add_action( 'wp_ajax_wpmc_extract_references', array( $this, 'wp_ajax_wpmc_extract_references' ) );
		add_action( 'wp_ajax_wpmc_retrieve_targets', array( $this, 'wp_ajax_wpmc_retrieve_targets' ) );
		add_action( 'wp_ajax_wpmc_check_targets', array( $this, 'wp_ajax_wpmc_check_targets' ) );
		add_action( 'wp_ajax_wpmc_get_all_issues', array( $this, 'wp_ajax_wpmc_get_all_issues' ) );
		add_action( 'wp_ajax_wpmc_get_all_deleted', array( $this, 'wp_ajax_wpmc_get_all_deleted' ) );
		add_action( 'wp_ajax_wpmc_delete_do', array( $this, 'wp_ajax_wpmc_delete_do' ) );
		add_action( 'wp_ajax_wpmc_ignore_do', array( $this, 'wp_ajax_wpmc_ignore_do' ) );
		add_action( 'wp_ajax_wpmc_recover_do', array( $this, 'wp_ajax_wpmc_recover_do' ) );
		add_action( 'wp_ajax_wpmc_validate_option', array( $this, 'wp_ajax_wpmc_validate_option' ) );
	}

	/*******************************************************************************
	 * ASYNCHRONOUS AJAX FUNCTIONS
	 ******************************************************************************/

	/**
	 * Writes a constant definition on wp-config.php
	 *
	 * Method: POST
	 * Params:
	 *  - nonce: Nonce
	 *  - name:  Name of the constant to define
	 *  - value: Value of the constant
	 * Return:
	 *  - data.action:  'wpmc_define'
	 *  - data.message: <string> (on failure)
	 *  - data.code:    <int>    (on failure)
	 */
	function wp_ajax_wpmc_define() {
		try {
			$action = 'wpmc_define';
			$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
			if ( !wp_verify_nonce( $nonce, $action ) ) throw new Exception( "Nonce check failed", self::E['INVALID_NONCE'] );

			$name = isset( $_POST['name'] ) ? $_POST['name'] : '';
			$value = isset( $_POST['value'] ) ? $_POST['value'] : '';
			$result = array (
				'success' => false,
				'data' => array ( 'action' => $action )
			);

			/* Check the constant name and type */
			$supported = false;
			$constants = array ( // Supported constants
				/* We can add more constants to support */
				'MEDIA_TRASH' => array ( 'type' => 'boolean' )
			);
			foreach ( $constants as $i => $item ) {
				if ( $i != $name ) continue;
				$supported = true;
				/* Transform the value into a proper format */
				switch ( $item['type'] ) {
				case 'boolean':
				case 'bool':
					$value = boolval( $value ) ? 'true' : 'false';
					break;
				case 'integer':
				case 'int':
					$value = intval( $value );
					break;
				case 'string':
					$value = "'" . strval( $value ) . "'";
					break;
				}
				break;
			}
			if ( !$supported ) throw new Exception( "Invalid parameters", self::E['INVALID_PARAMS'] );

			/* Open wp-config.php */
			$conf = ABSPATH . 'wp-config.php';
			$stream = fopen( $conf, 'r+' );
			if ( $stream === false ) throw new Exception( "Failed to open the config file", self::E['FILE_OPEN_FAILURE'] );

			try {
				if ( !flock( $stream, LOCK_EX ) ) throw new Exception( "Failed to lock the config file", self::E['FILE_OPEN_FAILURE'] );
				$stat = fstat( $stream );

				/* Find out the ideal position to write on */
				$found = false;
				$patterns = array (
					array (
						'regex' => '^\/\*\s*' . preg_quote( "That's all, stop editing!" ) . '.*?\s*\*\/',
						'where' => 'above'
					)
				);
				$current = 0;
				while ( !feof( $stream ) ) {
					$line = fgets( $stream ); // Read line by line
					if ( $line === false ) break; // No more lines
					$prev = $current; // Previous position
					$current = ftell( $stream ); // Current position

					foreach ( $patterns as $item ) {
						if ( !preg_match( '/'.$item['regex'].'/', trim( $line ) ) ) continue;
						/* Found */
						$found = true;
						if ( $item['where'] == 'above' ) {
							fseek( $stream, $prev );
							$current = $prev;
						}
						break 2;
					}
				}

				/* Check if the position is found */
				if ( !$found ) throw new Exception( "Cannot determine the position to write on", self::E['FILE_WRITE_FAILURE'] );

				/* Write the constant definition line */
				$new = "define( '{$name}', {$value} );" . PHP_EOL;
				$rest = fread( $stream, $stat['size'] - $current );
				fseek( $stream, $current );
				$written = fwrite( $stream, $new . $rest );

				/* All done */
				if ( $written === false ) throw new Exception( "Failed to write on the config file", self::E['FILE_WRITE_FAILURE'] );
				fclose( $stream );

			} catch( Exception $e ) {
				fclose( $stream );
				throw $e;
			}

		} catch( Exception $e ) {
			$result['data']['message'] = $e->getMessage();
			$result['data']['code'] = $e->getCode();
			exit( json_encode( $result ) );
		}

		$result['success'] = true;
		exit( json_encode( $result ) );
	}

	/**
	 * Method: POST
	 * Params:
	 *  - source: 'content' | 'media'
	 * Return:
	 *  - data.action: 'get_num_posts'
	 *  - data.num:    A number of the posts
	 */
	function wp_ajax_wpmc_get_num_posts() {
		$src = isset( $_POST['source'] ) ? $_POST['source'] : null;
		$num = 0;

		switch ($src) {
		case 'content':
			$num = count( $this->engine->get_posts_to_check() );
			break;
		case 'media':
			$num = count( $this->engine->get_media_entries() );
			break;
		}

		exit( json_encode( array (
			'success' => true,
			'data'    => array (
				'action'  => 'get_num_posts',
				'num'     => $num
			)
		)));
	}

	// Analyze the posts to extract the references.
	function wp_ajax_wpmc_extract_references() {
		$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : 0;
		$source = isset( $_POST['source'] ) ? $_POST['source'] : null;
		$limitsize = get_option( 'wpmc_posts_buffer', 5 );

		$finished = false;
		$message = ""; // will be filled by extractRefsFrom...
		if ( $source === 'content' )
			$finished = $this->engine->extractRefsFromContent( $limit, $limitsize, $message );
		else if ( $source === 'media' )
			$finished = $this->engine->extractRefsFromLibrary( $limit, $limitsize, $message );
		else {
			error_log('Media Cleaner: No source was mentioned while calling the extract_references action.');
		}

		$output = array(
			'success' => true,
			'action' => 'extract_references',
			'source' => $source,
			'limit' => $limit + $limitsize,
			'finished' => $finished,
			'message' => $message,
		);
		echo json_encode( $output );
		die();
	}

	// Retrieve either the the Media IDs or the files which need to be scanned.
	function wp_ajax_wpmc_retrieve_targets() {
		global $wpdb;
		$method = $this->core->current_method;

		if ( $method == 'files' ) {
			$output = null;
			$path = isset( $_POST['path'] ) ? $_POST['path'] : null;
			$files = $this->engine->get_files( $path );
			if ( $files === null ) {
				$output = array(
					'success' => true,
					'action' => 'retrieve_targets',
					'method' => 'files',
					'message' => __( "No files for this path ($path).", 'media-cleaner' ),
					'results' => array(),
				);
			}
			else {
				// translators: %d is a count of files
				$message = sprintf( __( "Retrieved %d targets.", 'media-cleaner' ), count( $files ) );
				$output = array(
					'success' => true,
					'action' => 'retrieve_targets',
					'method' => 'files',
					'message' => $message,
					'results' => $files,
				);
			}
			echo json_encode( $output );
			die();
		}

		if ( $method == 'media' ) {
			$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : 0;
			$limitsize = get_option( 'wpmc_medias_buffer', 100 );
			$results = $this->engine->get_media_entries( $limit, $limitsize );
			$finished = count( $results ) < $limitsize;
			$message = sprintf( __( "Retrieved %d targets.", 'media-cleaner' ), count( $results ) );
			$output = array(
				'success' => true,
				'action' => 'retrieve_targets',
				'method' => 'media',
				'limit' => $limit + $limitsize,
				'finished' => $finished,
				'message' => $message,
				'results' => $results,
			);
			echo json_encode( $output );
			die();
		}

		// No task.
		echo json_encode( array( 'success' => false, 'message' => __( "No task.", 'media-cleaner' ) ) );
		die();
	}

	// Actual scan (by giving a media ID or a file path)
	function wp_ajax_wpmc_check_targets() {

		// DEBUG: Simulate a timeout
		// $this->core->deepsleep(10); header("HTTP/1.0 408 Request Timeout"); exit;

		ob_start();
		$data = $_POST['data'];
		$method = $this->core->current_method;

		$this->core->timeout_check_start( count( $data ) );
		$success = 0;
		if ( $method == 'files' ) {
			do_action( 'wpmc_check_file_init' ); // Build_CroppedFile_Cache() in pro core.php
		}
		foreach ( $data as $piece ) {
			$this->core->timeout_check();
			if ( $method == 'files' ) {
				$this->core->log( "Check File: {$piece}" );
				$result = ( $this->engine->check_file( $piece ) ? 1 : 0 );
				if ( $result )
					$success += $result;
			}
			else if ( $method == 'media' ) {
				$this->core->log( "Checking Media #{$piece}" );
				$result = ( $this->engine->check_media( $piece ) ? 1 : 0 );
				if ( $result ) {
					$success += $result;
				}
			}
			$this->core->log();
			$this->core->timeout_check_additem();
		}
		ob_end_clean();
		$elapsed = $this->core->timeout_get_elapsed();
		$message = sprintf(
			// translators: %1$d is a number of targets, %2$d is a number of issues, %3$s is elapsed time in milliseconds
			__( 'Checked %1$d targets and found %2$d issues in %3$s.', 'media-cleaner' ),
			count( $data ), count( $data ) - $success, $elapsed
		);
		echo json_encode(
			array(
				'success' => true,
				'action' => 'check_targets',
				'method' => $method,
				'message' => $message,
				'results' => $success,
			)
		);
		die();
	}

	function wp_ajax_wpmc_get_all_issues() {
		global $wpdb;
		$isTrash = ( isset( $_POST['isTrash'] ) && $_POST['isTrash'] == 1 ) ? true : false;
		$table_name = $wpdb->prefix . "mclean_scan";
		$q = "SELECT id FROM $table_name WHERE ignored = 0 AND deleted = " . ( $isTrash ? 1 : 0 );
		if ( isset( $_POST['filter'] ) && !empty( $_POST['filter'] ) ) {
			$filter = sanitize_text_field( $_POST['filter']['filter'] );
			$search = sanitize_text_field( $_POST['filter']['search'] );
		}
		if ( !empty( $search ) )
			$q = $wpdb->prepare( $q . ' AND path LIKE %s', '%' . $wpdb->esc_like( $search ) . '%' );
		if ( !empty( $filter ) )
			$q = $wpdb->prepare( $q . ' AND issue = %s', $filter );
		$ids = $wpdb->get_col( $q );
		echo json_encode(
			array(
				'results' => array( 'ids' => $ids ),
				'success' => true,
				'message' => __( "List generated.", 'media-cleaner' )
			)
		);
		die;
	}

	function wp_ajax_wpmc_get_all_deleted() {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$ids = $wpdb->get_col( "SELECT id FROM $table_name WHERE ignored = 0 AND deleted = 1" );
		echo json_encode(
			array(
				'results' => array( 'ids' => $ids ),
				'success' => true,
				'message' => __( "List generated.", 'media-cleaner' )
			)
		);
		die;
	}

	function wp_ajax_wpmc_delete_do() {
		ob_start();
		$data = $_POST['data'];
		$success = 0;
		foreach ( $data as $piece ) {
			$success += ( $this->core->delete( $piece ) ? 1 : 0 );
		}
		ob_end_clean();
		echo json_encode(
			array(
				'success' => true,
				'result' => array( 'data' => $data, 'success' => $success ),
				'message' => __( "Status unknown.", 'media-cleaner' )
			)
		);
		die();
	}

	function wp_ajax_wpmc_ignore_do() {
		ob_start();
		$data = $_POST['data'];
		$success = 0;
		foreach ( $data as $piece ) {
			$success += ( $this->core->ignore( $piece ) ? 1 : 0 );
		}
		ob_end_clean();
		echo json_encode(
			array(
				'success' => true,
				'result' => array( 'data' => $data, 'success' => $success ),
				'message' => __( "Status unknown.", 'media-cleaner' )
			)
		);
		die();
	}

	function wp_ajax_wpmc_recover_do() {
		ob_start();
		$data = $_POST['data'];
		$success = 0;
		foreach ( $data as $piece ) {
			$success +=  ( $this->core->recover( $piece ) ? 1 : 0 );
		}
		ob_end_clean();
		echo json_encode(
			array(
				'success' => true,
				'result' => array( 'data' => $data, 'success' => $success ),
				'message' => __( "Status unknown.", 'media-cleaner' )
			)
		);
		die();
	}

	function wp_ajax_wpmc_validate_option() {
		$name = $_POST['name']; // Option Name
		$value = $_POST['value']; // Option Value
		$value = wp_unslash( $value ); // Unescape backslashes
		$validated = $this->admin->validate_option( $name, $value );
		if ( $validated instanceof WP_Error ) { // Invalid value
			$error = array (
				'code' => $validated->get_error_code() ?: 'invalid_option',
				'message' => $validated->get_error_message() ?: __( "Invalid Option Value", 'media-cleaner' )
			);
			wp_send_json_error( $error );
		}
		wp_send_json_success();
	}
}