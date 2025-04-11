<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_String_Translation_API_Regular {
    protected $type = 'regular';
    protected $helper;
    protected $translation_render;

    /* @var TRP_Query */

    public function __construct( $settings ) {
        $this->helper = new TRP_String_Translation_Helper();
        $this->translation_render = new TRP_Translation_Render( $settings );
    }

	public function get_strings(){

		$trp                = TRP_Translate_Press::get_trp_instance();
		$trp_query          = $trp->get_component( 'query' );
		$trp_settings       = $trp->get_component( 'settings' );
		$settings           = $trp_settings->get_settings();

		$originals_results = $this->helper->get_originals_results(
			$this->type,
			$trp_query->get_table_name_for_original_strings(),
			$trp_query->get_table_name_for_original_meta(),
			'get_table_name',
			array( 'status' => 'status', 'block_type' => 'translation-block-type' )
		);

		if ( $originals_results['total_item_count'] > 0 ){
			// query each language table to retrieve translations
			$dictionaries = array();
			foreach ( $settings['translation-languages'] as $language ) {
				if ( $language === $settings['default-language'] ) {
					continue;
				}

				$dictionaries[ $language ] = $trp_query->get_string_rows( $originals_results['original_ids'], array(), $language, 'OBJECT_K', true );

                $missing_strings = array_diff_key( $originals_results['originals'], $dictionaries[ $language ] );

                $missing_strings_array = array_map( function( $object ){
                    return $object->original; // convert to array of originals
                }, $missing_strings );

                $current_dictionary_array = array_map( function( $object ){
                    return $object->original; // convert to array of originals
                }, $dictionaries[ $language ] );

                $full_dictionary_array = array_merge( $missing_strings_array, $current_dictionary_array );

                $this->translation_render->process_strings( $full_dictionary_array, $language );

                $dictionaries[ $language ] = $trp_query->get_string_rows( array(), $full_dictionary_array, $language );
			}

			$dictionary_by_original = trp_sort_dictionary_by_original( $dictionaries, $this->type, null, null );

            $query_args = $this->helper->get_sanitized_query_args( $this->type );

            // Used to display (found in translation) label next to the original string in case we found the search result in translations
            if ( !empty( $query_args['s'] ) ) {
                foreach ( $dictionary_by_original as &$dictionary ) {
                    foreach ( $dictionary['translationsArray'] as $translationArray ) {
                        if ( strpos( $translationArray->translated, $query_args['s'] ) !== false )
                            $dictionary['foundInTranslation'] = true;
                    }
                }
            }

		}else{
			$dictionary_by_original = array();
		}

		echo trp_safe_json_encode( array( // phpcs:ignore
			'dictionary' => $dictionary_by_original,
			'totalItems' => $originals_results['total_item_count']
		) );
		wp_die();

	}


    /** Using editor api function hooked for saving.
     * Implementing save_strings function is not necessary
     * Leave this function empty, removing it will cause a thrown notice
     */
    public function save_strings() {

    }

    public function delete_strings() {
        $this->helper->check_ajax( 'regular', 'delete' );
        $original_ids   = $this->helper->get_original_ids_from_post_request();
        $regular_delete = new TRP_Regular_Delete();
        $items_deleted  = $regular_delete->delete_strings( $original_ids );

        echo trp_safe_json_encode( $items_deleted );//phpcs:ignore
        wp_die();

    }

}