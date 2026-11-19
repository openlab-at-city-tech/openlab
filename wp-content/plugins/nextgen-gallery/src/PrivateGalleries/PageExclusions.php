<?php
/**
 * Keeps client pages out of public listings and labels them in wp-admin.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\Util\Transient;
use WP_Error;

/**
 * Client pages stay out of search, feeds, core sitemaps, page lists and public REST reads. The
 * Pages screen labels them, and their edit screen warns the editor.
 */
class PageExclusions {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'pre_get_posts', [ self::class, 'exclude_from_queries' ] );
		add_filter( 'wp_sitemaps_posts_query_args', [ self::class, 'exclude_from_sitemaps' ], 10, 2 );
		add_filter( 'get_pages', [ self::class, 'exclude_from_get_pages' ] );
		add_filter( 'rest_page_query', [ self::class, 'exclude_from_rest_listing' ] );
		add_filter( 'rest_post_search_query', [ self::class, 'exclude_from_rest_listing' ] );
		add_filter( 'rest_pre_dispatch', [ self::class, 'refuse_rest_read' ], 10, 3 );

		add_filter( 'display_post_states', [ self::class, 'post_states' ], 10, 2 );
		add_action( 'admin_notices', [ self::class, 'classic_editor_notice' ] );
		add_action( 'enqueue_block_editor_assets', [ self::class, 'block_editor_notice' ] );

		add_action( 'before_delete_post', [ self::class, 'before_delete_page' ] );
		add_action( 'deleted_post', [ self::class, 'page_changed' ] );
		add_action( 'trashed_post', [ self::class, 'page_changed' ] );
		add_action( 'untrashed_post', [ self::class, 'page_changed' ] );
	}

	/**
	 * Leaves client pages out of front-end search and feed queries.
	 *
	 * @param \WP_Query $query Query.
	 * @return void
	 */
	public static function exclude_from_queries( $query ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		if ( ! $query->is_search() && ! $query->is_feed() ) {
			return;
		}

		self::add_not_in( $query );
	}

	/**
	 * Leaves client pages out of the core sitemaps, which list protected pages.
	 *
	 * @param array  $args      WP_Query args.
	 * @param string $post_type Post type.
	 * @return array
	 */
	public static function exclude_from_sitemaps( $args, $post_type ) {
		$pages = Registry::page_ids();

		if ( $pages && 'page' === $post_type ) {
			$args['post__not_in'] = array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : [], $pages );
		}

		return $args;
	}

	/**
	 * Leaves client pages out of get_pages() on the front end (page lists, Page List block).
	 *
	 * @param \WP_Post[] $pages Pages.
	 * @return \WP_Post[]
	 */
	public static function exclude_from_get_pages( $pages ) {
		if ( ( is_admin() && ! wp_doing_ajax() ) || ! is_array( $pages ) ) {
			return $pages;
		}

		$client_pages = Registry::page_ids();

		if ( empty( $client_pages ) ) {
			return $pages;
		}

		return array_values(
			array_filter(
				$pages,
				static function ( $page ) use ( $client_pages ) {
					return ! ( $page instanceof \WP_Post && in_array( (int) $page->ID, $client_pages, true ) );
				}
			)
		);
	}

	/**
	 * Leaves client pages out of the REST pages collection and the REST search for users who
	 * cannot edit pages, also when the request names them with `include`.
	 *
	 * @param array $args WP_Query args.
	 * @return array
	 */
	public static function exclude_from_rest_listing( $args ) {
		$pages = Registry::page_ids();

		if ( ! $pages || current_user_can( 'edit_pages' ) ) {
			return $args;
		}

		$args['post__not_in'] = array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : [], $pages );

		// WP_Query ignores post__not_in once post__in is set; [0] keeps an emptied list from matching every page.
		if ( ! empty( $args['post__in'] ) ) {
			$kept             = array_values( array_diff( array_map( 'intval', (array) $args['post__in'] ), $pages ) );
			$args['post__in'] = $kept ? $kept : [ 0 ];
		}

		return $args;
	}

	/**
	 * Refuses a REST read of a client page by someone who cannot edit it. Core accepts a
	 * `password` argument there, which would be an unlimited password oracle.
	 *
	 * @param mixed            $result  Response so far.
	 * @param \WP_REST_Server  $server  Server.
	 * @param \WP_REST_Request $request Request.
	 * @return mixed
	 */
	public static function refuse_rest_read( $result, $server, $request ) {
		if ( null !== $result ) {
			return $result;
		}

		$route = (string) $request->get_route();

		// Core matches routes case-insensitively, so these patterns must too.
		if ( preg_match( '#^/wp/v2/pages/(\d+)#i', $route, $matches ) ) {
			return self::refuse_client_pages( [ (int) $matches[1] ], $result );
		}

		// Other routes take the password too: a page's comments, or a page picked by include.
		$password = $request->get_param( 'password' );

		if ( null === $password || '' === $password ) {
			return $result;
		}

		$page_ids = [];

		if ( preg_match( '#^/wp/v2/comments/(\d+)#i', $route, $matches ) ) {
			$comment  = get_comment( (int) $matches[1] );
			$page_ids = $comment ? [ (int) $comment->comment_post_ID ] : [];
		} elseif ( preg_match( '#^/wp/v2/comments/?$#i', $route ) ) {
			$page_ids = wp_parse_id_list( $request->get_param( 'post' ) );

			// With an id, core checks the password against every listed comment's post.
			if ( ! empty( $request->get_param( 'id' ) ) ) {
				$page_ids = array_merge( $page_ids, Registry::page_ids() );
			}
		} elseif ( preg_match( '#^/wp/v2/pages/?$#i', $route ) ) {
			$page_ids = wp_parse_id_list( $request->get_param( 'include' ) );
		}

		return self::refuse_client_pages( $page_ids, $result );
	}

	/**
	 * A 404 when one of the pages is a client page the user cannot edit.
	 *
	 * @param int[] $page_ids Page ids the request reads.
	 * @param mixed $result   Response so far.
	 * @return mixed
	 */
	private static function refuse_client_pages( array $page_ids, $result ) {
		foreach ( $page_ids as $page_id ) {
			if ( Registry::is_client_page( $page_id ) && ! current_user_can( 'edit_post', $page_id ) ) {
				return new WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.', 'nggallery' ), [ 'status' => 404 ] );
			}
		}

		return $result;
	}

	/**
	 * Labels client pages on the Pages screen.
	 *
	 * @param string[] $states Post states.
	 * @param \WP_Post $post   Post.
	 * @return string[]
	 */
	public static function post_states( $states, $post ) {
		if ( $post instanceof \WP_Post && Registry::is_client_page( $post->ID ) ) {
			$states['ngg_client_gallery'] = __( 'Imagely client gallery', 'nggallery' );
		}

		return $states;
	}

	/**
	 * Editor notice on a client page's classic edit screen.
	 *
	 * @return void
	 */
	public static function classic_editor_notice() {
		$page_id = self::edited_client_page();

		if ( ! $page_id ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html( self::notice_text( $page_id ) )
		);
	}

	/**
	 * Editor notice on a client page in the block editor.
	 *
	 * @return void
	 */
	public static function block_editor_notice() {
		$page_id = self::edited_client_page();

		if ( ! $page_id ) {
			return;
		}

		wp_add_inline_script(
			'wp-notices',
			sprintf(
				'wp.domReady(function(){wp.data.dispatch("core/notices").createWarningNotice(%s,{id:"ngg-client-gallery",isDismissible:true});});',
				wp_json_encode( self::notice_text( $page_id ) )
			),
			'after'
		);

		wp_enqueue_script( 'wp-notices' );
		wp_enqueue_script( 'wp-dom-ready' );
		wp_enqueue_script( 'wp-data' );
	}

	/**
	 * Before a page is deleted for good: a private gallery that was never set up keeps no link
	 * to a page that is gone, so it is not mistaken for "Page missing".
	 *
	 * @param int $post_id Post id.
	 * @return void
	 */
	public static function before_delete_page( $post_id ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Rare event; reads the galleries linked to the page.
		$gallery_ids = $wpdb->get_col(
			$wpdb->prepare( "SELECT gid FROM {$wpdb->nggallery} WHERE pageid = %d AND is_private = 1", (int) $post_id )
		);

		if ( empty( $gallery_ids ) ) {
			return;
		}

		$mapper = GalleryMapper::get_instance();

		foreach ( $gallery_ids as $gallery_id ) {
			if ( ClientPage::page_has_mark( $post_id, $gallery_id ) ) {
				continue;
			}

			$gallery = $mapper->find( $gallery_id );

			if ( $gallery ) {
				$gallery->pageid = 0;
				$mapper->save( $gallery );
			}
		}
	}

	/**
	 * Rebuilds the private gallery list after a page was trashed, restored or deleted.
	 *
	 * @param int $post_id Post id.
	 * @return void
	 */
	public static function page_changed( $post_id ) {
		if ( 'page' !== get_post_type( $post_id ) && ! Registry::is_client_page( $post_id ) ) {
			return;
		}

		Registry::rebuild();
		Transient::flush( 'rest_galleries' );
	}

	/**
	 * Adds the client pages to a query's post__not_in.
	 *
	 * @param \WP_Query $query Query.
	 * @return void
	 */
	private static function add_not_in( $query ) {
		$pages = Registry::page_ids();

		if ( empty( $pages ) ) {
			return;
		}

		$existing = (array) $query->get( 'post__not_in' );
		$query->set( 'post__not_in', array_values( array_unique( array_merge( array_filter( array_map( 'intval', $existing ) ), $pages ) ) ) );
	}

	/**
	 * The client page open in the editor, if any.
	 *
	 * @return int
	 */
	private static function edited_client_page() {
		if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
			return 0;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'post' !== $screen->base || 'page' !== $screen->post_type ) {
			return 0;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only: picks which notice to show.
		$page_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

		return Registry::is_client_page( $page_id ) ? $page_id : 0;
	}

	/**
	 * Text of the editor notice.
	 *
	 * @param int $page_id Client page.
	 * @return string
	 */
	private static function notice_text( $page_id ) {
		$titles = [];

		foreach ( Registry::galleries_for_page( $page_id ) as $gallery_id ) {
			$gallery  = GalleryMapper::get_instance()->find( $gallery_id );
			$titles[] = $gallery && '' !== trim( (string) $gallery->title ) ? (string) $gallery->title : '#' . $gallery_id;
		}

		return sprintf(
			/* translators: %s: gallery titles */
			__( 'This page is the client page of the private Imagely gallery %s. Its password and visibility are managed in Imagely, in the gallery\'s Settings tab. Changing them here changes who can see the gallery.', 'nggallery' ),
			'"' . implode( '", "', $titles ) . '"'
		);
	}
}
