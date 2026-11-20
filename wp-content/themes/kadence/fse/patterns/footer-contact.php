<?php
/**
 * A footer design: columns for the site, its links and contact details, then the copyright row.
 *
 * Like the default footer, the columns are three across on desktop, two on tablet with the
 * first column spanning both, and one on mobile.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;

$edge_padding = [
	'right' => 'var(--global-content-edge-padding)',
	'left'  => 'var(--global-content-edge-padding)',
];

$columns_row_attributes = [
	'metadata' => [ 'name' => __( 'Footer columns', 'kadence' ) ],
	'style'    => [ 'spacing' => [ 'padding' => $edge_padding ] ],
	'layout'   => [ 'type' => 'constrained' ],
];

$columns_attributes = [
	'metadata'  => [ 'name' => __( 'Columns', 'kadence' ) ],
	'className' => 'is-style-kadence-footer-columns',
	'style'     => [
		'spacing' => [
			'padding' => [
				'top'    => 'var:preset|spacing|lg',
				'bottom' => 'var:preset|spacing|lg',
			],
		],
		'@tablet' => [ 'layout' => [ 'columnCount' => 2 ] ],
		'@mobile' => [ 'layout' => [ 'columnCount' => 1 ] ],
	],
	'layout'    => [
		'type'        => 'grid',
		'columnCount' => 3,
	],
];

$column_names = [
	1 => __( 'About', 'kadence' ),
	2 => __( 'Links', 'kadence' ),
	3 => __( 'Contact', 'kadence' ),
];

$column_attributes = [];
foreach ( $column_names as $column_number => $column_name ) {
	$column_attributes[ $column_number ] = [
		'metadata' => [ 'name' => $column_name ],
		'layout'   => [
			'type'           => 'flex',
			'orientation'    => 'vertical',
			'justifyContent' => 'stretch',
		],
	];
}
$column_attributes[1]['style'] = [ '@tablet' => [ 'layout' => [ 'columnSpan' => 2 ] ] ];

$links_attributes = [
	'ariaLabel'   => __( 'Footer', 'kadence' ),
	'overlayMenu' => 'never',
	'layout'      => [
		'type'        => 'flex',
		'orientation' => 'vertical',
	],
];

$links = [
	[
		'label' => __( 'About', 'kadence' ),
		'url'   => '#',
	],
	[
		'label' => __( 'Services', 'kadence' ),
		'url'   => '#',
	],
	[
		'label' => __( 'Contact', 'kadence' ),
		'url'   => '#',
	],
];

$contact_lines = [
	__( '123 Example Street, City', 'kadence' ),
	'hello@example.com',
	__( '+1 (555) 555-0100', 'kadence' ),
];

$bottom_row_attributes = [
	'metadata' => [ 'name' => __( 'Footer bottom', 'kadence' ) ],
	'style'    => [
		'spacing' => [
			'padding' => array_merge(
				[
					'top'    => 'var:preset|spacing|md',
					'bottom' => 'var:preset|spacing|md',
				],
				$edge_padding
			),
		],
	],
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
<!-- wp:group <?php echo serialize_block_attributes( $columns_row_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group" style="padding-right:var(--global-content-edge-padding);padding-left:var(--global-content-edge-padding)"><!-- wp:group <?php echo serialize_block_attributes( $columns_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group is-style-kadence-footer-columns" style="padding-top:var(--wp--preset--spacing--lg);padding-bottom:var(--wp--preset--spacing--lg)"><!-- wp:group <?php echo serialize_block_attributes( $column_attributes[1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:site-title {"level":0,"fontSize":"large"} /-->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Tell visitors about your business in a sentence or two.', 'kadence' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $column_attributes[2] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"medium"} -->
<h2 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Links', 'kadence' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:navigation <?php echo serialize_block_attributes( $links_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<!-- wp:navigation-link <?php echo serialize_block_attributes( $links[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /-->

<!-- wp:navigation-link <?php echo serialize_block_attributes( $links[1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /-->

<!-- wp:navigation-link <?php echo serialize_block_attributes( $links[2] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /-->
<!-- /wp:navigation --></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $column_attributes[3] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"medium"} -->
<h2 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Contact', 'kadence' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo implode( '<br>', array_map( 'esc_html', $contact_lines ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- each line is escaped. ?></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"size":"has-small-icon-size"} -->
<ul class="wp-block-social-links has-small-icon-size"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $bottom_row_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--md);padding-right:var(--global-content-edge-padding);padding-bottom:var(--wp--preset--spacing--md);padding-left:var(--global-content-edge-padding)"><!-- wp:paragraph -->
<p>&copy;</p>
<!-- /wp:paragraph -->

<!-- wp:site-title {"level":0,"isLink":false} /-->

<!-- wp:paragraph -->
<p><?php echo wp_kses_post( $credit ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
