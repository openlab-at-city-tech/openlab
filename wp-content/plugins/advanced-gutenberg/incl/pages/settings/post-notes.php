<?php

defined('ABSPATH') || die;

$post_notes_block_toolbar = $this->getOptionSetting('advgb_settings', 'post_notes_block_toolbar', 'checkbox', 1);
?>
<form method="post">
    <?php
    wp_nonce_field('advgb_settings_post_notes_nonce', 'advgb_settings_post_notes_nonce_field') ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php esc_html_e('Block toolbar icon', 'advanced-gutenberg') ?>
            </th>
            <td>
                <label>
                    <input type="checkbox"
                           name="post_notes_block_toolbar"
                           id="post_notes_block_toolbar"
                           value="1"
                        <?php echo esc_attr($post_notes_block_toolbar) ?>
                    />
                    <?php esc_html_e(
                        'Show an "Add note" icon in the block toolbar (next to Bold, Italic and Link). It opens the WordPress Notes feature for the selected block.',
                        'advanced-gutenberg'
                    ) ?>
                </label>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_settings_post_notes"
        >
            <?php esc_html_e('Save Post Notes Settings', 'advanced-gutenberg') ?>
        </button>
    </div>
</form>
