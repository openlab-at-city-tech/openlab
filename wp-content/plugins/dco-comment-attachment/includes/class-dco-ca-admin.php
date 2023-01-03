<?php
/**
 * Admin functions: DCO_CA_Admin class
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
 * Class with admin functions.
 *
 * @since 1.0.0
 *
 * @see DCO_CA_Base
 */
class DCO_CA_Admin extends DCO_CA_Base {

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

		add_filter( 'comment_row_actions', array( $this, 'add_comment_action_links' ), 10, 2 );
		add_action( 'admin_action_deletecommentattachment', array( $this, 'delete_attachment_action' ) );
		add_filter( 'bulk_actions-edit-comments', array( $this, 'add_comments_bulk_actions' ) );
		add_action( 'admin_action_deleteattachment', array( $this, 'delete_attachment_bulk_action' ) );
		add_filter( 'ngettext', array( $this, 'show_bulk_action_message' ), 10, 5 );
		add_filter( 'removable_query_args', array( $this, 'add_removable_query_args' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_delete_attachment', array( $this, 'delete_attachment_ajax' ) );
		add_action( 'add_meta_boxes_comment', array( $this, 'add_attachment_metabox' ) );
		add_action( 'edit_comment', array( $this, 'update_attachment' ) );
		add_action( 'rest_insert_comment', array( $this, 'update_rest_api_attachment' ), 10, 3 );
		add_filter( 'plugin_action_links_' . DCO_CA_BASENAME, array( $this, 'add_plugin_links' ) );
		add_filter( 'comment_notification_text', array( $this, 'add_attachments_to_new_comment_email' ), 10, 2 );
		add_filter( 'comment_moderation_text', array( $this, 'add_attachments_to_new_comment_email' ), 10, 2 );

		if ( $this->get_option( 'delete_with_comment' ) ) {
			add_action( 'delete_comment', array( $this, 'delete_attachment' ) );
		}
	}

	/**
	 * Adds additional comment action links.
	 *
	 * @since 1.0.0
	 *
	 * @param array      $actions An array of comment actions.
	 * @param WP_Comment $comment The comment object.
	 * @return array An array with standard comment actions
	 *               and attachment actions if attachment exists.
	 */
	public function add_comment_action_links( $actions, $comment ) {
		if ( $this->has_attachment() ) {
			$comment_id = (int) $comment->comment_ID;
			$nonce      = wp_create_nonce( "delete-comment-attachment_$comment_id" );

			$del_attach_nonce = esc_html( '_wpnonce=' . $nonce );
			$url              = esc_url( "comment.php?c=$comment_id&action=deletecommentattachment&$del_attach_nonce" );

			$title                              = esc_html__( 'Delete Attachment', 'dco-comment-attachment' );
			$actions['deletecommentattachment'] = "<a href='$url' class='dco-del-attachment' data-id='$comment_id' data-nonce='$nonce'>$title</a>";
		}

		return $actions;
	}

	/**
	 * Handles a request to delete an attachment on the comments page.
	 *
	 * @since 1.0.0
	 */
	public function delete_attachment_action() {
		$comment_id = isset( $_GET['c'] ) ? (int) $_GET['c'] : 0;

		check_admin_referer( 'delete-comment-attachment_' . $comment_id );

		if ( ! function_exists( 'comment_footer_die' ) ) {
			require_once ABSPATH . 'wp-admin/includes/comment.php';
		}

		$comment = get_comment( $comment_id );

		// Check the comment exists.
		if ( ! $comment ) {
			comment_footer_die( esc_html__( 'Invalid comment ID.', 'dco-comment-attachment' ) . sprintf( ' <a href="%s">' . esc_html__( 'Go back', 'dco-comment-attachment' ) . '</a>.', 'edit-comments.php' ) );
		}

		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			comment_footer_die( esc_html__( 'Sorry, you are not allowed to edit comments on this post.', 'dco-comment-attachment' ) );
		}

		if ( ! $this->has_attachment( $comment_id ) ) {
			comment_footer_die( esc_html__( 'The comment has no attachment.', 'dco-comment-attachment' ) . sprintf( ' <a href="%s">' . esc_html__( 'Go back', 'dco-comment-attachment' ) . '</a>.', 'edit-comments.php' ) );
		}

		$delete = $this->get_option( 'delete_attachment_action' );
		if ( ! $this->delete_attachment( $comment_id, $delete ) ) {
			comment_footer_die( esc_html__( 'An error occurred while deleting the attachment.', 'dco-comment-attachment' ) );
		}

		$redir = admin_url( 'edit-comments.php?p=' . (int) $comment->comment_post_ID );
		$redir = add_query_arg( array( 'attachmentdeleted' => 1 ), $redir );

		wp_safe_redirect( $redir );
		exit();
	}

	/**
	 * Adds additional bulk actions.
	 *
	 * @since 2.4.0
	 *
	 * @param array $actions An array of the available bulk actions.
	 * @return array An array with standard bulk actions
	 *               and attachment bulk actions.
	 */
	public function add_comments_bulk_actions( $actions ) {
		$actions['deleteattachment'] = __( 'Delete Attachment', 'dco-comment-attachment' );

		return $actions;
	}

	/**
	 * Handles a bulk action to delete comment attachments on the comments page.
	 *
	 * @since 2.4.0
	 */
	public function delete_attachment_bulk_action() {
		check_admin_referer( 'bulk-comments' );

		if ( isset( $_REQUEST['delete_comments'] ) && is_array( $_REQUEST['delete_comments'] ) ) {
			$comment_ids = array_map( 'absint', $_REQUEST['delete_comments'] );
		} else {
			return;
		}

		$redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids', 'deletedattachment' ), wp_get_referer() );

		wp_defer_comment_counting( true );

		$count = 0;
		foreach ( $comment_ids as $comment_id ) {
			if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
				continue;
			}

			$comment = get_comment( $comment_id );
			if ( ! $comment ) {
				continue;
			}

			$delete = $this->get_option( 'delete_attachment_action' );
			if ( $this->delete_attachment( $comment_id, $delete ) ) {
				$count ++;
			}
		}

		wp_defer_comment_counting( false );

		$redirect_to = add_query_arg( 'deletedattachment', $count, $redirect_to );

		// @see DCO_CA_Admin::show_bulk_action_message() for details.
		$redirect_to = add_query_arg( 'approved', $count, $redirect_to );

		wp_safe_redirect( $redirect_to );
		exit;
	}

	/**
	 * Shows bulk action updated message.
	 *
	 * @since 2.4.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param string $number      The number to compare against to use either the singular or plural form.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return string Filtered translated text.
	 */
	public function show_bulk_action_message( $translation, $single, $plural, $number, $domain ) {
		/**
		 * There is no hook in WordPress to add updated message for custom comments bulk action.
		 * So we override the approval message if attachment deletion was triggered.
		 */
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_REQUEST['deletedattachment'] ) || ! $_REQUEST['deletedattachment'] ) {
			return $translation;
		}
		// phpcs:enable

		if ( '%s comment approved.' === $single && '%s comments approved.' === $plural && 'default' === $domain ) {
			/* translators: %s: Number of comments. */
			$translation = sprintf( _n( 'Attachments deleted from %s comment.', 'Attachments deleted from %s comments.', $number, 'dco-comment-attachment' ), $number );
		}

		return $translation;
	}

	/**
	 * Adds single-use URL parameters.
	 *
	 * @since 2.4.0
	 *
	 * @param array $removable_query_args An array of query variable names to remove from the URL.
	 * @return array An array of query variable names to remove from the URL.
	 */
	public function add_removable_query_args( $removable_query_args ) {
		$removable_query_args[] = 'deletedattachment';

		return $removable_query_args;
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		// Only on comment edit page.
		if ( 'comment.php' === $hook_suffix ) {
			wp_enqueue_media();
		}

		// Only on comments page and comment edit page.
		if ( in_array( $hook_suffix, array( 'edit-comments.php', 'comment.php', 'settings_page_dco-comment-attachment' ), true ) ) {
			wp_enqueue_script( 'dco-comment-attachment-admin', DCO_CA_URL . 'assets/dco-comment-attachment-admin.js', array( 'jquery' ), DCO_CA_VERSION, true );

			$strings = array(
				'set_attachment_title'      => esc_attr__( 'Set Comment Attachment', 'dco-comment-attachment' ),
				'add_attachment_label'      => esc_attr__( 'Add Attachment', 'dco-comment-attachment' ),
				'replace_attachment_label'  => esc_attr__( 'Replace Attachment', 'dco-comment-attachment' ),
				'delete_attachment_action'  => $this->get_option( 'delete_attachment_action' ),
				'delete_attachment_confirm' => esc_attr__( 'This action will delete the attachment from the Media Library and cannot be undone. Continue?', 'dco-comment-attachment' ),
				'show_all'                  => esc_attr__( 'Show all', 'dco-comment-attachment' ),
				'show_less'                 => esc_attr__( 'Show less', 'dco-comment-attachment' ),
			);
			wp_localize_script( 'dco-comment-attachment-admin', 'dcoCA', $strings );

			wp_enqueue_style( 'dco-comment-attachment-admin', DCO_CA_URL . 'assets/dco-comment-attachment-admin.css', array(), DCO_CA_VERSION );
		}
	}

	/**
	 * Handles an ajax request to delete an attachment on the comments page.
	 *
	 * @since 1.0.0
	 */
	public function delete_attachment_ajax() {
		$comment_id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		check_ajax_referer( "delete-comment-attachment_$comment_id" );

		$comment = get_comment( $comment_id );

		// Check the comment exists.
		if ( ! $comment ) {
			/* translators: %d: The comment ID */
			wp_send_json_error( new WP_Error( 'invalid_comment', sprintf( esc_html__( 'Comment %d does not exist', 'dco-comment-attachment' ), $comment_id ) ) );
		}

		if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
			wp_send_json_error( new WP_Error( 'invalid_capability', esc_html__( 'Sorry, you are not allowed to edit comments on this post.', 'dco-comment-attachment' ) ) );
		}

		if ( ! $this->has_attachment( $comment_id ) ) {
			wp_send_json_error( new WP_Error( 'attachment_not_exists', esc_html__( 'The comment has no attachment.', 'dco-comment-attachment' ) ) );
		}

		$delete = $this->get_option( 'delete_attachment_action' );
		if ( ! $this->delete_attachment( $comment_id, $delete ) ) {
			wp_send_json_error( new WP_Error( 'deleting_error', esc_html__( 'An error occurred while deleting the attachment.', 'dco-comment-attachment' ) ) );
		}

		wp_send_json_success();
	}

	/**
	 * Adds the attachment metabox for the comment editing page.
	 *
	 * @since 1.0.0
	 */
	public function add_attachment_metabox() {
		add_meta_box( 'dco-comment-attachment', esc_html__( 'Attachments', 'dco-comment-attachment' ), array( $this, 'render_attachment_metabox' ), 'comment', 'normal' );
	}

	/**
	 * Renders the attachment metabox on the comment editing page.
	 *
	 * @since 1.0.0
	 */
	public function render_attachment_metabox() {
		if ( $this->has_attachment() ) :
			$attachment_id = $this->get_attachment_id();
			foreach ( (array) $attachment_id as $attach_id ) :
				?>
				<div class="dco-attachment-wrap">
					<?php
					echo $this->get_attachment_preview( $attach_id ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<div class="dco-attachment-notice dco-hidden"><?php echo wp_kses_data( __( 'Update the comment to see a preview of <a href="#" target="_blank">the selected attachment</a>.', 'dco-comment-attachment' ) ); ?></div>
					<div class="dco-attachment-actions">
						<a href="#" class="button dco-set-attachment"><?php esc_html_e( 'Replace Attachment', 'dco-comment-attachment' ); ?></a>
						<a href="#" class="dco-remove-attachment"><?php esc_html_e( 'Remove Attachment', 'dco-comment-attachment' ); ?></a>
					</div>
					<input type="hidden" name="dco_attachment_id[]" class="dco-attachment-id" value="<?php echo (int) $attach_id; ?>">
				</div>
				<?php
			endforeach;
		endif;
		?>
		<div class="dco-attachment-wrap">
			<div class="dco-attachment-notice dco-hidden"><?php echo wp_kses_data( __( 'Update the comment to see a preview of <a href="#" target="_blank">the selected attachment</a>.', 'dco-comment-attachment' ) ); ?></div>
			<div class="dco-attachment-actions">
				<a href="#" class="button dco-set-attachment"><?php echo esc_html_e( 'Add Attachment', 'dco-comment-attachment' ); ?></a>
				<a href="#" class="dco-remove-attachment dco-hidden"><?php esc_html_e( 'Remove Attachment', 'dco-comment-attachment' ); ?></a>
			</div>
			<input type="hidden" name="dco_attachment_id[]" class="dco-attachment-id" value="">
		</div>
		<?php
	}

	/**
	 * Adds additional actions for the plugin on the plugins page in the admin panel.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions An array of plugin action links.
	 * @return array An array of plugin action links with additional plugin actions.
	 */
	public function add_plugin_links( $actions ) {
		array_unshift(
			$actions,
			sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url( 'options-general.php?page=dco-comment-attachment' ),
				esc_html__( 'Settings', 'dco-comment-attachment' )
			)
		);

		return $actions;
	}

	/**
	 * Updates the attachment after editing the comment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	public function update_attachment( $comment_id ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		if ( 'comment' !== get_current_screen()->id ) {
			return false;
		}

		check_admin_referer( 'update-comment_' . $comment_id );

		$attachment_id = isset( $_POST['dco_attachment_id'] ) ? array_map( 'intval', $_POST['dco_attachment_id'] ) : array();

		// We need to delete the last empty element, because it's used
		// as a placeholder in the attachments edit form.
		// @see DCO_CA_Admin::render_attachment_metabox.
		array_pop( $attachment_id );

		if ( $attachment_id ) {
			$this->assign_attachment( $comment_id, $attachment_id );
		} else {
			$this->unassign_attachment( $comment_id );
		}
	}

	/**
	 * Updates attachments after comment is updated via REST API.
	 *
	 * @since 2.3.0
	 *
	 * @param WP_Comment      $comment Inserted or updated comment object.
	 * @param WP_REST_Request $request Request object.
	 * @param bool            $creating True when creating a comment, false when updating.
	 */
	public function update_rest_api_attachment( $comment, $request, $creating ) {
		if ( $creating ) {
			return;
		}

		$attachment_id = isset( $request['dco_attachment_id'] ) ? array_map( 'intval', (array) $request['dco_attachment_id'] ) : array();
		if ( $attachment_id ) {
			$this->assign_attachment( $comment->comment_ID, $attachment_id );
		} else {
			$this->unassign_attachment( $comment->comment_ID );
		}
	}

	/**
	 * Deletes an assigned attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $comment_id The comment ID.
	 * @param bool $delete True to unassign and remove an attachment,
	 *                     false to unassign an attachment only.
	 * @return bool True on success, false on failure.
	 */
	public function delete_attachment( $comment_id, $delete = true ) {
		if ( ! $this->has_attachment( $comment_id ) ) {
			return false;
		}

		$attachment_id = $this->get_attachment_id( $comment_id );

		if ( $delete ) {
			$result = true;
			foreach ( (array) $attachment_id as $attach_id ) {
				if ( ! wp_delete_attachment( $attach_id ) ) {
					$result = false;
				}
			}
			return $result;
		}

		if ( ! $this->unassign_attachment( $comment_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Unassigns an attachment for the comment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $comment_id The comment ID.
	 * @return int|bool Meta ID on success, false on failure.
	 */
	public function unassign_attachment( $comment_id ) {
		$meta_key = $this->get_attachment_meta_key();
		return delete_comment_meta( $comment_id, $meta_key );
	}

	/**
	 * Adds links to attached attachments to the new comment notification email.
	 *
	 * @since 2.1.0
	 *
	 * @param string $notify_message The comment notification email text.
	 * @param int    $comment_id     Comment ID.
	 * @return string The comment notification email text with links to attached attachments.
	 */
	public function add_attachments_to_new_comment_email( $notify_message, $comment_id ) {
		$attachment_id = $this->get_attachment_id( $comment_id );
		if ( ! $attachment_id ) {
			return $notify_message;
		}

		$attachments_list = "\r\n" . __( 'Attached attachments:', 'dco-comment-attachment' );
		foreach ( (array) $attachment_id as $attach_id ) {
			$attachments_list .= "\r\n- " . wp_get_attachment_url( $attach_id );
		}

		return $notify_message . $attachments_list;
	}

}
