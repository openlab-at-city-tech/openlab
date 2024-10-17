<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TRP_Google_Translate_V2_Machine_Translator extends TRP_Machine_Translator {
    /**
     * Send request to Google Translation API
     *
     * @param string $source_language       Translate from language
     * @param string $language_code         Translate to language
     * @param array $strings_array          Array of string to translate
     *
     * @return array|WP_Error               Response
     */
    public function send_request( $source_language, $language_code, $strings_array ){
        /* build our translation request */
        $translation_request = 'key=' . $this->get_api_key();
        $translation_request .= '&source='.$source_language;
        $translation_request .= '&target='.$language_code;
        foreach( $strings_array as $new_string ){
            $translation_request .= '&q='.rawurlencode(html_entity_decode( $new_string, ENT_QUOTES ));
        }
        $referer = $this->get_referer();

        /* Due to url length restrictions we need so send a POST request faked as a GET request and send the strings in the body of the request and not in the URL */
        $response = wp_remote_post( "https://translation.googleapis.com/language/translate/v2", array(
                'headers' => array(
                    'X-HTTP-Method-Override' => 'GET', //this fakes a GET request
                    'timeout'                => 45,
                    'Referer'                => $referer
                ),
                'body' => $translation_request,
            )
        );
        return $response;
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

        $source_language = $this->machine_translation_codes[$source_language_code];
        $target_language = $this->machine_translation_codes[$target_language_code];

        $translated_strings = array();

        /* split our strings that need translation in chunks of maximum 128 strings because Google Translate has a limit of 128 strings */
        $new_strings_chunks = array_chunk( $new_strings, 128, true );
        /* if there are more than 128 strings we make multiple requests */
        foreach( $new_strings_chunks as $new_strings_chunk ){
            $response = $this->send_request( $source_language, $target_language, $new_strings_chunk );

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

                $translation_response = json_decode( $response['body'] );
                if ( empty( $translation_response->error ) ) {
                    $this->machine_translator_logger->count_towards_quota( $new_strings_chunk );

                    /* if we have strings build the translation strings array and make sure we keep the original keys from $new_string */
                    $translations = ( empty( $translation_response->data->translations ) ) ? array() : $translation_response->data->translations;
                    $i            = 0;

                    foreach ( $new_strings_chunk as $key => $old_string ) {

                        if ( isset( $translations[ $i ] ) && !empty( $translations[ $i ]->translatedText ) ) {
                            $translated_strings[ $key ] = $translations[ $i ]->translatedText;
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
        
        return isset( $this->settings['trp_machine_translation_settings'], $this->settings['trp_machine_translation_settings']['google-translate-key'] ) ? $this->settings['trp_machine_translation_settings']['google-translate-key'] : false;

    }


    public function get_supported_languages(){

        if ( $this->get_api_key() ) {
            $response = wp_remote_post( "https://translation.googleapis.com/language/translate/v2/languages", array(
                    'headers' => array(
                        'timeout' => 45,
                        'Referer' => $this->get_referer()
                    ),
                    'body' => 'key=' . $this->get_api_key(),
                )
            );


            if ( is_array( $response ) && !is_wp_error( $response ) && isset( $response['response'] ) &&
                isset( $response['response']['code'] ) && $response['response']['code'] == 200 ) {
                $data                = json_decode( $response['body'] );
                $supported_languages = array();
                foreach ( $data->data->languages as $language ) {
                    $supported_languages[] = $language->language;
                }
                return apply_filters( 'trp_add_google_v2_supported_languages_to_the_array', $supported_languages );
            }
        }
        return array();
    }

    public function add_google_v2_supported_languages_that_are_not_returned_by_the_post_response($supported_language){
        $supported_language[] = 'fil';

        return $supported_language;
    }


    public function get_engine_specific_language_codes($languages){
        return $this->trp_languages->get_iso_codes($languages);
    }

    /*
     * Google does not support formality yet, but we need this for the machine translation tab to show the unsupported languages for formality
     */
    public function check_formality(){

        $formality_supported_languages = array();

        return $formality_supported_languages;
    }

    public function check_api_key_validity() {
        $machine_translator = $this;
        $translation_engine = $this->settings['trp_machine_translation_settings']['translation-engine'];
        $api_key            = $machine_translator->get_api_key();

        $is_error       = false;
        $return_message = '';

        if ( 'google_translate_v2' === $translation_engine && $this->settings['trp_machine_translation_settings']['machine-translation'] === 'yes') {

            if ( isset( $this->correct_api_key ) && $this->correct_api_key != null ) {
                return $this->correct_api_key;
            }

            if ( empty( $api_key ) ) {
                $is_error       = true;
                $return_message = __( 'Please enter your Google Translate key.', 'translatepress-multilingual' );
            } else {
                // Perform test.
                $response = $machine_translator->test_request();
                $code     = wp_remote_retrieve_response_code( $response );
                if ( 200 !== $code ) {
                    $is_error           = true;
                    $translate_response = trp_gt_response_codes( $code );
                    $return_message     = $translate_response['message'];
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
}
