<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_exclude_gettext_strings', 100 );
function trp_register_exclude_gettext_strings( $settings_array ){
	$settings_array[] = array(
		'name'          => 'exclude_gettext_strings',
		'type'          => 'list',
		'columns'       => array(
								'string' => __('Gettext String', 'translatepress-multilingual' ),
								'domain' => __('Domain', 'translatepress-multilingual')
							),
		'label'         => esc_html__( 'Exclude Gettext Strings', 'translatepress-multilingual' ),
		'description'   => wp_kses( __( 'Exclude these strings from being translated as Gettext strings by TranslatePress. Leave the domain empty to take into account any Gettext string.<br/>Can still be translated through po/mo files.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            => 'exclude_strings',
        'container'     => 'exclude_gettext_strings'
	);
	return $settings_array;
}

/**
 * Exclude gettext from being translated
 */
add_action( 'init', 'trp_load_exclude_strings' );
function trp_load_exclude_strings(){
	$option = get_option( 'trp_advanced_settings', true );

	if( isset( $option['exclude_gettext_strings'] ) && count( $option['exclude_gettext_strings']['string'] ) > 0 )
		add_filter('trp_skip_gettext_processing', 'trp_exclude_strings', 1000, 4 );

}

function trp_exclude_strings ( $return, $translation, $text, $domain ){
	$option = get_option( 'trp_advanced_settings', true );

	if ( isset( $option['exclude_gettext_strings'] ) ) {

		foreach( $option['exclude_gettext_strings']['string'] as $key => $string ){

            if((empty(trim($string))) && (trim($domain ) === trim( $option['exclude_gettext_strings']['domain'][$key]))){

                return true;
            }

			if( trim( $text ) === trim( $string ) ){

				if( empty( $option['exclude_gettext_strings']['domain'][$key] ) )
					return true;
				else if( trim( $domain ) === trim( $option['exclude_gettext_strings']['domain'][$key] ) )
					return true;

			}

		}
	}

	return $return;
}
