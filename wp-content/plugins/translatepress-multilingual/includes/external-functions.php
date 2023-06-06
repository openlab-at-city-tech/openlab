<?php


/**
 * Trim strings.
 *
 * @param string $string Raw string.
 *
 * @param array $args Array of options eg. enable numerals translation
 *
 * @return string           Trimmed string.
 */

 /* NB: We don't always have access to WP get_option, for instance while calling trp_full_trim inside trp-ajax */
 /* So this falls back to the option being transmitted either as a param from another function or obtained directly if get_option is available */
function trp_full_trim( $string, $args = array()  ) {

    if((is_array($string)) || (is_object($string))){
        return "";
    }

	if ( !isset( $args['numerals']) ) {
		if ( function_exists( 'get_option' ) ) {
			$opt = get_option( 'trp_advanced_settings', false );
			if ( isset( $opt["enable_numerals_translation"] ) ) {
				$args['numerals'] = $opt["enable_numerals_translation"];
			} else {
				$args['numerals'] = "no";
			}
		} else {
			$args['numerals'] = "no";
		}
	}

	/* Apparently the � char in the trim function turns some strings in an empty string so they can't be translated but I don't really know if we should remove it completely
	Removed chr( 194 ) . chr( 160 ) because it altered some special characters (¿¡)
	Also removed \xA0 (the same as chr(160) for altering special characters */
	//$word = trim($word," \t\n\r\0\x0B\xA0�".chr( 194 ) . chr( 160 ) );

	/* Solution to replace the chr(194).chr(160) from trim function, in order to escape the whitespace character ( \xc2\xa0 ), an old bug that couldn't be replicated anymore. */
	/* Trim nbsp the same way as the whitespace (chr194 chr160) above */
	$prefixes = array( "\xc2\xa0", "&nbsp;" );
	do{

	    $previous_iteration_string = $string;

        $string = trim($string, " \t\n\r\0\x0B");


		foreach( $prefixes as $prefix ) {
			$prefix_length = strlen($prefix);
			if (substr($string, 0, $prefix_length) == $prefix) {
				$string = substr($string, $prefix_length);
			}
			if (substr($string, -$prefix_length, $prefix_length) == $prefix) {
				$string = substr($string, 0, -$prefix_length);
			}
		}
	}while( $string != $previous_iteration_string );

	if ($args['numerals'] === "yes") {
		$filter_string = " \t\n\r\0\x0B\xA0�.,/`~!@#\$€£%^&*():;-_=+[]{}\\|?/<>'\"";
	} else {
		$filter_string = " \t\n\r\0\x0B\xA0�.,/`~!@#\$€£%^&*():;-_=+[]{}\\|?/<>1234567890'\"";
	}

	if ( strip_tags( $string ) === '' || trim ($string, $filter_string) === '' ){
		$string = '';
	}
	return $string;
}

function trp_sort_dictionary_by_original( $dictionaries, $type, $group, $languageForId ){
	$array = array();
	foreach( $dictionaries as $language => $dictionary ){
		if ( isset( $dictionary['default-language'] ) && $dictionary['default-language'] == true ){
			continue;
		}
		foreach( $dictionary as $string ) {
			$string = (object)$string;
			if ( isset( $string->original ) ){
				$found = false;
				$string->editedTranslation = $string->translated;
				foreach( $array as $key => $row ){
					if ( $row['original'] == $string->original ){
						if ( !isset( $string->domain ) || ( isset($row['originalId']) && $row['originalId'] == $string->ot_id && $row['pluralForm'] == (int)$string->plural_form ) /*|| ( $string->plural_form == 0 && $string->domain == $row['description'] )*/ ) {
							$array[ $key ]['translationsArray'][ $language ] = $string;
							unset( $array[ $key ]['translationsArray'][ $language ]->original );
							$found = true;

							if ( isset($string->domain) ){
								$array[ $key ]['description'] = $string->domain;
								$array[ $key ]['domain'] = $string->domain;
							}
							if ( $language == $languageForId ){
								$array[ $key ][ 'dbID' ] = $string->id;
							}
							break;
						}
					}
				}
				if ( ! $found ){
					$new_entry = array(
						'type'              => $type,
						'group'         => $group,
						'translationsArray' => array( $language  => $string ),
						'original'          => $string->original
					);
					unset($string->original);

					if ( isset( $string->domain ) ){
						$new_entry['description'] = $string->domain;
					}
                    if ( isset( $string->original_plural ) ){
                        $new_entry['originalPlural'] = $string->original_plural;
                    }
                    if ( isset( $string->context ) ){
                        $new_entry['context'] = $string->context;
                    }
                    if ( $type === 'gettext' ){
                        $new_entry['pluralForm'] = ( isset( $string->plural_form) ) ? $string->plural_form : 0;
                    }
                    if ( isset( $string->ot_id ) ){
                        $new_entry['originalId'] = $string->ot_id;
                    }
					if ( $language == $languageForId ){
						$new_entry['dbID'] = $string->id;
					}
					if ( isset( $new_entry['translationsArray'][$language]->block_type ) ){
						$new_entry['blockType'] = $new_entry['translationsArray'][$language]->block_type;
					}

					$array[] = $new_entry;
				}
			}
		}
	}
	return $array;
}

function trp_is_valid_language_code( $language_code ){
    // allowed characters A-Z a-z 0-9 - _
    if ( empty($language_code) || preg_match('/[^A-Za-z0-9\-_]/i', $language_code ) ) {
        return false;
    }else{
        return true;
    }
}
