<?php
/**
 * The footer template part's content: a row of columns to fill with blocks, then the copyright row.
 *
 * Like the classic footer's default, only the copyright row shows until someone
 * fills a column. The columns are three across on desktop, two on tablet with the
 * first column spanning both, and one on mobile, through core's per-viewport
 * layout styles.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;

$edge_padding = [
	'right' => 'var(--global-content-edge-padding)',
	'left'  => 'var(--global-content-edge-padding)',
];

// The row's top and bottom padding sits on the columns, so the row takes no space while the columns are hidden.
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

$column_attributes = [];
foreach ( [ 1, 2, 3 ] as $column_number ) {
	$column_attributes[ $column_number ] = [
		'metadata' => [
			// translators: %d is the column number.
			'name' => sprintf( __( 'Column %d', 'kadence' ), $column_number ),
		],
		// A Stack, so the Site Editor offers the block inserter in an empty column instead of the Group layout picker.
		'layout'   => [
			'type'           => 'flex',
			'orientation'    => 'vertical',
			'justifyContent' => 'stretch',
		],
	];
}
$column_attributes[1]['style'] = [ '@tablet' => [ 'layout' => [ 'columnSpan' => 2 ] ] ];

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
<div class="wp-block-group"></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $column_attributes[2] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $column_attributes[3] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"></div>
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
