<?php

class TRP_Editor_Api_Regular_Strings {

	/* @var TRP_Query */
	protected $trp_query;
	/* @var TRP_Translation_Render */
	protected $translation_render;
	/* @var TRP_Translation_Manager */
	protected $translation_manager;
	/* @var TRP_Url_Converter */
	protected $url_converter;
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
	 * Returns translations based on original strings and ids.
	 *
	 * Hooked to wp_ajax_trp_get_translations_regular
	 *       and wp_ajax_nopriv_trp_get_translations_regular.
	 */
	public function get_translations() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'get_translations', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_get_translations_regular' && !empty( $_POST['language'] ) && in_array( $_POST['language'], $this->settings['translation-languages'] ) ) {
				$originals = (empty($_POST['originals']) )? array() : json_decode(stripslashes($_POST['originals'])); /* phpcs:ignore */ /* sanitized downstream */
				$skip_machine_translation = (empty($_POST['skip_machine_translation']) )? array() : json_decode(stripslashes($_POST['skip_machine_translation'])); /* phpcs:ignore */ /* sanitized downstream */
				$ids = (empty($_POST['string_ids']) )? array() : json_decode(stripslashes($_POST['string_ids'])); /* phpcs:ignore */ /* sanitized downstream */
				if ( is_array( $ids ) || is_array( $originals) ) {
					$trp = TRP_Translate_Press::get_trp_instance();
					if (!$this->trp_query) {
						$this->trp_query = $trp->get_component('query');
					}
					if (!$this->translation_manager) {
						$this->translation_manager = $trp->get_component('translation_manager');
					}
					$block_type = $this->trp_query->get_constant_block_type_regular_string();
					$dictionaries = $this->get_translation_for_strings( $ids, $originals, $block_type, $skip_machine_translation );

					$localized_text = $this->translation_manager->string_groups();
					$string_group = __('Others', 'translatepress-multilingual'); // this type is not registered in the string types because it will be overwritten by the content in data-trp-node-type
					if ( isset( $_POST['dynamic_strings'] ) && $_POST['dynamic_strings'] === 'true'  ){
						$string_group = $localized_text['dynamicstrings'];
					}
					$dictionary_by_original = trp_sort_dictionary_by_original( $dictionaries, 'regular', $string_group, sanitize_text_field( $_POST['language'] ) );

					echo trp_safe_json_encode( $dictionary_by_original );//phpcs:ignore
				}
			}
		}

		wp_die();
	}
	/**
	 * Return dictionary with translated strings.
	 *
	 * @param $strings
	 * @param null $block_type
	 *
	 * @return array
	 */
	protected function get_translation_for_strings( $ids, $originals, $block_type = null, $skip_machine_translation = array() ){
		$trp = TRP_Translate_Press::get_trp_instance();
		if ( ! $this->trp_query ) {
			$this->trp_query = $trp->get_component( 'query' );
		}
		if ( ! $this->translation_render ) {
			$this->translation_render = $trp->get_component('translation_render');
		}
		if ( ! $this->url_converter ) {
			$this->url_converter = $trp->get_component('url_converter');
		}

		$home_url = home_url();
		$id_array = array();
		$original_array = array();
		$dictionaries = array();
		foreach ( $ids as $id ) {
			if ( isset( $id ) && is_numeric( $id ) ) {
				$id_array[] = (int) $id;
			}
		}
		foreach( $originals as $original ){
			if ( isset( $original ) ) {
				$trimmed_string = trp_full_trim( trp_sanitize_string( $original, false ) );
				if ( ( filter_var($trimmed_string, FILTER_VALIDATE_URL) === false) ){
					// not url
					$original_array[] = $trimmed_string;
				}else{
					// is url
					if ( $this->translation_render->is_external_link( $trimmed_string, $home_url ) || $this->url_converter->url_is_file( $trimmed_string ) ) {
						// allow only external url or file urls
						$original_array[] = remove_query_arg( 'trp-edit-translation', $trimmed_string );
					}
				}
			}
		}

		$current_language = isset( $_POST['language'] ) && in_array( $_POST['language'], $this->settings['translation-languages'] ) ? $_POST['language'] : ''; /* phpcs:ignore */ /* sanitized by checking against existing languages */

		// necessary in order to obtain all the original strings
		if ( $this->settings['default-language'] != $current_language ) {
			if ( !empty ( $original_array ) && current_user_can ( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
				$this->translation_render->process_strings($original_array, $current_language, $block_type, $skip_machine_translation);
			}
			$dictionaries[$current_language] = $this->trp_query->get_string_rows( $id_array, $original_array, $current_language );
		}else{
			$dictionaries[$current_language] = array();
		}

		if ( isset( $_POST['all_languages'] ) && $_POST['all_languages'] === 'true' ) {
			foreach ($this->settings['translation-languages'] as $language) {
				if ($language == $this->settings['default-language']) {
					$dictionaries[$language]['default-language'] = true;
					continue;
				}

				if ($language == $current_language) {
					continue;
				}
				if (empty($original_strings)) {
					$original_strings = $this->extract_original_strings($dictionaries[$current_language], $original_array, $id_array);
				}
				if (current_user_can(apply_filters( 'trp_translating_capability', 'manage_options' ))) {
					$this->translation_render->process_strings($original_strings, $language, $block_type, $skip_machine_translation);
				}
				$dictionaries[$language] = $this->trp_query->get_string_rows(array(), $original_strings, $language);
			}
		}

		if ( count( $skip_machine_translation ) > 0 ) {
			foreach ( $dictionaries as $language => $dictionary ) {
				if ( $language === $this->settings['default-language'] ) {
					continue;
				}
				foreach ( $dictionary as $key => $string ) {
					if ( $string->status == 1 && in_array( $string->original, $skip_machine_translation ) ) {
						// do not return translation for href and src
						$dictionaries[ $language ][ $key ]->translated = '';
						$dictionaries[ $language ][ $key ]->status     = 0;
					}
				}
			}
		}

		return $dictionaries;
	}

	/**
	 * Return array of original strings given their db ids.
	 *
	 * @param array $strings            Strings object to extract original
	 * @param array $original_array     Original strings array to append to.
	 * @param array $id_array           Id array to extract.
	 * @return array                    Original strings array + Extracted strings from ids.
	 */
	protected function extract_original_strings( $strings, $original_array, $id_array ){
		if ( count( $strings ) > 0 ) {
			foreach ($id_array as $id) {
				if ( is_object( $strings[$id] ) ){
					$original_array[] = $strings[ $id ]->original;
				}
			}
		}
		return array_values( $original_array );
	}

	/**
	 * Save translations from ajax post.
	 *
	 * Hooked to wp_ajax_trp_save_translations_regular.
	 */
	public function save_translations(){
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
			check_ajax_referer( 'save_translations', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_save_translations_regular' && !empty( $_POST['strings'] ) ) {
				$strings = json_decode(stripslashes($_POST['strings'])); /* phpcs:ignore */ /* sanitized downstream */
				$update_strings = $this->save_translations_of_strings( $strings );
			}
		}
		echo trp_safe_json_encode( $update_strings ); // phpcs:ignore
		die();
	}

	/**
	 * Save translations in DB for the strings
	 *
	 * @param $strings
	 * @param null $block_type
	 */
	protected function save_translations_of_strings( $strings, $block_type = null ){
		if ( !$block_type ){
			if (!$this->trp_query) {
				$trp = TRP_Translate_Press::get_trp_instance();
				$this->trp_query = $trp->get_component('query');
			}
			$block_type = $this->trp_query->get_constant_block_type_regular_string();
		}
		$update_strings = array();
		foreach ( $strings as $language => $language_strings ) {
			if ( in_array( $language, $this->settings['translation-languages'] ) && $language != $this->settings['default-language'] ) {
				$update_strings[ $language ] = array();
				foreach( $language_strings as $string ) {
					if ( isset( $string->id ) && is_numeric( $string->id ) ) {
						if ( ! isset( $string->block_type ) ){
							$string->block_type = $block_type;
						}
						array_push($update_strings[ $language ], array(
							'id' => (int)$string->id,
							'original' => trp_sanitize_string( $string->original, false ),
							'translated' => trp_sanitize_string( $string->translated ),
							'status' => (int)$string->status,
							'block_type' => (int)$string->block_type
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
			$this->trp_query->update_strings( $update_string_array, $language, array('id','translated', 'status', 'block_type'));
			$this->trp_query->remove_possible_duplicates($update_string_array, $language, 'regular');
		}

        do_action('trp_save_editor_translations_regular_strings', $update_strings, $this->settings);

		return $update_strings;
	}

	/**
	 * Set translation block to active.
	 *
	 * Creates TB is not exists. Adds auto translation if one is not provided.
	 * Supports handling multiple translation blocks
	 */
	public function create_translation_block(){
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
			check_ajax_referer( 'merge_translation_block', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_create_translation_block' && !empty( $_POST['strings'] ) && !empty( $_POST['language'] ) && in_array( $_POST['language'], $this->settings['translation-languages'] ) && !empty( $_POST['original'] ) ) {
				$strings = json_decode( stripslashes( $_POST['strings'] ) ); /* phpcs:ignore */ /* sanitized downstream */

				if ( isset ( $this->settings['translation-languages']) ){
					$trp = TRP_Translate_Press::get_trp_instance();
					if ( ! $this->trp_query ) {
						$this->trp_query = $trp->get_component( 'query' );
					}
					if ( ! $this->translation_render ) {
						$this->translation_render = $trp->get_component( 'translation_render' );
					}

					$active_block_type = $this->trp_query->get_constant_block_type_active();
					foreach( $this->settings['translation-languages'] as $language ){
						if ( $language != $this->settings['default-language'] ){
							$dictionaries = $this->get_translation_for_strings( array(), array( stripslashes( $_POST['original'] ) ), $active_block_type, array() );/* phpcs:ignore */ /* sanitized downstream */
							break;
						}
					}

					/*
					 * Merging the dictionary received from get_translation_for_strings (which contains ID and possibly automatic translations) with
					 * ajax translated (which can contain manual translations)
					 */
					$originals_array_constructed = false;
					$originals = array();
					if ( isset( $dictionaries ) ){
						foreach ( $dictionaries as $language => $dictionary ){
							if ( $language == $this->settings['default-language'] )
								continue;

							foreach( $dictionary as $dictionary_string_key => $dictionary_string ){
								if ( !isset ($strings->$language) ){
									continue;
								}
								$ajax_translated_string_list = $strings->$language;

								foreach( $ajax_translated_string_list as $ajax_key => $ajax_string ) {
									if ( trp_full_trim( trp_sanitize_string( $ajax_string->original, false ) ) == $dictionary_string->original ) {
										if ( $ajax_string->translated != '' ) {
											$dictionaries[ $language ][ $dictionary_string_key ]->translated = trp_sanitize_string( $ajax_string->translated );
											$dictionaries[ $language ][ $dictionary_string_key ]->status     = (int) $ajax_string->status;
										}
										$dictionaries[ $language ][ $dictionary_string_key ]->block_type = (int) $ajax_string->block_type;
									}
									$dictionaries[ $language ][ $dictionary_string_key ]->new_translation_block = true;
								}

								if( !$originals_array_constructed ){
									$originals[] = $dictionary_string->original;
								}
							}

							$originals_array_constructed = true;
						}
						$this->save_translations_of_strings( $dictionaries, $active_block_type );

						// update deactivated languages
						$copy_of_originals = $originals;
						if ( $originals_array_constructed ){
							$table_names = $this->trp_query->get_all_table_names( $this->settings['default-language'], $this->settings['translation-languages'] );
							if ( count( $table_names ) > 0 ){
								foreach( $table_names as $table_name ) {
									$originals = $copy_of_originals;
									$language = $this->trp_query->get_language_code_from_table_name( $table_name );
									$existing_dictionary = $this->trp_query->get_string_rows( array(), $originals, $language, ARRAY_A );
									foreach ( $existing_dictionary as $string_key => $string ){
										foreach ( $originals as $original_key => $original ){
											if ( $string['original'] == $original ){
												unset( $originals[$original_key] );
											}
										}
										$existing_dictionary[$string_key]['block_type'] = $active_block_type;
										$originals = array_values( $originals );
									}
									$this->trp_query->insert_strings( $originals, $language, $active_block_type );
									$this->trp_query->update_strings( $existing_dictionary, $language );
								}

							}
						}

						echo trp_safe_json_encode( $dictionaries );//phpcs:ignore
					}
				}

			}
		}
		die();
	}

	/**
	 * Set translation block to deprecated
	 *
	 * Can handle splitting multiple blocks.
	 *
	 * @return mixed|string|void
	 */
	public function split_translation_block() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
            check_ajax_referer( 'split_translation_block', 'security' );

			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_split_translation_block' && ! empty( $_POST['strings'] ) ) {
                $raw_original_array = json_decode( stripslashes( $_POST['strings'] ) ); /* phpcs:ignore */ /* sanitized downstream */
				$trp = TRP_Translate_Press::get_trp_instance();
				if ( ! $this->trp_query ) {
					$this->trp_query = $trp->get_component( 'query' );
				}
				$deprecated_block_type = $this->trp_query->get_constant_block_type_deprecated();
				$originals = array();
				foreach( $raw_original_array as $original ){
					$originals[] = trp_sanitize_string( $original, false );
				}

				// even inactive languages ( not in $this->settings['translation-languages'] array ) will be updated
				$all_languages_table_names = $this->trp_query->get_all_table_names( $this->settings['default-language'], array() );
				$rows_affected = $this->trp_query->update_translation_blocks_by_original( $all_languages_table_names, $originals, $deprecated_block_type );
				if ( $rows_affected == 0 ){
					// do updates individually if it fails
					foreach ( $all_languages_table_names as $table_name ){
						$this->trp_query->update_translation_blocks_by_original( array( $table_name ), $originals, $deprecated_block_type );
					}
				}
			}
        }

        die();
	}
}
