<?php
/**
 * REST route listing the site's pages for the admin app's "link to page" pickers.
 *
 * @package NextGEN Gallery
 */

namespace Imagely\NGG\REST\Admin;

use Imagely\NGG\Util\Security;

/**
 * Lists every page a gallery, album or eCommerce setting can link to.
 *
 * The admin app used to read core's /wp/v2/pages collection, which stops at 100 rows per
 * request, orders by date and returns the title with its HTML entities (#1200). Paging
 * through core's collection needs the Link header, which some servers strip, so the app
 * reads this route instead: one response with every page, sorted by title, decoded for
 * use as a plain-text option label.
 */
class Pages extends \WP_REST_Controller {

	/**
	 * Pages constructor.
	 */
	public function __construct() {
		$this->namespace = 'ngg/v1';
		$this->rest_base = 'admin/pages';
	}

	/**
	 * Register the route.
	 */
	public function register_routes() {
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
		);
	}

	/**
	 * Every admin-app screen sits behind "NextGEN Gallery overview", the same floor
	 * GalleryREST uses to read galleries. Published page titles are public anyway; private
	 * pages are only included for users who can read them (see get_pages()).
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function get_items_permissions_check( $request ): bool {
		return Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * List every page the current user may link to.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_items( $request ) {
		return new \WP_REST_Response( self::get_pages() );
	}

	/**
	 * Every published page (plus private pages for users who can read them), sorted by
	 * title, with the title as plain text.
	 *
	 * @return array<int, array{id: int, title: string, status: string}>
	 */
	public static function get_pages(): array {
		$statuses = [ 'publish' ];
		if ( current_user_can( 'read_private_pages' ) ) {
			$statuses[] = 'private';
		}

		// WP_Query rather than get_posts() so the_posts filters (WPML, Polylang, ...) apply
		// the way they do for core's /wp/v2/pages. No cap: the legacy album editor listed
		// every page through wp_dropdown_pages() and the pickers need the same.
		$query = new \WP_Query(
			[
				'post_type'              => 'page',
				'post_status'            => $statuses,
				'posts_per_page'         => -1,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			]
		);

		$pages = [];
		foreach ( $query->posts as $post ) {
			$pages[] = [
				'id'     => (int) $post->ID,
				'title'  => self::plain_title( $post ),
				'status' => (string) $post->post_status,
			];
		}

		// Sort in PHP, not only in SQL: the database collation decides case and accent
		// handling per site, and "Page 10" would land before "Page 2". remove_accents()
		// keeps "Édito" next to "Edito"; strnatcasecmp() ignores case and orders numbers
		// naturally. PHP 8's sort is stable, so equal titles keep the query's order.
		usort(
			$pages,
			static fn( array $a, array $b ): int => strnatcasecmp(
				remove_accents( $a['title'] ),
				remove_accents( $b['title'] )
			)
		);

		return $pages;
	}

	/**
	 * The page title as plain text for an <option> label.
	 *
	 * Core's title.rendered is get_the_title(): the_title filters applied (texturize,
	 * translation plugins, the "Private: %s" prefix), entities left encoded. The app
	 * rendered that as text, so "L'arquitecte" showed as "L&#8217;arquitecte"; decode it.
	 *
	 * @param \WP_Post $post The page.
	 *
	 * @return string
	 */
	private static function plain_title( \WP_Post $post ): string {
		$title = wp_strip_all_tags( get_the_title( $post ) );

		return trim( html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}
}
