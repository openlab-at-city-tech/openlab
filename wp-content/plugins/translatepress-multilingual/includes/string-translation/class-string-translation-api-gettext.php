<?php

class TRP_String_Translation_API_Gettext {
    protected $type = 'gettext';
    protected $helper;

    /* @var TRP_Query */

    public function __construct( $settings ) {
        $this->helper = new TRP_String_Translation_Helper();
    }

	/**
	 * Returns only original string ids
	 *
	 * @return void
	 */
	public function get_strings(){
		$trp                = TRP_Translate_Press::get_trp_instance();
		$trp_query          = $trp->get_component( 'query' );

		$originals_results = $this->helper->get_originals_results(
			$this->type,
			$trp_query->get_table_name_for_gettext_original_strings(),
			$trp_query->get_table_name_for_gettext_original_meta(),
			'get_gettext_table_name',
			array( 'status' => 'status' )
		);


		echo trp_safe_json_encode( array( //phpcs:ignore
			'originalIds' => $originals_results['original_ids'],
			'totalItems' => $originals_results['total_item_count']
		) );
		wp_die();

	}

	/**
	 * Function that inserts in db translation from language files for specified original string ids for a specific language
	 * This request changes locale from the very beginning so all the active plugins/theme load their textdomain translations
	 *
	 * @return void
	 */
	public function get_missing_gettext_strings() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'string_translation_get_missing_strings_gettext', 'security' );

			$action = 'trp_string_translation_get_missing_gettext_strings';
			if ( isset( $_POST['action'] ) && $_POST['action'] === $action && isset( $_POST['original_ids'] ) && isset( $_POST['trp_ajax_language'] ) ) {
				$original_ids = json_decode( $_POST['original_ids'] ); /* phpcs:ignore */ /* sanitized downstream */
				foreach ( $original_ids as $key => $id ) {
					$original_ids[ $key ] = (int) $id;
				}

				$trp_ajax_language = sanitize_text_field( $_POST['trp_ajax_language'] );

				$trp          = TRP_Translate_Press::get_trp_instance();
				$trp_settings = $trp->get_component( 'settings' );
				$trp_query    = $trp->get_component( 'query' );
				$settings     = $trp_settings->get_settings();


				if ( in_array( $trp_ajax_language, $settings['translation-languages'] ) ) {
					$language = $trp_ajax_language;
				} else {
					wp_die();
				}

				$dictionary      = $trp_query->get_gettext_string_rows_by_original_id( $original_ids, $language );
				$gettext_manager = $trp->get_component( 'gettext_manager' );
				$gettext_manager->add_missing_language_file_translations($dictionary, $language);

			}
		}
		echo trp_safe_json_encode(array()); //phpcs:ignore
		wp_die();
	}

	/**
	 * Based on original ids, returns all the translations from db for all the languages
	 *
	 * @return void
	 */
	public function get_strings_by_original_ids(){
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'string_translation_get_strings_by_original_ids_gettext', 'security' );

			$action = 'trp_string_translation_get_strings_by_original_ids_gettext';
			if ( isset( $_POST['action'] ) && $_POST['action'] === $action && isset( $_POST['original_ids'] ) ) {
				$original_ids = json_decode( $_POST['original_ids'] ); /* phpcs:ignore */ /* sanitized downstream */
				foreach ( $original_ids as $key => $id ) {
					$original_ids[ $key ] = (int) $id;
				}

				$trp          = TRP_Translate_Press::get_trp_instance();
				$trp_query    = $trp->get_component( 'query' );
				$trp_settings = $trp->get_component( 'settings' );
				$settings     = $trp_settings->get_settings();


				// query each language table to retrieve translations
				$dictionaries = array();
				foreach ( $settings['translation-languages'] as $language ) {
					$dictionaries[ $language ] = $trp_query->get_gettext_string_rows_by_original_id( $original_ids, $language );
				}

				/* html entity decode the strings so we display them properly in the textareas  */
				foreach( $dictionaries as $lang => $dictionary ){
					foreach( $dictionary as $key => $string ){
						$string = array_map('html_entity_decode', $string );
						$dictionaries[$lang][$key] = (object)$string;
					}
				}

				$translation_manager = $trp->get_component('translation_manager');
				$localized_text = $translation_manager->string_groups();
				$post_language = ( isset( $_POST['language'] ) ) ? sanitize_text_field( $_POST['language'] ) : null;
				$dictionary_by_original = trp_sort_dictionary_by_original( $dictionaries, 'gettext', $localized_text['gettextstrings'], $post_language );

				echo trp_safe_json_encode( array('dictionary' => $dictionary_by_original ) ); //phpcs:ignore

			}
			wp_die();
		}

	}


    /** Using editor api function hooked for saving.
     * Implementing save_strings function is not necessary
     * Leave this function empty, removing it will cause a thrown notice
     */
    public function save_strings() {

    }
}