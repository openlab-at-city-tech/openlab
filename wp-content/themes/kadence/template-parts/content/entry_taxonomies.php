<?php
/**
 * Template part for displaying a post's category terms
 *
 * @package kadence
 */

namespace Kadence;

$elements = kadence()->option( get_post_type() . '_title_element_categories' );
if ( isset( $elements ) && is_array( $elements ) && true === $elements['enabled'] ) {
	$tax_slug = ( isset( $elements['taxonomy'] ) && ! empty( $elements['taxonomy'] ) ? $elements['taxonomy'] : 'category' );
	if ( has_term( '', $tax_slug ) ) {
		$divider  = ( isset( $elements['divider'] ) && ! empty( $elements['divider'] ) ? $elements['divider'] : 'vline' );
		$style    = ( isset( $elements['style'] ) && ! empty( $elements['style'] ) ? $elements['style'] : 'normal' );
		switch ( $divider ) {
			case 'dot':
				$separator = ' &middot; ';
				break;
			case 'slash':
				/* translators: separator between taxonomy terms */
				$separator = _x( ' / ', 'list item separator', 'kadence' );
				break;
			case 'dash':
				/* translators: separator between taxonomy terms */
				$separator = _x( ' - ', 'list item separator', 'kadence' );
				break;
			default:
				/* translators: separator between taxonomy terms */
				$separator = _x( ' | ', 'list item separator', 'kadence' );
				break;
		}
		if ( 'pill' === $style ) {
			$separator = ' ';
		}
		?>
		<div class="entry-taxonomies">
			<span class="category-links term-links category-style-<?php echo esc_attr( $style ); ?>">
				<?php echo get_the_term_list( get_the_ID(), $tax_slug, '', esc_html( $separator ), '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div><!-- .entry-taxonomies -->
		<?php
	}
}
