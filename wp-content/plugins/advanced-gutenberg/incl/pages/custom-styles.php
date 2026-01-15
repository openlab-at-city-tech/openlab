<?php
defined('ABSPATH') || die;

$custom_styles_saved = get_option('advgb_custom_styles', AdvancedGutenbergBlockStyles::$default_custom_styles);
?>
<div class="publishpress-admin wrap">
    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e('Block Styles', 'advanced-gutenberg'); ?>
        </h1>
        <button type="button" class="page-title-action advgb-customstyles-new" disabled="disabled">
            <?php esc_html_e('Add new style', 'advanced-gutenberg'); ?>
        </button>
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
                        $content .= '';

                        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped
                        ?>
                    </ul>
                </div>
            </div>

            <div id="advgb-customstyles-info">

                <div id="advgb-customstyles-preview">
                    <div class="advgb-simple-preview">
                        <p class="preview-title"><?php esc_html_e('Preview', 'advanced-gutenberg'); ?></p>

                        <div class="advgb-preview-container">
                            <div class="advgb-customstyles-target">
                                <?php esc_html_e('Example text with this style', 'advanced-gutenberg') ?>
                            </div>
                        </div>
                    </div>

                    <fieldset class="advgb-fieldset advgb-preview-fieldset" style="display: none !important;">
                        <legend class="advgb-preview-legend button button-secondary">
                            <span class="dashicons dashicons-arrow-right"></span>
                            <?php esc_html_e('View Extended Preview', 'advanced-gutenberg'); ?>
                        </legend>
                        <div class="advgb-fieldset-content" style="display: none;">
                            <div class="advgb-extended-preview-container">
                                <div id="advgb-preview-target" class="advgb-customstyles-target">
                                    <!-- Dynamic content will be inserted here based on available styles -->
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Global settings -->

                <div class="advgb-customstyles-header-row">
                    <div class="advgb-style-title-wrapper">
                        <label for="advgb-customstyles-title">
                            <?php esc_html_e('Style title', 'advanced-gutenberg') ?>
                        </label>
                        <input type="text" class="regular-text" name="customstyles-title" id="advgb-customstyles-title"
                               value=""/>
                    </div>
                    <div class="advgb-classname-wrapper">
                        <label for="advgb-customstyles-classname">
                            <?php esc_html_e('Class name', 'advanced-gutenberg') ?>
                        </label>
                        <input type="text" class="regular-text" name="customstyles-classname" id="advgb-customstyles-classname"
                               value=""/>
                    </div>
                    <div class="advgb-identify-color-wrapper">
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
                                   class="minicolors minicolors-input"
                                   value="#000000"/>
                        </div>
                    </div>
                </div>

                <!-- Main Tabs Navigation -->
                <div class="advgb-main-tabs">
                    <ul class="advgb-tabs-panel">
                        <li class="advgb-tab" data-tab="style-editor">
                            <a><?php esc_html_e('Style Builder', 'advanced-gutenberg'); ?></a>
                        </li>
                        <li class="advgb-tab active" data-tab="custom-css">
                            <a><?php esc_html_e('Custom CSS', 'advanced-gutenberg'); ?></a>
                        </li>
                    </ul>
                </div>

                <!-- Custom CSS Tab Content -->
                <div id="custom-css-tab" class="advgb-tab-content main-tab-content" data-tab-content="custom-css" style="display: none;">

                    <div>
                        <div class="advgb-customstyles-css">
                            <label for="advgb-customstyles-css">
                                <?php esc_html_e('Custom CSS', 'advanced-gutenberg') ?>
                            </label>
                            <textarea name="customstyles-css" id="advgb-customstyles-css"></textarea>
                        </div>
                        <div id="css-tips" style="border-top: 1px solid #ccc; margin-top: -25px;">
                            <small>
                                <?php esc_html_e('Hint: Use "Ctrl + Space" for auto completion', 'advanced-gutenberg') ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Style Editor Tab Content -->
                <div id="style-editor-tab" class="advgb-tab-content main-tab-content active" data-tab-content="style-editor">

                    <div>
                        <!-- Enhanced Style Builder UI with Tabs -->
                        <div class="advgb-style-builder">
                            <h3><?php esc_html_e('Style Builder', 'advanced-gutenberg'); ?></h3>

                            <!-- Tab Navigation -->
                            <div class="advgb-tabs-wrapper advgb-sub-tabs advgb-tab-horz-desktop">
                                <ul class="advgb-tabs-panel">
                                    <li class="advgb-tab active" data-tab="colors">
                                        <a><?php esc_html_e('Colors', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="spacing">
                                        <a><?php esc_html_e('Spacing', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="typography">
                                        <a><?php esc_html_e('Typography', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="layout">
                                        <a><?php esc_html_e('Layout', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="border">
                                        <a><?php esc_html_e('Border', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="text-elements">
                                        <a><?php esc_html_e('Text', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="heading-elements">
                                        <a><?php esc_html_e('Headings', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="link-elements">
                                        <a><?php esc_html_e('Links', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="media-elements">
                                        <a><?php esc_html_e('Media', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="container-elements">
                                        <a><?php esc_html_e('Containers', 'advanced-gutenberg'); ?></a>
                                    </li>
                                    <li class="advgb-tab" data-tab="interactive-elements">
                                        <a><?php esc_html_e('Interactive', 'advanced-gutenberg'); ?></a>
                                    </li>
                                </ul>

                                <!-- Tab Content Wrapper -->
                                <div class="advgb-tab-body-wrapper">
                                    <?php
                                    $style_fields = AdvancedGutenbergBlockStyles::get_style_fields();
                                    foreach ($style_fields as $tab => $tab_config) : ?>
                                        <?php
                                            $active_style = $tab !== 'colors' ? 'display: none;' : '';
                                        ?>
                                        <div class="advgb-tab-body advgb-tab-content sub-tab-content" data-tab-content="<?php echo esc_attr($tab); ?>" style="<?php echo esc_attr($active_style); ?>">
                                            <div class="style-controls">
                                                <?php foreach ($tab_config['fields'] as $property => $field) : ?>
                                                    <?php echo AdvancedGutenbergBlockStyles::generate_control_group($field, $property); ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Global options -->
                <div style="text-align: center; margin-top: 15px; margin-bottom: 15px; clear: both;">
                    <form method="POST">
                        <?php wp_nonce_field('advgb_cstyles_nonce', 'advgb_cstyles_nonce_field'); ?>
                        <input type="hidden" name="save_custom_styles" value="1" />
                        <button class="button button-primary"
                                style="margin: 10px auto"
                                type="button"
                                id="save_custom_styles"
                                value="1"
                        >
                            <span><?php esc_html_e('Save styles', 'advanced-gutenberg') ?></span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>