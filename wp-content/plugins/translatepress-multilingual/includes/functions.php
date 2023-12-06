<?php

/**
 * Outputs language switcher.
 *
 * Uses customization options from Shortcode language switcher.
 */
function trp_the_language_switcher(){
    $trp = TRP_Translate_Press::get_trp_instance();
    $language_switcher = $trp->get_component( 'language_switcher' );
    echo $language_switcher->language_switcher(); /* phpcs:ignore */ /* escaped inside the function */
}

/**
 * Wrapper function for json_encode to eliminate possible UTF8 special character errors
 * @param $value
 * @return mixed|string|void
 */
function trp_safe_json_encode($value){
    if (version_compare(PHP_VERSION, '5.4.0') >= 0 && apply_filters('trp_safe_json_encode_pretty_print', true )) {
        $encoded = json_encode($value, JSON_PRETTY_PRINT);
    } else {
        $encoded = json_encode($value);
    }
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return $encoded;
        case JSON_ERROR_DEPTH:
            return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_STATE_MISMATCH:
            return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_CTRL_CHAR:
            return 'Unexpected control character found';
        case JSON_ERROR_SYNTAX:
            return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_UTF8:
            $clean = trp_utf8ize($value);
            return trp_safe_json_encode($clean);
        default:
            return 'Unknown error'; // or trigger_error() or throw new Exception()

    }
}

/**
 * Helper function for trp_safe_json_encode that helps eliminate utf8 json encode errors
 * @param $mixed
 * @return array|string
 */
function trp_utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = trp_utf8ize($value);
        }
    } else if (is_string ($mixed)) {
        return utf8_encode($mixed);
    }
    return $mixed;
}

/**
 * function that gets the translation for a string with context directly from a .mo file
 * @TODO this was developped firstly for woocommerce so it maybe needs further development.
*/
function trp_x( $text, $context, $domain, $language ) {
    $original_text = $text;

    $cache_key = 'trp_x_' . md5( $text . $context . $domain . $language );
    $new_text  = wp_cache_get( $cache_key );
    if ( $new_text !== false ) {
        return $new_text;
    }
    /* try to find the correct path for the textdomain */
    $path_cache_key = 'trp_x_path_' . md5( $domain . $language );
    $path           = wp_cache_get( $path_cache_key );
    if ( $path === false ) {
        $path = trp_find_translation_location_for_domain( $domain, $language );
        wp_cache_set( $path_cache_key, $path );
    }

    if ( !empty( $path ) ) {

        $mo_file = trp_cache_get( 'trp_x_' . $domain . '_' . $language );

        if ( false === $mo_file ) {
            $mo_file = new MO();
            $mo_file->import_from_file( $path );
            wp_cache_set( 'trp_x_' . $domain . '_' . $language, $mo_file );
        }

        if ( !$mo_file ) {
            $return = apply_filters( 'trp_x', $text, $original_text, $context, $domain, $language );
            wp_cache_set( $cache_key, $return );
            return $return;
        }

        if ( !empty( $mo_file->entries[ $context . '' . $text ] ) ) {
            $text = $mo_file->entries[ $context . '' . $text ]->translations[0];
        }
    }

    $return = apply_filters( 'trp_x', $text, $original_text, $context, $domain, $language );
    wp_cache_set( $cache_key, $return );
    return $return;
}

/**
 * Function that tries to find the path for a translation file defined by textdomain and language
 * @param $domain the textdomain of the string that you want the translation for
 * @param $language the language in which you want the translation
 * @return string the path of the mo file if it is found else an empty string
 */
function trp_find_translation_location_for_domain( $domain, $language ){
    global $trp_template_directory;
    if ( !isset($trp_template_directory)){
        // "caching" this because it sometimes leads to increased page load time due to many calls
        $trp_template_directory = get_template_directory();
    }
    $path = '';

    if( file_exists( WP_LANG_DIR . '/plugins/'. $domain .'-' . $language . '.mo') ) {
        $path = WP_LANG_DIR . '/plugins/'. $domain .'-' . $language . '.mo';
    }
    elseif ( file_exists( WP_LANG_DIR . '/themes/'. $domain .'-' . $language . '.mo') ){
        $path = WP_LANG_DIR . '/themes/'. $domain .'-' . $language . '.mo';
    } elseif( $domain === '' && file_exists( WP_LANG_DIR . '/' . $language . '.mo')){
        $path = WP_LANG_DIR . '/' . $language . '.mo';
    } else {
        $possible_translation_folders = array( '', 'languages/', 'language/', 'translations/', 'translation/', 'lang/' );
        foreach( $possible_translation_folders as $possible_translation_folder ){
            if (file_exists($trp_template_directory . '/' . $possible_translation_folder . $domain . '-' . $language . '.mo')) {
                $path = $trp_template_directory . '/' . $possible_translation_folder . $domain . '-' . $language . '.mo';
            } elseif ( file_exists(WP_PLUGIN_DIR . '/' . $domain . '/' . $possible_translation_folder . $domain . '-' . $language . '.mo') ) {
                $path = WP_PLUGIN_DIR . '/' . $domain . '/' . $possible_translation_folder . $domain . '-' . $language . '.mo';
            }
        }
    }

    return $path;
}

/**
 * Function that appends the affiliate_id to a given url
 * @param $link string the given url to append
 * @return string url with the added affiliate_id
 */
function trp_add_affiliate_id_to_link( $link ){

    //Avangate Affiliate Network
    $avg_affiliate_id = get_option('translatepress_avg_affiliate_id');
    if  ( !empty( $avg_affiliate_id ) ) {
        $link = add_query_arg( 'avgref', $avg_affiliate_id, $link );
    }
    else{
        // AffiliateWP
        $affiliate_id = get_option('translatepress_affiliate_id');
        if  ( !empty( $affiliate_id ) ) {
            $link = add_query_arg( 'ref', $affiliate_id, $link );
        }
    }

    return esc_url( apply_filters( 'trp_affiliate_link', $link ) );
}

/**
 * Function that makes string safe for display.
 *
 * Can be used on original or translated string.
 * Removes any unwanted html code from the string.
 * Do not confuse with trim.
 */
function trp_sanitize_string( $filtered, $execute_wp_kses = true ){
	$filtered = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $filtered );

	// don't remove \r \n \t. They are part of the translation, they give structure and context to the text.
	//$filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
	$filtered = trim( $filtered );

	$found = false;
	while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
		$filtered = str_replace($match[0], '', $filtered);
		$found = true;
	}

	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
	}

    if ( $execute_wp_kses ){
        $filtered = trp_wp_kses( $filtered );
    }
    return $filtered;
}

function trp_wp_kses($string){
    if ( apply_filters('trp_apply_wp_kses_on_strings', true) ){
        add_filter( 'wp_kses_allowed_html', 'trp_prevent_kses_from_stripping_trp_wbr_tag', 10, 2 );
        $string = wp_kses_post($string);
        remove_filter('wp_kses_allowed_html', 'trp_prevent_kses_from_stripping_trp_wbr_tag', 10);
    }

    return $string;
}

function trp_prevent_kses_from_stripping_trp_wbr_tag( $allowedposttags, $context ){

    if ( $context === 'post' ){
        $allowedposttags['wbr'] = true;
    }

    return $allowedposttags;
}

/**
 * function that checks if $_REQUEST['trp-edit-translation'] is set or if it has a certain value
 */
function trp_is_translation_editor( $value = '' ){
    if( isset( $_REQUEST['trp-edit-translation'] ) ){
        if( !empty( $value ) ) {
            if( $_REQUEST['trp-edit-translation'] === $value ) {
                return true;
            }
            else{
                return false;
            }
        }
        else{
            $possible_values = array ('preview', 'true');
            if( in_array( $_REQUEST['trp-edit-translation'], $possible_values ) ) {
                return true;
            }
        }
    }

    return false;
}

function trp_remove_accents( $string ){

    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    if (seems_utf8($string)) {
        $chars = array(
            // Decompositions for Latin-1 Supplement
            'ª' => 'a', 'º' => 'o',
            'À' => 'A', 'Á' => 'A',
            'Â' => 'A', 'Ã' => 'A',
            'Ä' => 'A', 'Å' => 'A',
            'Æ' => 'AE','Ç' => 'C',
            'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ý' => 'Y',
            'Þ' => 'TH','ß' => 's',
            'à' => 'a', 'á' => 'a',
            'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a',
            'æ' => 'ae','ç' => 'c',
            'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i',
            'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y', 'Ø' => 'O',
            // Decompositions for Latin Extended-A
            'Ā' => 'A', 'ā' => 'a',
            'Ă' => 'A', 'ă' => 'a',
            'Ą' => 'A', 'ą' => 'a',
            'Ć' => 'C', 'ć' => 'c',
            'Ĉ' => 'C', 'ĉ' => 'c',
            'Ċ' => 'C', 'ċ' => 'c',
            'Č' => 'C', 'č' => 'c',
            'Ď' => 'D', 'ď' => 'd',
            'Đ' => 'D', 'đ' => 'd',
            'Ē' => 'E', 'ē' => 'e',
            'Ĕ' => 'E', 'ĕ' => 'e',
            'Ė' => 'E', 'ė' => 'e',
            'Ę' => 'E', 'ę' => 'e',
            'Ě' => 'E', 'ě' => 'e',
            'Ĝ' => 'G', 'ĝ' => 'g',
            'Ğ' => 'G', 'ğ' => 'g',
            'Ġ' => 'G', 'ġ' => 'g',
            'Ģ' => 'G', 'ģ' => 'g',
            'Ĥ' => 'H', 'ĥ' => 'h',
            'Ħ' => 'H', 'ħ' => 'h',
            'Ĩ' => 'I', 'ĩ' => 'i',
            'Ī' => 'I', 'ī' => 'i',
            'Ĭ' => 'I', 'ĭ' => 'i',
            'Į' => 'I', 'į' => 'i',
            'İ' => 'I', 'ı' => 'i',
            'Ĳ' => 'IJ','ĳ' => 'ij',
            'Ĵ' => 'J', 'ĵ' => 'j',
            'Ķ' => 'K', 'ķ' => 'k',
            'ĸ' => 'k', 'Ĺ' => 'L',
            'ĺ' => 'l', 'Ļ' => 'L',
            'ļ' => 'l', 'Ľ' => 'L',
            'ľ' => 'l', 'Ŀ' => 'L',
            'ŀ' => 'l', 'Ł' => 'L',
            'ł' => 'l', 'Ń' => 'N',
            'ń' => 'n', 'Ņ' => 'N',
            'ņ' => 'n', 'Ň' => 'N',
            'ň' => 'n', 'ŉ' => 'n',
            'Ŋ' => 'N', 'ŋ' => 'n',
            'Ō' => 'O', 'ō' => 'o',
            'Ŏ' => 'O', 'ŏ' => 'o',
            'Ő' => 'O', 'ő' => 'o',
            'Œ' => 'OE','œ' => 'oe',
            'Ŕ' => 'R','ŕ' => 'r',
            'Ŗ' => 'R','ŗ' => 'r',
            'Ř' => 'R','ř' => 'r',
            'Ś' => 'S','ś' => 's',
            'Ŝ' => 'S','ŝ' => 's',
            'Ş' => 'S','ş' => 's',
            'Š' => 'S', 'š' => 's',
            'Ţ' => 'T', 'ţ' => 't',
            'Ť' => 'T', 'ť' => 't',
            'Ŧ' => 'T', 'ŧ' => 't',
            'Ũ' => 'U', 'ũ' => 'u',
            'Ū' => 'U', 'ū' => 'u',
            'Ŭ' => 'U', 'ŭ' => 'u',
            'Ů' => 'U', 'ů' => 'u',
            'Ű' => 'U', 'ű' => 'u',
            'Ų' => 'U', 'ų' => 'u',
            'Ŵ' => 'W', 'ŵ' => 'w',
            'Ŷ' => 'Y', 'ŷ' => 'y',
            'Ÿ' => 'Y', 'Ź' => 'Z',
            'ź' => 'z', 'Ż' => 'Z',
            'ż' => 'z', 'Ž' => 'Z',
            'ž' => 'z', 'ſ' => 's',
            // Decompositions for Latin Extended-B
            'Ș' => 'S', 'ș' => 's',
            'Ț' => 'T', 'ț' => 't',
            // Euro Sign
            '€' => 'E',
            // GBP (Pound) Sign
            '£' => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            'Ơ' => 'O', 'ơ' => 'o',
            'Ư' => 'U', 'ư' => 'u',
            // grave accent
            'Ầ' => 'A', 'ầ' => 'a',
            'Ằ' => 'A', 'ằ' => 'a',
            'Ề' => 'E', 'ề' => 'e',
            'Ồ' => 'O', 'ồ' => 'o',
            'Ờ' => 'O', 'ờ' => 'o',
            'Ừ' => 'U', 'ừ' => 'u',
            'Ỳ' => 'Y', 'ỳ' => 'y',
            // hook
            'Ả' => 'A', 'ả' => 'a',
            'Ẩ' => 'A', 'ẩ' => 'a',
            'Ẳ' => 'A', 'ẳ' => 'a',
            'Ẻ' => 'E', 'ẻ' => 'e',
            'Ể' => 'E', 'ể' => 'e',
            'Ỉ' => 'I', 'ỉ' => 'i',
            'Ỏ' => 'O', 'ỏ' => 'o',
            'Ổ' => 'O', 'ổ' => 'o',
            'Ở' => 'O', 'ở' => 'o',
            'Ủ' => 'U', 'ủ' => 'u',
            'Ử' => 'U', 'ử' => 'u',
            'Ỷ' => 'Y', 'ỷ' => 'y',
            // tilde
            'Ẫ' => 'A', 'ẫ' => 'a',
            'Ẵ' => 'A', 'ẵ' => 'a',
            'Ẽ' => 'E', 'ẽ' => 'e',
            'Ễ' => 'E', 'ễ' => 'e',
            'Ỗ' => 'O', 'ỗ' => 'o',
            'Ỡ' => 'O', 'ỡ' => 'o',
            'Ữ' => 'U', 'ữ' => 'u',
            'Ỹ' => 'Y', 'ỹ' => 'y',
            // acute accent
            'Ấ' => 'A', 'ấ' => 'a',
            'Ắ' => 'A', 'ắ' => 'a',
            'Ế' => 'E', 'ế' => 'e',
            'Ố' => 'O', 'ố' => 'o',
            'Ớ' => 'O', 'ớ' => 'o',
            'Ứ' => 'U', 'ứ' => 'u',
            // dot below
            'Ạ' => 'A', 'ạ' => 'a',
            'Ậ' => 'A', 'ậ' => 'a',
            'Ặ' => 'A', 'ặ' => 'a',
            'Ẹ' => 'E', 'ẹ' => 'e',
            'Ệ' => 'E', 'ệ' => 'e',
            'Ị' => 'I', 'ị' => 'i',
            'Ọ' => 'O', 'ọ' => 'o',
            'Ộ' => 'O', 'ộ' => 'o',
            'Ợ' => 'O', 'ợ' => 'o',
            'Ụ' => 'U', 'ụ' => 'u',
            'Ự' => 'U', 'ự' => 'u',
            'Ỵ' => 'Y', 'ỵ' => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin)
            'ɑ' => 'a',
            // macron
            'Ǖ' => 'U', 'ǖ' => 'u',
            // acute accent
            'Ǘ' => 'U', 'ǘ' => 'u',
            // caron
            'Ǎ' => 'A', 'ǎ' => 'a',
            'Ǐ' => 'I', 'ǐ' => 'i',
            'Ǒ' => 'O', 'ǒ' => 'o',
            'Ǔ' => 'U', 'ǔ' => 'u',
            'Ǚ' => 'U', 'ǚ' => 'u',
            // grave accent
            'Ǜ' => 'U', 'ǜ' => 'u',
        );

        // Used for locale-specific rules
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component( 'settings' );
        $settings = $trp_settings->get_settings();

        $default_language= $settings["default-language"];
        $locale = $default_language;

        if ( 'de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale ) {
            $chars[ 'Ä' ] = 'Ae';
            $chars[ 'ä' ] = 'ae';
            $chars[ 'Ö' ] = 'Oe';
            $chars[ 'ö' ] = 'oe';
            $chars[ 'Ü' ] = 'Ue';
            $chars[ 'ü' ] = 'ue';
            $chars[ 'ß' ] = 'ss';
        } elseif ( 'da_DK' === $locale ) {
            $chars[ 'Æ' ] = 'Ae';
            $chars[ 'æ' ] = 'ae';
            $chars[ 'Ø' ] = 'Oe';
            $chars[ 'ø' ] = 'oe';
            $chars[ 'Å' ] = 'Aa';
            $chars[ 'å' ] = 'aa';
        } elseif ( 'ca' === $locale ) {
            $chars[ 'l·l' ] = 'll';
        } elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
            $chars[ 'Đ' ] = 'DJ';
            $chars[ 'đ' ] = 'dj';
        }

        $string = strtr($string, $chars);
    } else {
        $chars = array();
        // Assume ISO-8859-1 if not UTF-8
        $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
            ."\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
            ."\xc3\xc4\xc5\xc7\xc8\xc9\xca"
            ."\xcb\xcc\xcd\xce\xcf\xd1\xd2"
            ."\xd3\xd4\xd5\xd6\xd8\xd9\xda"
            ."\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
            ."\xe4\xe5\xe7\xe8\xe9\xea\xeb"
            ."\xec\xed\xee\xef\xf1\xf2\xf3"
            ."\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
            ."\xfc\xfd\xff";

        $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

        $string = strtr($string, $chars['in'], $chars['out']);
        $double_chars = array();
        $double_chars['in'] = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
        $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
};

/**
 * Output an SVG depending on case.
 *
 * @param string $icon The icon to output. Default no icon.
 */
function trp_output_svg( $icon = '' ) {
    switch ( $icon ) {
        case 'check':
            ?>
            <svg class="trp-svg-icon fas-check-circle"><use xlink:href="#check-circle"></use></svg>
            <?php
            break;
        case 'error':
            ?>
            <svg class="trp-svg-icon fas-times-circle"><use xlink:href="#times-circle"></use></svg>
            <?php
            break;
        default:
            break;
    }
}

/**
 * Debuger function. Mainly designed for the get_url_for_language() function
 *
 * @since 1.3.6
 *
 * @param bool $enabled
 * @param array $logger
 */
function trp_bulk_debug($debug = false, $logger = array()){
    if(!$debug){
        return;
    }
    error_log('---------------------------------------------------------');
    $key_length = '';
    foreach ($logger as $key => $value){
        if ( strlen($key) > $key_length)
            $key_length = strlen($key);
    }

    foreach ($logger as $key => $value){
        error_log("$key :   " . str_repeat(' ', $key_length - strlen($key)) . $value);
    }
    error_log('---------------------------------------------------------');
}

/**
 * Used for showing useful notice in Translation Editor
 *
 * @return bool
 */
function trp_is_paid_version() {
	$licence = get_option( 'trp_licence_key' );

	if ( ! empty( $licence ) ) {
		return true;
	}

	//list of class names
	$addons = apply_filters( 'trp_paid_addons', array(
		'TRP_IN_Automatic_Language_Detection',
		'TRP_IN_Browse_as_other_Role',
		'TRP_IN_Extra_Languages',
		'TRP_IN_Navigation_Based_on_Language',
		'TRP_IN_Seo_Pack',
		'TRP_IN_Translator_Accounts',
        'TRP_Automatic_Language_Detection',
        'TRP_Browse_as_other_Role',
        'TRP_Extra_Languages',
        'TRP_Navigation_Based_on_Language',
        'TRP_Seo_Pack',
        'TRP_Translator_Accounts',
	) );

	foreach ( $addons as $className ) {
		if ( class_exists( $className ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Execute do_shortcode with a specific list of tags
 *
 * @param $content          string      String to execute do_shortcode on
 * @param $tags_allowed     array       Array of tags allowed to be executed
 * @return string           string      Resulted string
 */
function trp_do_these_shortcodes( $content, $tags_allowed ){
    global $shortcode_tags;
    $copy_shortcode_tags = $shortcode_tags;

    // select the allowed shortocde tags from the global array
    $allowed_shortcode_tags = array();
    foreach( $shortcode_tags as $shortcode_tag_key => $shortcode_tag_value){
        if ( in_array( $shortcode_tag_key, $tags_allowed ) ){
            $allowed_shortcode_tags[$shortcode_tag_key] = $shortcode_tag_value;
        }
    }

    // only execute these shortcode tags on the content
    $shortcode_tags = $allowed_shortcode_tags;

    // run shortcode
    $return_content = do_shortcode($content);

    // revert changes to shortcode_tags array
    $shortcode_tags = $copy_shortcode_tags;

    return $return_content;
}

/**
 * Obtains a list of TP languages. Can be without the default one
 * in which case use the parameter nodefault set to 'nodefault'
 *
 * @param string $nodefault param used to return published languages without default one
 * @return mixed array with key/value pairs of published language codes and names
 *
 */
function trp_get_languages($nodefault=null)
{
    $trp_obj = TRP_Translate_Press::get_trp_instance();
    $settings_obj = $trp_obj->get_component('settings');
    $lang_obj = $trp_obj->get_component('languages');

    $default_lang_labels = $settings_obj->get_setting('default-language');
    $published_lang = $settings_obj->get_setting('publish-languages');
    $published_lang_labels = $lang_obj->get_language_names($published_lang);
    if (isset($nodefault) && $nodefault === 'nodefault'){
        unset ($published_lang_labels[$default_lang_labels]);
    }
    return ($published_lang_labels);
}

/**
 * Wrapper function for wp_cache_get() that bypasses cache if TRP_DEBUG is on
 * @param int|string $key   The key under which the cache contents are stored.
 * @param string     $group Optional. Where the cache contents are grouped. Default empty.
 * @param bool       $force Optional. Whether to force an update of the local cache
 *                          from the persistent cache. Default false.
 * @param bool       $found Optional. Whether the key was found in the cache (passed by reference).
 *                          Disambiguates a return of false, a storable value. Default null.
 * @return mixed|false The cache contents on success, false on failure to retrieve contents or false when WP_DEBUG is on
 *
 */
function trp_cache_get( $key, $group = '', $force = false, &$found = null ){
    if( defined( 'TRP_DEBUG' ) && TRP_DEBUG == true )
        return false;

    $cache = wp_cache_get( $key, $group, $force, $found );
    return $cache;
}

/**
 * Wrapper function for get_transient() that bypasses cache if TRP_DEBUG is on
 */
function trp_get_transient( $transient ){
    if( ( defined( 'TRP_DEBUG' ) && TRP_DEBUG == true ) || defined( 'TRP_DEBUG_TRANSIENT' ) && TRP_DEBUG_TRANSIENT == true  )
        return false;

    return get_transient($transient);
}

/**
 * Determine if the setting in Advanced Options should make us add a slash at end of string
 * @param $settings the TranslatePress settings object
 * @return bool
 */
function trp_force_slash_at_end_of_link( $settings ){
    if ( !empty( $settings['trp_advanced_settings'] ) && isset( $settings['trp_advanced_settings']['force_slash_at_end_of_links'] ) && $settings['trp_advanced_settings']['force_slash_at_end_of_links'] === 'yes' )
        return true;
    else
        return false;
}

/**
 * This function is used by users to create their own language switcher.
 *It returns an array with all the necessary information for the user to create their own custom language switcher.
 *
 * @return array
 *
 * The array returned has the following indexes: language_name, language_code, short_language_name, flag_link, current_page_url
 */

function trp_custom_language_switcher() {
    $trp           = TRP_Translate_Press::get_trp_instance();
    $trp_languages = $trp->get_component( 'languages' );
    $trp_settings  = $trp->get_component( 'settings' );
    $settings      = $trp_settings->get_settings();

    $languages_to_display  = $settings['publish-languages'];
    $translation_languages = $trp_languages->get_language_names( $languages_to_display );

    $url_converter = $trp->get_component( 'url_converter' );

    $custom_ls_array = array();

    foreach ( $translation_languages as $item => $language ) {

        $custom_ls_array[ $item ]['language_name']       = $language;
        $custom_ls_array[ $item ]['language_code']       = $item;
        $custom_ls_array[ $item ]['short_language_name'] = $url_converter->get_url_slug( $item, false );

        $flags_path = TRP_PLUGIN_URL . 'assets/images/flags/';
        $flags_path = apply_filters( 'trp_flags_path', $flags_path, $item );

        $flag_file_name = $item . '.png';
        $flag_file_name = apply_filters( 'trp_flag_file_name', $flag_file_name, $item );

        $custom_ls_array[ $item ]['flag_link'] = esc_url( $flags_path . $flag_file_name );

        $custom_ls_array[ $item ]['current_page_url'] = esc_url( $url_converter->get_url_for_language( $item, null, '' ) );
    }

    return $custom_ls_array;
}

/**Function that provides translation for a specific text or html content, into another language.
 * The function can be used by third party plugin/theme authors.
 *
 * @param string $content is the content you want to translate, it must be in the default language and it can be any text or html code
 * @param string $language is the language you want to translate the content into, if it is left undefined the content will be translated
 * to the current language; it's set to current language by default
 * @param bool $prevent_over_translation is a parameter that prevents the translated content from being translated again during the translation
 * of the page. This can be set to false if the translated content is used in a way that TranslatePress can't detect the text.
 * It's set to true by default
 * @return string is the translated content in the chosen language
 */
function trp_translate( $content, $language = null, $prevent_over_translation = true ){
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_render = $trp->get_component( 'translation_render' );
    global $TRP_LANGUAGE;

    $lang_backup = $TRP_LANGUAGE;

    if ($language !== null){
        $TRP_LANGUAGE = $language;
    }
    $translated_custom_content = $trp_render->translate_page($content);

    if ($prevent_over_translation === true){
        $translated_custom_content = '<span data-no-translation>' . $translated_custom_content .'</span>';
    }

    $TRP_LANGUAGE = $lang_backup;

    return $translated_custom_content;
}

function trp_get_license_status(){
    $license_details = get_option( 'trp_license_details' );
    $is_demosite = ( strpos(site_url(), 'https://demo.translatepress.com' ) !== false );
    $status = 'free-version';
    if( !empty($license_details) && !$is_demosite) {
        /* if we have any invalid response for any of the addon show just the error notification and ignore any valid responses */
        if ( !empty( $license_details['invalid'] ) ) {
            $status = 'invalid';
            //take the first addon details (it should be the same for the rest of the invalid ones)
            $license_detail = $license_details['invalid'][0];
            if( $license_detail->error == 'missing' )
                $status = 'missing';
            elseif( $license_detail->error == 'expired' ){
                $status = 'expired';
            }elseif( $license_detail->error == 'revoked' ){
                $status = 'revoked';
            }
        }elseif( !empty( $license_details['valid'] ) ){
            $status = 'valid';
        }
    }
    return $status;
}

/**
 * Used by third parties to briefly switch language such as when sending an email
 * To get a user's preferred language use this code: get_user_meta( $user_id, 'trp_language', true );
 *
 * @param $language
 * @return void
 */
function trp_switch_language($language){
    global $TRP_LANGUAGE, $TRP_LANGUAGE_COPY, $TRP_LANGUAGE_ORIGINAL;
    $language = trp_validate_language( $language );
    $TRP_LANGUAGE_ORIGINAL = $TRP_LANGUAGE;
    $TRP_LANGUAGE = $language;
    $TRP_LANGUAGE_COPY = $language;

    // Because of 'trp_before_translate_content' filter function is_ajax_frontend() is called and it changes the global $TRP_LANGUAGE according to the url from which it was called.
    // Function trp_reset_language() is added on the hook in order to set global $TRP_LANGUAGE according to our need for the email language instead.
    add_filter( 'trp_before_translate_content', 'trp_reset_language', 99999999 );

    switch_to_locale($language);
    add_filter( 'plugin_locale', 'trp_get_locale', 99999999);
}

/**
 * Return $TRP_LANGUAGE as plugin locale
 *
 * @return mixed
 */
function trp_get_locale() {
    global $TRP_LANGUAGE;
    return $TRP_LANGUAGE;
}

/**
 * The value of $TRP_LANGUAGE is set according to the url, which can be problematic in some cases when sending emails
 * Restore the $TRP_LANGUAGE value in which email will be sent
 *
 * @param $output
 * @return mixed
 */
function trp_reset_language( $output ){
    global $TRP_LANGUAGE, $TRP_LANGUAGE_COPY;
    $TRP_LANGUAGE = $TRP_LANGUAGE_COPY;
    return $output;
}

/**
 * Return a valid TRP language in which the email will be sent
 *
 * @param $language
 * @return mixed
 */
function trp_validate_language( $language ){
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();
    if( empty( $language ) || !in_array( $language, $settings['translation-languages'] ) ){
        $language = $settings['default-language'];
    }
    return $language;
}

/**
 * Used by third parties to restore original language after using trp_switch_language
 */
function trp_restore_language(){
    global $TRP_LANGUAGE, $TRP_LANGUAGE_ORIGINAL;
    remove_filter( 'trp_before_translate_content', 'trp_reset_language' );

    restore_previous_locale();
    remove_filter( 'plugin_locale', 'trp_get_locale' );
    $TRP_LANGUAGE = $TRP_LANGUAGE_ORIGINAL;
}

/**
 * Determine user language
 *
 * @param $user_id
 * @return mixed
 */
function trp_get_user_language( $user_id ){
    return trp_validate_language( get_user_meta( $user_id, 'trp_language', true ) );
}
/**
 * Wrapper function for WooCommerce HPOS add, delete and update operations
 * Falls back to the traditional post_meta operations
 *
 * @param $order_id         int     Post ID
 * @param $meta_key         string  Metadata key
 * @param $meta_value       mixed   Metadata value
 * @param $operation_type   string  Parameter used to determine the type of operation that needs to be performed.
 *                                  Accepts: add / delete / update
 */
function trp_woo_hpos_manipulate_post_meta( $order_id, $meta_key, $meta_value, $operation_type ){

    if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
        $order    = wc_get_order( $order_id );
        $function = $operation_type . '_meta_data';

        $order->$function( $meta_key, $meta_value );
        $order->save();

        return;
    }

    $function = $operation_type . '_post_meta';

    $function( $order_id, $meta_key, $meta_value );
}

/**
 * Wrapper function for WooCommerce HPOS get operation
 * Falls back to the traditional post_meta operation
 *
 * @param  $order_id       int     Post ID
 * @param  $meta_key       string  Metadata key
 * @param  $single         bool    Whether to return a single value or not. Default: false
 * @return                 mixed   An array of values if `$single` is false. The value of the meta field if `$single` is true. False for an invalid `$post_id` (non-numeric, zero, or negative value). An empty string if a valid but non-existing post ID is passed.
 */
function trp_woo_hpos_get_post_meta( $order_id, $meta_key, $single = false ){
    if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
        $order = wc_get_order( $order_id );

        return $order->get_meta( $meta_key, $single );
    }

    return get_post_meta( $order_id, $meta_key, $single );
}

/**
 * Helper function that determines if we should output the dynamic translation script later than usual
 *
 * Some plugins add HTML to the DOM very late in the page load cycle, so the site becomes slow due our mutation observer capturing it
 *
 * @return bool
 */
function is_late_dom_html_plugin_active(){
    $classes_array = ['QueryMonitor']; // for the moment, only Query Monitor matches the criteria

    foreach ( $classes_array as $class ){
        if ( class_exists( $class ) ) return true;
    }

    return apply_filters( 'trp_delay_dom_changes_script', false );
}
