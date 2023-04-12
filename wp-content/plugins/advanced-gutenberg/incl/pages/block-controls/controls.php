<?php
defined( 'ABSPATH' ) || die;

$schedule_control   = PublishPress\Blocks\Controls::getControlValue( 'schedule', 1 );
$user_role_control  = PublishPress\Blocks\Controls::getControlValue( 'user_role', 1 );
$archive_control    = PublishPress\Blocks\Controls::getControlValue( 'archive', 1 );
$page_control       = PublishPress\Blocks\Controls::getControlValue( 'page', 1 );
?>
<form method="post">
    <?php wp_nonce_field( 'advgb_controls_settings_nonce', 'advgb_controls_settings_nonce_field' ); ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Schedule', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="schedule_control"
                               value="1"
                               <?php echo $schedule_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose when to start showing and/or stop showing your blocks.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e( 'This block control is available on:', 'advanced-gutenberg' ) ?>
                        <code><?php _e( 'Post', 'advanced-gutenberg' ) ?></code>
                        <code><?php _e( 'Widgets', 'advanced-gutenberg' ) ?></code>
                        <code><?php _e( 'Site Editor', 'advanced-gutenberg' ) ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'User roles', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="user_role_control"
                               value="1"
                               <?php echo $user_role_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose which users can see your blocks.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e( 'This block control is available on:', 'advanced-gutenberg' ) ?>
                        <code><?php _e( 'Post', 'advanced-gutenberg' ) ?></code>
                        <code><?php _e( 'Widgets', 'advanced-gutenberg' ) ?></code>
                        <code><?php _e( 'Site Editor', 'advanced-gutenberg' ) ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Term archives', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="archive_control"
                               value="1"
                               <?php echo $archive_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose on which taxonomies and terms archive pages your blocks can be displayed.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e( 'This block control is available on:', 'advanced-gutenberg' ) ?>
                        <code><?php _e( 'Widgets', 'advanced-gutenberg' ) ?></code>
                        <code><?php _e( 'Site Editor', 'advanced-gutenberg' ) ?></code>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Pages', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <fieldset>
                    <label>
                        <input type="checkbox" name="page_control"
                               value="1"
                               <?php echo $page_control ? ' checked' : '' ?>
                        />
                        <?php
                        _e(
                            'Choose in which pages your blocks can be displayed.',
                            'advanced-gutenberg'
                        )
                        ?>
                    </label><br>
                    <p class="description advgb-control-desc">
                        <?php _e( 'This block control is available on:', 'advanced-gutenberg' ) ?>
                        <code><?php _e( 'Widgets', 'advanced-gutenberg' ) ?></code>
                        <code><?php _e( 'Site Editor', 'advanced-gutenberg' ) ?></code>
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
            <?php esc_html_e( 'Save Controls', 'advanced-gutenberg' ) ?>
        </button>
    </div>
</form>
