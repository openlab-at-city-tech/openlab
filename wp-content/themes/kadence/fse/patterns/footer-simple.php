<?php
/**
 * A footer design: social links centered above the copyright row.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;

$footer_attributes = [
	'style'  => [
		'spacing' => [
			'padding' => [
				'top'    => 'var:preset|spacing|lg',
				'right'  => 'var(--global-content-edge-padding)',
				'bottom' => 'var:preset|spacing|md',
				'left'   => 'var(--global-content-edge-padding)',
			],
		],
	],
	'layout' => [ 'type' => 'constrained' ],
];

$social_attributes = [
	'size'   => 'has-small-icon-size',
	'layout' => [
		'type'           => 'flex',
		'justifyContent' => 'center',
	],
];

$bottom_row_attributes = [
	'metadata' => [ 'name' => __( 'Footer bottom', 'kadence' ) ],
	'layout'   => [
		'type'           => 'flex',
		'flexWrap'       => 'wrap',
		'justifyContent' => 'center',
	],
];

$credit = sprintf(
	// translators: %s is link to Kadence WP.
	__( '- WordPress Theme by %s', 'kadence' ),
	'<a href="https://www.kadencewp.com/" rel="nofollow noopener">Kadence WP</a>'
);
?>
<!-- wp:group <?php echo serialize_block_attributes( $footer_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--lg);padding-right:var(--global-content-edge-padding);padding-bottom:var(--wp--preset--spacing--md);padding-left:var(--global-content-edge-padding)"><!-- wp:social-links <?php echo serialize_block_attributes( $social_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<ul class="wp-block-social-links has-small-icon-size"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
<!-- /wp:social-links -->

<!-- wp:group <?php echo serialize_block_attributes( $bottom_row_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:paragraph -->
<p>&copy;</p>
<!-- /wp:paragraph -->

<!-- wp:site-title {"level":0,"isLink":false} /-->

<!-- wp:paragraph -->
<p><?php echo wp_kses_post( $credit ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
