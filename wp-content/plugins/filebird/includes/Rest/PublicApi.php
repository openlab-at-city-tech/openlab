<?php
namespace FileBird\Rest;

defined( 'ABSPATH' ) || exit;

use FileBird\Controller\Api;

class PublicApi {
	private $controller;

	public function register_rest_routes() {
		$this->controller = new Api();

        register_rest_route(
			NJFB_REST_URL,
			'fbv-api',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'restApi' ),
				'permission_callback' => array( $this, 'admin_permission_callback' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/folders
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folders',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this->controller, 'publicRestApiGetFolders' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/post-type-folders/?post_type=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'post-type-folders',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this->controller, 'publicRestApiGetPostTypeFolders' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/post-type-folder/posts/?post_type=&folder_id=&per_page=&page=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'post-type-folder/posts',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this->controller, 'publicRestApiGetPostTypeFolderPosts' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/post-type-folders
		//post_type=&title=&parent=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'post-type-folders',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'publicRestApiNewPostTypeFolder' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/post-type-folder/update
		//post_type=&id=&title=&parent=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'post-type-folder/update',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'publicRestApiUpdatePostTypeFolder' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/post-type-folder/delete
		//post_type=&ids=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'post-type-folder/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'publicRestApiDeletePostTypeFolder' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/post-type-folder/set-posts
		//post_type=&folderId=&ids=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'post-type-folder/set-posts',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'publicRestApiSetPostTypeFolder' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/folder/?folder_id=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folder',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this->controller, 'publicRestApiGetFolderDetail' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/folder/set-attachment
		//ids=&folder=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folder/set-attachment',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'publicRestApiSetAttachment' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/attachment-id/?folder_id=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'attachment-id',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this->controller, 'publicRestApiGetAttachmentIds' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/attachment-count/?folder_id=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'attachment-count',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this->controller, 'publicRestApiGetAttachmentCount' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/folders
		//parent_id=&name=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folders',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this->controller, 'publicRestApiNewFolder' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
	}

    private function getAuthorizationHeader() {
		$headers = null;
		if ( isset( $_SERVER['Authorization'] ) ) {
			$headers = trim( $_SERVER['Authorization'] );
		} elseif ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) { //Nginx or fast CGI
			$headers = trim( $_SERVER['HTTP_AUTHORIZATION'] );
		} elseif ( function_exists( 'apache_request_headers' ) ) {
			$requestHeaders = apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine( array_map( 'ucwords', array_keys( $requestHeaders ) ), array_values( $requestHeaders ) );
			//print_r($requestHeaders);
			if ( isset( $requestHeaders['Authorization'] ) ) {
				$headers = trim( $requestHeaders['Authorization'] );
			}
		}
		return $headers;
	}

    private function getBearerToken() {
		$token   = null;
		$headers = $this->getAuthorizationHeader();
		// The key is only ever read from the Authorization header. Accepting it
		// from the query string leaks it into proxy, CDN and access logs.
		if ( ! empty( $headers ) ) {
			if ( preg_match( '/Bearer\s(\S+)/', $headers, $matches ) ) {
				$token = $matches[1];
			}
		}
		return $token;
	}

	public function admin_permission_callback() {
		return current_user_can( 'upload_files' ) && current_user_can( 'manage_options' );
	}

	public function permission_callback( $request ) {
		$key = get_option( 'fbv_rest_api_key', '' );

		if ( \strlen( $key ) !== 40 ) {
			return false;
		}

		$token = $this->getBearerToken();

		return is_string( $token ) && hash_equals( $key, $token );
	}
}