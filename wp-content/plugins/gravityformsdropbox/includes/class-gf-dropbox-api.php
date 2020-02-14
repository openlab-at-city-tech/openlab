<?php

defined( 'ABSPATH' ) or die();

/**
 * Gravity Forms Dropbox API library.
 *
 * @since     2.4
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2019, Rocketgenius
 */
class GF_Dropbox_API {

	/**
	 * The size, in bytes, to trigger a Dropbox upload session.
	 *
	 * @since 2.4
	 * @var   int
	 */
	const UPLOAD_SESSION_THRESHOLD = 8000000;

	/**
	 * The size, in bytes, to split a file into for a Dropbox upload session.
	 *
	 * @since 2.4
	 * @var   int
	 */
	const UPLOAD_SESSION_CHUNK_SIZE = 4000000;

	/**
	 * Base Dropbox API URL.
	 *
	 * @since  2.4
	 * @var    string
	 */
	protected $api_url = 'https://api.dropboxapi.com/2/';

	/**
	 * Dropbox Access Token.
	 *
	 * @since  2.4
	 * @var    string
	 */
	protected $access_token = '';

	/**
	 * Dropbox App Key (for custom apps).
	 *
	 * @since  2.4
	 * @var    string
	 */
	protected $app_key = '';

	/**
	 * Dropbox App Secret (for custom apps).
	 *
	 * @since  2.4
	 * @var    string
	 */
	protected $app_secret = '';

	/**
	 * Initialize Dropbox API library.
	 *
	 * @since  2.4
	 *
	 * @param string $access_token Dropbox Access Token.
	 * @param string $app_key      Dropbox App Key (for custom apps).
	 * @param string $app_secret   Dropbox App Secret (for custom apps).
	 */
	public function __construct( $access_token, $app_key = '', $app_secret = '' ) {

		$this->access_token = $access_token;
		$this->app_key      = $app_key;
		$this->app_secret   = $app_secret;

	}





	// # ACCOUNT -------------------------------------------------------------------------------------------------------

	/**
	 * Get current account information.
	 *
	 * @since  2.4
	 *
	 * @return object
	 * @throws Exception
	 */
	public function get_current_account() {

		return $this->make_request( 'users/get_current_account' );

	}





	// # AUTHENTICATION ------------------------------------------------------------------------------------------------

	/**
	 * Acquire a bearer token once the user has authorized the app.
	 *
	 * @since 2.4
	 *
	 * @param string $code         The code acquired by directing users to Dropbox for authorization.
	 * @param string $redirect_uri Where to redirect the user after authorization has completed.
	 *
	 * @return object|string
	 * @throws Exception
	 */
	public function get_access_token( $code, $redirect_uri ) {

		// Build request arguments.
		$request_args = array(
			'body'    => array(
				'code'         => $code,
				'grant_type'   => 'authorization_code',
				'redirect_uri' => $redirect_uri,
			),
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->app_key . ':' . $this->app_secret ),
				'Content-Type'  => 'application/x-www-form-urlencoded',
			),
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( 'https://api.dropboxapi.com/oauth2/token', $request_args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) >= 400 ) {

			// Decode response.
			$response = wp_remote_retrieve_body( $result );
			$response = gf_dropbox()->is_json( $response ) ? json_decode( $response ) : $response;

			throw new Exception( isset( $response->error_description ) ? esc_html( $response->error_description ) : esc_html( $response ), wp_remote_retrieve_response_code( $result ) );

		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->is_json( $response ) ? json_decode( $response ) : $response;

		return $response;

	}

	/**
	 * Get authorization URL for Dropbox OAuth 2.0 flow.
	 *
	 * @sine 2.4
	 *
	 * @param string $redirect_uri Where to redirect the user after authorization has completed.
	 *
	 * @return string
	 */
	public function get_authorization_url( $redirect_uri ) {

		// Get base authorization URL.
		$auth_url = 'https://www.dropbox.com/oauth2/authorize';

		// Prepare URL params.
		$params = array(
			'client_id'     => $this->app_key,
			'response_type' => 'code',
			'redirect_uri'  => urlencode( $redirect_uri ),
		);

		return add_query_arg( $params, $auth_url );

	}

	/**
	 * Disables the access token used to authenticate the call.
	 *
	 * @since 2.4
	 *
	 * @return object|
	 * @throws Exception
	 */
	public function revoke_token() {

		return $this->make_request( 'auth/token/revoke' );

	}





	// # FILES -------------------------------------------------------------------------------------------------------

	/**
	 * Create a folder at a given path.
	 *
	 * @since  2.4
	 *
	 * @param string $path       Path in the user's Dropbox to create.
	 * @param bool   $autorename If there's a conflict, have the Dropbox server try to autorename the folder to avoid the conflict.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function create_folder( $path, $autorename = false ) {

		// Prepare parameters.
		$params = array(
			'path'       => $path,
			'autorename' => $autorename,
		);

		return $this->make_request( 'files/create_folder_v2', $params );

	}

	/**
	 * Get metadata for a file or folder.
	 *
	 * @since  2.4
	 *
	 * @param string $path                                The path of a file or folder on Dropbox.
	 * @param bool   $include_media_info                  If true, FileMetadata.media_info is set for photo and video.
	 * @param bool   $include_deleted                     If true, DeletedMetadata will be returned for deleted file or folder, otherwise LookupError.not_found will be returned.
	 * @param bool   $include_has_explicit_shared_members If true, the results will include a flag for each file indicating whether or not that file has any explicit members.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function get_metadata( $path, $include_media_info = false, $include_deleted = false, $include_has_explicit_shared_members = false ) {

		// Prepare parameters.
		$params = array(
			'path'                                => $path,
			'include_media_info'                  => $include_media_info,
			'include_deleted'                     => $include_deleted,
			'include_has_explicit_shared_members' => $include_has_explicit_shared_members,
		);

		return $this->make_request( 'files/get_metadata', $params );

	}

	/**
	 * Get contents of a folder.
	 *
	 * @since  2.4
	 *
	 * @param string $path      The path of a file or folder on Dropbox.
	 * @param bool   $recursive If true, the list folder operation will be applied recursively to all subfolders and the response will contain contents of all subfolders.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function list_folder( $path, $recursive = false ) {

		// Prepare parameters.
		$params = array(
			'path'      => $path,
			'recursive' => $recursive,
		);

		// Execute request.
		$response = $this->make_request( 'files/list_folder', $params );

		// If response does not have more entries, return.
		if ( ! $response->has_more ) {
			return $response;
		}

		// Define has more flag and cursor.
		$has_more = $response->has_more;

		// Continue to get contents until all have been returned.
		while ( $has_more ) {

			// Get additional entries.
			$continue_response = $this->list_folder_continue( $response->cursor );

			// Merge entries.
			$response->entries = array_merge( $response->entries, $continue_response->entries );

			// Update has more flag, cursor.
			$has_more         = $response->has_more = $continue_response->has_more;
			$response->cursor = $continue_response->cursor;

		}

		return $response;

	}

	/**
	 * Paginate through all files and retrieve updates to the folder.
	 *
	 * @since  2.4
	 *
	 * @param string $cursor The cursor returned by call to GF_Dropbox_API::list_folder() or GF_Dropbox_API::list_folder_continue().
	 *
	 * @return object
	 * @throws Exception
	 */
	private function list_folder_continue( $cursor ) {

		// Prepare parameters.
		$params = array(
			'cursor' => $cursor,
		);

		return $this->make_request( 'files/list_folder/continue', $params );

	}

	/**
	 * Save the data from a specified URL into a file in user's Dropbox.
	 *
	 * @since 2.4
	 *
	 * @param string $path The path in Dropbox where the URL will be saved to.
	 * @param string $url  The URL to be saved.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function save_url( $path, $url ) {

		// Prepare parameters.
		$params = array(
			'path' => $path,
			'url'  => $url,
		);

		// Execute request.
		$response = $this->make_request( 'files/save_url', $params );

		// If URL is saved, return.
		if ( isset( $response->{'.tag'} ) && 'complete' === $response->{'.tag'} ) {
			return $response;
		}

		// Get async job ID and set current status.
		$async_job_id = $response->async_job_id;
		$status       = 'in_progress';

		// Continue to poll for updates.
		while ( 'failed' !== $status && ! isset( $response->name ) ) {

			// Wait before getting update.
			sleep( 2 );

			// Get update.
			$response = $this->save_url_status( $async_job_id );

			// Get status.
			$status = isset( $response->{'.tag'} ) ? $response->{'.tag'} : 'in_progress';

		}

		return $response;

	}

	/**
	 * Check the status of a GF_Dropbox_API::save_url() job.
	 *
	 * @since 2.4
	 *
	 * @param string $async_job_id Id of the asynchronous job.
	 *
	 * @return object
	 * @throws Exception
	 */
	private function save_url_status( $async_job_id ) {

		// Prepare parameters.
		$params = array(
			'async_job_id' => $async_job_id,
		);

		return $this->make_request( 'files/save_url/check_job_status', $params );

	}

	/**
	 * Create a new file with the contents provided in the request.
	 *
	 * @since 2.4
	 *
	 * @param string $file   Path to file to upload.
	 * @param string $path   Destination path.
	 * @param array  $params Additional parameters.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function upload( $file, $path, $params = null ) {

		// Get size of file.
		$file_size = filesize( $file );

		// If file is above the threshold for a simple upload, start an upload session.
		if ( $file_size > self::UPLOAD_SESSION_THRESHOLD ) {
			return $this->upload_chunked( $file, $path, $params );
		}

		return $this->upload_simple( $file, $path, $params );

	}





	// # FILE UPLOADING ------------------------------------------------------------------------------------------------

	/**
	 * Upload file to Dropbox via an upload session.
	 *
	 * @since 2.4
	 *
	 * @param string $file   Path to file to upload.
	 * @param string $path   Destination path.
	 * @param array  $params Additional parameters.
	 *
	 * @return object
	 * @throws Exception
	 */
	private function upload_chunked( $file, $path, $params = null ) {

		// Get size of file.
		$file_size = filesize( $file );

		// Get session ID.
		$session_id = $this->upload_session_start( $file );

		// If upload session could not be started, throw exception.
		if ( ! $session_id ) {
			throw new Exception( 'Unable to start upload session for ' . basename( $file ) );
		}

		// Calculate uploaded and remaining sizes.
		$uploaded  = self::UPLOAD_SESSION_CHUNK_SIZE;
		$remaining = $file_size - self::UPLOAD_SESSION_CHUNK_SIZE;

		// Continue to upload chunks of the file until whole file is uploaded.
		while ( $remaining > self::UPLOAD_SESSION_CHUNK_SIZE ) {

			// Append the next chunk to the upload session.
			$session_id = $this->upload_session_append( $session_id, $file, $uploaded );

			// Calculate uploaded and remaining sizes.
			$uploaded  += self::UPLOAD_SESSION_CHUNK_SIZE;
			$remaining -= self::UPLOAD_SESSION_CHUNK_SIZE;

		}

		// Finish upload session.
		return $this->upload_session_finish( $session_id, $file, $uploaded, $remaining, $path, $params );

	}

	/**
	 * Append data to Dropbox upload session.
	 *
	 * @since 2.4
	 *
	 * @param string $session_id Upload session ID.
	 * @param string $file       Path to file to upload.
	 * @param int    $uploaded   The amount of data, in bytes, uploaded so far.
	 *
	 * @return string
	 * @throws Exception
	 */
	private function upload_session_append( $session_id, $file, $uploaded ) {

		// Get file contents.
		$fstream = fopen( $file, 'r' );
		fseek( $fstream, $uploaded );
		$fdata = fread( $fstream, self::UPLOAD_SESSION_CHUNK_SIZE );

		// Prepare parameters.
		$params = array(
			'close'  => false,
			'cursor' => array(
				'session_id' => $session_id,
				'offset'     => $uploaded,
			),
		);

		// Build request arguments.
		$args = array(
			'body'    => $fdata,
			'headers' => array(
				'Authorization'   => 'Bearer ' . $this->access_token,
				'Content-Type'    => 'application/octet-stream',
				'Dropbox-API-Arg' => json_encode( $params ),
			),
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( 'https://content.dropboxapi.com/2/files/upload_session/append_v2', $args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) >= 400 ) {
			throw new Exception( wp_remote_retrieve_body( $result ), wp_remote_retrieve_response_code( $result ) );
		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->maybe_decode_json( $response );

		// If error was found, throw exception.
		if ( rgar( $response, 'error' ) ) {
			throw new Exception( $response['error_summary'] );
		}

		return $session_id;

	}

	/**
	 * Finish Dropbox upload session.
	 *
	 * @since 2.4
	 *
	 * @param string $session_id Upload session ID.
	 * @param string $file       Path to file to upload.
	 * @param int    $uploaded   The amount of data, in bytes, uploaded so far.
	 * @param int    $remaining  THe amount of data, in bytes, to upload.
	 * @param string $path       Destination path.
	 * @param array  $params     Additional parameters.
	 *
	 * @return object|string
	 * @throws Exception
	 */
	private function upload_session_finish( $session_id, $file, $uploaded, $remaining, $path, $params = null ) {

		// Get file contents.
		$fstream = fopen( $file, 'r' );
		fseek( $fstream, $uploaded );
		$fdata = fread( $fstream, $remaining );

		// Prepare parameters.
		$request_params = array(
			'cursor' => array(
				'session_id' => $session_id,
				'offset'     => $uploaded,
			),
			'commit' => array(
				'path' => $path,
			),
		);

		// If additional parameters are defined, add them.
		if ( ! empty( $params ) && is_array( $params ) ) {
			$request_params['commit'] = array_merge( $request_params['commit'], $params );
		}

		// Build request arguments.
		$args = array(
			'body'    => $fdata,
			'headers' => array(
				'Authorization'   => 'Bearer ' . $this->access_token,
				'Content-Type'    => 'application/octet-stream',
				'Dropbox-API-Arg' => json_encode( $request_params ),
			),
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( 'https://content.dropboxapi.com/2/files/upload_session/finish', $args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) >= 400 ) {
			throw new Exception( wp_remote_retrieve_body( $result ), wp_remote_retrieve_response_code( $result ) );
		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->is_json( $response ) ? json_decode( $response ) : $response;

		// If error was found, throw exception.
		if ( is_object( $response ) && isset( $response->error_summary ) ) {
			throw new Exception( $response->error_summary );
		}

		return $response;

	}

	/**
	 * Begin Dropbox upload session.
	 *
	 * @since 2.4
	 *
	 * @param string $file   Path to file to upload.
	 *
	 * @return string|bool
	 * @throws Exception
	 */
	private function upload_session_start( $file ) {

		// Get file contents.
		$fstream = fopen( $file, 'r' );
		$fdata   = fread( $fstream, self::UPLOAD_SESSION_CHUNK_SIZE );

		// Build request arguments.
		$args = array(
			'body'    => $fdata,
			'headers' => array(
				'Authorization'   => 'Bearer ' . $this->access_token,
				'Content-Type'    => 'application/octet-stream',
				'Dropbox-API-Arg' => json_encode( array( 'close' => false ) ),
			),
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( 'https://content.dropboxapi.com/2/files/upload_session/start', $args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) >= 400 ) {
			throw new Exception( wp_remote_retrieve_body( $result ), wp_remote_retrieve_response_code( $result ) );
		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->maybe_decode_json( $response );

		return rgar( $response, 'session_id' ) ? $response['session_id'] : false;

	}

	/**
	 * Upload file directly to Dropbox.
	 *
	 * @since 2.4
	 *
	 * @param string $file   Path to file to upload.
	 * @param string $path   Destination path.
	 * @param array  $params Additional parameters.
	 *
	 * @return object
	 * @throws Exception
	 */
	private function upload_simple( $file, $path, $params = null ) {

		// Prepare parameters.
		$args = array( 'path' => $path );

		// Add additional params.
		if ( ! empty( $params ) && is_array( $params ) ) {
			$args = array_merge( $args, $params );
		}

		// Get file contents.
		$fstream = fopen( $file, 'r' );
		$fsize   = filesize( $file );
		$fdata   = fread( $fstream, $fsize );

		// Build request arguments.
		$request_args = array(
			'body'    => $fdata,
			'headers' => array(
				'Authorization'   => 'Bearer ' . $this->access_token,
				'Content-Type'    => 'application/octet-stream',
				'Dropbox-API-Arg' => json_encode( $args ),
			),
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( 'https://content.dropboxapi.com/2/files/upload', $request_args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) >= 400 ) {
			throw new Exception( wp_remote_retrieve_body( $result ), wp_remote_retrieve_response_code( $result ) );
		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->is_json( $response ) ? json_decode( $response ) : $response;

		return $response;

	}





	// # SHARING -------------------------------------------------------------------------------------------------------

	/**
	 * Create a shared link with custom settings.
	 * (If no settings are given then the default visibility is RequestedVisibility.public)
	 *
	 * @since 2.4
	 *
	 * @param string $path     The path to be shared by the shared link.
	 * @param array  $settings The requested settings for the newly created shared link.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function create_shared_link_with_settings( $path, $settings = null ) {

		// Prepare parameters.
		$params = array(
			'path'     => $path,
			'settings' => $settings,
		);

		return $this->make_request( 'sharing/create_shared_link_with_settings', $params );

	}

	/**
	 * List shared links of this user.
	 *    If no path is given, returns a list of all shared links for the current user.
	 *    If a non-empty path is given, returns a list of all shared links that allow access
	 *    to the given path - direct links to the given path and links to parent folders of the given path.
	 *    Links to parent folders can be suppressed by setting direct_only to true.
	 *
	 * @since 2.4
	 *
	 * @param string $path        The path to get the shared link for.
	 * @param string $cursor      The cursor returned by GF_Dropbox_API::list_shared_links().
	 * @param bool   $direct_only Return direct link to path.
	 *
	 * @return object
	 * @throws Exception
	 */
	public function list_shared_links( $path = null, $cursor = null, $direct_only = null ) {

		// Prepare parameters.
		$params = array(
			'path'        => $path,
			'cursor'      => $cursor,
			'direct_only' => $direct_only,
		);

		return $this->make_request( 'sharing/list_shared_links', $params );

	}





	// # REQUEST METHODS -----------------------------------------------------------------------------------------------

	/**
	 * Make API request.
	 *
	 * @since  2.4
	 *
	 * @param string $action  Request action.
	 * @param array  $params  Request params.
	 *
	 * @return object|string
	 * @throws Exception
	 */
	private function make_request( $action, $params = null ) {

		// Build request URL.
		$request_url = $this->api_url . $action;

		// Build request headers.
		$headers = array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->access_token,
		);

		// Build request arguments.
		$args = array(
			'body'    => json_encode( $params ),
			'headers' => $headers,
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( $request_url, $args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) >= 400 ) {
			throw new Exception( wp_remote_retrieve_body( $result ), wp_remote_retrieve_response_code( $result ) );
		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->is_json( $response ) ? json_decode( $response ) : $response;

		return $response;

	}

}
