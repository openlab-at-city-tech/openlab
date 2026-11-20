<?php

defined('ABSPATH') || die;

$google_api_key_saved = $this->getOptionSetting('advgb_settings', 'google_api_key', 'text', '');
?>
<form method="post">
    <?php
    wp_nonce_field('advgb_settings_maps_nonce', 'advgb_settings_maps_nonce_field') ?>
    <table class="form-table">

        <tr>
            <th scope="row">
                <label for="google_api_key">
                <?php
                esc_html_e('Google API key', 'advanced-gutenberg') ?>
                </label>
            </th>
            <td>
                    <input type="text"
                           name="google_api_key"
                           id="google_api_key"
                           class="regular-text"
                           value="<?php
                            echo esc_attr($google_api_key_saved) ?>"
                    />
                <p class="description">
                    <a target="_blank"
                       href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                       aria-label="<?php esc_attr_e('How to create a Google API Key', 'advanced-gutenberg'); ?>">
                        <?php
                        esc_html_e(
                            'How to create a Google API Key',
                            'advanced-gutenberg'
                        )
                        ?>
                    </a><br/>
                    <?php
                    esc_html_e(
                        'A Google API key is required to use the Map block without any warning.',
                        'advanced-gutenberg'
                    );
                    ?>
                </p>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_settings_maps"
        >
            <?php
            esc_html_e('Save Maps Settings', 'advanced-gutenberg') ?>
        </button>
    </div>
</form>
