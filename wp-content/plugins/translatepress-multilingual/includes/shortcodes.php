<?php
// add conditional language shortcode
add_shortcode( 'trp_language', 'trp_language_content');

/* ---------------------------------------------------------------------------
 * Shortcode [trp_language language="en_EN"] [/trp_language]
 * --------------------------------------------------------------------------- */


function trp_language_content( $attr, $content = null ){

    global $TRP_LANGUAGE_SHORTCODE;
    if (!isset($TRP_LANGUAGE_SHORTCODE)){
        $TRP_LANGUAGE_SHORTCODE = array();
    }

    $TRP_LANGUAGE_SHORTCODE[] = $content;

    extract(shortcode_atts(array(
        'language' => '',
    ), $attr));

    $current_language = get_locale();

    if( $current_language == $language ){
        $output = do_shortcode($content);
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