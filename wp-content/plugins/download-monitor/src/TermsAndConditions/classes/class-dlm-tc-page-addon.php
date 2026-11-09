<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_TC_Page_Addon {

	private static $js_printed = false;

	public function setup() {
		// Hijack the page addon download button
		add_filter( 'dlm_page_addon_download_button', array( $this, 'page_addon_download_button' ), 10, 2 );
	}

	/**
	 * Hijack the [download] shortcode
	 *
	 * @param $content
	 * @param $download_id
	 *
	 * @return string
	 */
	public function page_addon_download_button( $content, $download_id ) {
		$hijacked_content = $this->get_locked_content( $download_id );

		if ( '' !== $hijacked_content ) {
			$content = $hijacked_content;
		}

		return $content;
	}

	private function get_locked_content( $download_id ) {
		$access_manager = new DLM_TC_Access_Manager();

		try {
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( $download_id );

			if ( false === $access_manager->check_access( true, $download, null ) ) {
				if ( get_option( 'dlm_no_access_modal', false ) && apply_filters( 'do_dlm_xhr_access_modal', true, $download ) ) {
					$modal = new DLM_TC_Modal();

					return $modal->modal_content( $download_id );
				}

				$shortcode = new DLM_TC_Shortcodes();

				return $shortcode->term_and_conditions_form( array( 'id' => $download_id ) );
			}
		} catch ( Exception $exception ) {
			// no download found
		}

		return '';
	}
}
