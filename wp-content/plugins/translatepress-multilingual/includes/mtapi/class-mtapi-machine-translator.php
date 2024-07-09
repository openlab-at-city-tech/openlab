<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TRP_MTAPI_Machine_Translator extends TRP_Machine_Translator {

    private $license_key = null;
    public function __construct( $settings ) {
        parent::__construct( $settings );
        add_filter( 'trp_mtapi_source_language', array( $this, 'configure_api_source_language' ), 10, 3 );
        add_filter( 'trp_mtapi_target_language', array( $this, 'configure_api_target_language' ), 10, 3 );
    }

    /**
	 * Send request to MTAPI
	 *
	 * @param string $source_language       Translate from language
	 * @param string $language_code         Translate to language
	 * @param array $strings_array          Array of string to translate
	 * @param string $formality             The formality of the language
	 *
	 * @return array|WP_Error               Response
	 */
	public function send_request( $source_language, $language_code, $strings_array, $formality = "default" ){
		/* build our translation request */
        $translation_request = [];
		$translation_request['key'] = get_option('trp_license_key', '');
		$translation_request['url'] = trailingslashit( $this->get_referer() );
		$translation_request['source'] = $source_language;
		$translation_request['target'] = $language_code;
		$translation_request['formality'] = $formality;
		$translation_request['texts'] = [];

		foreach ( $strings_array as $new_string ) {
            $translation_request['texts'][] = html_entity_decode( $new_string, ENT_QUOTES );
		}

		$response = wp_remote_post( "{$this->get_api_url()}/translations", array(
				'method'    => 'POST',
				'timeout'   => 45,
				'headers'   => [
                    'Content-Type' => 'application/json'
				],
				'body'      => wp_json_encode($translation_request),
			)
		);

		return $response;
	}

	public function get_api_url(){
		return (defined('MTAPI_URL')  ? MTAPI_URL : 'https://mtapi.translatepress.com' );
	}

	/**
	 * Returns an array with the API provided translations of the $new_strings array.
	 *
	 * @param array $new_strings                    array with the strings that need translation. The keys are the node number in the DOM so we need to preserve the m
	 * @param string $target_language_code          language code of the language that we will be translating to. Not equal to the google language code
	 * @param string $source_language_code          language code of the language that we will be translating from. Not equal to the google language code
	 * @return array                                array with the translation strings and the preserved keys or an empty array if something went wrong
	 */
	public function translate_array($new_strings, $target_language_code, $source_language_code = null ){
		if ( $source_language_code == null ){
			$source_language_code = $this->settings['default-language'];
		}
		if( empty( $new_strings ) || !$this->verify_request_parameters( $target_language_code, $source_language_code ) )
			return array();

        $source_language = apply_filters( 'trp_mtapi_source_language', $this->machine_translation_codes[$source_language_code], $source_language_code, $target_language_code );
        $target_language = apply_filters( 'trp_mtapi_target_language', $this->machine_translation_codes[$target_language_code], $source_language_code, $target_language_code );

        $formality = $this->get_request_formality_for_language($target_language_code);

		$translated_strings = array();

		/* split our strings that need translation in chunks of maximum 50 strings due to limit of TranslatePress AI*/
		$new_strings_chunks = array_chunk( $new_strings, 50, true );

		foreach( $new_strings_chunks as $new_strings_chunk ){
			$response = $this->send_request( $source_language, $target_language, $new_strings_chunk, $formality );

			// this is run only if "Log machine translation queries." is set to Yes.
			$this->machine_translator_logger->log(array(
				'strings'   => serialize( $new_strings_chunk),
				'response'  => serialize( $response ),
				'lang_source'  => $source_language,
				'lang_target'  => $target_language,
			));

			/* analyze the response */
			if ( is_array( $response ) && ! is_wp_error( $response ) && isset( $response['response'] ) &&
			     isset( $response['response']['code']) && $response['response']['code'] == 200 ) {

				$this->machine_translator_logger->count_towards_quota( $new_strings_chunk );

				$translation_response = json_decode( $response['body'] );
				if ( empty( $translation_response->error ) ) {

					/* if we have strings build the translation strings array and make sure we keep the original keys from $new_string */
					$translations = ( empty( $translation_response->translations ) ) ? array() : $translation_response->translations;
					$i            = 0;

					foreach ( $new_strings_chunk as $key => $old_string ) {

						if ( isset( $translations[ $i ] ) && !empty( $translations[ $i ]->translation ) ) {
							$translated_strings[ $key ] = $translations[ $i ]->translation;
						} else {
							/*  In some cases when API doesn't have a translation for a particular string,
							translation is returned empty instead of same string. Setting original string as translation
							prevents TP from keep trying to submit same string for translation endlessly.  */
							$translated_strings[ $key ] = $old_string;
						}

						$i++;

					}
				}

				if( $this->machine_translator_logger->quota_exceeded() )
					break;

			}
		}

		// will have the same indexes as $new_string or it will be an empty array if something went wrong
		return $translated_strings;
	}

	/**
	 * Send a test request to verify if the functionality is working
	 */
	public function test_request(){

		return $this->send_request( 'en', 'es', array( 'about' ) );

	}

	public function get_api_key(){
        if ( $this->license_key === null ){
            $this->license_key = get_option( 'trp_license_key' );
            $this->license_key = ( empty( $this->license_key ) ) ? false : $this->license_key;
        }
        return $this->license_key;

	}


	public function get_supported_languages(){
		$response = wp_remote_post( "{$this->get_api_url()}/languages", array(
                'method'    => 'GET',
                'timeout'   => 45,
                'headers'   => [
                    'Content-Type' => 'application/json'
                ],
			)
		);

		if ( is_array( $response ) && ! is_wp_error( $response ) && isset( $response['response'] ) &&
		     isset( $response['response']['code']) && $response['response']['code'] == 200 ) {
			$data = json_decode( $response['body'] );
			$supported_languages = array();
			foreach( $data as $language ){
				$supported_languages[] = $language->language;
			}
			return apply_filters( 'trp_add_translatepress_ai_supported_languages_to_the_array', $supported_languages );
		}

        return array();
	}



	public function get_engine_specific_language_codes($languages){
        $iso_translation_codes = $this->trp_languages->get_iso_codes($languages);
        $engine_specific_languages = array();
        foreach( $languages as $language ) {
            /* All combinations of source and target languages are supported.
            Target language code can be country specific. Source language code is not. So the source language code is used here.
            */
            $engine_specific_languages[] = apply_filters( 'trp_mtapi_source_language', $iso_translation_codes[ $language ], $language, null );
        }
        return $engine_specific_languages;
	}

    public function check_api_key_validity() {
		$machine_translator = $this;
		$translation_engine = $this->settings['trp_machine_translation_settings']['translation-engine'];

		$is_error       = false;
		$return_message = '';

		if ( 'mtapi' === $translation_engine && $this->settings['trp_machine_translation_settings']['machine-translation'] === 'yes') {

			if ( isset( $this->correct_api_key ) && $this->correct_api_key != null ) {
				return $this->correct_api_key;
			}

            $is_error       = true;
            $return_message = __( 'Please check your TranslatePress license key.', 'translatepress-multilingual' );
            $license        = $this->get_api_key();
            $status         = get_option( 'trp_license_status' );
            if ( $status === 'valid' ) {
                $mtapi_server = new TRP_MTAPI_Customer( $this->get_api_url() );
                $site_status  = $mtapi_server->lookup_site( $license, home_url() );
                if ( !empty( $site_status ) && !empty( $site_status['status'] ) && $site_status['status'] === "active" ) {
                    $is_error       = false;
                    $return_message = '';
                }

            }

			$this->correct_api_key = array(
				'message' => $return_message,
				'error'   => $is_error,
			);
		}

		return array(
			'message' => $return_message,
			'error'   => $is_error,
		);
	}

    /**
     * Particularities for source language in TranslatePress API
     *
     * PT_BR is not treated in the same way as for the target language
     *
     * @param $source_language
     * @param $source_language_code
     * @param $target_language_code
     * @return string
     */
    public function configure_api_source_language($source_language, $source_language_code, $target_language_code ){
        $exceptions_source_mapping_codes = array(
            'zh_HK' => 'zh',
            'zh_TW' => 'zh',
            'zh_CN' => 'zh',
            'de_DE_formal' => 'de',
            'nb_NO' => 'nb'
        );
        if ( isset( $exceptions_source_mapping_codes[$source_language_code] ) ){
            $source_language = $exceptions_source_mapping_codes[$source_language_code];
        }

        return $source_language;
    }

    /**
     * Particularities for target language in TranslatePress API
     *
     * @param $target_language
     * @param $source_language_code
     * @param $target_language_code
     * @return string
     */
    public function configure_api_target_language($target_language, $source_language_code, $target_language_code ){
        $exceptions_target_mapping_codes = array(
            'zh_HK' => 'zh',
            'zh_TW' => 'zh',
            'zh_CN' => 'zh',
            'pt_BR' => 'pt-br',
            'pt_PT' => 'pt-pt',
            'pt_AO' => 'pt-pt',
            'pt_PT_ao90' => 'pt-pt',
            'de_DE_formal' => 'de',
            'en_GB' => 'en-gb',
            'en_US' => 'en-us',
            'en_CA' => 'en-us',
            'en_ZA' => 'en-gb',
            'en_NZ' => 'en-gb',
            'en_AU' => 'en-gb',
            'nb_NO' => 'nb'
        );
        if ( isset( $exceptions_target_mapping_codes[$target_language_code] ) ){
            $target_language = $exceptions_target_mapping_codes[$target_language_code];
        }

        return $target_language;
    }


    public function get_formality_setting_for_language($target_language_code){

        $formality = "default";

        if(isset($this->settings["translation-languages-formality-parameter"][ $target_language_code ])) {
            if ( $this->settings["translation-languages-formality-parameter"][ $target_language_code ] == 'informal'){
                $formality = "less";
            }else{
                if($this->settings["translation-languages-formality-parameter"][ $target_language_code ] == 'formal'){
                    $formality = "more";
                }
            }
        }

        return $formality;
    }

    public function get_languages_that_support_formality(){

        $formality_supported_languages = array();

        $data = get_option('trp_db_stored_data', array() );

        if (isset($data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'])){
            foreach ($this->settings['translation-languages'] as $language){
                if(array_key_exists($language, $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'])){
                    $formality_supported_languages[$language] = $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'][$language];
                }else{
                    $this->check_languages_availability($this->settings['translation-languages'], true);
                    $data = get_option('trp_db_stored_data', array());
                    $formality_supported_languages = $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'];
                    break;
                }
            }

        }
        return $formality_supported_languages;
    }

    public function get_request_formality_for_language($target_language_code){

        $formality = $this->get_formality_setting_for_language($target_language_code);
        $formality_supported_languages = $this->get_languages_that_support_formality();

        if(isset($formality_supported_languages[$target_language_code]) && $formality_supported_languages[$target_language_code] == "true"){
            $formality = ( $formality == "less" ) ? "informal" : $formality;
            $formality = ( $formality == "more" ) ? "formal" : $formality;
            return $formality;
        }else{
            return 'default';
        }
    }

    public function check_formality() {
        $formality_supported_languages = [];
        $language_iso_codes            = [];

        $response = wp_remote_post( "{$this->get_api_url()}/languages", array(
                'method'  => 'GET',
                'timeout' => 45,
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            )
        );

        if ( is_array( $response ) && !is_wp_error( $response ) && isset( $response['response'] ) && isset( $response['response']['code'] ) && $response['response']['code'] == 200 ) {
            $response_data = json_decode( $response['body'] );
            $all_languages = $this->trp_languages->get_wp_languages();
            foreach ( $all_languages as $language ) {
                $language_iso_codes[ $language['language'] ] = $this->configure_api_target_language( reset( $language['iso'] ), '', $language['language'] );
            }

            foreach ( $response_data as $supported_language ) {
                $matched_languages = array_keys( $language_iso_codes, strtolower( $supported_language->language ) );
                if ( $matched_languages ) {
                    foreach ( $matched_languages as $matched_language ) {
                        $formality_supported_languages[ $matched_language ] = $supported_language->formality ? 'true' : 'false';
                    }
                }
            }
        }

        return apply_filters( 'trp_mtapi_formality_languages', $formality_supported_languages );
    }
}
