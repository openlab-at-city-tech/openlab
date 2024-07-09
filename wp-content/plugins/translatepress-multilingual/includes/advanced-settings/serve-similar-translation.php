<?php

add_filter('trp_register_advanced_settings', 'serve_similar_translation', 1050);
function serve_similar_translation($settings_array)
{
    $settings_array[] = array(
        'name' => 'serve_similar_translation',
        'type' => 'checkbox',
        'label' => esc_html__('Automatic Translation Memory', 'translatepress-multilingual'),
        'description' => wp_kses(__('Serve same translation for similar text. The strings need to have a percentage of 95% similarity.<br>Helps prevent losing existing translation when correcting typos or making minor adjustments to the original text. <br>If a translation already exists for a very similar original string, it will automatically be used for the current original string.<br>Does not work when making changes to a text that is part of a translation block unless the new text is manually merged again in a translation block.<br>Each string needs to have a minimum of 50 characters.', 'translatepress-multilingual'), array('br' => array())) . '<br><p class="trp-error-inline">' . esc_html__( 'WARNING: This feature can negatively impact page loading times in secondary languages, particularly with large databases (for example websites with a lot of pages or products). If you experience slow loading times, disable this and try again.', 'translatepress-multilingual') . '</p>',
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}


add_filter('trp_add_similar_and_original_strings_to_db', 'trp_add_similar_and_original_strings_to_db');

function trp_add_similar_and_original_strings_to_db($bool){

    $bool = true;
    return $bool;
};

/**
 * In this function we are trying to find original similar strings on the page that are almost identical with a string that exists in DB with a translation
 * The purpose of this advanced setting is to ease the work of the user or to lower the cost of automatic translation by translating almost identical strings
 * with an already existing translation in DB
 */

add_filter( 'trp_get_existing_translations', 'trp_serve_similar_translations', 5, 5);
function trp_serve_similar_translations ( $dictionary, $prepared_query, $strings_array, $language_code, $block_type ){
    if( isset($_GET['trp-edit-translation']) ){
        return $dictionary;
    }

    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_query = $trp->get_component( 'query' );
    $table_name = $trp_query->get_table_name($language_code);

    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['serve_similar_translation'] ) && $option['serve_similar_translation'] === 'yes' ) {

        //here we set the minimum number of characters a string should have to be considered for checking similarity
        //in our case is set to 50 characters but it can be changed
        $minimal_characters_per_strings_considered_for_similarity = apply_filters('trp_minimal_characters_per_strings_considered_for_similarity', 50);

        //here we set the minimal percentage of compatibility between the string in the DB and the similar ones
        //we set it at 95% but it can be changed
        $minimal_percent_of_compatibility = apply_filters('trp_minimal_percent_of_compatibility_for_strings_to_be_similar', 0.95);

        foreach ( $strings_array as $string ) {
            //we try to get the translated strings from the dictionary
            if ( !isset( $dictionary[ $string ] ) ) {
                $result = false;
                $query  = "SELECT original,translated, status FROM `"
                    . sanitize_text_field( $table_name )
                    . "` WHERE status != " . TRP_Query::NOT_TRANSLATED . " AND `original` != '%s' AND MATCH(original) AGAINST ('%s' IN NATURAL LANGUAGE MODE ) LIMIT 1";

                $query  = $trp_query->db->prepare( $query, array( $string, $string ) );
                $result = $trp_query->db->get_results( $query, OBJECT_K );
                if ( !empty( $result ) ) {
                    // we reset the found query which has multiple arguments to the $original argument which is the unaltered string in the default language
                    // after this, we check the minimal length of the two strings and use the function 'trp_dice_match' to determine the percentage of similarity
                    // if the percentage is higher or equal to the chosen value, the similar string gets all the arguments from the string in DB including the translation
                    $original = reset( $result )->original;
                    if ( strlen( $string ) >= $minimal_characters_per_strings_considered_for_similarity && strlen( $original ) >= $minimal_characters_per_strings_considered_for_similarity && trp_dice_match( $string, $original ) >= $minimal_percent_of_compatibility) {
                        $dictionary[ $string ] = reset( $result );
                    }

                }
            }
        }
    }
    return $dictionary;
}


// https://en.wikipedia.org/wiki/S%C3%B8rensen%E2%80%93Dice_coefficient
// PHP clone of https://github.com/stephenjjbrown/string-similarity-js/blob/master/src/string-similarity.ts

function trp_dice_match($string1, $string2)
{
    // we're ignoring punctuation and making everything lowercase in hopes of getting better matches.
    $string1 = strtolower(str_replace(['?', '!', '.', ',', ';', ':'], '', $string1));
    $string2 = strtolower(str_replace(['?', '!', '.', ',', ';', ':'], '', $string2));

    $map = array();
    for ($i = 0; $i < strlen($string1) - 1; $i++ ){
        $substr1 = substr($string1, $i, 2);
        $value = (isset($map[$substr1])) ? $map[$substr1] + 1 : 1;
        $map[$substr1] = $value;
    }

    $match = 0;
    for ($j = 0; $j < strlen($string2) - 1; $j++){
        $substr2 = substr($string2, $j, 2);
        $count = (isset($map[$substr2])) ? $map[$substr2] : 0;
        if ($count > 0){
            $map[$substr2] = $count - 1;
            $match++;
        }
    }

    return ($match * 2) / (strlen($string1) + strlen($string2) - 2);
}