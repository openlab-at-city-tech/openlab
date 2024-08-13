<?php

$trp                = TRP_Translate_Press::get_trp_instance();
$machine_translator = $trp->get_component( 'machine_translator' );
$response           = $machine_translator->test_request();
// When MTAPI is active, Api key is actually the license and we should never show it in clear text
$api_key            = ( isset( $this->settings['trp_machine_translation_settings']['translation-engine'] ) && $this->settings['trp_machine_translation_settings']['translation-engine'] == 'mtapi' ) ? false : $machine_translator->get_api_key();
?>

<div id="trp-addons-page" class="wrap">

    <h1> <?php esc_html_e( 'TranslatePress Settings', 'translatepress-multilingual' );?></h1>
    <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

    <div class="grid feat-header">
        <div class="grid-cell">
            <?php if( $api_key != false ) : ?>
                <h2><?php esc_html_e('API Key from settings page:', 'translatepress-multilingual');?> <span style="font-family:monospace"><?php echo esc_html( $api_key ); ?></span></h2>
            <?php endif; ?>

            <h2><?php esc_html_e('HTTP Referrer:', 'translatepress-multilingual');?> <span style="font-family:monospace"><?php echo esc_url( $machine_translator->get_referer() ); ?></span></h2>
            <p><?php esc_html_e('Use this HTTP Referrer if the API lets you restrict key usage from its Dashboard.', 'translatepress-multilingual'); ?></p>

            <h3><?php esc_html_e('Response:', 'translatepress-multilingual');?></h3>
            <pre>
                <?php
                ob_start();
                !is_wp_error( $response ) ? print_r( $response["response"] ) : print_r( $response->get_error_message() );
                $buffer = ob_get_clean();
                echo '<pre>' . esc_html( $buffer ) . '</pre>';
                ?>
            </pre>
            <h3><?php esc_html_e('Response Body:', 'translatepress-multilingual');?></h3>
            <pre>
                <?php
                ob_start();
                !is_wp_error( $response ) ? print_r( esc_html( $response["body"] ) ) : print_r( $response->get_error_data() );
                $buffer = ob_get_clean();
                echo '<pre>' . esc_html( $buffer ) . '</pre>';
                ?>
            </pre>

            <h3><?php esc_html_e('Entire Response From wp_remote_get():', 'translatepress-multilingual');?></h3>
            <pre>
                <?php
                ob_start();
                print_r( $response );
                $buffer = ob_get_clean();
                echo '<pre>' . esc_html( $buffer ) . '</pre>';
                ?>
            </pre>
        </div>
    </div>


</div>
