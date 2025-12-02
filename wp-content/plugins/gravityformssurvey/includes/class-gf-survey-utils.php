<?php

class GF_Survey_Utils {
	
	public static $query_var = 'gf_conversational';

	/**
	 * Check if the current form view is a conversational form.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form The form array
	 *
	 * @return bool True if the current form view is a conversational form.
	 */
	public static function is_conversational_form( $form ) {
		global $wp;

		$slug = self::get_requested_slug();

		if ( ! empty( $form['gf_theme_layers']['enable'] ) &&
		     ! empty( $form['gf_theme_layers']['form_full_screen_slug'] ) &&
		     $form['gf_theme_layers']['form_full_screen_slug'] === $slug ) {

			return true;

		}

		return false;

	}

	/**
	 * Check if the site is using plain permalinks.
	 *
	 * @since 1.4
	 *
	 * @return bool
	 */
	public static function is_plain_permalinks() {
		return get_option( 'permalink_structure' ) == '';
	}

	/**
	 * Get the slug of the requested conversational form.
	 *
	 * @param $vars
	 * @return mixed|void
	 * @since 1.4
	 */
	public static function get_requested_slug() {
		global $wp;

		if ( self::is_plain_permalinks() && isset( $wp->query_vars[ self::$query_var ] ) ) {
			return strtolower( $wp->query_vars[ self::$query_var ] );
		} else {
			return strtolower( $wp->request );
		}
	}

}