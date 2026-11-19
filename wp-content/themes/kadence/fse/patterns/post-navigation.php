<?php
/**
 * Links to the previous and next post, matching the classic post navigation.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;

$navigation_attributes = [
	'tagName'   => 'nav',
	'ariaLabel' => __( 'Posts', 'kadence' ),
	'className' => 'navigation post-navigation',
	'layout'    => [ 'type' => 'default' ],
];

$previous_attributes = [
	'type'      => 'previous',
	'label'     => __( 'Previous', 'kadence' ),
	'showTitle' => true,
	'linkLabel' => true,
	'className' => 'nav-previous',
];

$next_attributes = [
	'label'     => __( 'Next', 'kadence' ),
	'showTitle' => true,
	'linkLabel' => true,
	'className' => 'nav-next',
];
?>
<!-- wp:group <?php echo serialize_block_attributes( $navigation_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<nav aria-label="<?php echo esc_attr__( 'Posts', 'kadence' ); ?>" class="wp-block-group navigation post-navigation"><!-- wp:group {"className":"nav-links","layout":{"type":"default"}} -->
<div class="wp-block-group nav-links"><!-- wp:post-navigation-link <?php echo serialize_block_attributes( $previous_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /-->

<!-- wp:post-navigation-link <?php echo serialize_block_attributes( $next_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></nav>
<!-- /wp:group -->
