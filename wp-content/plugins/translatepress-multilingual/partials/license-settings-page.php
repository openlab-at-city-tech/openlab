<?php

if ( !defined('ABSPATH' ) )
    exit();

$trp = TRP_Translate_Press::get_trp_instance();

if ( $status !== false && $status == 'valid' ) {
    $button_name  = 'trp_edd_license_deactivate';
    $button_value = __( 'Deactivate License', 'translatepress-multilingual' );
    $button_class = 'trp-button-secondary';
} else {
    $button_name  = 'trp_edd_license_activate';
    $button_value = __( 'Activate License', 'translatepress-multilingual' );
    $button_class = 'trp-submit-btn';
}

$license_message = [
    'valid'   => __( 'Your License Key is valid.', 'translatepress-multilingual' ),
    'invalid' => __( 'Your License Key is invalid.', 'translatepress-multilingual' ),
    'expired' => __( 'Your License has expired.', 'translatepress-multilingual' ),
];

if ( !empty( $details['invalid'] ) ){
    $license_object = $details['invalid'][0];
    $license_status = ( $license_object->error === 'expired' ) ? 'expired' : 'invalid';
}

else
    $license_status = 'valid';

if( !empty( $trp->active_pro_addons ) ){//if we have any Advanced or Pro addons active then show the license key activation form
    ?>
    <div id="trp-settings-page" class="wrap">
        <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

        <form method="post" action="<?php echo esc_attr( $action ); ?>">
            <?php settings_fields( 'trp_license_key' ); ?>
            <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

            <div id="trp-settings__wrap">
                <div class="trp-settings-container">
                    <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'License', 'translatepress-multilingual' ); ?></h3>
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
            </div>
        </form>
    </div>
<?php } else{ ?>
    <div id="trp-settings-page" class="wrap" style="margin-bottom: 300px;">
        <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

        <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

        <div id="trp-settings__wrap" class="trp-license-page-upsell-container">
            <div class="trp-settings-container trp-license-page-upsell-container__left">
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
                    <?php esc_html_e( 'Enjoy seamless, automated and correct translations with TranslatePress AI', 'translatepress-multilingual' ); ?>
                </h3>
                <div class="trp-settings-separator"></div>

                <div class="trp-license-page-upsell-container-content">
                    <span class="trp-primary-text">
                        <?php esc_html_e( 'Are you tired of the slow, manual effort of translating your website? Experience the future of website translation with TranslatePress AI.', 'translatepress-multilingual' ); ?>
                    </span>

                    <div class="trp-license-page-upsell-container-ul">
                        <div class="trp-license-page-upsell-li">
                            <span class="trp-license-icon-check"></span>
                            <span><?php esc_html_e( 'Automatically translate your entire website', 'translatepress-multilingual' ); ?> </span>
                        </div>

                        <div class="trp-license-page-upsell-li">
                            <span class="trp-license-icon-check"></span>
                            <span><?php esc_html_e( 'Accurate and Fast Translations', 'translatepress-multilingual' ); ?> </span>
                        </div>

                        <div class="trp-license-page-upsell-li">
                            <span class="trp-license-icon-check"></span>
                            <span><?php esc_html_e( 'Your message in a language your users understand', 'translatepress-multilingual' ); ?> </span>
                        </div>

                        <div class="trp-license-page-upsell-li">
                            <span class="trp-license-icon-check"></span>
                            <span><?php esc_html_e( 'Extra Features from the paid versions: Extra languages, SEO support and much more...', 'translatepress-multilingual' ); ?> </span>
                        </div>
                    </div>

                    <div>
                        <a href="https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=license-page&utm_campaign=tp-ai" class="trp-submit-btn" target="_blank"><?php esc_html_e("Get a License Today", 'translatepress-multilingual') ?></a>
                    </div>
                </div>
            </div>

            <div class="trp-settings-container trp-license-page-upsell-container__right">
                <div class="trp-license-page-upsell-container-content">
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
        </div>
    </div>
<?php } ?>