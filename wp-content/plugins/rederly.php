<?php

/*
Plugin Name: Rederly - OpenLab
Description: Customization for WeBWorK to ensure compatibility with Rederly on the OpenLab.
*/

add_filter(
	'webwork_pre_sanitize_post_data',
	function( $data ) {
		$data = [
			'webwork_user' => '',
			'problem_set' => '',
			'problem_number' => '',
			'problem_id' => '',
			'course' => '',
			'section' => '',
			'emailableUrl' => '',
			'randomSeed' => '',
			'notifyAddresses' => '',
			'studentName' => '',
		];

		$data['problem_set'] = sanitize_text_field( wp_unslash( $_POST['problemSetId'] ) );
		$data['problem_id'] = sanitize_text_field( wp_unslash( $_POST['problem'] ) );
		$data['emailableUrl'] = sanitize_text_field( wp_unslash( $_POST['emailURL'] ) );
		$data['studentName'] = sanitize_text_field( wp_unslash( $_POST['studentName'] ) );

		$data['section'] = sanitize_text_field( wp_unslash( $_POST['courseId'] ) );

		$data['notifyAddresses'] = wp_unslash( $_POST['email1'] );

		$text = base64_decode( $_POST['pgObject'] );

		// Rederly sends an entire webpage. We take the content of the 'problem' div.
		$dom    = DOMDocument::loadHTML( $text );
		$finder = new DomXPath( $dom );
		$nodes  = $finder->query( "//*[contains(@class, 'problem-content')]" );
		if ( $nodes && $nodes->length > 0 ) {
			$node = $nodes->item( 0 );
			$text = $dom->saveHTML( $node );
		}

		$pf = new \WeBWorK\Server\Util\ProblemFormatter();
		$text = $pf->clean_problem_from_webwork( $text, $data );

		$data['problem_id'] = sanitize_text_field( wp_unslash( $_POST['problemPath'] ) );

		$data['problem_text'] = $text;
		return $data;
	}
);
