<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>
    <?php require_once TRP_PLUGIN_DIR . 'partials/machine-translation-test-api-popup.php'; ?>

    <form method="post" action="options.php">
        <?php
            settings_fields( 'trp_machine_translation_settings' );
            wp_nonce_field('trp_test_api_nonce', 'trp_test_api_nonce_field');
        ?>

        <?php
            do_action ( 'trp_settings_navigation_tabs' );
            $free_version = !class_exists( 'TRP_Handle_Included_Addons' );
            $seo_pack_active = class_exists( 'TRP_IN_Seo_Pack');
        ?>

        <div id="trp-settings__wrap">
            <div class="trp-settings-container">
                <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Automatic Translation', 'translatepress-multilingual' ); ?></h3>
                <div class="trp-settings-separator"></div>

                <div class="trp-settings-options__wrapper">
                    <div class="trp-settings-options-item trp-settings-switch__wrapper">
                        <span class="trp-primary-text-bold"><?php esc_html_e('Enable Automatic Translation', 'translatepress-multilingual'); ?></span>

                        <div class="trp-switch">
                            <input type="checkbox" id="trp-machine-translation-enabled"
                                   class="trp-switch-input"
                                   name="trp_machine_translation_settings[machine-translation]"
                                   value="yes"
                                <?php checked($this->settings['trp_machine_translation_settings']['machine-translation'], 'yes'); ?> />

                            <label for="trp-machine-translation-enabled" class="trp-switch-label"></label>
                        </div>
                    </div>
                    <div class="trp-to-hide">
                        <?php do_action ( 'trp_machine_translation_extra_settings_middle', $this->settings['trp_machine_translation_settings'] ); ?>
                    </div>

                    <?php if( !class_exists( 'TRP_DeepL' ) && !class_exists( 'TRP_IN_DeepL' ) ) : ?>
                        <div class="trp-engine trp-engine trp-automatic-translation-engine__container trp-to-hide" id="deepl_upsell">
                            <p class="trp-upsell-multiple-languages trp-primary-text" id="trp-upsell-deepl">

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
                        </div>
                    <?php endif; ?>

                    <div class="trp-automatic-translation-extra-lower trp-to-hide">
                        <?php if( !empty( $machine_translator->get_api_key() ) ) : ?>
                            <div id="trp-test-api-key">
                                <button type="button" class="trp-button-secondary">
                                    <?php esc_html_e( 'Test API credentials', 'translatepress-multilingual' ); ?>
                                </button>

                                <span class="trp-description-text">
                                    <?php esc_html_e( 'Check if the selected translation engine is configured correctly.', 'translatepress-multilingual' ) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div id="alternative-engines" class="trp-to-hide trp-settings-options-item">
                            <span class="trp-primary-text-bold"><?php esc_html_e( 'Alternative Engines', 'translatepress-multilingual' ); ?> </span>
                            <div class="trp-alternative-engines-select__wrapper">
                                <?php
                                // empty array, since all translations engines are added through this filter.
                                $translation_engines = apply_filters( 'trp_machine_translation_engines', array() );
                                ?>
                                <?php if($this->settings['trp_machine_translation_settings']['translation-engine'] == 'mtapi'): ?>
                                <details>
                                    <summary> <?php echo '<span class="trp-primary-text">' . esc_html__("More info", "translatepress-multilingual") . '</span>'; ?> </summary>
                                    <?php else: ?>
                                        <div class='tp-ai-upsell'>
                                            <div class="trp-ai-upsell-body">
                                                <img src="<?php echo esc_url( TRP_PLUGIN_URL.'assets/images/'); ?>ai-icon.svg" width="24" height="24"/>
                                                <div class="trp-ai-upsell-body__content">
                                                    <span class="trp-settings-secondary-heading">
                                                        <?php esc_html_e("Switch to TranslatePress AI", "translatepress-multilingual"); ?>
                                                    </span>
                                                    <span class="trp-primary-text">
                                                        <?php esc_html_e("Integrate machine translation directly with your WordPress website.", "translatepress-multilingual"); ?>
                                                        <a href="https://translatepress.com/ai/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettingsAT&utm_campaign=tp-ai" target="_blank">
                                                            <span class="trp-upsell-button">
                                                                <span><?php esc_html_e("Learn More", "translatepress-multilingual"); ?></span>
                                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M17 7.3252L7 17.3252M17 7.3252H8M17 7.3252V16.3252" stroke="#354052" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="trp-ai-upsell-arrow">
                                                <svg width="59" height="53" viewBox="0 0 59 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.5273 43.08C35.399 35.8285 45.8995 19.1128 45.6418 1.6843C45.6166 -0.0196409 42.9806 0.30536 43.0058 2.00161C43.2451 18.1842 33.2461 33.8966 18.5432 40.6141C16.9951 41.3215 17.9673 43.7928 19.5273 43.08Z" fill="#419DE0"/>
                                                    <path d="M15.2928 38.8925C16.3153 41.3252 17.338 43.758 18.3607 46.1906C18.8374 45.5891 19.314 44.9876 19.7909 44.386C16.5342 44.2247 13.2943 44.3613 10.0685 44.8422C10.5323 45.1176 10.9962 45.3928 11.46 45.6682C11.4355 45.5744 11.4109 45.4805 11.3863 45.3866C11.3797 45.7303 11.3732 46.0742 11.3666 46.4179C11.591 45.8447 12.2628 45.3535 12.6801 44.9135C13.2623 44.3 13.8445 43.6863 14.4266 43.0728C15.5264 41.9138 16.626 40.7547 17.7258 39.5955C17.0366 39.0403 16.3474 38.4851 15.6582 37.9299C14.2781 39.9422 14.5432 42.4082 16.337 44.069C16.6803 43.287 17.0237 42.5048 17.3671 41.7228C16.3038 41.4852 15.2406 41.2473 14.1774 41.0097C13.4759 40.8529 12.7693 41.4545 12.6676 42.125C12.4926 43.2792 12.8814 44.3171 13.854 44.9847C14.3896 45.3523 14.9305 45.3412 15.4754 45.0185C15.9913 44.713 16.4907 44.515 17.0542 44.3259C18.6742 43.7825 17.688 41.3175 16.0699 41.8601C15.2929 42.1207 14.6005 42.4675 13.8982 42.8834C14.4387 42.8947 14.9791 42.9059 15.5196 42.9172C15.4439 42.7847 15.3683 42.6519 15.2925 42.5193C14.7894 42.891 14.2861 43.2626 13.7828 43.6346C14.8458 43.8723 15.9091 44.1099 16.9724 44.3477C18.3543 44.6565 18.8896 42.8227 18.0025 42.0015C17.3303 41.379 17.204 40.3564 17.7258 39.5955C18.6967 38.18 16.8357 36.6889 15.6582 37.9299C14.1705 39.498 12.6824 41.0659 11.1946 42.634C10.0705 43.8189 8.52587 44.8645 8.99414 46.652C9.13716 47.1975 9.85937 47.5565 10.3857 47.478C13.3731 47.0327 16.3786 46.8613 19.3963 47.0109C20.3558 47.0583 21.2485 46.2102 20.8265 45.2063C19.8039 42.7736 18.7811 40.3409 17.7585 37.9082C17.0973 36.3357 14.6282 37.3119 15.2928 38.8925Z" fill="#419DE0"/>
                                                </svg>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <select id="trp-translation-engines" class="trp-select" name="trp_machine_translation_settings[translation-engine]">
                                        <?php foreach( $translation_engines as $engine ) : ?>
                                            <option class="trp-translation-engine" id="trp-translation-engine-<?= esc_attr( $engine['value'] ) ?>" name="trp_machine_translation_settings[translation-engine]" value="<?= esc_attr( $engine['value'] ) ?>" <?php selected( $this->settings['trp_machine_translation_settings']['translation-engine'], $engine['value'] ); ?>> <?php echo esc_html($engine['label']); ?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="trp-description-text">
                                        <?php
                                            echo wp_kses_post(
                                                sprintf(
                                                /* Translators: The <br> ensures a line break after "order to" */
                                                    esc_html__('Choose which engine you want to use in order to %1$s automatically translate your website.', 'translatepress-multilingual'),
                                                    '<br>'
                                                )
                                            );
                                        ?>
                                    </span>
                                    <?php if($this->settings['trp_machine_translation_settings']['translation-engine'] == 'mtapi'): ?>
                                </details>
                                    <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trp-settings-container trp-to-hide">
                <h3 class="trp-settings-primary-heading"><?php esc_html_e('Automatic Translation Settings', 'translatepress-multilingual'); ?></h3>
                <div class="trp-settings-separator"></div>

                <div class="trp-settings-options__wrapper">
                    <!-- Block Crawlers -->
                    <div class="trp-settings-options-item trp-settings-checkbox">
                        <input type="checkbox" id="trp-block-crawlers"
                               name="trp_machine_translation_settings[block-crawlers]"
                               value="yes"
                            <?php isset($this->settings['trp_machine_translation_settings']['block-crawlers'])
                                ? checked($this->settings['trp_machine_translation_settings']['block-crawlers'], 'yes')
                                : checked('', 'yes'); ?> />
                        <label for="trp-block-crawlers" class="trp-checkbox-label">
                            <div class="trp-checkbox-content">
                                <span class="trp-primary-text-bold"><?php esc_html_e('Block Crawlers', 'translatepress-multilingual'); ?></span>
                                <span class="trp-description-text">
                                    <?php echo wp_kses(
                                        __('Block crawlers from triggering automatic translations on your website.<br>This will not prevent crawlers from accessing this site\'s pages.',
                                            'translatepress-multilingual'), ['br' => []]
                                    ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <?php
                        $is_disabled = '';

                        if ( $free_version || !$seo_pack_active )
                            $is_disabled = 'disabled';
                    ?>

                    <!-- Automatically Translate Slugs -->
                    <div class="trp-settings-options-item trp-settings-checkbox <?php if ( $is_disabled ) echo 'trp-settings-checkbox__disabled'; ?>">
                        <input type="checkbox" id="trp-auto-translate-slugs"
                               name="trp_machine_translation_settings[automatically-translate-slug]"
                               value="yes"
                            <?php (isset($this->settings['trp_machine_translation_settings']['automatically-translate-slug'])
                                && !$free_version && $seo_pack_active)
                                ? checked($this->settings['trp_machine_translation_settings']['automatically-translate-slug'], 'yes')
                                : checked('', 'yes');
                            echo esc_attr($is_disabled); ?> />

                        <label for="trp-auto-translate-slugs" class="trp-checkbox-label">
                            <div class="trp-checkbox-content">
                                <b class="trp-primary-text-bold"><?php esc_html_e('Automatically Translate Slugs', 'translatepress-multilingual'); ?></b>
                                <span class="trp-description-text">
                                    <?php
                                        echo wp_kses(
                                            __('Generate automatic translations of slugs for posts, pages and Custom Post Types.<br/>The slugs will be automatically translated starting with the second refresh of each page.',
                                                'translatepress-multilingual'), ['br' => []]
                                        );
                                    ?>
                                </span>
                                <?php
                                if ( $is_disabled ) {
                                    $upgrade_url = esc_url(trp_add_affiliate_id_to_link('https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=automatically_translate_slugs&utm_campaign=tpfree'));
                                    $seo_pack_url = esc_url('https://translatepress.com/docs/addons/seo-pack/');

                                    $is_free_version = $free_version;
                                    $message = $is_free_version
                                        ? __('This feature is only available in the paid version. Upgrade TranslatePress and unlock more premium features.', 'translatepress-multilingual')
                                        : sprintf(
                                            __('Requires <a href="%s" title="TranslatePress Add-on SEO Pack documentation" target="_blank">SEO Pack Add-on</a> to be installed and activated.', 'translatepress-multilingual'),
                                            $seo_pack_url
                                        );
                                ?>

                                <div class="trp-upgrade-notice-at__wrapper">
                                    <div class="trp-upgrade-notice">
                                        <span class="trp-upgrade-notice-text">
                                            <?php echo wp_kses(
                                                $message,
                                                ['a' => ['href' => [], 'title' => [], 'target' => []]]
                                            ); ?>
                                        </span>

                                        <?php if ($is_free_version) : ?>
                                            <a href="<?php echo $upgrade_url; // phpcs:ignore ?>">
                                                <span class="trp-upgrade-notice-button">
                                                    <span><?php esc_html_e('Upgrade now', 'translatepress-multilingual'); ?></span>
                                                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17 7.3252L7 17.3252M17 7.3252H8M17 7.3252V16.3252" stroke="#354052" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </label>
                    </div>

                    <!-- Limit Machine Translation Per Day -->
                    <div class="trp-settings-options-item trp-settings-machine-translation-limit__wrapper">
                        <div class="trp-settings-checkbox">
                            <input type="checkbox" id="trp-machine-translation-limit-toggle"
                                   name="trp_machine_translation_settings[machine_translation_limit_enabled]"
                                   value="yes"
                                <?php checked( ( isset($this->settings['trp_machine_translation_settings']['machine_translation_limit_enabled'])
                                    && $this->settings['trp_machine_translation_settings']['machine_translation_limit_enabled'] === 'yes' ) || ( !empty( $this->settings['trp_machine_translation_settings']['machine_translation_limit'] ) ) ); ?> />

                            <label for="trp-machine-translation-limit-toggle" class="trp-checkbox-label">
                                <div class="trp-checkbox-content">
                                    <span class="trp-primary-text-bold"><?php esc_html_e('Limit machine translation / characters per day', 'translatepress-multilingual'); ?></span>

                                    <span class="trp-description-text">
                                    <?php esc_html_e('Add a limit to the number of automatically translated characters so you can better budget your project.', 'translatepress-multilingual'); ?>
                                </span>
                                </div>
                            </label>
                        </div>

                        <?php
                            $trp = TRP_Translate_Press::get_trp_instance();
                            $machine_translator_logger = $trp->get_component('machine_translator_logger');

                            $today_count = $machine_translator_logger->get_todays_character_count();
                        ?>

                        <div class="trp-machine-translation-per-day__wrapper">
                            <input type="number" id="trp-machine-translation-limit"
                                   name="trp_machine_translation_settings[machine_translation_limit]"
                                   value="<?php echo isset($this->settings['trp_machine_translation_settings']['machine_translation_limit'])
                                       ? esc_attr($this->settings['trp_machine_translation_settings']['machine_translation_limit'])
                                       : 1000000; ?>"
                                <?php echo (isset($this->settings['trp_machine_translation_settings']['machine_translation_limit_enabled'])
                                    && $this->settings['trp_machine_translation_settings']['machine_translation_limit_enabled'] === 'yes')
                                    ? '' : 'disabled'; ?> />

                            <span class="trp-secondary-text"><?php esc_html_e('characters per day', 'translatepress-multilingual'); ?></span>

                            <div class="trp-machine-translation-per-day-count-pill">
                                <span class="trp-primary-text"><?php esc_html_e("Today's Character Count: ", 'translatepress-multilingual'); ?></span>
                                <strong>
                                    <?php echo esc_html( $today_count . ' / ' . number_format( isset($this->settings['trp_machine_translation_settings']['machine_translation_limit'])
                                       ? (int) $this->settings['trp_machine_translation_settings']['machine_translation_limit']
                                       : 1000000 ) ); ?>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Log Machine Translation Queries -->
                    <div class="trp-settings-options-item trp-settings-checkbox">
                        <input type="checkbox" id="trp-log-machine-translations"
                               name="trp_machine_translation_settings[machine_translation_log]"
                               value="yes"
                            <?php isset($this->settings['trp_machine_translation_settings']['machine_translation_log'])
                                ? checked($this->settings['trp_machine_translation_settings']['machine_translation_log'], 'yes')
                                : checked('', 'yes'); ?> />
                        <label for="trp-log-machine-translations" class="trp-checkbox-label">
                            <div class="trp-checkbox-content">
                                <b class="trp-primary-text-bold"><?php esc_html_e('Log machine translation queries.', 'translatepress-multilingual'); ?></b>
                                <span class="trp-description-text">
                                <?php
                                    echo wp_kses(
                                        __('Only enable for testing purposes. Can impact performance.<br>All records are stored in the wp_trp_machine_translation_log database table. Use a plugin like <a href="https://wordpress.org/plugins/wp-data-access/" target="_blank">WP Data Access</a> to browse the logs or directly from your database manager (PHPMyAdmin, etc.)',
                                            'translatepress-multilingual'),
                                        ['br' => [], 'a' => ['href' => [], 'target' => [], 'title' => []]]
                                    );
                                ?>
                                </span>
                            </div>
                        </label>
                    </div>

                </div> <!-- End of Wrapper -->
            </div> <!-- End of Settings Container -->

            <div class="trp-to-hide">
                <?php do_action ( 'trp_machine_translation_extra_settings_bottom', $this->settings['trp_machine_translation_settings'] ); ?>
            </div>

            <button type="submit" class="trp-submit-btn">
                <?php esc_html_e( 'Save Changes', 'translatepress-multilingual' ); ?>
            </button>
        </div>
    </form>
</div>