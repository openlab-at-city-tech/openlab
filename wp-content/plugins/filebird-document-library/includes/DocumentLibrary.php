<?php

namespace FileBird\Blocks;

defined( 'ABSPATH' ) || exit;

use FileBird\Controller\Attachment\SizeMeta;
use FileBird\Classes\Attachment\AttachmentSize;

class DocumentLibrary extends AbstractBlock {
	protected $block_name = 'document-library';

	public function __construct() {
		parent::__construct();
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'fbdl_enqueue_frontend', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		
		add_filter( 'as3cf_object_meta', array( $this, 'force_download_as3cf' ), 10, 4 );
	}

	public function plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'filebird-document-library.php' ) !== false ) {
			$new_links = array(
				'doc'     => '<a href="https://ninjateam.gitbook.io/filebird/filebird-document-library" target="_blank">' . __( 'Documentation', 'filebird-dl' ) . '</a>',
				'support' => '<a href=https://ninjateam.org/support/" target="_blank">' . __( 'Support', 'filebird-dl' ) . '</a>',
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}

	protected function get_block_attributes() {
		return array(
			'layout'  => array(
				'type'    => 'string',
				'default' => 'grid',
			),
			'title'   => array(
				'type'    => 'string',
				'default' => '',
			),
			'column'  => array(
				'type'    => 'number',
				'default' => 4,
			),
			'request' => array(
				'type'    => 'object',
				'default' => array(
					'pagination'     => array(
						'current' => 2,
						'limit'   => 10,
					),
					'search'         => '',
					'orderBy'        => 'post_title',
					'orderType'      => 'DESC',
					'selectedFolder' => array(),
				),
			),
		);
	}

	protected function get_files_icon() {
		return array(
			// Images
			'jpg'    => 'image.svg',
			'jpeg'   => 'image.svg',
			'png'    => 'image.svg',
			'gif'    => 'image.svg',
			'webp'   => 'image.svg',
			'svg'    => 'image.svg',
			// Audio
			'mp3'    => 'audio.svg',
			'm4a'    => 'audio.svg',
			'ogg'    => 'audio.svg',
			'wav'    => 'audio.svg',
			// Video
			'mp4'    => 'video.svg',
			'm4v'    => 'video.svg',
			'mov'    => 'video.svg',
			'wmv'    => 'video.svg',
			'avi'    => 'video.svg',
			'mpg'    => 'video.svg',
			'ogv'    => 'video.svg',
			'3gp'    => 'video.svg',
			'3g2'    => 'video.svg',
			'vtt'    => 'video.svg',
			// Document
			'pdf'    => 'pdf.svg',
			'doc'    => 'docx.svg',
			'docx'   => 'docx.svg',
			'odt'    => 'docx.svg',
			'xls'    => 'xls.svg',
			'xlsx'   => 'xls.svg',
			'key'    => 'pptx.svg',
			'ppt'    => 'pptx.svg',
			'pptx'   => 'pptx.svg',
			'pps'    => 'pptx.svg',
			'ppsx'   => 'pptx.svg',
			// Zip
			'zip'    => 'zip.svg',
			// No Support
			'no_ext' => 'no-ext.svg',
		);
	}

	protected function register_block_editor_script() {
		wp_register_script(
			$this->namespace . '-' . $this->block_name . '-js',
			FBV_DL_URL . 'blocks/dist/index.js',
			$this->get_editor_dependencies(),
			FBV_DL_VERSION,
			true
		);

		wp_set_script_translations( $this->namespace . '-' . $this->block_name . '-js', 'filebird-dl', FBV_DL_DIR . '/languages/' );

		wp_register_style(
			$this->namespace . '-' . $this->block_name . '-css',
			FBV_DL_URL . 'blocks/dist/index.css',
			$this->get_editor_dependencies(),
			FBV_DL_VERSION
		);
		$this->localize_script();
	}

	public function localize_script( $handle = 'editor' ) {
		$handle = ( 'frontend' === $handle ) ? 'frontend' : 'js';
		wp_localize_script(
			$this->namespace . '-' . $this->block_name . '-' . $handle,
			'fbdl',
			array(
				'json_url'        => apply_filters( 'filebird_json_url', rtrim( rest_url( NJFB_REST_URL ), '/' ) ),
				'rest_nonce'      => wp_create_nonce( 'wp_rest' ),
				'assets_icon_url' => FBV_DL_URL . 'blocks/assets/icons/',
				'type_icons'      => $this->get_files_icon(),
			)
		);
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_script(
			$this->namespace . '-' . $this->block_name . '-frontend',
			FBV_DL_URL . 'blocks/dist/frontend.js',
			array( 'wp-element', 'wp-i18n' ),
			FBV_DL_VERSION,
			true
		);

		wp_set_script_translations(
			$this->namespace . '-' . $this->block_name . '-frontend',
			'filebird-dl',
			FBV_DL_DIR . '/languages/'
		);

		$this->localize_script( 'frontend' );

		wp_enqueue_style(
			$this->namespace . '-' . $this->block_name . '-frontend',
			FBV_DL_URL . 'blocks/dist/index.css',
			array(),
			FBV_DL_VERSION
		);
	}

	public function render( $attributes = array(), $content = '' ) {

		$attributes['request']['selectedFolder'] = array_map(array('FileBird\Blocks\Helpers', 'encrypt'), $attributes['request']['selectedFolder']);

		ob_start(); ?>
<div id="filebird-document-library">
	<div class="njt-fbdl" data-json="<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>"></div>
</div>
		<?php
		return ob_get_clean();
	}

	public function register_rest_routes() {
		register_rest_route(
			NJFB_REST_URL,
			'get-attachments',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_attachments' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function get_attachments( $request ) {
		global $wpdb;

		$params = $request->get_params();

		$limit           = intval( $params['pagination']['limit'] );
		$search          = $wpdb->esc_like( $params['search'] );
		$currentPage     = intval( $params['pagination']['current'] );
		// $selectedFolders = array_map( 'intval', $params['selectedFolder'] );
		$selectedFolders = $params['selectedFolder'];
		$orderBy         = \sanitize_key( $params['orderBy'] );
		$orderType       = \sanitize_key( $params['orderType'] );

		if ( empty( $selectedFolders ) ) {
			return new \WP_REST_Response(
				array(
					'files'       => array(),
					'foundPosts'  => 0,
					'maxNumPages' => 0,
				)
			);
		}

		$is_user_logged_in = is_user_logged_in();

		remove_all_filters('pre_get_posts');

		$where_arr   = array( '1 = 1' );
		$ids         = array_map( function( $id ) use( $is_user_logged_in ) {
			if( is_numeric( $id ) && $is_user_logged_in ) {
				return intval( $id );
			} else {
				return intval( Helpers::decrypt( $id ) );
			}
		}, $selectedFolders);
		$ids = array_filter( $ids, function( $id ){
			return $id > 0;
		} );

		$where_arr[] = '`folder_id` IN (' . implode( ',', $ids ) . ')';
		$in_not_in   = $wpdb->get_col( "SELECT `attachment_id` FROM {$wpdb->prefix}fbv_attachment_folder" . ' WHERE ' . implode( ' AND ', apply_filters( 'fbv_in_not_in_where_query', $where_arr, $ids ) ) );

		if ( empty( $in_not_in ) ) {
			return new \WP_REST_Response(
				array(
					'files'       => array(),
					'foundPosts'  => 0,
					'maxNumPages' => 0,
				)
			);
		}

		$queryArgs = array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post__in'       => $in_not_in,
			'orderby'        => array(
				$orderBy => $orderType,
				'ID'     => 'ASC',
			),
			'post_status'    => 'inherit',
			'posts_per_page' => $limit,
			's'              => $search,
			'offset'         => ( $currentPage - 1 ) * $limit,
		);

		if (class_exists(SizeMeta::class)) {
			$sizeMeta = SizeMeta::getInstance()->meta_key;
		} else {
			$sizeMeta = AttachmentSize::META_KEY;
		}

		if ( 'size' === $orderBy ) {
			$queryArgs['meta_key'] = $sizeMeta;
			$queryArgs['orderby']  = 'meta_value_num';
			$queryArgs['order']    = $orderType;
		}
		$queryArgs = apply_filters( 'fbdl_query_args', $queryArgs, $params );
		$query = new \WP_Query( $queryArgs );

		$posts = $query->get_posts();
		$files = array();
		foreach ( $posts as $post ) {
			$size = \get_post_meta( $post->ID, $sizeMeta, true );
			$url  = \wp_get_attachment_url( $post->ID );
			$type = \wp_check_filetype( $url );
			$file = array(
				'title'    => $post->post_title,
				'type'     => $type['ext'],
				'size'     => ! empty( $size ) ? \size_format( $size ) : '',
				'url'      => $url,
				'link'     => $url,
				'alt'      => $post->post_excerpt,
				'modified' => wp_date( 'M d, Y', strtotime( $post->post_modified ) ),
			);

			$files[] = $file;
		}

		if ( 'post_title' === $orderBy ) {
			if ( 'asc' === $orderType ) {
				usort(
					$files,
					function( $file1, $file2 ) {
						return strnatcasecmp( $file1['title'], $file2['title'] );
					}
				);
			} else {
				usort(
					$files,
					function( $file1, $file2 ) {
						return strnatcasecmp( $file1['title'], $file2['title'] ) * -1;
					}
				);
			}
		}

		return new \WP_REST_Response(
			array(
				'files'       => $files,
				'foundPosts'  => $query->found_posts,
				'maxNumPages' => $query->max_num_pages,
			)
		);
	}
	public function force_download_as3cf( $args, $post_id, $image_size, $copy ) {
		$extension = strtolower( pathinfo( $args['Key'], PATHINFO_EXTENSION ) );
		$force_download_exts = array(
		  'pdf', 'zip', 'doc', 'docx', 'xls', 'xlsx',
		  'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff',
		  'txt', 'csv', 'ppt', 'pptx', 'mp4'
		);
		if ( apply_filters( 'filebird_dl_as3cf', false ) || in_array( $extension, $force_download_exts, true ) ) {
			$args['ContentDisposition'] = 'attachment; filename="' . basename( $args['Key'] ) . '"';
		}
		return $args;
	}
};
