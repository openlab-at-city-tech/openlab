<?php

namespace Gravity_Forms\Gravity_Forms_APC;

defined( 'ABSPATH' ) || die();

use GFAPI;
use GF_Advanced_Post_Creation;
use GFAddOn;
use Gravity_Forms\Gravity_Forms_APC\Helpers\Form_Population;
use GFFormsModel;
use GFCommon;
/**
 * Gravity Forms Advanced Post Creation Post Update Handler.
 *
 * This class acts as a wrapper for all things for updating a post.
 *
 * @since     1.0
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2021, Rocketgenius
 */
class Post_Update_Handler {
	// Flag to track if we're currently in the process of preparing read-only values.
	private $is_preparing_readonly_values = false;

	/**
	 * Instance of the APC addon object.
	 *
	 * @since 1.0
	 *
	 * @var GF_Advanced_Post_Creation
	 */
	protected $addon;

	/**
	 * Instance of Post_Update_Handler.
	 *
	 * @since 1.6.0
	 *
	 * @var Post_Update_Handler
	 */
	private static $_instance = null;

	/**
	 * The ID of the post being updated.
	 *
	 * @since 1.0
	 *
	 * @var integer|string
	 */
	private $post_id;

	/**
	 * The Post being updated.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $post;

	/**
	 * The feed being processed.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $feed;

	/**
	 * The entry associated with the post being updated.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $entry;

	/**
	 * The form object.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $form;

	/**
	 * Post_Update_Handler constructor.
	 *
	 * @since 1.0
	 *
	 * @param GF_Advanced_Post_Creation $addon   Instance of the APC addon object.
	 * @param integer|string            $post_id The ID of the post being updated.
	 * @param array                     $feed    The feed being processed.
	 * @param array                     $entry   The entry associated with the post being updated.
	 * @param array                     $form    The form object.
	 */
	public function __construct( $addon, $post_id = null, $feed = null, $entry = null, $form = null ) {
		$this->addon   = $addon;
		$this->post_id = $post_id;
		$this->feed    = $feed;
		$this->entry   = $entry;
		$this->form    = $form;
	}

	/**
	 * Get instance of this class.
	 *
	 * @since  1.6.0
	 * @access public
	 *
	 * @param GF_Advanced_Post_Creation $addon Instance of the APC addon object.
	 *
	 * @return Post_Update_Handler
	 */
	public static function get_instance( $addon ) {
		if ( null === self::$_instance ) {
			self::$_instance = new self( $addon );
		}

		return self::$_instance;
	}

	/**
	 * Initialize hooks for editing functionality.
	 *
	 * @since  1.6.0
	 * @access public
	 */
	public function init() {
		add_filter( 'gform_pre_render', array( $this, 'maybe_populate_form' ), 10, 1 );
		add_filter( 'gform_pre_validation', array( $this, 'prepare_readonly_values' ), 10, 1 );
		add_filter( 'gform_entry_post_save', array( $this, 'restore_readonly_values' ), 10, 2 );
		add_filter( 'gform_entry_id_pre_save_lead', array( $this, 'maybe_edit_existing_entry' ), 10, 2 );
		add_action( 'gform_after_submission', array( $this, 'maybe_create_update_post' ), 10, 2 );
	}

	/**
	 * Updates the post.
	 *
	 * @since 1.0
	 * @since 1.5 Updated to return the WP_error from wp_update_post().
	 *
	 * @return bool|\WP_Error
	 */
	public function update() {
		$addon = $this->addon;
		if ( ! $this->validate() ) {
			return false;
		}

		$addon->log_debug( __METHOD__ . '(): Running for post #' . $this->post_id );

		/**
		 * Allow custom actions to be performed before the post is updated.
		 *
		 * @since 1.0
		 *
		 * @param array $post  The post to be updated.
		 * @param array $feed  The feed being processed.
		 * @param array $entry The entry linked to the post.
		 */
		do_action( 'gform_advancedpostcreation_pre_update_post', $this->post, $this->feed, $this->entry );

		$post_ids = gform_get_meta( $this->entry['id'], $this->addon->get_slug() . '_post_id' );

		$media_before = $this->get_entry_current_media( $post_ids );

		$this->maybe_remove_old_media();

		$this->prepare_post_data();

		/**
		 * Allows modifying the post data before updating it.
		 *
		 * @since 1.0
		 *
		 * @param array $post  The post array being updated.
		 * @param array $feed  The feed being processed.
		 * @param array $entry The entry linked to the post.
		 */
		$this->post = apply_filters( 'gform_advancedpostcreation_update_post_data', $this->post, $this->feed, $this->entry );

		$result = wp_update_post( $this->post, true );

		if ( is_wp_error( $result ) ) {
			$addon->log_debug( __METHOD__ . '(): Error updating post: ' . $result->get_error_message() );
			$addon->add_feed_error( sprintf( esc_html__( 'Error updating post #%d: %s', 'gravityformsadvancedpostcreation' ), $this->post_id, $result->get_error_message() ), $this->feed, $this->entry, $this->form );

			return $result;
		}

		$addon->log_debug( __METHOD__ . '(): Post updated.' );
		$addon->add_note( rgar( $this->entry, 'id' ), sprintf( esc_html__( 'Post #%d updated.', 'gravityformsadvancedpostcreation' ), $this->post_id ), 'success' );

		$this->update_post_properties();

		$media_after = $addon->get_current_media();
		if ( $media_after != $media_before ) {
			$this->update_media( $media_after, $post_ids );
		}

		/**
		 * Allow custom actions to be performed after the post is updated.
		 *
		 * @since 1.3.3
		 *
		 * @param array $post  The post that was updated.
		 * @param array $feed  The feed that was processed.
		 * @param array $entry The entry used to update the post.
		 */
		do_action( 'gform_advancedpostcreation_post_update_post', $this->post, $this->feed, $this->entry );

		GFAPI::send_notifications( $this->form, $this->entry, 'post_edited' );

		return true;
	}

	/**
	 * Returns the current media in the entry that is attached to the current post.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function get_entry_current_media( $post_ids ) {

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $id ) {
				$post_feed_id = (int) rgar( $id, 'feed_id' );
				if ( $post_feed_id === (int) $this->feed['id'] ) {
					$this->addon->set_current_media( rgar( $id, 'media', array() ) );
					return $this->addon->get_current_media();
				}
			}
		}

		return array();
	}

	/**
	 * Updates the entry's media meta data with the new media uploaded to the post.
	 *
	 * @since 1.0
	 *
	 * @param array $new_media The new media files.
	 *
	 * @return boolean;
	 */
	protected function update_media( $new_media, $post_ids ) {
		if ( ! is_array( $post_ids ) ) {
			return false;
		}

		foreach ( $post_ids as &$id ) {
			if ( $this->post_id == $id['post_id'] ) {
				$id['media'] = $new_media;
				break;
			}
		}

		return gform_update_meta( $this->entry['id'], $this->addon->get_slug() . '_post_id', $post_ids );
	}

	/**
	 * Updates other post properties like meta, post thumbnail and taxonomies.
	 *
	 * @since 1.0
	 */
	protected function update_post_properties() {
		$meta_fields = rgars( $this->feed, 'meta/postMetaFields' );
		if ( $meta_fields ) {
			foreach ( $meta_fields as $meta_field ) {
				$meta_key = 'gf_custom' === $meta_field['key'] ? $meta_field['custom_key'] : $meta_field['key'];
				delete_post_meta( $this->post_id, $meta_key );
			}
		}
		$this->addon->maybe_set_post_thumbnail( $this->post_id, $this->feed, $this->entry, $this->form );
		$this->addon->maybe_handle_post_media( $this->post_id, $this->feed, $this->entry );
		$this->addon->maybe_set_post_meta( $this->post_id, $this->feed, $this->entry, $this->form );
		$this->addon->maybe_set_post_taxonomies( $this->post_id, $this->feed, $this->entry, $this->form );
	}

	/**
	 * Prepares the post with the new data before updating.
	 *
	 * @since 1.0
	 */
	protected function prepare_post_data() {
		$this->addon->set_post_author( null, $this->feed, $this->entry );
		$this->post['post_title']  = $this->addon->get_post_title( $this->feed, $this->entry, $this->form );
		$this->post                = $this->addon->set_post_data( $this->post, $this->feed, $this->entry, $this->form );
	}

	/**
	 * Validates the required data to update the post.
	 *
	 * @since 1.0
	 *
	 * @return boolean
	 */
	protected function validate() {
		$this->post = get_post( $this->post_id, 'ARRAY_A' );
		if (
			! is_array( $this->post ) || empty( $this->post )
			|| ! is_array( $this->feed ) || empty( $this->feed )
			|| ! is_array( $this->entry ) || empty( $this->entry )
			|| ! is_array( $this->form ) || empty( $this->form )
		) {
			$this->addon->log_error( 'Can not update post, Invalid data provided: ' . var_export( array_merge( $this->post, $this->feed, $this->entry, $this->form ), true ) );
			return false;
		}

		return true;
	}

	/**
	 * Deletes media created from files which have been deleted from the entry.
	 *
	 * @since 1.0
	 *
	 * @return array Current Media after removing old files.
	 *
	 */
	public function maybe_remove_old_media() {
		$media = $this->addon->get_current_media();
		if ( empty( $media ) ) {
			return array();
		}

		$files = $this->get_current_files();
		$dirty = false;

		foreach ( $media as $file_url => $media_id ) {
			if ( ! in_array( $file_url, $files ) ) {
				$this->addon->log_debug( __METHOD__ . '(): deleting: ' . $media_id );
				wp_delete_attachment( $media_id );
				unset( $media[ $file_url ] );

				$dirty = true;
			}
		}

		if ( $dirty ) {
			$this->addon->set_current_media( $media );
		}

		return $this->addon->get_current_media();
	}

	/**
	 * Returns an array of uploaded file URLs for the current entry.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected function get_current_files() {
		$fields = GFAPI::get_fields_by_type( $this->form, array( 'fileupload' ), true );
		$files  = array();

		if ( empty( $fields ) ) {
			return $files;
		}

		foreach ( $fields as $field ) {
			$value = rgar( $this->entry, strval( $field->id ) );
			if ( ! empty( $value ) ) {
				if ( $field->multipleFiles ) {
					$files = array_merge( $files, json_decode( $value, true ) );
				} else {
					$files[] = $value;
				}
			}
		}

		return $files;
	}

	/**
	 * Generates a secure URL for editing a form entry.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param int $entry_id The ID of the entry to edit.
	 * @return string URL for editing the entry or empty string if unable to generate URL.
	 */
	public function get_edit_entry_link( $entry_id ) {
		// Get the entry.
		$entry = GFAPI::get_entry( $entry_id );
		if ( is_wp_error( $entry ) ) {
			return '';
		}

		// Check if the user is logged in and is the entry creator.
		if ( ! is_user_logged_in() || get_current_user_id() != absint( $entry['created_by'] ) ) {
			return '';
		}

		// Get the created posts for this entry.
		$created_posts = gform_get_meta( $entry_id, $this->addon->get_slug() . '_post_id' );
		if ( empty( $created_posts ) ) {
			return '';
		}

		// Get the post ID for the first created post.
		$post_id = rgar( $created_posts[0], 'post_id' );
		if ( ! $post_id ) {
			return '';
		}

		// Get feed ID from post meta.
		$feed_id = get_post_meta( $post_id, '_' . $this->addon->get_slug() . '_feed_id', true );
		if ( ! $feed_id ) {
			return '';
		}

		// Get the feed data.
		$feed = $this->addon->get_feed( $feed_id );
		if ( ! $feed ) {
			return '';
		}

		if ( $this->entry_has_payment_feed( $entry_id ) ) {
			return '';
		}

		// Check if editing is enabled for this feed.
		if ( ! rgars( $feed, 'meta/enable_editing' ) || 'logged-in-user' !== rgars( $feed, 'meta/postAuthor' ) ) {
			return '';
		}

		// Get the edit page ID from the feed.
		$edit_post_page_id = rgars( $feed, 'meta/edit_post_page' );
		if ( ! $edit_post_page_id ) {
			return '';
		}

		// Get the edit page URL.
		$edit_post_page_url = get_permalink( $edit_post_page_id );
		if ( ! $edit_post_page_url ) {
			return '';
		}

		// Add the entry ID and a security nonce to the URL.
		$edit_post_page_url = add_query_arg( 'entry_id', $entry_id, $edit_post_page_url );
		$edit_nonce         = wp_create_nonce( 'apc-gform_advancedpostcreation_edit_entry' );

		return add_query_arg( 'apc_edit_nonce', $edit_nonce, $edit_post_page_url );
	}

	/**
	 * Checks if the current entry is editable based on URL parameters.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param int $form_id ID of the form being checked.
	 * @return bool True if entry is editable, false otherwise.
	 */
	public function entry_is_editable( $form_id ) {
		// Check if we have the necessary URL parameters.
		if ( ! rgget( 'entry_id' ) || ! rgget( 'apc_edit_nonce' ) ) {
			return false;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( rgget( 'apc_edit_nonce' ), 'apc-gform_advancedpostcreation_edit_entry' ) ) {
			return false;
		}

		// Get the entry.
		$entry = GFAPI::get_entry( rgget( 'entry_id' ) );
		if ( is_wp_error( $entry ) ) {
			return false;
		}

		// Verify the form ID matches.
		if ( $entry['form_id'] != $form_id ) {
			return false;
		}

		// Verify the user has permission to edit this entry (must be the creator).
		if ( get_current_user_id() != absint( $entry['created_by'] ) ) {
			return false;
		}

		// Check if the entry has associated posts.
		$created_posts = gform_get_meta( $entry['id'], $this->addon->get_slug() . '_post_id' );
		if ( empty( $created_posts ) ) {
			return false;
		}

		// Get the post ID for the first created post.
		$post_id = rgar( $created_posts[0], 'post_id' );
		if ( ! $post_id ) {
			return false;
		}

		// Get feed ID from post meta.
		$feed_id = get_post_meta( $post_id, '_' . $this->addon->get_slug() . '_feed_id', true );
		if ( ! $feed_id ) {
			return false;
		}

		// Get the feed data.
		$feed = $this->addon->get_feed( $feed_id );
		if ( ! $feed ) {
			return false;
		}

		// Check if editing is enabled for this feed.
		if ( ! rgars( $feed, 'meta/enable_editing' ) || 'logged-in-user' !== rgars( $feed, 'meta/postAuthor' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the editable fields array from a feed.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param array $feed The feed to get editable fields from.
	 * @return array Array of editable field IDs.
	 */
	private function get_editable_fields_from_feed( $feed ) {
		$editable_fields = array();

		// Return empty array if no feed or no meta.
		if ( empty( $feed ) || ! isset( $feed['meta'] ) ) {
			return $editable_fields;
		}

		// Get editable fields from feed meta.
		if ( isset( $feed['meta']['editable_fields'] ) ) {
			$editable_fields = $feed['meta']['editable_fields'];
		}

		/**
		 * Filter to modify the list of editable fields from the feed settings.
		 *
		 * @since 1.6.0
		 *
		 * @param array $editable_fields Array of field IDs that can be edited.
		 * @param array $feed The feed being processed.
		 */
		$editable_fields = apply_filters( 'gform_advancedpostcreation_editable_fields', $editable_fields, $feed );

		return $editable_fields;
	}

	/**
	 * Populates a form with data from an existing entry if edit URL parameters are present.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $form The form object being displayed.
	 * @return array The modified form object.
	 */
	public function maybe_populate_form( $form ) {
		// Check if this form is being viewed for editing.
		if ( ! $this->entry_is_editable( $form['id'] ) ) {
			return $form;
		}

		// Get the entry.
		$entry_id = rgget( 'entry_id' );
		$entry    = GFAPI::get_entry( $entry_id );
		if ( is_wp_error( $entry ) ) {
			return $form;
		}

		// Get post ID(s) associated with this entry.
		$created_posts = gform_get_meta( $entry['id'], $this->addon->get_slug() . '_post_id' );
		if ( empty( $created_posts ) ) {
			return $form;
		}

		// Get the feed for this post to determine editable fields.
		$post_id = $created_posts[0]['post_id'];
		$feed_id = get_post_meta( $post_id, '_' . $this->addon->get_slug() . '_feed_id', true );
		$feed    = $this->addon->get_feed( $feed_id );

		// Get editable fields from the feed.
		$editable_fields = $this->get_editable_fields_from_feed( $feed );

		$form_id = $form['id'];

		// Use our Form_Population class to populate the form with entry values.
		$values = Form_Population::get_population_values_from_entry( $form, $entry );
		$form   = Form_Population::do_population( $form, $values, $editable_fields );

		$readonly_field_ids = [];
		foreach ( $form['fields'] as $field ) {
			if ( isset( $field->cssClass ) && strpos( $field->cssClass, Form_Population::READONLY_CLASS ) !== false ) {
				$readonly_field_ids[] = $field->id;
			}
		}
		$readonly_field_ids_json = json_encode( $readonly_field_ids );

		// Inject the hidden fields with the post id and feed id AND the readonly data attribute.
		// We do it this way instead of adding an actual GF_Field_Hidden field to the form
		// because this way we don't have to deal with a random field ID later.
		add_filter( 'gform_get_form_filter', function ( $form_string, $form ) use ( $form_id, $post_id, $feed_id, $readonly_field_ids_json ) {
			if ( $form['id'] !== $form_id ) {
				return $form_string;
			}
			$hidden_fields = sprintf(
				'<input type="hidden" name="apc_edit_post_id" value="%d"><input type="hidden" name="apc_edit_feed_id" value="%d">',
				intval( $post_id ),
				intval( $feed_id )
			);
			// Add the disabled fields data attribute to the form tag.
			$form_tag_replacement = sprintf( '<form data-apc-readonly-fields=\'%s\' ', esc_attr( $readonly_field_ids_json ) );
			$form_string          = str_replace( '<form ', $form_tag_replacement, $form_string );

			return str_replace( '</form>', $hidden_fields . '</form>', $form_string );
		}, 10, 2);

		// Change the submit button text for editing.
		$form['button']['text'] = esc_html__( 'Update Post', 'gravityformsadvancedpostcreation' );

		return $form;
	}

	/**
	 * Updates an existing entry instead of creating a new one.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param int|null $entry_id The entry ID that would be created, or null to create a new entry.
	 * @param array $form The form object.
	 * @return int|null The entry ID to use.
	 */
	public function maybe_edit_existing_entry( $entry_id, $form ) {
		// If we don't have an entry ID in the URL, proceed with creating a new entry.
		if ( ! rgget( 'entry_id' ) || ! rgget( 'apc_edit_nonce' ) ) {
			return $entry_id;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( rgget( 'apc_edit_nonce' ), 'apc-gform_advancedpostcreation_edit_entry' ) ) {
			return $entry_id;
		}

		// Get the original entry ID from the URL.
		$original_entry_id = rgget( 'entry_id' );

		// Verify the entry exists.
		$entry = GFAPI::get_entry( $original_entry_id );
		if ( is_wp_error( $entry ) ) {
			return $entry_id;
		}

		// Verify the user has permission to edit this entry.
		if ( get_current_user_id() != absint( $entry['created_by'] ) ) {
			return $entry_id;
		}

		// This is an edit - return the original entry ID to update instead of creating a new one.
		return $original_entry_id;
	}

	/**
	 * Restores readonly field values in the entry after it has been saved.
	 * Also ensures file upload values are preserved when no new file is uploaded.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $entry The entry that was created.
	 * @param array $form The form object.
	 * @return array Modified entry with preserved readonly values.
	 */
	public function restore_readonly_values( $entry, $form ) {
		// If not processing an edit, return unchanged.
		if ( ! rgpost( 'apc_edit_post_id' ) ) {
			return $entry;
		}

		$entry_id       = $entry['id'];
		$fields_updated = false;

		/**
		 * Process readonly fields to preserve their original values.
		 */
		if ( ! empty( $form['gform_apc_readonly_values'] ) ) {
			foreach ( $form['gform_apc_readonly_values'] as $field_id => $value ) {
				// Skip file URL tags (handled separately).
				$is_file_url_tag = strpos( $field_id, '_file_url' ) !== false;
				if ( $is_file_url_tag ) {
					continue;
				}

				// Only update entry if value exists and is different from current.
				$has_value    = ! \GFCommon::is_empty_array( $value );
				$needs_update = $has_value && rgar( $entry, $field_id ) != $value;

				if ( $needs_update ) {
					// Use GFAPI to update the entry values directly.
					\GFAPI::update_entry_field( $entry_id, $field_id, $value );
					$fields_updated = true;
				}
			}
		}

		/**
		 * Handle file upload fields preservation.
		 * This ensures uploaded files are properly maintained when:
		 * 1. A field is read-only and should not change.
		 * 2. No new file was uploaded but existing files should be preserved.
		 */
		foreach ( $form['fields'] as $field ) {
			// Only process file upload fields.
			if ( $field->type !== 'fileupload' ) {
				continue;
			}

			$field_id        = $field->id;
			$new_value       = rgar( $entry, $field_id );
			$preserved_value = null;

			// Process based on single vs multiple file upload fields.
			if ( ! $field->multipleFiles ) {
				// Single file upload field.
				$this->process_single_file_upload_field( $field_id, $new_value, $form, $preserved_value );
			} else {
				// Multiple file upload field.
				$this->process_multiple_file_upload_field( $field_id, $new_value, $form, $preserved_value );
			}

			// If we have a preserved value, update the entry.
			if ( $preserved_value !== null ) {
				\GFAPI::update_entry_field( $entry_id, $field_id, $preserved_value );
				$fields_updated = true;
			}
		}

		// Re-fetch the entry if fields were updated to get the current values.
		if ( $fields_updated ) {
			$entry = \GFAPI::get_entry( $entry_id );
		}

		return $entry;
	}

	/**
	 * Process a single file upload field for value preservation.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param int $field_id The field ID.
	 * @param string $new_value Current value from entry.
	 * @param array $form Form object with readonly values.
	 * @param mixed &$preserved_value Output parameter - will be set if value should be preserved.
	 */
	private function process_single_file_upload_field( $field_id, $new_value, $form, &$preserved_value ) {
		// Check for hidden field values from custom implementation.
		$hidden_field_name = "gform_apc_file_{$field_id}";
		$hidden_file       = rgpost( $hidden_field_name );

		$has_new_upload  = ! empty( $new_value );
		$has_hidden_file = ! empty( $hidden_file );

		// If no new upload but we have hidden file, use it.
		if ( ! $has_new_upload && $has_hidden_file ) {
			$preserved_value = $hidden_file;
			return;
		}

		// If field is read-only, try to use readonly value.
		$is_readonly_field = isset( $form['gform_apc_readonly_values'][ $field_id ] );
		if ( $is_readonly_field ) {
			$readonly_value     = $form['gform_apc_readonly_values'][ $field_id ];
			$has_readonly_value = ! empty( $readonly_value );

			if ( $has_readonly_value ) {
				// Format appropriately for single file field.
				if ( is_array( $readonly_value ) && ! empty( $readonly_value ) ) {
					$preserved_value = reset( $readonly_value );
				} else {
					$preserved_value = $readonly_value;
				}
			}
		}
	}

	/**
	 * Process a multiple file upload field for value preservation.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param int $field_id The field ID.
	 * @param string $new_value Current value from entry (JSON).
	 * @param array $form Form object with readonly values.
	 * @param mixed &$preserved_value Output parameter - will be set if value should be preserved.
	 */
	private function process_multiple_file_upload_field( $field_id, $new_value, $form, &$preserved_value ) {
		// Check for hidden field values containing multiple file URLs.
		$hidden_field_name = "gform_apc_files_{$field_id}";
		$hidden_files      = rgpost( $hidden_field_name );

		$has_new_uploads = ! empty( $new_value );
		$has_hidden_files = ! empty( $hidden_files );

		// Handle different scenarios for multi-file fields.
		if ( ! $has_new_uploads && $has_hidden_files ) {
			// No new uploads, use existing files from hidden field.
			$decoded_files = json_decode( stripslashes( $hidden_files ), true );
			if ( is_array( $decoded_files ) ) {
				$preserved_value = json_encode( $decoded_files );
			}
		} elseif ( $has_new_uploads && $has_hidden_files ) {
			// Has new uploads, merge with existing files.
			$new_files = json_decode( $new_value, true );
			$old_files = json_decode( stripslashes( $hidden_files ), true );

			if ( is_array( $new_files ) && is_array( $old_files ) ) {
				$merged_files    = array_merge( $old_files, $new_files );
				$preserved_value = json_encode( $merged_files );
			}
		}

		// If field is read-only, try to use readonly value (highest priority).
		$is_readonly_field = isset( $form['gform_apc_readonly_values'][ $field_id ] );
		if ( $is_readonly_field ) {
			$readonly_value     = $form['gform_apc_readonly_values'][ $field_id ];
			$has_readonly_value = ! empty( $readonly_value );

			if ( $has_readonly_value ) {
				// Format appropriately for multiple file field.
				if ( is_array( $readonly_value ) ) {
					$preserved_value = json_encode( $readonly_value );
				} else {
					$preserved_value = $readonly_value;
				}
			}
		}
	}

	/**
	 * Identifies and caches readonly values during form validation for later preservation.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $form The form being validated.
	 * @return array The form object.
	 */
	public function prepare_readonly_values( $form ) {
		// Guard against recursive calls.
		if ( $this->is_preparing_readonly_values ) {
			return $form;
		}

		// Set flag to prevent recursion.
		$this->is_preparing_readonly_values = true;

		// Process only for entry edit mode with a post ID.
		$post_id = rgpost( 'apc_edit_post_id' );
		if ( ! $post_id ) {
			$this->is_preparing_readonly_values = false;
			return $form;
		}

		// Get feed ID from post data.
		$feed_id = rgpost( 'apc_edit_feed_id' );
		if ( ! $feed_id ) {
			$this->is_preparing_readonly_values = false;
			return $form;
		}

		// Get feed details to find editable fields.
		$feed = $this->addon->get_feed( $feed_id );
		if ( ! $feed ) {
			$this->is_preparing_readonly_values = false;
			return $form;
		}

		// Get the list of editable fields.
		$editable_fields = $this->get_editable_fields_from_feed( $feed );
		if ( empty( $editable_fields ) ) {
			$this->is_preparing_readonly_values = false;
			return $form;
		}

		// Get original entry from URL.
		$entry_id = rgget( 'entry_id' );
		$entry    = GFAPI::get_entry( $entry_id );
		if ( is_wp_error( $entry ) ) {
			$this->is_preparing_readonly_values = false;
			return $form;
		}

		// Create container for readonly values if needed.
		if ( ! isset( $form['gform_apc_readonly_values'] ) ) {
			$form['gform_apc_readonly_values'] = array();
		}

		// Loop through fields and preserve readonly values.
		foreach ( $form['fields'] as $field ) {
			// Skip fields that are editable.
			if ( in_array( (string) $field->id, $editable_fields ) ) {
				continue;
			}

			// For read-only fields, store original values.
			$field_id       = $field->id;
			$original_value = rgar( $entry, $field_id );

			// Store in readonly values array.
			$form['gform_apc_readonly_values'][ $field_id ] = $original_value;
		}

		$this->is_preparing_readonly_values = false;
		return $form;
	}

	/**
	 * Returns true if the entry has any active payment feeds.
	 *
	 * @since 1.6.0
	 *
	 * @param int $entry_id
	 * @return bool
	 */
	private function entry_has_payment_feed( $entry_id ) {

		$entry = GFAPI::get_entry( $entry_id );
		if ( is_wp_error( $entry ) || empty( $entry['form_id'] ) ) {
			return false;
		}

		$form_id = (int) $entry['form_id'];

		foreach ( GFAddOn::get_registered_addons() as $class ) {
			if ( ! is_string( $class ) || ! class_exists( $class ) || ! is_subclass_of( $class, 'GFPaymentAddOn' ) ) {
				continue;
			}
			$addon = is_callable( array( $class, 'get_instance' ) ) ? $class::get_instance() : null;
			if ( ! $addon || ! method_exists( $addon, 'get_slug' ) ) {
				continue;
			}
			$slug = $addon->get_slug();

			$feeds = GFAPI::get_feeds( null, $form_id, $slug );
			if ( ! is_wp_error( $feeds ) && ! empty( $feeds ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Updates the post after submission if the entry contains a hidden post ID field.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $entry The entry after form submission.
	 * @param array $form The form object.
	 */
	public function maybe_create_update_post( $entry, $form ) {
		// Only process entries with post ID.
		$post_id = rgpost( 'apc_edit_post_id' );
		if ( ! $post_id ) {
			return;
		}

		// Verify post exists
		$post = get_post( $post_id );
		if ( ! $post ) {
			$this->addon->log_error( __METHOD__ . '(): Post not found: ' . $post_id );
			return;
		}

		// Verify user owns the post
		$current_user_id = get_current_user_id();
		if ( $post->post_author != $current_user_id ) {
			$this->addon->log_error( __METHOD__ . '(): User ' . $current_user_id . ' attempted to edit post ' . $post_id . ' owned by ' . $post->post_author );
			return;
		}

		// Get feed ID from post data.
		$feed_id = rgpost( 'apc_edit_feed_id' );
		if ( ! $feed_id ) {
			return;
		}

		// Get the feed.
		$feed = $this->addon->get_feed( $feed_id );
		if ( empty( $feed ) ) {
			return;
		}

		// Check if we should update the post based on this entry.
		$entry_id = rgar( $entry, 'id' );
		$post_ids = gform_get_meta( $entry_id, $this->addon->get_slug() . '_post_id' );

		// If we don't have post IDs meta yet, check if we should create it.
		if ( empty( $post_ids ) ) {
			// Create a new post meta entry linking this entry to the post.
			$post_ids   = array();
			$post_ids[] = array(
				'post_id' => $post_id,
				'feed_id' => $feed_id,
			);

			// Save the post meta.
			gform_update_meta( $entry_id, $this->addon->get_slug() . '_post_id', $post_ids );
		}

		// Create a Post_Update_Handler to update the post.
		$update_handler = new Post_Update_Handler( $this->addon, $post_id, $feed, $entry, $form );
		$update_handler->update();
	}
}
