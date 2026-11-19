<?php
namespace AIOSEO\TableOfContents\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Models;

/**
 * Handles the user Vue settings (toggled radios, table pagination, etc.).
 *
 * @since 1.0.0
 */
class VueSettings {
	/**
	 * Returns the settings.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function getOptions() {
		return new \WP_REST_Response( [
			'success'         => true,
			'options'         => aioseoTableOfContents()->options->all(),
			'internalOptions' => aioseoTableOfContents()->internalOptions->all(),
			'settings'        => aioseoTableOfContents()->vueSettings->all()
		], 200 );
	}

	/**
	 * Toggles a radio in the settings.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function toggleRadio( $request ) {
		$body   = $request->get_json_params();
		$radio  = ! empty( $body['radio'] ) ? sanitize_text_field( $body['radio'] ) : null;
		$value  = ! empty( $body['value'] ) ? sanitize_text_field( $body['value'] ) : null;

		$radios = aioseoTableOfContents()->vueSettings->toggledRadio;
		if ( ! array_key_exists( $radio, $radios ) ) {
			return new \WP_REST_Response( [
				'success' => false
			], 400 );
		}

		$radios[ $radio ] = $value;
		aioseoTableOfContents()->vueSettings->toggledRadio = $radios;

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Toggles a table's items per page setting.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function changeItemsPerPage( $request ) {
		$body   = $request->get_json_params();
		$table  = ! empty( $body['table'] ) ? sanitize_text_field( $body['table'] ) : null;
		$value  = ! empty( $body['value'] ) ? intval( $body['value'] ) : null;

		$tables = aioseoTableOfContents()->vueSettings->tablePagination;
		if ( ! array_key_exists( $table, $tables ) ) {
			return new \WP_REST_Response( [
				'success' => false
			], 400 );
		}

		$tables[ $table ] = $value;
		aioseoTableOfContents()->vueSettings->tablePagination = $tables;

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Save options from the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function saveChanges( $request ) {
		$body    = $request->get_json_params();
		$options = ! empty( $body['options'] ) ? $body['options'] : []; // The options class will sanitize them.

		aioseoTableOfContents()->options->sanitizeAndSave( $options );

		// Re-initialize the notices.
		aioseoTableOfContents()->notifications->init();

		return new \WP_REST_Response( [
			'success'       => true,
			'notifications' => Models\Notification::getNotifications()
		], 200 );
	}
}