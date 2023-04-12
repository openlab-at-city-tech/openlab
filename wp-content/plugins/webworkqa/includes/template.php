<?php

/**
 * Template functions.
 *
 * @since 1.0.0
 */

/**
 * Get the post_data corresponding to the current query var.
 *
 * @since 1.0.0
 *
 * @return bool|array
 */
function webwork_get_current_post_data() {
	$data = false;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['post_data_key'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_data_key = sanitize_text_field( wp_unslash( $_GET['post_data_key'] ) );

		// Todo need a way to clean up old values from options table.
		// Maybe: put in non-persistent cache, and delete immediately.
		$data = get_option( $post_data_key );
		if ( ! $data ) {
			$data = false;
		}

		// Decode 'pg_object', which is an HTML representation of the question.
		if ( isset( $data['pg_object'] ) ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$data['pg_object'] = base64_decode( $data['pg_object'] );
		}
	}

	return $data;
}

/**
 * Clean the contents of the 'pg_object' for display in the WP context.
 *
 * @since 1.0.0
 */
function webwork_prepare_pg_object( $o ) {
	wp_enqueue_script( 'webwork-mathjax-loader' );

	// Thought about using DOMDocument but it is awful.
	return preg_replace( '|<script type="text/javascript">.*?</script>|s', '', $o );
}

/**
 * Get the current wwclass object.
 *
 * @since 1.0.0
 *
 * @return bool|\WeBWorK\WWClass
 */
function webwork_get_current_wwclass() {
	static $wwclass;

	if ( empty( $wwclass ) ) {
		$current_object_id   = 0;
		$current_object_type = '';
		foreach ( webwork()->get_integrations() as $key => $class ) {
			$current_object_id = $class::get_current_object_id();
			if ( $current_object_id ) {
				$current_object_type = $key;
				break;
			}
		}

		if ( ! $current_object_id ) {
			return false;
		}

		$wwclass = webwork_get_wwclass(
			array(
				'object_type' => $current_object_id,
				'object_id'   => $current_object_id,
			)
		);
	}

	return $wwclass;
}

/**
 * Get a list of related questions.
 *
 * @todo make it possible to pass params?
 *
 * @since 1.0.0
 *
 * @return \WeBWorK\RelatedQuestions
 */
function webwork_get_related_questions() {
	$post_data = webwork_get_current_post_data();

	$args = array();
	foreach ( array( 'set', 'problem' ) as $key ) {
		if ( isset( $post_data[ $key ] ) ) {
			$args[ $key ] = $post_data[ $key ];
		}
	}

	$wwclass = webwork_get_current_wwclass();
	if ( ! $wwclass ) {
		return false;
	}
	return $wwclass->get_related_questions( $args );
}
