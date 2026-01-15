<?php

namespace Gravity_Forms\Gravity_Forms_APC;

defined( 'ABSPATH' ) || die();

use GFAPI;
use GF_Advanced_Post_Creation;
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

	/**
	 * Instance of the APC addon object.
	 *
	 * @since 1.0
	 *
	 * @var GF_Advanced_Post_Creation
	 */
	protected $addon;

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
	public function __construct( $addon, $post_id, $feed, $entry, $form ) {
		$this->addon   = $addon;
		$this->post_id = $post_id;
		$this->feed    = $feed;
		$this->entry   = $entry;
		$this->form    = $form;
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
		$this->post['post_status'] = $this->feed['meta']['postStatus'];
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

}
