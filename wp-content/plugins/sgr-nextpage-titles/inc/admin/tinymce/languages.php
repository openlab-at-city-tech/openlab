<?php
/**
 * Add Internationalization to Multipage TinyMCE Plugin.
 *
 * This file is based on wp-includes/js/tinymce/langs/wp-langs.php
 *
 * @since 1.3
 */

if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( '_WP_Editors' ) )
    require( ABSPATH . WPINC . '/class-wp-editor.php' );

function multipage_tinymce_plugin_translation() {
    $strings = array(
        'new_subpage'					=> __( 'Start a new Subpage', 'sgr-nextpage-titles' ),
		'enter_subpage_title'			=> __( 'Enter the subpage title', 'sgr-nextpage-titles' ),
		//'subpage_title_too_short'		=> __( 'The subpage title is too short.', 'sgr-nextpage-titles' ),
    );

    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.multipage_tinymce_plugin", ' . json_encode( $strings ) . ");\n";

     return $translated;
}

$strings = multipage_tinymce_plugin_translation();

?>