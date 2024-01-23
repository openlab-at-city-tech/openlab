<?php

/**
 * Class TRP_Languages
 *
 * Provides available languages, with name and code.
 */
class TRP_Languages{

  	protected $languages = array();
	protected $wp_languages;
	protected $wp_languages_backup = array();
	protected $settings;
	protected $is_admin_request;


    /**
     * Returns array of all possible languages.
     *
     * @param string $english_or_native_name    'english_name' | 'native_name'
     * @return array                            Returns associative array with language code as key and language name as value.
     */
    public function get_languages( $english_or_native_name = 'english_name' ){
		if ( empty( $this->languages[$english_or_native_name] ) ) {
			$wp_languages = $this->get_wp_languages();
			foreach ( $wp_languages as $wp_language ) {
				$this->languages[$english_or_native_name][$wp_language['language']] = $wp_language[$english_or_native_name];
			}
		}

        return apply_filters( 'trp_languages', $this->languages[$english_or_native_name], $english_or_native_name );
    }

    /** Set proper locale when changing languages with translatepress
     *
     * @param $locale
     * @return mixed
     */
    public function change_locale( $locale ){

        if ( $this->is_string_translation_request_for_different_language() ){
            $trp_ajax_language = (isset($_POST['trp_ajax_language']) ) ? sanitize_text_field( $_POST['trp_ajax_language'] ) : '';
            if ( !$this->settings ){
                $trp = TRP_Translate_Press::get_trp_instance();
                $trp_settings = $trp->get_component( 'settings' );
                $this->settings = $trp_settings->get_settings();
            }
            if ( $trp_ajax_language && in_array( $trp_ajax_language, $this->settings['translation-languages'] ) ){
                return $trp_ajax_language;
            }
        }

        if ( $this->is_admin_request === null ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $trp_is_admin_request = $trp->get_component( 'url_converter' );
            $this->is_admin_request= $trp_is_admin_request->is_admin_request();
        }

        if ( $this->is_admin_request ){
            return $locale;
        }

        global $TRP_LANGUAGE;
        if( !empty($TRP_LANGUAGE) ){
            $locale = $TRP_LANGUAGE;
        }
        return $locale;
    }

	public function is_string_translation_request_for_different_language(){
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$action = 'trp_string_translation_get_missing_gettext_strings';
			if ( isset( $_POST['action'] ) && $_POST['action'] === $action ) {
				return true;
			}
		}
		return false;
	}

    /**
     * Returns all languages information provided by WP.
     *
     * @uses wp_get_available_translations()
     *
     * @return array            WP languages information.
     */
	public function get_wp_languages(){
		if ( empty( $this->wp_languages ) ){
			require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
			$this->wp_languages = wp_get_available_translations();
			if ( count( $this->wp_languages ) == 0 ) {
				$this->wp_languages = $this->get_wp_languages_backup();
			}
		}
		$default = array( 'en_US' => array( 'language'	=> 'en_US', 'english_name'=> 'English (United States)', 'native_name' => 'English', 'iso' => array( 'en' ) ) );
		return apply_filters( 'trp_wp_languages', $default + $this->wp_languages );
	}

    /**
     * Returns iso language codes for provided array.
     *
     * Iso codes are short language codes with no localization. Not to be confused with full language codes.
     * Iso codes are not unique.
     *
     * @param array $language_codes         String array of language codes for which to return iso codes.
     * @param bool $map_google_codes        Whether to return Google API compatible codes.
     * @return array                        String array of iso codes.
     */
	public function get_iso_codes( $language_codes, $map_google_codes = true ){
        if ( !in_array( 'en_US', $language_codes ) ){
            $language_codes[] = 'en_US';
        }
		$iso_codes = array();
		$wp_languages = $this->get_wp_languages();
		$map_wp_codes_to_google = apply_filters( 'trp_map_wp_codes_to_google', array(
			'zh_HK' => 'zh-TW',
			'zh_TW'	=> 'zh-TW',
			'zh_CN'	=> 'zh-CN',
			'nb_NO'	=> 'no'
		) );
		foreach ( $language_codes as $language_code ) {
			if ( $map_google_codes && isset( $map_wp_codes_to_google[$language_code] ) ){
				$iso_codes[$language_code] = $map_wp_codes_to_google[$language_code];
			}else {
				foreach ($wp_languages as $wp_language) {
					if ($wp_language['language'] == $language_code) {
						$iso_codes[$language_code] = reset($wp_language['iso']);
						break;
					}
				}
			}
		}
		return $iso_codes;
	}

	/**
	 * Return an array of all language codes.
	 *
	 * @return array		Array of language codes.
	 */
	public function get_all_language_codes(){
		return array_keys ( $this->get_languages() );
	}

    /**
     * Returns array of full language names for the provided array of language codes.
     *
     * English_or_native_name parameter set to null means to obey the admin settings.
     *
     * @param array $language_codes                     String array of language codes.
     * @param string $english_or_native_name            'english_name' | 'native_name' | null
     * @return array                                    Associative array with language code as key and full language name as value
     */
	public function get_language_names( $language_codes, $english_or_native_name = null ){
		if ( !$english_or_native_name ){
			if ( !$this->settings ){
				$trp = TRP_Translate_Press::get_trp_instance();
				$trp_settings = $trp->get_component( 'settings' );
				$this->settings = $trp_settings->get_settings();
			}
			$english_or_native_name = $this->settings['native_or_english_name'];
		}
		$return = array();
        $languages = $this->get_languages( $english_or_native_name );
		foreach ( $language_codes as $language_code ){
			if( isset( $languages[$language_code] ) ) {
				$return[$language_code] = apply_filters( 'trp_language_name', $languages[$language_code], $language_code, $english_or_native_name, $language_codes );
			}
		}

		return $return;
	}

	/**
	 * Returns substring of the string from the beginning until the occurrence of character
	 *
	 * If character not found do nothing.
	 *
	 * @param $string string    String to trim
	 * @param $character string Delimitator string
	 *
	 * @return string
	 */
	public function string_trim_after_character( $string, $character ){
		if ( strpos( $string, $character ) !== false ) {
			$string = substr($string, 0, strpos($string, $character ));
		}
		return $string;
	}

	/**
	 * Return true if the language (without country) of the language_code is present multiple times in the array
	 *
	 * (ex. For language code en_UK, language_code_array [en_US, en_UK], return true)
	 *
	 * @param $language_code string         Language code (ex. en_US)
	 * @param $language_code_array array    Array of language codes
	 *
	 * @return bool
	 */
	public function duplicated_language( $language_code, $language_code_array ){
		// strip country from code ( ex. en_US => en )
		$stripped_language_code = $this->string_trim_after_character( $language_code, "_" );
		foreach ( $language_code_array as $key => $value ){
			$stripped_value = $this->string_trim_after_character( $value, "_" );
			if ( $language_code != $value && $stripped_language_code == $stripped_value ){
				return true;
			}
		}
		return false;

	}

    /**
     * Return the short language name for English name.
     *
     * @param string $name                  Original language name.
     * @param string $code                  Language code.
     * @param string $english_or_native     'english_name' | 'native_name'
     * @return string                       Short language name.
     */
	public function beautify_language_name( $name, $code, $english_or_native, $language_codes ){
	    $wp_lang = $this->get_wp_languages();
		if ( $english_or_native == 'english_name' ) {
			if ( ! $this->duplicated_language( $code, $language_codes) && (!isset($wp_lang[$code]['is_custom_language']) || (isset($wp_lang[$code]['is_custom_language']) && $wp_lang[$code]['is_custom_language'] !== true))){
				$name = $this->string_trim_after_character( $name, " (" );
			}
		}
		return apply_filters( 'trp_beautify_language_name', $name, $code, $english_or_native, $language_codes );
	}

	/**
	 * Return language arrays with English languages first
	 *
	 * @param $languages_array array            Languages array
	 * @param $english_or_native_name string    'english_name' | 'native_name'
	 *
	 * @return array
	 */
	public function reorder_languages( $languages_array, $english_or_native_name ){
		$english_array = array();
		foreach( $languages_array as $key => $value ){
			if ( $this->string_trim_after_character( $key, '_' ) == 'en' ){
				$english_array[$key] = $value;
				unset( $languages_array[$key] );
			}
		}

		return $english_array + $languages_array;
	}

    /**
     * Return back-up array with full language name information.
     *
     * Used in case the connection with WP fails via wp_get_available_translations() call.
     *
     * @return array        Array with full language information.
     */
	public function get_wp_languages_backup(){
		$string = '{"translations":[{"language":"af","version":"4.8","updated":"2017-06-23 21:35:47","english_name":"Afrikaans","native_name":"Afrikaans","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/af.zip","iso":{"1":"af","2":"afr"},"strings":{"continue":"Gaan voort"}},{"language":"ar","version":"4.8","updated":"2017-07-09 03:55:46","english_name":"Arabic","native_name":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ar.zip","iso":{"1":"ar","2":"ara"},"strings":{"continue":"\u0627\u0644\u0645\u062a\u0627\u0628\u0639\u0629"}},{"language":"ary","version":"4.7.5","updated":"2017-01-26 15:42:35","english_name":"Moroccan Arabic","native_name":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629 \u0627\u0644\u0645\u063a\u0631\u0628\u064a\u0629","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/ary.zip","iso":{"1":"ar","3":"ary"},"strings":{"continue":"\u0627\u0644\u0645\u062a\u0627\u0628\u0639\u0629"}},{"language":"as","version":"4.7.2","updated":"2016-11-22 18:59:07","english_name":"Assamese","native_name":"\u0985\u09b8\u09ae\u09c0\u09af\u09bc\u09be","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/as.zip","iso":{"1":"as","2":"asm","3":"asm"},"strings":{"continue":""}},{"language":"azb","version":"4.7.2","updated":"2016-09-12 20:34:31","english_name":"South Azerbaijani","native_name":"\u06af\u0624\u0646\u0626\u06cc \u0622\u0630\u0631\u0628\u0627\u06cc\u062c\u0627\u0646","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/azb.zip","iso":{"1":"az","3":"azb"},"strings":{"continue":"Continue"}},{"language":"az","version":"4.7.2","updated":"2016-11-06 00:09:27","english_name":"Azerbaijani","native_name":"Az\u0259rbaycan dili","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/az.zip","iso":{"1":"az","2":"aze"},"strings":{"continue":"Davam"}},{"language":"bel","version":"4.8","updated":"2017-06-17 20:31:44","english_name":"Belarusian","native_name":"\u0411\u0435\u043b\u0430\u0440\u0443\u0441\u043a\u0430\u044f \u043c\u043e\u0432\u0430","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/bel.zip","iso":{"1":"be","2":"bel"},"strings":{"continue":"\u041f\u0440\u0430\u0446\u044f\u0433\u043d\u0443\u0446\u044c"}},{"language":"bg_BG","version":"4.8","updated":"2017-06-18 19:16:01","english_name":"Bulgarian","native_name":"\u0411\u044a\u043b\u0433\u0430\u0440\u0441\u043a\u0438","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/bg_BG.zip","iso":{"1":"bg","2":"bul"},"strings":{"continue":"\u041d\u0430\u043f\u0440\u0435\u0434"}},{"language":"bn_BD","version":"4.7.2","updated":"2017-01-04 16:58:43","english_name":"Bengali","native_name":"\u09ac\u09be\u0982\u09b2\u09be","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/bn_BD.zip","iso":{"1":"bn"},"strings":{"continue":"\u098f\u0997\u09bf\u09df\u09c7 \u099a\u09b2."}},{"language":"bo","version":"4.7.2","updated":"2016-09-05 09:44:12","english_name":"Tibetan","native_name":"\u0f56\u0f7c\u0f51\u0f0b\u0f61\u0f72\u0f42","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/bo.zip","iso":{"1":"bo","2":"tib"},"strings":{"continue":"\u0f58\u0f74\u0f0b\u0f58\u0f50\u0f74\u0f51\u0f0d"}},{"language":"bs_BA","version":"4.7.2","updated":"2016-09-04 20:20:28","english_name":"Bosnian","native_name":"Bosanski","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/bs_BA.zip","iso":{"1":"bs","2":"bos"},"strings":{"continue":"Nastavi"}},{"language":"ca","version":"4.8","updated":"2017-06-16 11:47:56","english_name":"Catalan","native_name":"Catal\u00e0","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ca.zip","iso":{"1":"ca","2":"cat"},"strings":{"continue":"Continua"}},{"language":"ceb","version":"4.7.2","updated":"2016-03-02 17:25:51","english_name":"Cebuano","native_name":"Cebuano","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/ceb.zip","iso":{"2":"ceb","3":"ceb"},"strings":{"continue":"Padayun"}},{"language":"cs_CZ","version":"4.7.2","updated":"2017-01-12 08:46:26","english_name":"Czech","native_name":"\u010ce\u0161tina\u200e","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/cs_CZ.zip","iso":{"1":"cs","2":"ces"},"strings":{"continue":"Pokra\u010dovat"}},{"language":"cy","version":"4.8","updated":"2017-06-14 13:21:24","english_name":"Welsh","native_name":"Cymraeg","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/cy.zip","iso":{"1":"cy","2":"cym"},"strings":{"continue":"Parhau"}},{"language":"da_DK","version":"4.8","updated":"2017-06-14 23:24:44","english_name":"Danish","native_name":"Dansk","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/da_DK.zip","iso":{"1":"da","2":"dan"},"strings":{"continue":"Forts\u00e6t"}},{"language":"de_CH","version":"4.8","updated":"2017-06-15 21:25:12","english_name":"German (Switzerland)","native_name":"Deutsch (Schweiz)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/de_CH.zip","iso":{"1":"de"},"strings":{"continue":"Weiter"}},{"language":"de_DE_formal","version":"4.8","updated":"2017-07-04 12:57:09","english_name":"German (Formal)","native_name":"Deutsch (Sie)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/de_DE_formal.zip","iso":{"1":"de"},"strings":{"continue":"Weiter"}},{"language":"de_CH_informal","version":"4.8","updated":"2017-06-15 08:50:23","english_name":"German (Switzerland, Informal)","native_name":"Deutsch (Schweiz, Du)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/de_CH_informal.zip","iso":{"1":"de"},"strings":{"continue":"Weiter"}},{"language":"de_DE","version":"4.8","updated":"2017-07-08 16:08:42","english_name":"German","native_name":"Deutsch","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/de_DE.zip","iso":{"1":"de"},"strings":{"continue":"Weiter"}},{"language":"dzo","version":"4.7.2","updated":"2016-06-29 08:59:03","english_name":"Dzongkha","native_name":"\u0f62\u0fab\u0f7c\u0f44\u0f0b\u0f41","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/dzo.zip","iso":{"1":"dz","2":"dzo"},"strings":{"continue":""}},{"language":"el","version":"4.8","updated":"2017-06-21 18:05:57","english_name":"Greek","native_name":"\u0395\u03bb\u03bb\u03b7\u03bd\u03b9\u03ba\u03ac","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/el.zip","iso":{"1":"el","2":"ell"},"strings":{"continue":"\u03a3\u03c5\u03bd\u03ad\u03c7\u03b5\u03b9\u03b1"}},{"language":"en_NZ","version":"4.8","updated":"2017-06-17 08:09:19","english_name":"English (New Zealand)","native_name":"English (New Zealand)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/en_NZ.zip","iso":{"1":"en","2":"eng","3":"eng"},"strings":{"continue":"Continue"}},{"language":"en_ZA","version":"4.7.5","updated":"2017-01-26 15:53:43","english_name":"English (South Africa)","native_name":"English (South Africa)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/en_ZA.zip","iso":{"1":"en","2":"eng","3":"eng"},"strings":{"continue":"Continue"}},{"language":"en_GB","version":"4.8","updated":"2017-06-15 07:18:00","english_name":"English (UK)","native_name":"English (UK)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/en_GB.zip","iso":{"1":"en","2":"eng","3":"eng"},"strings":{"continue":"Continue"}},{"language":"en_AU","version":"4.8","updated":"2017-06-15 05:14:35","english_name":"English (Australia)","native_name":"English (Australia)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/en_AU.zip","iso":{"1":"en","2":"eng","3":"eng"},"strings":{"continue":"Continue"}},{"language":"en_CA","version":"4.8","updated":"2017-06-23 16:48:27","english_name":"English (Canada)","native_name":"English (Canada)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/en_CA.zip","iso":{"1":"en","2":"eng","3":"eng"},"strings":{"continue":"Continue"}},{"language":"eo","version":"4.8","updated":"2017-06-27 10:36:23","english_name":"Esperanto","native_name":"Esperanto","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/eo.zip","iso":{"1":"eo","2":"epo"},"strings":{"continue":"Da\u016drigi"}},{"language":"es_AR","version":"4.8","updated":"2017-06-20 00:55:30","english_name":"Spanish (Argentina)","native_name":"Espa\u00f1ol de Argentina","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/es_AR.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"es_MX","version":"4.8","updated":"2017-06-16 17:22:41","english_name":"Spanish (Mexico)","native_name":"Espa\u00f1ol de M\u00e9xico","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/es_MX.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"es_ES","version":"4.8","updated":"2017-07-02 08:44:01","english_name":"Spanish (Spain)","native_name":"Espa\u00f1ol","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/es_ES.zip","iso":{"1":"es"},"strings":{"continue":"Continuar"}},{"language":"es_CO","version":"4.7.5","updated":"2017-01-26 15:54:37","english_name":"Spanish (Colombia)","native_name":"Espa\u00f1ol de Colombia","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/es_CO.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"es_GT","version":"4.7.5","updated":"2017-01-26 15:54:37","english_name":"Spanish (Guatemala)","native_name":"Espa\u00f1ol de Guatemala","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/es_GT.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"es_CL","version":"4.7.2","updated":"2016-11-28 20:09:49","english_name":"Spanish (Chile)","native_name":"Espa\u00f1ol de Chile","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/es_CL.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"es_PE","version":"4.7.2","updated":"2016-09-09 09:36:22","english_name":"Spanish (Peru)","native_name":"Espa\u00f1ol de Per\u00fa","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/es_PE.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"es_VE","version":"4.8","updated":"2017-07-07 00:53:01","english_name":"Spanish (Venezuela)","native_name":"Espa\u00f1ol de Venezuela","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/es_VE.zip","iso":{"1":"es","2":"spa"},"strings":{"continue":"Continuar"}},{"language":"et","version":"4.7.2","updated":"2017-01-27 16:37:11","english_name":"Estonian","native_name":"Eesti","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/et.zip","iso":{"1":"et","2":"est"},"strings":{"continue":"J\u00e4tka"}},{"language":"eu","version":"4.8","updated":"2017-06-21 08:00:44","english_name":"Basque","native_name":"Euskara","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/eu.zip","iso":{"1":"eu","2":"eus"},"strings":{"continue":"Jarraitu"}},{"language":"fa_IR","version":"4.8","updated":"2017-06-09 15:50:45","english_name":"Persian","native_name":"\u0641\u0627\u0631\u0633\u06cc","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/fa_IR.zip","iso":{"1":"fa","2":"fas"},"strings":{"continue":"\u0627\u062f\u0627\u0645\u0647"}},{"language":"fi","version":"4.8","updated":"2017-06-08 18:25:22","english_name":"Finnish","native_name":"Suomi","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/fi.zip","iso":{"1":"fi","2":"fin"},"strings":{"continue":"Jatka"}},{"language":"fr_BE","version":"4.8","updated":"2017-06-23 06:47:57","english_name":"French (Belgium)","native_name":"Fran\u00e7ais de Belgique","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/fr_BE.zip","iso":{"1":"fr","2":"fra"},"strings":{"continue":"Continuer"}},{"language":"fr_CA","version":"4.8","updated":"2017-07-05 17:58:06","english_name":"French (Canada)","native_name":"Fran\u00e7ais du Canada","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/fr_CA.zip","iso":{"1":"fr","2":"fra"},"strings":{"continue":"Continuer"}},{"language":"fr_FR","version":"4.8","updated":"2017-07-07 13:48:37","english_name":"French (France)","native_name":"Fran\u00e7ais","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/fr_FR.zip","iso":{"1":"fr"},"strings":{"continue":"Continuer"}},{"language":"gd","version":"4.7.2","updated":"2016-08-23 17:41:37","english_name":"Scottish Gaelic","native_name":"G\u00e0idhlig","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/gd.zip","iso":{"1":"gd","2":"gla","3":"gla"},"strings":{"continue":"Lean air adhart"}},{"language":"gl_ES","version":"4.8","updated":"2017-06-17 20:40:15","english_name":"Galician","native_name":"Galego","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/gl_ES.zip","iso":{"1":"gl","2":"glg"},"strings":{"continue":"Continuar"}},{"language":"gu","version":"4.8","updated":"2017-06-07 12:07:46","english_name":"Gujarati","native_name":"\u0a97\u0ac1\u0a9c\u0ab0\u0abe\u0aa4\u0ac0","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/gu.zip","iso":{"1":"gu","2":"guj"},"strings":{"continue":"\u0a9a\u0abe\u0ab2\u0ac1 \u0ab0\u0abe\u0a96\u0ab5\u0ac1\u0a82"}},{"language":"haz","version":"4.4.2","updated":"2015-12-05 00:59:09","english_name":"Hazaragi","native_name":"\u0647\u0632\u0627\u0631\u0647 \u06af\u06cc","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.4.2\/haz.zip","iso":{"3":"haz"},"strings":{"continue":"\u0627\u062f\u0627\u0645\u0647"}},{"language":"he_IL","version":"4.8","updated":"2017-06-15 13:33:29","english_name":"Hebrew","native_name":"\u05e2\u05b4\u05d1\u05b0\u05e8\u05b4\u05d9\u05ea","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/he_IL.zip","iso":{"1":"he"},"strings":{"continue":"\u05d4\u05de\u05e9\u05da"}},{"language":"hi_IN","version":"4.8","updated":"2017-06-17 08:25:42","english_name":"Hindi","native_name":"\u0939\u093f\u0928\u094d\u0926\u0940","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/hi_IN.zip","iso":{"1":"hi","2":"hin"},"strings":{"continue":"\u091c\u093e\u0930\u0940"}},{"language":"hr","version":"4.8","updated":"2017-07-02 07:13:09","english_name":"Croatian","native_name":"Hrvatski","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/hr.zip","iso":{"1":"hr","2":"hrv"},"strings":{"continue":"Nastavi"}},{"language":"hu_HU","version":"4.7.2","updated":"2017-01-26 15:48:39","english_name":"Hungarian","native_name":"Magyar","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/hu_HU.zip","iso":{"1":"hu","2":"hun"},"strings":{"continue":"Folytat\u00e1s"}},{"language":"hy","version":"4.7.2","updated":"2016-12-03 16:21:10","english_name":"Armenian","native_name":"\u0540\u0561\u0575\u0565\u0580\u0565\u0576","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/hy.zip","iso":{"1":"hy","2":"hye"},"strings":{"continue":"\u0547\u0561\u0580\u0578\u0582\u0576\u0561\u056f\u0565\u056c"}},{"language":"id_ID","version":"4.8","updated":"2017-06-08 21:11:01","english_name":"Indonesian","native_name":"Bahasa Indonesia","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/id_ID.zip","iso":{"1":"id","2":"ind"},"strings":{"continue":"Lanjutkan"}},{"language":"is_IS","version":"4.7.5","updated":"2017-04-13 13:55:54","english_name":"Icelandic","native_name":"\u00cdslenska","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/is_IS.zip","iso":{"1":"is","2":"isl"},"strings":{"continue":"\u00c1fram"}},{"language":"it_IT","version":"4.8","updated":"2017-07-04 13:01:37","english_name":"Italian","native_name":"Italiano","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/it_IT.zip","iso":{"1":"it","2":"ita"},"strings":{"continue":"Continua"}},{"language":"ja","version":"4.8","updated":"2017-06-25 11:16:15","english_name":"Japanese","native_name":"\u65e5\u672c\u8a9e","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ja.zip","iso":{"1":"ja"},"strings":{"continue":"\u7d9a\u3051\u308b"}},{"language":"ka_GE","version":"4.8","updated":"2017-06-12 09:20:11","english_name":"Georgian","native_name":"\u10e5\u10d0\u10e0\u10d7\u10e3\u10da\u10d8","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ka_GE.zip","iso":{"1":"ka","2":"kat"},"strings":{"continue":"\u10d2\u10d0\u10d2\u10e0\u10eb\u10d4\u10da\u10d4\u10d1\u10d0"}},{"language":"kab","version":"4.8","updated":"2017-07-03 15:14:56","english_name":"Kabyle","native_name":"Taqbaylit","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/kab.zip","iso":{"2":"kab","3":"kab"},"strings":{"continue":"Kemmel"}},{"language":"km","version":"4.7.2","updated":"2016-12-07 02:07:59","english_name":"Khmer","native_name":"\u1797\u17b6\u179f\u17b6\u1781\u17d2\u1798\u17c2\u179a","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/km.zip","iso":{"1":"km","2":"khm"},"strings":{"continue":"\u1794\u1793\u17d2\u178f"}},{"language":"ko_KR","version":"4.8","updated":"2017-06-19 07:08:35","english_name":"Korean","native_name":"\ud55c\uad6d\uc5b4","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ko_KR.zip","iso":{"1":"ko","2":"kor"},"strings":{"continue":"\uacc4\uc18d"}},{"language":"ckb","version":"4.7.2","updated":"2017-01-26 15:48:25","english_name":"Kurdish (Sorani)","native_name":"\u0643\u0648\u0631\u062f\u06cc\u200e","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/ckb.zip","iso":{"1":"ku","3":"ckb"},"strings":{"continue":"\u0628\u0647\u200c\u0631\u062f\u0647\u200c\u0648\u0627\u0645 \u0628\u0647\u200c"}},{"language":"lo","version":"4.7.2","updated":"2016-11-12 09:59:23","english_name":"Lao","native_name":"\u0e9e\u0eb2\u0eaa\u0eb2\u0ea5\u0eb2\u0ea7","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/lo.zip","iso":{"1":"lo","2":"lao"},"strings":{"continue":"\u0e95\u0ecd\u0ec8\u200b\u0ec4\u0e9b"}},{"language":"lt_LT","version":"4.8","updated":"2017-07-05 11:43:04","english_name":"Lithuanian","native_name":"Lietuvi\u0173 kalba","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/lt_LT.zip","iso":{"1":"lt","2":"lit"},"strings":{"continue":"T\u0119sti"}},{"language":"lv","version":"4.7.5","updated":"2017-03-17 20:40:40","english_name":"Latvian","native_name":"Latvie\u0161u valoda","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/lv.zip","iso":{"1":"lv","2":"lav"},"strings":{"continue":"Turpin\u0101t"}},{"language":"mk_MK","version":"4.7.5","updated":"2017-01-26 15:54:41","english_name":"Macedonian","native_name":"\u041c\u0430\u043a\u0435\u0434\u043e\u043d\u0441\u043a\u0438 \u0458\u0430\u0437\u0438\u043a","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/mk_MK.zip","iso":{"1":"mk","2":"mkd"},"strings":{"continue":"\u041f\u0440\u043e\u0434\u043e\u043b\u0436\u0438"}},{"language":"ml_IN","version":"4.7.2","updated":"2017-01-27 03:43:32","english_name":"Malayalam","native_name":"\u0d2e\u0d32\u0d2f\u0d3e\u0d33\u0d02","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/ml_IN.zip","iso":{"1":"ml","2":"mal"},"strings":{"continue":"\u0d24\u0d41\u0d1f\u0d30\u0d41\u0d15"}},{"language":"mn","version":"4.7.2","updated":"2017-01-12 07:29:35","english_name":"Mongolian","native_name":"\u041c\u043e\u043d\u0433\u043e\u043b","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/mn.zip","iso":{"1":"mn","2":"mon"},"strings":{"continue":"\u04ae\u0440\u0433\u044d\u043b\u0436\u043b\u04af\u04af\u043b\u044d\u0445"}},{"language":"mr","version":"4.8","updated":"2017-07-05 19:40:47","english_name":"Marathi","native_name":"\u092e\u0930\u093e\u0920\u0940","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/mr.zip","iso":{"1":"mr","2":"mar"},"strings":{"continue":"\u0938\u0941\u0930\u0941 \u0920\u0947\u0935\u093e"}},{"language":"ms_MY","version":"4.7.5","updated":"2017-03-05 09:45:10","english_name":"Malay","native_name":"Bahasa Melayu","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/ms_MY.zip","iso":{"1":"ms","2":"msa"},"strings":{"continue":"Teruskan"}},{"language":"my_MM","version":"4.1.18","updated":"2015-03-26 15:57:42","english_name":"Myanmar (Burmese)","native_name":"\u1017\u1019\u102c\u1005\u102c","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.1.18\/my_MM.zip","iso":{"1":"my","2":"mya"},"strings":{"continue":"\u1006\u1000\u103a\u101c\u1000\u103a\u101c\u102f\u1015\u103a\u1006\u1031\u102c\u1004\u103a\u1015\u102b\u104b"}},{"language":"nb_NO","version":"4.8","updated":"2017-06-26 11:11:30","english_name":"Norwegian (Bokm\u00e5l)","native_name":"Norsk bokm\u00e5l","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/nb_NO.zip","iso":{"1":"nb","2":"nob"},"strings":{"continue":"Fortsett"}},{"language":"ne_NP","version":"4.8","updated":"2017-06-23 11:30:58","english_name":"Nepali","native_name":"\u0928\u0947\u092a\u093e\u0932\u0940","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ne_NP.zip","iso":{"1":"ne","2":"nep"},"strings":{"continue":"\u091c\u093e\u0930\u0940 \u0930\u093e\u0916\u094d\u0928\u0941\u0939\u094b\u0938\u094d"}},{"language":"nl_BE","version":"4.8","updated":"2017-06-20 17:04:00","english_name":"Dutch (Belgium)","native_name":"Nederlands (Belgi\u00eb)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/nl_BE.zip","iso":{"1":"nl","2":"nld"},"strings":{"continue":"Doorgaan"}},{"language":"nl_NL","version":"4.8","updated":"2017-06-26 13:23:34","english_name":"Dutch","native_name":"Nederlands","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/nl_NL.zip","iso":{"1":"nl","2":"nld"},"strings":{"continue":"Doorgaan"}},{"language":"nl_NL_formal","version":"4.7.5","updated":"2017-02-16 13:24:21","english_name":"Dutch (Formal)","native_name":"Nederlands (Formeel)","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/nl_NL_formal.zip","iso":{"1":"nl","2":"nld"},"strings":{"continue":"Doorgaan"}},{"language":"nn_NO","version":"4.8","updated":"2017-06-08 13:05:53","english_name":"Norwegian (Nynorsk)","native_name":"Norsk nynorsk","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/nn_NO.zip","iso":{"1":"nn","2":"nno"},"strings":{"continue":"Hald fram"}},{"language":"oci","version":"4.7.2","updated":"2017-01-02 13:47:38","english_name":"Occitan","native_name":"Occitan","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/oci.zip","iso":{"1":"oc","2":"oci"},"strings":{"continue":"Contunhar"}},{"language":"pa_IN","version":"4.7.2","updated":"2017-01-16 05:19:43","english_name":"Punjabi","native_name":"\u0a2a\u0a70\u0a1c\u0a3e\u0a2c\u0a40","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/pa_IN.zip","iso":{"1":"pa","2":"pan"},"strings":{"continue":"\u0a1c\u0a3e\u0a30\u0a40 \u0a30\u0a71\u0a16\u0a4b"}},{"language":"pl_PL","version":"4.8","updated":"2017-06-30 13:42:57","english_name":"Polish","native_name":"Polski","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/pl_PL.zip","iso":{"1":"pl","2":"pol"},"strings":{"continue":"Kontynuuj"}},{"language":"ps","version":"4.1.18","updated":"2015-03-29 22:19:48","english_name":"Pashto","native_name":"\u067e\u069a\u062a\u0648","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.1.18\/ps.zip","iso":{"1":"ps","2":"pus"},"strings":{"continue":"\u062f\u0648\u0627\u0645 \u0648\u0631\u06a9\u0693\u0647"}},{"language":"pt_BR","version":"4.8","updated":"2017-06-21 17:29:18","english_name":"Portuguese (Brazil)","native_name":"Portugu\u00eas do Brasil","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/pt_BR.zip","iso":{"1":"pt","2":"por"},"strings":{"continue":"Continuar"}},{"language":"pt_PT","version":"4.8","updated":"2017-06-23 10:24:37","english_name":"Portuguese (Portugal)","native_name":"Portugu\u00eas","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/pt_PT.zip","iso":{"1":"pt"},"strings":{"continue":"Continuar"}},{"language":"rhg","version":"4.7.2","updated":"2016-03-16 13:03:18","english_name":"Rohingya","native_name":"Ru\u00e1inga","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/rhg.zip","iso":{"3":"rhg"},"strings":{"continue":""}},{"language":"ro_RO","version":"4.8","updated":"2017-06-18 18:31:34","english_name":"Romanian","native_name":"Rom\u00e2n\u0103","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ro_RO.zip","iso":{"1":"ro","2":"ron"},"strings":{"continue":"Continu\u0103"}},{"language":"ru_RU","version":"4.8","updated":"2017-06-15 13:54:09","english_name":"Russian","native_name":"\u0420\u0443\u0441\u0441\u043a\u0438\u0439","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ru_RU.zip","iso":{"1":"ru","2":"rus"},"strings":{"continue":"\u041f\u0440\u043e\u0434\u043e\u043b\u0436\u0438\u0442\u044c"}},{"language":"sah","version":"4.7.2","updated":"2017-01-21 02:06:41","english_name":"Sakha","native_name":"\u0421\u0430\u0445\u0430\u043b\u044b\u044b","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/sah.zip","iso":{"2":"sah","3":"sah"},"strings":{"continue":"\u0421\u0430\u043b\u0495\u0430\u0430"}},{"language":"si_LK","version":"4.7.2","updated":"2016-11-12 06:00:52","english_name":"Sinhala","native_name":"\u0dc3\u0dd2\u0d82\u0dc4\u0dbd","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/si_LK.zip","iso":{"1":"si","2":"sin"},"strings":{"continue":"\u0daf\u0dd2\u0d9c\u0da7\u0db8 \u0d9a\u0dbb\u0d9c\u0dd9\u0db1 \u0dba\u0db1\u0dca\u0db1"}},{"language":"sk_SK","version":"4.8","updated":"2017-06-15 09:02:13","english_name":"Slovak","native_name":"Sloven\u010dina","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/sk_SK.zip","iso":{"1":"sk","2":"slk"},"strings":{"continue":"Pokra\u010dova\u0165"}},{"language":"sl_SI","version":"4.8","updated":"2017-06-08 15:29:14","english_name":"Slovenian","native_name":"Sloven\u0161\u010dina","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/sl_SI.zip","iso":{"1":"sl","2":"slv"},"strings":{"continue":"Nadaljuj"}},{"language":"sq","version":"4.7.5","updated":"2017-04-24 08:35:30","english_name":"Albanian","native_name":"Shqip","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/sq.zip","iso":{"1":"sq","2":"sqi"},"strings":{"continue":"Vazhdo"}},{"language":"sr_RS","version":"4.8","updated":"2017-06-08 11:06:53","english_name":"Serbian","native_name":"\u0421\u0440\u043f\u0441\u043a\u0438 \u0458\u0435\u0437\u0438\u043a","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/sr_RS.zip","iso":{"1":"sr","2":"srp"},"strings":{"continue":"\u041d\u0430\u0441\u0442\u0430\u0432\u0438"}},{"language":"sv_SE","version":"4.8","updated":"2017-06-27 07:35:06","english_name":"Swedish","native_name":"Svenska","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/sv_SE.zip","iso":{"1":"sv","2":"swe"},"strings":{"continue":"Forts\u00e4tt"}},{"language":"szl","version":"4.7.2","updated":"2016-09-24 19:58:14","english_name":"Silesian","native_name":"\u015al\u014dnsk\u014f g\u014fdka","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/szl.zip","iso":{"3":"szl"},"strings":{"continue":"K\u014dntynuowa\u0107"}},{"language":"ta_IN","version":"4.7.2","updated":"2017-01-27 03:22:47","english_name":"Tamil","native_name":"\u0ba4\u0bae\u0bbf\u0bb4\u0bcd","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/ta_IN.zip","iso":{"1":"ta","2":"tam"},"strings":{"continue":"\u0ba4\u0bca\u0b9f\u0bb0\u0bb5\u0bc1\u0bae\u0bcd"}},{"language":"te","version":"4.7.2","updated":"2017-01-26 15:47:39","english_name":"Telugu","native_name":"\u0c24\u0c46\u0c32\u0c41\u0c17\u0c41","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/te.zip","iso":{"1":"te","2":"tel"},"strings":{"continue":"\u0c15\u0c4a\u0c28\u0c38\u0c3e\u0c17\u0c3f\u0c02\u0c1a\u0c41"}},{"language":"th","version":"4.7.2","updated":"2017-01-26 15:48:43","english_name":"Thai","native_name":"\u0e44\u0e17\u0e22","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/th.zip","iso":{"1":"th","2":"tha"},"strings":{"continue":"\u0e15\u0e48\u0e2d\u0e44\u0e1b"}},{"language":"tl","version":"4.7.2","updated":"2016-12-30 02:38:08","english_name":"Tagalog","native_name":"Tagalog","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/tl.zip","iso":{"1":"tl","2":"tgl"},"strings":{"continue":"Magpatuloy"}},{"language":"tr_TR","version":"4.8","updated":"2017-06-19 13:54:12","english_name":"Turkish","native_name":"T\u00fcrk\u00e7e","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/tr_TR.zip","iso":{"1":"tr","2":"tur"},"strings":{"continue":"Devam"}},{"language":"tt_RU","version":"4.7.2","updated":"2016-11-20 20:20:50","english_name":"Tatar","native_name":"\u0422\u0430\u0442\u0430\u0440 \u0442\u0435\u043b\u0435","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/tt_RU.zip","iso":{"1":"tt","2":"tat"},"strings":{"continue":"\u0434\u04d9\u0432\u0430\u043c \u0438\u0442\u04af"}},{"language":"tah","version":"4.7.2","updated":"2016-03-06 18:39:39","english_name":"Tahitian","native_name":"Reo Tahiti","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/tah.zip","iso":{"1":"ty","2":"tah","3":"tah"},"strings":{"continue":""}},{"language":"ug_CN","version":"4.7.2","updated":"2016-12-05 09:23:39","english_name":"Uighur","native_name":"Uy\u01a3urq\u0259","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.2\/ug_CN.zip","iso":{"1":"ug","2":"uig"},"strings":{"continue":"\u062f\u0627\u06cb\u0627\u0645\u0644\u0627\u0634\u062a\u06c7\u0631\u06c7\u0634"}},{"language":"uk","version":"4.8","updated":"2017-07-01 22:52:09","english_name":"Ukrainian","native_name":"\u0423\u043a\u0440\u0430\u0457\u043d\u0441\u044c\u043a\u0430","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/uk.zip","iso":{"1":"uk","2":"ukr"},"strings":{"continue":"\u041f\u0440\u043e\u0434\u043e\u0432\u0436\u0438\u0442\u0438"}},{"language":"ur","version":"4.8","updated":"2017-07-02 09:17:00","english_name":"Urdu","native_name":"\u0627\u0631\u062f\u0648","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/ur.zip","iso":{"1":"ur","2":"urd"},"strings":{"continue":"\u062c\u0627\u0631\u06cc \u0631\u06a9\u06be\u06cc\u06ba"}},{"language":"uz_UZ","version":"4.7.5","updated":"2017-05-13 09:55:38","english_name":"Uzbek","native_name":"O\u2018zbekcha","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/uz_UZ.zip","iso":{"1":"uz","2":"uzb"},"strings":{"continue":"Davom etish"}},{"language":"vi","version":"4.8","updated":"2017-06-15 11:24:18","english_name":"Vietnamese","native_name":"Ti\u1ebfng Vi\u1ec7t","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/vi.zip","iso":{"1":"vi","2":"vie"},"strings":{"continue":"Ti\u1ebfp t\u1ee5c"}},{"language":"zh_HK","version":"4.8","updated":"2017-06-15 13:17:37","english_name":"Chinese (Hong Kong)","native_name":"\u9999\u6e2f\u4e2d\u6587\u7248\t","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/zh_HK.zip","iso":{"1":"zh","2":"zho"},"strings":{"continue":"\u7e7c\u7e8c"}},{"language":"zh_TW","version":"4.8","updated":"2017-07-05 10:14:12","english_name":"Chinese (Taiwan)","native_name":"\u7e41\u9ad4\u4e2d\u6587","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.8\/zh_TW.zip","iso":{"1":"zh","2":"zho"},"strings":{"continue":"\u7e7c\u7e8c"}},{"language":"zh_CN","version":"4.7.5","updated":"2017-01-26 15:54:45","english_name":"Chinese (China)","native_name":"\u7b80\u4f53\u4e2d\u6587","package":"http:\/\/downloads.wordpress.org\/translation\/core\/4.7.5\/zh_CN.zip","iso":{"1":"zh","2":"zho"},"strings":{"continue":"\u7ee7\u7eed"}}]}';
		$decoded = json_decode( $string, true );
		return $decoded['translations'];
	}
}