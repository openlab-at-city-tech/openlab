<?php

defined('ABSPATH') || die;

$schedule_control  = PublishPress\Blocks\Controls::getControlValue('schedule', 1);
$user_role_control = PublishPress\Blocks\Controls::getControlValue('user_role', 1);
$device_type_control = PublishPress\Blocks\Controls::getControlValue('device_type', 1);
$device_width_control = PublishPress\Blocks\Controls::getControlValue('device_width', 1);
$archive_control   = PublishPress\Blocks\Controls::getControlValue('archive', 1);
$page_control      = PublishPress\Blocks\Controls::getControlValue('page', 1);

$browser_device_control = PublishPress\Blocks\Controls::getControlValue('browser_device', 1);
$operating_system_control = PublishPress\Blocks\Controls::getControlValue('operating_system', 1);
$cookie_control = PublishPress\Blocks\Controls::getControlValue('cookie', 1);
$user_meta_control = PublishPress\Blocks\Controls::getControlValue('user_meta', 1);
$post_meta_control = PublishPress\Blocks\Controls::getControlValue('post_meta', 1);
$query_string_control = PublishPress\Blocks\Controls::getControlValue('query_string', 1);
$capabilities_control = PublishPress\Blocks\Controls::getControlValue('capabilities', 1);
?>
<form method="post">
    <?php
    wp_nonce_field('advgb_controls_settings_nonce', 'advgb_controls_settings_nonce_field'); ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php
                esc_html_e('Schedule', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="schedule_control"
                               value="1"
                            <?php
                            checked($schedule_control); ?>
                        />
                        <?php
                        esc_html_e(
                            'Choose when to start showing and/or stop showing your blocks.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                esc_html_e('User roles', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="user_role_control"
                               value="1"
                            <?php
                            checked($user_role_control); ?>
                        />
                        <?php
                        esc_html_e(
                            'Choose which users can see your blocks.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                esc_html_e('Device Type', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="device_type_control"
                               value="1"
                            <?php
                            checked($device_type_control); ?>
                        />
                        <?php
                        esc_html_e(
                            'Choose the device type to show your blocks on.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                esc_html_e('Device Width', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="device_width_control"
                               value="1"
                            <?php
                            checked($device_width_control); ?>
                        />
                        <?php
                        esc_html_e(
                            'Choose the device width to show your blocks on.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                esc_html_e('Term archives', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="archive_control"
                               value="1"
                            <?php
                            checked($archive_control); ?>
                        />
                        <?php
                        esc_html_e(
                            'Choose on which taxonomies and terms archive pages your blocks can be displayed.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                esc_html_e('Pages', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="page_control"
                               value="1"
                            <?php
                            checked($page_control); ?>
                        />
                        <?php
                        esc_html_e(
                            'Choose in which pages your blocks can be displayed.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Browser & Device', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="browser_device_control" value="1" <?php checked($browser_device_control); ?> />
                        <?php esc_html_e('Choose which browsers and devices can see your blocks.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Operating System', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="operating_system_control" value="1" <?php checked($operating_system_control); ?> />
                        <?php esc_html_e('Choose which operating systems can see your blocks.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Cookie', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="cookie_control" value="1" <?php checked($cookie_control); ?> />
                        <?php esc_html_e('Show or hide blocks based on cookie values.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('User Meta', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="user_meta_control" value="1" <?php checked($user_meta_control); ?> />
                        <?php esc_html_e('Show or hide blocks based on user meta values.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Post Meta', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="post_meta_control" value="1" <?php checked($post_meta_control); ?> />
                        <?php esc_html_e('Show or hide blocks based on post meta values.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Query String', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="query_string_control" value="1" <?php checked($query_string_control); ?> />
                        <?php esc_html_e('Show or hide blocks based on URL query parameters.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Capabilities', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="capabilities_control" value="1" <?php checked($capabilities_control); ?> />
                        <?php esc_html_e('Show or hide blocks based on user capabilities.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php esc_html_e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php esc_html_e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php esc_html_e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_controls"
        >
            <?php
            esc_html_e('Save Controls', 'advanced-gutenberg') ?>
        </button>
    </div>
</form>
