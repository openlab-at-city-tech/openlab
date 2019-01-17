<?php

su_add_shortcode( array(
		'id' => 'table',
		'callback' => 'su_shortcode_table',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/table.svg',
		'name' => __( 'Table', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'content',
		'atts' => array(
			'url' => array(
				'type' => 'upload',
				'default' => '',
				'name' => __( 'CSV file', 'shortcodes-ultimate' ),
				'desc' => __( 'Upload CSV file if you want to create HTML-table from file', 'shortcodes-ultimate' )
			),
			'responsive' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Responsive', 'shortcodes-ultimate' ),
				'desc' => __( 'Add horizontal scrollbar if table width larger than page width', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( "<table>\n<tr>\n\t<td>Table</td>\n\t<td>Table</td>\n</tr>\n<tr>\n\t<td>Table</td>\n\t<td>Table</td>\n</tr>\n</table>", 'shortcodes-ultimate' ),
		'desc' => __( 'Styled table from HTML or CSV file', 'shortcodes-ultimate' ),
		'icon' => 'table',
	) );

function su_shortcode_table( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'url'   => false,
			'responsive' => false,
			'class' => ''
		), $atts, 'table' );

	if ( $atts['responsive'] ) {
		$atts['class'] .= ' su-table-responsive';
	}

	su_query_asset( 'css', 'su-shortcodes' );
	su_query_asset( 'js', 'jquery' );
	su_query_asset( 'js', 'su-other-shortcodes' );

	$table_data = $atts['url']
		? su_parse_csv( $atts['url'] ) :
		do_shortcode( $content );

	return '<div class="su-table' . su_get_css_class( $atts ) . '">' . $table_data . '</div>';

}
