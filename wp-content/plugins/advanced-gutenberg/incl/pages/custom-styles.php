<?php
defined( 'ABSPATH' ) || die;

$custom_styles_saved = get_option( 'advgb_custom_styles', $this::$default_custom_styles );
?>
<div class="publishpress-admin wrap">
    <?php
    if ( isset( $_GET['save'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action
        ?>
        <div id="message" class="updated fade">
            <p>
                <?php esc_html_e( 'Your styles have been saved!', 'advanced-gutenberg' ); ?>
            </p>
        </div>
    <?php endif; ?>

    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Block Styles', 'advanced-gutenberg' ); ?>
        </h1>
    </header>

    <div class="wrap">
        <div id="customstyles-tab" class="tab-content">
            <div id="advgb-customstyles-list">
                <div id="mybootstrap">
                    <ul class="advgb-customstyles-list">
                        <?php
                        $content = '';
                        foreach ( $custom_styles_saved as $customStyles ) {
                            $content .= '<li class="advgb-customstyles-items" data-id-customstyle="' . esc_attr( (int) $customStyles['id'] ) . '">';
                            $content .= '<a><i class="title-icon" style="background-color: ' . esc_attr( $customStyles['identifyColor'] ) . '"></i><span class="advgb-customstyles-items-title">' . esc_html( $customStyles['title'] ) . '</span></a>';
                            $content .= '<a class="copy" title="' . esc_attr__( 'Copy', 'advanced-gutenberg' ) . '"><span class="dashicons dashicons-admin-page"></span></a>';
                            $content .= '<a class="trash" title="' . esc_attr__( 'Delete', 'advanced-gutenberg' ) . '"><span class="dashicons dashicons-no"></span></a>';
                            $content .= '<ul style="margin-left: 30px"><li class="advgb-customstyles-items-class">(' . esc_html( $customStyles['name'] ) . ')</li></ul>';
                            $content .= '</li>';
                        }
                        $content .= '<li style="text-align: center; margin-top: 40px"><a class="advgb-customstyles-new button button-secondary"><span class="dashicons dashicons-plus"></span>' . esc_html__( 'Add new style', 'advanced-gutenberg' ) . '</a></li>';

                        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped
                        ?>
                    </ul>
                </div>
            </div>

            <div id="advgb-customstyles-info">
                <div class="advgb-style-title-wrapper">
                    <label for="advgb-customstyles-title">
                        <?php esc_html_e( 'Style title', 'advanced-gutenberg' ) ?>
                    </label>
                    <input type="text" class="regular-text" name="customstyles-title" id="advgb-customstyles-title"
                           value=""/>
                </div>
                <div class="advgb-customstyles-two-columns">
                    <div>
                        <label for="advgb-customstyles-classname">
                            <?php esc_html_e( 'Class name', 'advanced-gutenberg' ) ?>
                        </label>
                        <input type="text" class="regular-text" name="customstyles-classname" id="advgb-customstyles-classname"
                               value=""/>
                    </div>
                    <div id="identify-colors">
                        <div class="control-label">
                            <label for="advgb-customstyles-identify-color"
                                   class="advgb_qtip"
                                   data-qtip="<?php esc_attr_e(
                                       'This option help you identify specific custom styles in the list
                                            (usually set this same as the custom style\'s background color)',
                                       'advanced-gutenberg'
                                   ) ?>"
                            >
                                <?php esc_html_e( 'Identification color', 'advanced-gutenberg' ) ?>
                            </label>
                        </div>
                        <div class="controls">
                            <input type="text"
                                   name="customstyles-identify-color"
                                   id="advgb-customstyles-identify-color"
                                   class="minicolors minicolors-input"
                                   value="#000000"/>
                        </div>
                    </div>
                </div>
                <div class="advgb-customstyles-css">
                    <label for="advgb-customstyles-css">
                        <?php esc_html_e( 'Custom CSS', 'advanced-gutenberg' ) ?>
                    </label>
                    <textarea name="customstyles-css" id="advgb-customstyles-css"></textarea>
                </div>
                <div id="css-tips" style="border-top: 1px solid #ccc; margin-top: -25px;">
                    <small>
                        <?php esc_html_e( 'Hint: Use "Ctrl + Space" for auto completion', 'advanced-gutenberg' ) ?>
                    </small>
                </div>
                <div style="text-align: center; margin-top: 15px; margin-bottom: 15px; clear: both;">
                    <form method="POST">
                        <?php wp_nonce_field( 'advgb_cstyles_nonce', 'advgb_cstyles_nonce_field' ); ?>
                        <input type="hidden" name="save_custom_styles" value="1" />
                        <button class="button button-primary"
                                style="margin: 10px auto"
                                type="button"
                                id="save_custom_styles"
                                value="1"
                        >
                            <span><?php esc_html_e( 'Save styles', 'advanced-gutenberg' ) ?></span>
                        </button>
                    </form>
                </div>

                <div id="advgb-customstyles-preview">
                    <p class="preview-title"><?php esc_html_e( 'Preview', 'advanced-gutenberg' ); ?></p>
                    <p class="previous-block" style="margin-bottom: 20px; margin-top: 10px; font-size: 16px; line-height: 24px;">
                        <strong>
                            <?php esc_html_e( 'Previous Paragraph.', 'advanced-gutenberg' ); ?>
                        </strong>
                        <?php echo esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vitae condimentum augue. Nullam semper augue quis posuere lacinia. Praesent non lectus nunc.' ); ?>
                    </p>
                    <div class="advgb-customstyles-target"><?php esc_html_e('Example text with this style', 'advanced-gutenberg' ) ?></div>
                    <p class="follow-block" style="margin-bottom: 10px; margin-top: 20px; font-size: 16px; line-height: 24px;">
                        <strong>
                            <?php esc_html_e( 'Following Paragraph.', 'advanced-gutenberg' ); ?>
                        </strong>
                        <?php echo esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vitae condimentum augue. Nullam semper augue quis posuere lacinia. Praesent non lectus nunc.' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
