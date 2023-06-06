<div id="trp-main-settings" class="wrap">
    <form method="post" action="options.php">
        <?php settings_fields( 'trp_machine_translation_settings' ); ?>
        <h1> <?php esc_html_e( 'TranslatePress Automatic Translation', 'translatepress-multilingual' );?></h1>
        <?php
        do_action ( 'trp_settings_navigation_tabs' );
        $free_version = !class_exists( 'TRP_Handle_Included_Addons' );
        $seo_pack_active = class_exists( 'TRP_IN_Seo_Pack');
        ?>

        <table id="trp-options" class="form-table trp-machine-translation-options">
            <tr>
                <th scope="row"><?php esc_html_e( 'Enable Automatic Translation', 'translatepress-multilingual' ); ?> </th>
                <td>
                    <select id="trp-machine-translation-enabled" name="trp_machine_translation_settings[machine-translation]" class="trp-select">
                        <option value="no" <?php selected( $this->settings['trp_machine_translation_settings']['machine-translation'], 'no' ); ?>><?php esc_html_e( 'No', 'translatepress-multilingual') ?></option>
                        <option value="yes" <?php selected( $this->settings['trp_machine_translation_settings']['machine-translation'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'translatepress-multilingual') ?></option>
                    </select>

                    <p class="description">
                        <?php esc_html_e( 'Enable or disable the automatic translation of the site. To minimize translation costs, each untranslated string is automatically translated only once, then stored in the database.', 'translatepress-multilingual' ) ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Translation Engine', 'translatepress-multilingual' ); ?> </th>
                <td>
                    <?php $translation_engines = apply_filters( 'trp_machine_translation_engines', array() ); ?>

                    <?php foreach( $translation_engines as $engine ) : ?>
                        <label for="trp-translation-engine-<?= esc_attr( $engine['value'] ) ?>" style="margin-right:10px;">
                             <input type="radio" class="trp-translation-engine trp-radio" id="trp-translation-engine-<?= esc_attr( $engine['value'] ) ?>" name="trp_machine_translation_settings[translation-engine]" value="<?= esc_attr( $engine['value'] ) ?>" <?php checked( $this->settings['trp_machine_translation_settings']['translation-engine'], $engine['value'] ); ?>>
                             <?php echo esc_html( $engine['label'] ) ?>
                        </label>
                    <?php endforeach; ?>

                    <p class="description">
                        <?php esc_html_e( 'Choose which engine you want to use in order to automatically translate your website.', 'translatepress-multilingual' ) ?>
                    </p>
                </td>
            </tr>


            <?php if( !class_exists( 'TRP_DeepL' ) && !class_exists( 'TRP_IN_DeepL' ) ) : ?>
                <tr style="display:none;">
                    <th scope="row"></th>
                    <td>
                        <p class="trp-upsell-multiple-languages" id="trp-upsell-deepl">

                            <?php
                            //link and message in case the user has the free version of TranslatePress
                            if( !class_exists( 'TRP_Handle_Included_Addons' ) ):
                                $url = trp_add_affiliate_id_to_link('https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=deepl_upsell&utm_campaign=tpfree');
                                $message = __( '<strong>DeepL</strong> automatic translation is available as a <a href="%1$s" target="_blank" title="%2$s">%2$s</a>.', 'translatepress-multilingual' );
                                $message_upgrade = __( 'By upgrading you\'ll get access to all paid add-ons, premium support and help fund the future development of TranslatePress.', 'translatepress-multilingual' );
                                ?>
                            <?php
                            //link and message in case the user has the pro version of TranslatePress
                                else:
                                    $url = 'admin.php?page=trp_addons_page' ;
                                $message = __( 'To use <strong>DeepL</strong> for automatic translation, activate this Pro add-on from the <a href="%1$s" target="_self" title="%2$s">%2$s</a>.', 'translatepress-multilingual' );
                                $message_upgrade= "";
                                    ?>
                        <?php endif; ?>
                        <?php
                            if(empty($message_upgrade)) {
                                $lnk = sprintf(
                                // Translators: %1$s is the URL to the DeepL add-on. %2$s is the name of the Pro offerings.
                                    $message, esc_url( $url ),
                                    _x( 'Addons tab', 'Verbiage for the DeepL Pro Add-on', 'translatepress-multilingual' )
                                );
                            }else{
                                $lnk = sprintf(
                                // Translators: %1$s is the URL to the DeepL add-on. %2$s is the name of the Pro offerings.
                                    $message, esc_url( $url ),
                                    _x( 'TranslatePress Pro Add-on', 'Verbiage for the DeepL Pro Add-on', 'translatepress-multilingual' )
                                );
                            }

                                if(!empty($message_upgrade)) {
                                    $lnk .= '<br/><br />' . $message_upgrade;
                                }
                                $lnk .= '<br/><br />' . __( 'Please note that DeepL API usage is paid separately. See <a href="https://www.deepl.com/pro.html#developer">DeepL pricing information</a>.', 'translatepress-multilingual' );
                                if(!empty($message_upgrade)) {
                                    $lnk .= sprintf(
                                        '<br /><br />' . '<a href="%1$s" class="button button-primary" target="_blank" title="%2$s">%2$s</a>',
                                        esc_url( $url ),
                                        __( 'TranslatePress Pro Add-ons', 'translatepress-multilingual' )
                                    );
                                }
                                echo wp_kses_post( $lnk ); // Post kses for more generalized output that is more forgiving and has late escaping.
                            ?>
                        </p>
                    </td>
                </tr>
            <?php endif; ?>


            <?php do_action ( 'trp_machine_translation_extra_settings_middle', $this->settings['trp_machine_translation_settings'] ); ?>

            <?php if( !empty( $machine_translator->get_api_key() ) ) : ?>
                <tr id="trp-test-api-key">
                    <th scope="row"></th>
                    <td>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=trp_test_machine_api' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Test API credentials', 'translatepress-multilingual' ); ?></a>
                        <p class="description">
                            <?php esc_html_e( 'Click here to check if the selected translation engine is configured correctly.', 'translatepress-multilingual' ) ?>
                        </p>
                    </td>
                </tr>
            <?php endif; ?>

            <tr style="border-bottom: 1px solid #ccc;"></tr>

            <tr>
                <th scope=row><?php esc_html_e( 'Block Crawlers', 'translatepress-multilingual' ); ?></th>
                <td>
                    <label>
                        <input type=checkbox name="trp_machine_translation_settings[block-crawlers]" value="yes" <?php isset( $this->settings['trp_machine_translation_settings']['block-crawlers'] ) ? checked( $this->settings['trp_machine_translation_settings']['block-crawlers'], 'yes' ) : checked( '', 'yes' ); ?>>
                        <?php esc_html_e( 'Yes' , 'translatepress-multilingual' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( 'Block crawlers from triggering automatic translations on your website.', 'translatepress-multilingual' ); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope=row><?php esc_html_e( 'Automatically Translate Slugs', 'translatepress-multilingual' ); ?></th>
                <td>
                    <label>
                        <?php
                        $is_disabled = '';
                        //link and message in case the user has the free version of TranslatePress
                        if( $free_version || !$seo_pack_active ){
                            $is_disabled = 'disabled';
                        }
                        ?>
                        <input type=checkbox name="trp_machine_translation_settings[automatically-translate-slug]" value="yes" <?php ( isset( $this->settings['trp_machine_translation_settings']['automatically-translate-slug'] ) && !$free_version && $seo_pack_active ) ? checked( $this->settings['trp_machine_translation_settings']['automatically-translate-slug'], 'yes' ) : checked( '', 'yes' ); echo $is_disabled //phpcs:ignore ?>>
                        <?php esc_html_e( 'Yes' , 'translatepress-multilingual' ); ?>
                    </label>
                    <p class="description">
                        <?php
                        echo wp_kses( __( 'Generate automatic translations of slugs for posts, pages and Custom Post Types.<br/>The slugs will be automatically translated starting with the second refresh of each page.', 'translatepress-multilingual' ), array( 'br' => array() ) );
                        if( $free_version ){
                            $url = trp_add_affiliate_id_to_link( 'https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=automatically_translate_slugs&utm_campaign=tpfree' );
                            $message = __( 'This feature is available only in the paid version. <a href="%1$s" target="_blank" title="%2$s">%2$s</a> and unlock more premium features.', 'translatepress-multilingual' );
                            $lnk = sprintf(
                                $message, esc_url( $url ),
                                __( 'Upgrade TranslatePress', 'translatepress-multilingual' )
                            );
                            ?>
                            <p class="trp-upsell-auto-translate-slugs">
                                <?php echo wp_kses_post( $lnk ); // Post kses for more generalized output that is more forgiving and has late escaping. ?>
                            </p>
                            <?php
                        }
                        if( !$free_version && !$seo_pack_active ){
                            echo wp_kses( __( '<br/>Requires <a href="https://translatepress.com/docs/addons/seo-pack/" title="TranslatePress Add-on SEO Pack documentation" target="_blank"> SEO Pack Add-on</a> to be installed and activated.', 'translatepress-multilingual' ), array( 'br' => array(), 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ) );
                        }
                        ?>
                    </p>
                </td>
            </tr>

            <tr>
               <th scope="row"><?php esc_html_e( 'Limit machine translation / characters per day', 'translatepress-multilingual' ); ?></th>
               <td>
                   <label>
                       <input type="number" name="trp_machine_translation_settings[machine_translation_limit]" value="<?php echo isset( $this->settings['trp_machine_translation_settings']['machine_translation_limit'] ) ? esc_attr( $this->settings['trp_machine_translation_settings']['machine_translation_limit'] ) : 1000000; ?>">
                   </label>
                   <p class="description">
                       <?php esc_html_e( 'Add a limit to the number of automatically translated characters so you can better budget your project.', 'translatepress-multilingual' ); ?>
                   </p>
               </td>
           </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Today\'s character count:', 'translatepress-multilingual' ); ?></th>
                <td>
                    <strong><?php echo isset( $this->settings['trp_machine_translation_settings']['machine_translation_counter'] ) ? esc_html( $this->settings['trp_machine_translation_settings']['machine_translation_counter'] ) : 0; ?></strong>
                    (<?php echo isset( $this->settings['trp_machine_translation_settings']['machine_translation_counter_date'] ) ? esc_html( $this->settings['trp_machine_translation_settings']['machine_translation_counter_date'] ) : esc_html( date('Y-m-d') ); ?>)
                </td>
            </tr>

            <tr>
               <th scope=row><?php esc_html_e( 'Log machine translation queries.', 'translatepress-multilingual' ); ?></th>
               <td>
                   <label>
                       <input type=checkbox name="trp_machine_translation_settings[machine_translation_log]" value="yes" <?php isset( $this->settings['trp_machine_translation_settings']['machine_translation_log'] ) ? checked( $this->settings['trp_machine_translation_settings']['machine_translation_log'], 'yes' ) : checked( '', 'yes' ); ?>>
                       <?php esc_html_e( 'Yes' , 'translatepress-multilingual' ); ?>
                   </label>
                   <p class="description">
                       <?php echo wp_kses( __( 'Only enable for testing purposes. Can impact performance.<br>All records are stored in the wp_trp_machine_translation_log database table. Use a plugin like <a href="https://wordpress.org/plugins/wp-data-access/" target="_blank">WP Data Access</a> to browse the logs or directly from your database manager (PHPMyAdmin, etc.)', 'translatepress-multilingual' ), array( 'br' => array(), 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ) ); ?>
                   </p>
               </td>
           </tr>

            <?php do_action ( 'trp_machine_translation_extra_settings_bottom', $this->settings['trp_machine_translation_settings'] ); ?>
        </table>

        <p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'translatepress-multilingual' ); ?>" /></p>
    </form>
</div>