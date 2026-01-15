<?php
defined('ABSPATH') || die;

$block_features_settings = $this->getOptionSetting('advgb_settings', 'block_features_settings', 'array', []);
$setting_last_saved_role = $this->getOptionSetting('advgb_settings', 'setting_last_saved_role', 'text', '');

?>
<form method="post">
     <?php
        wp_nonce_field('advgb_settings_block_features_nonce', 'advgb_settings_block_features_nonce_field') ?>
    <table class="form-table">
        <tbody>
            <tr>
                <td colspan="2" style="padding-left: 0;padding-top: 0;">
                    <p style="margin-top: 10px;">
                        <label>
                            <span><?php esc_html_e('Select Role', 'advanced-gutenberg'); ?>:</span>
                            <select name="setting_last_saved_role" class="pp-blocks-settings-role-select">
                                <?php
                                $table_default_tab_role = $setting_last_saved_role;

                                $table_tabs = [];
                                foreach (wp_roles()->roles as $role => $detail) :
                                    if ($table_default_tab_role == '') {
                                        $table_default_tab_role = $role;
                                    }
                                    $active_option = ($table_default_tab_role == $role);
                                    ?>
                                    <option
                                        value="<?php echo esc_attr($role); ?>"
                                        data-content="<?php echo esc_attr('.pp-blocks-features-settings-' . $role . '-content'); ?>"
                                        <?php selected($active_option, true); ?>
                                    >
                                        <?php echo esc_html($detail['name']); ?>
                                    </option>
                                    <?php
                                endforeach; ?>

                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo join(' | ', $table_tabs);
                                    ?>
                            </select>
                        </label>
                    </p>
                </td>
            </tr>

            <?php foreach (wp_roles()->roles as $role => $detail) :
                $visibility_class = ($table_default_tab_role == $role) ? '' : 'hidden-element';
                ?>
                <tr class="pp-blocks-settings-tab-content pp-blocks-features-settings-<?php echo esc_attr($role); ?>-content <?php echo esc_attr($visibility_class); ?>">
                    <?php
                        $disable_block_adding = !empty($block_features_settings[$role]['disable_block_adding']);
                    ?>
                    <th scope="row">
                        <?php esc_html_e('Disable all block adding', 'advanced-gutenberg'); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox"
                                name="block_features_settings[<?php echo esc_attr($role); ?>][disable_block_adding]" id="block_features_settings_<?php echo esc_attr($role); ?>_disable_block_adding"
                                value="1"
                                <?php checked($disable_block_adding, true);?>
                            >
                            <span class="description">
                                <?php printf(esc_html__('Prevent users in %1s role from adding new block to posts.', 'advanced-gutenberg'), esc_html($detail['name'])); ?>
                            </span>
                        </label>
                        <br>
                    </td>
                </tr>

                <tr class="pp-blocks-settings-tab-content pp-blocks-features-settings-<?php echo esc_attr($role); ?>-content <?php echo esc_attr($visibility_class); ?>">
                <?php
                        $disable_pattern_directory = !empty($block_features_settings[$role]['disable_pattern_directory']);
                ?>
                    <th scope="row">
                        <?php esc_html_e('Disable the Pattern Directory', 'advanced-gutenberg'); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox"
                                name="block_features_settings[<?php echo esc_attr($role); ?>][disable_pattern_directory]" id="block_features_settings_<?php echo esc_attr($role); ?>_disable_pattern_directory"
                                value="1"
                                <?php checked($disable_pattern_directory, true);?>
                            >
                            <span class="description">
                                <?php printf(esc_html__('Disable the pattern directory for users in %1s role.', 'advanced-gutenberg'), esc_html($detail['name'])); ?>
                            </span>
                        </label>
                        <br>
                    </td>
                </tr>

                <tr class="pp-blocks-settings-tab-content pp-blocks-features-settings-<?php echo esc_attr($role); ?>-content <?php echo esc_attr($visibility_class); ?>">
                <?php
                        $disable_openverse = !empty($block_features_settings[$role]['disable_openverse']);
                ?>
                    <th scope="row">
                        <?php esc_html_e('Disable the Openverse', 'advanced-gutenberg'); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox"
                                name="block_features_settings[<?php echo esc_attr($role); ?>][disable_openverse]" id="block_features_settings_<?php echo esc_attr($role); ?>_disable_openverse"
                                value="1"
                                <?php checked($disable_openverse, true);?>
                            >
                            <span class="description">
                                <?php printf(esc_html__('Disable Openverse for users in %1s role.', 'advanced-gutenberg'), esc_html($detail['name'])); ?>
                            </span>
                        </label>
                        <br>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_settings_block_features"
        >
               <?php
                esc_html_e('Save Image Settings', 'advanced-gutenberg') ?>
        </button>
    </div>
</form>
