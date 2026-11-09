<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_TC_Options {

	/**
	 * Setup the class
	 */
	public function setup() {

		add_filter( 'dlm_settings', array( $this, 'add_settings' ) );
	}

	/**
	 * Add Terms and Conditions Lock settings
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function add_settings( $settings ) {
		$settings['lead_generation']['sections']['terns_and_conditions']['title'] = __( 'Terms & Conditions', 'download-monitor' );
		$settings['lead_generation']['sections']['terns_and_conditions']['fields'] = array(
			array(
				'title' => __( 'Terms & Conditions', 'download-monitor'),
				'name'  => 'dlm_tc_text',
				'std'   => __( 'I accept the terms & conditions', 'download-monitor' ),
				'label' => __( 'Terms & Condition Text', 'download-monitor' ),
				'desc'  => __( 'The text that visitors need to accept, is displayed next to the checkbox. Use <code>%%terms_conditions%%</code> to add a link to the terms and conditions page selected below.', 'download-monitor' ),
				'type'  => 'editor'
			),
			array(
				'title' => __( 'Terms & Conditions page', 'download-monitor' ),
				'name'    => 'dlm_tc_content_page',
				'std'     => '',
				'label'   => __( 'Terms and Conditions page', 'download-monitor' ),
				'desc'    => __( "Choose what page the users are redirected to when they click <code>%%terms_conditions%%</code> in the acceptance message.", 'download-monitor' ),
				'type'    => 'lazy_select',
				'options' => array(),
				'width'   => 'half'
			),
			array(
				'title' => __( 'Global setting', 'download-monitor' ),
				'name'     => 'dlm_tc_global',
				'std'      => '',
				'cb_label' => __( '', 'download-monitor' ),
				'label'    => __( 'All downloads require Terms & Conditions', 'download-monitor' ),
				'desc'     => __( 'Require all of your downloads to have your terms & conditions checked.', 'download-monitor' ),
				'type'     => 'checkbox'
			),
		);

		return $settings;
	}

}
