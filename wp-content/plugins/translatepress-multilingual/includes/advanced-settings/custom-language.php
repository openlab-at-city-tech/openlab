<?php

add_filter('trp_register_advanced_settings', 'trp_register_custom_language', 2285);
/*
 * To use the 'mixed' type for advanced settings, there needs to be specified the type of the control
 * There are 4 options to choose from:
 * text: simple textbox
 * textarea: classic textarea used in TP advanced options
 * select: a dropdown select box with the possible options set in a sub-array
 *  like 'option_name'   => array ('label'=> esc_html__( 'Option label', 'translatepress-multilingual' ), 'type' => 'select', 'values' => array ( __('Volvo','translatepress-multilingual') , __('Saab', 'translatepress-multilingual'), __('Scania', 'translatepress-multilingual') ) ),
 *
 *
 * checkbox: a classic checkbox with the checked value always set to 'yes' and the unchecked value to empty.
 * For the elements that don't require pre-determined values, leave the 'values' array empty
 *
 */
function trp_register_custom_language($settings_array){

    $text = wp_kses(  __( 'To edit an existing TranslatePress language, input the language code and fill in only the columns you want to overwrite (e.g. Language name, Flag).<br>You can also add new custom languages. They will be available under General settings, All Languages list, where the URL slug can be edited.<br>For custom flag, first upload the image in media library then paste the URL.<br>Changing or deleting a custom language will impact translations and site URL\'s.<br>The Language code and the Automatic Translation Code should contain only alphabetical values, numerical values, "-" and "_".<br>The Automatic Translation codes can be found on <a href = "https://cloud.google.com/translate/docs/languages" target = "_blank">Google ISO Codes</a> and <a href = "https://www.deepl.com/docs-api/translating-text/" target = "_blank">DeepL Target Codes</a>.' , 'translatepress-multilingual' ), array( 'br' => array(), 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ));
	$settings_array[] = array(
		'name'          => 'custom_language',
		'columns'       => array (

                            'cuslangcode' => array ('label' => esc_html__( 'Language code', 'translatepress-multilingual' ), 'type' => 'text', 'values' => '', 'placeholder' => 'e. g. en_US', 'required' => true ),
                            'cuslangname' => array ('label' => esc_html__( 'Language name', 'translatepress-multilingual' ), 'type' => 'text', 'values' => '', 'placeholder' => '', 'required' => false ),
                            'cuslangnative' => array ('label' => esc_html__( 'Native name', 'translatepress-multilingual' ), 'type' => 'text', 'values' => '', 'placeholder' => '', 'required' => false ),
                            'cuslangiso' => array ('label' => esc_html__( 'Automatic translation code', 'translatepress-multilingual' ), 'type' => 'text', 'values' => '', 'placeholder' => 'e. g. en', 'required' => false ),
                            'cuslangflag' => array ('label' => esc_html__( 'Flag URL', 'translatepress-multilingual' ), 'type' => 'textarea', 'values' => '', 'placeholder' => '', 'required' => false ),
							'cuslangisrtl' => array ('label' => esc_html__( 'Text RTL', 'translatepress-multilingual' ), 'type' => 'checkbox', 'values' => '', 'placeholder' => '', 'required' => false ),
		),
		'type'          => 'mixed',
		'label'         => esc_html__( 'Custom language', 'translatepress-multilingual' ),
        /* phpcs:ignore */
        'description'   => $text,
        'id'            => 'custom_language',

    );

    return $settings_array;
}
