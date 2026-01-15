<?php

if ( !defined('ABSPATH' ) )
    exit();

// add conditional language shortcode
/**
 * Old shortcode that displays different content in a particular language
 *
 * @deprecated 2.9.21 Use the shortcode language_include or language_exclude instead.
 * @see trp_include_content_in_language(), trp_exclude_content_in_language())
 */
add_shortcode( 'trp_language', 'trp_language_content');

/* ---------------------------------------------------------------------------
 * Shortcode [trp_language language="en_US"] [/trp_language]
 * --------------------------------------------------------------------------- */


function trp_language_content( $attr, $content = null ){

    global $TRP_LANGUAGE_SHORTCODE;
    if (!isset($TRP_LANGUAGE_SHORTCODE)){
        $TRP_LANGUAGE_SHORTCODE = array();
    }

    $TRP_LANGUAGE_SHORTCODE[] = $content;

    $attr = shortcode_atts(array(
        'language' => '',
    ), $attr);

    $current_language = get_locale();

    if( $current_language == $attr['language'] ){
        $output = do_shortcode($content);
    }else{
        $output = "";
    }

    return $output;
}

// add conditional languages shortcode
add_shortcode( 'language-include', 'trp_include_content_in_language');

/* ---------------------------------------------------------------------------
 * Shortcode [language-include lang="en_us,fr_fr,ro_RO" enable_translation="yes" (default value)] [/language-include]
 *
 * Displays content in the chosen languages (attribute lang="") and allows to decide if the content should be translatable or not
 * ( enable_translation="yes"/"no" - default value is "yes" )
 * --------------------------------------------------------------------------- */

function trp_include_content_in_language( $attr, $content = null ){

    $attr = shortcode_atts([
        'lang' => '',
        'enable_translation' => 'yes', // default is "yes" if not set
    ], $attr, 'language-include');

    $output = trp_get_include_exclude_content( true, $attr['lang'], $attr['enable_translation'], $content );

    return $output;
}


// add conditional language shortcode
add_shortcode( 'language-exclude', 'trp_exclude_content_in_language');

/* ---------------------------------------------------------------------------
 * Shortcode [language-exclude lang="en_us,fr_fr,ro_RO" enable_translation="yes" (default value)] [/language-exclude]
 *
 * Restricts content in the chosen languages (attribute lang="") and allows to decide if the content should be translatable or not
 * ( enable_translation="yes"/"no" - default value is "yes" )
 * --------------------------------------------------------------------------- */

function trp_exclude_content_in_language( $attr, $content = null ){

    $attr = shortcode_atts([
        'lang' => '',
        'enable_translation' => 'yes', // default is "yes" if not set
    ], $attr, 'language-exclude');

    $output = trp_get_include_exclude_content( false, $attr['lang'], $attr['enable_translation'], $content );

    return $output;
}

// function to be called inside the language_include/ exclude shortcodes
// $include can be true or false
function trp_get_include_exclude_content( $include, $allowed_languages, $enable_translation_var, $content = null )
{

    global $TRP_LANGUAGE;

    $allowed_languages = array_map('trim', explode(',', strtolower($allowed_languages)));

    $enable_translation = true;
    if (isset($enable_translation_var)) {
        $value = strtolower(trim($enable_translation_var));
        if ($value === 'no') {
            $enable_translation = false;
        }
    }

    if ($include === in_array(strtolower($TRP_LANGUAGE), $allowed_languages)) {
        /* "include the content" and "current language is among specified languages"
         * OR
         * "exclude the content" and "current language is NOT among specified languages"
         */
        $output = $enable_translation ? do_shortcode($content) : do_shortcode('<trp-tag data-no-translation>' . $content . '</trp-tag>');
    }else{
        $output = "";
    }
    
    return $output;
}

add_filter('trp_exclude_words_from_automatic_translation', 'trp_add_shortcode_content_to_excluded_words_from_auto_translation');

function trp_add_shortcode_content_to_excluded_words_from_auto_translation($excluded_words){

    global $TRP_LANGUAGE_SHORTCODE;
    if (!isset($TRP_LANGUAGE_SHORTCODE)){
        $TRP_LANGUAGE_SHORTCODE = array();
    }

    $excluded_words = array_merge($excluded_words, $TRP_LANGUAGE_SHORTCODE);

    return $excluded_words;

}