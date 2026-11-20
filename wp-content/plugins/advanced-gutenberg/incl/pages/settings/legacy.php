<?php

defined('ABSPATH') || die;

$legacy_map   = AdvancedGutenbergMain::legacyBlocksMap();
$legacy_state = AdvancedGutenbergMain::getLegacyBlocksState();

$gallery_lightbox_checked = $this->getOptionSetting('advgb_settings', 'gallery_lightbox', 'checkbox', 0);
$gallery_lightbox_caption = $this->getOptionSetting('advgb_settings', 'gallery_lightbox_caption', 'text', '1');
$font_styles_checked      = $this->getOptionSetting('advgb_settings', 'enable_core_blocks_features', 'checkbox', 1); // Matches settingIsEnabled(): a missing key means enabled
?>
<form method="post">
    <?php
    wp_nonce_field('advgb_settings_legacy_nonce', 'advgb_settings_legacy_nonce_field') ?>

    <h2><?php esc_html_e('Legacy Blocks', 'advanced-gutenberg') ?></h2>
    <p class="description" style="margin: 12px 0;">
        <?php esc_html_e(
            'These older blocks are turned off by default on new sites. Enable any you want to use in the editor.',
            'advanced-gutenberg'
        ) ?>
    </p>

    <table class="form-table">
        <?php foreach ($legacy_map as $slug => $label) : ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html($label) ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox"
                               name="advgb_legacy_blocks[<?php echo esc_attr($slug) ?>]"
                               id="advgb_legacy_block_<?php echo esc_attr($slug) ?>"
                               value="1"
                            <?php checked(! empty($legacy_state[$slug])) ?>
                        />
                        <?php
                        /* translators: %s: block name */
                        printf(esc_html__('Enable the %s block', 'advanced-gutenberg'), esc_html($label)) ?>
                    </label>
                </td>
            </tr>
        <?php endforeach ?>
    </table>

    <h2><?php esc_html_e('Legacy Features', 'advanced-gutenberg') ?></h2>
    <p class="description" style="margin: 12px 0;">
        <?php esc_html_e(
            'Older features, turned off by default on new sites.',
            'advanced-gutenberg'
        ) ?>
    </p>

    <table class="form-table">
        <tr>
            <th scope="row">
                <?php esc_html_e('Font styles', 'advanced-gutenberg') ?>
            </th>
            <td>
                <label>
                    <input type="checkbox"
                           name="enable_core_blocks_features"
                           id="enable_core_blocks_features"
                           value="1"
                        <?php echo esc_attr($font_styles_checked) ?>
                    />
                    <?php esc_html_e('Enable font settings for supported blocks', 'advanced-gutenberg') ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php esc_html_e('Gallery lightbox', 'advanced-gutenberg') ?>
            </th>
            <td>
                <label>
                    <input type="checkbox"
                           name="gallery_lightbox"
                           id="gallery_lightbox"
                           value="1"
                        <?php echo esc_attr($gallery_lightbox_checked) ?>
                    />
                    <?php esc_html_e('Open gallery images as a lightbox style popup', 'advanced-gutenberg') ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="gallery_lightbox_caption">
                <?php esc_html_e('Image caption', 'advanced-gutenberg') ?>
                </label>
            </th>
            <td>
                    <select name="gallery_lightbox_caption" id="gallery_lightbox_caption">
                        <option value="0"<?php echo ($gallery_lightbox_caption === '0' || $gallery_lightbox_caption === 0) ? ' selected' : '' ?>>
                            <?php esc_html_e('Disabled', 'advanced-gutenberg'); ?>
                        </option>
                        <option value="1"<?php echo ($gallery_lightbox_caption === '1' || $gallery_lightbox_caption === 1) ? ' selected' : '' ?>>
                            <?php esc_html_e('Bottom', 'advanced-gutenberg'); ?>
                        </option>
                        <option value="2"<?php echo ($gallery_lightbox_caption === '2' || $gallery_lightbox_caption === 2) ? ' selected' : '' ?>>
                            <?php esc_html_e('Overlay', 'advanced-gutenberg'); ?>
                        </option>
                    </select>
                <p class="description">
                    <?php esc_html_e('Display caption text on images loaded as lightbox in galleries.', 'advanced-gutenberg') ?>
                </p>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_settings_legacy"
        >
            <?php esc_html_e('Save Legacy Settings', 'advanced-gutenberg') ?>
        </button>
    </div>
</form>
