<?php
$trp = TRP_Translate_Press::get_trp_instance();
if( !empty( $trp->active_pro_addons ) ){//if we have any Advanced or Pro addons active then show the license key activation form
    ?>
    <div id="trp-license-settings" class="wrap">
        <form method="post" action="<?php echo esc_attr( $action ); ?>">
            <?php settings_fields( 'trp_license_key' ); ?>
            <h1> <?php esc_html_e( 'TranslatePress Settings', 'translatepress-multilingual' );?></h1>
            <?php do_action ( 'trp_settings_navigation_tabs' ); ?>
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row" valign="top">
                        <?php esc_html_e('License Key', 'translatepress-multilingual'); ?>
                    </th>
                    <td>
                        <div>
                            <input id="trp_license_key" name="trp_license_key" type="password" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
                            <?php wp_nonce_field( 'trp_license_nonce', 'trp_license_nonce' ); ?>
                            <?php if( $status !== false && $status == 'valid' ) {
                                $button_name =  'trp_edd_license_deactivate';
                                $button_value = __('Deactivate License', 'translatepress-multilingual' );

                                if( empty( $details['invalid'] ) )
                                    echo '<span title="'. esc_html__( 'Active on this site', 'translatepress-multilingual' ) .'" class="trp-active-license dashicons dashicons-yes"></span>';
                                else
                                    echo '<span title="'. esc_html__( 'Your license is invalid', 'translatepress-multilingual' ) .'" class="trp-invalid-license dashicons dashicons-warning"></span>';

                            }
                            else {
                                $button_name =  'trp_edd_license_activate';
                                $button_value = __('Activate License', 'translatepress-multilingual');
                            }
                            ?>
                            <input type="submit" class="button-secondary" name="<?php echo esc_attr( $button_name ); ?>" value="<?php echo esc_attr( $button_value ); ?>"/>
                        </div>
                        <p class="description">
                            <?php esc_html_e( 'Enter your license key.', 'translatepress-multilingual' ); ?>
                        </p>
                    </td>
                </tbody>
            </table>
        </form>
    </div>
<?php } else{ ?>
    <div id="trp-license-settings" class="wrap" style="margin-bottom: 300px;">
        <h1> <?php esc_html_e( 'TranslatePress Settings', 'translatepress-multilingual' );?></h1>
        <?php do_action ( 'trp_settings_navigation_tabs' ); ?>
        <div class="tp-ai-upsell">
            <h2><img src="<?php echo esc_url( TRP_PLUGIN_URL.'assets/images/' ); ?>ai-icon.svg" width="24" height="24"/> <?php esc_html_e("Seamless, automated & correct translations with TranslatePress AI", 'translatepress-multilingual' ) ?></h2>
            <p><?php esc_html_e("Are you tired of the slow, manual effort of translating your website?", 'translatepress-multilingual') ?></p>
            <p><?php esc_html_e("With TranslatePress AI, experience the future of website translation.", 'translatepress-multilingual') ?></p>
            <ul>
                <li><?php esc_html_e("Automatically translate your entire website", 'translatepress-multilingual') ?></li>
                <li><?php esc_html_e("Accurate and fast translations", 'translatepress-multilingual') ?></li>
                <li><?php esc_html_e("Your message in a language your users understand.", 'translatepress-multilingual') ?></li>
                <li><?php esc_html_e("Extra features from our paid versions: extra languages, SEO support and more...", 'translatepress-multilingual') ?></li>
            </ul>
            <div class="trp-upsell-button">
                <a href="https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=license-page&utm_campaign=tp-ai" class="button" target="_blank"><?php esc_html_e("Get a License Today", 'translatepress-multilingual') ?></a>
            </div>
        </div>
        <div style="margin-left: 20px">
            <p><strong><?php esc_html_e( 'Already purchased a pro version?', 'translatepress-multilingual' ); ?></strong></p>
            <ul>
                <li><?php esc_html_e("Leave the Free version installed.", 'translatepress-multilingual') ?></li>
                <li><?php esc_html_e("Install and activate the Pro version ", 'translatepress-multilingual') ?></li>
                <li><?php esc_html_e("A prompt will appear on this page asking you to enter your license key.", 'translatepress-multilingual') ?></li>
            </ul>
        </div>
    </div>
<?php } ?>