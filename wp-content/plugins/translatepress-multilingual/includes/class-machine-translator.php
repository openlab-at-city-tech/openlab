<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Machine_Translator
 *
 * Facilitates Machine Translation calls.
 */
class TRP_Machine_Translator {
    protected $settings;
	protected $referer;
	protected $url_converter;
	protected $machine_translator_logger;
	protected $machine_translation_codes;
	protected $trp_languages;
    protected $correct_api_key = null;
    /**
     * TRP_Machine_Translator constructor.
     *
     * @param array $settings         Settings option.
     */
    public function __construct( $settings ){
        $this->settings = $settings;

        $trp = TRP_Translate_Press::get_trp_instance();

        if ( ! $this->machine_translator_logger ) {
            $this->machine_translator_logger = $trp->get_component('machine_translator_logger');
        }

        if ( ! $this->trp_languages ) {
            $this->trp_languages = $trp->get_component('languages');
        }

        $this->machine_translation_codes = $this->trp_languages->get_iso_codes($this->settings['translation-languages']);

        add_filter( 'trp_exclude_words_from_automatic_translation', array( $this, 'sort_exclude_words_from_automatic_translation_array' ), 99999, 1 );
        add_filter( 'trp_exclude_words_from_automatic_translation', array( $this, 'exclude_special_symbol_from_translation' ), 9999, 2 );
    }

    /**
     * Whether automatic translation is available.
     *
     * @param array $languages
     * @return bool
     */
    public function is_available( $languages = array() ) {
        /**
         * Return false in case it was directly called from parent and not from a derived class (DeepL / Google / TPAI)
         * Calling this method on the parent class means it was called too early and machine translation is not available at this point
         */
        if ( get_class( $this ) === __CLASS__ )
            return false;

        $settings = $this->settings['trp_machine_translation_settings'] ?? array();
        $enabled  = ( $settings['machine-translation'] ?? '' ) === 'yes';
        $is_available = false;

        if ( $enabled ) {
            $engine = $settings['translation-engine'] ?? null;

            if ( $engine === 'deepl' && get_option( 'trp_license_status' ) !== 'valid' ) {
                $is_available = false;
            } elseif ( empty( $languages ) ) {
                $is_available = true;
            } else {
                $is_available = $this->check_languages_availability( $languages );
            }
        }

        return apply_filters( 'trp_machine_translator_is_available', $is_available, $languages, $settings );
    }

    public function check_languages_availability( $languages, $force_recheck = false ){
        if ( !method_exists( $this, 'get_supported_languages' ) || !method_exists( $this, 'get_engine_specific_language_codes' )){
            return true;
        }
        $force_recheck = ( current_user_can('manage_options') &&
            !empty( $_GET['trp_recheck_supported_languages']) && $_GET['trp_recheck_supported_languages'] === '1' &&
            wp_verify_nonce( sanitize_text_field( $_GET['trp_recheck_supported_languages_nonce'] ), 'trp_recheck_supported_languages' ) ) ? true : $force_recheck; //phpcs:ignore
        $data = get_option('trp_db_stored_data', array() );
        if ( isset( $_GET['trp_recheck_supported_languages'] )) {
            unset($_GET['trp_recheck_supported_languages'] );
        }

        // if supported languages are not stored, fetch them and update option
        if ( empty( $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'] ) || $force_recheck || ( method_exists($this,'check_formality') && !isset($data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages']))){
            if ( empty( $data['trp_mt_supported_languages'] ) ) {
                $data['trp_mt_supported_languages'] = array();
            }
            if ( empty( $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ] ) ) {
                $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ] = array( 'languages' => array() );
            }

            $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ]['languages'] = $this->get_supported_languages();
            if (method_exists($this, 'check_formality')) {
                $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ]['formality-supported-languages'] = $this->check_formality();
            }
            $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'] = date("Y-m-d H:i:s" );
            update_option('trp_db_stored_data', $data );
        }

        $languages_iso_to_check = $this->get_engine_specific_language_codes( $languages );

        $all_are_available = !array_diff($languages_iso_to_check, $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['languages']);

        return apply_filters('trp_mt_available_supported_languages', $all_are_available, $languages, $this->settings );
    }

    public function get_last_checked_supported_languages(){
        $data = get_option('trp_db_stored_data', array() );
        if ( empty( $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'] ) ){
            $this->check_languages_availability( $this->settings['translation-languages'], true);
        }
        return $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'];
    }

    /**
     * Output an SVG based on translation engine and error flag.
     *
     * @param bool $show_errors true to show an error SVG, false if not.
     */
    public function automatic_translation_svg_output( $show_errors ) {
        if ( method_exists( $this, 'automatic_translate_error_check' ) ) {
            if ( $show_errors ) {
                trp_output_svg( 'error' );
            } else {
                trp_output_svg( 'check' );
            }
        }
        
    }

    /**
     *
     * @deprecated
     * Check the automatic translation API keys for Google Translate and DeepL
     *
     * @param TRP_Translate_Press $machine_translator Machine translator instance.
     * @param string $translation_engine              The translation engine (can be google_translate_v2 and deepl).
     * @param string $api_key                         The API key to check.
     *
     * @return array [ (string) $message, (bool) $error ].
     */
    public function automatic_translate_error_check( $machine_translator, $translation_engine, $api_key ) {

        $is_error       = false;
        $return_message = '';

        switch ( $translation_engine ) {
            case 'google_translate_v2':
                if ( empty( $api_key ) ) {
                    $is_error = true;
                    $return_message = __( 'Please enter your Google Translate key.', 'translatepress-multilingual' );
                } else {
                    // Perform test.
                    $response = $machine_translator->test_request();
                    $code     = wp_remote_retrieve_response_code( $response );
                    if ( 200 !== $code ) {
                        $is_error        = true;
                        $translate_response = trp_gt_response_codes( $code );
                        $return_message     = $translate_response['message'];
                    }
                }
                break;
            case 'deepl':
                if ( empty( $api_key ) ) {
                    $is_error = true;
                    $return_message = __( 'Please enter your DeepL API key.', 'translatepress-multilingual' );
                } else {
                    // Perform test.
                    $is_error= false;
                    $response = $machine_translator->test_request();
                    $code     = wp_remote_retrieve_response_code( $response );
                    if ( 200 !== $code && ( method_exists( 'TRP_DeepL', 'deepl_response_codes' ) || method_exists( 'TRP_IN_DeepL', 'deepl_response_codes' ) ) ) {

						// Test whether the old deepL add-on or the new repackaging model is used
						if ( method_exists( 'TRP_DeepL', 'deepl_response_codes' ) ) {
							$translate_response = TRP_DeepL::deepl_response_codes( $code );
						} else {
							$translate_response = TRP_IN_DeepL::deepl_response_codes( $code );
						}
	                    $is_error       = true;
                        $return_message = $translate_response['message'];
                    }
                }
                break;
            default:
                break;
        }


        $this->correct_api_key=array(
            'message' => $return_message,
            'error'   => $is_error,
        );

        return $this->correct_api_key;
    }

    // checking if the api_key is correct in order to display unsupported languages

    public function is_correct_api_key(){

        if(method_exists($this, 'check_api_key_validity')){
            $verification = $this->check_api_key_validity();
        }else {
            //we only need this values for automatic translate error check function for backwards compatibility

            $machine_translator = $this;
            $translation_engine = $this->settings['trp_machine_translation_settings']['translation-engine'];
            $api_key = $this->get_api_key();
            $verification = $this->automatic_translate_error_check( $machine_translator, $translation_engine, $api_key );
        }
        if($verification['error']== false) {
            return true;
        }
        return false;
    }


	/**
	 * Return site referer
	 *
	 * @return string
	 */
	public function get_referer(){
		if( ! $this->referer ) {
			if( ! $this->url_converter ) {
				$trp = TRP_Translate_Press::get_trp_instance();
				$this->url_converter = $trp->get_component( 'url_converter' );
			}

			$this->referer = $this->url_converter->get_abs_home();
		}

		return apply_filters( 'trp_machine_translator_referer', $this->referer );
	}

    /**
     * Verifies that the machine translation request is valid
     * @deprecated  since TP 1.6.0 (only here to support Deepl Add-on version 1.0.0)
     *
     * @param  string $to_language language we're looking to translate to
     * @return bool
     */
    public function verify_request( $to_language ){

        if( empty( $this->get_api_key() ) ||
            empty( $to_language ) || $to_language == $this->settings['default-language'] ||
            empty( $this->machine_translation_codes[$this->settings['default-language']] )
          )
            return false;

        // Method that can be extended in the child class to add extra validation
        if( !$this->extra_request_validations( $to_language ) )
            return false;

        // Check if crawlers are blocked
        if( !empty( $this->settings['trp_machine_translation_settings']['block-crawlers'] ) && $this->settings['trp_machine_translation_settings']['block-crawlers'] == 'yes' && $this->is_crawler() )
            return false;

        // Check if daily quota is met
        if( $this->machine_translator_logger->quota_exceeded() )
            return false;

        return true;

    }

    /**
     * Verifies that the machine translation request is valid
     *
     * @param  string $target_language_code language we're looking to translate to
     * @param  string $source_language_code language we're looking to translate from
     * @return bool
     */
    public function verify_request_parameters($target_language_code, $source_language_code){
        if( empty( $this->get_api_key() ) ||
            empty( $target_language_code ) || empty( $source_language_code ) ||
            empty( $this->machine_translation_codes[$target_language_code] ) ||
            empty( $this->machine_translation_codes[$source_language_code] ) ||
            $this->machine_translation_codes[$target_language_code] == $this->machine_translation_codes[$source_language_code]
        )
            return false;

        // Method that can be extended in the child class to add extra validation
        if( !$this->extra_request_validations( $target_language_code ) )
            return false;

        // Check if crawlers are blocked
        if( !empty( $this->settings['trp_machine_translation_settings']['block-crawlers'] ) && $this->settings['trp_machine_translation_settings']['block-crawlers'] == 'yes' && $this->is_crawler() )
            return false;

        // Check if daily quota is met
        if( $this->machine_translator_logger->quota_exceeded() )
            return false;

        return true;
    }

    /**
     * Verifies user agent to check if the request is being made by a crawler
     *
     * @return boolean
     */
    private function is_crawler(){
        if( !isset( $_SERVER['HTTP_USER_AGENT'] ) )
            return false;

        $crawlers = apply_filters( 'trp_machine_translator_crawlers', 'rambler|abacho|acoi|accona|aspseek|altavista|estyle|scrubby|lycos|geona|ia_archiver|alexa|sogou|skype|facebook|twitter|pinterest|linkedin|naver|bing|google|yahoo|duckduckgo|yandex|baidu|teoma|xing|java\/1.7.0_45|bot|crawl|slurp|spider|mediapartners|\sask\s|\saol\s' );

        return preg_match( '/'. $crawlers .'/i', sanitize_text_field ( $_SERVER['HTTP_USER_AGENT'] ) );
    }

    private function get_placeholders( $count ){
	    $placeholders = array();
	    for( $i = 1 ; $i <= $count; $i++ ){
            $placeholders[] = '1TP' . $i . 'T';
        }
	    return $placeholders;
    }

    /**
     * Check if a string should be sent for translation based on minimum length and content criteria
     *
     * @param string $string The string to check
     * @return bool True if the string should be translated, false otherwise
     */
    private function should_translate_string( $string ) {
        // Trim whitespace for accurate length check
        $trimmed = trim( $string );

        // Check if string is empty after trimming
        if ( empty( $trimmed ) ) {
            return false;
        }

        // Get minimum length (default: 2 characters)
        // Allow customization via filter
        $min_length = apply_filters( 'trp_minimum_translation_length', 2 );

        // Check minimum length
        if ( mb_strlen( $trimmed, 'UTF-8' ) < $min_length ) {
            return false;
        }

        // Check if string is only punctuation/special characters (optional, can be disabled via filter)
        $skip_punctuation_only = apply_filters( 'trp_skip_punctuation_only_strings', true );
        if ( $skip_punctuation_only && preg_match( '/^[[:punct:][:space:]]+$/u', $trimmed ) ) {
            return false;
        }

        return true;
    }

    /**
     * Function to be used externally
     *
     * @param $strings
     * @param $target_language_code
     * @param $source_language_code
     * @return array
     */
    public function translate($strings, $target_language_code, $source_language_code = null ){
        if ( !empty($strings) && is_array($strings) && method_exists( $this, 'translate_array' ) && apply_filters( 'trp_disable_automatic_translations_due_to_error', false ) === false ) {

            /* google has a problem translating this characters ( '%', '$', '#' )...for some reasons it puts spaces after them so we need to 'encode' them and decode them back. hopefully it won't break anything important */
            /* we put '%s' before '%' because google seems to transform %s into % in strings for some languages which causes a 500 Fatal Error in PHP 8*/
            $imploded_strings = implode(" ", $strings);
            $trp_exclude_words_from_automatic_translation = apply_filters('trp_exclude_words_from_automatic_translation', array('%s', '%d', '%', '$', '#'), $imploded_strings);
            $placeholders = $this->get_placeholders(count($trp_exclude_words_from_automatic_translation));
            $shortcode_tags_to_execute = apply_filters( 'trp_do_these_shortcodes_before_automatic_translation', array('trp_language', 'language-include', 'language-exclude') );

            $strings = array_unique($strings);
            $original_strings = $strings;

            // Filter out strings that are too short to translate
            $strings_to_skip = array();
            $strings_to_translate = array();
            foreach ($strings as $key => $string) {
                if ( !$this->should_translate_string($string) ) {
                    $strings_to_skip[$key] = $string;
                } else {
                    $strings_to_translate[$key] = $string;
                }
            }

            // If all strings are too short, return them as-is
            if ( empty($strings_to_translate) ) {
                return $original_strings;
            }

            // Continue with only the strings that meet the minimum length
            $strings = $strings_to_translate;

            foreach ($strings as $key => $string) {
                /* html_entity_decode is needed before replacing the character "#" from the list because characters like &#8220; (8220 utf8)
                 * will get an extra space after '&' which will break the character, rendering it like this: & #8220;
                 */

                $strings[$key] = str_replace($trp_exclude_words_from_automatic_translation, $placeholders, html_entity_decode( $string ));
                $strings[$key] = trp_do_these_shortcodes( $strings[$key], $shortcode_tags_to_execute );
            }

            if ( $this->settings['trp_machine_translation_settings']['translation-engine'] === 'deepl' ) {

                // if we don't have a valid license, return an empty array
                $license_status = get_option( 'trp_license_status' );
                if( $license_status !== 'valid' ){
                    return array();
                }
            }

            $machine_strings = $this->translate_array($strings, $target_language_code, $source_language_code);

            $machine_strings_return_array = array();
            if (!empty($machine_strings)) {
                foreach ($machine_strings as $key => $machine_string) {
                    // Restore placeholders to original excluded words
                    $processed_string = str_ireplace( $placeholders, $trp_exclude_words_from_automatic_translation, $machine_string );

                    // Restore quote patterns (use $strings which is decoded, not $original_strings with HTML entities)
                    $processed_string = $this->restore_translation_quotes($strings[$key], $processed_string);

                    // Restore punctuation and spacing patterns (use $strings which is decoded, not $original_strings with HTML entities)
                    $processed_string = $this->restore_punctuation_patterns($strings[$key], $processed_string);

                    $machine_strings_return_array[$original_strings[$key]] = $processed_string;
                }
            }

            // Add skipped strings back to the return array with their original values
            foreach ($strings_to_skip as $key => $skipped_string) {
                $machine_strings_return_array[$original_strings[$key]] = $original_strings[$key];
            }

            return $machine_strings_return_array;
        }else {
            return array();
        }
    }

    /**
     * @param $trp_exclude_words_from_automatic_translation
     * @return mixed
     *
     * We need to sort the $trp_exclude_words_from_automatic_translation array descending because we risk to not translate excluded multiple words when one
     * is repeated ( example: Facebook, Facebook Store, Facebook View, because Facebook was the first one in the array it was replaced with a code and the
     * other words group ( Store, View) were translated)
     */
    public function sort_exclude_words_from_automatic_translation_array($trp_exclude_words_from_automatic_translation){
        usort($trp_exclude_words_from_automatic_translation, array($this,"sort_array"));

        return $trp_exclude_words_from_automatic_translation;
    }

    public function sort_array($a, $b){
        return strlen($b)-strlen($a);
    }


    public function test_request(){}

    public function get_api_key(){
        return false;
    }

    public function extra_request_validations( $to_language ){
        return true;
    }

    public function exclude_special_symbol_from_translation($array, $strings){
        $float_array_symbols = array('d', 's', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H');
        foreach ($float_array_symbols as $float_array_symbol){
            for($i= 1; $i<=10; $i++) {
                $symbol = '%'.$i .'$'.$float_array_symbol;
                if ( strpos( $strings, $symbol ) !== false ) {
                    $array[] = '%' . $i . '$' . $float_array_symbol;
                }
            }
        }
        return $array;
    }

    /**
     * Restore and normalize quotes in translated strings to match the original
     *
     * @param string $original_string The original untranslated string
     * @param string $translated_string The translated string from the API
     * @return string The translated string with quotes restored and normalized
     */
    public function restore_translation_quotes($original_string, $translated_string) {
        // Allow disabling this functionality via filter
        if ( apply_filters( 'trp_disable_restore_translation_quotes', false ) ) {
            return $translated_string;
        }

        // Check if original string is empty or translated string is empty
        if ( empty($original_string) || empty($translated_string) ) {
            return $translated_string;
        }

        // Define all quote characters to check for
        $quote_chars = [
            // Straight quotes
            "'",   // U+0027 Apostrophe / single quote
            '"',   // U+0022 Double quote

            // Curly / typographic quotes
            '‘',   // U+2018 Left single curly quote
            '’',   // U+2019 Right single curly quote (also apostrophe)
            '“',   // U+201C Left double curly quote
            '”',   // U+201D Right double curly quote

            // Common international quotes
            '„',   // U+201E Low double quote (German, Polish)
            '‚',   // U+201A Low single quote
            '«',   // U+00AB Left double angle quote (French, Italian, etc.)
            '»',   // U+00BB Right double angle quote
        ];

        // Step 1: Restore boundary quotes if they were stripped
        $original_first_char = mb_substr($original_string, 0, 1, 'UTF-8');
        $original_last_char = mb_substr($original_string, -1, 1, 'UTF-8');
        $translated_first_char = mb_substr($translated_string, 0, 1, 'UTF-8');
        $translated_last_char = mb_substr($translated_string, -1, 1, 'UTF-8');

        // Check if original starts with a quote character and translated doesn't
        if ( in_array($original_first_char, $quote_chars, true) && !in_array($translated_first_char, $quote_chars, true) ) {
            $translated_string = $original_first_char . $translated_string;
        }

        // Check if original ends with a quote character and translated doesn't
        // Need to recalculate last char if we added a quote at the beginning
        if ( in_array($original_last_char, $quote_chars, true) && !in_array($translated_last_char, $quote_chars, true) ) {
            $translated_string = $translated_string . $original_last_char;
        }

        // Step 2: Normalize ALL quotes - collect all quotes from original (excluding apostrophes)
        $original_quotes = array();
        $original_len = mb_strlen($original_string, 'UTF-8');

        for ($i = 0; $i < $original_len; $i++) {
            $char = mb_substr($original_string, $i, 1, 'UTF-8');
            if ( in_array($char, $quote_chars, true) ) {
                // Check if this is an apostrophe (letter-apostrophe-letter pattern)
                $prev_char = ($i > 0) ? mb_substr($original_string, $i - 1, 1, 'UTF-8') : '';
                $next_char = ($i < $original_len - 1) ? mb_substr($original_string, $i + 1, 1, 'UTF-8') : '';

                $is_apostrophe = ($char === "'" || $char === '’') &&
                    preg_match('/^\p{L}$/u', $prev_char) &&
                    preg_match('/^\p{L}$/u', $next_char);

                if ( !$is_apostrophe ) {
                    $original_quotes[] = $char;
                }
            }
        }

        // If we found quotes in the original, replace them in order in the translation
        if ( !empty($original_quotes) ) {
            $quote_index = 0;
            $translated_len = mb_strlen($translated_string, 'UTF-8');
            $translated_chars = array();

            // Convert translation to array of characters
            for ($i = 0; $i < $translated_len; $i++) {
                $translated_chars[] = mb_substr($translated_string, $i, 1, 'UTF-8');
            }

            // Replace all quotes in translation in order (excluding apostrophes)
            for ($i = 0; $i < $translated_len; $i++) {
                $char = $translated_chars[$i];
                if ( in_array($char, $quote_chars, true) ) {
                    // Check if this is an apostrophe (letter-apostrophe-letter pattern)
                    $prev_char = ($i > 0) ? $translated_chars[$i - 1] : '';
                    $next_char = ($i < $translated_len - 1) ? $translated_chars[$i + 1] : '';

                    $is_apostrophe = ($char === "'" || $char === '’') &&
                        preg_match('/^\p{L}$/u', $prev_char) &&
                        preg_match('/^\p{L}$/u', $next_char);

                    if ( !$is_apostrophe && $quote_index < count($original_quotes) ) {
                        $translated_chars[$i] = $original_quotes[$quote_index];
                        $quote_index++;
                    }
                }
            }

            $translated_string = implode('', $translated_chars);
        }

        return $translated_string;
    }

    /**
     * Restore punctuation and spacing patterns at string boundaries
     *
     * Handles patterns at the beginning or end of strings:
     * - Two-character: ", " (comma+space), ". " (period+space), "; " (semicolon+space)
     * - Single character: "," (comma), "." (period), ";" (semicolon), " " (space)
     *
     * @param string $original_string The original untranslated string
     * @param string $translated_string The translated string from the API
     * @return string The translated string with punctuation patterns restored
     */
    public function restore_punctuation_patterns($original_string, $translated_string) {
        // Allow disabling this functionality via filter
        if ( apply_filters( 'trp_disable_restore_punctuation_patterns', false ) ) {
            return $translated_string;
        }

        // Check if original string is empty or translated string is empty
        if ( empty($original_string) || empty($translated_string) ) {
            return $translated_string;
        }

        // Define patterns to check (longer patterns first to avoid partial matches)
        // leading or trailing spaces are trimmed by trp_full_trim() inside translate_page but we still check here as "space+comma" at the end is valid for example
        $patterns = [', ', '. ', '; ', ' ,', ' .', ' ;', ',', '.', ';', ' '];

        // Check all patterns and restore at both leading and trailing positions
        foreach ($patterns as $pattern) {
            $pattern_len = mb_strlen($pattern, 'UTF-8');

            // Check and restore leading pattern
            $original_start = mb_substr($original_string, 0, $pattern_len, 'UTF-8');
            $translated_start = mb_substr($translated_string, 0, $pattern_len, 'UTF-8');

            if ($original_start === $pattern && $translated_start !== $pattern) {
                $translated_string = $pattern . $translated_string;
            }

            // Check and restore trailing pattern
            $original_end = mb_substr($original_string, -$pattern_len, $pattern_len, 'UTF-8');
            $translated_end = mb_substr($translated_string, -$pattern_len, $pattern_len, 'UTF-8');

            if ($original_end === $pattern && $translated_end !== $pattern) {
                $translated_string = $translated_string . $pattern;
            }
        }

        return $translated_string;
    }

}
