<?php
defined( 'ABSPATH' ) || die;

$recaptcha_enabled      = $this->getOptionSetting( 'advgb_recaptcha_config', 'recaptcha_enable', 'checkbox', 0 );
$recaptcha_site_key     = $this->getOptionSetting( 'advgb_recaptcha_config', 'recaptcha_site_key', 'text', '' );
$recaptcha_secret_key   = $this->getOptionSetting( 'advgb_recaptcha_config', 'recaptcha_secret_key', 'text', '' );
$recaptcha_language     = $this->getOptionSetting( 'advgb_recaptcha_config', 'recaptcha_language', 'text', '' );
$recaptcha_theme        = $this->getOptionSetting( 'advgb_recaptcha_config', 'recaptcha_theme', 'text', '' );
?>
<form method="POST">
    <?php wp_nonce_field( 'advgb_captcha_nonce', 'advgb_captcha_nonce_field' ) ?>
    <p>
        <?php
        printf(
            __(
                'Use the Google reCAPTCHA to avoid spam in PublishPress Blocks forms. Get credentials for your domain by registering %shere%s.',
                'advanced-gutenberg'
            ),
            '<a href="https://www.google.com/recaptcha/about/" target="_blank">',
            '</a>'
        );
        ?>
    </p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Enable reCAPTCHA', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="checkbox"
                           name="recaptcha_enable"
                           id="recaptcha_enable"
                           value="1"
                        <?php echo esc_attr( $recaptcha_enabled ) ?>
                    />
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Site key', 'advanced-gutenberg' ) ?>
                (<?php _e( 'required', 'advanced-gutenberg' ) ?>)
            </th>
            <td>
                <label>
                    <input type="text"
                           name="recaptcha_site_key"
                           id="recaptcha_site_key"
                           class="regular-text"
                           value="<?php echo esc_attr( $recaptcha_site_key ) ?>"
                    />
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Secret key', 'advanced-gutenberg' ) ?>
                (<?php _e( 'required', 'advanced-gutenberg' ) ?>)
            </th>
            <td>
                <label>
                    <input type="text"
                           name="recaptcha_secret_key"
                           id="recaptcha_secret_key"
                           class="regular-text"
                           value="<?php echo esc_attr( $recaptcha_secret_key ) ?>"
                    />
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Language', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="text"
                           name="recaptcha_language"
                           id="recaptcha_language"
                           placeholder="<?php esc_attr_e( 'Auto detect', 'advanced-gutenberg' ); ?>"
                           value="<?php echo esc_attr( $recaptcha_language ) ?>"
                           style="width: 50px;"
                    />
                </label>
                <p class="description">
                    <a target="_blank" href="https://developers.google.com/recaptcha/docs/language">
                        <?php _e( 'Language codes list', 'advanced-gutenberg' ) ?>
                    </a>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Theme', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <select name="recaptcha_theme" id="recaptcha_theme">
                        <option value="light" <?php echo $recaptcha_theme === 'light' ? 'selected' : '' ?>>
                            <?php esc_html_e( 'Light', 'advanced-gutenberg' ); ?>
                        </option>
                        <option value="dark" <?php echo $recaptcha_theme === 'dark' ? 'selected' : '' ?>>
                            <?php esc_html_e( 'Dark', 'advanced-gutenberg' ); ?>
                        </option>
                        <option value="invisible" <?php echo $recaptcha_theme === 'invisible' ? 'selected' : '' ?>>
                            <?php esc_html_e( 'Invisible', 'advanced-gutenberg' ); ?>
                        </option>
                    </select>
                </label>
                <p class="description">
                    <?php
                    _e(
                        'We strongly recommend not using Invisible reCAPTCHA if you have more than 1 Newsletter or Contact forms block in a page.',
                        'advanced-gutenberg'
                    )
                    ?>
                </p>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                id="save_recaptcha_config"
                name="save_recaptcha_config"
        >
            <?php esc_html_e( 'Save reCAPTCHA Settings', 'advanced-gutenberg' ) ?>
        </button>
    </div>

</form>
