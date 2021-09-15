<?php

namespace CM;

if (!class_exists('\CM\Settings')) {

    abstract class Settings {

        const NON_EXISTING_OPTION_DEFAULT = 0;
        const VERSION = 2;

        protected static $config = null;
        protected static $abbrev = null;
        protected static $dir = null;

        abstract public static function init();

        final public static function load_config() {
            $dir = static::$dir;
            $abbrev = static::$abbrev;
            include $dir . '/config.php';

            if (empty($config)) {
                wp_die('Missing config file!');
            }
            do_action($config['abbrev'] . '_after_config');
            static::$config[$abbrev] = apply_filters($config['abbrev'] . '_config', $config);
        }

        final public static function config($key = '') {
            if ($key) {
                return static::$config[static::$abbrev][$key] ?? static::NON_EXISTING_OPTION_DEFAULT;
            } else {
                return static::$config[static::$abbrev];
            }
        }

        /**
         * Abbrevs with the plugins own unique abbreviation
         * @param string $string
         * @return string
         */
        final public static function abbrev($string = '') {
            return static::config('abbrev') . $string;
        }

        public static function render() {
            $result = static::saveOptions();
            if (is_array($result) && isset($result['messages'])) {
                $messages = $result['messages'];
            }

            ob_start();
            include static::$dir . '/view.php';
            echo static::renderAssets();
            $content = ob_get_clean();
            return $content;
        }

        /**
         * Function responsible for saving the options
         */
        public static function saveOptions() {
            $messages = '';
            $post_prefiltered = filter_input_array(INPUT_POST);
            if (empty($post_prefiltered)) {
                return array('messages' => $messages);
            }
            $post = array_map('stripslashes_deep', $post_prefiltered);

            /*
             * By default save option happens only if <abb>_saveSettings key is in $post
             */
            $is_settings_save = apply_filters(static::abbrev('_is_save_options'), isset($post[static::abbrev('_saveSettings')]), $post);

            if ($is_settings_save) {
                check_admin_referer('update-options');

                do_action(static::abbrev('_save_options_before'), $post, array(&$messages));

                $options_names = apply_filters(static::abbrev('_thirdparty_option_names'), array_filter(array_keys($post), function ($k) {
                            return strpos($k, static::abbrev('_')) === 0;
                        }));

                $all_options = [];
                foreach ($options_names as $option_name) {
                    $option_value_prefiltered = isset($post[$option_name]) ? $post[$option_name] : static::NON_EXISTING_OPTION_DEFAULT;
                    $option_value = apply_filters(static::abbrev('_before_saving_option'), $option_value_prefiltered, $option_name);
//                update_option($option_name, static::sanitizeInput($option_name, $option_value));
                    $all_options[$option_name] = static::sanitizeInput($option_name, $option_value);
                }

                update_option(static::abbrev('_options'), $all_options);
                do_action(static::abbrev('_save_options_after_on_save'), $post, array(&$messages));
            }

            do_action(static::abbrev('_save_options_after'), $post, array(&$messages));

            return array('messages' => $messages);
        }

        /**
         * Get the value of the option for a plugin eg: CM_Settings::get('option_name');
         * @param string $option_name
         * @param various $default
         * @return various
         */
        public static function get($option_name, $default = null) {
            $config = static::config();
            $all_option = get_option(static::abbrev('_options'), []); //array of all plugin options
            /*
             * old method
             */
            $old_value = get_option($option_name);
            if (!empty($old_value)) {
                /*
                 * Only ever remove options if they are prefixed
                 */
                if (strpos($option_name, static::abbrev()) === 0) {
                    $all_option[$option_name] = $old_value;
                    update_option(static::abbrev('_options'), $all_option);
                    delete_option($option_name);
                } else {
                    $default = $old_value;
                }
            }
            $value = $all_option[$option_name] ?? $default;
            if (isset($all_option[$option_name])) {
                $value = $all_option[$option_name];
            } else {
                if (isset($config['default'][$option_name])) {
                    $value = $config['default'][$option_name];
                } else {
                    $value = $default;
                }
            }
            return apply_filters('cm_settings_get', $value, $option_name, $default);
        }

        /**
         * Sanitizes the inputs
         *
         * @param type $input
         */
        public static function sanitizeInput($optionName, $optionValue) {
            $pre_sanitized_value = apply_filters(static::abbrev('_before_sanitizing_option'), $optionValue, $optionName);

            if (!is_array($pre_sanitized_value)) {
                $sanitized_value = esc_attr($pre_sanitized_value);
            } else {
                $sanitized_value = $pre_sanitized_value;
            }

            return $sanitized_value;
        }

        /**
         * Function renders (default) or returns the setttings tabs
         *
         * @param type $return
         *
         * @return string
         */
        public static function renderSettingsTabsControls($return = false) {
            $content = '';
            $config = static::config();
            $settingsTabsArrayBase = $config['tabs'];

            $settingsTabsArray = apply_filters(static::abbrev('-settings-tabs-array'), $settingsTabsArrayBase);

            ksort($settingsTabsArray);

            if ($settingsTabsArray) {
                $content .= '<ul>';
                foreach ($settingsTabsArray as $tabKey => $tabLabel) {
                    $content .= '<li><a href="#tabs-' . $tabKey . '">' . $tabLabel . '</a></li>';
                }
                $content .= '</ul>';
            }

            if ($return) {
                return $content;
            }
            echo $content;
        }

        /**
         * Function renders (default) or returns the settings tabs
         *
         * @param type $return
         *
         * @return string
         */
        public static function renderSettingsTabs($return = false) {
            $content = '';
            $settingsTabsArrayBase = static::config('tabs');

            $settingsTabsArray = apply_filters(static::abbrev('-settings-tabs-array'), $settingsTabsArrayBase);

            if ($settingsTabsArray) {
                foreach ($settingsTabsArray as $tabKey => $tabLabel) {
                    $filterName = static::abbrev('-custom-settings-tab-content-') . $tabKey;

                    $config = static::config();
                    $tab_config = $config['presets']['default'][$tabKey] ?? [];

                    $tabContent = apply_filters($filterName, '');
                    if (!empty($tab_config) && is_array($tab_config)) {
                        foreach ($tab_config as $block_key => $block_config) {
                            $tabContent .= static::renderSettingsBlock($block_config);
                        }
                    }

                    if (!empty($tabContent)) {
                        $content .= '<div id="tabs-' . $tabKey . '">';
                        $content .= $tabContent;
                        $content .= '</div>';
                    }
                }
            }

            if ($return) {
                return $content;
            }
            echo $content;
        }

        public static function renderSettingsBlock($config) {
            if (empty($config)) {
                return '';
            }
            ob_start();
            ?>
            <div class="block">
                <?php if (!empty($config['label'])) : ?>
                    <h3><?php echo esc_attr($config['label']); ?></h3>
                <?php endif; ?>

                <?php if (!empty($config['before'])) : ?>
                    <?php echo do_shortcode($config['before']); ?>
                <?php endif; ?>

                <?php if (!empty($config['settings'])) : ?>
                    <table class="floated-form-table form-table">
                        <?php
                        foreach ($config['settings'] as $key => $setting_config) {
                            echo static::renderSetting($setting_config);
                        }
                        ?>
                    </table>
                <?php endif; ?>

                <?php if (!empty($config['after'])) : ?>
                    <?php echo do_shortcode($config['after']); ?>
                <?php endif; ?>
            </div>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        public static function renderSetting($config) {
            ob_start();
            ?>
            <tr valign="top" class="whole-line">
                <th scope="row"><?php echo esc_attr($config['label']); ?></th>
                <td>
                    <?php
                    if (isset($config['onlyin'])) {
                        echo static::renderOnlyin($config['onlyin']);
                    } else {
                        if (isset($config['html'])) {
                            echo do_shortcode($config['html']);
                        } else {
                            echo static::renderField($config);
                        }
                    }
                    ?>
                </td>
                <td colspan="2" class="cm_field_help_container"><?php echo esc_attr($config['description']); ?></td>
            </tr>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        public static function renderField($config) {
            if (empty($config['name'])) {
                wp_die('Setting is missing required "name" field!');
            }
            ob_start();
            ?>
            <input type="hidden" name="<?php echo esc_attr($config['name']); ?>" value="0" />
            <input type="checkbox" name="<?php echo esc_attr($config['name']); ?>" <?php checked(true, \CM\CMF_Settings::get($config['name'])); ?> value="<?php echo esc_attr($config['value']); ?>" />
            <?php
            $content = ob_get_clean();
            return $content;
        }

        public static function renderOnlyin($onlyin = 'Pro') {
            static $renderOnce = 0;
            ob_start();
            if (!$renderOnce):
                ?>
                <style>
                    .onlyinpro * {
                        color: #aaa !important;
                    }
                    .onlyinpro {
                        color: #aaa !important;
                    }
                    .onlyinpro.hide {
                        display: none !important;
                    }
                </style>
                <?php
                $renderOnce = 1;
            endif;
            ?>
            <div class="onlyinpro">Available in <?php echo esc_attr($onlyin); ?> version and above. <a href="<?php echo admin_url('admin.php?page=' . static::abbrev('_pro')); ?>" target="">UPGRADE NOW&nbsp;➤</a></div>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        public static function renderAssets() {
            ob_start();
            ?>
            <style>
                div.cminds_settings_description {
                    float: left;
                    max-width: 55%;
                    margin-bottom: 20px;
                }
                .cminds_settings_description .button.cm_cleanup_button {
                    color: #A00;
                }
                .cminds_settings_description .button.cm_cleanup_button:hover {
                    color: #F00;
                }
                .admin-tt {
                    z-index: 9999;
                }
                #cm_settings_tabs .block {
                    border: 1px solid grey;
                    border-radius: 13px;
                    padding: 20px;
                    margin: 5px;
                    float: none;
                    width: auto;
                }
                #cm_settings_tabs .block h3 {
                    padding-top: 0;
                    margin-top: 0;
                }
                #cm_settings_tabs table th {
                    position: relative;
                    padding-right: 25px;
                }
                .floated-form-table,
                .floated-form-table tr {
                    clear: none;
                }
                .floated-form-table tr {
                    float: left;
                    width: 49%;
                }
                .floated-form-table tr.whole-line {
                    width: 99%;
                }
                .cm_field_help,
                .cm_help {
                    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAMOSURBVHjaPMtPbFN1AMDx7+/33mu7dl03RiddCWywKdgYYmSIxrFMXJTMBD1wMVzcSRNNzLh4MHLSA4lGQmLkYiQSMg3JhhFRxzBKRtgOZMbIcIibazpwpWu7177/7/08mPi5f8RvJQ8AhegJ/Ghwa0rGpeDDREwkNAENR+FH0bmle6vz1erGVL1WMXP57eTy29H5T49U6ot8uza0NS2RUuABgYJcK0jUWymjm5sLjZ8qlXAUsAF0pegQSr3fm9WGknGNOw34swJWw8Z2FI6fYEdGMvxokpFnCsOXZhrfR0qNAg3pudFALiNfjxkaF27B51dsjPUyIzmPV/t89ndafHu9yckJDycyGDpQOBSGwZQQIiuTcfFuZ1rn4lzE+csmO0WRFwfauDTvcvZKjSd3xTi8x+TaXIkzU2Vy2Q4SIjxcq1WPibUNP9Cl1N7+rELpQZX9vTb5rnbOTT/EbDpMn3qcTVvnzdPLJBM6Z8f7Uc4a878Xy3q6RbPmFxvp4loFlMfMrwFBuI6KFK8dSrIr38qpifvYjo8fBCyubHKwT6e28TApdU1QN20syyIKI+IGaFIyXIg4Obab81erXLj6D/EYBIGP2XRAgWXWI2m5YSq3RYLyUUqhlEBFPq88t4VS1eCTi6toukAiUGFAV0bD9UP8IEA2XT4t9CTZ2SVp2h6ahJih89WsxwdfrhIpiOsSx/XoysDA3nYW75YwjFhSLv21cj0eF7zx8jaiwKXpBEDIgT7BC0+1kTAEtuNh2zZjRx6hI23wy42b9Pb3z0qhx72FP9Z56dkc7x3vpkVz2DSbtLeEdHfomA0LGdmcONbF8SN9TEz+gO24d9PpzKhY+NuiUqmMJ6X/0cF9eZaW63w9s0qx7GEYBt2dcY4ObqPQl2Xy8jV+nPmZwedH3tF147S4tWyyWa9Tq9XGvUZ1bN+eHYVsRytSNzB0A6UCVor3+W56lgflMnsLT5xIt7V/DPB/th2Le3dut7ak0pMiCp/2nAaua2GaNk3bIdWanOvd/dgZ33e/icUSSCn5dwCBU3Hcr3rapwAAAABJRU5ErkJggg==');
                    width: 15px;
                    height: 15px;
                    display: inline-block;
                    cursor: pointer;
                }
                .cm_field_help {
                    margin: 0 -20px 0 0;
                    float: right;
                }
                .cm_field_help:hover,
                .cm_help:hover {
                    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAALsSURBVHjaZJBdaFt1HIaf8885SZo0ttU1Lu2oLe10I4ypTNyEblSYWCp4JYjsxg1FygTZsN6I+AFeCAoyENyVgkMZQqeoqFsrTje2LNPCYB917eratdIsaZJzcr7P+XkxVhBfeC7eiwdeXu3SLR8AQesPg3h4Q1allMb76aSWTmhguUIQx5/Pzt0sra3VTjTqVbPQu4lC7yZ07qRfiXzW25nYsyGnUErDB0KBQjso5GDW6OHcjPVLtRqNAQ6ALkKXJvLWQHdiTyaV4KoF16tgWw6OK7hBmr4OxciDGfbuKo58M2X9GIuMARblOfep281Agkjk2EWR14/bEgr/YfxTSya+8KTWEllercnXpy6eunB9rZvLS960iMiXZyMZfbf5P/EuO16dl3eOrYqIyHdTJTlZnh9XnRm1u9KIOXG6illb4W4ePlBm8Pnf17sWmkyXK1xbChjsy7O8dOttPdeWsEtXrNzichXE55EDZcIoRuKIF3Zn1mXHDQjCkCsLTXYO6dRrtzNKT2g0TAfbtomjmJQBCaUYKca8N/7onRUvlkglIQwDzJYLArbZiJXtRdnCvQokQEQQ0ZA44MgbuwDY8dIFErqGQkOikHxHAi+ICMIQveXxSbE/c/CBvGJuxSeTNkgaOi9/cAkwiMUkbSgc1yffAY9t7aRU/hPDSGbU7PzCb6mUxivPbCQOPVpuCEQcndjG0YktpA0Nx/VxHIf9o/fTlTM4ffYcA5s3n1GanvJnrq3y9BMF3tzXQ1vCpWm21o8yLRsVOxx+Ls++0SG+mvwJx/X+yuU6xrSZv22q1eqhjAo+3Lm9l9kbDY5P3WSx4mMYBj33pXh2eCPFoW4mv5/m56lfGX5y72u6bnys/XHDpNloUK/XD/nW2v7tW/qK3V3tKN3A0A1EQhYWV/jh5Bn+qVTYWtx2OHdP50cA67Lj2sxdvdzels1NanH0uO9aeJ6NaTq0HJdse+b8wOBDR4LA+zaZTKOU4t8BAMeWkeCMnZOsAAAAAElFTkSuQmCC');
                }
                .cm_help {
                    margin-right: 5px;
                }
                .ui-tabs-anchor:focus,
                .ui-tabs-anchor:active {
                    outline: none;
                    box-shadow: none;
                }
            </style>
            <script>
                (function ($) {
                    $(function () {
                        if ($.fn.tabs) {
                            $('#cm_settings_tabs').tabs({
                                activate: function (event, ui) {
                                    window.location.hash = ui.newPanel.attr('id').replace(/-/g, '_');
                                },
                                create: function (event, ui) {
                                    var tab = location.hash.replace(/\_/g, '-');
                                    var tabContainer = $(ui.panel.context).find('a[href="' + tab + '"]');
                                    if (typeof tabContainer !== 'undefined' && tabContainer.length)
                                    {
                                        var index = tabContainer.parent().index();
                                        $(ui.panel.context).tabs('option', 'active', index);
                                    }
                                }
                            });
                        }

                        $('.cm_field_help_container').each(function () {
                            var newElement,
                                    element = $(this);

                            newElement = $('<div class="cm_field_help"></div>');
                            newElement.attr('title', element.html());

                            if (element.siblings('th').length)
                            {
                                element.siblings('th').append(newElement);
                            } else
                            {
                                element.siblings('*').append(newElement);
                            }
                            element.remove();
                        });

                        $('.cm_field_help').tooltip({
                            show: {
                                effect: "slideDown",
                                delay: 100
                            },
                            position: {
                                my: "left top",
                                at: "right top"
                            },
                            content: function () {
                                var element = $(this);
                                return element.attr('title');
                            },
                            close: function (event, ui) {
                                ui.tooltip.hover(
                                        function () {
                                            $(this).stop(true).fadeTo(400, 1);
                                        },
                                        function () {
                                            $(this).fadeOut("400", function () {
                                                $(this).remove();
                                            });
                                        });
                            }
                        });
                    });
                })(jQuery);
            </script>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        /**
         * Flushes the rewrite rules to reflect the permalink changes automatically (if any)
         *
         * @global type $wp_rewrite
         */
        public static function _flush_rewrite_rules() {
            global $wp_rewrite;
            // First, we "add" the custom post type via the above written function.

            do_action(static::abbrev('_flush_rewrite_rules'));

            // Clear the permalinks
            flush_rewrite_rules();

            //Call flush_rules() as a method of the $wp_rewrite object
            $wp_rewrite->flush_rules();
        }

    }

}