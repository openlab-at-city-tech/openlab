<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_translation_for_gettext_strings', 523 );
function trp_translation_for_gettext_strings( $settings_array ){
    $settings_array[] = array(
        'name'          => 'disable_translation_for_gettext_strings',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Disable translation for gettext strings', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Gettext Strings are strings outputted by themes and plugins. <br> Translating these types of strings through TranslatePress can be unnecessary if they are already translated using the .po/.mo translation file system.<br>Enabling this option can improve the page load performance of your site in certain cases. The disadvantage is that you can no longer edit gettext translations using TranslatePress, nor benefit from automatic translation on these strings.', 'translatepress-multilingual' ), array( 'br' => array()) ),
        'id'            => 'debug',
        'container'     => 'debug'
        );
    return $settings_array;
}

add_action( 'trp_before_running_hooks', 'trp_remove_hooks_to_disable_gettext_translation', 10, 1);
function trp_remove_hooks_to_disable_gettext_translation( $trp_loader ){
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['disable_translation_for_gettext_strings'] ) && $option['disable_translation_for_gettext_strings'] === 'yes' ) {
        $trp             = TRP_Translate_Press::get_trp_instance();
        $gettext_manager = $trp->get_component( 'gettext_manager' );
        $trp_loader->remove_hook( 'init', 'create_gettext_translated_global', $gettext_manager );
        $trp_loader->remove_hook( 'shutdown', 'machine_translate_gettext', $gettext_manager );
    }
}

add_filter( 'trp_skip_gettext_querying', 'trp_skip_gettext_querying', 10, 4 );
function trp_skip_gettext_querying( $skip, $translation, $text, $domain ){
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['disable_translation_for_gettext_strings'] ) && $option['disable_translation_for_gettext_strings'] === 'yes' ) {
        return true;
    }
    return $skip;
}



add_action( 'trp_editor_notices', 'display_message_for_disable_gettext_in_editor', 10, 1 );
function display_message_for_disable_gettext_in_editor( $trp_editor_notices ) {
    $option = get_option( 'trp_advanced_settings', true );

    // Skip if user dismissed it
    if ( get_user_meta( get_current_user_id(), '_trp_dismissed_gettext_notice', true ) ) {
        return $trp_editor_notices;
    }

    if ( isset( $option['disable_translation_for_gettext_strings'] ) && $option['disable_translation_for_gettext_strings'] === 'yes' ) {
        $url = add_query_arg( array(
            'page' => 'trp_advanced_page#debug_options',
        ), site_url('wp-admin/admin.php') );

        $ajax_url = admin_url( 'admin-ajax.php' );

        $html  = "<div id='trp-gettext-notice' class='trp-notice trp-notice-warning'>";

        $html .= '<p><strong>' . esc_html__( 'Gettext Strings translation is disabled', 'translatepress-multilingual' ) . '</strong></p>';
        $html .= '<p>' . esc_html__( 'To enable it go to ', 'translatepress-multilingual' ) .
            '<a class="trp-link-primary" target="_blank" href="' . esc_url( $url ) . '">' .
            esc_html__( 'TranslatePress->Advanced Settings->Debug->Disable translation for gettext strings', 'translatepress-multilingual' ) .
            '</a>' . esc_html__(' and uncheck the Checkbox.', 'translatepress-multilingual') .'</p>';

        // Custom dismiss link
        $html .= '<a href="#" id="trp-dismiss-gettext-notice" class="trp-button-primary">'. esc_html__('Dismiss', 'translatepress-multilingual') .'</a>';

        // Inline JS with ajax URL hardcoded
        $html .= "<script>
            document.addEventListener('DOMContentLoaded', function(){
                var btn = document.getElementById('trp-dismiss-gettext-notice');
                if (btn) {
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        var notice = document.getElementById('trp-gettext-notice');
                        if (notice) notice.style.display = 'none';
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '" . esc_url( $ajax_url ) . "', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('action=trp_dismiss_gettext_notice');
                    });
                }
            });
        </script>";

        $html .= '</div>';

        $trp_editor_notices = $html;
    }

    return $trp_editor_notices;
}

// Handle AJAX dismiss
add_action( 'wp_ajax_trp_dismiss_gettext_notice', function() {
    if ( current_user_can( 'edit_posts' ) ) {
        update_user_meta( get_current_user_id(), '_trp_dismissed_gettext_notice', true );
    }
    wp_die();
});
