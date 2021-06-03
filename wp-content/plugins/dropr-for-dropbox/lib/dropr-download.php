<?php
/**
 * Dropbox download Class
 *
 * @link       http://awsm.in/awsm-dropbox
 * @since      1.1
 *
 * @package    Dropr_main
 */
class DroprDownload {

	private static $instance;
	private $plugin_path;
	private $plugin_version;
	/**
	 * Returns the instance of the class [Singleton]
	 * @return instance
	 */
	public static function getInstance( $plugin_path, $version ) {
		if ( self::$instance === null ) {
			self::$instance = new DroprDownload( $plugin_path, $version );
		}
		return self::$instance;
	}

	private function __construct( $plugin_path, $version ) {
		$this->plugin_path    = $plugin_path;
		$this->plugin_version = $version;
	}

	/**
   * Adds Add from dropbox Link on featured image meta box
   * @since    1.1
   */
	public function featuredDropboxlink( $content ) {
		$content .= '<p class="hide-if-no-js"><a href="#" id="droper-featured"><img src="' . plugins_url( 'dropr-for-dropbox/images/dropr-icon-xs-b.png' ) . '" style="float: left;margin-right: 5px;">  ' . __( 'Add From Dropbox', 'dropr' ) . '</a>';
		$content .= '<img src="' . plugins_url( 'dropr-for-dropbox/images/loading-bubbles.svg' ) . '" style="display:none;" class="droprLoader" alt="Loading icon"/></p><p id="dropr-holder"></p>';
		return $content;
	}

	/**
   * Ajax image upload handler
   * @since    1.1
   */
	public function dropruploadimage() {
		$json['status'] = false;
		$post_id        = intval( $_REQUEST['pid'] );

		if ( ! check_ajax_referer( 'dropr-featured', 'droprKey', false ) ) {
			die( json_encode( $json ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			die( json_encode( $json ) );
		}

		global $post;
		$json            = array();
		$files           = $_REQUEST['files'];
		$dropfile        = $files[0];
		$filename        = sanitize_file_name( $dropfile['name'] );
		$file_size       = intval( $dropfile['bytes'] );
		$fileurl         = esc_url_raw( $dropfile['link'] );
		$fileurl         = str_replace( 'dl=0', 'raw=1', $fileurl );
		$file_type       = wp_check_filetype( $filename );
		$attachment_id   = 0;
		$attachment_data = array(
			'name' => $filename,
			'size' => $file_size,
			'link' => $fileurl,
			'type' => $file_type,
		);

		$generic_options = dropr_get_generic_options();

		if ( $generic_options['featured_image_storage'] === 'local' ) {
			$attachment_data['storage'] = 'local';

			$attachment_id = $this->uploadremotefile( $fileurl, $filename, $post_id );
		} else {
			$attachment_data['storage'] = 'dropbox';

			$attachment_id = $this->external_media_handler( $attachment_data, $post_id );
		}

		if ( $attachment_id ) {
			$json['attachment_id'] = $attachment_id;
			$json['status']        = true;
			set_post_thumbnail( $post_id, $attachment_id );
			$json['html'] = $this->wp_post_thumbnail_html( $attachment_id, $post_id );

			update_post_meta( $attachment_id, '_awsm_dropr_attached_file', $attachment_data );
		} else {
			$json['status'] = false;
		}
		die( json_encode( $json ) );
	}

	/**
	 * Attachment handler.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function attachment_handler() {
		$attachment_ids  = array();
		$dropbox_files   = $_POST['files'];
		$generic_options = dropr_get_generic_options();

		if ( ! empty( $dropbox_files ) && is_array( $dropbox_files ) ) {
			foreach ( $dropbox_files as $dropbox_file ) {
				$filename        = sanitize_file_name( $dropbox_file['name'] );
				$file_size       = intval( $dropbox_file['bytes'] );
				$fileurl         = esc_url_raw( $dropbox_file['link'] );
				$fileurl         = str_replace( 'dl=0', 'raw=1', $fileurl );
				$file_type       = wp_check_filetype( $filename );
				$post_id         = 0;
				$attachment_data = array(
					'name' => $filename,
					'size' => $file_size,
					'link' => $fileurl,
					'type' => $file_type,
				);

				$attachment_id = 0;
				if ( $generic_options['media_library_storage'] === 'local' ) {
					$attachment_data['storage'] = 'local';

					$attachment_id = $this->uploadremotefile( $fileurl, $filename, $post_id );
				} else {
					$attachment_data['storage'] = 'dropbox';

					$attachment_id = $this->external_media_handler( $attachment_data );
				}

				if ( $attachment_id ) {
					update_post_meta( $attachment_id, '_awsm_dropr_attached_file', $attachment_data );
					$attachment_ids[] = $attachment_id;
				}
			}
		}

		return $attachment_ids;
	}

	/**
	 * Handle external media.
	 *
	 * @param array $attachment_data The attachment data array.
	 * @param integer $post_id The Post ID.
	 * @return int
	 */
	public function external_media_handler( $attachment_data, $post_id = 0 ) {
		$attachment_id = 0;
		$mime_type     = $attachment_data['type']['type'];

		if ( ! empty( $mime_type ) ) {
			$attachment_title    = preg_replace( '/\.[^.]+$/', '', $attachment_data['name'] );
			$attachment_metadata = array(
				'file'     => wp_basename( $attachment_data['link'] ),
				'filesize' => $attachment_data['size'],
			);

			$is_valid = true;
			if ( strpos( $mime_type, 'image' ) !== false ) {
				$image_size = @getimagesize( $attachment_data['link'] );
				if ( ! empty( $image_size ) ) {
					$mime_type                     = $image_size['mime'];
					$attachment_metadata['width']  = $image_size[0];
					$attachment_metadata['height'] = $image_size[1];
					$attachment_metadata['sizes']  = array(
						'full' => $attachment_metadata,
					);
				} else {
					$is_valid = false;
				}
			}

			if ( $is_valid ) {
				$attachment_args = array(
					'guid'           => $attachment_data['link'],
					'post_title'     => $attachment_title,
					'post_content'   => '',
					'post_mime_type' => $mime_type,
				);

				$attachment_id = wp_insert_attachment( $attachment_args, false, $post_id );
				if ( ! empty( $attachment_id ) ) {
					wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
				}
			}
		}

		return $attachment_id;
	}

	/**
	 * Add the media to the Media Library.
	 */
	public function add_to_media_library() {
		check_ajax_referer( 'dropr-featured', 'droprKey' );

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error();
		}

		$attachment_ids = $this->attachment_handler();

		if ( empty( $attachment_ids ) || ! is_array( $attachment_ids ) ) {
			wp_send_json_error();
		}

		$data_arr = array();
		foreach ( $attachment_ids as $attachment_id ) {
			$attachment_data = wp_prepare_attachment_for_js( $attachment_id );
			if ( ! empty( $attachment_data ) ) {
				$data_arr[] = $attachment_data;
			}
		}

		if ( empty( $data_arr ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $data_arr );
	}

	/**
	 * Thumbanail ajax response
	 * @since    1.1
	 */
	public function wp_post_thumbnail_html( $thumbnail_id = null, $post = null ) {
		global $content_width, $_wp_additional_image_sizes;

		$post = get_post( $post );

		$upload_iframe_src  = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
		$set_thumbnail_link = '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set featured image' ) . '" href="%s" id="set-post-thumbnail" class="thickbox">%s</a></p>';
		$content            = sprintf( $set_thumbnail_link, $upload_iframe_src, esc_html__( 'Set featured image' ) );

		if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
			$old_content_width = $content_width;
			$content_width     = 266;
			if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
				$thumbnail_html = wp_get_attachment_image( $thumbnail_id, array( $content_width, $content_width ) );
			} else {
				$thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'post-thumbnail' );
			}
			if ( ! empty( $thumbnail_html ) ) {
				$ajax_nonce = wp_create_nonce( 'set_post_thumbnail-' . $post->ID );
				$content    = sprintf( $set_thumbnail_link, $upload_iframe_src, $thumbnail_html );
				$content   .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html__( 'Remove featured image' ) . '</a></p>';
			}
			$content_width = $old_content_width;
		}
		return apply_filters( 'admin_post_thumbnail_html', $content, $post->ID );
	}

	/**
   * Downloads files from dropbox to server
   * @since    1.1
   */
	public function uploadremotefile( $file, $name, $post_id ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( ! empty( $file ) ) {
			$file_array             = array();
			$file_array['name']     = $name;
			$file_array['tmp_name'] = download_url( $file );
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return false;
			}
			$thumbnail_id = media_handle_sideload( $file_array, $post_id, $name );
			@unlink( $file_array['tmp_name'] );
			if ( is_wp_error( $thumbnail_id ) ) {
				return false;
			}
			return $thumbnail_id;
		} else {
			return false;
		}

	}

	/**
	 * Customize the attachment data prepared for JavaScript.
	 *
	 * @param array $response Array of prepared attachment data.
	 * @param WP_Post $attachment Attachment object.
	 * @return array
	 */
	public function prepare_attachment_for_js( $response, $attachment ) {
		if ( strpos( $attachment->guid, 'dropbox.com' ) !== false ) {
			$attachment_data = get_post_meta( $attachment->ID, '_awsm_dropr_attached_file', true );
			if ( ! empty( $attachment_data ) && is_array( $attachment_data ) ) {
				$splitted_name        = explode( '?', $response['filename'] );
				$response['filename'] = $splitted_name[0];
			}
		}
		return $response;
	}

	/**
	 * Customize the attached file.
	 *
	 * @param string $file Path to attached file.
	 * @param int $attachment_id Attachment ID.
	 * @return string
	 */
	public function get_attached_file( $file, $attachment_id ) {
		if ( empty( $file ) ) {
			$attachment_data = get_post_meta( $attachment_id, '_awsm_dropr_attached_file', true );
			if ( ! empty( $attachment_data ) && is_array( $attachment_data ) ) {
				$file = $attachment_data['link'];
			}
		}
		return $file;
	}

	/**
	 * Meta box html actions
	 * @since 1.1
	 */
	public function addMediasupport() {
		add_action( 'wp_ajax_dropruploadimage', array( $this, 'dropruploadimage' ) );
		add_action( 'wp_ajax_dropr_add_to_media_library', array( $this, 'add_to_media_library' ) );

		add_filter( 'admin_post_thumbnail_html', array( $this, 'featuredDropboxlink' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'prepare_attachment_for_js' ), 10, 2 );
		add_filter( 'get_attached_file', array( $this, 'get_attached_file' ), 10, 2 );
	}
}
