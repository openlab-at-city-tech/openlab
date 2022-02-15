<?php
/**
 * Server-side rendering of the `core/table-of-contents` block.
 *
 * @package WordPress
 */

/**
 * Extracts heading content, id, page, and level from the given post content.
 *
 * @access private
 *
 * @param string $content       The post content to extract headings from.
 * @param int    $headings_page The page of the post where the headings are
 *                              located.
 *
 * @return array The list of headings.
 */
function gutenberg_gutenberg_block_core_table_of_contents_get_headings_from_content(
	$content,
	$headings_page = 1
) {
	/* phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */
	// Disabled because of PHP DOMDocument and DOMXPath APIs using camelCase.

	// Create a document to load the post content into.
	$doc = new DOMDocument();

	// Enable user error handling for the HTML parsing. HTML5 elements aren't
	// supported (as of PHP 7.4) and There's no way to guarantee that the markup
	// is valid anyway, so we're just going to ignore all errors in parsing.
	// Nested heading elements will still be parsed.
	// The lack of HTML5 support is a libxml2 issue:
	// https://bugzilla.gnome.org/show_bug.cgi?id=761534.
	libxml_use_internal_errors( true );

	// Parse the post content into an HTML document.
	$doc->loadHTML(
		// loadHTML expects ISO-8859-1, so we need to convert the post content to
		// that format. We use htmlentities to encode Unicode characters not
		// supported by ISO-8859-1 as HTML entities. However, this function also
		// converts all special characters like < or > to HTML entities, so we use
		// htmlspecialchars_decode to decode them.
		htmlspecialchars_decode(
			utf8_decode(
				htmlentities(
					'<html><body>' . $content . '</body></html>',
					ENT_COMPAT,
					'UTF-8',
					false
				)
			),
			ENT_COMPAT
		)
	);

	// We're done parsing, so we can disable user error handling. This also
	// clears any existing errors, which helps avoid a memory leak.
	libxml_use_internal_errors( false );

	// IE11 treats template elements like divs, so to avoid extracting heading
	// elements from them, we first have to remove them.
	// We can't use foreach directly on the $templates DOMNodeList because it's a
	// dynamic list, and removing nodes confuses the foreach iterator. So
	// instead, we convert the iterator to an array and then iterate over that.
	$templates = iterator_to_array(
		$doc->documentElement->getElementsByTagName( 'template' )
	);

	foreach ( $templates as $template ) {
		$template->parentNode->removeChild( $template );
	}

	$xpath = new DOMXPath( $doc );

	// Get all non-empty heading elements in the post content.
	$headings = iterator_to_array(
		$xpath->query(
			'//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6][text()!=""]'
		)
	);

	return array_map(
		function ( $heading ) use ( $headings_page ) {
			$id = null;

			if ( isset( $heading->attributes ) ) {
				$id_attribute = $heading->attributes->getNamedItem( 'id' );

				if ( null !== $id_attribute && '' !== $id_attribute->nodeValue ) {
					$id = $id_attribute->nodeValue;
				}
			}

			return array(
				// A little hacky, but since we know at this point that the tag will
				// be an h1-h6, we can just grab the 2nd character of the tag name
				// and convert it to an integer. Should be faster than conditionals.
				'level'   => (int) $heading->nodeName[1],
				'id'      => $id,
				'page'    => $headings_page,
				'content' => $heading->textContent,
			);
		},
		$headings
	);
	/* phpcs:enable */
}

/**
 * Gets the content, anchor, level, and page of headings from a post. Returns
 * data from all headings in a paginated post if $current_page_only is false;
 * otherwise, returns only data from headings on the current page being
 * rendered.
 *
 * @access private
 *
 * @param int  $post_id           Id of the post to extract headings from.
 * @param bool $current_page_only Whether to include headings from the entire
 *                                post, or just those from the current page (if
 *                                the post is paginated).
 *
 * @return array The list of headings.
 */
function gutenberg_block_core_table_of_contents_get_headings(
	$post_id,
	$current_page_only
) {
	global $multipage, $page, $pages;

	if ( $multipage ) {
		// Creates a list of heading lists, one list per page.
		$pages_of_headings = array_map(
			function( $page_content, $page_index ) {
				return gutenberg_gutenberg_block_core_table_of_contents_get_headings_from_content(
					$page_content,
					$page_index + 1
				);
			},
			$pages,
			array_keys( $pages )
		);

		if ( $current_page_only ) {
			// Return the headings from the current page.
			return $pages_of_headings[ $page - 1 ];
		} else {
			// Concatenate the heading lists into a single array and return it.
			return array_merge( ...$pages_of_headings );
		}
	} else {
		// Only one page, so return headings from entire post_content.
		return gutenberg_gutenberg_block_core_table_of_contents_get_headings_from_content(
			get_post( $post_id )->post_content
		);
	}
}

/**
 * Converts a flat list of heading parameters to a hierarchical nested list
 * based on each header's immediate parent's level.
 *
 * @access private
 *
 * @param array $heading_list Flat list of heading parameters to nest.
 * @param int   $index        The current list index.
 *
 * @return array A hierarchical nested list of heading parameters.
 */
function gutenberg_block_core_table_of_contents_linear_to_nested_heading_list(
	$heading_list,
	$index = 0
) {
	$nested_heading_list = array();

	foreach ( $heading_list as $key => $heading ) {
		// Make sure we are only working with the same level as the first
		// iteration in our set.
		if ( $heading['level'] === $heading_list[0]['level'] ) {
			// Check that the next iteration will return a value.
			// If it does and the next level is greater than the current level,
			// the next iteration becomes a child of the current iteration.
			if (
				isset( $heading_list[ $key + 1 ] ) &&
				$heading_list[ $key + 1 ]['level'] > $heading['level']
			) {
				// We need to calculate the last index before the next iteration
				// that has the same level (siblings). We then use this last index
				// to slice the array for use in recursion. This prevents duplicate
				// nodes.
				$heading_list_length = count( $heading_list );
				$end_of_slice        = $heading_list_length;
				for ( $i = $key + 1; $i < $heading_list_length; $i++ ) {
					if ( $heading_list[ $i ]['level'] === $heading['level'] ) {
						$end_of_slice = $i;
						break;
					}
				}

				// Found a child node: Push a new node onto the return array with
				// children.
				$nested_heading_list[] = array(
					'heading'  => $heading,
					'index'    => $index + $key,
					'children' => gutenberg_block_core_table_of_contents_linear_to_nested_heading_list(
						array_slice(
							$heading_list,
							$key + 1,
							$end_of_slice - ( $key + 1 )
						),
						$index + $key + 1
					),
				);
			} else {
				// No child node: Push a new node onto the return array.
				$nested_heading_list[] = array(
					'heading'  => $heading,
					'index'    => $index + $key,
					'children' => null,
				);
			}
		}
	}

	return $nested_heading_list;
}

/**
 * Renders the heading list of the `core/table-of-contents` block on server.
 *
 * @access private
 *
 * @param array  $nested_heading_list Nested list of heading data.
 * @param string $page_url            URL of the page the block belongs to.
 *
 * @return string The heading list rendered as HTML.
 */
function gutenberg_block_core_table_of_contents_render_list(
	$nested_heading_list,
	$page_url
) {
	$entry_class = 'wp-block-table-of-contents__entry';

	$child_nodes = array_map(
		function ( $child_node ) use ( $entry_class, $page_url ) {
			global $multipage;

			$id      = $child_node['heading']['id'];
			$content = $child_node['heading']['content'];

			if ( isset( $id ) ) {
				if ( $multipage ) {
					$href = add_query_arg(
						'page',
						(string) $child_node['heading']['page'],
						remove_query_arg( 'page', $page_url )
					) . '#' . $id;
				} else {
					$href = $page_url . '#' . $id;
				}

				$entry = sprintf(
					'<a class="%1$s" href="%2$s">%3$s</a>',
					esc_attr( $entry_class ),
					esc_url( $href ),
					esc_html( $content )
				);
			} else {
				$entry = sprintf(
					'<span class="%1$s">%2$s</span>',
					esc_attr( $entry_class ),
					esc_html( $content )
				);
			}

			return sprintf(
				'<li>%1$s%2$s</li>',
				$entry,
				$child_node['children']
					? gutenberg_block_core_table_of_contents_render_list(
						$child_node['children'],
						esc_url( $page_url )
					)
					: null
			);
		},
		$nested_heading_list
	);

	return '<ul>' . implode( $child_nodes ) . '</ul>';
}

/**
 * Renders the `core/table-of-contents` block on server.
 *
 * @access private
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  WP_Block $block      Block instance.
 *
 * @return string Rendered block HTML.
 */
function gutenberg_render_block_core_table_of_contents( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$headings = gutenberg_block_core_table_of_contents_get_headings(
		$block->context['postId'],
		$attributes['onlyIncludeCurrentPage']
	);

	// If there are no headings.
	if ( count( $headings ) === 0 ) {
		return '';
	}

	return sprintf(
		'<nav %1$s>%2$s</nav>',
		get_block_wrapper_attributes(),
		gutenberg_block_core_table_of_contents_render_list(
			gutenberg_block_core_table_of_contents_linear_to_nested_heading_list( $headings ),
			get_permalink( $block->context['postId'] )
		)
	);
}

/**
 * Registers the `core/table-of-contents` block on server.
 *
 * @access private
 *
 * @uses gutenberg_render_block_core_table_of_contents()
 *
 * @throws WP_Error An exception parsing the block definition.
 */
function gutenberg_register_block_core_table_of_contents() {
	register_block_type_from_metadata(
		__DIR__ . '/table-of-contents',
		array(
			'render_callback' => 'gutenberg_render_block_core_table_of_contents',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_table_of_contents', 20 );
