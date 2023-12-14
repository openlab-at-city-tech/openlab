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
	 * @var null
	 */
	protected $processor = null;

	/**
	 * Link object.
	 *
	 * @var null
	 */
	protected $link = null;

	/**
	 * A Store array to store data in order to avoid repetitive queries.
	 *
	 * @var array
	 */
	protected $temp_store = array();

	public function __construct( Link $link = null ) {
		$this->link = $link;
	}

	/**
	 * Replaces url in post's content, meta and comments.
	 * Returns false if link not replaced anywhere, else true.
	 *
	 * @param int|null $post_id
	 *
	 * @return bool
	 */
	public function execute_url_in_post( int $post_id = null ) {
		if ( empty( $post_id ) ) {
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
	 * Runs only on post's content.
	 *
	 * @param int|null $post_id
	 *
	 * @return bool
	 */
	public function execute_in_post_content( int $post_id = null, string $content = null ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$new_content = '';

		if ( empty( $content ) ) {
			if ( empty( $this->temp_store['posts'][ $post_id ]['content'] ) ) {
				$this->temp_store['posts'][ $post_id ]['content'] = get_post_field( 'post_content', $post_id );
			}

			$content = $this->temp_store['posts'][ $post_id ]['content'];
		}

		if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$new_content = $this->execute_post_blocks( $content, $this->link->get_link(), $this->link->get_new_link() );
		} else {
			$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );
		}

		if ( $content !== $new_content ) {
			// At first, we are creating a new post revision.
			wp_save_post_revision( $post_id );

			// Then update post with new content.
			kses_remove_filters();

			return wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $new_content,
				)
			);
			kses_init_filters();
		}

		return false;
	}

	/**
	 * Runs the links action in post blocks.
	 *
	 * @param string|null $content
	 * @param string|null $link
	 * @param string|null $new_link
	 * @return string
	 */
	public function execute_post_blocks( string $content = null, string $link = null, string $new_link = null ) {
		// No point running if content is empty or if there is no link to modify.
		if ( empty( $content ) || empty( $link ) ) {
			return $content;
		}

		if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$blocks        = parse_blocks( $content );
			$parsed_blocks = array();

			if ( ! empty( $blocks ) ) {
				foreach ( $blocks as $block ) {
					$parsed_blocks[] = $this->get_processor()->parse_block( $block, $link, $new_link );
				}

			}
		}

		return ! empty( $parsed_blocks ) ? serialize_blocks( $parsed_blocks ) : $content;
	}

	/**
	 * Executes in all post meta values by post id.
	 *
	 * @param int|null $post_id
	 *
	 * @return false|void
	 */
	public function execute_in_post_meta_values( int $post_id = null ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		// The `wpmudev_blc_process_extensive` filter is returned in `Utilities::process_extensive()`.
		if ( Utilities::process_extensive() ) {
			global $wpdb;

			$query = $wpdb->prepare(
				"SELECT meta_key, meta_value as content FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_value LIKE %s",
				$post_id,
				'%' . $wpdb->esc_like( $this->link->get_link() ) . '%'
			);
			$metas = $wpdb->get_results( $query );

			if ( ! empty( $metas ) ) {
				foreach ( $metas as $meta ) {
					$new_content = $this->get_processor()->execute( $meta->content, $this->link->get_link(), $this->link->get_new_link() );

					if ( $new_content !== $meta->content ) {
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

			foreach ( $supported_meta_keys as $meta_key ) {
				$content     = get_post_meta( $post_id, sanitize_key( $meta_key ), true );
				$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );

				if ( $new_content !== $content ) {
					return update_post_meta( $post_id, sanitize_key( $meta_key ), $new_content );
				}
			}
		}

		return false;
	}

	/**
	 * Executes link action in post's comments.
	 * TODO: Revisit comments links because WP wraps urls with `<a>` tags when comment is displayed. The means that because of that links will be found as broken but not replaced.
	 *
	 * @param int|null $post_id
	 *
	 * @return bool
	 */
	public function execute_in_post_comments( int $post_id = null ) {
		$post_comments = get_comments( array( 'post_id' => $post_id ) );

		if ( ! empty( $post_comments ) ) {
			foreach ( $post_comments as $post_comment ) {
				$content     = $post_comment->comment_content;
				$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );

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
	 * @param [type] $post_id
	 * @return bool
	 */
	public function execute_in_post_reusable_blocks( int $post_id = null ) {
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
			$this->get_processor()->load( [ 'is_recurring_block' => true ] );

			foreach ( $reusable_blocks as $block_post_id ) {
				if ( $this->execute_in_post_content( $block_post_id ) ) {
					$result = true;
				}
			}

			$this->get_processor()->clear();
		}

		return $result;
	}

	public function get_reusable_blocks_from_content( string $content = null ) {
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
	 * @param int|null $user_id
	 *
	 * @return bool
	 */
	public function execute_in_user_author_meta( int $user_id = null ) {
		if ( is_null( $user_id ) ) {
			return false;
		}

		/*
		 * TODO: Use an global process_extensive flag and use `get_user_meta()` only with $user_id (no key).
		 */
		$content     = get_user_meta( $user_id, 'description', true );
		$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );

		return update_user_meta( $user_id, 'description', $new_content );
	}

	/**
	 * Executes link action in comments by comment id.
	 * TODO: Revisit comments links because WP wraps urls with `<a>` tags when comment is displayed. The means that because of that links will be found as broken but not replaced.
	 *
	 * @param int|null $id comment id
	 *
	 * @return bool
	 */
	public function execute_in_comment( int $id = null, string $content = null ) {
		if ( empty( $content ) ) {
			$comment = get_comment( $id );

			if ( $comment instanceof WP_Comment || $comment instanceof \WP_Comment ) {
				$content = $comment->comment_content;
			}

		}

		$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );

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
			$new_content = $this->get_processor()->execute( $content, $this->link->get_link(), $this->link->get_new_link() );

			return update_metadata_by_mid( $type, $meta_id, $new_content );
		}

		return false;
	}

	public function get_processor() {
		if ( empty( $this->processor ) ) {
			$this->processor = $this->determine_processor();
		}

		$this->processor->load();

		return $this->processor;
	}

	public function determine_processor() {
		$type = $this->link->get_type();

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
