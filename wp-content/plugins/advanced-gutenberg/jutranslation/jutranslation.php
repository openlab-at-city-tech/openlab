<?php
/**
 * Jutranslation main file
 *
 * @package Joomunited\ADVGB\Jutranslation
 */

namespace Joomunited\ADVGB\Jutranslation;

/**
 * Class Jutranslation
 */
class Jutranslation
{
    /**
     * Extension slug
     *
     * @var string $extension_slug
     */
    public static $extension_slug;

    /**
     * Extension real name
     *
     * @var string $extension_name
     */
    public static $extension_name;

    /**
     * Extension text domain
     *
     * @var string $text_domain
     */
    public static $text_domain;

    /**
     * Language file
     *
     * @var string $language_file
     */
    public static $language_file;

    /**
     * The main plugin php file
     *
     * @var string $main_plugin_file
     */
    public static $main_plugin_file;

    /**
     * Initialize Jutranslation
     *
     * @param string $main_plugin_file Main plugin file
     * @param string $extension_slug   Extension slug
     * @param string $extension_name   Extension name
     * @param string $text_domain      Text domain
     * @param string $language_file    Language file
     *
     * @return void
     */
    public static function init($main_plugin_file, $extension_slug, $extension_name, $text_domain, $language_file)
    {
        self::$main_plugin_file = $main_plugin_file;
        self::$extension_slug   = $extension_slug;
        self::$extension_name   = $extension_name;
        self::$text_domain      = $text_domain;
        self::$language_file    = plugin_dir_path(self::$main_plugin_file) . $language_file;

        //Load override file
        add_action('load_textdomain', array(__CLASS__, 'overrideLanguage'), 1, 2);

        add_filter(self::$extension_slug . '_get_addons', function ($addons) {
            $addon                          = new \stdClass();
            $addon->main_plugin_file        = Jutranslation::$main_plugin_file;
            $addon->extension_name          = Jutranslation::$extension_name;
            $addon->extension_slug          = Jutranslation::$extension_slug;
            $addon->text_domain             = Jutranslation::$text_domain;
            $addon->language_file           = Jutranslation::$language_file;
            $addons[$addon->extension_slug] = $addon;
            return $addons;
        });

        //Only need Jutranslation on admin side
        if (!is_admin()) {
            return;
        }

        // Check if the current user
        add_action('admin_init', function () {
            if (current_user_can('manage_options')) {
                //Initialize needed ajax mehtods
                add_action(
                    'wp_ajax_jutranslation_' . Jutranslation::$extension_slug,
                    array(__CLASS__, 'dispatchQuery')
                );
            }
        });
    }

    /**
     * Ajax queries dispatcher
     *
     * @return void
     */
    public static function dispatchQuery()
    {
        check_ajax_referer('jutranslation', 'wp_nonce');

        if (!isset($_REQUEST['task'])) {
            die;
        }

        switch ($_REQUEST['task']) {
            case 'jutranslation.saveStrings':
                self::saveStrings();
                break;
            case 'jutranslation.getTranslation':
                self::getTranslation();
                break;
            case 'jutranslation.showViewForm':
                self::showViewForm();
                break;
        }
    }

    /**
     * Return the main html content for jutranslation
     *
     * @return mixed
     */
    public static function getInput()
    {
        echo '<div id="jutranslation" class="wordpress">';
        echo '<div class="control-group">';

        //Declare all js and css to include
        wp_enqueue_script(
            'jutranslation',
            plugin_dir_url(self::$main_plugin_file) . 'jutranslation/assets/js/jutranslation.js',
            array(),
            ADVANCED_GUTENBERG_VERSION
        );
        wp_enqueue_style(
            'jutranslation',
            plugin_dir_url(self::$main_plugin_file) . 'jutranslation/assets/css/jutranslation.css'
        );
        wp_localize_script('jutranslation', 'jutranslation', array(
            'token'       => wp_create_nonce('jutranslation'),
            'ajax_action' => 'jutranslation_' . self::$extension_slug,
            'base_url'    => admin_url('admin-ajax.php') . '?'
        ));

        add_thickbox();

        //Get all installed languages
        $installed_languages = array();
        foreach (wp_get_installed_translations('core') as $type) {
            foreach ($type as $lang => $value) {
                $lang                  = str_replace('_', '-', $lang);
                $installed_languages[] = $lang;
            }
        }

        //Add Polylang languages
        if (function_exists('pll_languages_list')) {
            foreach (pll_languages_list(array('fields' => 'locale')) as $pll_language) {
                $lang                  = str_replace('_', '-', $pll_language);
                $installed_languages[] = $lang;
            }
        }

        //Add WPML languages
        if (!function_exists('pll_languages_list') && function_exists('icl_get_languages')) {
            foreach (icl_get_languages() as $wpml_language) {
                $lang                  = str_replace('_', '-', $wpml_language['default_locale']);
                $installed_languages[] = $lang;
            }
        }

        //Add default en-US language
        if (!isset($installed_languages['en-US'])) {
            $installed_languages = array_merge(array('en-US'), $installed_languages);
        }

        $installed_languages = array_unique($installed_languages);

        //Get addons
        $addons = apply_filters(self::$extension_slug . '_get_addons', array());
        ksort($addons);

        $languages = array();
        foreach ($installed_languages as $installed_language) {
            foreach ($addons as $addon) {
                $langObject                    = new \stdClass();
                $langObject->extension         = $addon;
                $langObject->installed         = false;
                $langObject->extension_version = '';
                $langObject->language_version  = '';
                $langObject->revision          = '1';
                $langObject->languageCode      = $installed_language;
                $langObject->modified          = '0';

                $languages[] = $langObject;
            }
        }

        //Check if language is installed
        foreach ($languages as &$language) {
            if (str_replace('-', '_', $language->languageCode) === 'en_US') {
                $file = $language->extension->language_file;
            } else {
                $file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'plugins';
                $file .= DIRECTORY_SEPARATOR . $language->extension->text_domain . '-';
                $file .= str_replace('-', '_', $language->languageCode) . '.mo';
            }

            if (file_exists($file)) {
                $language->installed = true;

                // Load language file
                $mo = new \MO();
                $mo->import_from_file($file);

                //Assign it to the language
                if (isset($mo->headers['Version'])) {
                    $language->language_version = $mo->headers['Version'];
                }
                if (isset($mo->headers['Revision'])) {
                    $language->revision = $mo->headers['Revision'];
                }
                if (isset($mo->headers['Modified'])) {
                    $language->modified = $mo->headers['Modified'];
                } else {
                    //The header has not been found, so the language file has not been add by jutranslation
                    $language->modified = '1';
                }
            }

            //Check for language override
            $language->overrided = 0;
            $file                = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'plugins';
            $file                .= DIRECTORY_SEPARATOR . $language->extension->text_domain . '-';
            $file                .= str_replace('-', '_', $language->languageCode) . '.override.mo';
            if (file_exists($file)) {
                // Load language file
                $mo = new \MO();
                $mo->import_from_file($file);

                $language->overrided = 0;

                //Check if a translation exists for each entry
                foreach ($mo->entries as $entry) {
                    if (is_countable($entry->translations)) { // phpcs:ignore -- We created it in line 198
                        if (count($entry->translations)) {
                            $language->overrided++;
                        }
                    }
                }
            }
        }
        unset($language);

        //Show documentation link
        echo '<p>You can refer to the 
        <a href="https://www.joomunited.com/documentation/ju-translation-translate-wordpress-and-joomla-extensions" 
        target="_blank">documentation page on Joomunited</a> for more informations about extension translation</p>';

        echo '<p>';
        foreach ($addons as $addon) {
            //Get extension version
            $plugin_data       = get_plugin_data($addon->main_plugin_file);
            $extension_version = $plugin_data['Version'];

            echo 'Current ' . esc_html($addon->extension_name) . ' version is ' . esc_html($extension_version) . '<br/>';
        }
        echo '</p>';

        $julanguages = array();

        echo '<table id="jutranslations-languages" class="table table-striped" >
                    <thead >
                        <tr>
                            <th ' . (count($addons) > 1 ? '' : 'style="display:none;"') . '>Plugin</th>
                            <th>Language</th>
                            <th>Current version</th>
                            <th>Latest available version</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($languages as $language) {
            echo '<tr data-slug="' . esc_attr($language->extension->extension_slug) . '" data-lang="' . esc_attr($language->languageCode) .
                 '" data-installed="' . esc_attr($language->installed) . '" data-version="' . esc_attr($language->extension_version) . '">';
            echo '<td ' . (count($addons) > 1 ? '' : 'style="display:none;"') . '>' .
                 esc_html($language->extension->extension_name) . '</td>';
            echo '<td>' . esc_html($language->languageCode) . '</td>';
            echo '<td class="current_version">' .
                 esc_html($language->installed ? ($language->language_version ? ($language->revision ? esc_html($language->language_version .
                                                                                                                ' rev' . $language->revision) : $language->language_version . ' rev' .
                                                                                                                                                $language->revision) : 'Unknown') : 'Not installed') . '</td>';
            echo '<td><div class="original_content">';
            echo '<span class="latest_version"><img src="' .
                 esc_attr(plugin_dir_url(self::$main_plugin_file) . 'jutranslation/assets/images/radio.svg') .
                 '" alt="loading"/></span><br/>';
            echo '<a class="jutranslation-override" href="#" data-language="' . esc_attr($language->languageCode) .
                 '">Override (<span class="jutranslation-override-count">' . esc_html($language->overrided) . '</span>)</a> ';
            if ($language->languageCode !== 'en-US') {
                //Reference en-US file can't be modified
                echo '<a class="jutranslation-edition" href="#" data-language="' .
                     esc_attr($language->languageCode) . '">Edit original file</a>';
            }

            //No sharing for en-US
            if ($language->languageCode !== 'en-US') {
                echo ' <a class="jutranslation-share" style="' . (($language->modified === '0') ? 'display:none' : '') .
                     '" href="#" data-language="' . esc_attr($language->languageCode) . '">Share with Joomunited</a>';
            }
            echo '</div><div class="temporary_content"></div></td>';
            echo '</tr>';

            if (!isset($julanguages[$language->extension->extension_slug])) {
                $plugin_data = get_plugin_data($language->extension->main_plugin_file);

                $julanguages[$language->extension->extension_slug]                      = array();
                $julanguages[$language->extension->extension_slug]['extension']         = $language->extension->extension_slug;
                $julanguages[$language->extension->extension_slug]['extension_version'] = $plugin_data['Version'];
                $julanguages[$language->extension->extension_slug]['language_version']  = $language->language_version;
                $julanguages[$language->extension->extension_slug]['languages']         = array();
                $julanguages[$language->extension->extension_slug]['versions']          = array();
                $julanguages[$language->extension->extension_slug]['revisions']         = array();
            }
            $julanguages[$language->extension->extension_slug]['languages'][]                        = $language->languageCode;
            $julanguages[$language->extension->extension_slug]['versions'][$language->languageCode]  = $language->language_version;
            $julanguages[$language->extension->extension_slug]['revisions'][$language->languageCode] = $language->revision;
        }
        echo '</tbody>
                  </table>';

        echo '<script type="text/javascript">julanguages = ' . json_encode($julanguages) . ';</script>';
        echo '</div></div>';
    }

    /**
     * Save a post translation for a given language
     *
     * @return void
     */
    protected static function saveStrings()
    {

        //Security check
        if (!wp_verify_nonce($_REQUEST['wp_nonce'], 'jutranslation')) {
            echo json_encode(array('status' => 'error', 'message' => 'nonce error'));
            die();
        }

        //Get and check language
        $language = $_POST['language'];
        if (!$language) {
            echo json_encode(array('status' => 'error', 'message' => 'language empty'));
            die();
        }
        if (!preg_match('/^[a-z]{2,3}(?:-[a-zA-Z]{4})?(?:-[A-Z]{2,3})?$/', $language)) {
            echo json_encode(array('status' => 'error', 'message' => 'invalid language code'));
            die();
        }

        $plugin = $_POST['slug'];
        if (!$plugin) {
            echo json_encode(array('status' => 'error', 'message' => 'plugin empty'));
            die();
        }
        if (!preg_match('/^[a-zA-Z-_]+$/', $plugin)) {
            echo json_encode(array('status' => 'error', 'message' => 'invalid plugin slug'));
            die();
        }

        $addons = apply_filters(self::$extension_slug . '_get_addons', array());
        if (!isset($addons[$plugin])) {
            echo json_encode(array('status' => 'error', 'message' => 'plugin not found'));
            die();
        }

        //Get the file to write to
        $destination       = $_POST['destination'];
        $file              = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'plugins';
        $file              .= DIRECTORY_SEPARATOR . $addons[$plugin]->text_domain . '-' . str_replace('-', '_', $language);
        $extension_version = '';
        $revision          = '';
        switch ($destination) {
            case 'override':
                $file .= '.override.mo';
                break;
            case 'edition':
                //Disallow editing main en-US file
                if ($language === 'en-US') {
                    echo json_encode(
                        array('status' => 'error', 'message' => 'editing main reference file not allowed')
                    );
                    die();
                }

                $file .= '.mo';

                //Get informations about previous installed file
                // Load language file
                $mo = new \MO();
                $mo->import_from_file($file);

                //Get the file version
                if (isset($mo->headers['Version']) && $mo->headers['Version']) {
                    $extension_version = $mo->headers['Version'];
                } else {
                    //Use the current extension version
                    $plugin_data       = get_plugin_data($addons[$plugin]->main_plugin_file);
                    $extension_version = $plugin_data['Version'];
                }

                //Get the file revision
                if (isset($mo->headers['Revision'])) {
                    $revision = $mo->headers['Revision'];
                } else {
                    //Use the current extension version
                    $revision = (int) $_POST['revision'];
                }
                break;
            default: //Case new language version installation from Joomunited
                //Get the version
                $extension_version = $_POST['extension_version'];
                if (!$extension_version) {
                    echo json_encode(array('status' => 'error', 'message' => 'version empty'));
                    die();
                }

                //Get revision
                $revision = (int) $_POST['revision'];

                $file .= '.mo';
                break;
        }

        //Check version number
        if ($destination !== 'override' &&
            !preg_match('/^([0-9]+)\.([0-9]+)(\.([0-9]+))?(\.([0-9]+))?$/', $extension_version)) {
            echo json_encode(array('status' => 'error', 'message' => 'invalid version number'));
            die();
        }
        $modified = 0;
        if (isset($_POST['modified'])) {
            $modified = $_POST['modified'];
        }

        //Get strings and remove slashes auto added by WP
        $strings = stripslashes($_POST['strings']);

        //Check if strings is a valid array
        $strings = json_decode($strings);
        if ($strings === false || !is_object($strings) || !count((array) $strings)) {
            $strings = new \stdClass();
        }

        // Load translation class
        $mo = new \MO();

        //Generate the file header
        if ($destination !== 'override') {
            $mo->headers['Version']  = $extension_version;
            $mo->headers['Revision'] = $revision;
            $mo->headers['Modified'] = (int) $modified;
        }

        foreach ($strings as $code => $string) {
            //Only save reference language empty strings
            if ($string !== '' || $language === 'en-US') {
                $entry              = &$mo->make_entry($code, $string);
                $mo->entries[$code] = &$entry;
            }
        }

        //Create parents folders of language file
        wp_mkdir_p(dirname($file));

        //Write the language file
        if ($mo->export_to_file($file)) {
            echo json_encode(array('status' => 'success', 'message' => 'file created'));
            die();
        }

        echo json_encode(array('status' => 'error', 'message' => 'writing file failed'));
        die();
    }

    /**
     * Get a translation file content
     *
     * @return void
     */
    protected static function getTranslation()
    {
        check_ajax_referer('jutranslation', 'wp_nonce');

        //Get and check language
        $language = $_POST['language'];
        if (!$language) {
            echo json_encode(array('status' => 'error', 'message' => 'language empty'));
            die();
        }
        if (!preg_match('/^[a-z]{2,3}(?:-[a-zA-Z]{4})?(?:-[A-Z]{2,3})?$/', $language)) {
            echo json_encode(array('status' => 'error', 'message' => 'invalid language code'));
            die();
        }

        $plugin = $_POST['slug'];
        if (!$plugin) {
            echo json_encode(array('status' => 'error', 'message' => 'plugin empty'));
            die();
        }
        if (!preg_match('/^[a-zA-Z-_]+$/', $plugin)) {
            echo json_encode(array('status' => 'error', 'message' => 'invalid plugin slug'));
            die();
        }

        $addons = apply_filters(self::$extension_slug . '_get_addons', array());
        if (!isset($addons[$plugin])) {
            echo json_encode(array('status' => 'error', 'message' => 'plugin not found'));
            die();
        }

        //Get the language file for reference language en-US
        $file = $addons[$plugin]->language_file;
        $mo   = new \MO();
        $mo->import_from_file($file);

        //Retrieve reference the strings
        $reference_strings = array();
        foreach ($mo->entries as $entry) {
            $reference_strings[$entry->singular] = $entry->translations[0];
        }

        //Get the default language file for this language
        $file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'plugins';
        $file .= DIRECTORY_SEPARATOR . $addons[$plugin]->text_domain . '-';
        $file .= str_replace('-', '_', $language) . '.mo';
        $mo   = new \MO();
        $mo->import_from_file($file);

        //Retrieve default strings
        $language_strings = array();
        foreach ($mo->entries as $entry) {
            $language_strings[$entry->singular] = $entry->translations[0];
        }

        //Get the override file content if exists
        $file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'plugins';
        $file .= DIRECTORY_SEPARATOR . $addons[$plugin]->text_domain . '-';
        $file .= str_replace('-', '_', $language) . '.override.mo';
        if (file_exists($file)) {
            $mo = new \MO();
            $mo->import_from_file($file);

            //Retrieve override strings
            $override_strings = array();
            foreach ($mo->entries as $entry) {
                $override_strings[$entry->singular] = $entry->translations[0];
            }
        } else {
            $override_strings = array();
        }

        //Generate the final variable cotaining all strings
        $final_result = array();
        $override_array = array(
            'reference_strings' => $reference_strings,
            'language_strings'  => $language_strings,
            'override_strings'  => $override_strings
        );

        foreach ($override_array as $variable => $strings) {
            foreach ($strings as $constant => $value) {
                if (empty($final_result[$constant])) {
                    $obj                     = new \stdClass();
                    $obj->key                = $constant;
                    $obj->reference          = '';
                    $obj->language           = '';
                    $obj->override           = '';
                    $final_result[$constant] = $obj;
                }
                switch ($variable) {
                    case 'reference_strings':
                        $final_result[$constant]->reference = $value;
                        break;
                    case 'language_strings':
                        $final_result[$constant]->language = $value;
                        break;
                    case 'override_strings':
                        $final_result[$constant]->override = $value;
                        break;
                }
            }
        }
        echo json_encode(
            array('status' => 'success', 'datas' => array('language' => $language, 'strings' => $final_result))
        );
        die();
    }

    /**
     * Show submit form to share translation
     *
     * @return void
     */
    protected static function showViewForm()
    {
        check_ajax_referer('jutranslation', 'wp_nonce');

        echo '<!doctype html>';
        echo '<html lang="en">';
        echo '<head>';
        echo '  <meta charset="utf-8">';
        echo '  <title>Share with Joomunited</title>';
        echo '</head>';
        echo '<body>';

        //Get and check language
        $language = $_GET['language'];
        if (!$language) {
            throw new \Exception('language empty');
        }
        if (!preg_match('/^[a-z]{2,3}(?:-[a-zA-Z]{4})?(?:-[A-Z]{2,3})?$/', $language)) {
            throw new \Exception('invalid language code');
        }

        $plugin = $_GET['slug'];
        if (!$plugin) {
            echo json_encode(array('status' => 'error', 'message' => 'plugin empty'));
            die();
        }
        if (!preg_match('/^[a-zA-Z-_]+$/', $plugin)) {
            echo json_encode(array('status' => 'error', 'message' => 'invalid plugin slug'));
            die();
        }

        $addons = apply_filters(self::$extension_slug . '_get_addons', array());
        if (!isset($addons[$plugin])) {
            echo json_encode(array('status' => 'error', 'message' => 'plugin not found'));
            die();
        }

        $file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'plugins';
        $file .= DIRECTORY_SEPARATOR . $addons[$plugin]->text_domain . '-' . str_replace('-', '_', $language) . '.mo';
        if (!file_exists($file)) {
            throw new Exception('language file doesn\'t exist');
        }

        //Get informations
        $mo = new \MO();
        $mo->import_from_file($file);

        //Check if the file has been modified by the user
        if (isset($mo->headers['Modified']) && $mo->headers['Modified'] !== '1') {
            throw new Exception('language file not modified');
        }

        $strings = array();
        //Check if a translation exists for each entry
        foreach ($mo->entries as $entry) {
            $strings[$entry->singular] = $entry->translations[0];
        }
        $strings = json_encode($strings);

        //Get the current extension version
        $plugin_data = get_plugin_data($addons[$plugin]->main_plugin_file);
        $version     = $plugin_data['Version'];

        echo '<form method="POST" ' .
             ' action="https://www.joomunited.com/index.php?option=com_jutranslation&task=contribution.share">';
        echo '<input type="hidden" name="extension" value="' . esc_attr($addons[$plugin]->extension_slug) . '" />';
        echo '<input type="hidden" name="extension_language" value="' . esc_attr($language) . '" />';
        echo '<input type="hidden" name="extension_version" value="' . esc_attr($version) . '" />';
        echo '<textarea style="display: none" name="strings">' . htmlentities($strings) . '</textarea>'; // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</form>';
        //Add waiting image
        echo '<div style="text-align:center"><img src="' .
             esc_attr(plugin_dir_url(self::$main_plugin_file)) . 'jutranslation/assets/images/preview_loader.gif"></div>';

        //Submit automatically the form on page loading
        echo '<script type="text/javascript">document.forms[0].submit();</script>';

        echo '</body>';
        echo '</html>';
        wp_die();
    }

    /**
     * Load overrided languages
     *
     * @param string $text_domain Text domain to load
     * @param string $mofile      Mo file to load
     *
     * @return void;
     */
    public static function overrideLanguage($text_domain, $mofile)
    {
        //Only for our plugin and addons
        $addons = apply_filters(self::$extension_slug . '_get_addons', array());

        foreach ($addons as $addon) {
            if ($text_domain === $addon->text_domain) {
                $path_parts     = explode(DIRECTORY_SEPARATOR, $mofile);
                $filename       = $path_parts[count($path_parts) - 1];
                $filename_parts = explode('.', $filename);

                //Return if it's action already for overrode file
                if (count($filename_parts) > 2 || $filename_parts[count($filename_parts) - 2] === 'override') {
                    return;
                }

                //Load the overrode file
                $path_parts[count($path_parts) - 1] = str_replace(
                    '.mo',
                    '.override.mo',
                    $path_parts[count($path_parts) - 1]
                );
                if (file_exists(implode(DIRECTORY_SEPARATOR, $path_parts))) {
                    load_textdomain($text_domain, implode(DIRECTORY_SEPARATOR, $path_parts));
                }
            }
        }
    }
}
