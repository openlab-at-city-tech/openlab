<?php
defined( 'ABSPATH' ) || die;

$enable_blocks_spacing          = $this->getOptionSetting( 'advgb_settings', 'enable_blocks_spacing', 'checkbox', 0 );
$blocks_spacing                 = $this->getOptionSetting( 'advgb_settings', 'blocks_spacing', 'text', 0 );
$editor_width                   = $this->getOptionSetting( 'advgb_settings', 'editor_width', 'text', '0' );
$enable_columns_visual_guide    = $this->getOptionSetting( 'advgb_settings', 'enable_columns_visual_guide', 'checkbox', 1 );
$blocks_icon_color              = $this->getOptionSetting( 'advgb_settings', 'blocks_icon_color', 'text', '#655997' );

// Disabled since 3.0.0 - @TODO Remove later
$disable_wpautop_checked        = $this->getOptionSetting( 'advgb_settings', 'disable_wpautop', 'checkbox', 0 );

// Replace old default icon color and disable wpautop since 3.0.0 - @TODO Remove later
if( $blocks_icon_color === '#5952de' || $disable_wpautop_checked === 'checked' ) {

    $advgb_settings = get_option( 'advgb_settings' );

    // Replace old default color since 3.0.0 - Remove @TODO later
    if( $blocks_icon_color === '#5952de' ) {
        $blocks_icon_color = '#655997';
        $advgb_settings['blocks_icon_color'] = $blocks_icon_color;
    }

    // Force to be disabled since 3.0.0 - Remove @TODO later
    if( $disable_wpautop_checked === 'checked' ) {
        $disable_wpautop_checked = '';
        $advgb_settings['disable_wpautop'] = 0;
    }

    update_option( 'advgb_settings', $advgb_settings );
}
?>
<form method="post">
    <?php wp_nonce_field( 'advgb_settings_general_nonce', 'advgb_settings_general_nonce_field' ) ?>
    <table class="form-table">

        <?php
        // Pro settings
        if( defined( 'ADVANCED_GUTENBERG_PRO' ) ) {
            if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_setting' ) ) {
                echo PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_setting(
                    'enable_pp_branding',
                    __( 'Display PublishPress branding', 'advanced-gutenberg' ),
                    __( 'PublishPress logo and links in the footer of the admin pages', 'advanced-gutenberg' )
                );
            }
        }
        ?>

        <tr>
            <th scope="row">
                <?php _e( 'Enable blocks spacing', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="checkbox" name="enable_blocks_spacing"
                           id="enable_blocks_spacing"
                           value="1"
                        <?php echo esc_attr( $enable_blocks_spacing ) ?>
                    />
                    <?php
                    _e(
                        'Vertical spacing between blocks',
                        'advanced-gutenberg'
                    )
                    ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Blocks spacing', 'advanced-gutenberg' ) ?>
                <span> (px)</span>
            </th>
            <td>
                <label>
                    <input type="number"
                           min="0"
                           name="blocks_spacing"
                           id="blocks_spacing"
                           style="width: 70px;"
                           value="<?php echo esc_attr( $blocks_spacing ) ?>"
                    />
                </label>
            </td>
        </tr>
        <tr<?php echo ( ! $this->settingIsEnabled( 'enable_advgb_blocks' ) ? ' style="display:none;"' : '' ) ?>>
            <th scope="row">
                <?php _e( 'Blocks icon color', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="text"
                           name="blocks_icon_color"
                           id="blocks_icon_color"
                           class="minicolors minicolors-input"
                           value="<?php echo esc_attr( $blocks_icon_color ) ?>"
                           style="width:100px"
                    />
                </label>
                <p class="description">
                    <?php
                    _e(
                        'Apply in admin to blocks from PublishPress Blocks plugin.',
                        'advanced-gutenberg'
                    )
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Gutenberg editor width', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <select name="editor_width" id="editor_width">
                        <option value="" <?php echo $editor_width === '' ? 'selected' : '' ?>>
                            <?php esc_html_e( 'Default', 'advanced-gutenberg' ); ?>
                        </option>
                        <option value="75" <?php echo $editor_width === '75' ? 'selected' : '' ?>>
                            <?php esc_html_e( 'Large', 'advanced-gutenberg' ); ?>
                        </option>
                        <option value="95" <?php echo $editor_width === '95' ? 'selected' : '' ?>>
                            <?php esc_html_e( 'Full width', 'advanced-gutenberg' ); ?>
                        </option>
                    </select>
                </label>
            </td>
        </tr>
        <tr<?php echo ( ! $this->settingIsEnabled( 'enable_advgb_blocks' ) ? ' style="display:none;"' : '' ) ?>>
            <th scope="row">
                <?php _e( 'Enable columns visual guide', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="checkbox" name="enable_columns_visual_guide"
                           id="enable_columns_visual_guide"
                           value="1"
                        <?php echo esc_attr( $enable_columns_visual_guide ) ?>
                    />
                    <?php
                    _e(
                        'Visual guide for PublishPress Blocks Columns block',
                        'advanced-gutenberg'
                    )
                    ?>
                </label>
            </td>
        </tr>
        <tr style="display: none;">
            <th scope="row">
                <?php _e( 'Remove autop', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="checkbox" name="disable_wpautop"
                           id="ag_disable_wpautop"
                           value="1"
                        <?php echo esc_attr( $disable_wpautop_checked ) ?>
                    />
                    <?php
                    _e(
                        'Autop WordPress function is used to prevent unwanted paragraphs to be added',
                        'advanced-gutenberg'
                    )
                    ?>
                </label>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                name="save_settings_general"
        >
            <?php esc_html_e( 'Save General Settings', 'advanced-gutenberg' ) ?>
        </button>
    </div>
</form>
