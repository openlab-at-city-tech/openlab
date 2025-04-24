<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="trp-settings-page" class="wrap trp-main-settings">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php' ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'trp_settings' ); ?>
        <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

        <div id="trp-settings__wrap">
            <div class="trp-settings-container trp-settings-container-languages">
                <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Website Languages', 'translatepress-multilingual' ); ?></h3>
                <div class="trp-settings-separator"></div>

                <div class="trp-default-language__container">
                    <p class="trp-default-language-label trp-primary-text-bold"><?php esc_html_e( 'Default Language', 'translatepress-multilingual' ); ?></p>
                    <div class="trp-default-language-select__wrapper">
                        <select id="trp-default-language" name="trp_settings[default-language]" class="trp-select2">
                            <?php
                            foreach( $languages as $language_code => $language_name ){ ?>
                                <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>" <?php echo ( $this->settings['default-language'] == $language_code ? 'selected' : '' ); ?> >
                                    <?php echo esc_html( $language_name ); ?>
                                </option>
                            <?php }?>
                        </select>
                        <span class="trp-description-text"><?php esc_html_e( 'Select the original language of your content.', 'translatepress-multilingual' ); ?></span>
                    </div>
                </div>

                <p class="trp-settings-warning" style="display: none;" >
                    <?php esc_html_e( 'WARNING. Changing the default language will invalidate existing translations.', 'translatepress-multilingual' ); ?><br/>
                    <?php esc_html_e( 'Even changing from en_US to en_GB, because they are treated as two different languages.', 'translatepress-multilingual' ); ?><br/>
                    <?php esc_html_e( 'In most cases changing the default flag is all it is needed: ', 'translatepress-multilingual' ); ?>
                    <a href="https://translatepress.com/docs/developers/replace-default-flags/"><?php esc_html_e( 'replace the default flag', 'translatepress-multilingual' ); ?></a>
                </p>

                <?php do_action( 'trp_language_selector', $languages ); ?>

            </div>

            <div class="trp-settings-container">
                <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Language Settings', 'translatepress-multilingual' ); ?></h3>
                <div class="trp-settings-separator"></div>

                <div class="trp-settings-options__wrapper">
                    <div class="trp-settings-checkbox trp-settings-options-item">
                        <input type="checkbox" id="trp-native-language-name" name="trp_settings[native_or_english_name]"
                               value="native_name" <?php checked( $this->settings['native_or_english_name'], 'native_name' ); ?> />

                        <label for="trp-native-language-name" class="trp-checkbox-label">
                            <div class="trp-checkbox-content">
                                <span class="trp-primary-text-bold"><?php esc_html_e('Use Native language name', 'translatepress-multilingual'); ?></span>
                                <span class="trp-description-text"><?php esc_html_e('Check if you want to display languages in their native names. Otherwise, languages will be displayed in English.', 'translatepress-multilingual'); ?></span>
                            </div>
                        </label>
                    </div>

                    <div class="trp-settings-checkbox trp-settings-options-item">
                        <input type="checkbox" id="trp-subdirectory-for-default-language" name="trp_settings[add-subdirectory-to-default-language]"
                               value="yes" <?php checked($this->settings['add-subdirectory-to-default-language'], 'yes'); ?> />

                        <label for="trp-subdirectory-for-default-language" class="trp-checkbox-label">
                            <div class="trp-checkbox-content">
                                <span class="trp-primary-text-bold"><?php esc_html_e('Use a subdirectory for the default language', 'translatepress-multilingual'); ?></span>
                                <span class="trp-description-text">
                                    <?php echo wp_kses( __( 'Check if you want to add the subdirectory in the URL for the default language.</br>By checking this option, the default language seen by website visitors will become the first one in the "All Languages" list.', 'translatepress-multilingual' ), array( 'br' => array() ) ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="trp-settings-checkbox trp-settings-options-item">
                        <input type="checkbox" id="trp-force-language-in-custom-links" name="trp_settings[force-language-to-custom-links]"
                               value="yes" <?php checked($this->settings['force-language-to-custom-links'], 'yes'); ?> />

                        <label for="trp-force-language-in-custom-links" class="trp-checkbox-label">
                            <div class="trp-checkbox-content">
                                <span class="trp-primary-text-bold"><?php esc_html_e('Force language in custom links', 'translatepress-multilingual'); ?></span>
                                <span class="trp-description-text">
                                    <?php esc_html_e( 'Select Yes if you want to force custom links without language encoding to keep the currently selected language.', 'translatepress-multilingual' ); ?>
                                </span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="trp-settings-container">
                <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Language Switcher', 'translatepress-multilingual' ); ?></h3>
                <div class="trp-settings-separator"></div>

                <div class="trp-settings-options__wrapper">
                    <div class="trp-settings-options-item trp-settings-checkbox">
                        <input type="checkbox" disabled checked id="trp-ls-shortcode" >

                        <label>
                            <div class="trp-checkbox-content">
                                <b class="trp-primary-text-bold"><?php esc_html_e( 'Shortcode ', 'translatepress-multilingual' ); ?>[language-switcher] </b>
                                <?php $this->output_language_switcher_select( 'shortcode-options', $this->settings['shortcode-options'] ); ?>

                                <span class="trp-description-text">
                                    <?php esc_html_e( 'Use shortcode on any page or widget.', 'translatepress-multilingual' ); ?>
                                    <?php echo wp_kses_post( sprintf( __('You can also add the <a href="%s" title="Language Switcher Block Documentation">Language Switcher Block</a> in the WP Gutenberg Editor.', 'translatepress-multilingual'), esc_url('https://translatepress.com/docs/settings/#language-switcher-block' ) ) ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="trp-settings-options-item trp-settings-checkbox">
                        <input type="checkbox" id="trp-ls-menu" disabled checked >

                        <label>
                            <div class="trp-checkbox-content">
                                <b class="trp-primary-text-bold"><?php esc_html_e( 'Menu item', 'translatepress-multilingual' ); ?></b>
                                <?php $this->output_language_switcher_select( 'menu-options', $this->settings['menu-options'] ); ?>
                                <span class="trp-description-text">
                                    <?php
                                    $link_start = '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) .'">';
                                    $link_end = '</a>';
                                    printf( wp_kses( __( 'Go to  %1$s Appearance -> Menus%2$s to add languages to the Language Switcher in any menu.', 'translatepress-multilingual' ), [ 'a' => [ 'href' => [] ] ] ), $link_start, $link_end ); //phpcs:ignore ?>
                                    <a href="https://translatepress.com/docs/settings/#language-switcher" target="_blank"><?php esc_html_e( 'Learn more in our documentation.', 'translatepress-multilingual' ); ?></a>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="trp-settings-options-item trp-settings-checkbox">
                        <input type="checkbox" id="trp-ls-floater" name="trp_settings[trp-ls-floater]" value="yes" <?php if ( isset($this->settings['trp-ls-floater']) && ( $this->settings['trp-ls-floater'] == 'yes' ) ){ echo 'checked'; }  ?>>

                        <label>
                            <div class="trp-checkbox-content">
                                <b class="trp-primary-text-bold"><?php esc_html_e( 'Floating language selection', 'translatepress-multilingual' ); ?></b>
                                <?php $this->output_language_switcher_select( 'floater-options', $this->settings['floater-options'] ); ?>
                                <?php $this->output_language_switcher_floater_color( $this->settings['floater-color'] ); ?>
                                <?php $this->output_language_switcher_floater_possition( $this->settings['floater-position'] ); ?>
                                <span class="trp-description-text">
                                    <?php esc_html_e( 'Add a floating dropdown that follows the user on every page.', 'translatepress-multilingual' ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="trp-settings-options-item trp-settings-checkbox">
                        <input type="checkbox" id="trp-ls-show-poweredby" name="trp_settings[trp-ls-show-poweredby]"  value="yes"  <?php if ( isset($this->settings['trp-ls-show-poweredby']) && ( $this->settings['trp-ls-show-poweredby'] == 'yes' ) ){ echo 'checked'; }  ?>>

                        <label>
                            <div class="trp-checkbox-content">
                                <b class="trp-primary-text-bold"><?php esc_html_e( 'Show "Powered by TranslatePress"', 'translatepress-multilingual' ); ?></b>
                                <span class="trp-description-text">
                                    <?php esc_html_e( 'Show the small "Powered by TranslatePress" label in the floater language switcher.', 'translatepress-multilingual' ); ?>
                                </span>
                            </div>
                        </label>
                    </div>

                    <?php do_action ( 'trp_extra_settings', $this->settings ); ?>
                </div>
            </div>

            <?php
            $email_course_dismissed = get_user_meta( get_current_user_id(), 'trp_email_course_dismissed', true );

            if( ( empty( $email_course_dismissed ) || $email_course_dismissed != '1' ) && false ) : ?>
                <div class="trp-email-course">
                    <div class="trp-email-course__content">
                        <h2>
                            <?php esc_html_e( '5 Days to Better Multilingual Websites', 'translatepress-multilingual' ); ?>
                        </h2>

                        <p>
                            <?php printf( esc_html__( '%sJoin our FREE & EXCLUSIVE onboarding course%s and learn how to grow your multilingual traffic, reach international markets, and save time & money while getting the most out of TranslatePress!', 'translatepress-multilingual' ), '<strong>', '</strong>' ); ?>
                        </p>

                        <div class="trp-email-course__message"></div>

                        <div class="trp-email-course__form">
                            <div class="trp-email-course__error"><?php esc_html_e( 'Invalid email address', 'translatepress-multilingual' ) ?></div>

                            <input type="email" name="trp_email_course_email" placeholder="<?php esc_html_e( 'Your email', 'translatepress-multilingual' ) ?>" value=""/>
                            <input type="hidden" name="trp_installed_plugin_version" value="<?php echo esc_attr( TRP_Plugin_Optin::get_current_active_version() ); ?>" />

                            <input type="submit" class="button-primary" value="<?php esc_attr_e( 'Sign me up!', 'translatepress-multilingual' ); ?>"/>
                        </div>

                        <p class="trp-email-course__footer">
                            <?php esc_html_e( 'Sign up with your email address and receive a 5-part email guide to help you maximize the power of TranslatePress.', 'translatepress-multilingual' ); ?>
                        </p>

                        <!-- <a class="trp-email-course__close" href="#dismiss-email-course" title="<?php esc_html_e( 'Dismiss email course notification', 'translatepress-multilingual') ?>"></a> -->
                    </div>
                </div>
            <?php endif; ?>
            <button type="submit" class="trp-submit-btn">
                <?php esc_html_e( 'Save Changes', 'translatepress-multilingual' ); ?>
            </button>

        </div>
    </form>
</div>
