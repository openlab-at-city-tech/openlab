<?php
/**
 * Main template wrapper.
 *
 * Now we don't have to repeat `get_header()/get_footer()`
 * in other template files all the time.
 *
 * @link  http://scribu.net/wordpress/theme-wrappers.html
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$template_path = Tool\Wrapper::$path;

get_header(
	/**
	 * Filters get_header() parameter.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $name           The name of the specialized header, the get_header() parameter.
	 * @param  string $template_path  The template path requesting the header.
	 */
	(string) apply_filters( 'michelle/get_header', '', $template_path )
);

?>

<?php

/**
 * WordPress uses `include` to load the template.
 * @see  wp-includes/template-loader.php
 */
if ( $template_path ) {
	include $template_path; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
}

?>

<?php

get_footer(
	/**
	 * Filters get_footer() parameter.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $name           The name of the specialized footer, the get_footer() parameter.
	 * @param  string $template_path  The template path requesting the footer.
	 */
	(string) apply_filters( 'michelle/get_footer', '', $template_path )
);
