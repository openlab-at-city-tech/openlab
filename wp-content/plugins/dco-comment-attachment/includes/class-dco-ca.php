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
			add_filter( 'preprocess_comment', array( $this, 'check_attachment' ) );
			add_action( 'comment_post', array( $this, 'save_attachment' ), 10, 3 );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		if ( $this->is_attachment_displayed() ) {
			add_filter( 'comment_text', array( $this, 'display_attachment' ) );
		}

		if ( $this->get_option( 'autoembed_links' ) && ! is_admin() ) {
			add_filter( 'comment_text', array( $this, 'autoembed_links' ), 5 );
		}
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( $this->is_comments_used() ) {
			if ( $this->is_attachment_field_enabled() ) {
				wp_enqueue_script( 'dco-comment-attachment', DCO_CA_URL . 'assets/dco-comment-attachment.js', array( 'jquery' ), DCO_CA_VERSION, true );
			}

			wp_enqueue_style( 'dco-comment-attachment', DCO_CA_URL . 'assets/dco-comment-attachment.css', array(), DCO_CA_VERSION );
		}
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
			$this->form_element( 'autoembed-links-notification' );
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
				<label for="attachment">
					<?php
					esc_html_e( 'Attachment', 'dco-comment-attachment' );
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
				$name = $this->get_upload_field_name();
				?>
				<input id="attachment" name="<?php echo esc_attr( $name ); ?>" type="file" />
				<?php
				/**
				 * Filters the input form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the input form element.
				 * @param string $name Name of the attachment input.
				 */
				$markup = apply_filters( 'dco_ca_form_element_input', ob_get_clean(), $name );
				break;
			case 'upload-size':
				ob_start();
				$max_upload_size = $this->get_max_upload_size( true );
				/* translators: %s: the maximum allowed upload file size */
				printf( esc_html__( 'The maximum upload file size: %s.', 'dco-comment-attachment' ), esc_html( $max_upload_size ) );

				/**
				 * Filters the maximum upload file size form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the maximum upload
				 *                       file size form element.
				 * @param string $max_upload_size The max upload file size with format.
				 */
				$markup = apply_filters( 'dco_ca_form_element_upload_size', '<br>' . ob_get_clean(), $max_upload_size );
				break;
			case 'file-types':
				ob_start();
				$this->enable_filter_upload();
				$types = $this->get_allowed_file_types( 'html' );
				$this->disable_filter_upload();
				/* translators: %s: the allowed file types list */
				printf( esc_html__( 'You can upload: %s.', 'dco-comment-attachment' ), wp_kses_data( $types ) );

				/**
				 * Filters the allowed file types list form element markup.
				 *
				 * @since 1.1.1
				 *
				 * @param string $markup HTML markup for the allowed file
				 *                       types list form element.
				 * @param string $types The file types list allowed for upload.
				 */
				$markup = apply_filters( 'dco_ca_form_element_file_types', '<br>' . ob_get_clean(), $types );
				break;
			case 'autoembed-links-notification':
				ob_start();
				$autoembed_links = $this->get_option( 'autoembed_links' );
				if ( $autoembed_links ) {
					esc_html_e( 'Links to YouTube, Facebook, Twitter and other services inserted in the comment text will be automatically embedded.', 'dco-comment-attachment' );
				}

				/**
				 * Filters the autoembed links notification form element markup.
				 *
				 * @since 1.3.0
				 *
				 * @param string $markup HTML markup for the autoembed links
				 *                       notification list form element.
				 * @param bool $autoembed_links Whether the links is automatically embedded.
				 */
				$markup = apply_filters( 'dco_ca_form_element_autoembed_links_notification', '<br>' . ob_get_clean(), $autoembed_links );
				break;
		}

		/**
		 * Filters the form element markup.
		 *
		 * @since 1.1.1
		 *
		 * @param string $markup HTML markup for the form element.
		 */
		echo apply_filters( 'dco_ca_form_element', $markup ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Checks the attachment before posting a comment.
	 *
	 * @since 1.0.0
	 *
	 * @param array $commentdata Comment data.
	 * @return array Comment data on success.
	 */
	public function check_attachment( $commentdata ) {
		$field_name = $this->get_upload_field_name();

		if ( ! isset( $_FILES[ $field_name ] ) ) {
			return $commentdata;
		}

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$attachment = $_FILES[ $field_name ];
		// phpcs:enable
		$tmp_name   = $attachment['tmp_name'];
		$name       = $attachment['name'];
		$error_code = $attachment['error'];

		$upload_error = $this->get_upload_error( $error_code );
		if ( $upload_error ) {
			$this->display_error( $upload_error );
		}

		// Check that the file has been uploaded.
		if ( ! isset( $tmp_name ) || ! is_uploaded_file( $tmp_name ) ) {
			if ( $this->get_option( 'required_attachment' ) ) {
				$this->display_error( __( 'Attachment is required.', 'dco-comment-attachment' ) );
			} else {
				return $commentdata;
			}
		}

		// We need to do this check, because the maximum allowed upload file size in WordPress
		// can be less than the specified on the server.
		if ( $attachment['size'] > $this->get_max_upload_size() ) {
			$upload_error = $this->get_upload_error( 1 );
			$this->display_error( $upload_error );
		}

		$this->enable_filter_upload();
		$filetype = wp_check_filetype( $name );
		$this->disable_filter_upload();

		if ( ! $filetype['ext'] ) {
			$this->display_error( __( "WordPress doesn't allow this type of uploads.", 'dco-comment-attachment' ) );
		}

		return $commentdata;
	}

	/**
	 * Displays the text of the error uploading attachment when sending a comment.
	 *
	 * @since 1.0.0
	 *
	 * @param string $error The text of error uploading attachment.
	 */
	public function display_error( $error ) {
		if ( $error ) {
			$err_title = __( 'ERROR', 'dco-comment-attachment' );
			wp_die( '<p><strong>' . esc_html( $err_title ) . '</strong>: ' . esc_html( $error ) . '</p>', esc_html__( 'Comment Submission Failure', 'dco-comment-attachment' ), array( 'back_link' => true ) );
		}
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
	 * Saves attachment after comment is posted.
	 *
	 * @since 1.0.0
	 *
	 * @param int        $comment_id The comment ID.
	 * @param int|string $comment_approved 1 if the comment is approved, 0 if not,
	 *                                     'spam' if spam.
	 * @param array      $comment Comment data.
	 */
	public function save_attachment( $comment_id, $comment_approved, $comment ) {
		$field_name     = $this->get_upload_field_name();
		$attach_to_post = $this->get_option( 'attach_to_post' );

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$post_id = 0;
		if ( $attach_to_post ) {
			$post_id = $comment['comment_post_ID'];
		}

		$this->enable_filter_upload();
		$attachment_id = media_handle_upload( $field_name, $post_id );
		$this->disable_filter_upload();

		if ( ! is_wp_error( $attachment_id ) ) {
			$this->assign_attachment( $comment_id, $attachment_id );
		}
	}

	/**
	 * Displays an assigned attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param string $comment_content Optional. Text of the comment.
	 * @return string Text of the comment with an assigned attachment.
	 */
	public function display_attachment( $comment_content = '' ) {
		if ( ! $this->has_attachment() ) {
			return $comment_content;
		}

		$attachment_id      = $this->get_attachment_id();
		$attachment_content = $this->get_attachment_preview( $attachment_id );

		return $comment_content . $attachment_content;
	}

	/**
	 * Embeds links.
	 *
	 * @since 1.2.0
	 *
	 * @param string $comment_content Text of the comment.
	 * @return string Text of the comment with embedded links.
	 */
	public function autoembed_links( $comment_content ) {
		return $GLOBALS['wp_embed']->autoembed( $comment_content );
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
		add_filter( 'upload_mimes', array( $this, 'filter_upload_mimes' ) );
	}

	/**
	 * Disables filtering of the standard list of allowed mime types and file extensions.
	 *
	 * @since 1.1.0
	 */
	public function disable_filter_upload() {
		remove_filter( 'upload_mimes', array( $this, 'filter_upload_mimes' ) );
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

}
