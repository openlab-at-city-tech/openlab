<?php


class TRP_String_Translation_API_Regular {
    protected $type = 'regular';
    protected $helper;

    /* @var TRP_Query */

    public function __construct( $settings ) {
        $this->helper = new TRP_String_Translation_Helper();
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
			}
			$dictionary_by_original = trp_sort_dictionary_by_original( $dictionaries, $this->type, null, null );
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
}