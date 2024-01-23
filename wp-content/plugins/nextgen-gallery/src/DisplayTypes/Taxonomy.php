<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataStorage\Sanitizer;
use Imagely\NGG\DisplayType\Controller as ParentController;

class Taxonomy extends ParentController {

	public static $instance = null;

	protected $ngg_tag_detection_has_run = false;

	/**
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

			// WordPress somewhat-correctly generates several notices, so silence them as they're really unnecessary.
			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				error_reporting( 0 );
			}

			// Without this all url generated from this page lacks the /ngg_tag/(slug) section of the URL.
			add_filter( 'ngg_wprouting_add_post_permalink', '__return_false' );

			// create in-code a fake post; we feed it back to WordPress as the sole result of the "the_posts" filter.
			$posts   = null;
			$posts[] = $this->create_ngg_tag_post( $tag );

			$wp_query->is_404      = false;
			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;

			unset( $wp_query->query['error'] );
			$wp_query->query_vars['error'] = '';
		}

		if ( $wp_query_orig !== false ) {
			$wp_query = $wp_query_orig;
		}

		return $posts;
	}

	public function create_ngg_tag_post( $tag ) {
		$title = sprintf( __( 'Images tagged &quot;%s&quot;', 'nggallery' ), $tag );
		$title = \apply_filters( 'ngg_basic_tagcloud_title', $title, $tag );

		$post                 = new \stdClass();
		$post->post_author    = false;
		$post->post_name      = 'ngg_tag';
		$post->guid           = \get_bloginfo( 'wpurl' ) . '/' . 'ngg_tag';
		$post->post_title     = $title;
		$post->post_content   = $this->render_tag( $tag );
		$post->ID             = false;
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
