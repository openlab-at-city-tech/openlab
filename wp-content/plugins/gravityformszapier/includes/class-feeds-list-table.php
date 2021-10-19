<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class GF_Zapier_Feeds_List_Table
 *
 * @since 4.1
 */
class GF_Zapier_Feeds_List_Table extends GFAddOnFeedsTable {

	/**
	 * Outputs the feeds message and list table.
	 *
	 * @since 4.1
	 */
	public function display() {
		if ( gf_zapier()->is_gravityforms_supported( '2.5-rc-1' ) ) {
			$open  = '<div class="alert info">';
			$close = '</div>';
		} else {
			$open  = '<p class="notice notice-large notice-info">';
			$close = '</p>';
		}

		echo $open . sprintf(
				// Translators: 1. Opening <a> tag for link to Zapier, 2. Closing <a> tag. 3. Opening <a> tag for link to Gravity Forms Zapier documentation. 4. Closing <a> tag.
				esc_html__( 'Zapier feeds are created automatically when %1$szaps are configured%2$s on zapier.com. %3$sLearn more%4$s.', 'gravityformszapier' ),
				'<a href="' . esc_url( 'https://zapier.com/apps/gravity-forms/integrations' ) . '" target="_blank">',
				'</a>',
				'<a href="' . esc_url( 'https://docs.gravityforms.com/zapier-add-on/' ) . '" target="_blank">',
				'</a>'
			) . $close;

		parent::display();
	}

}