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
                _e('Schedule', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="schedule_control"
                               value="1"
                            <?php
                            echo $schedule_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose when to start showing and/or stop showing your blocks.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                _e('User roles', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="user_role_control"
                               value="1"
                            <?php
                            echo $user_role_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose which users can see your blocks.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                _e('Device Type', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="device_type_control"
                               value="1"
                            <?php
                            echo $device_type_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose the device type to show your blocks on.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                _e('Device Width', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="device_width_control"
                               value="1"
                            <?php
                            echo $device_width_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose the device width to show your blocks on.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                _e('Term archives', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="archive_control"
                               value="1"
                            <?php
                            echo $archive_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose on which taxonomies and terms archive pages your blocks can be displayed.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                _e('Pages', 'advanced-gutenberg') ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="page_control"
                               value="1"
                            <?php
                            echo $page_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose in which pages your blocks can be displayed.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php
                        _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php
                            _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php
                            _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Browser & Device', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="browser_device_control" value="1" <?php echo $browser_device_control ? ' checked' : '' ?> />
                        <?php _e('Choose which browsers and devices can see your blocks.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Operating System', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="operating_system_control" value="1" <?php echo $operating_system_control ? ' checked' : '' ?> />
                        <?php _e('Choose which operating systems can see your blocks.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Cookie', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="cookie_control" value="1" <?php echo $cookie_control ? ' checked' : '' ?> />
                        <?php _e('Show or hide blocks based on cookie values.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('User Meta', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="user_meta_control" value="1" <?php echo $user_meta_control ? ' checked' : '' ?> />
                        <?php _e('Show or hide blocks based on user meta values.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Post Meta', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="post_meta_control" value="1" <?php echo $post_meta_control ? ' checked' : '' ?> />
                        <?php _e('Show or hide blocks based on post meta values.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Query String', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="query_string_control" value="1" <?php echo $query_string_control ? ' checked' : '' ?> />
                        <?php _e('Show or hide blocks based on URL query parameters.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Capabilities', 'advanced-gutenberg') ?></th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="capabilities_control" value="1" <?php echo $capabilities_control ? ' checked' : '' ?> />
                        <?php _e('Show or hide blocks based on user capabilities.', 'advanced-gutenberg') ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e('This block control is available on:', 'advanced-gutenberg') ?>
                        <code><?php _e('Post', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Widgets', 'advanced-gutenberg') ?></code>
                        <code><?php _e('Site Editor', 'advanced-gutenberg') ?></code>
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
