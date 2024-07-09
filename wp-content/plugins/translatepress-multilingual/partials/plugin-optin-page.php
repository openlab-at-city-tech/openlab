<div class="wrap trp-optin-page">

    <div class="trp-optin-page__wrap">
        <div class="trp-optin-page__content">

            <div class="trp-optin-page__top">
                <img class="trp-option-page__logo-wordpress" src="<?php echo esc_attr(TRP_PLUGIN_URL . '/assets/images/plugin_optin_logo_wordpress.png') ?>">

                <span class="trp-optin-page__extra-icon dashicons dashicons-plus"></span>

                <img class="trp-option-page__logo-translatepress" src="<?php echo esc_attr(TRP_PLUGIN_URL . '/assets/images/plugin_optin_logo_translatepress.png') ?>">
            </div>

            <p class="trp-optin-page__message">
                <?php printf( wp_kses_post( __( 'Hey %s,<br>Never miss an important update - opt in to our security and feature updates notifications, and non-sensitive diagnostic tracking.', 'translatepress-multilingual' ) ), '<strong>'. esc_html($this->get_user_name() ) . '</strong>' );?>
            </p>

            <div class="trp-optin-page__bottom">
                <a class="button-primary" href="<?php echo esc_attr(wp_nonce_url( add_query_arg( [] ), 'trp_enable_plugin_optin' )); ?>" onclick="this.classList.add('disabled')" ><?php esc_html_e( 'Allow & Continue', 'translatepress-multilingual' ); ?></a>

                <a class="button-secondary" href="<?php echo esc_attr(wp_nonce_url( add_query_arg( [] ), 'trp_disable_plugin_optin' )); ?>"><?php esc_html_e( 'Skip', 'translatepress-multilingual' ); ?></a>
            </div>

        </div>

        <div class="trp-optin-page__footer">
            
            <div class="trp-optin-page__more-wrap">
                <a class="trp-optin-page__more" href="#" onclick="event.preventDefault(); document.getElementsByClassName('trp-optin-page__extra')[0].classList.toggle('hidden');"><?php esc_html_e( 'This will allow TranslatePress to:', 'translatepress-multilingual' ); ?></a>
            </div>

            <div class="trp-optin-page__extra hidden">
                <div class="trp-optin-page__extra-line">
                    <span class="trp-optin-page__extra-icon dashicons dashicons-admin-users"></span>
                    <div class="trp-optin-page__extra-content">
                        <h4><?php esc_html_e( 'Your profile overview', 'translatepress-multilingual' ); ?></h4>
                        <p><?php esc_html_e( 'Name and email address', 'translatepress-multilingual' ); ?></p>
                    </div>
                </div>

                <!-- <div class="trp-optin-page__extra-line">
                    <span class="trp-optin-page__extra-icon dashicons dashicons-admin-settings"></span>
                    <div class="trp-optin-page__extra-content">
                        <h4><?php //esc_html_e( 'Your site overview', 'translatepress-multilingual' ); ?></h4>
                        <p><?php //esc_html_e( 'Site URL, WP version, PHP info', 'translatepress-multilingual' ); ?></p>
                    </div>
                </div> -->

                <div class="trp-optin-page__extra-line">
                    <span class="trp-optin-page__extra-icon dashicons dashicons-testimonial"></span>
                    <div class="trp-optin-page__extra-content">
                        <h4><?php esc_html_e( 'Admin Notices', 'translatepress-multilingual' ); ?></h4>
                        <p><?php esc_html_e( 'Updates, announcements, marketing, no spam', 'translatepress-multilingual' ); ?></p>
                    </div>
                </div>

                <div class="trp-optin-page__extra-line">
                    <span class="trp-optin-page__extra-icon dashicons dashicons-admin-plugins"></span>
                    <div class="trp-optin-page__extra-content">
                        <h4><?php esc_html_e( 'Plugin status & settings', 'translatepress-multilingual' ); ?></h4>
                        <p><?php esc_html_e( 'Active, Deactivated, installed version and settings', 'translatepress-multilingual' ); ?></p>
                    </div>
                </div>

                <!-- <div class="trp-optin-page__extra-line">
                    <span class="trp-optin-page__extra-icon dashicons dashicons-menu"></span>
                    <div class="trp-optin-page__extra-content">
                        <h4><?php //esc_html_e( 'Plugins & Themes', 'translatepress-multilingual' ); ?></h4>
                        <p><?php //esc_html_e( 'Title, slug, version and is active', 'translatepress-multilingual' ); ?></p>
                    </div>
                </div> -->
            </div>

            <div class="trp-optin-page__footer-links">
                <a target="_blank" href="https://translatepress.com/privacy-policy/"><?php esc_html_e( 'Privacy Policy', 'translatepress-multilingual' ); ?></a>
                -
                <a target="_blank" href="https://translatepress.com/terms-conditions/#section10"><?php esc_html_e( 'Terms of Service', 'translatepress-multilingual' ); ?></a>
            </div>
        </div>
    </div>

</div>