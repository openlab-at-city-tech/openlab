<?php
/**
 * The main Processor file that Link calls. Acts like a Controller and it will share jobs to sub processors based on several conditions.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors;

use WPMUDEV_BLC\App\Broken_Links_Actions\Link;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Replace_Link;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Unlink_Link;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Nofollow_Link;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Content_Processor;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Main
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Main extends Base {
	/**
	 * The object that will execute the specified action (replace|unlink|nofollow)
	 *
	 * @var Link_Processor|null
	 */
	protected $processor = null;

	/**
	 * Link object.
	 *
	 * @var Link
	 */
	protected Link $link;

	/**
	 * A Store array to store data in order to avoid repetitive queries.
	 *
	 * @var array
	 */
	protected $temp_store = array();

	/**
	 * Main constructor. Accepts a Link object to be able to execute the action on the given link.
	 *
	 * @param Link $link The Link object that contains all information about the link and the action to be executed.
	 * @throws \InvalidArgumentException If the provided Link object is null or not an instance of Link.
	 */
	public function __construct( $link ) {
		if ( null === $link ) {
			throw new \InvalidArgumentException( 'Link cannot be null.' );
		}

		if ( ! ( $link instanceof Link ) ) {
			throw new \InvalidArgumentException( 'Expected an instance of Link.' );
		}

		$this->link = $link;
	}

	/**
	 * Returns the Link object.
	 *
	 * @return Link
	 */
	public function get_link_object() {
		return $this->link;
	}

	/**
	 * Replaces url in post's content, meta and comments.
	 * Returns false if link not replaced anywhere, else true.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function execute_url_in_post( int $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$result = $this->init_processor( $post_id );

		if ( is_wp_error( $result ) ) {
			Utilities::log( 'BLC_LINK_ACTION_ERROR: ' . $result->get_error_message() );
			return false;
		}

		//Execute link in post content.
		$completed_in_posts = $this->execute_in_post_content( $post_id );

		//Execute link in post metas.
		$completed_in_meta = $this->execute_in_post_meta_values( $post_id );

		//Execute link in post comments.
		$completed_in_comments = $this->execute_in_post_comments( $post_id );

		// Execute link reusable blocks.
		$completed_in_reusable_blocks = $this->execute_in_post_reusable_blocks( $post_id );

		return $completed_in_posts || $completed_in_meta || $completed_in_comments || $completed_in_reusable_blocks;
	}

	/**
	 * Initializes the processor object based on the link action type and sets the post ID if applicable.
	 *
	 * @param int $post_id The post ID to set in the processor object if it has a post_id property.
	 * @return true|\WP_Error True on success or
	 *  a WP_Error if there was an issue determining the processor.
	 */
	public function init_processor( int $post_id ) {
		$this->processor = $this->determine_processor();

		if ( is_wp_error( $this->processor ) ) {
			return $this->processor;
		}

		if (
			$this->processor &&
			property_exists( $this->processor, 'post_id' )
		) {
			$this->processor->post_id = $post_id;
		}

		return true;
	}

	/**
	 * Runs only on post's content.
	 *
	 * @param int $post_id
	 * @param string $content
	 *
	 * @return bool
	 */
	public function execute_in_post_content( int $post_id = 0, string $content = '' ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$processor = $this->get_processor();
		if ( is_wp_error( $processor ) ) {
			return false;
		}

		$new_content = '';

		if ( empty( $content ) ) {
			if ( empty( $this->temp_store['posts'][ $post_id ]['content'] ) ) {
				$this->temp_store['posts'][ $post_id ]['content'] = get_post_field( 'post_content', $post_id );
			}

			$content = $this->temp_store['posts'][ $post_id ]['content'];
		}

		$link = $this->get_link_object();

		if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$new_content = $this->execute_post_blocks(
				$content,
				$link->get_link(),
				$link->get_new_link()
			);
		} else {
			$new_content = $processor->execute(
				$content,
				$link->get_link(),
				$link->get_new_link()
			);
		}

		$new_content = apply_filters( 'wpmudev_blc_link_action_post_content', $new_content, $post_id, $this->link );

		if ( $content !== $new_content ) {
			// At first, we are creating a new post revision.
			wp_save_post_revision( $post_id );

			// Then update post with new content.
			kses_remove_filters();

			$result = wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $new_content,
				)
			);
			kses_init_filters();

			return $result;
		}

		return false;
	}

	/**
	 * Runs the links action in post blocks.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @return string
	 */
	public function execute_post_blocks( string $content = '', string $link = '', string $new_link = '' ) {
		// No point running if content is empty or if there is no link to modify.
		if ( empty( $content ) || empty( $link ) ) {
			return $content;
		}

		$processor = $this->get_processor();
		if ( is_wp_error( $processor ) ) {
			return $content;
		}

		if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$blocks        = parse_blocks( $content );
			$parsed_blocks = array();

			if ( ! empty( $blocks ) ) {
				foreach ( $blocks as $block ) {
					$parsed_blocks[] = $processor->parse_block( $block, $link, $new_link );
				}
			}
		}

		return ! empty( $parsed_blocks ) ? serialize_blocks( $parsed_blocks ) : $content;
	}

	/**
	 * Executes in all post meta values by post id.
	 *
	 * @param int $post_id
	 *
	 * @return false|void
	 */
	public function execute_in_post_meta_values( int $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$processor = $this->get_processor();
		if ( is_wp_error( $processor ) ) {
			return false;
		}

		$link = $this->get_link_object();

		// The `wpmudev_blc_process_extensive` filter is returned in `Utilities::process_extensive()`.
		if ( Utilities::process_extensive() ) {
			global $wpdb;

			$query = $wpdb->prepare(
				"SELECT meta_key, meta_value as content FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_value LIKE %s",
				$post_id,
				'%' . $wpdb->esc_like( $link->get_link() ) . '%'
			);
			$metas = $wpdb->get_results( $query );

			if ( ! empty( $metas ) ) {

				$old_link = $link->get_link();
				$new_link = $link->get_new_link();

				foreach ( $metas as $meta ) {
					// The query returns the raw stored value. Decode it the way get_post_meta() would,
					// so the processor never edits serialized text directly: doing so leaves stale
					// string lengths behind, and the value can no longer be decoded.
					$content = $this->decode_meta_value( $meta->content );

					if ( is_wp_error( $content ) ) {
						// Undecodable, or holds objects. Leave the stored value untouched.
						continue;
					}

					$new_content = $this->execute_in_meta_value( $content, $processor, $old_link, $new_link );
					$new_content = apply_filters( 'wpmudev_blc_link_action_post_meta_content', $new_content, $post_id, $meta->meta_key, $this->link );

					if ( $new_content !== $content ) {
						return update_post_meta( $post_id, sanitize_key( $meta->meta_key ), $new_content );
					}
				}
			}
		} else {
			$supported_meta_keys = apply_filters(
				'wpmudev_blc_edit_link_post_meta_keys',
				array()
			);

			if ( empty( $supported_meta_keys ) ) {
				return false;
			}

			$old_link = $link->get_link();
			$new_link = $link->get_new_link();
			foreach ( $supported_meta_keys as $meta_key ) {
				$content = get_post_meta( $post_id, sanitize_key( $meta_key ), true );

				if ( is_string( $content ) ) {
					$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );
				} else {
					// If content is not a string, we cannot process it, so we return it as is.
					$new_content = $content;
				}
				$new_content = apply_filters( 'wpmudev_blc_link_action_post_meta_content', $new_content, $post_id, $meta_key, $this->link );

				if ( $new_content !== $content ) {
					return update_post_meta( $post_id, sanitize_key( $meta_key ), $new_content );
				}
			}
		}

		return false;
	}

	/**
	 * Decodes a raw post meta value as stored in the database.
	 *
	 * Mirrors what get_post_meta() returns, except that object creation is switched off.
	 * Meta values are editable from the post's Custom Fields box, so they must never be
	 * turned into live objects here.
	 *
	 * @param string $raw The raw `meta_value` column.
	 * @return mixed|\WP_Error The decoded value, or a WP_Error when the value cannot be
	 *                         decoded or contains an object.
	 */
	protected function decode_meta_value( string $raw ) {
		if ( ! is_serialized( $raw ) ) {
			return $raw;
		}

		$raw = trim( $raw );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Decoding is deliberate: a malformed value must not break the link action, and `allowed_classes` is false so no object is ever created.
		$value = @unserialize( $raw, array( 'allowed_classes' => false ) );

		if ( false === $value && 'b:0;' !== $raw ) {
			return new \WP_Error( 'blc-meta-decode-error', esc_html__( 'Meta value could not be decoded.', 'broken-link-checker' ) );
		}

		if ( $this->contains_object( $value ) ) {
			return new \WP_Error( 'blc-meta-decode-error', esc_html__( 'Meta value contains an object.', 'broken-link-checker' ) );
		}

		return $value;
	}

	/**
	 * Runs the processor on every string within a decoded meta value that holds the link.
	 *
	 * Strings that do not hold the link are skipped so the processor cannot reformat them.
	 *
	 * @param mixed          $value     The decoded meta value.
	 * @param Link_Processor $processor The processor for the current action.
	 * @param string         $old_link  The link being actioned.
	 * @param string         $new_link  The replacement link (empty for unlink).
	 * @return mixed The processed value.
	 */
	protected function execute_in_meta_value( $value, $processor, $old_link, $new_link ) {
		if ( is_string( $value ) ) {
			if ( '' === $value || false === stripos( $value, $old_link ) ) {
				return $value;
			}

			return $processor->execute( $value, $old_link, $new_link );
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				$value[ $key ] = $this->execute_in_meta_value( $item, $processor, $old_link, $new_link );
			}
		}

		return $value;
	}

	/**
	 * Recursively checks whether a decoded value contains any PHP object.
	 *
	 * Incomplete classes, created when unserialize() runs with `allowed_classes => false`,
	 * are objects too.
	 *
	 * @param mixed $value The decoded value to inspect.
	 * @return bool True when an object is found at any depth.
	 */
	protected function contains_object( $value ) {
		if ( is_object( $value ) ) {
			return true;
		}

		if ( ! is_array( $value ) ) {
			return false;
		}

		foreach ( $value as $item ) {
			if ( $this->contains_object( $item ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Executes link action in post's comments.
	 * TODO: Revisit comments links because WP wraps urls with `<a>` tags when comment is displayed. The means that because of that links will be found as broken but not replaced.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function execute_in_post_comments( int $post_id = 0 ) {
		$post_comments = get_comments( array( 'post_id' => $post_id ) );

		$processor = $this->get_processor();
		if ( is_wp_error( $processor ) ) {
			return false;
		}

		if ( ! empty( $post_comments ) ) {
			$link     = $this->get_link_object();
			$old_link = $link->get_link();
			$new_link = $link->get_new_link();

			foreach ( $post_comments as $post_comment ) {
				// Make WordPress auto-link urls in comment content so the processor can find the broken link wrapped in <a> tag and replace it. Otherwise, the processor will not find the link because of the HTML tags.
				$content = make_clickable( $post_comment->comment_content );

				// When unlinking in comments, we need to make sure that the replacement does not contain URL schema because of the auto-linking of urls in comments.
				add_filter( 'wpmudev_blc_unlink_replacements', array( $this, 'encode_comment_urls' ), 10, 1 );

				$new_content = $processor->execute( $content, $old_link, $new_link );

				remove_filter( 'wpmudev_blc_unlink_replacements', array( $this, 'encode_comment_urls' ), 10, 1 );

				if ( $new_content !== $content ) {
					$update = wp_update_comment(
						array(
							'comment_ID'      => intval( $post_comment->comment_ID ),
							'comment_content' => $new_content,
						)
					);

					return $update && ! is_wp_error( $update );
				}
			}
		}

		return false;
	}

	/**
	 * Runs the link action on the Reusable Blocks of the given $post_id.
	 *
	 * @param int $post_id
	 * @return bool
	 */
	public function execute_in_post_reusable_blocks( int $post_id = 0 ) {
		if ( ! function_exists( 'register_block_type' ) || apply_filters( 'wpmudev_blc_link_actions_exclude_reusable_blocks', false ) ) {
			return false;
		}

		if ( empty( $post_id ) ) {
			return false;
		}

		$result = false;

		if ( empty( $this->temp_store['posts'][ $post_id ]['content'] ) ) {
			$this->temp_store['posts'][ $post_id ]['content'] = get_post_field( 'post_content', $post_id );
		}

		$content         = $this->temp_store['posts'][ $post_id ]['content'];
		$reusable_blocks = $this->get_reusable_blocks_from_content( $content );

		if ( ! empty( $reusable_blocks ) ) {
			$processor = $this->get_processor();
			if ( is_wp_error( $processor ) ) {
				return false;
			}

			$processor->load( [ 'is_recurring_block' => true ] );

			foreach ( $reusable_blocks as $block_post_id ) {
				if ( $this->execute_in_post_content( $block_post_id ) ) {
					$result = true;
				}
			}

			$processor->clear();
		}

		return $result;
	}

	public function get_reusable_blocks_from_content( string $content = '' ) {
		if ( empty( $content ) ) {
			return array();
		}

		static $reusable_blocks = null;

		if ( is_null( $reusable_blocks ) ) {
			$reusable_blocks = get_posts(
				array(
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'post_type'      => 'wp_block',
				)
			);
		}

		if ( ! empty( $reusable_blocks ) ) {
			foreach ( $reusable_blocks as $block_key => $block_id ) {
				$block_name = '<!-- wp:block {"ref":' . $block_id . '} /-->';

				if ( strpos( $content, $block_name ) === false ) {
					unset( $reusable_blocks[ $block_key ] );
				}
			}
		}

		return $reusable_blocks;
	}

	public function get_site_blocks() {

	}

	/**
	 * Executes in users meta table by user id.
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function execute_in_user_author_meta( int $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		/*
		 * TODO: Use an global process_extensive flag and use `get_user_meta()` only with $user_id (no key).
		 */
		$processor = $this->get_processor();
		if ( is_wp_error( $processor ) ) {
			return false;
		}

		$link = $this->get_link_object();

		$content     = get_user_meta( $user_id, 'description', true );
		$new_content = $processor->execute(
			$content,
			$link->get_link(),
			$link->get_new_link()
		);

		return update_user_meta( $user_id, 'description', $new_content );
	}

	/**
	 * Executes link action in comments by comment id.
	 * TODO: Revisit comments links because WP wraps urls with `<a>` tags when comment is displayed. The means that because of that links will be found as broken but not replaced.
	 *
	 * @param int $id comment id
	 *
	 * @return bool
	 */
	public function execute_in_comment( int $id = 0, string $content = null ) {
		$processor = $this->get_processor();
		if ( is_wp_error( $processor ) ) {
			return false;
		}

		if ( empty( $content ) ) {
			$comment = get_comment( $id );

			if ( $comment instanceof WP_Comment || $comment instanceof \WP_Comment ) {
				$content = $comment->comment_content;
			}
		}

		// Make WordPress auto-link urls in comment content so the processor can find the broken link wrapped in <a> tag and replace it. Otherwise, the processor will not find the link because of the HTML tags.
		$content = make_clickable( $content );

		// When unlinking in comments, we need to make sure that the replacement does not contain URL schema because of the auto-linking of urls in comments.
		add_filter( 'wpmudev_blc_unlink_replacements', array( $this, 'encode_comment_urls' ), 10, 1 );

		$link        = $this->get_link_object();
		$new_content = $processor->execute(
			$content,
			$link->get_link(),
			$link->get_new_link()
		);

		remove_filter( 'wpmudev_blc_unlink_replacements', array( $this, 'encode_comment_urls' ), 10, 1 );

		if ( $new_content !== $content ) {
			$update = wp_update_comment(
				array(
					'comment_ID'      => intval( $id ),
					'comment_content' => $new_content,
				)
			);

			return $update && ! is_wp_error( $update );
		}

		return false;
	}

	/**
	 * Encodes url schema in the replacement string when unlinking in comments to avoid having the old link show up
	 * in the new content because of the HTML tags and the auto-linking of urls in comments.
	 *
	 * @param array $replacements List of replacements to be made in the content,
	 * where key is the string to be replaced, and value is the replacement string.
	 *
	 * @return array
	 */
	public function encode_comment_urls( array $replacements = array() ) {
		// When unlinking in comments, we need to make sure that the replacement does not contain URL schema because of the auto-linking of urls in comments.
		return array_map(
			function ( $replacement ) {
				return str_replace( array( ':', '/' ), array( '&#58;', '&#47;' ), $replacement );
			},
			$replacements
		);
	}

	/**
	 * Executes in postmeta by meta_id.
	 *
	 * @param int|null $meta_id
	 * @param string|null $content
	 *
	 * @return bool
	 */
	public function execute_in_postmeta( int $meta_id = null, string $content = null ) {
		return $this->execute_in_meta_table( 'post', $meta_id, $content );
	}

	/**
	 * Executes the action in usermeta table by meta_id.
	 *
	 * @param int|null $meta_id
	 * @param string|null $content
	 *
	 * @return false
	 */
	public function execute_in_usermeta( int $meta_id = null, string $content = null ) {
		return $this->execute_in_meta_table( 'user', $meta_id, $content );
	}

	public function execute_in_meta_table( string $type = null, int $meta_id = null, string $content = null ) {
		if ( empty( $meta_id ) || ! in_array( $type, array( 'user', 'post' ) ) ) {
			return false;
		}

		if ( empty( $content ) ) {
			$meta    = get_metadata_by_mid( $type, $meta_id );
			$content = property_exists( $meta, 'meta_value' ) ? $meta->meta_value : null;
		}

		if ( ! empty( $content ) ) {
			$processor = $this->get_processor();

			if ( is_wp_error( $processor ) ) {
				return false;
			}

			$link        = $this->get_link_object();
			$new_content = $processor->execute(
				$content,
				$link->get_link(),
				$link->get_new_link()
			);

			return update_metadata_by_mid( $type, $meta_id, $new_content );
		}

		return false;
	}

	/**
	 * Returns the processor object that will execute the specified action (replace|unlink|nofollow).
	 *
	 * @param bool $reset Whether to reset the processor data before returning it. Default is false.
	 * @return Link_Processor|null
	 */
	public function get_processor( $reset = false ) {
		if ( $this->processor instanceof Link_Processor && $reset ) {
			// Clear previous processor data if there is any.
			$this->processor->load();
		}

		return $this->processor;
	}

	/**
	 * Determines the processor to execute based on the Link type.
	 *
	 * @return Link_Processor|\WP_Error
	 */
	public function determine_processor() {
		if ( empty( $this->get_link_object() ) ) {
			return new \WP_Error(
				'blc-link-execution-error',
				esc_html__(
					'Missing link',
					'broken-link-checker'
				)
			);
		}

		$type = $this->get_link_object()->get_type();

		if ( empty( $this->actions_map()[ $type ] ) ) {
			return new \WP_Error(
				'blc-link-execution-error',
				esc_html__(
					'Unmapped action',
					'broken-link-checker'
				)
			);
		}

		return $this->actions_map()[ $type ];
	}


	/**
	 * Map with allowed actions and classes that handle link actions.
	 *
	 * @return array
	 */
	public function actions_map() {
		return apply_filters(
			'wpmudev_blc_link_actions_map',
			array(
				'replace'  => Replace_Link::instance(),
				'unlink'   => Unlink_Link::instance(),
				'nofollow' => Nofollow_Link::instance(),
			),
			$this
		);
	}
}
