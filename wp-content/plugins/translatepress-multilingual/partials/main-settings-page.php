
<div id="trp-main-settings" class="wrap">
    <form method="post" action="options.php">
        <?php settings_fields( 'trp_settings' ); ?>
        <h1> <?php esc_html_e( 'TranslatePress Settings', 'translatepress-multilingual' );?></h1>
        <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

        <div id="trp-main-settings__wrap">
            <table id="trp-options" class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Default Language', 'translatepress-multilingual' ); ?> </th>
                    <td>
                        <select id="trp-default-language" name="trp_settings[default-language]" class="trp-select2">
                            <?php
                            foreach( $languages as $language_code => $language_name ){ ?>
                                <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>" <?php echo ( $this->settings['default-language'] == $language_code ? 'selected' : '' ); ?> >
                                    <?php echo esc_html( $language_name ); ?>
                                </option>
                            <?php }?>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'Select the original language of your content.', 'translatepress-multilingual' ); ?>
                        </p>

                        <p class="warning" style="display: none;" >
                            <?php esc_html_e( 'WARNING. Changing the default language will invalidate existing translations.', 'translatepress-multilingual' ); ?><br/>
                            <?php esc_html_e( 'Even changing from en_US to en_GB, because they are treated as two different languages.', 'translatepress-multilingual' ); ?><br/>
                            <?php esc_html_e( 'In most cases changing the default flag is all it is needed: ', 'translatepress-multilingual' ); ?>
                            <a href="https://translatepress.com/docs/developers/replace-default-flags/"><?php esc_html_e( 'replace the default flag', 'translatepress-multilingual' ); ?></a>
                        </p>

                    </td>
                </tr>

                <?php do_action( 'trp_language_selector', $languages ); ?>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Native language name', 'translatepress-multilingual' ); ?> </th>
                    <td>
                        <select id="trp-native-language-name" name="trp_settings[native_or_english_name]" class="trp-select">
                            <option value="english_name" <?php selected( $this->settings['native_or_english_name'], 'english_name' ); ?>><?php esc_html_e( 'No', 'translatepress-multilingual') ?></option>
                            <option value="native_name" <?php selected( $this->settings['native_or_english_name'], 'native_name' ); ?>><?php esc_html_e( 'Yes', 'translatepress-multilingual') ?></option>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'Select Yes if you want to display languages in their native names. Otherwise, languages will be displayed in English.', 'translatepress-multilingual' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Use a subdirectory for the default language', 'translatepress-multilingual' ); ?> </th>
                    <td>
                        <select id="trp-subdirectory-for-default-language" name="trp_settings[add-subdirectory-to-default-language]" class="trp-select">
                            <option value="no" <?php selected( $this->settings['add-subdirectory-to-default-language'], 'no' ); ?>><?php esc_html_e( 'No', 'translatepress-multilingual') ?></option>
                            <option value="yes" <?php selected( $this->settings['add-subdirectory-to-default-language'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'translatepress-multilingual') ?></option>
                        </select>
                        <p class="description">
                            <?php echo wp_kses ( __( 'Select Yes if you want to add the subdirectory in the URL for the default language.</br>By selecting Yes, the default language seen by website visitors will become the first one in the "All Languages" list.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Force language in custom links', 'translatepress-multilingual' ); ?> </th>
                    <td>
                        <select id="trp-force-language-in-custom-links" name="trp_settings[force-language-to-custom-links]" class="trp-select">
                            <option value="no" <?php selected( $this->settings['force-language-to-custom-links'], 'no' ); ?>><?php esc_html_e( 'No', 'translatepress-multilingual') ?></option>
                            <option value="yes" <?php selected( $this->settings['force-language-to-custom-links'], 'yes' ); ?>><?php esc_html_e( 'Yes', 'translatepress-multilingual') ?></option>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'Select Yes if you want to force custom links without language encoding to keep the currently selected language.', 'translatepress-multilingual' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Language Switcher', 'translatepress-multilingual' ); ?> </th>
                    <td>
                        <div class="trp-ls-type">
                            <input type="checkbox" disabled checked id="trp-ls-shortcode" ><b><?php esc_html_e( 'Shortcode ', 'translatepress-multilingual' ); ?>[language-switcher] </b>
                            <div>
                                <?php $this->output_language_switcher_select( 'shortcode-options', $this->settings['shortcode-options'] ); ?>
                            </div>
                            <p class="description">
                                <?php esc_html_e( 'Use shortcode on any page or widget.', 'translatepress-multilingual' ); ?>
                                <?php echo wp_kses_post( sprintf( __('You can also add the <a href="%s" title="Language Switcher Block Documentation">Language Switcher Block</a> in the WP Gutenberg Editor.', 'translatepress-multilingual'), esc_url('https://translatepress.com/docs/settings/#language-switcher-block' ) ) ); ?>
                            </p>
                        </div>
                        <div class="trp-ls-type">
                            <label><input type="checkbox" id="trp-ls-menu" disabled checked ><b><?php esc_html_e( 'Menu item', 'translatepress-multilingual' ); ?></b></label>
                            <div>
                                <?php $this->output_language_switcher_select( 'menu-options', $this->settings['menu-options'] ); ?>
                            </div>
                            <p class="description">
                                <?php
                                $link_start = '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) .'">';
                                $link_end = '</a>';
                                printf( wp_kses( __( 'Go to  %1$s Appearance -> Menus%2$s to add languages to the Language Switcher in any menu.', 'translatepress-multilingual' ), [ 'a' => [ 'href' => [] ] ] ), $link_start, $link_end ); //phpcs:ignore ?>
                                <a href="https://translatepress.com/docs/settings/#language-switcher" target="_blank"><?php esc_html_e( 'Learn more in our documentation.', 'translatepress-multilingual' ); ?></a>
                            </p>
                        </div>
                        <div class="trp-ls-type">
                            <label><input type="checkbox" id="trp-ls-floater" name="trp_settings[trp-ls-floater]"  value="yes"  <?php if ( isset($this->settings['trp-ls-floater']) && ( $this->settings['trp-ls-floater'] == 'yes' ) ){ echo 'checked'; }  ?>><b><?php esc_html_e( 'Floating language selection', 'translatepress-multilingual' ); ?></b></label>
                            <div>
                                <?php $this->output_language_switcher_select( 'floater-options', $this->settings['floater-options'] ); ?>
                                <?php $this->output_language_switcher_floater_color( $this->settings['floater-color'] ); ?>
                                <?php $this->output_language_switcher_floater_possition( $this->settings['floater-position'] ); ?>
                            </div>
                            <p class="description">
                                <?php esc_html_e( 'Add a floating dropdown that follows the user on every page.', 'translatepress-multilingual' ); ?>
                            </p>
                        </div>
                        <div class="trp-ls-type">
                        <label><input type="checkbox" id="trp-ls-show-poweredby" name="trp_settings[trp-ls-show-poweredby]"  value="yes"  <?php if ( isset($this->settings['trp-ls-show-poweredby']) && ( $this->settings['trp-ls-show-poweredby'] == 'yes' ) ){ echo 'checked'; }  ?>><b><?php esc_html_e( 'Show "Powered by TranslatePress"', 'translatepress-multilingual' ); ?></b></label>
                        <p class="description">
                        <?php esc_html_e( 'Show the small "Powered by TranslatePress" label in the floater language switcher.', 'translatepress-multilingual' ); ?>
                        </p>
                        </div>
                    </td>
                </tr>

                <?php do_action ( 'trp_extra_settings', $this->settings ); ?>
            </table>

            <?php
            $email_course_dismissed = get_user_meta( get_current_user_id(), 'trp_email_course_dismissed', true );

            if( empty( $email_course_dismissed ) || $email_course_dismissed != '1' ) : ?>
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

                        <a class="trp-email-course__close" href="#dismiss-email-course" title="<?php esc_html_e( 'Dismiss email course notification', 'translatepress-multilingual') ?>"></a>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'translatepress-multilingual' ); ?>" /></p>
    </form>
</div>
