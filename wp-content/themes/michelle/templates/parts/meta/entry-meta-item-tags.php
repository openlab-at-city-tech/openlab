<?php
/**
 * Post meta: Tags.
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/tag.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/* translators: Used between list items, there is a space after the comma. */
$separate_meta = '<span class="term-separator">' . esc_html_x( ', ', 'Tags list separator.', 'michelle' ) . '</span>';
$tags_list     = get_the_tag_list( '', $separate_meta, '', get_the_ID() );

if (
	is_wp_error( $tags_list )
	|| ! $tags_list
) {
	return;
}

?>

<span class="entry-meta-item tags-links">
	<svg class="svg-icon" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M11.3,4.3C11.1,4.1,10.9,4,10.6,4H3C2.5,4,2,4.5,2,5v6c0,0.6,0.5,1,1,1h7.6c0.3,0,0.5-0.1,0.7-0.3L15,8L11.3,4.3z M10,9C9.5,9,9,8.5,9,8s0.5-1,1-1s1,0.5,1,1S10.5,9,10,9z"/></svg>

	<span class="entry-meta-description"><?php echo esc_html_x( 'Tagged as:', 'Post meta info description: tags list.', 'michelle' ); ?></span>
	<?php echo $tags_list; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
</span>
