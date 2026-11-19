<?php
/**
 * Decides which private galleries the current request may show.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

use Imagely\NGG\Util\Security;

/**
 * Private galleries render only on their own unlocked client page. Elsewhere they are left out of
 * every DisplayedGallery query, and users who can manage them see a notice instead.
 */
class Visibility {

	/**
	 * Set by the admin shortcode preview, where managers see their galleries.
	 *
	 * @var bool
	 */
	public static $preview = false;

	/**
	 * Depth of strict sections (sitemaps, feeds), where every private gallery is hidden.
	 *
	 * @var int
	 */
	private static $strict = 0;

	/**
	 * Request cache of post_password_required() per client page.
	 *
	 * @var array<int,bool>
	 */
	private static $unlocked = [];

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'ngg_displayed_gallery_cache_params', [ self::class, 'add_cache_params' ] );
	}

	/**
	 * Private gallery ids that must be left out of this request's output.
	 *
	 * @return int[]
	 */
	public static function get_hidden_gallery_ids() {
		$all = Registry::get_all();

		if ( empty( $all ) ) {
			return [];
		}

		if ( self::$strict > 0 ) {
			return array_keys( $all );
		}

		$visible = self::get_visible_gallery_ids( $all );

		return array_values( array_diff( array_keys( $all ), $visible ) );
	}

	/**
	 * Runs a callback with every private gallery hidden, whoever is logged in. For output that
	 * becomes public (sitemaps, feeds).
	 *
	 * @param callable $callback Callback.
	 * @return mixed What the callback returned.
	 */
	public static function strictly( $callback ) {
		++self::$strict;

		try {
			return $callback();
		} finally {
			--self::$strict;
		}
	}

	/**
	 * Whether a gallery must be left out of this request's output.
	 *
	 * @param int|string $gallery_id Gallery id.
	 * @return bool
	 */
	public static function is_hidden( $gallery_id ) {
		return in_array( (int) $gallery_id, self::get_hidden_gallery_ids(), true );
	}

	/**
	 * Whether an image belongs to a gallery hidden in this request. No query when none is hidden.
	 *
	 * @param int|string $image_id Image id.
	 * @return bool
	 */
	public static function is_hidden_image( $image_id ) {
		$hidden = self::get_hidden_gallery_ids();

		if ( empty( $hidden ) || (int) $image_id <= 0 ) {
			return false;
		}

		$image = \Imagely\NGG\DataMappers\Image::get_instance()->find( (int) $image_id );

		return $image && in_array( (int) $image->galleryid, $hidden, true );
	}

	/**
	 * Whether the current user may manage a private gallery.
	 *
	 * @param int $gallery_id Gallery id.
	 * @return bool
	 */
	public static function can_manage( $gallery_id ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		return Security::can_manage_owned( Registry::author_of( $gallery_id ), 'nextgen_edit_gallery' );
	}

	/**
	 * Client pages whose password the current visitor has entered, on the page being viewed.
	 *
	 * @return int[]
	 */
	public static function get_unlocked_page_ids() {
		$pages = [];

		if ( did_action( 'wp' ) && is_singular() ) {
			$page_id = (int) get_queried_object_id();

			if ( Registry::is_client_page( $page_id ) && self::is_page_unlocked( $page_id ) ) {
				$pages[] = $page_id;
			}
		}

		/**
		 * Filters the client pages treated as unlocked in this request (Pro adds lightbox REST pages).
		 *
		 * @param int[] $pages Page ids.
		 */
		return array_values( array_unique( array_map( 'intval', (array) apply_filters( 'ngg_private_gallery_unlocked_page_ids', $pages ) ) ) );
	}

	/**
	 * Whether the visitor has entered a client page's password.
	 *
	 * @param int $page_id Page id.
	 * @return bool
	 */
	public static function is_page_unlocked( $page_id ) {
		$page_id = (int) $page_id;

		if ( ! isset( self::$unlocked[ $page_id ] ) ) {
			$page = get_post( $page_id );

			self::$unlocked[ $page_id ] = $page instanceof \WP_Post
				&& 'publish' === $page->post_status
				&& ! post_password_required( $page );
		}

		return self::$unlocked[ $page_id ];
	}

	/**
	 * Forgets the request caches (tests).
	 *
	 * @return void
	 */
	public static function reset() {
		self::$unlocked = [];
		self::$preview  = false;
		self::$strict   = 0;
	}

	/**
	 * Notice shown to managers in place of a private gallery outside its client page.
	 *
	 * @param object $displayed_gallery Displayed gallery.
	 * @return array{html:string,fully_hidden:bool,hidden:int[]}|null Null when nothing requested is hidden.
	 */
	public static function get_render_notice( $displayed_gallery ) {
		if ( isset( $displayed_gallery->source ) && in_array( (string) $displayed_gallery->source, [ 'albums', 'album' ], true ) ) {
			return self::get_album_notice( $displayed_gallery );
		}

		$requested = self::get_requested_gallery_ids( $displayed_gallery );

		if ( empty( $requested ) ) {
			return null;
		}

		$hidden = array_values( array_intersect( $requested, self::get_hidden_gallery_ids() ) );

		if ( empty( $hidden ) ) {
			return null;
		}

		$html = '';

		foreach ( $hidden as $gallery_id ) {
			if ( self::can_manage( $gallery_id ) ) {
				$html .= self::notice_html( $gallery_id );
			}
		}

		return [
			'html'         => $html,
			'fully_hidden' => count( $hidden ) === count( $requested ),
			'hidden'       => $hidden,
		];
	}

	/**
	 * Notice for managers about the private galleries an album leaves out. The album itself still renders.
	 *
	 * @param object $displayed_gallery Displayed gallery.
	 * @return array{html:string,fully_hidden:bool,hidden:int[]}|null Null when the viewer gets no notice.
	 */
	private static function get_album_notice( $displayed_gallery ) {
		$hidden_all = self::get_hidden_gallery_ids();

		if ( empty( $hidden_all ) || empty( $displayed_gallery->container_ids ) ) {
			return null;
		}

		$html = '';

		foreach ( array_intersect( self::get_album_gallery_ids( (array) $displayed_gallery->container_ids ), $hidden_all ) as $gallery_id ) {
			if ( self::can_manage( $gallery_id ) ) {
				$html .= self::notice_html( $gallery_id );
			}
		}

		// The album ids must stay in container_ids, so nothing is listed as hidden.
		return '' === $html ? null : [
			'html'         => $html,
			'fully_hidden' => false,
			'hidden'       => [],
		];
	}

	/**
	 * Gallery ids in albums, including their sub-albums.
	 *
	 * @param array $album_ids Album ids.
	 * @return int[]
	 */
	private static function get_album_gallery_ids( array $album_ids ) {
		$mapper  = \Imagely\NGG\DataMappers\Album::get_instance();
		$queue   = array_filter( array_map( 'intval', array_filter( $album_ids, 'is_numeric' ) ) );
		$visited = [];
		$ids     = [];

		while ( $queue ) {
			$album_id = (int) array_shift( $queue );

			if ( $album_id <= 0 || isset( $visited[ $album_id ] ) ) {
				continue;
			}

			$visited[ $album_id ] = true;
			$album                = $mapper->find( $album_id );

			foreach ( $album ? (array) $album->sortorder : [] as $item ) {
				if ( is_string( $item ) && 0 === strpos( $item, 'a' ) ) {
					$queue[] = absint( substr( $item, 1 ) );
				} elseif ( is_numeric( $item ) && (int) $item > 0 ) {
					$ids[] = (int) $item;
				}
			}
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * Adds the private context to the render cache key, so locked, unlocked and manager output
	 * never share an entry.
	 *
	 * @param array $params Cache key params.
	 * @return array
	 */
	public static function add_cache_params( $params ) {
		$all = Registry::get_all();

		if ( empty( $all ) ) {
			return $params;
		}

		$hidden  = self::get_hidden_gallery_ids();
		$managed = [];

		foreach ( $hidden as $gallery_id ) {
			if ( self::can_manage( $gallery_id ) ) {
				$managed[] = $gallery_id;
			}
		}

		$params[] = [
			'ngg_private_hidden'  => $hidden,
			'ngg_private_managed' => $managed,
		];

		return $params;
	}

	/**
	 * Gallery ids visible in this request.
	 *
	 * @param array $all Registry map.
	 * @return int[]
	 */
	private static function get_visible_gallery_ids( array $all ) {
		$visible = [];

		if ( self::is_management_context() ) {
			foreach ( array_keys( $all ) as $gallery_id ) {
				if ( self::can_manage( $gallery_id ) ) {
					$visible[] = (int) $gallery_id;
				}
			}
		}

		$pages = self::get_unlocked_page_ids();

		if ( $pages ) {
			foreach ( $all as $gallery_id => $row ) {
				if ( in_array( $row['p'], $pages, true ) ) {
					$visible[] = (int) $gallery_id;
				}
			}
		}

		/**
		 * Filters the private galleries visible in this request (Pro removes expired ones).
		 *
		 * @param int[] $visible Gallery ids.
		 * @param array $all     Every private gallery: id => [ 'p' => page id, 'a' => author id ].
		 */
		$visible = (array) apply_filters( 'ngg_private_gallery_visible_ids', array_values( array_unique( $visible ) ), $all );

		return array_map( 'intval', $visible );
	}

	/**
	 * Admin screens, REST requests and the admin preview, where managers see their galleries.
	 *
	 * @return bool
	 */
	private static function is_management_context() {
		if ( self::$preview ) {
			return true;
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return true;
		}

		if ( is_admin() && ! wp_doing_ajax() ) {
			return true;
		}

		return defined( 'REST_REQUEST' ) && REST_REQUEST && is_user_logged_in();
	}

	/**
	 * Gallery ids a displayed gallery asks for by id.
	 *
	 * @param object $displayed_gallery Displayed gallery.
	 * @return int[]
	 */
	private static function get_requested_gallery_ids( $displayed_gallery ) {
		$source = isset( $displayed_gallery->source ) ? (string) $displayed_gallery->source : '';

		if ( ! in_array( $source, [ 'galleries', 'gallery' ], true ) || empty( $displayed_gallery->container_ids ) ) {
			return [];
		}

		$ids = [];

		foreach ( (array) $displayed_gallery->container_ids as $id ) {
			if ( is_numeric( $id ) && (int) $id > 0 ) {
				$ids[] = (int) $id;
			}
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * The manager notice for one gallery.
	 *
	 * @param int $gallery_id Gallery id.
	 * @return string
	 */
	private static function notice_html( $gallery_id ) {
		$page_id = Registry::page_for( $gallery_id );
		$link    = '';

		if ( $page_id && get_post( $page_id ) ) {
			$link = sprintf(
				' <a href="%s">%s</a>',
				esc_url( get_permalink( $page_id ) ),
				esc_html__( 'Open the client page', 'nggallery' )
			);
		}

		return sprintf(
			'<div class="ngg-private-gallery-notice" style="padding:12px 16px;margin:0 0 1em;border-left:4px solid #72aee6;background:#f0f6fc;color:#1d2327;">%s%s</div>',
			esc_html(
				sprintf(
					/* translators: %d: gallery id */
					__( 'Gallery %d is private. Visitors do not see it here; it shows only on its client page. You see this notice because you can manage the gallery.', 'nggallery' ),
					$gallery_id
				)
			),
			$link
		);
	}
}
