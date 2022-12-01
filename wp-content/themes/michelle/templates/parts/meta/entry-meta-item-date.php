<?php
/**
 * Post meta: Publish and update date.
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/time.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<span class="entry-meta-item entry-date posted-on">
	<svg class="svg-icon" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8,2C4.7,2,2,4.7,2,8s2.7,6,6,6s6-2.7,6-6S11.3,2,8,2z M10.5,11.5L7.2,8.3V4h1.5v3.7l2.8,2.8L10.5,11.5z"/></svg>

	<span class="entry-meta-description label-published"><?php echo esc_html_x( 'Posted on:', 'Post meta info description: publish date.', 'michelle' ); ?></span>
	<a href="<?php the_permalink(); ?>" rel="bookmark"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>" class="published" title="<?php echo esc_attr_x( 'Posted on:', 'Post meta info description: publish date.', 'michelle' ); echo ' ' . esc_attr( get_the_date() ); ?>"><?php echo esc_html( get_the_date() ); ?></time></a>

	<span class="entry-meta-description label-updated"><?php echo esc_html_x( 'Last updated on:', 'Post meta info description: update date.', 'michelle' ); ?></span>
	<time class="updated" datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>" title="<?php echo esc_attr_x( 'Last updated on:', 'Post meta info description: update date.', 'michelle' ); echo ' ' . esc_attr( get_the_modified_date() ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time>

</span>
