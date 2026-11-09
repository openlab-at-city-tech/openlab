<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! class_exists( 'DLM_Post_Type_Manager' ) ) {

	/**
	 * Class DLM_Post_Type_Manager
	 *
	 * Class used to handle the Custom Post Types
	 */
	class DLM_Post_Type_Manager {

		/**
		 * Setup hooks
		 */
		public function setup() {
			add_action( 'rest_api_init',
				array( $this, 'register_dlm_download_post_meta_rest' ) );
			add_action( 'init', array( $this, 'register' ), 10 );

			add_action( 'current_screen', array( $this, 'disable_geditor' ) );
			// Action to do when a post is deleted.
			add_action( 'before_delete_post', array( $this, 'delete_post' ), 15,
				2 );
			// Route to custom list table for the Downloads CPT.
			add_filter( 'wp_list_table_class_name', array( $this, 'custom_list_table' ), 15, 2 );
		}

		/**
		 * Custom Admin List Table for the Downloads CPT. Used to improve performance.
		 *
		 * @param  string  $class_name  The class name of the list table.
		 * @array $args  The arguments passed to the filter.
		 *
		 * @return string
		 * @since 5.0.0
		 */
		public function custom_list_table( $class_name, $args ) {
			if ( 'dlm_download' === $args['screen']->post_type && 'edit' === $args['screen']->base ) {
				$class_name = 'DLM_Admin_List_Table';
			}

			return $class_name;
		}

		/**
		 * Register Post Types
		 */
		public function register() {

			// Register Download Post Type
			register_post_type( "dlm_download",
				apply_filters( 'dlm_cpt_dlm_download_args', array(
					'labels'              => array(
						'all_items'          => __( 'All Downloads',
							'download-monitor' ),
						'name'               => __( 'Downloads',
							'download-monitor' ),
						'singular_name'      => __( 'Download',
							'download-monitor' ),
						'add_new'            => __( 'Add New',
							'download-monitor' ),
						'add_new_item'       => __( 'Add Download',
							'download-monitor' ),
						'edit'               => __( 'Edit',
							'download-monitor' ),
						'edit_item'          => __( 'Edit Download',
							'download-monitor' ),
						'new_item'           => __( 'New Download',
							'download-monitor' ),
						'view'               => __( 'View Download',
							'download-monitor' ),
						'view_item'          => __( 'View Download',
							'download-monitor' ),
						'search_items'       => __( 'Search Downloads',
							'download-monitor' ),
						'not_found'          => __( 'No Downloads found',
							'download-monitor' ),
						'not_found_in_trash' => __( 'No Downloads found in trash',
							'download-monitor' ),
						'parent'             => __( 'Parent Download',
							'download-monitor' ),
					),
					'description'         => __( 'This is where you can create and manage downloads for your site.',
						'download-monitor' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'capabilities'        => array(
						'publish_posts'       => 'manage_downloads',
						'edit_posts'          => 'manage_downloads',
						'edit_others_posts'   => 'manage_downloads',
						'delete_posts'        => 'manage_downloads',
						'delete_others_posts' => 'manage_downloads',
						'read_private_posts'  => 'manage_downloads',
						'edit_post'           => 'manage_downloads',
						'delete_post'         => 'manage_downloads',
						'read_post'           => 'manage_downloads',
					),
					'publicly_queryable'  => true,
					'exclude_from_search' => ( 1
					                           !== absint( get_option( 'dlm_wp_search_enabled',
							0 ) ) ),
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => apply_filters( 'dlm_cpt_dlm_download_supports',
						array(
							'title',
							'editor',
							'excerpt',
							'thumbnail',
							'custom-fields',
						) ),
					'has_archive'         => false,
					'show_in_nav_menus'   => false,
					'menu_position'       => 35,
					'show_in_rest'        => true,
					'menu_icon'           => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iNjQuMDAwMDAwcHQiIGhlaWdodD0iNjQuMDAwMDAwcHQiIHZpZXdCb3g9IjAgMCA2NC4wMDAwMDAgNjQuMDAwMDAwIgogcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCI+Cgo8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjAwMDAwMCw2NC4wMDAwMDApIHNjYWxlKDAuMTAwMDAwLC0wLjEwMDAwMCkiCmZpbGw9IiNmZmZmZmYiIHN0cm9rZT0ibm9uZSI+CjxwYXRoIGQ9Ik0yMjYgNjIzIGMtMTAxIC0zMSAtMTgzIC0xMTEgLTIxMSAtMjA3IC03MiAtMjQwIDE0MiAtNDY2IDM4MCAtNDA0CjczIDIwIDEzMyA1NiAxNjggMTAyIDk0IDEyMiAxMDAgMjYzIDE2IDM4OSAtNzMgMTExIC0yMjAgMTYxIC0zNTMgMTIweiBtMTQ0Ci0xNTggYzAgLTExIC0xMiAtMTUgLTUwIC0xNSAtMzggMCAtNTAgNCAtNTAgMTUgMCAxMSAxMiAxNSA1MCAxNSAzOCAwIDUwIC00CjUwIC0xNXogbTAgLTUwIGMwIC0xMSAtMTIgLTE1IC01MCAtMTUgLTM4IDAgLTUwIDQgLTUwIDE1IDAgMTEgMTIgMTUgNTAgMTUKMzggMCA1MCAtNCA1MCAtMTV6IG0yIC04MiBjMyAtMzcgNCAtMzggNTEgLTQzIGw0OCAtNSAtNzYgLTc1IC03NSAtNzQgLTc1IDc0Ci03NiA3NSA0OCA1IGM0NyA1IDQ4IDYgNTEgNDMgbDMgMzcgNDkgMCA0OSAwIDMgLTM3eiIvPgo8L2c+Cjwvc3ZnPgo=',
				) )
			);

			// Register Download Version Post Type
			register_post_type( "dlm_download_version",
				apply_filters( 'dlm_cpt_dlm_download_version_args', array(
					'labels'              => array(
						'all_items'          => __( 'All Download Versions',
							'download-monitor' ),
						'name'               => __( 'Download Versions',
							'download-monitor' ),
						'singular_name'      => __( 'Download Version',
							'download-monitor' ),
						'add_new'            => __( 'Add New',
							'download-monitor' ),
						'add_new_item'       => __( 'Add Download Version',
							'download-monitor' ),
						'edit'               => __( 'Edit',
							'download-monitor' ),
						'edit_item'          => __( 'Edit Download Version',
							'download-monitor' ),
						'new_item'           => __( 'New Download Version',
							'download-monitor' ),
						'view'               => __( 'View Download Version',
							'download-monitor' ),
						'view_item'          => __( 'View Download Version',
							'download-monitor' ),
						'search_items'       => __( 'Search Download Versions',
							'download-monitor' ),
						'not_found'          => __( 'No Download Versions found',
							'download-monitor' ),
						'not_found_in_trash' => __( 'No Download Versions found in trash',
							'download-monitor' ),
						'parent'             => __( 'Parent Download Version',
							'download-monitor' ),
					),
					'public'              => false,
					'show_ui'             => false,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'show_in_nav_menus'   => false,
				) )
			);

			/**
			 * Hook for DLM CPT register
			 *
			 * @hooked WPChill\DownloadMonitor\Shop\Util\PostType setup() - 10
			 */
			do_action( 'dlm_after_post_type_register' );
		}

		/**
		 * Register Download meta fields for REST API
		 */
		public function register_dlm_download_post_meta_rest() {
			// Register the featured meta field.
			register_rest_field( 'dlm_download', 'featured', array(
				'get_callback' => function ( $post_arr ) {
					return get_post_meta( $post_arr['id'], '_featured', true );

				},
			) );
			// @todo: Remove this in the future as the download count is now based on the download log.
			// Register the download count meta field.
			register_rest_field( 'dlm_download', 'download_count', array(
				'get_callback' => function ( $post_arr ) {
					return get_post_meta( $post_arr['id'], '_download_count',
						true );

				},
			) );
			// Register the download count meta field.
			register_rest_field( 'dlm_download', 'author', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_author_meta( 'nickname',
						$post_arr['author'] );
				},
			) );
		}

		/**
		 * Explicitely disable the gutenberg editor for downloads
		 * This is needed because the download edit page is not compatible with the gutenberg editor
		 */
		public function disable_geditor() {

			$screen = get_current_screen();
			if ( $screen->post_type == 'dlm_download' ) {
				// Disable gutenberg editor for Downloads
				add_filter( 'use_block_editor_for_post_type', '__return_false',
					100 );
			}
		}

		/**
		 * Actions to do when a version is deleted.
		 *
		 * @param  int  $id  The ID of the Version.
		 *
		 * @return void
		 * @since 4.7.72
		 */
		public function delete_files( $id ) {

			$version = download_monitor()->service( 'version_repository' )
			                             ->retrieve_single( $id );
			$version->delete_files();
		}

		/**
		 * Action to do when a Download or Version is deleted.
		 *
		 * @param  int  $id  The ID of the post.
		 * @param  object  $post  Post object.
		 *
		 * @return void
		 * @since 4.7.72
		 */
		public function delete_post( $id, $post ) {

			// Don't do anything if the post is not a download or version.
			if ( 'dlm_download' !== $post->post_type
			     && 'dlm_download_version' !== $post->post_type
			) {
				return;
			}
			// User needs to set this in order to delete the files to true. Defaults to false.
			if ( ! apply_filters( 'dlm_delete_files', false ) ) {
				return;
			}
			// Delete files in Versions.
			if ( 'dlm_download_version' === $post->post_type ) {
				$this->delete_files( $id );
			}
			// Delete files in all versions from a Download.
			if ( 'dlm_download' === $post->post_type ) {

				$download = download_monitor()->service( 'download_repository' )
				                              ->retrieve(
					                              array(
						                              'p'           => absint( $id ),
						                              'post_status' => array(
							                              'publish',
							                              'future',
							                              'trash',
							                              'draft',
							                              'inherit',
						                              ),
					                              )
				                              );

				// The retrieved download is an array of downloads. We only need the first and only one, as it's a query
				// based on ID.
				if ( ! empty( $download ) ) {
					$download  = reset($download);
				}

				$versions = $download->get_versions();
				if ( ! empty( $versions ) ) {
					foreach ( $versions as $version ) {
						$this->delete_files( $version->get_id() );
					}
				}
			}
		}
	}
}
