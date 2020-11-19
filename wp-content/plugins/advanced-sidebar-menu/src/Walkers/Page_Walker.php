<?php

namespace Advanced_Sidebar_Menu\Walkers;

/**
 * This walker's only purpose is to allow us to close our menus only when needed.
 */
class Page_Walker extends \Walker_Page {
	/**
	 * Allow us to close our menus at the appropriate times.
	 *
	 * @param string   $output - Full output of el.
	 * @param \WP_Post $page   - Current page.
	 * @param int      $depth  - Depth of menu.
	 * @param array    $args   - Any arguments.
	 */
	public function end_el( &$output, $page, $depth = 0, $args = [] ) {
		/** Do Nothing */
	}
}
