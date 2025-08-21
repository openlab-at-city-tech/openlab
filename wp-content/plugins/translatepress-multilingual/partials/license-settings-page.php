<?php

if ( !defined('ABSPATH' ) )
    exit();

$license_message = [
    'valid'   => __( 'Your License Key is valid.', 'translatepress-multilingual' ),
    'invalid' => __( 'Your License Key is invalid.', 'translatepress-multilingual' ),
    'expired' => __( 'Your License has expired.', 'translatepress-multilingual' ),
];

if ( !empty( $details['invalid'] ) ){
    $license_object = $details['invalid'][0];
    $license_status = ( $license_object->error === 'expired' ) ? 'expired' : 'invalid';
} elseif ( empty( $details ) ) {
    $license_status = 'invalid';
} else {
    $license_status = 'valid';
}

if ( $license_status == 'valid' ) {
    $button_name  = 'trp_edd_license_deactivate';
    $button_value = __( 'Deactivate License', 'translatepress-multilingual' );
    $button_class = 'trp-button-secondary';
} else {
    $button_name  = 'trp_edd_license_activate';
    $button_value = __( 'Activate License', 'translatepress-multilingual' );
    $button_class = 'trp-submit-btn';
}
    ?>
    <div id="trp-settings-page" class="wrap">
        <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

        <form method="post" action="<?php echo esc_attr( $action ); ?>">
            <?php settings_fields( 'trp_license_key' ); ?>
            <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

            <div id="trp-settings__wrap">

                <div class="trp-license-page-upsell-container">
                    <div class="trp-license-page-upsell-container__left">
                        <div class="trp-settings-container">
                            <h3 class="trp-settings-primary-heading">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle"><path d="M15 21H6C5.20435 21 4.44129 20.6839 3.87868 20.1213C3.31607 19.5587 3 18.7957 3 18V17H13V19C13 19.5304 13.2107 20.0391 13.5858 20.4142C13.9609 20.7893 14.4696 21 15 21ZM15 21C15.5304 21 16.0391 20.7893 16.4142 20.4142C16.7893 20.0391 17 19.5304 17 19V5C17 4.60444 17.1173 4.21776 17.3371 3.88886C17.5568 3.55996 17.8692 3.30362 18.2346 3.15224C18.6001 3.00087 19.0022 2.96126 19.3902 3.03843C19.7781 3.1156 20.1345 3.30608 20.4142 3.58579C20.6939 3.86549 20.8844 4.22186 20.9616 4.60982C21.0387 4.99778 20.9991 5.39992 20.8478 5.76537C20.6964 6.13082 20.44 6.44318 20.1111 6.66294C19.7822 6.8827 19.3956 7 19 7H17M19 3H8C7.20435 3 6.44129 3.31607 5.87868 3.87868C5.31607 4.44129 5 5.20435 5 6V17M9 7H13M9 11H13" stroke="#354052" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php esc_html_e( 'Add a license key', 'translatepress-multilingual' ); ?></h3>
                            <div class="trp-settings-separator"></div>

                            <div class="trp-license__wrapper">
                                <div class="trp-license-left-row">
                                <span class="trp-secondary-text-bold">
                                    <?php esc_html_e('License Key', 'translatepress-multilingual'); ?>
                                </span>
                                </div>

                                <div class="trp-license-right-row">
                                    <div class="trp-license-field-col">
                                        <input id="trp_license_key" name="trp_license_key" type="password" value="<?php echo esc_attr( $license ); ?>" />
                                        <?php wp_nonce_field( 'trp_license_nonce', 'trp_license_nonce' ); ?>
                                        <div class="trp-license-message trp-license-status-<?php echo esc_attr( $license_status ); ?>">
                                            <div class="trp-license-icon"></div>
                                            <span class="trp-license-message-text">
                                            <?php echo isset( $license_message[$license_status] ) ? esc_html( $license_message[$license_status] ) : '' ?>
                                        </span>
                                        </div>

                                        <span class="trp-description-text">
                                        <?php
                                        printf(
                                            esc_html__( 'Manage your license in your %1$s.', 'translatepress-multilingual' ),
                                            '<a href="' . esc_url( 'https://translatepress.com/account/' ) . '" target="_blank">' . esc_html__( 'Account Page', 'translatepress-multilingual' ) . '</a>'
                                        );
                                        ?>
                                    </span>
                                    </div>

                                    <div class="trp-license-action-col">
                                        <input type="submit" class="<?php echo esc_attr( $button_class ); ?>" name="<?php echo esc_attr( $button_name ); ?>" value="<?php echo esc_attr( $button_value ); ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($license_status != 'valid') : ?>
                        <div class="trp-settings-container">
                            <h3 class="trp-settings-primary-heading">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.3609 1.61325C16.124 0.795583 14.8756 0.795583 14.6387 1.61325L14.3034 2.77142C13.5801 5.26682 12.5917 7.21606 9.9063 7.88796L8.66004 8.19983C7.77999 8.41995 7.77999 9.58005 8.66004 9.80017L9.9063 10.112C12.5917 10.7839 13.5801 12.7332 14.3034 15.2286L14.6387 16.3867C14.8756 17.2044 16.124 17.2044 16.3609 16.3867L16.6966 15.2286C17.4195 12.7332 18.4083 10.7839 21.0937 10.112L22.34 9.80017C23.22 9.58005 23.22 8.41995 22.34 8.19983C21.4599 7.97971 21.0937 7.88796 21.0937 7.88796C18.4083 7.21606 17.4195 5.26682 16.6966 2.77142L16.3609 1.61325Z" fill="url(#paint0_linear_58602_3700)"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.01653 13.3833C5.8744 12.8722 5.12534 12.8722 4.98321 13.3833L4.78203 14.1071C4.34806 15.6668 3.75504 16.885 2.14378 17.305L1.39602 17.4999C0.867993 17.6375 0.867993 18.3625 1.39602 18.5001L2.14378 18.695C3.75504 19.115 4.34806 20.3332 4.78203 21.8929L4.98321 22.6167C5.12534 23.1278 5.8744 23.1278 6.01653 22.6167L6.21797 21.8929C6.65168 20.3332 7.24496 19.115 8.85622 18.695L9.60398 18.5001C10.132 18.3625 10.132 17.6375 9.60398 17.4999C9.07595 17.3623 8.85622 17.305 8.85622 17.305C7.24496 16.885 6.65168 15.6668 6.21797 14.1071L6.01653 13.3833Z" fill="url(#paint1_linear_58602_3700)"/>
                                    <defs>
                                        <linearGradient id="paint0_linear_58602_3700" x1="15.36" y1="16.8" x2="23.04" y2="1.44004" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#FF02F0"/>
                                            <stop offset="1" stop-color="#FFC800"/>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_58602_3700" x1="5.28004" y1="23.04" x2="10.08" y2="13.44" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#8930FD"/>
                                            <stop offset="1" stop-color="#49CCF9"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <?php esc_html_e( 'Don’t have a TranslatePress AI License Key?', 'translatepress-multilingual' ); ?>
                            </h3>
                            <div class="trp-settings-separator"></div>

                            <div class="trp-license-page-upsell-container-content">
                                <span class="trp-primary-text">
                                    <?php echo sprintf( esc_html__( 'You can get one for %1$sfree%2$s, by creating a free account. It includes:', 'translatepress-multilingual' ), '<strong>', '</strong>' ); ?>
                                </span>

                                <div class="trp-license-page-upsell-container-ul">
                                    <div class="trp-license-page-upsell-li">
                                        <span class="trp-license-icon-check"></span>
                                        <span><?php esc_html_e( 'Access to TranslatePress AI for instant automatic translations', 'translatepress-multilingual' ); ?> </span>
                                    </div>

                                    <div class="trp-license-page-upsell-li">
                                        <span class="trp-license-icon-check"></span>
                                        <span><?php esc_html_e( '2000 AI words to translate automatically', 'translatepress-multilingual' ); ?> </span>
                                    </div>
                                </div>

                                <div>
                                    <a href="https://translatepress.com/tp-ai-free/?utm_source=wpbackend&utm_medium=clientsite&utm_content=license-page&utm_campaign=tpaifree" class="trp-submit-btn" target="_blank"><?php esc_html_e("Get a free License Today", 'translatepress-multilingual') ?></a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php
                        // Debug information section
                        $force_check_request = get_transient('trp_debug_force_check_license_request');
                        $force_check_response = get_transient('trp_debug_force_check_license_response');
                        $activate_request = get_transient('trp_debug_activate_license_request');
                        $activate_response = get_transient('trp_debug_activate_license_response');

                        if ($force_check_request || $force_check_response || $activate_request || $activate_response) : ?>
                        <div class="trp-settings-container">
                            <h3 class="trp-settings-primary-heading" style="display: flex; align-items: center;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;">
                                    <path d="M12 15C12.5523 15 13 14.5523 13 14C13 13.4477 12.5523 13 12 13C11.4477 13 11 13.4477 11 14C11 14.5523 11.4477 15 12 15Z" fill="#354052"/>
                                    <path d="M12 11V7" stroke="#354052" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" stroke="#354052" stroke-width="2"/>
                                </svg>
                                <?php esc_html_e( 'Debug Information', 'translatepress-multilingual' ); ?>
                            </h3>
                            <div class="trp-settings-separator"></div>
                            
                            <?php if ($force_check_request || $force_check_response) : ?>
                            <details>
                                <summary><?php esc_html_e( 'Debug Data for License Checking', 'translatepress-multilingual' ); ?></summary>
                                
                                <code>
<?php if ($force_check_request) : ?>
=== REQUEST ===
URL: <?php echo esc_html($force_check_request['url']); ?>

Timestamp: <?php echo esc_html($force_check_request['timestamp']); ?>

Parameters:
<?php echo esc_html(print_r($force_check_request['params'], true)); ?>
<?php endif; ?>

<?php if ($force_check_response) : ?>
=== RESPONSE ===
Response Code: <?php echo esc_html($force_check_response['response_code']); ?>

Timestamp: <?php echo esc_html($force_check_response['timestamp']); ?>

Response Body:
<?php echo esc_html(str_replace(['{', '}', ','], ["\n{\n", "\n}\n", ",\n"], $force_check_response['response_body'])); ?>
<?php endif; ?>
                                </code>
                            </details>
                            <?php endif; ?>

                            <?php if ($activate_request || $activate_response) : ?>
                            <details>
                                <summary><?php esc_html_e( 'Debug Data for License Activation', 'translatepress-multilingual' ); ?></summary>
                                
                                <code>
<?php if ($activate_request) : ?>
=== REQUEST ===
URL: <?php echo esc_html($activate_request['url']); ?>

Timestamp: <?php echo esc_html($activate_request['timestamp']); ?>

Parameters:
<?php echo esc_html(print_r($activate_request['params'], true)); ?>
<?php endif; ?>

<?php if ($activate_response) : ?>
=== RESPONSE ===
Response Code: <?php echo esc_html($activate_response['response_code']); ?>

Timestamp: <?php echo esc_html($activate_response['timestamp']); ?>

Response Body:
<?php echo esc_html(str_replace(['{', '}', ','], ["\n{\n", "\n}\n", ",\n"], $activate_response['response_body'])); ?>
<?php endif; ?>
                                </code>
                            </details>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($license_status != 'valid') : ?>
                    <div class="trp-license-page-upsell-container__right">
                        <div class="trp-settings-container trp-license-page-upsell-container-content">
                            <h3 class="trp-settings-secondary-heading">
                                <?php esc_html_e( 'Already purchased a Premium version?', 'translatepress-multilingual' ); ?>
                            </h3>

                            <div class="trp-license-page-upsell-container-ul">
                                <div class="trp-license-page-upsell-li">
                                    <span class="trp-license-icon-arrow"></span>
                                    <span>
                                    <?php
                                    printf(
                                        esc_html__( 'Go to your %1$s', 'translatepress-multilingual' ),
                                        '<a href="' . esc_url( 'https://translatepress.com/account/' ) . '" target="_blank">' . esc_html__( 'TranslatePress.com Account', 'translatepress-multilingual' ) . '</a>'
                                    );
                                    ?>
                                </span>
                                </div>

                                <div class="trp-license-page-upsell-li">
                                    <span class="trp-license-icon-arrow"></span>
                                    <span><?php esc_html_e( 'Download & Install the Pro plugin', 'translatepress-multilingual' ); ?> </span>
                                </div>
                            </div>

                            <div>
                                <a href="https://translatepress.com/docs/installation/" class="trp-button-secondary" target="_blank"><?php esc_html_e("Learn More", 'translatepress-multilingual') ?></a>
                            </div>
                        </div>
                </div>
                    <?php endif; ?>
                </div>

            </div>
        </form>

    </div>