<?php

class TRP_Editor_Api_Gettext_Strings {

	/* @var TRP_Query */
	protected $trp_query;
	/* @var TRP_SP_Slug_Manager*/
	protected $slug_manager;
	/* @var TRP_Translation_Render */
	protected $translation_render;
	/* @var TRP_Translation_Manager */
	protected $translation_manager;
    /* @var TRP_Settings */
    protected $settings;

	/**
	 * TRP_Translation_Manager constructor.
	 *
	 * @param array $settings       Settings option.
	 */
	public function __construct( $settings ){
		$this->settings = $settings;
	}

	/**
	 * Hooked to wp_ajax_trp_get_translations_gettext
	 */
	public function gettext_get_translations() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_get_translations_gettext' && ! empty( $_POST['string_ids'] ) && ! empty( $_POST['language'] ) && in_array( $_POST['language'], $this->settings['translation-languages'] ) ) {
				check_ajax_referer( 'gettext_get_translations', 'security' );
				if ( ! empty( $_POST['string_ids'] ) ) {
					$gettext_string_ids = json_decode( stripslashes( $_POST['string_ids'] ) ); /* phpcs:ignore */ /* sanitized when inserting in db */
				}
				else {
					$gettext_string_ids = array();
				}

				$current_language = sanitize_text_field( $_POST['language'] );
				$dictionaries     = array();

				if ( is_array( $gettext_string_ids ) ) {

					$trp = TRP_Translate_Press::get_trp_instance();
					if ( ! $this->trp_query ) {
						$this->trp_query = $trp->get_component( 'query' );
					}
					if ( ! $this->translation_manager ) {
						$this->translation_manager = $trp->get_component( 'translation_manager' );
					}


					$dictionaries[ $current_language ] = $this->trp_query->get_gettext_string_rows_by_ids( $gettext_string_ids, $current_language );

					/* build the original id array */
					$original_ids            = array();
					if ( ! empty( $dictionaries[ $current_language ] ) ) {
						foreach ( $dictionaries[ $current_language ] as $current_language_string ) {
							/* searching by original id */
							$original_ids[] = (int)$current_language_string['ot_id'];
						}
					}
					echo trp_safe_json_encode( array( // phpcs:ignore
						'originalIds' => $original_ids,
					) );

				}
			}
		}
		wp_die();
	}


	/*
	 * Save gettext translations
	 */
	public function gettext_save_translations(){
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
			if (isset($_POST['action']) && $_POST['action'] === 'trp_save_translations_gettext' && !empty($_POST['strings'])) {
				check_ajax_referer( 'gettext_save_translations', 'security' );
				$strings = json_decode(stripslashes($_POST['strings']));/* phpcs:ignore */ /* properly sanitized bellow */
				$update_strings = array();
				foreach ( $strings as $language => $language_strings ) {
					if ( in_array( $language, $this->settings['translation-languages'] ) ) {
						$update_strings[ $language ] = array();
						foreach( $language_strings as $string ) {
							if ( isset( $string->id ) && is_numeric( $string->id ) ) {
								array_push($update_strings[ $language ], array(
									'id' => (int)$string->id,
                                    'original' => trp_sanitize_string( $string->original, false ),
									'translated' => trp_sanitize_string( $string->translated ),
									'domain' => sanitize_text_field( $string->domain ),
									'status' => (int)$string->status,
									'plural_form' => (int)$string->plural_form,
									'context' => $string->context
								));
							}
						}
					}
				}

				if ( ! $this->trp_query ) {
					$trp = TRP_Translate_Press::get_trp_instance();
					$this->trp_query = $trp->get_component( 'query' );
				}

				foreach( $update_strings as $language => $update_string_array ) {
                    $gettext_insert_update = $this->trp_query->get_query_component('gettext_insert_update');
                    $gettext_insert_update->update_gettext_strings( $update_string_array, $language, array('id','translated', 'status') );
                    $this->trp_query->remove_possible_duplicates($update_string_array, $language, 'gettext');
				}

                do_action('trp_save_editor_translations_gettext_strings', $update_strings, $this->settings);
			}
		}
		echo trp_safe_json_encode( $update_strings );//phpcs:ignore
		wp_die();
	}
}
