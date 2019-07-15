<?php
/**
 * Multipage Options.
 *
 * @package Multipage
 * @subpackage Options
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Get the default site options and their values.
 *
 * @since 1.4
 *
 * @return array Filtered option names and values.
 */
function mpp_get_default_options() {

	// Default options.
	$options = array (
	
		/* Main Settings ******************************************************/
		
		// Hide the intro title.
		'mpp_hide_intro_title'				=> false,
		
		// Decide on what page show the comments.
		'mpp_comments_on_page'				=> 'all',
		
		// Use the continue or the prev next pagination.
		'mpp_continue_or_prev_next'			=> 'continue',
		
		// Disable the standard pagination.
		'mpp_disable_standard_pagination'	=> true,
		
		// Show the table of contents only on the first page of the post.
		'mpp_toc_only_on_the_first_page'	=> false,
		
		// Set the table of contents position.
		'mpp_toc_position'					=> 'top-right',

		// Set the table of contents position.
		'mpp_toc_row_labels'				=> 'number',
		
		// Hide the table of contents header.
		'mpp_hide_toc_header'				=> false,
		
		// Add a link for the comments inside the table of contents.
		'mpp_comments_toc_link'				=> false,
		
		/* Advanced Settings **************************************************/
		
		// Rewrite Title Priority.
		'mpp_rewrite_title_priority'		=> 20,
		
		// Rewrite Content Priority.
		'mpp_rewrite_content_priority'		=> 20,
		
		// Disable TinyMCE Buttons.
		'mpp_disable_tinymce_buttons'		=> false
		
	);
	
	/**
	 * Filters the default options to be set upon activation.
	 *
	 * @since 1.4
	 *
	 * @param array $options Array of default options to set.
	 */
	return apply_filters( 'mpp_get_default_options', $options );
}

/**
 * Is the standard WordPress pagination disabled on multipage posts?
 *
 * @since 1.4
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool frue if the standard WordPress pagination should be hidden otherwise false.
 */
function mpp_hide_intro_title( $default = false ) {

	/**
	 * Filters whether or not the standard WordPress pagination is disabled.
	 *
	 * @since 1.4
	 *
	 * @param bool $value Whether the standard pagination is disabled.
	 */
	return (bool) apply_filters( 'mpp_hideintro_title', (bool) get_option( 'mpp-hide-intro-title', $default ) );
}

/**
 * Output the pages where comments will appear.
 *
 * @since 1.4
 *
 * @param string $default Optional. Default: all.
 */
function mpp_comments_on_page( $default = 'all' ) {
	echo mpp_get_comments_on_page( $default );
}
	/**
	 * Return the pages where to display comments.
	 *
	 * @since 1.4
	 *
	 * @param string $default Optional. Default: all.
	 * @return int The pages where to display comments.
	 */
	function mpp_get_comments_on_page( $default = 'all' ) {

		/**
		 * Filters the pages where to display comments.
		 *
		 * @since 1.4
		 *
		 * @param string $value The pages where to display comments.
		 */
		return apply_filters( 'mpp_get_comments_on_page', get_option( 'mpp-comments-on-page', $default ) );
	}	

/**
 * Output the navigation type (continue or next and previous).
 *
 * @since 1.4
 *
 * @param string $default Optional. Default: continue.
 */
function mpp_continue_or_prev_next( $default = 'continue' ) {
	echo mpp_get_continue_or_prev_next( $default );
}
	/**
	 * Return the navigation type (continue or next and previous).
	 *
	 * @since 1.4
	 *
	 * @param string $default Optional. Default: continue.
	 * @return string The navigation type.
	 */
	function mpp_get_continue_or_prev_next( $default = 'continue' ) {

		/**
		 * Filters the navigation type.
		 *
		 * @since 1.4
		 *
		 * @param string $value The navigation type.
		 */
		return apply_filters( 'mpp_get_continue_or_prev_next', get_option( 'mpp-continue-or-prev-next', $default ) );
	}

/**
 * Is the standard WordPress pagination disabled on multipage posts?
 *
 * @since 1.4
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool frue if the standard WordPress pagination should be hidden otherwise false.
 */
function mpp_disable_standard_pagination( $default = true ) {

	/**
	 * Filters whether or not the standard WordPress pagination is disabled.
	 *
	 * @since 1.4
	 *
	 * @param bool $value Whether the standard pagination is disabled.
	 */
	return (bool) apply_filters( 'mpp_disable_standard_pagination', (bool) get_option( 'mpp-disable-standard-pagination', $default ) );
}

/**
 * Is the table of contents only on the first page of the post?
 *
 * @since 1.4
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool frue if the table of contents should be only on the first page of the post otherwise false.
 */
function mpp_toc_only_on_the_first_page( $default = false ) {

	/**
	 * Filters whether or not the standard WordPress pagination is disabled.
	 *
	 * @since 1.4
	 *
	 * @param bool $value Whether the standard pagination is disabled.
	 */
	return (bool) apply_filters( 'mpp_toc_only_on_the_first_page', (bool) get_option( 'mpp-toc-only-on-the-first-page', $default ) );
}

/**
 * Output the table of contents position.
 *
 * @since 1.4
 *
 * @param string $default Optional. Default: top-right.
 */
function mpp_toc_position( $default = 'top-right' ) {
	echo mpp_get_toc_position( $default );
}
	/**
	 * Return the table of contents position.
	 *
	 * @since 1.4
	 *
	 * @param string $default Optional. Default: top-right.
	 * @return string The table of contents position.
	 */
	function mpp_get_toc_position( $default = 'top-right' ) {

		/**
		 * Filters the table of contents position.
		 *
		 * @since 1.4
		 *
		 * @param string $value The table of contents positon.
		 */
		return apply_filters( 'mpp_get_toc_position', get_option( 'mpp-toc-position', $default ) );
	}
	
/**
 * Output the table of contents row labels type.
 *
 * @since 1.4
 *
 * @param string $default Optional. Default: number.
 */
function mpp_toc_row_labels( $default = 'number' ) {
	echo mpp_get_toc_row_labels( $default );
}
	/**
	 * Return the table of contents row labels type.
	 *
	 * @since 1.4
	 *
	 * @param string $default Optional. Default: number.
	 * @return string The table of contents row labels type.
	 */
	function mpp_get_toc_row_labels( $default = 'number' ) {

		/**
		 * Filters the table of contents row labels type.
		 *
		 * @since 1.4
		 *
		 * @param string $value The table of contents row labels type.
		 */
		return apply_filters( 'mpp_get_toc_row_labels', get_option( 'mpp-toc-row-labels', $default ) );
	}

/**
 * Is the table of contents header visible or not?
 *
 * @since 1.4
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool true if the table of contents header should be hidden otherwise false.
 */
function mpp_hide_toc_header( $default = false ) {

	/**
	 * Filters whether or not the standard WordPress pagination is disabled.
	 *
	 * @since 1.4
	 *
	 * @param bool $value Whether table of contents header is hidden.
	 */
	return (bool) apply_filters( 'mpp_hide_toc_header', (bool) get_option( 'mpp-hide-toc-header', $default ) );
}

/**
 * Is the table of contents including a link for comments?
 *
 * @since 1.4
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool true if the table of contents is including a link for comments.
 */
function mpp_comments_toc_link( $default = false ) {

	/**
	 * Filters whether or not the table of contents is including a link for comments.
	 *
	 * @since 1.4
	 *
	 * @param bool $value Whether the table of contents is including a link for comments.
	 */
	return (bool) apply_filters( 'mpp_comments_toc_link', (bool) get_option( 'mpp-comments-toc-link', $default ) );
}

/**
 * Output the rewrite titles priority.
 *
 * @since 1.4
 *
 * @param bool|string $default Optional. Default: 20 (normal).
 */
function mpp_rewrite_title_priority( $default = 20 ) {
	echo mpp_get_rewrite_title_priority( $default );
}
	/**
	 * Return the rewrite titles priority.
	 *
	 * @since 1.4
	 *
	 * @param bool|string $default Optional. Default: 20 (normal).
	 * @return int The rewrite titles priority.
	 */
	function mpp_get_rewrite_title_priority( $default = 20 ) {

		/**
		 * Filters the rewrite titles priority.
		 *
		 * @since 1.4
		 *
		 * @param int $value The rewrite titles priority.
		 */
		return (int) apply_filters( 'mpp_get_rewrite_title_priority', (int) get_option( '_mpp-rewrite-title-priority', $default ) );
	}
	
/**
 * Output the rewrite content priority.
 *
 * @since 1.4
 *
 * @param bool|string $default Optional. Default: 20 (normal).
 */
function mpp_rewrite_content_priority( $default = 20 ) {
	echo mpp_get_rewrite_content_priority( $default );
}
	/**
	 * Return the rewrite content priority.
	 *
	 * @since 1.4
	 *
	 * @param bool|string $default Optional. Default: 20 (normal).
	 * @return int The rewrite content priority.
	 */
	function mpp_get_rewrite_content_priority( $default = 20 ) {

		/**
		 * Filters the rewrite content priority.
		 *
		 * @since 1.4
		 *
		 * @param int $value The rewrite content priority.
		 */
		return (int) apply_filters( 'mpp_get_rewrite_content_priority', (int) get_option( '_mpp-rewrite-content-priority', $default ) );
	}

/**
 * Are the TinyMCE Buttons disabled?
 *
 * @since 1.4
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool true if the TinyMCE Buttons should be hidden otherwise false.
 */
function mpp_disable_tinymce_buttons( $default = false ) {

	/**
	 * Filters whether or not TinyMCE Buttons are disabled.
	 *
	 * @since 1.4
	 *
	 * @param bool $value Whether TinyMCE Buttons are disabled.
	 */
	return (bool) apply_filters( 'mpp_disable_tinymce_buttons', (bool) get_option( 'mpp-disable-tinymce-buttons', $default ) );
}
