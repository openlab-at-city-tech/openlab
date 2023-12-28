<?php
/**
 * Post meta: Category.
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/category.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/* translators: Used between list items, there is a space after the comma. */
$separate_meta   = '<span class="term-separator">' . esc_html_x( ', ', 'Categories list separator.', 'michelle' ) . '</span>';
$categories_list = get_the_category_list( $separate_meta, '', get_the_ID() );

if ( empty( $categories_list ) ) {
	return;
}

?>

<span class="entry-meta-item cat-links">
	<svg class="svg-icon" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M13,5H8L7.3,3.6C7.1,3.2,6.8,3,6.4,3H3C2.5,3,2,3.5,2,4v2v6c0,0.6,0.5,1,1,1h10c0.6,0,1-0.4,1-1V6C14,5.4,13.6,5,13,5z"/></svg>

	<span class="entry-meta-description"><?php echo esc_html_x( 'Categorized in:', 'Post meta info description: categories list.', 'michelle' ); ?></span>
	<?php echo $categories_list; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
</span>
