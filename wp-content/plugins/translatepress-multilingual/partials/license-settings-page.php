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
    <h1> <?php esc_html_e( 'TranslatePress Settings', 'translatepress-multilingual' );?></h1>
    <?php do_action ( 'trp_settings_navigation_tabs' ); ?>
    <h4><?php printf( __( 'If you purchased a <a href="%s">premium version</a>, first install and activate it. After this you will be prompted with an input to enter your license key.', 'translatepress-multilingual' ), 'https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=license-page&utm_campaign=TRP' ); //phpcs:ignore ?></h4>
<?php } ?>