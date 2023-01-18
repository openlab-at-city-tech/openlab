<?php
/**
 * The template for displaying the footer.
 *
 * @link  https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

do_action( 'tha_content_bottom' );
do_action( 'tha_content_after' );

do_action( 'tha_footer_before' );
do_action( 'tha_footer_top' );
do_action( 'tha_footer_bottom' );
do_action( 'tha_footer_after' );

do_action( 'tha_body_bottom' );

wp_footer();

?>

</body>


</html>
