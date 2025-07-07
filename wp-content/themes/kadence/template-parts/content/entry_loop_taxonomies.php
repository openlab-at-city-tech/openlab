<?php
/**
 * Template part for displaying a post's category terms
 *
 * @package kadence
 */

namespace Kadence;

$slug     = ( is_search() ? 'search' : get_post_type() );
$elements = kadence()->option( $slug . '_archive_element_categories' );

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
		 if ('pill' === $style ) {
			$separator = ' ';
		}

		?>
		<div class="entry-taxonomies">
			<span class="category-links term-links category-style-<?php echo esc_attr( $style ); ?>">
				<?php
					if ( $tax_slug === 'category' ) {
						$categories = get_the_terms(get_the_ID(), $tax_slug); // get_the_categories()
						
						if( ! empty( $categories ) ) {
							$category_html = '';
							foreach ( $categories as $key => $category ) {
								$color = get_term_meta( $category->term_id, 'archive_category_color', true );
								$hover_color = get_term_meta( $category->term_id, 'archive_category_hover_color', true );
								
								if ($color !== '' || $hover_color !== '') {
									$category_html .= '<style>';
									if ( $color !== '') {
										$category_html .=
											'.loop-entry.type-post .entry-taxonomies a.category-link-' . esc_attr( $category->slug ) . ' {
											' . ( $style === 'pill' ? 'background-color' : 'color') . ': ' . esc_attr( $color ) . ';
										}'
										;
									}
									if ( $hover_color !== '') {
										$category_html .=
											'.loop-entry.type-post .entry-taxonomies a.category-link-' . esc_attr( $category->slug )  . ':hover {
											' . ( $style === 'pill' ? 'background-color' : 'color') . ': ' . esc_attr( $hover_color ) . ';
										}'
										;
									}
									$category_html .= '</style>';
								}
								
								$category_html .= '<a href="' . esc_url( get_term_link( $category->term_id ) ) . '" class="category-link-' . esc_attr( $category->slug ) . '" rel="tag">' . esc_attr__( $category->name) . '</a>';
								
								if ( $key < count($categories) - 1 ) {
									$category_html .= esc_html( $separator );
								}
							}
							echo $category_html;
						}
					} else {
						echo get_the_term_list(get_the_ID(), $tax_slug, '', esc_html($separator), ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				?>
			</span>
		</div><!-- .entry-taxonomies -->
		<?php
	}
}
