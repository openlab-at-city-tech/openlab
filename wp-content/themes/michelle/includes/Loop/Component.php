<?php
/**
 * Loop component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.12
 */

namespace WebManDesign\Michelle\Loop;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Pagination.
			Pagination::init();
			// Featured posts.
			Featured_Posts::init();

			// Actions

				add_action( 'michelle/postslist/before', __CLASS__ . '::search_form' );

	} // /init

	/**
	 * Output search form on top of search results page.
	 *
	 * @since    1.0.0
	 * @version  1.0.12
	 *
	 * @return  void
	 */
	public static function search_form() {

		// Requirements check

			if ( ! is_search() ) {
				return;
			}


		// Output

			get_search_form( true );

			get_template_part( 'templates/parts/component/search-results-count' );

	} // /search_form

}
