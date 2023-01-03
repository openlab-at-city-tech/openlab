<?php
/**
 * Public functions: DCO_CA class
 *
 * @package DCO_Comment_Attachment
 * @author Denis Yanchevskiy
 * @copyright 2019
 * @license GPLv2+
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Class with public functions.
 *
 * @since 1.0.0
 *
 * @see DCO_CA_Base
 */
class DCO_CA extends DCO_CA_Base {

	/**
	 * Flag showing that attachment checked on upload.
	 *
	 * @since 2.2.0
	 *
	 * @var bool $attachment_checked True if the attachment checked or false otherwise.
	 */
	private $attachment_checked = false;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ) );
	}

	/**
	 * Initializes hooks.
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {
		parent::init_hooks();

		if ( $this->is_attachment_field_enabled() ) {
			add_action( 'comment_form_submit_field', array( $this, 'add_attachment_field' ) );
			add_filter( 'preprocess_comment', array( $this, 'display_attachment_error' ) );
			add_action( 'comment_post', array( $this, 'save_attachment' ), 5, 3 );

			if ( $this->get_option( 'manually_moderation' ) ) {
				add_filter( 'pre_comment_approved', array( $this, 'approve_comment' ) );
			}

			add_filter( 'rest_preprocess_comment', array( $this, 'check_attachment' ) );
			add_action( 'rest_insert_comment', array( $this, 'save_rest_api_attachment' ), 10, 3 );
		}

		add_filter( 'rest_prepare_comment', array( $this, 'add_rest_api_links' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		if ( $this->is_attachment_displayed() ) {
			add_filter( 'comment_text', array( $this, 'display_attachment' ), 10, 2 );
		}

		if ( $this->get_option( 'autoembed_links' ) && ! is_admin() ) {
			add_filter( 'comment_text', array( $this, 'autoembed_links' ), 5 );
		}

		add_shortcode( 'dco_ca', array( $this, 'dco_ca_shortcode' ) );
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_comments_used() ) {
			return;
		}

		if ( $this->is_attachment_field_enabled() ) {
			wp_enqueue_script( 'dco-comment-attachment', DCO_CA_URL . 'assets/dco-comment-attachment.js', array( 'jquery' ), DCO_CA_VERSION, true );
			wp_localize_script(
				'dco-comment-attachment',
				'dco_ca',
				array(
					'commenting_form_not_found' => __( 'The commenting form not found.', 'dco-comment-attachment' ),
				)
			);
		}

		wp_enqueue_style( 'dco-comment-attachment', DCO_CA_URL . 'assets/dco-comment-attachment.css', array(), DCO_CA_VERSION );
	}

	/**
	 * Adds a file upload field to the commenting form.
	 *
	 * @since 1.0.0
	 *
	 * @param string $submit_field HTML markup for the submit field.
	 * @return string HTML markup for the file field and the submit field.
	 */
	public function add_attachment_field( $submit_field ) {
		ob_start();
		?>
		<p class="comment-form-attachment">
			<?php
			$this->form_element( 'label' );
			$this->form_element( 'input' );
			$this->form_element( 'upload-size' );
			$this->form_element( 'file-types' );
			$this->form_element( 'autoembed-links' );
			$this->form_element( 'drop-area' );
			?>
		</p>
		<?php
		/**
		 * Filters the attachment field markup.
		 *
		 * @since 1.1.1
		 *
		 * @param string $markup HTML markup for the attachment field.
		 */
		$file_field = apply_filters( 'dco_ca_attachment_field', ob_get_clean() );

		return $file_field . $submit_field;
	}

	/**
	 * Generates HTML markup for the form element.
	 *
	 * @since 1.1.1
	 *
	 * @param string $type The type of the form element.
	 * @return void HTML markup for the specified form element.
	 */
	public function form_element( $type ) {
		$markup = '';
		switch ( $type ) {
			case 'label':
				ob_start();
				$required = $this->get_option( 'required_attachment' );
				?>
				<label class="comment-form-attachment__label" for="attachment">
					<?php
					$label = $this->get_option( 'enable_multiple_upload' ) ? __( 'Attachments', 'dco-comment-attachment' ) : __( 'Attachment', 'dco-comment-attachment' );
					echo esc_html( $label );
					if ( $required ) {
						echo ' <span class="required">*</span>';
					}
					?>
				</label>
				<?php
				/**
				 * Filters the label form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the label form element.
				 * @param bool $required Whether to attachment is required.
				 */
				$markup = apply_filters( 'dco_ca_form_element_label', ob_get_clean(), $required );
				break;
			case 'input':
				ob_start();
				$name     = $this->get_upload_field_name();
				$multiple = '';
				if ( $this->get_option( 'enable_multiple_upload' ) ) {
					$name    .= '[]';
					$multiple = ' multiple';
				}
				$accept = '';
				$this->enable_filter_upload();
				$types = $this->get_allowed_file_types( 'array' );
				$this->disable_filter_upload();
				if ( $types ) {
					$accept = '.' . implode( ',.', $types );
				}
				?>
				<input class="comment-form-attachment__input" id="attachment" name="<?php echo esc_attr( $name ); ?>" type="file" accept="<?php echo esc_attr( $accept ); ?>"<?php echo esc_attr( $multiple ); ?> />
				<?php
				/**
				 * Filters the input form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the input form element.
				 * @param string $name Name of the attachment input.
				 * @param array $types Allowed upload file types.
				 */
				$markup = apply_filters( 'dco_ca_form_element_input', ob_get_clean(), $name, $types );
				break;
			case 'upload-size':
				ob_start();
				$max_upload_size = $this->get_max_upload_size( true );
				?>
				<span class="comment-form-attachment__file-size-notice">
					<?php
					/* translators: %s: the maximum allowed upload file size */
					printf( esc_html__( 'The maximum upload file size: %s.', 'dco-comment-attachment' ), esc_html( $max_upload_size ) );
					?>
				</span>
				<?php
				/**
				 * Filters the maximum upload file size form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the maximum upload
				 *                       file size form element.
				 * @param string $max_upload_size The max upload file size with format.
				 */
				$markup = apply_filters( 'dco_ca_form_element_upload_size', ob_get_clean(), $max_upload_size );
				break;
			case 'file-types':
				ob_start();
				$this->enable_filter_upload();
				$types = $this->get_allowed_file_types( 'html' );
				$this->disable_filter_upload();
				?>
				<span class="comment-form-attachment__file-types-notice">
					<?php
					/* translators: %s: the allowed file types list */
					printf( esc_html__( 'You can upload: %s.', 'dco-comment-attachment' ), wp_kses_data( $types ) );
					?>
				</span>
				<?php
				/**
				 * Filters the allowed file types list form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the allowed file
				 *                       types list form element.
				 * @param string $types The file types list allowed for upload.
				 */
				$markup = apply_filters( 'dco_ca_form_element_file_types', ob_get_clean(), $types );
				break;
			case 'autoembed-links-notification':
				_deprecated_argument( __FUNCTION__, esc_html__( 'DCO Comment Attachment 2.0.0', 'dco-comment-attachment' ), esc_html__( 'The type "autoembed-links-notification" is deprecated. Use "autoembed-links" instead.', 'dco-comment-attachment' ) );
				// No break for deprecated compatibility.
			case 'autoembed-links':
				ob_start();
				$autoembed_links = $this->get_option( 'autoembed_links' );
				if ( $autoembed_links ) :
					?>
					<span class="comment-form-attachment__autoembed-links-notice">
						<?php esc_html_e( 'Links to YouTube, Facebook, Twitter and other services inserted in the comment text will be automatically embedded.', 'dco-comment-attachment' ); ?>
					</span>
					<?php
				endif;

				/**
				 * Filters the autoembed links notification form element markup.
				 *
				 * @since 1.3.0
				 *
				 * @param string $markup HTML markup for the autoembed links
				 *                       notification list form element.
				 * @param bool $autoembed_links Whether the links is automatically embedded.
				 */
				if ( has_filter( 'dco_ca_form_element_autoembed_links_notification' ) ) {
					$markup = apply_filters_deprecated( 'dco_ca_form_element_autoembed_links_notification', array( ob_get_clean(), $autoembed_links ), 'DCO Comment Attachment 2.0.0', 'dco_ca_form_element_autoembed_links' );
				} else {
					$markup = apply_filters( 'dco_ca_form_element_autoembed_links', ob_get_clean(), $autoembed_links );
				}
				break;
			case 'drop-area':
				ob_start();
				?>
				<span class="comment-form-attachment__drop-area">
					<span class="comment-form-attachment__drop-area-inner">
						<?php echo $this->get_option( 'enable_multiple_upload' ) ? esc_html__( 'Drop files here', 'dco-comment-attachment' ) : esc_html__( 'Drop file here', 'dco-comment-attachment' ); ?>
					</span>
				</span>
				<?php
				/**
				 * Filters the drop area form element markup.
				 *
				 * @since 2.2.0
				 *
				 * @param string $markup HTML markup for the drop area form element.
				 */
				$markup = apply_filters( 'dco_ca_form_element_drop_area', ob_get_clean() );
				break;
		}

		/**
		 * Filters the form element markup.
		 *
		 * @since 1.1.1
		 *
		 * @param string $markup HTML markup for the form element.
		 * @param string $type The type of the form element.
		 */
		echo apply_filters( 'dco_ca_form_element', $markup, $type ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Checks the attachments before posting a comment.
	 *
	 * @since 1.0.0
	 *
	 * @param array $prepared_comment The prepared comment data for `wp_insert_comment`.
	 * @return array|WP_Error Comment data on success, or error object on failure.
	 */
	public function check_attachment( $prepared_comment ) {
		$field_name = $this->get_upload_field_name();

		if ( ! isset( $_FILES[ $field_name ] ) ) {
			return $prepared_comment;
		}

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$attachments = $_FILES[ $field_name ];
		// phpcs:enable

		// If the feature to upload multiple files is disabled,
		// but the user has uploaded multiple files.
		if ( ! $this->get_option( 'enable_multiple_upload' ) && is_array( $attachments['name'] ) ) {
			return new WP_Error(
				'dco-comment-attachment',
				__( 'Uploading multiple files is forbidden!', 'dco-comment-attachment' ),
				array( 'status' => 403 )
			);
		}

		$names       = (array) $attachments['name'];
		$tmp_names   = (array) $attachments['tmp_name'];
		$error_codes = (array) $attachments['error'];
		$sizes       = (array) $attachments['size'];

		foreach ( $error_codes as $error_code ) {
			$upload_error = $this->get_upload_error( $error_code );
			if ( $upload_error ) {
				return new WP_Error(
					"dco-comment-attachment-$error_code",
					$upload_error,
					array( 'status' => 500 )
				);
			}
		}

		// Check that the file has been uploaded.
		if ( ! isset( $tmp_names[0] ) || ! is_uploaded_file( $tmp_names[0] ) ) {
			if ( $this->get_option( 'required_attachment' ) ) {
				return new WP_Error(
					'dco-comment-attachment',
					__( 'Attachment is required.', 'dco-comment-attachment' ),
					array( 'status' => 400 )
				);
			} else {
				return $prepared_comment;
			}
		}

		// We need to do this check, because the maximum allowed upload file size in WordPress
		// can be less than the specified on the server.
		$size = 0;
		foreach ( $sizes as $s ) {
			$size += $s;
		}

		if ( $size > $this->get_max_upload_size() ) {
			$error_code   = 1;
			$upload_error = $this->get_upload_error( $error_code );
			return new WP_Error(
				"dco-comment-attachment-$error_code",
				$upload_error,
				array( 'status' => 500 )
			);
		}

		foreach ( $names as $name ) {
			$this->enable_filter_upload();
			$filetype = wp_check_filetype( $name );
			$this->disable_filter_upload();

			if ( ! $filetype['ext'] ) {
				return new WP_Error(
					'dco-comment-attachment',
					__( "WordPress doesn't allow this type of uploads.", 'dco-comment-attachment' ),
					array( 'status' => 400 )
				);
			}
		}

		$this->attachment_checked = true;

		return $prepared_comment;
	}

	/**
	 * Checks the attachments and displays error.
	 *
	 * @since 2.3.0
	 *
	 * @param array $commentdata Comment data.
	 * @return array Comment data on success.
	 */
	public function display_attachment_error( $commentdata ) {
		$commentdata = $this->check_attachment( $commentdata );

		if ( is_wp_error( $commentdata ) ) {
			$error     = $commentdata->get_error_message();
			$err_title = __( 'ERROR', 'dco-comment-attachment' );
			wp_die( '<p><strong>' . esc_html( $err_title ) . '</strong>: ' . esc_html( $error ) . '</p>', esc_html__( 'Comment Submission Failure', 'dco-comment-attachment' ), array( 'back_link' => true ) );
		}

		return $commentdata;
	}

	/**
	 * Gets the upload error message by the PHP upload error code.
	 *
	 * @since 1.0.0
	 *
	 * @param int $error_code The PHP upload error code.
	 * @return string|false The error message if an error occurred,
	 *                      false if upload success.
	 */
	public function get_upload_error( $error_code ) {
		$upload_errors = array(
			/* translators: %s: the maximum allowed upload file size */
			1 => sprintf( __( 'The file is too large. Allowed attachments up to %s.', 'dco-comment-attachment' ), $this->get_max_upload_size( true ) ),
			2 => __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'dco-comment-attachment' ),
			3 => __( 'The uploaded file was only partially uploaded.', 'dco-comment-attachment' ),
			6 => __( 'Missing a temporary folder.', 'dco-comment-attachment' ),
			7 => __( 'Failed to write file to disk.', 'dco-comment-attachment' ),
			8 => __( 'A PHP extension stopped the file upload.', 'dco-comment-attachment' ),
		);

		if ( isset( $upload_errors[ $error_code ] ) ) {
			return $upload_errors[ $error_code ];
		}

		return false;
	}

	/**
	 * Determines if the comment will be automatically approved or manual moderation is required.
	 *
	 * @since 2.1.0
	 *
	 * @param int|string|WP_Error $approved The approval status. Accepts 1, 0, 'spam', 'trash',
	 *                                      or WP_Error.
	 * @return int|string|WP_Error Allowed comments return the approval status (0|1|'spam'|'trash').
	 *                             Disallowed comments return a WP_Error.
	 */
	public function approve_comment( $approved ) {
		$field_name = $this->get_upload_field_name();

		if ( $this->is_attachment_checked() ) {
			$approved = 0;
		}

		return $approved;
	}

	/**
	 * Saves attachments after comment is posted.
	 *
	 * @since 1.0.0
	 *
	 * @param int        $comment_id The comment ID.
	 * @param int|string $comment_approved 1 if the comment is approved, 0 if not,
	 *                                     'spam' if spam.
	 * @param array      $comment Comment data.
	 */
	public function save_attachment( $comment_id, $comment_approved, $comment ) {
		$field_name = $this->get_upload_field_name();
		if ( ! isset( $_FILES[ $field_name ] ) ) {
			return;
		}

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$post_id = 0;

		/**
		 * Filters whether to attach the attachment to the commented post.
		 *
		 * @since 2.2.0
		 *
		 * @param bool $attach_to_post Whether to attach the attachment to the commented post.
		 */
		$attach_to_post = apply_filters( 'dco_ca_attach_to_post', true );
		if ( $attach_to_post ) {
			$post_id = $comment['comment_post_ID'];
		}

		$ids = array();
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$attachments = $_FILES[ $field_name ];
		// phpcs:enable
		$names       = (array) $attachments['name'];
		$types       = (array) $attachments['type'];
		$tmp_names   = (array) $attachments['tmp_name'];
		$error_codes = (array) $attachments['error'];
		$sizes       = (array) $attachments['size'];

		foreach ( $names as $key => $value ) {
			// Emulate the upload of each file separately, because the `media_handle_upload`
			// function doesn't support uploading multiple files.
			$file                  = array(
				'name'     => $value,
				'type'     => $types[ $key ],
				'tmp_name' => $tmp_names[ $key ],
				'error'    => $error_codes[ $key ],
				'size'     => $sizes[ $key ],
			);
			$_FILES[ $field_name ] = $file;

			$this->enable_filter_upload();
			$attachment_id = media_handle_upload( $field_name, $post_id );
			$this->disable_filter_upload();

			if ( ! is_wp_error( $attachment_id ) ) {
				$ids[] = $attachment_id;
			}
		}

		if ( $ids ) {
			$this->assign_attachment( $comment_id, $ids );
		}

		$_FILES[ $field_name ] = $attachments;
	}

	/**
	 * Saves attachments after comment is posted via REST API.
	 *
	 * @since 2.3.0
	 *
	 * @param WP_Comment      $comment Inserted or updated comment object.
	 * @param WP_REST_Request $request Request object.
	 * @param bool            $creating True when creating a comment, false when updating.
	 */
	public function save_rest_api_attachment( $comment, $request, $creating ) {
		if ( ! $creating ) {
			return;
		}

		$this->save_attachment( $comment->comment_ID, $comment->comment_approved, $comment->to_array() );
	}

	/**
	 * Displays an assigned attachments.
	 *
	 * @since 1.0.0
	 *
	 * @param string          $comment_text Text of the current comment.
	 * @param WP_Comment|null $comment      The comment object. Null if not found.
	 * @return string Text of the comment with an assigned attachment.
	 */
	public function display_attachment( $comment_text = '', $comment = null ) {
		if ( is_null( $comment ) ) {
			$comment = get_comment();
		}

		if ( ! $comment instanceof WP_Comment || ! $this->has_attachment( $comment->comment_ID ) ) {
			return $comment_text;
		}

		$attachment_id      = $this->get_attachment_id( $comment->comment_ID );
		$attachment_content = $this->generate_attachment_markup( $attachment_id );

		return $comment_text . $attachment_content;
	}

	/**
	 * Generates an attachment markup.
	 *
	 * @since 2.4.0
	 *
	 * @param int|array $attachment_id The attachment ID(s).
	 * @return string The attachment HTML markup.
	 */
	public function generate_attachment_markup( $attachment_id ) {
		if ( ! is_array( $attachment_id ) ) {
			$attachment_id = (array) $attachment_id;
		}

		if ( count( $attachment_id ) > 1 ) {
			$this->enable_gallery_image_size();
			$attachments_content = array();
			foreach ( $attachment_id as $attach_id ) {
				// Check that the attachment exists.
				if ( ! wp_get_attachment_url( $attach_id ) ) {
					continue;
				}

				$type = $this->get_embed_type( $attach_id );
				$key  = "{$type}_{$attach_id}";

				$attachments_content[ $key ] = $this->get_attachment_preview( $attach_id );
			}

			if ( $this->get_option( 'combine_images' ) || is_admin() ) {
				// Combine only images.
				$not_images = array();
				foreach ( $attachments_content as $key => $content ) {
					if ( strpos( $key, 'image' ) === false ) {
						$not_images[ $key ] = $content;
					}
				}
				$attachments_content = array_diff( $attachments_content, $not_images );

				$gallery_start = '<div class="dco-attachment-gallery">';
				array_unshift( $attachments_content, $gallery_start );
				$attachments_content[] = '</div>';
				$attachments_content   = array_merge( $attachments_content, $not_images );
			}

			$attachment_content = implode( '', $attachments_content );
			$this->disable_gallery_image_size();
		} else {
			$attachment_content = $this->get_attachment_preview( current( $attachment_id ) );
		}

		return $attachment_content;
	}

	/**
	 * Adds attachment links into comment REST API response.
	 *
	 * @since 2.3.0
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Comment       $comment The original comment object.
	 * @return WP_REST_Response The response object.
	 */
	public function add_rest_api_links( $response, $comment ) {
		$attachment_id = (array) $this->get_attachment_id( $comment->comment_ID );
		if ( ! $attachment_id ) {
			return $response;
		}

		$rel = $this->get_attachment_meta_key();

		foreach ( $attachment_id as $attach_id ) {
			$response->add_link(
				$rel,
				rest_url( 'wp/v2/media/' . $attach_id ),
				array(
					'embeddable' => true,
				)
			);
		}

		return $response;
	}

	/**
	 * The dco_ca shortcode handler.
	 *
	 * @since 2.4.0
	 *
	 * @param array $atts {
	 *     An array of shortcode attributes.
	 *
	 *     @type int $post_id Optional. The post ID. Default current post ID.
	 *     @type string $type Optional. Attachment types separated by comma.
	 *                                  Accepts: all, image, video, audio, misc.
	 *                                  Default: all.
	 * }
	 */
	public function dco_ca_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'post_id' => get_the_ID(),
				'type'    => 'all',
			),
			$atts,
			'dco_ca'
		);

		$comments = get_comments(
			array(
				'post_id'  => $atts['post_id'],
				'meta_key' => 'attachment_id',
				'status'   => 'approve',
			)
		);

		$ids = array();
		foreach ( $comments as $comment ) {
			$attachment_id = (array) $this->get_attachment_id( $comment->comment_ID );
			if ( 'all' === $atts['type'] ) {
				$ids = array_merge( $ids, $attachment_id );
			} else {
				$types = array_map( 'trim', explode( ',', $atts['type'] ) );
				foreach ( $attachment_id as $attach_id ) {
					$type = $this->get_embed_type( $attach_id );
					if ( in_array( $type, $types, true ) ) {
						$ids[] = $attach_id;
					}
				}
			}
		}

		return '<div class="dco-attachments-list">' . $this->generate_attachment_markup( $ids ) . '</div>';
	}

	/**
	 * Embeds links.
	 *
	 * @since 1.2.0
	 *
	 * @param string $comment_text Text of the current comment.
	 * @return string Text of the comment with embedded links.
	 */
	public function autoembed_links( $comment_text ) {
		return $GLOBALS['wp_embed']->autoembed( $comment_text );
	}

	/**
	 * Filters a standard list of allowed mime types and file extensions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $mimes Mime types keyed by the file extension regex
	 *                     corresponding to those types.
	 * @return array Filtered mime types array.
	 */
	public function filter_upload_mimes( $mimes ) {
		$allowed_mimes = $this->get_option( 'allowed_file_types' );

		$filtered_mimes = array();
		foreach ( $mimes as $mime => $mime_type ) {
			$exts = explode( '|', $mime );
			if ( count( $exts ) > 1 ) {
				$ext_items = array();
				foreach ( $exts as $ext ) {
					if ( in_array( $ext, $allowed_mimes, true ) ) {
						$ext_items[] = $ext;
					}
				}
				if ( $ext_items ) {
					$exts                    = implode( '|', $ext_items );
					$filtered_mimes[ $exts ] = $mime_type;
				}
			} else {
				if ( in_array( $mime, $allowed_mimes, true ) ) {
					$filtered_mimes[ $mime ] = $mime_type;
				}
			}
		}

		return $filtered_mimes;
	}

	/**
	 * Enables filtering of the standard list of allowed mime types and file extensions.
	 *
	 * @since 1.1.0
	 */
	public function enable_filter_upload() {
		add_filter( 'upload_mimes', array( $this, 'filter_upload_mimes' ), 999 );
	}

	/**
	 * Disables filtering of the standard list of allowed mime types and file extensions.
	 *
	 * @since 1.1.0
	 */
	public function disable_filter_upload() {
		remove_filter( 'upload_mimes', array( $this, 'filter_upload_mimes' ), 999 );
	}

	/**
	 * Checks that this is a single post and comments are enabled for this post.
	 *
	 * @since 1.1.0
	 *
	 * @return bool True if we are on a single post with allowed comments
	 *              or false otherwise.
	 */
	public function is_comments_used() {
		return is_singular() && comments_open();
	}

	/**
	 * Checks that the attachment field is enabled or not.
	 *
	 * @since 1.1.0
	 *
	 * @return bool True if the attachment field is enabled or false otherwise.
	 */
	public function is_attachment_field_enabled() {
		$disable = false;

		if ( ! $this->is_user_can_upload() ) {
			$disable = true;
		}

		/**
		 * Filters whether to disable the attachment upload field.
		 *
		 * Prevents the attachment upload field from being appended to the commenting form.
		 *
		 * @since 1.1.0
		 *
		 * @param bool $disable Whether to disable the attachment upload field.
		 *                      Returning true to the filter will disable the attachment field.
		 *                      Default false.
		 */
		return ! apply_filters( 'dco_ca_disable_attachment_field', $disable );
	}

	/**
	 * Checks that attachment displayed or not.
	 *
	 * @since 1.2.0
	 *
	 * @return bool True if the attachment display is enabled or false otherwise.
	 */
	public function is_attachment_displayed() {
		/**
		 * Filters whether to disable the attachment display.
		 *
		 * Prevents the attachment from being displayed in the comments list.
		 *
		 * @since 1.2.0
		 *
		 * @param bool $bool Whether to disable the attachment display.
		 *                   Returning true to the filter will disable the attachment display.
		 *                   Default false.
		 */
		return ! apply_filters( 'dco_ca_disable_display_attachment', false );
	}

	/**
	 * Checks that attachment checked on upload or not.
	 *
	 * @since 2.2.0
	 *
	 * @return bool True if the attachment checked or false otherwise.
	 */
	public function is_attachment_checked() {
		return $this->attachment_checked;
	}

	/**
	 * Checks that the current user can upload the attachment.
	 *
	 * @return bool True if the user can upload or false otherwise.
	 */
	public function is_user_can_upload() {
		$who_can_upload = (int) $this->get_option( 'who_can_upload' );

		// All users.
		if ( 1 === $who_can_upload ) {
			return true;
		}

		// Only logged users.
		if ( 2 === $who_can_upload && is_user_logged_in() ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets the name of the upload field used in the commenting form.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the upload input.
	 */
	public function get_upload_field_name() {
		return 'attachment';
	}

	/**
	 * Sets the image size for the gallery.
	 *
	 * @since 2.0.0
	 */
	public function enable_gallery_image_size() {
		add_filter( 'dco_ca_admin_thumbnail_size', array( $this, 'get_gallery_image_size' ) );
		add_filter( 'dco_ca_get_option_thumbnail_size', array( $this, 'get_gallery_image_size' ) );
	}

	/**
	 * Restores the image size for the single image.
	 *
	 * @since 2.0.0
	 */
	public function disable_gallery_image_size() {
		remove_filter( 'dco_ca_admin_thumbnail_size', array( $this, 'get_gallery_image_size' ) );
		remove_filter( 'dco_ca_get_option_thumbnail_size', array( $this, 'get_gallery_image_size' ) );
	}

	/**
	 * Sets the image size for the gallery (callback function).
	 *
	 * @since 2.0.0
	 *
	 * @param string $size The thumbnail size of the attachment image.
	 * @return string The overridden thumbnail size, if it's necessary.
	 */
	public function get_gallery_image_size( $size ) {
		if ( 'dco_ca_admin_thumbnail_size' === current_filter() ) {
			/**
			 * Filters the attachment image size in the gallery for the admin panel.
			 *
			 * @since 2.0.0
			 *
			 * @param string $size The thumbnail size of the attachment image.
			 */
			return apply_filters( 'dco_ca_admin_gallery_size', 'thumbnail' );
		}

		if ( 'dco_ca_get_option_thumbnail_size' === current_filter() && $this->get_option( 'combine_images' ) ) {
			return $this->get_option( 'gallery_size' );
		}

		return $size;
	}

}
