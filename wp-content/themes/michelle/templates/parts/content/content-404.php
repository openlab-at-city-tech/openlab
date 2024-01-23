<?php
/**
 * Template part for displaying default error 404 content.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.6
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_template_part( 'templates/parts/component/page-header', '404' );

?>

<div class="page-content error-404 not-found">
	<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'michelle' ); ?></p>

	<?php get_search_form(); ?>
</div>
