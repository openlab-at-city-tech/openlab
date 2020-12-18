<?php
defined('ABSPATH') || die;

$custom_styles_saved = get_option('advgb_custom_styles', $this::$default_custom_styles);
?>

<div class="advgb-header" style="padding-top: 40px">
    <h1 class="header-title"><?php esc_html_e('Custom styles', 'advanced-gutenberg'); ?></h1>
</div>

<?php if (isset($_GET['save_styles'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action ?>
    <div class="ju-notice-msg ju-notice-success">
        <?php esc_html_e('Your styles have been saved', 'advanced-gutenberg'); ?>
        <i class="dashicons dashicons-dismiss ju-notice-close"></i>
    </div>
<?php endif; ?>

<div id="customstyles-tab" class="tab-content clearfix">
    <div class="col-sm-3" id="advgb-customstyles-list">
        <div id="mybootstrap">
            <ul class="advgb-customstyles-list">
                <?php
                $content = '';
                foreach ($custom_styles_saved as $customStyles) {
                    $content .= '<li class="advgb-customstyles-items" data-id-customstyle="' . (int) $customStyles['id'] . '">';
                    $content .= '<a><i class="title-icon" style="background-color: ' . $customStyles['identifyColor'] . '"></i><span class="advgb-customstyles-items-title">' . esc_html($customStyles['title']) . '</span></a>';
                    $content .= '<a class="copy" title="' . __('Copy', 'advanced-gutenberg') . '"><i class="mi mi-content-copy"></i></a>';
                    $content .= '<a class="trash" title="' . __('Delete', 'advanced-gutenberg') . '"><i class="mi mi-delete"></i></a>';
                    $content .= '<a class="edit" title="' . __('Edit', 'advanced-gutenberg') . '"><i class="mi mi-edit"></i></a>';
                    $content .= '<ul style="margin-left: 30px"><li class="advgb-customstyles-items-class">(' . esc_html($customStyles['name']) . ')</li></ul>';
                    $content .= '</li>';
                }
                $content .= '<li style="text-align: center; margin-top: 20px"><a class="advgb-customstyles-new button pp-default-button"><span class="dashicons dashicons-plus"></span>' . esc_html__('Add new class', 'advanced-gutenberg') . '</a></li>';

                echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped
                ?>
            </ul>
            <span id="savedInfo" style="display:none;">
                <?php esc_html_e('All modifications were saved!', 'advanced-gutenberg') ?>
            </span>
        </div>
    </div>

    <div class="col-sm-9" id="advgb-customstyles-info">
        <div class="control-group col-sm-6">
            <label for="advgb-customstyles-classname">
                <?php esc_html_e('Style class', 'advanced-gutenberg') ?>
            </label>
            <input type="text" class="ju-input" name="customstyles-classname" id="advgb-customstyles-classname"
                   value=""/>
        </div>
        <div id="identify-colors" class="control-group clearfix col-sm-6">
            <div class="control-label">
                <label for="advgb-customstyles-identify-color"
                       class="advgb_qtip"
                       data-qtip="<?php esc_attr_e(
                           'This option help you identify specific custom styles in the list
                                (usually set this same as the custom style\'s background color)',
                           'advanced-gutenberg'
                       ) ?>"
                >
                    <?php esc_html_e('Identification color', 'advanced-gutenberg') ?>
                </label>
            </div>
            <div class="controls">
                <input type="text"
                       name="customstyles-identify-color"
                       id="advgb-customstyles-identify-color"
                       class="minicolors minicolors-input ju-input"
                       value="#000000"/>
            </div>
        </div>
        <div class="control-group advgb-customstyles-css col-sm-12">
            <label for="advgb-customstyles-css">
                <?php esc_html_e('Custom CSS', 'advanced-gutenberg') ?>
            </label>
            <textarea name="customstyles-css" id="advgb-customstyles-css"></textarea>
        </div>
        <div class="col-sm-12" id="css-tips" style="border-top: 1px solid #ccc; margin-top: -25px;">
            <small><?php esc_html_e('Hint: Use "Ctrl + Space" for auto completion', 'advanced-gutenberg') ?></small>
        </div>
        <div style="text-align: center; margin-top: 15px; margin-bottom: 15px; clear: both;">
            <form method="POST">
                <?php wp_nonce_field('advgb_cstyles_nonce', 'advgb_cstyles_nonce_field'); ?>
                <input type="hidden" name="save_custom_styles" value="1" />
                <button class="button button-primary pp-primary-button"
                        style="margin: 10px auto"
                        type="button"
                        id="save_custom_styles"
                        value="1"
                >
                    <span><?php esc_html_e('Save styles', 'advanced-gutenberg') ?></span>
                </button>
            </form>
        </div>

        <div id="advgb-customstyles-preview">
            <p class="preview-title"><?php esc_html_e('Preview', 'advanced-gutenberg'); ?></p>
            <p class="previous-block" style="margin-bottom: 20px; margin-top: 10px;">
                <?php esc_html_e('Previous Paragraph Previous Paragraph Previous Paragraph Previous Paragraph Previous Paragraph Previous Paragraph Previous Paragraph Previous Paragraph Previous Paragraph', 'advanced-gutenberg') ?>
            </p>
            <div class="advgb-customstyles-target"><?php esc_html_e('Example of text', 'advanced-gutenberg') ?></div>
            <p class="follow-block">
                <?php esc_html_e('Following Paragraph Following Paragraph  Following Paragraph Following Paragraph Following Paragraph Following Paragraph Following Paragraph Following Paragraph', 'advanced-gutenberg') ?>
            </p>
        </div>
    </div>
</div>

