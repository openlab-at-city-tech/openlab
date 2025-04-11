<?php


if ( !defined('ABSPATH' ) )
    exit();

add_image_size( 'trp-custom-language-flag', 18, 12 );

// Register country flag size for use in Add Media modal
add_filter( 'image_size_names_choose', 'trp_add_flag_sizes' );
function trp_add_flag_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'trp-custom-language-flag' => __( 'Custom Language Flag', 'translatepress-multilingual' )
	) );
}

add_filter( 'trp_wp_languages', 'trpc_add_custom_language', 10, 2 );
function trpc_add_custom_language( $languages ) {

	$option = get_option( 'trp_advanced_settings', true );

	if ( isset( $option['custom_language'] ) ) {
		//print_r($option['custom_language'];

		foreach ( $option['custom_language']['cuslangname'] as $key => $value ) {


		    if(isset($option["custom_language"]["cuslangcode"][ $key ])) {
                $lang = $option["custom_language"]["cuslangcode"][$key];
            }else{
		        $lang = $option["custom_language"]["cuslangiso"][ $key ];
            }

			$custom_language_iso    = $option["custom_language"]["cuslangiso"][ $key ];
			$custom_language_name   = $option["custom_language"]["cuslangname"][ $key ];
			$custom_language_native = $option["custom_language"]["cuslangnative"][ $key ];

            if ( array_key_exists( $lang, $languages ) ) {
                if(empty( $custom_language_name )){
                    $custom_language_name = $languages[$lang]['english_name'];
                }
                if(empty( $custom_language_native )){
                    $custom_language_native = $languages[$lang]['native_name'];
                }
                if(empty( $custom_language_iso )){
                    $custom_language_iso = reset($languages[$lang]['iso']);
                }
            }else{
                if( empty($custom_language_iso) && isset($option["custom_language"]["cuslangcode"][ $key ])){
                    $custom_language_iso = $option["custom_language"]["cuslangcode"][$key];
                }
            }

			$languages[ $lang ] = array(
				'language'           => $lang,
				'english_name'       => $custom_language_name,
				'native_name'        => $custom_language_native,
                'iso'                => array( $custom_language_iso ),
                'is_custom_language' => true

			);

			global $TRP_LANGUAGE;

			if ( isset( $option["cuslangisrtl"] ) && $option["cuslangisrtl"] === 'yes' && $TRP_LANGUAGE === $custom_language_iso ) {
				$GLOBALS['text_direction'] = 'rtl';
			}
		}
	}

	return $languages;
}

add_filter('gettext_with_context', 'trpc_language_rtl', 10, 4);
function trpc_language_rtl($translated, $text, $context, $domain){
	$option = get_option( 'trp_advanced_settings', true );
	global $TRP_LANGUAGE;

	if ( isset( $option['custom_language'] ) ) {
		foreach ( $option['custom_language']['cuslangname'] as $key => $value ) {
			$custom_language_code = $option["custom_language"]["cuslangcode"][$key];
			if($text == 'ltr' && $context == "text direction" && isset($option["custom_language"]["cuslangisrtl"][$key]) && $option["custom_language"]["cuslangisrtl"][$key] === 'yes' && $TRP_LANGUAGE === $custom_language_code){
				$translated = 'rtl';
			}
		}
	}
	return $translated;
}

add_filter( 'trp_flags_path', 'trpc_flags_path_custom', 10, 2 );
/**
 * @param $original_flags_path
 * @param $language_code
 *
 * @return mixed
 *
 * Returns the original flags path for original languages
 * Or the custom flag path for flags uploaded into the media library
 * The image is returned resized to the custom size dictated bu trp-custom-language-flag
 *
 */
function trpc_flags_path_custom( $original_flags_path,  $language_code ) {

	// only change the folder path for the custom languages:
	$option = get_option( 'trp_advanced_settings', true );

	if ( isset( $option['custom_language'] ) ) {
		foreach ( $option['custom_language']['cuslangname'] as $key => $value ) {
			if ($language_code === $option["custom_language"]["cuslangcode"][$key] && !empty($option["custom_language"]["cuslangflag"][$key]) ) {
				$attachment_array = wp_get_attachment_image_src(attachment_url_to_postid($option["custom_language"]["cuslangflag"][ $key ]), 'trp-custom-language-flag');
                return isset($attachment_array) && $attachment_array ? $attachment_array[0] : $option["custom_language"]["cuslangflag"][ $key ];
			}
		}
	}
	return $original_flags_path;
}


add_filter( 'trp_flag_file_name', 'trpc_flag_name_custom', 10, 2 );
/**
 * @param $original_flags_path
 * @param $language_code
 *
 * @return string
 *
 * For the custom languages the flag name is contained into the flag path
 * it does not follow the naming pattern language.png
 * So no need to return anything in that case
 */
function trpc_flag_name_custom ( $original_flags_path,  $language_code ){
	// only change flag name for the custom languages:
	$option = get_option( 'trp_advanced_settings', true );
	if ( isset( $option['custom_language'] ) ) {
		foreach ( $option['custom_language']['cuslangname'] as $key => $value ) {
			if ($language_code === $option["custom_language"]["cuslangcode"][$key] && !empty($option["custom_language"]["cuslangflag"][$key])) {
				return '';
			}
		}
	}
	return $original_flags_path;

}

add_filter('trp_saving_advanced_settings_is_successful', 'trp_add_messages_custom_language_codes', 10, 3);

/**
 * The function verifies if the language codes and ISO codes written by the user contain only the allowed characters, A-Z a-z 0-9 _ - and if the language code is unique among other custom languages and existing languages.
 *
 * @param bool $is_correct_code retains if the language code and the ISO code are valid or not
 * @param $settings
 * @param $submitted_settings
 */

function trp_verify_custom_language_codes($is_correct_code, $settings){

    if(isset($settings['custom_language']['cuslangcode'])) {
        foreach ($settings['custom_language']['cuslangcode'] as $key => $item) {
            if (!empty($settings['custom_language']['cuslangcode'][$key])) {
                if (!trp_is_valid_language_code($item)) {
                    $is_correct_code = false;

                    return array(
                        'message' => esc_html__('The Language code of the added custom language is invalid.','translatepress-multilingual'),
                        'correct_code' => $is_correct_code
                    );
                }
            }else{
                $is_correct_code = false;
                return array(
                    'message'      => esc_html__('The Language code of the added custom language cannot be empty.', 'translatepress-multilingual'),
                    'correct_code' => $is_correct_code
                );
            }
        }
    }

    if(isset($settings['custom_language']['cuslangiso'])) {
        foreach ($settings['custom_language']['cuslangiso'] as $key => $item) {
            if(!empty($settings['custom_language']['cuslangiso'][$key])){
                if (!trp_is_valid_language_code($item)) {
                    $is_correct_code = false;

                    return array(
                        'message' => esc_html__('The Automatic Translation Code of the added custom language is invalid.', 'translatepress-multilingual'),
                        'correct_code' => $is_correct_code
                    );
                }
            }
        }
    }

    return array(
        'message'      => '',
        'correct_code' => $is_correct_code
    );
}

function trp_add_messages_custom_language_codes($correct_code, $settings, $submitted_settings){

    $correct_code_custom_language = trp_verify_custom_language_codes(true, $settings);

    if($correct_code_custom_language['correct_code'] === false){
        /* phpcs:ignore */
        add_settings_error( 'trp_advanced_settings', 'settings_error', esc_html($correct_code_custom_language['message']), 'error' );
        $correct_code = false;

        return $correct_code;
    }
    return $correct_code;
}


add_filter('trp_extra_sanitize_advanced_settings', 'trp_save_settings_language', 10, 3);

/**
 *  The custom language is saved only if the codes are correct.
 * @param $settings
 * @param $submitted_settings
 * @param $prev_settings
 * @return mixed
 */

function trp_save_settings_language($settings, $submitted_settings, $prev_settings){

    $correct_custom_languagea_code = trp_verify_custom_language_codes(true, $settings);

    if($correct_custom_languagea_code['correct_code'] === false) {
        $settings['custom_language'] = $prev_settings['custom_language'];
    }

    return $settings;

}