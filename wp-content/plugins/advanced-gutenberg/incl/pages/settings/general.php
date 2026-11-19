<?php

defined('ABSPATH') || die;

$blocks_icon_color = $this->getOptionSetting('advgb_settings', 'blocks_icon_color', 'text', '#655997');

// Replace old default icon color since 3.0.0 - @TODO Remove later
if ($blocks_icon_color === '#5952de') {
    $advgb_settings                      = get_option('advgb_settings');
    $blocks_icon_color                   = '#655997';
    $advgb_settings['blocks_icon_color'] = $blocks_icon_color;
    update_option('advgb_settings', $advgb_settings);
}
?>
<form method="post">
    <?php
    wp_nonce_field('advgb_settings_general_nonce', 'advgb_settings_general_nonce_field') ?>
    <table class="form-table">
        <tr<?php
        echo( ! $this->settingIsEnabled('enable_advgb_blocks') ? ' style="display:none;"' : '' ) ?>>
            <th scope="row">
                <label for="blocks_icon_color">
                <?php
                esc_html_e('Blocks icon color', 'advanced-gutenberg') ?>
                </label>
            </th>
            <td>
                    <input type="text"
                           name="blocks_icon_color"
                           id="blocks_icon_color"
                           class="minicolors minicolors-input"
                           value="<?php
                            echo esc_attr($blocks_icon_color) ?>"
                           style="width:100px"
                    />
                <p class="description">
                    <?php
                    esc_html_e(
                        'Apply in admin to blocks from PublishPress Blocks plugin.',
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
                name="save_settings_general"
        >
            <?php
            esc_html_e('Save Settings', 'advanced-gutenberg') ?>
        </button>
    </div>
</form>
