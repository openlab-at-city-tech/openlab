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

		add_filter('term_links-category', function ($links) use ($style) {
			foreach ($links as &$link) {
				// Skip if the <a> already has a 'style' attribute or a class for a category-link
				if (preg_match('/<a[^>]*style=[\'"][^\'"]+[\'"]/i', $link) || preg_match('/class=["\'][^"\']*category-link-\d+[^"\']*["\']/', $link)) {
					continue;
				}

				// Extract the anchor text (content between > and </a>)
				if (preg_match('/<a[^>]*>(.*?)<\/a>/i', $link, $matches)) {
					$anchor_content = $matches[1]; // Get the anchor text

					// Convert the anchor text to lowercase to match the slug
					$term_slug = sanitize_title($anchor_content);

					// Get the term object by slug (using the taxonomy, e.g., 'category')
					$term = get_term_by('slug', $term_slug, 'category'); // Replace 'category' with your taxonomy

					if ($term) {
						$term_id = $term->term_id;

						// Get the custom color and hover color values
						$color = get_term_meta($term_id, 'archive_category_color', true);
						$hover_color = get_term_meta($term_id, 'archive_category_hover_color', true);

						// If the color exists, modify the link and add a <style> tag
						if ($color) {
							// Add a class to the anchor for the category ID
							$link = preg_replace(
								'/<a\s+/i',
								'<a class="category-link-' . esc_attr($term_id) . '" ',
								$link
							);

							// Append the style after the <a> tag
							if ('pill' === $style) {
								// Use background-color for pill style
								$style_tag =
									'<style>
								.loop-entry.type-post .entry-taxonomies a.category-link-' . esc_attr($term_id) . ' {
									background-color: ' . esc_attr($color) . ';
								}
								.loop-entry.type-post .entry-taxonomies a.category-link-' . esc_attr($term_id) . ':hover {
									background-color: ' . esc_attr($hover_color) . ';
								}
							</style>';
							} else {
								// Use color for regular style
								$style_tag =
									'<style>
								.loop-entry.type-post .entry-taxonomies a.category-link-' . esc_attr($term_id) . ' {
									color: ' . esc_attr($color) . ';
								}
								.loop-entry.type-post .entry-taxonomies a.category-link-' . esc_attr($term_id) . ':hover {
									color: ' . esc_attr($hover_color) . ';
								}
							</style>';
							}

							$link .= $style_tag; // Append the style element after the <a> tag
						}
					}
				}
			}

			return $links;
		});

		?>
		<div class="entry-taxonomies">
			<span class="category-links term-links category-style-<?php echo esc_attr( $style ); ?>">
				<?php echo get_the_term_list( get_the_ID(), $tax_slug, '', esc_html( $separator ), '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div><!-- .entry-taxonomies -->
		<?php
	}
}
