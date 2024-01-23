<?php
/**
 * Search results count.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.12
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<p class="search-results-count">
	<?php

	printf(
		esc_html(
			/* translators: %d: The number of search results. */
			_n(
				'We found %d result for your search.',
				'We found %d results for your search.',
				(int) $wp_query->found_posts,
				'michelle'
			)
		),
		(int) $wp_query->found_posts
	);

	?>
</p>
