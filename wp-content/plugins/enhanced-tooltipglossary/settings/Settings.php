<?php

namespace CMTT;

include_once plugin_dir_path(__FILE__) . 'SettingsView.php';

abstract class Settings {

    const NON_EXISTING_OPTION_DEFAULT = 0;
    const VERSION = 5;

    protected static $config = null;
    protected static $abbrev = null;
    protected static $dir = null;
    protected static $settingsPageSlug = null;

    abstract public static function init();

    final public static function load_config() {
        $dir = static::$dir;
        $abbrev = static::$abbrev;
        include $dir . '/config.php';

        if (empty($config)) {
            wp_die('Missing config file!');
        }
        do_action(static::$abbrev . '_after_config');
        /*
         * If you want to add addon Settings use the filter below
         * It HAS to follow the structure of the main plugins config file (config.php)
         */
        static::$config[$abbrev] = apply_filters(static::$abbrev . '_config', $config);
    }

    final public static function config($key = '') {
        if (empty(static::$config)) {
            static::load_config();
        }
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
        return static::$abbrev . $string;
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
     * Option allowing to store single setting
     * @param type $option_name
     * @param type $value
     */
    public static function set($option_name, $value) {
        $all_option = get_option(static::abbrev('_options'), []);
        $all_option[$option_name] = $value;
        update_option(static::abbrev('_options'), $all_option, false);
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
                $all_options[$option_name] = static::sanitizeInput($option_name, $option_value);
            }


            update_option(static::abbrev('_options'), $all_options, false);
            do_action(static::abbrev('_save_options_after_on_save'), $post, array(&$messages));
        }

        do_action(static::abbrev('_save_options_after'), $post, array(&$messages));

        return array('messages' => $messages);
    }

    /**
     * Get the config of the option for a plugin eg: CM_Settings::getConfig('option_name');
     * @param type $option_name
     */
    public static function getConfig($option_name) {
        $config = static::config();
        if (isset($config['settings'][$option_name])) {
            return $config['settings'][$option_name];
        }
        return false;
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
         * Check if option already exists in new array
         */
        if (isset($all_option[$option_name])) {
            $value = $all_option[$option_name];
        } else {
            /*
             * Check the value with old method
             */
            $old_value = get_option($option_name, null);
            if (null !== $old_value) {
                /*
                 * If they are prefixed:
                 * 1) use old value as value
                 * 2) update new value in the array
                 * 3) remove old value
                 */
                if (strpos($option_name, static::abbrev()) === 0) {
                    $value = $all_option[$option_name] = $old_value;
                    update_option(static::abbrev('_options'), $all_option, false);
                    delete_option($option_name);
                } else {
                    /*
                     * Non-prefixed options (not coming from plugin), we use as they are
                     */
                    $value = $old_value;
                }
            } else {
                /*
                 * No old value use one of the defaults with the following order
                 * 1) config
                 * 2) argument
                 */
                if (isset($config['settings'][$option_name]['value'])) {
                    $value = $config['settings'][$option_name]['value'];
                } else {
                    $value = $default;
                }
                /*
                 * Store the default value in DB (since version 5)
                 */
                $all_option[$option_name] = $value;
                update_option(static::abbrev('_options'), $all_option, false);
            }
        }
        static::maybei18n($value, $option_name);
        return apply_filters('cm_settings_get', $value, $option_name, $default);
    }

    /**
     * @param type mixed
     * @param type string
     * @return type $value
     */
    public static function maybei18n(&$value, $option_name) {
        $option = static::getConfig($option_name);
        if (!empty($option['type']) && $option['type'] == 'label') {
            $value = __($value, static::abbrev());
        }
        return $value;
    }

    /**
     * Sanitizes the inputs
     *
     * @param type $input
     */
    public static function sanitizeInput($optionName, $optionValue) {
        $pre_sanitized_value = apply_filters(static::abbrev('_before_sanitizing_option'), $optionValue, $optionName);

        $optionType = static::getConfig($optionName)['type'] ?? '';

        if (!is_array($pre_sanitized_value) && $optionType != 'textarea' && $optionType != 'rich_text') {
            $sanitized_value = esc_attr($pre_sanitized_value);
        } else {
            $sanitized_value = $pre_sanitized_value;
        }

        return $sanitized_value;
    }

    /**
     * Function renders (default) or returns the settings tabs
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
            $content .= '<ul class="settings-tabs-wrapper">';
            foreach ($settingsTabsArray as $tabKey => $tabSpecifications) {
                $isActive = (\array_key_first($settingsTabsArray) === $tabKey) ? 'settings-tabs-active' : '';
                $tabName = $tabSpecifications['tab_name'] ?? $tabSpecifications;
                $content .= '<li class="settings-tabs-item ' . $isActive . '">
                                    <a href="#tabs-' . $tabKey . '" class="settings-tabs-link">' . $tabName . '</a>
                                 </li>';
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
            foreach ($settingsTabsArray as $tabKey => $tabSpecifications) {
                $filterName = static::abbrev('-custom-settings-tab-content-') . $tabKey;

                $tabContent = apply_filters($filterName, '');

                if (!empty($tabSpecifications) && !empty($tabSpecifications['section'])) {
                    foreach ($tabSpecifications['section'] as $section_key => $section_name) {
                        $tabContent .= static::renderSettingsBlock([$section_key => $section_name], $tabKey);
                    }
                }

                if (!empty($tabContent)) {
                    $content .= '<div id="tabs-' . $tabKey . '" class="settings-tab">';
                    $content .= '<div class="cminds_settings_toggle_tabs cminds_settings_toggle-opened">Toggle All</div>';
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

    public static function renderSettingsBlock($section, $tabKey) {
        if (empty($section)) {
            return '';
        }

        $settings = static::config('settings');

        ob_start();
        ?>
        <div class="block" id="<?php echo sanitize_title($section[key($section)]) ?>">
            <?php if (!empty($section[key($section)])) : ?>
                <h3 class="section-title"><span><?php echo esc_attr($section[key($section)]); ?></span> <?php self::showTabArrow(); ?></h3>
            <?php endif; ?>
            <?php if (!empty($settings)) : ?>
                <table class="floated-form-table form-table">
                    <?php
                    foreach ($settings as $name => $config) {
                        if ($tabKey == $config['tab'] && key($section) == $config['section']) {
                            echo static::renderSetting($name, $config);
                        }
                    }
                    ?>
                </table>
            <?php endif; ?>
        </div>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    public static function renderSetting($key, $config) {
        ob_start();
        $class = apply_filters(static::abbrev('-settings-item-class'), '', $key);
        ?>
        <tr valign="top"
            class="<?php echo $class; ?> <?php echo $key; ?> wrapper-<?php echo esc_attr($config['type']); ?>">
            <th scope="row">
                <div><?php echo esc_attr($config['label']); ?></div>
            </th>
            <td class="field-<?php echo esc_attr($config['type']); ?>">
                <?php
                if (isset($config['onlyin'])) {
                    echo static::renderOnlyin($config['onlyin']);
                } else {
                    if (isset($config['html'])) {
                        echo do_shortcode($config['html']);
                    } else {
                        echo static::renderField($key, $config);
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

    public static function renderField($key, $config) {
        if (empty($config['name'])) {
            $config['name'] = $key;
            //                wp_die('Setting is missing required "name" field!');
        }
        /**
         * Here we're already having the value for the field from DB
         */
        $config['value'] = static::get($config['name'], $config['value']);
        $content = SettingsView::renderOptionControls($key, $config);
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
                /*border: 1px solid grey;*/
                border-radius: 13px;
                padding: 20px;
                margin: 5px;
                float: none;
                width: auto;
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
            .floated-form-table tr.whole-line th {
                width: calc(25% - 25px);
            }
            .floated-form-table tr.whole-line td {
                width: calc(75% - 25px);
            }
            .floated-form-table tr.whole-line td > * {
                margin: 0 10px
            }
            .cm_field_help,
            .cm_help {
                background-image: url("data:image/svg+xml,%3Csvg width='11' height='11' viewBox='0 0 11 11' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M5.5 11C2.46243 11 0 8.53757 0 5.5C0 2.46243 2.46243 0 5.5 0C8.53757 0 11 2.46243 11 5.5C11 8.53757 8.53757 11 5.5 11ZM4.86908 6.85128H6.02117C6.02655 6.64718 6.05474 6.47866 6.10577 6.34572C6.1568 6.21279 6.2481 6.08321 6.3797 5.95699L6.84295 5.52596C7.039 5.33529 7.18133 5.14864 7.26995 4.96602C7.35858 4.78341 7.40289 4.58065 7.40289 4.35775C7.40289 3.84749 7.2384 3.45406 6.90942 3.17745C6.58044 2.90084 6.11652 2.76253 5.51763 2.76253C4.91607 2.76253 4.44879 2.91225 4.11578 3.21169C3.78277 3.51113 3.61358 3.9294 3.60821 4.46651H4.96978C4.97515 4.26509 5.02752 4.10531 5.12689 3.98714C5.22625 3.86898 5.3565 3.8099 5.51763 3.8099C5.86676 3.8099 6.04132 4.01265 6.04132 4.41817C6.04132 4.58468 5.98962 4.73708 5.88623 4.87539C5.78283 5.01369 5.63177 5.1661 5.43304 5.3326C5.23431 5.49911 5.09063 5.69582 5.00201 5.92275C4.91339 6.14968 4.86908 6.45919 4.86908 6.85128ZM4.70391 8.07589C4.70391 8.27462 4.77575 8.43776 4.91943 8.56533C5.06311 8.69289 5.24237 8.75667 5.45721 8.75667C5.67206 8.75667 5.85131 8.69289 5.99499 8.56533C6.13867 8.43776 6.21051 8.27462 6.21051 8.07589C6.21051 7.87716 6.13867 7.71401 5.99499 7.58645C5.85131 7.45888 5.67206 7.3951 5.45721 7.3951C5.24237 7.3951 5.06311 7.45888 4.91943 7.58645C4.77575 7.71401 4.70391 7.87716 4.70391 8.07589Z' fill='%236BC07F'/%3E%3C/svg%3E%0A");
                min-width: 15px;
                height: 15px;
                display: inline-block;
                cursor: pointer;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
            .cm_field_help:hover,
            .cm_help:hover {
                background-image: url("data:image/svg+xml,%3Csvg width='11' height='11' viewBox='0 0 11 11' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M5.5 11C2.46243 11 0 8.53757 0 5.5C0 2.46243 2.46243 0 5.5 0C8.53757 0 11 2.46243 11 5.5C11 8.53757 8.53757 11 5.5 11ZM4.86908 6.85128H6.02117C6.02655 6.64718 6.05474 6.47866 6.10577 6.34572C6.1568 6.21279 6.2481 6.08321 6.3797 5.95699L6.84295 5.52596C7.039 5.33529 7.18133 5.14864 7.26995 4.96602C7.35858 4.78341 7.40289 4.58065 7.40289 4.35775C7.40289 3.84749 7.2384 3.45406 6.90942 3.17745C6.58044 2.90084 6.11652 2.76253 5.51763 2.76253C4.91607 2.76253 4.44879 2.91225 4.11578 3.21169C3.78277 3.51113 3.61358 3.9294 3.60821 4.46651H4.96978C4.97515 4.26509 5.02752 4.10531 5.12689 3.98714C5.22625 3.86898 5.3565 3.8099 5.51763 3.8099C5.86676 3.8099 6.04132 4.01265 6.04132 4.41817C6.04132 4.58468 5.98962 4.73708 5.88623 4.87539C5.78283 5.01369 5.63177 5.1661 5.43304 5.3326C5.23431 5.49911 5.09063 5.69582 5.00201 5.92275C4.91339 6.14968 4.86908 6.45919 4.86908 6.85128ZM4.70391 8.07589C4.70391 8.27462 4.77575 8.43776 4.91943 8.56533C5.06311 8.69289 5.24237 8.75667 5.45721 8.75667C5.67206 8.75667 5.85131 8.69289 5.99499 8.56533C6.13867 8.43776 6.21051 8.27462 6.21051 8.07589C6.21051 7.87716 6.13867 7.71401 5.99499 7.58645C5.85131 7.45888 5.67206 7.3951 5.45721 7.3951C5.24237 7.3951 5.06311 7.45888 4.91943 7.58645C4.77575 7.71401 4.70391 7.87716 4.70391 8.07589Z' fill='%234A8B5A'/%3E%3C/svg%3E%0A");
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

    public static function showTabArrow() {
        echo '<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="#6BC07F">
                        <path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"/>
                    </svg>';
    }

    public static function enqueueAssets($page) {

        if (static::$settingsPageSlug != $page) {
            return;
        }

        $baseurl = plugin_dir_url(__FILE__) . '/assets/';
        wp_enqueue_style(self::abbrev('settings-select2-css'), $baseurl . '/css/select2.min.css');
        wp_enqueue_style(self::abbrev('settings-css'), $baseurl . '/css/settings.css', [self::abbrev('settings-select2-css')]);
        wp_enqueue_script(self::abbrev('settings-select2-js'), $baseurl . '/js/select2.min.js', ['jquery']);
        wp_enqueue_script(self::abbrev('settings-js'), $baseurl . '/js/settings.js', ['jquery', self::abbrev('settings-select2-js')]);
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
    }

}
