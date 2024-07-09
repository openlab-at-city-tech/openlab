<?php

/**
 * No Search Results Feedback Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( bbp_get_search_terms() ) : ?>

<div class="bbp-template-notice">
	<ul>
		<li><?php esc_html_e( 'Oh, bother! No search results were found here.', 'bbpress' ); ?></li>
	</ul>
</div>

<?php else : ?>

<div class="bbp-template-notice">
	<ul>
		<li><?php esc_html_e( 'Please enter some search terms.', 'bbpress' ); ?></li>
	</ul>
</div>

<?php endif;
