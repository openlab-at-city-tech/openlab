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
	 * The API header key required to select a particular user from a team account.
	 *
	 * @since 2.9
	 * @var string
	 */
	const HEADER_KEY_SELECT_USER = 'Dropbox-API-Select-User';

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
	 * Dropbox refresh token.
	 *
	 * @since 3.0
	 * @var string
	 */
	protected $refresh_token = '';

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
	 * Team member ID if the current account is a team account.
	 *
	 * @since  3.0
	 */
	private $team_member_id = null;

	/**
	 * The root namespace ID for the authenticated user if the current account is a team account.
	 *
	 * @since  3.0
	 */
	private $root_namespace_id = '';

	/**
	 * Initialize Dropbox API library.
	 *
	 * @since  2.4
	 * @since  3.0 Renamed $access_token parameter to $settings to add support for refresh tokens.
	 *
	 * @param array|string $settings   Add-on settings.
	 * @param string       $app_key    Dropbox App Key (for custom apps).
	 * @param string       $app_secret Dropbox App Secret (for custom apps).
	 */
	public function __construct( $settings, $app_key = '', $app_secret = '' ) {
		$this->access_token  = rgar( $settings, 'accessToken', $settings );
		$this->refresh_token = rgar( $settings, 'refresh_token', '' );
		$this->app_key       = $app_key;
		$this->app_secret    = $app_secret;
	}

	/**
	 * Setter for access_token property.
	 *
	 * @param string $access_token Dropbox access_token.
	 */
	public function set_access_token( $access_token ) {
		$this->access_token = $access_token;
	}


	// # ACCOUNT -------------------------------------------------------------------------------------------------------

	/**
	 * Get current account information.
	 *
	 * @since  2.4
	 *
	 * @param bool $team_account Whether the account to retrieve is a team account.
	 *
	 * @throws Exception If the account request could not be made.
	 * @return object
	 */
	public function get_current_account( $team_account = false ) {
		try {
			if ( ! $team_account ) {
				return $this->make_request( 'users/get_current_account' );
			}

			$this->team_member_id = $this->get_authenticated_admin()->admin_profile->team_member_id;

			$response = $this->make_request(
				'users/get_current_account',
				null,
				array( self::HEADER_KEY_SELECT_USER => $this->team_member_id )
			);

			if ( $response->root_info->root_namespace_id ) {
				$this->root_namespace_id = $response->root_info->root_namespace_id;
			}

			return $response;

		} catch ( Exception $e ) {
			return $e->getCode() === 400 && ! $team_account ? $this->get_current_account( true ) : $e;
		}
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

			throw new Exception(
				isset( $response->error )
					? esc_html( $response->error )
					: esc_html__( 'Something went wrong trying to retrieve an access token from Dropbox.', 'gravityformsdropbox' ),
				wp_remote_retrieve_response_code( $result )
			);
		}

		// Decode response.
		$response = wp_remote_retrieve_body( $result );
		$response = gf_dropbox()->is_json( $response ) ? json_decode( $response ) : $response;

		return $response;

	}

	/**
	 * Get the authenticated admin details for Dropbox OAuth 2.0 flow.
	 *
	 * This method facilitates connecting a Dropbox team account to Gravity Forms.
	 *
	 * @since 2.9
	 *
	 * @throws Exception
	 * @return object|string
	 */
	public function get_authenticated_admin() {
		return $this->make_request( 'team/token/get_authenticated_admin' );
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
			'client_id'         => $this->app_key,
			'response_type'     => 'code',
			'redirect_uri'      => urlencode( $redirect_uri ),
			'token_access_type' => 'offline',
		);

		return add_query_arg( $params, $auth_url );

	}

	/**
	 * Request a new refresh token from Dropbox via the Gravity API.
	 *
	 * @since 3.0
	 *
	 * @param GF_Dropbox $addon    Instance of the add-on class.
	 * @param array      $settings Array of plugin settings.
	 *
	 * @return array|WP_Error
	 */
	public function refresh_access_token( $addon, $settings ) {
		if ( rgar( $settings, 'customAppEnable' ) ) {
			return $this->refresh_custom_app_access_token( $settings );
		}
		return wp_remote_post(
			$addon->get_gravity_api_url( '/auth/dropbox/refresh' ),
			array(
				'method'    => 'POST',
				'sslverify' => false,
				'body'      => array(
					'refresh_token' => rgar( $settings, 'refresh_token' ),
				),
			)
		);
	}

	/**
	 * Request a new refresh token from Dropbox directly using the custom app key and secret.
	 *
	 * @since 3.0
	 * @param array $settings Array of plugin settings.
	 *
	 * @return array|WP_Error
	 */
	private function refresh_custom_app_access_token( $settings ) {
		return wp_remote_post(
			'https://api.dropbox.com/oauth2/token',
			array(
				'method'  => 'POST',
				'body'    => array(
					'grant_type'    => 'refresh_token',
					'refresh_token' => rgar( $settings, 'refresh_token' ),
				),
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( rgar( $settings, 'customAppKey' ) . ':' . rgar( $settings, 'customAppSecret' ) ),
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
			)
		);
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
		$headers = array();

		if ( $this->team_member_id ) {
			$headers[ self::HEADER_KEY_SELECT_USER ] = $this->team_member_id;
		}

		return $this->make_request( 'auth/token/revoke', null, $headers );
	}

	/**
	 * Verify the app key and secret are valid.
	 * Unlike is_valid_app_key_secret() this will check both the app key and secret.
	 *
	 * @since 3.0
	 *
	 * @param string $customAppKey    The custom app key.
	 * @param string $customAppSecret The custom app settings.
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function verify_custom_app_key_and_secret( $customAppKey, $customAppSecret ) {

		// Build request arguments.
		$request_args = array(
			'body'    => wp_json_encode( array( 'query' => 'verify' ) ),
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $customAppKey . ':' . $customAppSecret ),
				'Content-Type'  => 'application/json',
			),
			'method'  => 'POST',
			'timeout' => 120,
		);

		// Execute request.
		$result = wp_remote_request( 'https://api.dropboxapi.com/2/check/app', $request_args );

		// If response is an error, throw exception.
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), $result->get_error_code() );
		}

		// If an error status code was returned, throw exception.
		if ( wp_remote_retrieve_response_code( $result ) > 200 ) {

			return false;
		}

		return true;

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

		$headers = $this->maybe_get_team_accout_headers();

		return $this->make_request( 'files/get_metadata', $params, $headers );
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

		$headers = $this->maybe_get_team_accout_headers();

		// Execute request.
		$response = $this->make_request( 'files/list_folder', $params, $headers );

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
	 * Get the headers required to make a request to a business or team API endpoint
	 *
	 * @since 3.0
	 *
	 * @return array
	 **/
	private function maybe_get_team_accout_headers( $headers = array() ) {

		if ( $this->team_member_id ) {
			$headers[ self::HEADER_KEY_SELECT_USER ] = $this->team_member_id;
			$headers['Dropbox-API-Path-Root']        = sprintf( '{".tag": "namespace_id", "namespace_id": "%s"}', $this->root_namespace_id );
		}

		return $headers;
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
	public function upload( $file, $path, $params = null, $headers = array() ) {

		$headers = $this->maybe_get_team_accout_headers( $headers );

		// Get size of file.
		$file_size = filesize( $file );

		// If file is above the threshold for a simple upload, start an upload session.
		if ( $file_size > self::UPLOAD_SESSION_THRESHOLD ) {
			return $this->upload_chunked( $file, $path, $params, $headers );
		}

		return $this->upload_simple( $file, $path, $params, $headers );

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
	private function upload_chunked( $file, $path, $params = null, $additional_headers = array() ) {

		// Get size of file.
		$file_size = filesize( $file );

		// Get session ID.
		$session_id = $this->upload_session_start( $file, $additional_headers );

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
			$session_id = $this->upload_session_append( $session_id, $file, $uploaded, $additional_headers );

			// Calculate uploaded and remaining sizes.
			$uploaded  += self::UPLOAD_SESSION_CHUNK_SIZE;
			$remaining -= self::UPLOAD_SESSION_CHUNK_SIZE;

		}

		// Finish upload session.
		return $this->upload_session_finish( $session_id, $file, $uploaded, $remaining, $path, $params, $additional_headers );

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
	private function upload_session_append( $session_id, $file, $uploaded, $additional_headers = array() ) {

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
			'headers' => array_merge(
				array(
					'Authorization'   => 'Bearer ' . $this->access_token,
					'Content-Type'    => 'application/octet-stream',
					'Dropbox-API-Arg' => json_encode( $params ),
				),
				$additional_headers
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
	private function upload_session_finish( $session_id, $file, $uploaded, $remaining, $path, $params = null, $additional_headers = array() ) {

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
			'headers' => array_merge(
				array(
					'Authorization'   => 'Bearer ' . $this->access_token,
					'Content-Type'    => 'application/octet-stream',
					'Dropbox-API-Arg' => json_encode( $request_params ),
				),
				$additional_headers
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
	private function upload_session_start( $file, $additional_headers = array() ) {

		// Get file contents.
		$fstream = fopen( $file, 'r' );
		$fdata   = fread( $fstream, self::UPLOAD_SESSION_CHUNK_SIZE );

		// Build request arguments.
		$args = array(
			'body'    => $fdata,
			'headers' => array_merge(
				array(
					'Authorization'   => 'Bearer ' . $this->access_token,
					'Content-Type'    => 'application/octet-stream',
					'Dropbox-API-Arg' => json_encode( array( 'close' => false ) ),
				),
				$additional_headers
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
	private function upload_simple( $file, $path, $params = null, $additional_headers = array() ) {

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
			'headers' => array_merge(
				array(
					'Authorization'   => 'Bearer ' . $this->access_token,
					'Content-Type'    => 'application/octet-stream',
					'Dropbox-API-Arg' => json_encode( $args ),
				),
				$additional_headers
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

		$headers = $this->maybe_get_team_accout_headers();

		return $this->make_request( 'sharing/create_shared_link_with_settings', $params, $headers );

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
	 * @param array  $additional_headers Additional request headers.
	 *
	 * @return object|string
	 * @throws Exception
	 */
	private function make_request( $action, $params = null, $additional_headers = array() ) {

		// Build request URL.
		$request_url = $this->api_url . $action;

		// Build request headers.
		$headers = array_merge(
			array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->access_token,
			),
			$additional_headers
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
