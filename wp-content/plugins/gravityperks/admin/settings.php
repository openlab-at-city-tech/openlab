<?php

class GWPerksSettings {

    public static function settings_page() {

        $settings = get_site_option('gwp_settings') ? get_site_option('gwp_settings') : array();
        $is_submit = gwpost('gwp_settings_submit') == true;

        if( gwpost( 'uninstall' ) ) {

            // uninstall functionality...

        } else if( gwpost( 'gwp_settings_submit' ) ) {

            check_admin_referer( 'update', 'gwp_settings' );

            $settings = array_merge( $settings, array(
                'license_key' => trim( stripslashes( $_POST['gwp_license_key'] ) )
            ) );

            update_site_option( 'gwp_settings', $settings );

            GWPerks::flush_license();
            
        }
        
        $is_license_valid = gwar( $settings, 'license_key' ) && GWPerks::has_valid_license();

        ?>

        <?php if( $is_submit && gwget('register') && $is_license_valid ): ?>
            <div class="updated"><p>
                <?php printf( __('Awesome! Your now have unlimited access to <strong>all perks</strong> with automatic upgrades and support. %sLet\'s go install some perks!%s', 'gravityperks'), '<a href="' . GW_MANAGE_PERKS_URL . '&view=install">', '</a>' ); ?>
            </p></div>
        <?php elseif( $is_submit && ! $is_license_valid ): ?>
            <div class="error"><p>
                <?php _e('Oops! That doesn\'t appear to be a valid license.', 'gravityperks'); ?>
            </p></div>
        <?php endif; ?>

        <style type="text/css">
            .gform_tab_content input,
            .gform_tab_content textarea {
                outline-style: none;
                font-family: inherit;
                font-size: inherit;
                padding: 3px 5px;
            }
        </style>

        <form method="post" action="">

            <?php wp_nonce_field('update', 'gwp_settings'); ?>

            <h3><?php _e('Gravity Perks Settings', 'gravityperks'); ?></h3>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gwp_license_key"><?php _e('Gravity Perks License Key', 'gravityperks'); ?></label></th>
                    <td>
                        <input type="password" id="gwp_license_key" name="gwp_license_key" value="<?php echo !gwar($settings, 'license_key') ? '' : trim(esc_attr(gwar($settings, 'license_key'))); ?>" size="50" />
                        <?php
                        if(gwar($settings, 'license_key')):
                            if( $is_license_valid ): ?>

                                <?php if( ! version_compare( GFCommon::$version, '1.8', '>=' ) ): ?>
                                    <img src="<?php echo GFCommon::get_base_url(); ?>/images/tick.png" style="position:relative;top:2px" />
                                <?php else: ?>
                                    <i class="fa fa-check gf_keystatus_valid"></i>
                                <?php endif; ?>

                            <?php else: ?>

                                <?php if( ! version_compare( GFCommon::$version, '1.8', '>=' ) ): ?>
                                    <img src="<?php echo GFCommon::get_base_url(); ?>/images/cross.png" style="position:relative;top:2px" />
                                <?php else: ?>
                                    <i class="fa fa-times gf_keystatus_invalid"></i>
                                <?php endif; ?>

                            <?php endif;
                        endif; ?>

                        <p class="description" style="padding-top:0;">
                            <?php if( ! $is_license_valid ): ?>
                                <?php _e( 'Register your copy of Gravity Perks for unlimited access to all perks, automatic upgrades and support!', 'gravityperks' ); ?>
                            <?php else: ?>
                                <?php _e( 'Awesome! You have unlimited access to <strong>all perks</strong> with automatic upgrades and support.', 'gravityperks' ); ?>
                            <?php endif; ?>
                        </p>

                    </td>
                </tr>
                <tr>
                    <td colspan="2" >
                        <input type="submit" name="gwp_settings_submit" class="button-primary" value="<?php _e('Save Settings', 'gravityperks') ?>" />
                    </td>
                </tr>
            </table>

        </form>

        <?php
    }

}