<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataStorage\Sanitizer;
use Imagely\NGG\DisplayType\Controller as ParentController;

/**
 * Taxonomy display type controller.
 */
class Taxonomy extends ParentController {

	/**
	 * Instance cache.
	 *
	 * @var Taxonomy|null
	 */
	public static $instance = null;

	/**
	 * Whether NGG tag detection has run.
	 *
	 * @var bool
	 */
	protected $ngg_tag_detection_has_run = false;

	/**
	 * Gets an instance of the taxonomy display type.
	 *
	 * @return Taxonomy
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Taxonomy();
		}
		return self::$instance;
	}

	public function render_tag( $tag ) {
		$mapper = DisplayTypeMapper::get_instance();

		// Respect the global display type setting.
		$display_type = $mapper->find_by_name( NGG_BASIC_TAGCLOUD );
		$display_type = ! empty( $display_type->settings['gallery_display_type'] ) ? $display_type->settings['gallery_display_type'] : NGG_BASIC_THUMBNAILS;

		// $tag crosses into a shortcode-attribute context here, so it must be escaped for
		// that context specifically: esc_attr() neutralizes quotes, and encoding the
		// shortcode delimiters stops do_shortcode() from parsing an injected tag out of it.
		$tag = str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), esc_attr( $tag ) );

		return "[ngg source='tags' container_ids='{$tag}' slug='{$tag}' display_type='{$display_type}']";
	}

	/**
	 * Determines if the current page is /ngg_tag/{*}
	 *
	 * @param array     $posts WordPress post objects
	 * @param \WP_Query $wp_query_local
	 * @return array WordPress post objects
	 */
	public function detect_ngg_tag( $posts, $wp_query_local ) {
		global $wp;
		global $wp_query;
		$wp_query_orig = false;

		if ( $wp_query_local !== null && $wp_query_local !== $wp_query ) {
			$wp_query_orig = $wp_query;
			$wp_query      = $wp_query_local;
		}

		// This appears to be necessary for multisite installations, but I can't imagine why. More hackery..
		$tag = \urldecode( \get_query_var( 'ngg_tag' ) ? \get_query_var( 'ngg_tag' ) : \get_query_var( 'name' ) );
		$tag = \stripslashes( Sanitizer::strip_html( $tag ) ); // Tags may not include HTML.

		// A real tag is always a sanitize_title() slug (see Legacy/lib/tags.php, where
		// tags are created/renamed via the same function) — normalize to that slug
		// rather than reject, since sanitize_title() percent-encodes non-ASCII input
		// (e.g. Cyrillic/CJK), so raw UTF-8 tags would otherwise never match and
		// legitimate non-ASCII tag pages would 404. Its output alphabet still has
		// no quotes/brackets/HTML, so the #856 injection payloads still die.
		$tag = \sanitize_title( $tag );

		if ( ! $this->ngg_tag_detection_has_run // don't run more than once; necessary for certain themes.
		&& ! \is_admin() // will destroy 'view all posts' page without this.
		&& ! empty( $tag ) // only run when a tag has been given to WordPress.
		&& is_string( $wp->request )
		&& ( stripos( $wp->request, 'ngg_tag' ) === 0 // make sure the query begins with /ngg_tag.
		|| ( isset( $wp_query->query_vars['page_id'] )
				&& $wp_query->query_vars['page_id'] === 'ngg_tag' )
		)
		) {
			$this->ngg_tag_detection_has_run = true;

			// Without this all url generated from this page lacks the /ngg_tag/(slug) section of the URL.
			add_filter( 'ngg_wprouting_add_post_permalink', '__return_false' );

			// create in-code a fake post; we feed it back to WordPress as the sole result of the "the_posts" filter.
			$posts   = null;
			$posts[] = $this->create_ngg_tag_post( $tag );

			// On WP <= 6.3.x, WP_Query::get_posts() unconditionally caches "the_posts" filter's
			// return value under wp_cache key $post->ID in the 'posts' group. Disabling
			// cache_results avoids planting this virtual post there on those older cores.
			// On current core the query never reaches that caching branch for a filtered
			// result (it takes the _prime_post_caches() branch instead, which only caches
			// what its own DB query returns), so this is a no-op there — kept for the
			// 5.5.4 support floor.
			$wp_query->query_vars['cache_results'] = false;

			$wp_query->is_404      = false;
			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;
			// ngg_tag is a real taxonomy (nggallery.php registers it with defaults), so
			// parse_query() sets is_tax/is_tag true here; get_queried_object() checks that
			// branch before is_singular, so leaving these set makes core resolve the tag
			// term (not this virtual post) as the queried object — the term ID then flows
			// into wp_get_shortlink()'s get_post( $post_id ), which is null for a term ID
			// and produces the reported "post_type on null" warning independent of $post->ID.
			$wp_query->is_tax = false;
			$wp_query->is_tag = false;

			// This virtual post has no real permalink of its own. redirect_canonical()'s
			// page_on_front check (is_page() && get_queried_object_id() === (int)
			// get_option( 'page_on_front' )) matches ID 0 whenever a site has
			// show_on_front=page with no page_on_front set — a common, valid Reading-settings
			// state — sending every /ngg_tag/{slug}/ request back to the homepage.
			// Util\Router::restore_request_uri() already removes core's own
			// template_redirect hook for redirect_canonical() and calls it directly instead,
			// so cancel it via the 'redirect_canonical' filter rather than remove_action().
			// Only the page_on_front redirect (target === home_url( '/' )) is cancelled —
			// trailing-slash and www/non-www normalisation for this URL must still fire.
			add_filter( 'redirect_canonical', [ $this, 'cancel_page_on_front_redirect' ] );

			// Themes may call comments_template() unguarded on the page template (e.g.
			// Twenty Eleven/Twelve's page.php), and WP_Comment_Query only restricts by
			// post when $post_id is non-empty (class-wp-comment-query.php:721-723) — with
			// this virtual post's ID of 0, that guard never applies and every approved
			// comment on the site, including ones on private/draft/password-protected
			// posts, would render. Gated on $post_id: an earlier comments_array call for
			// a different, real post (e.g. a sidebar widget) must not be blanked, and
			// must not spend the self-unhook before this page's own call ever fires.
			add_filter( 'comments_array', [ $this, 'suppress_ngg_tag_comments' ], 10, 2 );

			unset( $wp_query->query['error'] );
			$wp_query->query_vars['error'] = '';
		}

		if ( $wp_query_orig !== false ) {
			$wp_query = $wp_query_orig;
		}

		return $posts;
	}

	/**
	 * Cancels only the page_on_front redirect computed for this virtual post, leaving
	 * every other normalisation (trailing slash, www/non-www, etc.) intact. Matched by
	 * intent (is_page() && queried object ID 0) rather than exact target-URL equality,
	 * since query strings (utm_*, fbclid, ...) survive on the redirect target and break
	 * a string match against home_url( '/' ).
	 *
	 * @param string|false $redirect_url The redirect URL, or false.
	 * @return string|false
	 */
	public function cancel_page_on_front_redirect( $redirect_url ) {
		if ( \is_page() && 0 === \get_queried_object_id() ) {
			return false;
		}
		return $redirect_url;
	}

	/**
	 * Suppresses comments for the virtual ngg_tag page (post ID 0), then unhooks itself.
	 * Passes through comments for any other post untouched.
	 *
	 * @param array $comments The comments.
	 * @param int   $post_id  The post ID core queried comments for.
	 * @return array
	 */
	public function suppress_ngg_tag_comments( $comments, $post_id ) {
		if ( 0 !== (int) $post_id ) {
			return $comments;
		}
		\remove_filter( 'comments_array', [ $this, 'suppress_ngg_tag_comments' ] );
		return [];
	}

	public function create_ngg_tag_post( $tag ) {
		// $tag is a sanitize_title() slug (percent-encoded for non-ASCII input), which is
		// correct for the shortcode/lookup sinks but unreadable as a display title, so the
		// title sink shows the native term name instead when the term exists.
		$term         = \get_term_by( 'slug', $tag, 'ngg_tag' );
		$display_name = $term ? $term->name : $tag;

		// post_title is a separate output sink from the shortcode-attribute one in
		// render_tag(): themes may print get_the_title() raw or inside an HTML
		// attribute, so $display_name needs HTML-attribute escaping here too.
		/* translators: %s: tag name */
		$title = sprintf( __( 'Images tagged &quot;%s&quot;', 'nggallery' ), esc_html( $display_name ) );
		$title = \apply_filters( 'ngg_basic_tagcloud_title', $title, $tag );

		$post               = new \stdClass();
		$post->post_author  = false;
		$post->post_name    = 'ngg_tag';
		$post->guid         = \get_bloginfo( 'wpurl' ) . '/ngg_tag';
		$post->post_title   = $title;
		$post->post_content = $this->render_tag( $tag );
		// 0, not -1: every core guard around this virtual post (get_post()'s $GLOBALS['post']
		// fallback, wp_get_shortlink()'s `! empty( $post_id )` gate) is an empty() test, and
		// -1 is non-empty — it would pass those guards and dereference null instead of failing
		// closed. Matches the ID used for nextgen-gallery-pro's virtual ecommerce posts.
		$post->ID             = 0;
		$post->post_type      = 'page';
		$post->post_status    = 'publish';
		$post->comment_status = 'closed';
		$post->ping_status    = 'closed';
		$post->comment_count  = 0;
		$post->post_date      = current_time( 'mysql' );
		$post->post_date_gmt  = current_time( 'mysql', 1 );

		return( $post );
	}
}
