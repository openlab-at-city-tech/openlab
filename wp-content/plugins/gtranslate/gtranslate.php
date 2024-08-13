<?php
/*
Plugin Name: GTranslate
Plugin URI: https://gtranslate.io/?xyz=998
Description: Translate your website and make it multilingual. For support visit <a href="https://wordpress.org/support/plugin/gtranslate">GTranslate Support Forum</a>.
Version: 3.0.6
Author: Translate AI Multilingual Solutions
Author URI: https://gtranslate.io
Text Domain: gtranslate

*/

/*  Copyright 2010 - 2022 GTranslate Inc. ( website: https://gtranslate.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('widgets_init', array('GTranslate', 'register'));
register_activation_hook(__FILE__, array('GTranslate', 'activate'));
register_deactivation_hook(__FILE__, array('GTranslate', 'deactivate'));
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('GTranslate', 'settings_link'));
add_action('admin_menu', array('GTranslate', 'admin_menu'));
add_action('init', array('GTranslate', 'enqueue_scripts'));
add_action('plugins_loaded', array('GTranslate', 'load_textdomain'));
add_action('send_headers', array('GTranslate', 'set_dns_prefetch_header'));
add_filter('script_loader_tag', array('GTranslate', 'add_script_attributes'), 10, 3);
add_filter('walker_nav_menu_start_el', array('GTranslate', 'render_menu_items') , 10 , 4);
add_shortcode('GTranslate', array('GTranslate', 'render_shortcode'));
add_shortcode('gtranslate', array('GTranslate', 'render_shortcode'));
add_shortcode('gt-link', array('GTranslate', 'render_single_item'));

class GTranslate extends WP_Widget {
    public static $lang_array = array('en'=>'English','ar'=>'Arabic','bg'=>'Bulgarian','zh-CN'=>'Chinese (Simplified)','zh-TW'=>'Chinese (Traditional)','hr'=>'Croatian','cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','fi'=>'Finnish','fr'=>'French','de'=>'German','el'=>'Greek','hi'=>'Hindi','it'=>'Italian','ja'=>'Japanese','ko'=>'Korean','no'=>'Norwegian','pl'=>'Polish','pt'=>'Portuguese','ro'=>'Romanian','ru'=>'Russian','es'=>'Spanish','sv'=>'Swedish','ca'=>'Catalan','tl'=>'Filipino','iw'=>'Hebrew','id'=>'Indonesian','lv'=>'Latvian','lt'=>'Lithuanian','sr'=>'Serbian','sk'=>'Slovak','sl'=>'Slovenian','uk'=>'Ukrainian','vi'=>'Vietnamese','sq'=>'Albanian','et'=>'Estonian','gl'=>'Galician','hu'=>'Hungarian','mt'=>'Maltese','th'=>'Thai','tr'=>'Turkish','fa'=>'Persian','af'=>'Afrikaans','ms'=>'Malay','sw'=>'Swahili','ga'=>'Irish','cy'=>'Welsh','be'=>'Belarusian','is'=>'Icelandic','mk'=>'Macedonian','yi'=>'Yiddish','hy'=>'Armenian','az'=>'Azerbaijani','eu'=>'Basque','ka'=>'Georgian','ht'=>'Haitian Creole','ur'=>'Urdu','bn' => 'Bengali','bs' => 'Bosnian','ceb' => 'Cebuano','eo' => 'Esperanto','gu' => 'Gujarati','ha' => 'Hausa','hmn' => 'Hmong','ig' => 'Igbo','jw' => 'Javanese','kn' => 'Kannada','km' => 'Khmer','lo' => 'Lao','la' => 'Latin','mi' => 'Maori','mr' => 'Marathi','mn' => 'Mongolian','ne' => 'Nepali','pa' => 'Punjabi','so' => 'Somali','ta' => 'Tamil','te' => 'Telugu','yo' => 'Yoruba','zu' => 'Zulu','my' => 'Myanmar (Burmese)','ny' => 'Chichewa','kk' => 'Kazakh','mg' => 'Malagasy','ml' => 'Malayalam','si' => 'Sinhala','st' => 'Sesotho','su' => 'Sudanese','tg' => 'Tajik','uz' => 'Uzbek','am' => 'Amharic','co' => 'Corsican','haw' => 'Hawaiian','ku' => 'Kurdish (Kurmanji)','ky' => 'Kyrgyz','lb' => 'Luxembourgish','ps' => 'Pashto','sm' => 'Samoan','gd' => 'Scottish Gaelic','sn' => 'Shona','sd' => 'Sindhi','fy' => 'Frisian','xh' => 'Xhosa');
    public static $lang_array_native_json = '{"af":"Afrikaans","sq":"Shqip","am":"\u12a0\u121b\u122d\u129b","ar":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629","hy":"\u0540\u0561\u0575\u0565\u0580\u0565\u0576","az":"Az\u0259rbaycan dili","eu":"Euskara","be":"\u0411\u0435\u043b\u0430\u0440\u0443\u0441\u043a\u0430\u044f \u043c\u043e\u0432\u0430","bn":"\u09ac\u09be\u0982\u09b2\u09be","bs":"Bosanski","bg":"\u0411\u044a\u043b\u0433\u0430\u0440\u0441\u043a\u0438","ca":"Catal\u00e0","ceb":"Cebuano","ny":"Chichewa","zh-CN":"\u7b80\u4f53\u4e2d\u6587","zh-TW":"\u7e41\u9ad4\u4e2d\u6587","co":"Corsu","hr":"Hrvatski","cs":"\u010ce\u0161tina\u200e","da":"Dansk","nl":"Nederlands","en":"English","eo":"Esperanto","et":"Eesti","tl":"Filipino","fi":"Suomi","fr":"Fran\u00e7ais","fy":"Frysk","gl":"Galego","ka":"\u10e5\u10d0\u10e0\u10d7\u10e3\u10da\u10d8","de":"Deutsch","el":"\u0395\u03bb\u03bb\u03b7\u03bd\u03b9\u03ba\u03ac","gu":"\u0a97\u0ac1\u0a9c\u0ab0\u0abe\u0aa4\u0ac0","ht":"Kreyol ayisyen","ha":"Harshen Hausa","haw":"\u014clelo Hawai\u02bbi","iw":"\u05e2\u05b4\u05d1\u05b0\u05e8\u05b4\u05d9\u05ea","hi":"\u0939\u093f\u0928\u094d\u0926\u0940","hmn":"Hmong","hu":"Magyar","is":"\u00cdslenska","ig":"Igbo","id":"Bahasa Indonesia","ga":"Gaeilge","it":"Italiano","ja":"\u65e5\u672c\u8a9e","jw":"Basa Jawa","kn":"\u0c95\u0ca8\u0ccd\u0ca8\u0ca1","kk":"\u049a\u0430\u0437\u0430\u049b \u0442\u0456\u043b\u0456","km":"\u1797\u17b6\u179f\u17b6\u1781\u17d2\u1798\u17c2\u179a","ko":"\ud55c\uad6d\uc5b4","ku":"\u0643\u0648\u0631\u062f\u06cc\u200e","ky":"\u041a\u044b\u0440\u0433\u044b\u0437\u0447\u0430","lo":"\u0e9e\u0eb2\u0eaa\u0eb2\u0ea5\u0eb2\u0ea7","la":"Latin","lv":"Latvie\u0161u valoda","lt":"Lietuvi\u0173 kalba","lb":"L\u00ebtzebuergesch","mk":"\u041c\u0430\u043a\u0435\u0434\u043e\u043d\u0441\u043a\u0438 \u0458\u0430\u0437\u0438\u043a","mg":"Malagasy","ms":"Bahasa Melayu","ml":"\u0d2e\u0d32\u0d2f\u0d3e\u0d33\u0d02","mt":"Maltese","mi":"Te Reo M\u0101ori","mr":"\u092e\u0930\u093e\u0920\u0940","mn":"\u041c\u043e\u043d\u0433\u043e\u043b","my":"\u1017\u1019\u102c\u1005\u102c","ne":"\u0928\u0947\u092a\u093e\u0932\u0940","no":"Norsk bokm\u00e5l","ps":"\u067e\u069a\u062a\u0648","fa":"\u0641\u0627\u0631\u0633\u06cc","pl":"Polski","pt":"Portugu\u00eas","pa":"\u0a2a\u0a70\u0a1c\u0a3e\u0a2c\u0a40","ro":"Rom\u00e2n\u0103","ru":"\u0420\u0443\u0441\u0441\u043a\u0438\u0439","sm":"Samoan","gd":"G\u00e0idhlig","sr":"\u0421\u0440\u043f\u0441\u043a\u0438 \u0458\u0435\u0437\u0438\u043a","st":"Sesotho","sn":"Shona","sd":"\u0633\u0646\u068c\u064a","si":"\u0dc3\u0dd2\u0d82\u0dc4\u0dbd","sk":"Sloven\u010dina","sl":"Sloven\u0161\u010dina","so":"Afsoomaali","es":"Espa\u00f1ol","su":"Basa Sunda","sw":"Kiswahili","sv":"Svenska","tg":"\u0422\u043e\u04b7\u0438\u043a\u04e3","ta":"\u0ba4\u0bae\u0bbf\u0bb4\u0bcd","te":"\u0c24\u0c46\u0c32\u0c41\u0c17\u0c41","th":"\u0e44\u0e17\u0e22","tr":"T\u00fcrk\u00e7e","uk":"\u0423\u043a\u0440\u0430\u0457\u043d\u0441\u044c\u043a\u0430","ur":"\u0627\u0631\u062f\u0648","uz":"O\u2018zbekcha","vi":"Ti\u1ebfng Vi\u1ec7t","cy":"Cymraeg","xh":"isiXhosa","yi":"\u05d9\u05d9\u05d3\u05d9\u05e9","yo":"Yor\u00f9b\u00e1","zu":"Zulu"}';

    public static function activate() {
        $data = array(
            'gtranslate_title' => __('Website Translator', 'gtranslate'),
        );
        $data = get_option('GTranslate');
        self::load_defaults($data);

        add_option('GTranslate', $data);
    }

    public static function deactivate() {
        // delete_option('GTranslate');
    }

    public static function settings_link($links) {
        $settings_link = array('<a href="' . admin_url('options-general.php?page=gtranslate_options') . '">'.__('Settings', 'gtranslate').'</a>');
        return array_merge($links, $settings_link);
    }

    public static function control() {
        $data = get_option('GTranslate');
        ?>
        <p><label><?php _e('Title', 'gtranslate'); ?>: <input name="gtranslate_title" type="text" class="widefat" value="<?php echo $data['gtranslate_title']; ?>"/></label></p>
        <p><?php _e('Please go to <a href="' . admin_url('options-general.php?page=gtranslate_options') . '">'.__('GTranslate Settings', 'gtranslate').'</a> for configuration.', 'gtranslate'); ?></p>
        <?php
        if (isset($_POST['gtranslate_title'])){
            $data['gtranslate_title'] = esc_attr($_POST['gtranslate_title']);
            update_option('GTranslate', $data);
        }
    }

    public static function set_dns_prefetch_header() {
        $data = get_option('GTranslate');
        self::load_defaults($data);

        if($data['enable_cdn'])
            header('Link: <https://cdn.gtranslate.net/>; rel=dns-prefetch', false);
    }

    public static function enqueue_scripts() {
        $data = get_option('GTranslate');
        self::load_defaults($data);

        if(is_admin())
            wp_enqueue_script('jquery');

        // make sure main_lang is set correctly in config.php file
        if($data['pro_version'] or $data['enterprise_version']) {
            include dirname(__FILE__) . '/url_addon/config.php';

            if($main_lang != $data['default_language']) { // update main_lang in config.php
                $config_file = dirname(__FILE__) . '/url_addon/config.php';
                if(is_readable($config_file) and is_writable($config_file)) {
                    $config = file_get_contents($config_file);
                    if(strpos($config, 'main_lang') !== false) {
                        $config = preg_replace('/\$main_lang = \'[a-z-]{2,5}\'/i', '$main_lang = \''.$data['default_language'].'\'', $config);
                        if(is_string($config) and strlen($config) > 10)
                            file_put_contents($config_file, $config);
                    }
                }
            }
        }
    }

    public static function load_textdomain() {
        load_plugin_textdomain('gtranslate');

        // set correct language direction
        global $text_direction;
        if(isset($_SERVER['HTTP_X_GT_LANG']) and in_array($_SERVER['HTTP_X_GT_LANG'], array('ar', 'iw', 'fa')))
            $text_direction = 'rtl';
        elseif(isset($_SERVER['HTTP_X_GT_LANG']))
            $text_direction = 'ltr';
    }

    public static function add_script_attributes($tag, $handle, $src) {
        if(!empty($src) and strpos($handle, 'gt_widget_script_') === 0) {
            $orig_url = strtok($_SERVER['REQUEST_URI'], '?');
            $orig_domain = parse_url(site_url(), PHP_URL_HOST);
            $widget_id = str_replace('gt_widget_script_', '', $handle);

            $tag = strstr($tag, '</script>', true) . '</script><script src="' . esc_attr($src) . '" data-no-optimize="1" data-no-minify="1" data-gt-orig-url="' . esc_attr($orig_url) . '" data-gt-orig-domain="' . esc_attr($orig_domain) . '" data-gt-widget-id="' . esc_attr($widget_id) .'" defer></script>';
        }

        return $tag;
    }

    public static function render_shortcode($atts) {
        if(!is_array($atts)) $atts = array();

        $atts['position'] = 'inline';
        $atts['wrapper_selector'] = '.gtranslate_wrapper';

        return self::get_widget_code($atts);
    }

    public static function render_single_item($atts) {
        if(!is_array($atts) or !isset($atts['lang']))
            return;

        $data = get_option('GTranslate');
        self::load_defaults($data);

        $lang_code = $atts['lang'];

        if(!isset($atts['label'])) {
            $lang_array = $data['native_language_names'] ? json_decode(GTranslate::$lang_array_native_json, true) : GTranslate::$lang_array;
            $label = $lang_array[$lang_code];
        } else {
            $label = $atts['label'];
        }

        $widget_look = isset($atts['widget_look']) ? $atts['widget_look'] : $data['widget_look'];

        if(!in_array($widget_look, array('flags', 'flags_code', 'flags_name', 'lang_codes', 'lang_names')))
            $widget_look = 'flags_name';

        $flag_size = $data['flag_size'];
        $flag_src = self::get_flag_src($lang_code);

        if(isset($atts['current_wrapper']))
            $add_class = $lang_code == $data['default_language'] ? ' class="gt-current-wrapper notranslate"' : ' class="notranslate"';
        else
            $add_class = $lang_code == $data['default_language'] ? ' class="gt-current-lang notranslate"' : ' class="notranslate"';

        switch($widget_look) {
            case 'lang_names': $el_code = '<a href="#" data-gt-lang="' . esc_attr($lang_code) . '"' . $add_class . '>' . esc_html($label) . '</a>'; break;
            case 'lang_codes': $el_code = '<a href="#" data-gt-lang="' . esc_attr($lang_code) . '"' . $add_class . '>' . esc_html(strtoupper($lang_code)) . '</a>'; break;
            case 'flags': $el_code = '<a href="#" data-gt-lang="' . esc_attr($lang_code) . '"' . $add_class . '><img src="' . esc_attr($flag_src) . '" width="' . esc_attr($flag_size) . '" height="' . esc_attr($flag_size) . '" alt="' . esc_attr($lang_code) . '" loading="lazy"></a>'; break;
            case 'flags_name': $el_code = '<a href="#" data-gt-lang="' . esc_attr($lang_code) . '"' . $add_class . '><img src="' . esc_attr($flag_src) . '" width="' . esc_attr($flag_size) . '" height="' . esc_attr($flag_size) . '" alt="' . esc_attr($lang_code) . '" loading="lazy"> <span>' . esc_html($label) . '</span></a>'; break;
            case 'flags_code': $el_code = '<a href="#" data-gt-lang="' . esc_attr($lang_code) . '"' . $add_class . '><img src="' . esc_attr($flag_src) . '" width="' . esc_attr($flag_size) . '" height="' . esc_attr($flag_size) . '" alt="' . esc_attr($lang_code) . '" loading="lazy"> <span>' . esc_html(strtoupper($lang_code)) . '</span></a>'; break;
        }

        global $gt_base_loaded;
        if(!$gt_base_loaded) {
            $gt_base_loaded = true;

            $gt_settings = self::load_settings($data);
            $unique_id = wp_rand(10000000, 88888888);

            // remove excess settings based on widget_look to keep front-end code small
            $old_settings = $gt_settings;
            $gt_settings = array();
            $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'flag_style', 'flag_size', 'alt_flags', 'custom_domains', 'custom_css');
            foreach($keep_keys as $key)
                if(isset($old_settings[$key]) and $old_settings[$key] !== '')
                    $gt_settings[$key] = $old_settings[$key];

            if($data['enable_cdn']) {
                wp_enqueue_script('gt_widget_script_' . $unique_id, 'https://cdn.gtranslate.net/widgets/latest/base.js', array(), '', true);
            } else {
                $base_path = plugins_url('', __FILE__);
                if($data['enterprise_version'])
                    $gt_settings['flags_location'] = $base_path . '/flags/';
                else
                    $gt_settings['flags_location'] = wp_make_link_relative($base_path) . '/flags/';

                wp_enqueue_script('gt_widget_script_' . $unique_id, $base_path . '/js/base.js', array(), '', true);
            }
            wp_add_inline_script('gt_widget_script_' . $unique_id, "window.gtranslateSettings = /* document.write */ window.gtranslateSettings || {};window.gtranslateSettings['" . $unique_id . "'] = " . json_encode($gt_settings) . ";", 'before');
        }

        return $el_code;
    }

    public static function render_menu_items($item_output, $item, $depth, $args) {
        if(!empty($item->post_title) and strpos($item->post_title, '[gtranslate') !== false)
            return do_shortcode($item->post_title);

        if(empty($item->description) or strpos($item->description, '[gt-link') === false)
            return $item_output;

        $output = do_shortcode($item->description);
        if(!empty($output))
            return $output;

        return $item_output;
    }

    public static function get_flag_src($lang) {
        $data = get_option('GTranslate');
        self::load_defaults($data);

        if($data['enable_cdn'])
            $base_src = 'https://cdn.gtranslate.net/flags/';
        else
            $base_src = plugins_url('', __FILE__) . '/flags/';

        if($data['flag_style'] == '2d')
            $base_src .= 'svg/';
        else
            $base_src .= $data['flag_size'] . '/';

        $flag_ext = $data['flag_style'] == '3d' ? '.png' : '.svg';

        $alt_flags = array();
        $raw_alt_flags = $data['alt_flags']; // example raw_alt_flags: ['us', 'br', 'ar']
        foreach($raw_alt_flags as $country_code) {
            switch($country_code) {
                case 'us': $alt_flags['en'] = 'en-us'; break;
                case 'ca': $alt_flags['en'] = 'en-ca'; break;
                case 'br': $alt_flags['pt'] = 'pt-br'; break;
                case 'mx': $alt_flags['es'] = 'es-mx'; break;
                case 'ar': $alt_flags['es'] = 'es-ar'; break;
                case 'co': $alt_flags['es'] = 'es-co'; break;
                case 'qc': $alt_flags['fr'] = 'fr-qc'; break;
                default: break;
            }
        }

        $flag = isset($alt_flags[$lang]) ? $alt_flags[$lang] : $lang;

        return $base_src . $flag . $flag_ext;
    }

    public static function get_widget_code($atts) {
        $unique_id = wp_rand(10000000, 88888888);

        $data = get_option('GTranslate');
        self::load_defaults($data);

        if(isset($atts['widget_look']) and in_array($atts['widget_look'], array('float', 'dropdown_with_flags', 'dropdown', 'flags_dropdown', 'popup', 'flags', 'globe', 'flags_code', 'flags_name', 'lang_names', 'lang_codes'))) {
            $data['widget_look'] = $atts['widget_look'];
        }

        if(isset($atts['wrapper_selector'])) {
            $data['wrapper_selector'] = $atts['wrapper_selector'];
        }

        $gt_settings = self::load_settings($data);

        if(isset($atts['position'])) {
            $position = $float_position = $atts['position'];
        } else {
            if($data['floating_language_selector'] == 'no')
                $position = $float_position = 'inline';
            else
                $position = $float_position = $data['floating_language_selector'];
        }

        if($float_position == 'inline') {
            $switcher_horizontal_position = 'inline';
            $switcher_vertical_position = '';
        } else
            list($switcher_vertical_position, $switcher_horizontal_position) = explode('_', $float_position);
        $gt_settings['switcher_horizontal_position'] = $switcher_horizontal_position;
        $gt_settings['switcher_vertical_position'] = $switcher_vertical_position;

        if($position == 'inline') {
            $horizontal_position = 'inline';
            $vertical_position = '';
        } else
            list($vertical_position, $horizontal_position) = explode('_', $position);
        $gt_settings['horizontal_position'] = $horizontal_position;
        $gt_settings['vertical_position'] = $vertical_position;

        $widget_code = '';
        if($gt_settings['wrapper_selector'] == '.gtranslate_wrapper' or empty(trim($gt_settings['wrapper_selector']))) {
            $gt_settings['wrapper_selector'] = '#gt-wrapper-' . $unique_id;
            $widget_code .= '<div class="gtranslate_wrapper" id="gt-wrapper-' . $unique_id . '"></div>';
        }

        if(strpos($data['widget_look'], '_') !== false) {
            $widget_short_name = explode('_', $data['widget_look']);
            foreach($widget_short_name as $i => $segment)
                $widget_short_name[$i] = substr($segment, 0, 1);
            $widget_short_name = implode('', $widget_short_name);
        } else {
            $widget_short_name = $data['widget_look'];
        }

        // remove excess settings based on widget_look to keep front-end code small
        $old_settings = $gt_settings;
        $gt_settings = array();
        switch($data['widget_look']) {
            case 'float': $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'flag_style', 'wrapper_selector', 'alt_flags', 'custom_domains', 'float_switcher_open_direction', 'switcher_horizontal_position', 'switcher_vertical_position', 'custom_css'); break;
            case 'dropdown_with_flags': $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'flag_style', 'flag_size', 'wrapper_selector', 'alt_flags', 'custom_domains', 'switcher_open_direction', 'switcher_horizontal_position', 'switcher_vertical_position', 'switcher_text_color', 'switcher_arrow_color', 'switcher_border_color', 'switcher_background_color', 'switcher_background_shadow_color', 'switcher_background_hover_color', 'dropdown_text_color', 'dropdown_hover_color', 'dropdown_background_color', 'custom_css'); break;
            case 'dropdown': $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'wrapper_selector', 'custom_domains', 'select_language_label', 'custom_css', 'horizontal_position', 'vertical_position'); break;
            case 'flags_dropdown': $keep_keys = array('default_language', 'languages', 'dropdown_languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'add_new_line', 'flag_style', 'flag_size', 'wrapper_selector', 'alt_flags', 'custom_domains', 'custom_css', 'horizontal_position', 'vertical_position'); break;
            case 'popup':
            case 'flags':
            case 'flags_name':
            case 'flags_code': $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'flag_style', 'flag_size', 'wrapper_selector', 'alt_flags', 'custom_domains', 'custom_css', 'horizontal_position', 'vertical_position'); break;
            case 'globe': $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'wrapper_selector', 'flag_size', 'globe_size', 'alt_flags', 'globe_color', 'custom_domains', 'custom_css', 'horizontal_position', 'vertical_position'); break;
            case 'lang_codes':
            case 'lang_names': $keep_keys = array('default_language', 'languages', 'url_structure', 'native_language_names', 'detect_browser_language', 'wrapper_selector', 'custom_domains', 'custom_css', 'horizontal_position', 'vertical_position'); break;
            default: $keep_keys = array_keys($old_settings); break;
        }
        foreach($keep_keys as $key)
            if(isset($old_settings[$key]) and $old_settings[$key] !== '')
                $gt_settings[$key] = $old_settings[$key];

        // overwrite settings from shortcode attributes
        /* todo: sanitize input
        if(is_array($atts)) {
            foreach($keep_keys as $key)
                if(isset($atts[$key]) and $atts[$key] !== '')
                    $gt_settings[$key] = $atts[$key];
        }
        */

        // add necessary js
        if($data['enable_cdn']) {
            wp_enqueue_script('gt_widget_script_' . $unique_id, 'https://cdn.gtranslate.net/widgets/latest/' . $widget_short_name . '.js', array(), '', true);
        } else {
            $base_path = plugins_url('', __FILE__);
            if($data['widget_look'] == 'globe') {
                if($data['enterprise_version'])
                    $gt_settings['flags_location'] = $base_path . '/flags/svg/';
                else
                    $gt_settings['flags_location'] = wp_make_link_relative($base_path) . '/flags/svg/';
            } else {
                if($data['enterprise_version'])
                    $gt_settings['flags_location'] = $base_path . '/flags/';
                else
                    $gt_settings['flags_location'] = wp_make_link_relative($base_path) . '/flags/';
            }

            wp_enqueue_script('gt_widget_script_' . $unique_id, $base_path . '/js/' . $widget_short_name . '.js', array(), '', true);
        }
        wp_add_inline_script('gt_widget_script_' . $unique_id, "window.gtranslateSettings = /* document.write */ window.gtranslateSettings || {};window.gtranslateSettings['" . $unique_id . "'] = " . json_encode($gt_settings) . ";", 'before');

        return $widget_code;
    }

    public static function register() {
        register_widget('GTranslateWidget');
    }

    public static function admin_menu() {
        add_options_page(__('GTranslate Options', 'gtranslate'), 'GTranslate', 'administrator', 'gtranslate_options', array('GTranslate', 'options'));

    }

    public static function options() {
        ?>
        <div class="wrap">
        <div id="icon-options-general" class="icon32"><br/></div>
        <h2><img src="<?php echo plugins_url('gt_logo.svg', __FILE__); ?>" border="0" title="<?php _e('GTranslate - your window to the world', 'gtranslate'); ?>" alt="G|translate" height="70"></h2>
        <?php
        if(isset($_POST['save']) and $_POST['save'])
            self::control_options();
        $data = get_option('GTranslate');
        self::load_defaults($data);

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-effects-core');

        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style( 'wp-color-picker');
        wp_add_inline_script('wp-color-picker', 'jQuery(document).ready(function($) {$(".color-field").wpColorPicker({change:function(e,c){$("#"+e.target.getAttribute("id")+"_hidden").val(c.color.toString());e.target.value = c.color.toString();RefreshDoWidgetCode();}});});');

        add_thickbox();

        $site_url = site_url();
        $wp_plugin_url = preg_replace('/^https?:/i', '', plugins_url() . '/gtranslate');

        extract($data);

        $gt_lang_array_json = json_encode(self::$lang_array);
        $gt_lang_array = self::$lang_array;

        if(!empty($language_codes))
            $gt_lang_codes_json = json_encode(explode(',', $language_codes));
        else
            $gt_lang_codes_json = '[]';

        if(!empty($language_codes2))
            $gt_lang_codes2_json = json_encode(explode(',', $language_codes2));
        else
            $gt_lang_codes2_json = '[]';

$script = <<<EOT

jQuery(document).ready(function($){
    jQuery('input[name="alt_flags2[]"]').change(function() {
        if($(this).prop('checked')) {
            var lang_group = $(this).attr('data-lang-group');

            // uncheck other items from lang group
            $('input[name="alt_flags2[]"][data-lang-group="'+lang_group+'"]').prop('checked', false);
            $(this).prop('checked', true);
        }

        $('#alt_us_hidden').prop('checked', $('#alt_us').prop('checked'));
        $('#alt_ca_hidden').prop('checked', $('#alt_ca').prop('checked'));
        $('#alt_br_hidden').prop('checked', $('#alt_br').prop('checked'));
        $('#alt_mx_hidden').prop('checked', $('#alt_mx').prop('checked'));
        $('#alt_ar_hidden').prop('checked', $('#alt_ar').prop('checked'));
        $('#alt_co_hidden').prop('checked', $('#alt_co').prop('checked'));
        $('#alt_qc_hidden').prop('checked', $('#alt_qc').prop('checked'));

        RefreshDoWidgetCode();
    });
});

var gt_lang_array = $gt_lang_array_json;
var languages = [], language_codes = $gt_lang_codes_json, language_codes2 = $gt_lang_codes2_json;

if(language_codes.length == 0)
    for(var key in gt_lang_array)
        language_codes.push(key);
if(language_codes2.length == 0)
    for(var key in gt_lang_array)
        language_codes2.push(key);

function SyncCustomDomains() {
    jQuery('#custom_domains_status_sync').show();

    jQuery.ajax({
        url: 'https://tdns.gtranslate.net/tdn-bin/load-custom-domains',
        type: 'GET',
        dataType: 'json',
        headers: {"X-GT-Domain": window.gt_debug_main_domain||location.hostname},
        success: function(data) {
            jQuery('#custom_domains_status_sync').hide();

            if(data.err) { // todo: nice alert box
                if(data.err == 'no license')
                    alert('No subscription found for "' + (window.gt_debug_main_domain||location.hostname) + '". Please subscribe at https://gtranslate.io/');
                else if(data.err == 'no settings')
                    alert('Make sure your subscription for "' + (window.gt_debug_main_domain||location.hostname) + '" has Language Hosting feature and Custom domains are configured in your GTranslate dashboard: https://my.gtranslate.io/settings#advanced');
                else
                    alert(data.err);

                jQuery('#custom_domains').prop('checked', false);
                RefreshDoWidgetCode();

                return;
            }

            jQuery('#custom_domains_data').val(JSON.stringify(data));
            jQuery('#custom_domains_list_tbl tr.lang_domain_row').remove();
            for(l in data)
                jQuery('#custom_domains_list_tbl tr:last').after('<tr class="lang_domain_row"><td>'+l+'</td><td>'+data[l]+'</td></tr>');
            jQuery('.custom_domains_list').show();
        },
        error: function(e) {
            alert('Something strange happened, please try again later.');

            jQuery('#custom_domains').prop('checked', false);
            RefreshDoWidgetCode();

            jQuery('#custom_domains_status_sync').hide();
            jQuery('.custom_domains_list').hide();
        }
    });
}

function RefreshDoWidgetCode() {
    var widget_look = jQuery('#widget_look').val();

    var switcher_horizontal_position = jQuery('#floating_language_selector').val().split('_').pop();
    var switcher_vertical_position = jQuery('#floating_language_selector').val().split('_').shift();
    var horizontal_position = jQuery('#floating_language_selector').val().split('_').pop();
    var vertical_position = jQuery('#floating_language_selector').val().split('_').shift();

    var float_switcher_open_direction = jQuery('#float_switcher_open_direction').val();
    var switcher_open_direction = jQuery('#switcher_open_direction').val();

    var default_language = jQuery('#default_language').val();
    var native_language_names = jQuery('#native_language_names').prop('checked');
    var detect_browser_language = jQuery('#detect_browser_language').prop('checked');
    var add_new_line = jQuery('#add_new_line').prop('checked');
    var select_language_label = jQuery('#select_language_label').val();
    var flag_size = parseInt(jQuery('#flag_size').val());
    var flag_style = jQuery('#flag_style').val();
    var globe_size = parseInt(jQuery('#globe_size').val());
    var globe_color = jQuery('#globe_color').val();

    var pro_version = jQuery('#pro_version').prop('checked');
    var enterprise_version = jQuery('#enterprise_version').prop('checked');

    var url_structure = 'none';
    if(pro_version)
        url_structure = 'sub_directory';
    else if(enterprise_version)
        url_structure = 'sub_domain';

    var wrapper_selector = jQuery('#wrapper_selector').val();

    var dropdown_languages = jQuery('input[name="incl_langs[]"]:checked').map(function(){return jQuery(this).val()}).get();
    var languages = jQuery('input[name="fincl_langs[]"]:checked').map(function(){return jQuery(this).val()}).get();
    if(widget_look == 'dropdown' || widget_look == 'globe' || widget_look == 'lang_names' || widget_look == 'lang_codes')
        languages = dropdown_languages;

    var custom_domains = jQuery('#custom_domains:checked').length > 0 ? true : false;
    var custom_domains_data = JSON.parse(jQuery('#custom_domains_data').val()||'{}');
    var enable_cdn = jQuery('#enable_cdn:checked').length > 0 ? true : false;
    var show_in_menu = jQuery('#show_in_menu').val();
    var floating_language_selector = jQuery('#floating_language_selector').val();

    var email_translation = jQuery('#email_translation').prop('checked');

    var switcher_text_color = jQuery('#switcher_text_color').val();
    var switcher_arrow_color = jQuery('#switcher_arrow_color').val();
    var switcher_border_color = jQuery('#switcher_border_color').val();
    var switcher_background_color = jQuery('#switcher_background_color').val();
    var switcher_background_shadow_color = jQuery('#switcher_background_shadow_color').val();
    var switcher_background_hover_color = jQuery('#switcher_background_hover_color').val();
    var dropdown_text_color = jQuery('#dropdown_text_color').val();
    var dropdown_hover_color = jQuery('#dropdown_hover_color').val();
    var dropdown_background_color = jQuery('#dropdown_background_color').val();

    var alt_flags = {};
    jQuery('input[name="alt_flags[]"]:checked').map(function(){return jQuery(this).val()}).get().forEach(function(country_code) {
        switch(country_code) {
            case 'us': alt_flags['en'] = 'usa'; break;
            case 'ca': alt_flags['en'] = 'canada'; break;
            case 'br': alt_flags['pt'] = 'brazil'; break;
            case 'mx': alt_flags['es'] = 'mexico'; break;
            case 'ar': alt_flags['es'] = 'argentina'; break;
            case 'co': alt_flags['es'] = 'colombia'; break;
            case 'qc': alt_flags['fr'] = 'quebec'; break;
            default: break;
        }
    });

    var custom_css = jQuery('#custom_css').val();

    var gt_settings = {
        default_language: default_language,
        url_structure: url_structure,
        switcher_horizontal_position: 'inline',
        horizontal_position: 'inline',
        float_switcher_open_direction: float_switcher_open_direction,
        switcher_open_direction: switcher_open_direction,
        native_language_names: native_language_names,
        add_new_line: add_new_line,
        select_language_label: select_language_label,
        flag_size: flag_size,
        flag_style: flag_style,
        globe_size: globe_size,
        globe_color: globe_color,
        languages: languages,
        dropdown_languages: dropdown_languages,
        custom_domains: custom_domains ? custom_domains_data : null,
        alt_flags: alt_flags,

        switcher_text_color: switcher_text_color,
        switcher_arrow_color: switcher_arrow_color,
        switcher_border_color: switcher_border_color,
        switcher_background_color: switcher_background_color,
        switcher_background_shadow_color: switcher_background_shadow_color,
        switcher_background_hover_color: switcher_background_hover_color,
        dropdown_text_color: dropdown_text_color,
        dropdown_hover_color: dropdown_hover_color,
        dropdown_background_color: dropdown_background_color,

        custom_css: custom_css,
    };

    // disable loading on hover
    window.gt_translate_script = true;

    // make sure default language is on
    if(widget_look == 'flags_dropdown' || widget_look == 'float' || widget_look == 'dropdown_with_flags' || widget_look == 'flags' || widget_look == 'flags_name' || widget_look == 'flags_code' || widget_look == 'popup')
        jQuery('#fincl_langs'+default_language).prop('checked', true);
    if(widget_look == 'dropdown' || widget_look == 'globe' || widget_look == 'lang_names' || widget_look == 'lang_codes')
        jQuery('#incl_langs'+default_language).prop('checked', true);

    if(pro_version || enterprise_version) {
        if(enterprise_version) {
            jQuery('#custom_domains_option').show();
            if(custom_domains)
                jQuery('.custom_domains_list').show();
            else
                jQuery('.custom_domains_list').hide();
        } else {
            jQuery('#custom_domains_option').hide();
            jQuery('.custom_domains_list').hide();
        }

        jQuery('#url_translation_option').show();
        jQuery('#hreflang_tags_option').show();
        jQuery('#email_translation_option').show();
        if(email_translation)
            jQuery('#email_translation_debug_option').show();
        else
            jQuery('#email_translation_debug_option').hide();
    } else {
        jQuery('#custom_domains_option').hide();
        jQuery('#url_translation_option').hide();
        jQuery('#hreflang_tags_option').hide();
        jQuery('#email_translation_option').hide();
        jQuery('#email_translation_debug_option').hide();
    }

    if(widget_look == 'dropdown' || widget_look == 'flags_dropdown' || widget_look == 'globe' || widget_look == 'lang_names' || widget_look == 'lang_codes') {
        jQuery('#dropdown_languages_option').show();
    } else {
        jQuery('#dropdown_languages_option').hide();
    }

    if(widget_look == 'globe') {
        jQuery('#alternative_flags_option').show();
    } else {
        jQuery('#alternative_flags_option').hide();
    }

    if(widget_look == 'flags' || widget_look == 'flags_dropdown' || widget_look == 'float' || widget_look == 'dropdown_with_flags' || widget_look == 'flags_name' || widget_look == 'flags_code' || widget_look == 'popup') {
        jQuery('#flag_languages_option').show();
        jQuery('#alternative_flags_option').show();
    } else {
        jQuery('#flag_languages_option').hide();
        if(widget_look != 'globe')
            jQuery('#alternative_flags_option').hide();
    }

    if(widget_look == 'flags_dropdown') {
        jQuery('#line_break_option').show();
    } else {
        jQuery('#line_break_option').hide();
    }

    if(widget_look == 'dropdown' || widget_look == 'lang_names' || widget_look == 'lang_codes' || widget_look == 'globe') {
        jQuery('#flag_style_option').hide();

        if(widget_look == 'globe')
            jQuery('#flag_size_option').show();
        else
            jQuery('#flag_size_option').hide();
    } else {
        jQuery('#flag_style_option').show();

        if(widget_look == 'float')
            jQuery('#flag_size_option').hide();
        else
            jQuery('#flag_size_option').show();
    }

    if(widget_look == 'dropdown_with_flags') {
        jQuery('.switcher_color_options').show();
        jQuery('#switcher_open_direction_option').show();
    } else {
        jQuery('.switcher_color_options').hide();
        jQuery('#switcher_open_direction_option').hide();
    }

    if(widget_look == 'globe') {
        jQuery('#globe_size_option').show();
        jQuery('.globe_color_options').show();
    } else {
        jQuery('#globe_size_option').hide();
        jQuery('.globe_color_options').hide();
    }

    if(widget_look == 'dropdown') {
        jQuery('#select_language_label_option').show();
    } else {
        jQuery('#select_language_label_option').hide();
    }

    if(widget_look == 'float') {
        jQuery('#float_switcher_open_direction_option').show();
    } else {
        jQuery('#float_switcher_open_direction_option').hide();
    }

    var init_widget_code = '<div class="gtranslate_wrapper"></div>';
    init_widget_code += '<script>window.gtranslateSettings = ' + JSON.stringify(gt_settings) + '<\/script>';
    var widget_short_name = widget_look.split('_').map(function(el){return el.charAt(0)}).join('');
    var widgets_location = '$wp_plugin_url/js/';
    if(widget_short_name.length == 1)
        widget_short_name = widget_look;
    init_widget_code += '<script src="'+widgets_location+widget_short_name+'.js" defer><\/script>';

    jQuery('html').attr('lang', gt_settings.default_language);

    ShowWidgetPreview(init_widget_code);
}

function ShowWidgetPreview(widget_preview) {
    jQuery('#widget_preview').html('');
    jQuery('style.gtranslate_css').remove();
    jQuery('#widget_preview').html(widget_preview);

    setTimeout(function(){
        jQuery('a[data-gt-lang]').attr('onclick', 'return false;');
        jQuery('select.gt_selector option').removeAttr('data-gt-href');
    }, 1000);
}

jQuery('#pro_version').attr('checked', '$pro_version'.length > 0);
jQuery('#enterprise_version').attr('checked', '$enterprise_version'.length > 0);
jQuery('#custom_domains').attr('checked', '$custom_domains'.length > 0);
jQuery('#url_translation').attr('checked', '$url_translation'.length > 0);
jQuery('#add_hreflang_tags').attr('checked', '$add_hreflang_tags'.length > 0);
jQuery('#email_translation').attr('checked', '$email_translation'.length > 0);
jQuery('#email_translation_debug').attr('checked', '$email_translation_debug'.length > 0);
jQuery('#enable_cdn').attr('checked', '$enable_cdn'.length > 0);
jQuery('#select_language_label').val('$select_language_label');
jQuery('#wrapper_selector').val('$wrapper_selector');
jQuery('#show_in_menu').val('$show_in_menu');
jQuery('#floating_language_selector').val('$floating_language_selector');
jQuery('#float_switcher_open_direction').val('$float_switcher_open_direction');
jQuery('#switcher_open_direction').val('$switcher_open_direction');
jQuery('#native_language_names').attr('checked', '$native_language_names'.length > 0);
jQuery('#detect_browser_language').attr('checked', '$detect_browser_language'.length > 0);
jQuery('#add_new_line').attr('checked', '$add_new_line'.length > 0);
jQuery('#default_language').val('$default_language');
jQuery('#widget_look').val('$widget_look');
jQuery('#flag_size').val('$flag_size');
jQuery('#flag_style').val('$flag_style');
jQuery('#switcher_text_color').val('$switcher_text_color');
jQuery('#switcher_arrow_color').val('$switcher_arrow_color');
jQuery('#switcher_border_color').val('$switcher_border_color');
jQuery('#switcher_background_color').val('$switcher_background_color');
jQuery('#switcher_background_shadow_color').val('$switcher_background_shadow_color');
jQuery('#switcher_background_hover_color').val('$switcher_background_hover_color');
jQuery('#dropdown_text_color').val('$dropdown_text_color');
jQuery('#dropdown_hover_color').val('$dropdown_hover_color');
jQuery('#dropdown_background_color').val('$dropdown_background_color');
jQuery('#globe_size').val('$globe_size');
jQuery('#globe_color').val('$globe_color');

if(jQuery('#pro_version:checked').length || jQuery('#enterprise_version:checked').length) {
    if(jQuery('#enterprise_version:checked').length) {
        jQuery('#custom_domains_option').show();
        if(jQuery('#custom_domains:checked').length)
            jQuery('.custom_domains_list').show();
        else
            jQuery('.custom_domains_list').hide();
    } else {
        jQuery('#custom_domains_option').hide();
        jQuery('.custom_domains_list').hide();
    }

    jQuery('#url_translation_option').show();
    jQuery('#hreflang_tags_option').show();
    jQuery('#email_translation_option').show();
    if(jQuery('#email_translation:checked').length)
        jQuery('#email_translation_debug_option').show();
    else
        jQuery('#email_translation_debug_option').hide();
}

if('$widget_look' == 'dropdown' || '$widget_look' == 'flags_dropdown' || '$widget_look' == 'globe' || '$widget_look' == 'lang_names' || '$widget_look' == 'lang_codes') {
    jQuery('#dropdown_languages_option').show();
} else {
    jQuery('#dropdown_languages_option').hide();
}

if('$widget_look' == 'dropdown_with_flags') {
    jQuery('.switcher_color_options').show();
    jQuery('#switcher_open_direction_option').show();
} else {
    jQuery('.switcher_color_options').hide();
    jQuery('#switcher_open_direction_option').hide();
}

if('$widget_look' == 'float') {
    jQuery('#float_switcher_open_direction_option').show();
} else {
    jQuery('#float_switcher_open_direction_option').hide();
}

if('$widget_look' == 'globe') {
    jQuery('#alternative_flags_option').show();
    jQuery('#globe_size_option').show();
    jQuery('.globe_color_options').show();
} else {
    jQuery('#alternative_flags_option').hide();
    jQuery('#globe_size_option').hide();
    jQuery('.globe_color_options').hide();
}

if('$widget_look' == 'flags' || '$widget_look' == 'flags_dropdown' || '$widget_look' == 'float' || '$widget_look' == 'dropdown_with_flags' || '$widget_look' == 'flags_name' || '$widget_look' == 'flags_code' || '$widget_look' == 'popup') {
    jQuery('#flag_languages_option').show();
    jQuery('#alternative_flags_option').show();
} else {
    jQuery('#flag_languages_option').hide();
    if('$widget_look' != 'globe')
        jQuery('#alternative_flags_option').hide();
}

if('$widget_look' == 'flags_dropdown') {
    jQuery('#line_break_option').show();
} else {
    jQuery('#line_break_option').hide();
}

if('$widget_look' == 'dropdown' || '$widget_look' == 'lang_names' || '$widget_look' == 'lang_codes' || '$widget_look' == 'globe') {
    jQuery('#flag_size_option,#flag_style_option').hide();
} else {
    jQuery('#flag_style_option').show();

    if('$widget_look' == 'float')
        jQuery('#flag_size_option').hide();
    else
        jQuery('#flag_size_option').show();
}

if('$widget_look' == 'dropdown') {
    jQuery('#select_language_label_option').show();
} else {
    jQuery('#select_language_label_option').hide();
}

jQuery(function(){
    jQuery(".connectedSortable1").sortable({connectWith: ".connectedSortable1"}).disableSelection();
    jQuery(".connectedSortable2").sortable({connectWith: ".connectedSortable2"}).disableSelection();
    jQuery(".connectedSortable1").on("sortstop", function(event, ui) {
        language_codes = jQuery(".connectedSortable1 li input").map(function() {return jQuery(this).val();}).toArray();

        jQuery('#language_codes_order').val(language_codes.join(','));
        RefreshDoWidgetCode();
    });

    jQuery(".connectedSortable2").on("sortstop", function(event, ui) {
        language_codes2 = jQuery(".connectedSortable2 li input").map(function() {return jQuery(this).val();}).toArray();

        jQuery('#language_codes_order2').val(language_codes2.join(','));
        RefreshDoWidgetCode();
    });
});

function light_color_scheme() {
    jQuery('#switcher_text_color').iris('color', '#666');
    jQuery('#switcher_arrow_color').iris('color', '#666');
    jQuery('#switcher_border_color').iris('color', '#ccc');
    jQuery('#switcher_background_color').iris('color', '#fff');
    jQuery('#switcher_background_shadow_color').iris('color', '#efefef');
    jQuery('#switcher_background_hover_color').iris('color', '#f0f0f0');
    jQuery('#dropdown_text_color').iris('color', '#000');
    jQuery('#dropdown_hover_color').iris('color', '#fff');
    jQuery('#dropdown_background_color').iris('color', '#eee');

    return false;
}

function dark_color_scheme() {
    jQuery('#switcher_text_color').iris('color', '#f7f7f7');
    jQuery('#switcher_arrow_color').iris('color', '#f2f2f2');
    jQuery('#switcher_border_color').iris('color', '#161616');
    jQuery('#switcher_background_color').iris('color', '#303030');
    jQuery('#switcher_background_shadow_color').iris('color', '#474747');
    jQuery('#switcher_background_hover_color').iris('color', '#3a3a3a');
    jQuery('#dropdown_text_color').iris('color', '#eaeaea');
    jQuery('#dropdown_hover_color').iris('color', '#748393');
    jQuery('#dropdown_background_color').iris('color', '#474747');

    return false;
}
EOT;

// selected languages
if(count($fincl_langs) > 0)
    $script .= "jQuery.each(languages, function(i, val) {jQuery('#fincl_langs'+language_codes[i]).attr('checked', false);});\n";
if(count($incl_langs) > 0)
    $script .= "jQuery.each(languages, function(i, val) {jQuery('#incl_langs'+language_codes2[i]).attr('checked', false);});\n";
foreach($fincl_langs as $lang)
    $script .= "jQuery('#fincl_langs$lang').attr('checked', true);\n";
foreach($incl_langs as $lang)
    $script .= "jQuery('#incl_langs$lang').attr('checked', true);\n";

// alt flags
foreach($alt_flags as $flag)
    $script .= "jQuery('#alt_$flag').attr('checked', true);\n";

$script .= <<<EOT
RefreshDoWidgetCode();
EOT;
?>

        <form id="gtranslate" name="form1" method="post" class="notranslate" action="<?php echo admin_url('options-general.php?page=gtranslate_options'); ?>">

        <div class="postbox-container og_left_col">

        <div id="poststuff">
            <div class="postbox">
                <h3 id="settings"><?php _e('Widget options', 'gtranslate'); ?></h3>
                <div class="inside">
                    <table style="width:100%;" cellpadding="4">
                    <tr>
                        <td class="option_name"><?php _e('Widget look', 'gtranslate'); ?>:</td>
                        <td>
                            <select id="widget_look" name="widget_look" onChange="RefreshDoWidgetCode()">
                                <option value="float"><?php _e('Float', 'gtranslate'); ?></option>
                                <option value="dropdown_with_flags"><?php _e('Nice dropdown with flags', 'gtranslate'); ?></option>
                                <option value="popup"><?php _e('Popup', 'gtranslate'); ?></option>
                                <option value="dropdown"><?php _e('Dropdown', 'gtranslate'); ?></option>
                                <option value="flags"><?php _e('Flags', 'gtranslate'); ?></option>
                                <option value="flags_dropdown"><?php _e('Flags and dropdown', 'gtranslate'); ?></option>
                                <option value="flags_name"><?php _e('Flags with language name', 'gtranslate'); ?></option>
                                <option value="flags_code"><?php _e('Flags with language code', 'gtranslate'); ?></option>
                                <option value="lang_names"><?php _e('Language names', 'gtranslate'); ?></option>
                                <option value="lang_codes"><?php _e('Language codes', 'gtranslate'); ?></option>
                                <option value="globe"><?php _e('Globe', 'gtranslate'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Translate from', 'gtranslate'); ?>:</td>
                        <td>
                            <select id="default_language" name="default_language" onChange="RefreshDoWidgetCode()">
                                <option value="af"><?php _e('Afrikaans', 'gtranslate'); ?></option>
                                <option value="sq"><?php _e('Albanian', 'gtranslate'); ?></option>
                                <option value="am"><?php _e('Amharic', 'gtranslate'); ?></option>
                                <option value="ar"><?php _e('Arabic', 'gtranslate'); ?></option>
                                <option value="hy"><?php _e('Armenian', 'gtranslate'); ?></option>
                                <option value="az"><?php _e('Azerbaijani', 'gtranslate'); ?></option>
                                <option value="eu"><?php _e('Basque', 'gtranslate'); ?></option>
                                <option value="be"><?php _e('Belarusian', 'gtranslate'); ?></option>
                                <option value="bn"><?php _e('Bengali', 'gtranslate'); ?></option>
                                <option value="bs"><?php _e('Bosnian', 'gtranslate'); ?></option>
                                <option value="bg"><?php _e('Bulgarian', 'gtranslate'); ?></option>
                                <option value="ca"><?php _e('Catalan', 'gtranslate'); ?></option>
                                <option value="ceb"><?php _e('Cebuano', 'gtranslate'); ?></option>
                                <option value="ny"><?php _e('Chichewa', 'gtranslate'); ?></option>
                                <option value="zh-CN"><?php _e('Chinese (Simplified)', 'gtranslate'); ?></option>
                                <option value="zh-TW"><?php _e('Chinese (Traditional)', 'gtranslate'); ?></option>
                                <option value="co"><?php _e('Corsican', 'gtranslate'); ?></option>
                                <option value="hr"><?php _e('Croatian', 'gtranslate'); ?></option>
                                <option value="cs"><?php _e('Czech', 'gtranslate'); ?></option>
                                <option value="da"><?php _e('Danish', 'gtranslate'); ?></option>
                                <option value="nl"><?php _e('Dutch', 'gtranslate'); ?></option>
                                <option value="en" selected="selected"><?php _e('English', 'gtranslate'); ?></option>
                                <option value="eo"><?php _e('Esperanto', 'gtranslate'); ?></option>
                                <option value="et"><?php _e('Estonian', 'gtranslate'); ?></option>
                                <option value="tl"><?php _e('Filipino', 'gtranslate'); ?></option>
                                <option value="fi"><?php _e('Finnish', 'gtranslate'); ?></option>
                                <option value="fr"><?php _e('French', 'gtranslate'); ?></option>
                                <option value="fy"><?php _e('Frisian', 'gtranslate'); ?></option>
                                <option value="gl"><?php _e('Galician', 'gtranslate'); ?></option>
                                <option value="ka"><?php _e('Georgian', 'gtranslate'); ?></option>
                                <option value="de"><?php _e('German', 'gtranslate'); ?></option>
                                <option value="el"><?php _e('Greek', 'gtranslate'); ?></option>
                                <option value="gu"><?php _e('Gujarati', 'gtranslate'); ?></option>
                                <option value="ht"><?php _e('Haitian Creole', 'gtranslate'); ?></option>
                                <option value="ha"><?php _e('Hausa', 'gtranslate'); ?></option>
                                <option value="haw"><?php _e('Hawaiian', 'gtranslate'); ?></option>
                                <option value="iw"><?php _e('Hebrew', 'gtranslate'); ?></option>
                                <option value="hi"><?php _e('Hindi', 'gtranslate'); ?></option>
                                <option value="hmn"><?php _e('Hmong', 'gtranslate'); ?></option>
                                <option value="hu"><?php _e('Hungarian', 'gtranslate'); ?></option>
                                <option value="is"><?php _e('Icelandic', 'gtranslate'); ?></option>
                                <option value="ig"><?php _e('Igbo', 'gtranslate'); ?></option>
                                <option value="id"><?php _e('Indonesian', 'gtranslate'); ?></option>
                                <option value="ga"><?php _e('Irish', 'gtranslate'); ?></option>
                                <option value="it"><?php _e('Italian', 'gtranslate'); ?></option>
                                <option value="ja"><?php _e('Japanese', 'gtranslate'); ?></option>
                                <option value="jw"><?php _e('Javanese', 'gtranslate'); ?></option>
                                <option value="kn"><?php _e('Kannada', 'gtranslate'); ?></option>
                                <option value="kk"><?php _e('Kazakh', 'gtranslate'); ?></option>
                                <option value="km"><?php _e('Khmer', 'gtranslate'); ?></option>
                                <option value="ko"><?php _e('Korean', 'gtranslate'); ?></option>
                                <option value="ku"><?php _e('Kurdish (Kurmanji)', 'gtranslate'); ?></option>
                                <option value="ky"><?php _e('Kyrgyz', 'gtranslate'); ?></option>
                                <option value="lo"><?php _e('Lao', 'gtranslate'); ?></option>
                                <option value="la"><?php _e('Latin', 'gtranslate'); ?></option>
                                <option value="lv"><?php _e('Latvian', 'gtranslate'); ?></option>
                                <option value="lt"><?php _e('Lithuanian', 'gtranslate'); ?></option>
                                <option value="lb"><?php _e('Luxembourgish', 'gtranslate'); ?></option>
                                <option value="mk"><?php _e('Macedonian', 'gtranslate'); ?></option>
                                <option value="mg"><?php _e('Malagasy', 'gtranslate'); ?></option>
                                <option value="ms"><?php _e('Malay', 'gtranslate'); ?></option>
                                <option value="ml"><?php _e('Malayalam', 'gtranslate'); ?></option>
                                <option value="mt"><?php _e('Maltese', 'gtranslate'); ?></option>
                                <option value="mi"><?php _e('Maori', 'gtranslate'); ?></option>
                                <option value="mr"><?php _e('Marathi', 'gtranslate'); ?></option>
                                <option value="mn"><?php _e('Mongolian', 'gtranslate'); ?></option>
                                <option value="my"><?php _e('Myanmar (Burmese)', 'gtranslate'); ?></option>
                                <option value="ne"><?php _e('Nepali', 'gtranslate'); ?></option>
                                <option value="no"><?php _e('Norwegian', 'gtranslate'); ?></option>
                                <option value="ps"><?php _e('Pashto', 'gtranslate'); ?></option>
                                <option value="fa"><?php _e('Persian', 'gtranslate'); ?></option>
                                <option value="pl"><?php _e('Polish', 'gtranslate'); ?></option>
                                <option value="pt"><?php _e('Portuguese', 'gtranslate'); ?></option>
                                <option value="pa"><?php _e('Punjabi', 'gtranslate'); ?></option>
                                <option value="ro"><?php _e('Romanian', 'gtranslate'); ?></option>
                                <option value="ru"><?php _e('Russian', 'gtranslate'); ?></option>
                                <option value="sm"><?php _e('Samoan', 'gtranslate'); ?></option>
                                <option value="gd"><?php _e('Scottish Gaelic', 'gtranslate'); ?></option>
                                <option value="sr"><?php _e('Serbian', 'gtranslate'); ?></option>
                                <option value="st"><?php _e('Sesotho', 'gtranslate'); ?></option>
                                <option value="sn"><?php _e('Shona', 'gtranslate'); ?></option>
                                <option value="sd"><?php _e('Sindhi', 'gtranslate'); ?></option>
                                <option value="si"><?php _e('Sinhala', 'gtranslate'); ?></option>
                                <option value="sk"><?php _e('Slovak', 'gtranslate'); ?></option>
                                <option value="sl"><?php _e('Slovenian', 'gtranslate'); ?></option>
                                <option value="so"><?php _e('Somali', 'gtranslate'); ?></option>
                                <option value="es"><?php _e('Spanish', 'gtranslate'); ?></option>
                                <option value="su"><?php _e('Sundanese', 'gtranslate'); ?></option>
                                <option value="sw"><?php _e('Swahili', 'gtranslate'); ?></option>
                                <option value="sv"><?php _e('Swedish', 'gtranslate'); ?></option>
                                <option value="tg"><?php _e('Tajik', 'gtranslate'); ?></option>
                                <option value="ta"><?php _e('Tamil', 'gtranslate'); ?></option>
                                <option value="te"><?php _e('Telugu', 'gtranslate'); ?></option>
                                <option value="th"><?php _e('Thai', 'gtranslate'); ?></option>
                                <option value="tr"><?php _e('Turkish', 'gtranslate'); ?></option>
                                <option value="uk"><?php _e('Ukrainian', 'gtranslate'); ?></option>
                                <option value="ur"><?php _e('Urdu', 'gtranslate'); ?></option>
                                <option value="uz"><?php _e('Uzbek', 'gtranslate'); ?></option>
                                <option value="vi"><?php _e('Vietnamese', 'gtranslate'); ?></option>
                                <option value="cy"><?php _e('Welsh', 'gtranslate'); ?></option>
                                <option value="xh"><?php _e('Xhosa', 'gtranslate'); ?></option>
                                <option value="yi"><?php _e('Yiddish', 'gtranslate'); ?></option>
                                <option value="yo"><?php _e('Yoruba', 'gtranslate'); ?></option>
                                <option value="zu"><?php _e('Zulu', 'gtranslate'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="option_name">* <?php _e('Sub-directory URL structure', 'gtranslate'); ?>:<br><code><small>http://example.com/<b>ru</b>/</small></code></td>
                        <td><input id="pro_version" name="pro_version" value="1" type="checkbox" onclick="if(jQuery('#pro_version').is(':checked') && jQuery('#enterprise_version').is(':checked'))jQuery('#enterprise_version').prop('checked', false);RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()"/> <a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" title="If you already have a subscription, you can enable this." rel="noreferrer">* <?php _e('for paid plans only', 'gtranslate'); ?></a></td>
                    </tr>
                    <tr>
                        <td class="option_name">* <?php _e('Sub-domain URL structure', 'gtranslate'); ?>:<br><code><small>http://<b>es</b>.example.com/</small></code></td>
                        <td><input id="enterprise_version" name="enterprise_version" value="1" type="checkbox" onclick="if(jQuery('#pro_version').is(':checked') && jQuery('#enterprise_version').is(':checked'))jQuery('#pro_version').prop('checked', false);RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()"/> <a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" title="If you already have a subscription, you can enable this." rel="noreferrer">* <?php _e('for paid plans only', 'gtranslate'); ?></a></td>
                    </tr>
                    <tr id="custom_domains_option" style="display:none;">
                        <td class="option_name"><?php _e('Custom domains', 'gtranslate'); ?>:<br><code><small>http://example.<b>es</b>/</small></code></td>
                        <td><input id="custom_domains" name="custom_domains" value="1" type="checkbox" onclick="if(jQuery('#custom_domains').is(':checked'))SyncCustomDomains();RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()"/> <span id="custom_domains_status_sync" style="display:none;"><span class="dashicons dashicons-update gt-icon-spin"></span> <?php _e('Synchronizing...', 'gtranslate'); ?></span> <input type="hidden" id="custom_domains_data" name="custom_domains_data" value="<?php echo esc_attr(stripslashes($data['custom_domains_data'])); ?>"></td>
                    </tr>
                    <tr id="url_translation_option" style="display:none;">
                        <td class="option_name"><?php _e('Enable URL Translation', 'gtranslate'); ?>:</td>
                        <td><input id="url_translation" name="url_translation" value="1" type="checkbox"/></td>
                    </tr>
                    <tr id="hreflang_tags_option" style="display:none;">
                        <td class="option_name"><?php _e('Add hreflang tags', 'gtranslate'); ?>:</td>
                        <td><input id="add_hreflang_tags" name="add_hreflang_tags" value="1" type="checkbox"/></td>
                    </tr>
                    <tr id="email_translation_option" style="display:none;">
                        <td class="option_name"><?php _e('Enable WooCommerce Email Translation', 'gtranslate'); ?>:</td>
                        <td><input id="email_translation" name="email_translation" value="1" type="checkbox"/></td>
                    </tr>
                    <tr id="email_translation_debug_option" style="display:none;">
                        <td class="option_name"><?php _e('Debug Email Translation', 'gtranslate'); ?>:</td>
                        <td><input id="email_translation_debug" name="email_translation_debug" value="1" type="checkbox"/></td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Native language names', 'gtranslate'); ?>:</td>
                        <td><input id="native_language_names" name="native_language_names" value="1" type="checkbox" onclick="RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()"/></td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Auto switch to browser language', 'gtranslate'); ?>:</td>
                        <td><input id="detect_browser_language" name="detect_browser_language" value="1" type="checkbox"/></td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Enable CDN', 'gtranslate'); ?>:</td>
                        <td><input id="enable_cdn" name="enable_cdn" value="1" type="checkbox" onclick="RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()"/></td>
                    </tr>
                    <tr id="select_language_label_option" style="display:none">
                        <td class="option_name"><?php _e('Select language label', 'gtranslate'); ?>:</td>
                        <td><input id="select_language_label" name="select_language_label" type="text" onchange="RefreshDoWidgetCode()"/></td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Show in menu', 'gtranslate'); ?>: <a href="#TB_inline?width=700&height=150&inlineId=show-in-menu-option-description" title="<?php echo esc_attr(translate('Learn more', 'gtranslate')); ?>" class="thickbox" style="text-decoration:none"><span class="dashicons dashicons-editor-help"></span></a><div id="show-in-menu-option-description" style="display:none"><p><?php _e('Show in menu option is best for <b>Flags</b>, <b>Flags with language name</b>, <b>Flags with language code</b>, <b>Language names</b>, <b>Language codes</b> widget looks.', 'gtranslate'); ?></p><p><?php _e('Other looks most likely will require additional CSS rules to match your theme design.', 'gtranslate'); ?></p></div></td>
                        <td>
                            <select id="show_in_menu" name="show_in_menu">
                                <option value="" selected> - <?php _e('None', 'gtranslate'); ?> - </option>
                                <?php $menus = get_registered_nav_menus(); ?>
                                <?php foreach($menus as $location => $description): ?>
                                <option value="<?php echo $location; ?>"><?php echo $description; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Show floating language selector', 'gtranslate'); ?>: <a href="#TB_inline?width=700&height=150&inlineId=show-floating-language-selector-option-description" title="<?php echo esc_attr(translate('Learn more', 'gtranslate')); ?>" class="thickbox" style="text-decoration:none"><span class="dashicons dashicons-editor-help"></span></a><div id="show-floating-language-selector-option-description" style="display:none"><p><?php _e('Show floating language selector option is the easiest and suitable for most websites. It is best for <b>Float</b>, <b>Nice dropdown with flags</b>, <b>Popup</b>, <b>Globe</b> widget looks.', 'gtranslate'); ?></p></div></td>
                        <td>
                            <select id="floating_language_selector" name="floating_language_selector">
                                <option value="no"><?php _e('No', 'gtranslate'); ?></option>
                                <option value="bottom_left"><?php _e('Bottom left', 'gtranslate'); ?></option>
                                <option value="bottom_right"><?php _e('Bottom right', 'gtranslate'); ?></option>
                                <option value="top_left"><?php _e('Top left', 'gtranslate'); ?></option>
                                <option value="top_right"><?php _e('Top right', 'gtranslate'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="option_name"><?php _e('Wrapper selector CSS', 'gtranslate'); ?>: <a href="#TB_inline?width=700&height=170&inlineId=wrapper-selector-option-description" title="<?php echo esc_attr(translate('Learn more', 'gtranslate')); ?>" class="thickbox" style="text-decoration:none"><span class="dashicons dashicons-editor-help"></span></a><div id="wrapper-selector-option-description" style="display:none"><p><?php _e('If you want the language selector to appear inside a particular HTML element on your page then this option is for you. You simply need to write a CSS selector to point to that HTML element and GTranslate will appear inside of it.', 'gtranslate'); ?></p><p><?php _e('If you are not using this option make sure it is empty or has the default value to not have additional unused code on your front-end. Default value for Wrapper Selector is <code>.gtranslate_wrapper</code>', 'gtranslate'); ?></p></div></td>
                        <td><input id="wrapper_selector" name="wrapper_selector" type="text" placeholder=".gtranslate_wrapper" onchange="RefreshDoWidgetCode()"/></td>
                    </tr>
                    <tr id="float_switcher_open_direction_option" style="display:none">
                        <td class="option_name"><?php _e('Open direction', 'gtranslate'); ?>:</td>
                        <td>
                            <select id="float_switcher_open_direction" name="float_switcher_open_direction" onchange="RefreshDoWidgetCode()">
                                <option value="left"><?php _e('Left', 'gtranslate'); ?></option>
                                <option value="right"><?php _e('Right', 'gtranslate'); ?></option>
                                <option value="top"><?php _e('Top', 'gtranslate'); ?></option>
                                <option value="bottom"><?php _e('Bottom', 'gtranslate'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="switcher_open_direction_option" style="display:none">
                        <td class="option_name"><?php _e('Open direction', 'gtranslate'); ?>:</td>
                        <td>
                            <select id="switcher_open_direction" name="switcher_open_direction" onchange="RefreshDoWidgetCode()">
                                <option value="top"><?php _e('Top', 'gtranslate'); ?></option>
                                <option value="bottom"><?php _e('Bottom', 'gtranslate'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="flag_size_option">
                        <td class="option_name"><?php _e('Flag size', 'gtranslate'); ?>:</td>
                        <td>
                        <select id="flag_size"  name="flag_size" onchange="RefreshDoWidgetCode()">
                            <option value="16" selected>16px</option>
                            <option value="24">24px</option>
                            <option value="32">32px</option>
                            <option value="48">48px</option>
                        </select>
                        </td>
                    </tr>
                    <tr id="flag_style_option">
                        <td class="option_name"><?php _e('Flag style', 'gtranslate'); ?>:</td>
                        <td>
                        <select id="flag_style"  name="flag_style" onchange="RefreshDoWidgetCode()">
                            <option value="3d">3D (.png)</option>
                            <option value="2d">2D (.svg)</option>
                        </select>
                        </td>
                    </tr>
                    <tr id="globe_size_option">
                        <td class="option_name"><?php _e('Globe size', 'gtranslate'); ?>:</td>
                        <td>
                        <select id="globe_size"  name="globe_size" onchange="RefreshDoWidgetCode()">
                            <option value="20">20px</option>
                            <option value="40">40px</option>
                            <option value="60">60px</option>
                        </select>
                        </td>
                    </tr>
                    <tr id="flag_languages_option" style="display:none;">
                        <td class="option_name" colspan="2"><div><?php _e('Flag languages', 'gtranslate'); ?>: <a onclick="jQuery('.connectedSortable1 input').attr('checked', true);RefreshDoWidgetCode()" style="cursor:pointer;text-decoration:underline;"><?php _e('Check All', 'gtranslate'); ?></a> | <a onclick="jQuery('.connectedSortable1 input').attr('checked', false);RefreshDoWidgetCode()" style="cursor:pointer;text-decoration:underline;"><?php _e('Uncheck All', 'gtranslate'); ?></a> <span style="float:right;"><b>HINT</b>: To reorder the languages simply drag and drop them in the list below.</span></div><br />
                        <div>
                        <?php $gt_lang_codes = explode(',', $language_codes); ?>
                        <?php for($i = 0; $i < count($gt_lang_array) / 26; $i++): ?>
                        <ul style="list-style-type:none;width:25%;float:left;" class="connectedSortable1">
                            <?php for($j = $i * 26; $j < 26 * ($i+1); $j++): ?>
                            <?php if(isset($gt_lang_codes[$j])): ?>
                            <li><input type="checkbox" onclick="RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()" id="fincl_langs<?php echo $gt_lang_codes[$j]; ?>" name="fincl_langs[]" value="<?php echo $gt_lang_codes[$j]; ?>"><label for="fincl_langs<?php echo $gt_lang_codes[$j]; ?>"><span class="en_names"><?php _e($gt_lang_array[$gt_lang_codes[$j]], 'gtranslate'); ?></span></label></li>
                            <?php endif; ?>
                            <?php endfor; ?>
                        </ul>
                        <?php endfor; ?>
                        </div>
                        </td>
                    </tr>
                    <tr id="line_break_option" style="display:none;">
                        <td class="option_name"><?php _e('Line break after flags', 'gtranslate'); ?>:</td>
                        <td><input id="add_new_line" name="add_new_line" value="1" type="checkbox" checked="checked" onclick="RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()"/></td>
                    </tr>
                    <tr id="dropdown_languages_option" style="display:none;">
                        <td class="option_name" colspan="2"><div><?php _e('Languages', 'gtranslate'); ?>: <a onclick="jQuery('.connectedSortable2 input').attr('checked', true);RefreshDoWidgetCode()" style="cursor:pointer;text-decoration:underline;"><?php _e('Check All', 'gtranslate'); ?></a> | <a onclick="jQuery('.connectedSortable2 input').attr('checked', false);RefreshDoWidgetCode()" style="cursor:pointer;text-decoration:underline;"><?php _e('Uncheck All', 'gtranslate'); ?></a> <span style="float:right;"><b>HINT</b>: To reorder the languages simply drag and drop them in the list below.</span></div><br />
                        <div>
                        <?php $gt_lang_codes = explode(',', $language_codes2); ?>
                        <?php for($i = 0; $i < count($gt_lang_array) / 26; $i++): ?>
                        <ul style="list-style-type:none;width:25%;float:left;" class="connectedSortable2">
                            <?php for($j = $i * 26; $j < 26 * ($i+1); $j++): ?>
                            <?php if(isset($gt_lang_codes[$j])): ?>
                            <li><input type="checkbox" onclick="RefreshDoWidgetCode()" onchange="RefreshDoWidgetCode()" id="incl_langs<?php echo $gt_lang_codes[$j]; ?>" name="incl_langs[]" value="<?php echo $gt_lang_codes[$j]; ?>"><label for="incl_langs<?php echo $gt_lang_codes[$j]; ?>"><span class="en_names"><?php _e($gt_lang_array[$gt_lang_codes[$j]], 'gtranslate'); ?></span></label></li>
                            <?php endif; ?>
                            <?php endfor; ?>
                        </ul>
                        <?php endfor; ?>
                        </div>
                        </td>
                    </tr>
                    </table>
                </div>
            </div>
        </div>

        <div id="poststuff">
            <div class="postbox">
                <h3 id="settings"><?php _e('Custom CSS', 'gtranslate'); ?> <a href="#TB_inline?width=700&height=170&inlineId=common-customization-tips-description" title="<?php echo esc_attr(translate('Common customizations tips')); ?>" class="thickbox" style="text-decoration:none"><span class="dashicons dashicons-editor-help"></span></a></h3>
                <div class="inside">
                    <textarea id="custom_css" name="custom_css" onchange="RefreshDoWidgetCode()" style="font-family:Monospace;font-size:11px;height:150px;width:565px;"><?php echo htmlspecialchars($custom_css, ENT_QUOTES, get_option('blog_charset')); ?></textarea><br />
                    <div id="common-customization-tips-description" style="display:none">
                        <p><?php _e('Hide current language:'); ?> <code>a.gt-current-lang{display:none}</code></p>
                        <p><?php _e('Monochrome flags:'); ?> <code>a[data-gt-lang] img{filter:grayscale(1)}</code></p>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="switcher_text_color" id="switcher_text_color_hidden" value="<?php echo $switcher_text_color; ?>" />
        <input type="hidden" name="switcher_arrow_color" id="switcher_arrow_color_hidden" value="<?php echo $switcher_arrow_color; ?>" />
        <input type="hidden" name="switcher_border_color" id="switcher_border_color_hidden" value="<?php echo $switcher_border_color; ?>" />
        <input type="hidden" name="switcher_background_color" id="switcher_background_color_hidden" value="<?php echo $switcher_background_color; ?>" />
        <input type="hidden" name="switcher_background_shadow_color" id="switcher_background_shadow_color_hidden" value="<?php echo $switcher_background_shadow_color; ?>" />
        <input type="hidden" name="switcher_background_hover_color" id="switcher_background_hover_color_hidden" value="<?php echo $switcher_background_hover_color; ?>" />
        <input type="hidden" name="dropdown_text_color" id="dropdown_text_color_hidden" value="<?php echo $dropdown_text_color; ?>" />
        <input type="hidden" name="dropdown_hover_color" id="dropdown_hover_color_hidden" value="<?php echo $dropdown_hover_color; ?>" />
        <input type="hidden" name="dropdown_background_color" id="dropdown_background_color_hidden" value="<?php echo $dropdown_background_color; ?>" />

        <input type="hidden" name="globe_color" id="globe_color_hidden" value="<?php echo $globe_color; ?>" />

        <div style="display:none">
            <input type="checkbox" name="alt_flags[]" id="alt_us_hidden" value="us" <?php if(in_array('us', $alt_flags)) echo 'checked'; ?> />
            <input type="checkbox" name="alt_flags[]" id="alt_ca_hidden" value="ca" <?php if(in_array('ca', $alt_flags)) echo 'checked'; ?> />
            <input type="checkbox" name="alt_flags[]" id="alt_br_hidden" value="br" <?php if(in_array('br', $alt_flags)) echo 'checked'; ?> />
            <input type="checkbox" name="alt_flags[]" id="alt_mx_hidden" value="mx" <?php if(in_array('mx', $alt_flags)) echo 'checked'; ?> />
            <input type="checkbox" name="alt_flags[]" id="alt_ar_hidden" value="ar" <?php if(in_array('ar', $alt_flags)) echo 'checked'; ?> />
            <input type="checkbox" name="alt_flags[]" id="alt_co_hidden" value="co" <?php if(in_array('co', $alt_flags)) echo 'checked'; ?> />
            <input type="checkbox" name="alt_flags[]" id="alt_qc_hidden" value="qc" <?php if(in_array('qc', $alt_flags)) echo 'checked'; ?> />
        </div>

        <input type="hidden" id="language_codes_order" name="language_codes" value="<?php echo $language_codes; ?>" />
        <input type="hidden" id="language_codes_order2" name="language_codes2" value="<?php echo $language_codes2; ?>" />
        <?php wp_nonce_field('gtranslate-save'); ?>

        <p class="submit"><input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes'); ?>" /></p>

        <p style="margin-top:-10px;"><a target="_blank" href="https://wordpress.org/support/plugin/gtranslate/reviews/?filter=5" rel="noreferrer"><?php _e('Love GTranslate? Give us 5 stars on WordPress.org :)', 'gtranslate'); ?></a></p>

        </div>

        </form>

        <div class="postbox-container og_right_col">
            <div id="poststuff">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Widget preview', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <div id="widget_preview"></div>
                    </div>
                </div>
            </div>

            <div id="poststuff" class="custom_domains_list" style="display:none;">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Language hosting', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <table id="custom_domains_list_tbl" style="width:100%;" cellpadding="0">
                            <tr>
                                <th><?php _e('Language', 'gtranslate'); ?></th>
                                <th><?php _e('Domain', 'gtranslate'); ?></th>
                            </tr>
                            <?php
                            if(isset($data['custom_domains_data']) and !empty($data['custom_domains_data'])) {
                                $custom_domains_data = json_decode(stripslashes($data['custom_domains_data']), true);

                                if(is_array($custom_domains_data))
                                    foreach($custom_domains_data as $k => $v)
                                        echo '<tr class="lang_domain_row"><td>'.esc_html($k).'</td><td>'.esc_html($v).'</td></tr>';
                            }
                            ?>
                        </table>
                        <br>
                        <input type="button" class="button-secondary" value="Synchronize" onclick="SyncCustomDomains();RefreshDoWidgetCode();" title="<?php esc_attr_e('Synchronize custom domains with GTranslate dashboard: https://my.gtranslate.io', 'gtranslate'); ?>">
                    </div>
                </div>
            </div>

            <div id="poststuff" class="switcher_color_options" style="display:none">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Color options', 'gtranslate'); ?> ( <a href="#" onclick="return light_color_scheme()">light</a> | <a href="#" onclick="return dark_color_scheme()">dark</a> )</h3>
                    <div class="inside">
                        <table style="width:100%;" cellpadding="0">
                            <tr>
                                <td class="option_name"><?php _e('Switcher text color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="switcher_text_color" id="switcher_text_color" class="color-field" value="#666" data-default-color="#666" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Switcher arrow color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="switcher_arrow_color" id="switcher_arrow_color" class="color-field" value="#666" data-default-color="#666" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Switcher border color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="switcher_border_color" id="switcher_border_color" class="color-field" value="#ccc" data-default-color="#ccc" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Switcher background color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="switcher_background_color" id="switcher_background_color" class="color-field" value="#fff" data-default-color="#fff" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Switcher background shadow color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="switcher_background_shadow_color" id="switcher_background_shadow_color" class="color-field" value="#fff" data-default-color="#efefef" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Switcher background hover color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="switcher_background_hover_color" id="switcher_background_hover_color" class="color-field" value="#f0f0f0" data-default-color="#f0f0f0" /></td>
                            </tr>

                            <tr>
                                <td class="option_name"><?php _e('Dropdown text color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="dropdown_text_color" id="dropdown_text_color" class="color-field" value="#000" data-default-color="#000" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Dropdown hover color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="dropdown_hover_color" id="dropdown_hover_color" class="color-field" value="#fff" data-default-color="#fff" /></td>
                            </tr>
                            <tr>
                                <td class="option_name"><?php _e('Dropdown background color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="dropdown_background_color" id="dropdown_background_color" class="color-field" value="#eee" data-default-color="#eee" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div id="poststuff" class="globe_color_options" style="display:none">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Color options', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <table style="width:100%;" cellpadding="0">
                            <tr>
                                <td class="option_name"><?php _e('Globe color', 'gtranslate'); ?>:</td>
                                <td><input type="text" name="globe_color" id="globe_color" class="color-field" value="#66aaff" data-default-color="#66aaff" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div id="poststuff" class="alternative_flags_option">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Alternative flags', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <input type="checkbox" id="alt_us" name="alt_flags2[]" value="us" data-lang-group="en"><label for="alt_us"><?php _e('USA flag', 'gtranslate'); ?> (<?php _e('English', 'gtranslate'); ?>)</label><br />
                        <input type="checkbox" id="alt_ca" name="alt_flags2[]" value="ca" data-lang-group="en"><label for="alt_ca"><?php _e('Canada flag', 'gtranslate'); ?> (<?php _e('English', 'gtranslate'); ?>)</label><br />
                        <input type="checkbox" id="alt_br" name="alt_flags2[]" value="br" data-lang-group="pt"><label for="alt_br"><?php _e('Brazil flag', 'gtranslate'); ?> (<?php _e('Portuguese', 'gtranslate'); ?>)</label><br />
                        <input type="checkbox" id="alt_mx" name="alt_flags2[]" value="mx" data-lang-group="es"><label for="alt_mx"><?php _e('Mexico flag', 'gtranslate'); ?> (<?php _e('Spanish', 'gtranslate'); ?>)</label><br />
                        <input type="checkbox" id="alt_ar" name="alt_flags2[]" value="ar" data-lang-group="es"><label for="alt_ar"><?php _e('Argentina flag', 'gtranslate'); ?> (<?php _e('Spanish', 'gtranslate'); ?>)</label><br />
                        <input type="checkbox" id="alt_co" name="alt_flags2[]" value="co" data-lang-group="es"><label for="alt_co"><?php _e('Colombia flag', 'gtranslate'); ?> (<?php _e('Spanish', 'gtranslate'); ?>)</label><br />
                        <input type="checkbox" id="alt_qc" name="alt_flags2[]" value="qc" data-lang-group="fr"><label for="alt_qc"><?php _e('Quebec flag', 'gtranslate'); ?> (<?php _e('French', 'gtranslate'); ?>)</label><br />
                    </div>
                </div>
            </div>

            <div id="poststuff">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Language selector positioning tips', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <ul style="list-style-type:square;padding-left:20px;">
                            <li style="margin:0;"><?php _e('Show floating language selector option is the easiest and suitable for most websites.', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Show in menu option is best for <b>Flags</b>, <b>Flags with language name</b>, <b>Flags with language code</b>, <b>Language names</b>, <b>Language codes</b> widget looks.', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('You can use GTranslate Widget in any pre-defined widget locations.', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('<code>[gtranslate]</code> shortcode can be used anywhere on your website.', 'gtranslate'); ?> <a href="#TB_inline?width=700&height=170&inlineId=gtranslate-shortcode-description" title="<?php echo esc_attr(translate('Learn more', 'gtranslate')); ?>" class="thickbox" style="text-decoration:none"><span class="dashicons dashicons-welcome-learn-more"></span></a><div id="gtranslate-shortcode-description" style="display:none"><p><?php _e('You can use <code>[gtranslate]</code> inside posts, menu items or anywhere else.', 'gtranslate'); ?> <?php _e('In theme files you can call <code>echo do_shortcode(\'[gtranslate]\');</code> in PHP context.', 'gtranslate'); ?></p><p><?php _e('You can use additional widget_look attribute to place a specific selector, for example <code>[gtranslate widget_look="popup"]</code>. Valid values are <b>float</b>, <b>dropdown_with_flags</b>, <b>popup</b>, <b>dropdown</b>, <b>flags</b>, <b>flags_dropdown</b>, <b>flags_name</b>, <b>flags_code</b>, <b>lang_names</b>, <b>lang_codes</b>, <b>globe</b>.', 'gtranslate'); ?></p></div></li>
                            <li style="margin:0;"><?php _e('<code>[gt-link lang="en" label="English" widget_look="flags_name"]</code> shortcode can be used to render individual language links.', 'gtranslate'); ?> <a href="#TB_inline?width=700&height=240&inlineId=gt-link-shortcode-description" title="<?php echo esc_attr(translate('Learn more', 'gtranslate')); ?>" class="thickbox" style="text-decoration:none"><span class="dashicons dashicons-welcome-learn-more"></span></a><div id="gt-link-shortcode-description" style="display:none"><p><?php _e('It is mainly used to easily place individual language links inside menu items. For example you can create a menu item with URL = #, Navigation Label = Spanish and Description = <code>[gt-link lang="es" label="Spanish" widget_look="flags_name"]</code> and a single menu item will appear to change the language to Spanish.', 'gtranslate'); ?></p><p><?php _e('Valid values for widget_look attribute are <b>flags</b>, <b>flags_code</b>, <b>flags_name</b>, <b>lang_codes</b>, <b>lang_names</b>.', 'gtranslate'); ?></p><p><?php _e('Language codes for lang attribute are case sensitive. The full list can be found on <a href="https://gtranslate.io/supported-languages" target="_blank" rel="noreferrer">https://gtranslate.io/supported-languages</a>', 'gtranslate'); ?></p></div></li>
                            <li style="margin:0;"><?php _e('Wrapper selector CSS can be used to render the language selector inside matching elements.', 'gtranslate'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="poststuff">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Paid version advantages', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <ul style="list-style-type:square;padding-left:20px;">
                            <li style="margin:0;"><?php _e('Search engine indexing', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Search engine friendly (SEF) URLs', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Human level neural translations', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Edit translations manually', 'gtranslate'); ?></li>
                            <li style="margin:0;"><a href="https://gtranslate.io/website-translation-quote" title="Website Translation Price Calculator" target="_blank" rel="noreferrer"><?php _e('Automatic translation post-editing service and professional translations', 'gtranslate'); ?></a></li>
                            <li style="margin:0;"><?php _e('Meta data translation (keywords, page description, etc...)', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('URL/slug translation', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Language hosting (custom domain like example.fr, example.es)', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Seamless updates', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Increased international traffic and AdSense revenue', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Works in China', 'gtranslate'); ?></li>
                            <li style="margin:0;"><?php _e('Priority Live Chat support', 'gtranslate'); ?></li>
                        </ul>

                        <a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" class="button-primary" rel="noreferrer"><?php _e('Try Now (15 days free)', 'gtranslate'); ?></a> <a href="https://gtranslate.io/?xyz=998#faq" target="_blank" class="button-primary" rel="noreferrer"><?php _e('FAQ', 'gtranslate'); ?></a> <a href="https://gtranslate.io/website-translation-quote" target="_blank" class="button-primary" rel="noreferrer"><?php _e('Website Translation Quote', 'gtranslate'); ?></a> <a href="https://gtranslate.io/?xyz=998#contact" target="_blank" class="button-primary" rel="noreferrer"><?php _e('Live Chat', 'gtranslate'); ?></a>
                    </div>
                </div>
            </div>

            <div id="poststuff">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Useful links', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <style>
                        ul.useful_links_list {list-style-type:square;padding-left:20px;margin:0;}
                        ul.useful_links_list li {margin:0;}
                        ul.useful_links_list li a {text-decoration:none;}
                        </style>
                        <table style="width:100%;" cellpadding="4">
                            <tr>
                                <td>
                                    <ul class="useful_links_list">
                                        <li><a href="https://gtranslate.io/videos" target="_blank" rel="noreferrer"><?php _e('Videos', 'gtranslate'); ?></a></li>
                                        <li><a href="https://docs.gtranslate.io/how-tos" target="_blank" rel="noreferrer"><?php _e('How-tos', 'gtranslate'); ?></a></li>
                                        <li><a href="https://gtranslate.io/blog" target="_blank" rel="noreferrer"><?php _e('Blog', 'gtranslate'); ?></a></li>
                                        <li><a href="https://gtranslate.io/about-us" target="_blank" rel="noreferrer"><?php _e('About GTranslate team', 'gtranslate'); ?></a></li>
                                        <li><a href="https://gtranslate.io/?xyz=998#faq" target="_blank" rel="noreferrer"><?php _e('FAQ', 'gtranslate'); ?></a></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="useful_links_list">
                                        <li><a href="https://my.gtranslate.io/" target="_blank" rel="noreferrer"><?php _e('User dashboard', 'gtranslate'); ?></a></li>
                                        <li><a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" rel="noreferrer"><?php _e('Compare plans', 'gtranslate'); ?></a></li>
                                        <li><a href="https://gtranslate.io/website-translation-quote" target="_blank" rel="noreferrer"><?php _e('Website Translation Quote', 'gtranslate'); ?></a></li>
                                        <li><a href="https://gtranslate.io/detect-browser-language" target="_blank" rel="noreferrer"><?php _e('Detect browser language', 'gtranslate'); ?></a></li>
                                        <li><a href="https://wordpress.org/support/plugin/gtranslate/reviews/" target="_blank" rel="noreferrer"><?php _e('Reviews', 'gtranslate'); ?></a></li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div id="poststuff">
                <div class="postbox">
                    <h3 id="settings"><?php _e('Live Chat (for paid plans and pre-sales questions)', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <p>2am - 6pm (Mon - Fri) UTC-4</p>
                        <p><?php _e('We are here to make your experience with GTranslate more convenient.'); ?></p>
                    </div>
                    <h3 id="settings"><?php _e('Forum Support (free)', 'gtranslate'); ?></h3>
                    <div class="inside">
                        <p><a href="https://wordpress.org/support/plugin/gtranslate/" target="_blank" rel="noreferrer"><?php _e('WordPress Forum Support', 'gtranslate'); ?></a></p>
                        <p><?php _e('We try to help everyone as time permits.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <script><?php echo $script; ?></script>
        <style>
        #widget_preview a:focus {box-shadow:none;outline:none;}
        #custom_domains_list_tbl th {text-align:left;}
        #custom_domains_list_tbl td {padding:5px 0;}
        .switcher_color_options button {box-shadow:none !important;border:1px solid #b4b9be !important;border-radius:0 !important;}
        .switcher_color_options h3 a {text-decoration:none;font-weight:400;}
        .switcher_color_options h3 a:hover {text-decoration:underline;}
        .postbox #settings {padding-left:12px;}
        .og_left_col {      width: 59%;     }
        .og_right_col {     width: 39%;     float: right;       }
        .og_left_col #poststuff,        .og_right_col #poststuff {      min-width: 0;       }
        table.form-table tr th,     table.form-table tr td {        line-height: 1.5;       }
        table.form-table tr th {        font-weight: bold;      }
        table.form-table tr th[scope=row] { min-width: 300px;       }
        table.form-table tr td hr {     height: 1px;        margin: 0px;        background-color: #DFDFDF;      border: none;       }
        table.form-table .dashicons-before {        margin-right: 10px;     font-size: 12px;        opacity: 0.5;       }
        table.form-table .dashicons-facebook-alt {      color: #3B5998;     }
        table.form-table .dashicons-googleplus {        color: #D34836;     }
        table.form-table .dashicons-twitter {       color: #55ACEE;     }
        table.form-table .dashicons-rss {       color: #FF6600;     }
        table.form-table .dashicons-admin-site,     table.form-table .dashicons-admin-generic {     color: #666;        }

        .connectedSortable1, .connectedSortable1 li, .connectedSortable2, .connectedSortable2 li {margin:0;padding:0;}
        .connectedSortable1 li label, .connectedSortable2 li label {cursor:move;}

        @keyframes gt-icon-spin-animation {
            0% {transform:rotate(0deg);}
            100% {transform:rotate(359deg);}
        }
        .gt-icon-spin {animation:gt-icon-spin-animation 2s infinite linear;}
        </style>

        <script>window.intercomSettings = {app_id: "r70azrgx", 'platform': 'wordpress', 'translate_from': '<?php echo $default_language; ?>', 'is_sub_directory': <?php echo (empty($pro_version) ? '0' : '1'); ?>, 'is_sub_domain': <?php echo (empty($enterprise_version) ? '0' : '1'); ?>};(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/r70azrgx';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>

        <?php
    }

    public static function control_options() {
        check_admin_referer('gtranslate-save');

        $data = get_option('GTranslate');
        if(!is_array($data))
            self::load_defaults($data);

        $data['pro_version'] = isset($_POST['pro_version']) ? intval($_POST['pro_version']) : '';
        $data['enterprise_version'] = isset($_POST['enterprise_version']) ? intval($_POST['enterprise_version']) : '';
        $data['wrapper_selector'] = isset($_POST['wrapper_selector']) ? sanitize_text_field($_POST['wrapper_selector']) : '.gtranslate_wrapper';
        $data['custom_domains'] = isset($_POST['custom_domains']) ? intval($_POST['custom_domains']) : '';
        $data['custom_domains_data'] = isset($_POST['custom_domains_data']) ? sanitize_text_field($_POST['custom_domains_data']) : '';
        $data['url_translation'] = isset($_POST['url_translation']) ? intval($_POST['url_translation']) : '';
        $data['add_hreflang_tags'] = isset($_POST['add_hreflang_tags']) ? intval($_POST['add_hreflang_tags']) : '';
        $data['email_translation'] = isset($_POST['email_translation']) ? intval($_POST['email_translation']) : '';
        $data['email_translation_debug'] = isset($_POST['email_translation_debug']) ? intval($_POST['email_translation_debug']) : '';
        $data['enable_cdn'] = isset($_POST['enable_cdn']) ? intval($_POST['enable_cdn']) : '';
        $data['show_in_menu'] = isset($_POST['show_in_menu']) ? sanitize_text_field($_POST['show_in_menu']) : '';
        $data['floating_language_selector'] = isset($_POST['floating_language_selector']) ? sanitize_text_field($_POST['floating_language_selector']) : 'no';
        $data['native_language_names'] = isset($_POST['native_language_names']) ? intval($_POST['native_language_names']) : '';
        $data['detect_browser_language'] = isset($_POST['detect_browser_language']) ? intval($_POST['detect_browser_language']) : '';
        $data['add_new_line'] = isset($_POST['add_new_line']) ? intval($_POST['add_new_line']) : '';
        $data['default_language'] = isset($_POST['default_language']) ? sanitize_text_field($_POST['default_language']) : 'en';
        $data['widget_look'] = isset($_POST['widget_look']) ? sanitize_text_field($_POST['widget_look']) : 'float';
        $data['flag_size'] = isset($_POST['flag_size']) ? intval($_POST['flag_size']) : 24;
        $data['flag_style'] = isset($_POST['flag_style']) ? sanitize_text_field($_POST['flag_style']) : '2d';
        $data['globe_size'] = isset($_POST['globe_size']) ? intval($_POST['globe_size']) : 60;
        $data['globe_color'] = isset($_POST['globe_color']) ? sanitize_hex_color($_POST['globe_color']) : '#66aaff';
        $data['incl_langs'] = (isset($_POST['incl_langs']) and is_array($_POST['incl_langs'])) ? array_map('sanitize_text_field', $_POST['incl_langs']) : array($data['default_language']);
        $data['fincl_langs'] = (isset($_POST['fincl_langs']) and is_array($_POST['fincl_langs'])) ? array_map('sanitize_text_field', $_POST['fincl_langs']) : array($data['default_language']);
        $data['alt_flags'] = (isset($_POST['alt_flags']) and is_array($_POST['alt_flags'])) ? array_map('sanitize_text_field', $_POST['alt_flags']) : array();
        $data['select_language_label'] = isset($_POST['select_language_label']) ? sanitize_text_field($_POST['select_language_label']) : 'Select Language';

        $data['custom_css'] = isset($_POST['custom_css']) ? wp_kses_post($_POST['custom_css']) : '';

        $data['switcher_text_color'] = isset($_POST['switcher_text_color']) ? sanitize_hex_color($_POST['switcher_text_color']) : '#666';
        $data['switcher_arrow_color'] = isset($_POST['switcher_arrow_color']) ? sanitize_hex_color($_POST['switcher_arrow_color']) : '#666';
        $data['switcher_border_color'] = isset($_POST['switcher_border_color']) ? sanitize_hex_color($_POST['switcher_border_color']) : '#ccc';
        $data['switcher_background_color'] = isset($_POST['switcher_background_color']) ? sanitize_hex_color($_POST['switcher_background_color']) : '#fff';
        $data['switcher_background_shadow_color'] = isset($_POST['switcher_background_shadow_color']) ? sanitize_hex_color($_POST['switcher_background_shadow_color']) : '#efefef';
        $data['switcher_background_hover_color'] = isset($_POST['switcher_background_color']) ? sanitize_hex_color($_POST['switcher_background_hover_color']) : '#f0f0f0';
        $data['dropdown_text_color'] = isset($_POST['dropdown_text_color']) ? sanitize_hex_color($_POST['dropdown_text_color']) : '#000';
        $data['dropdown_hover_color'] = isset($_POST['dropdown_hover_color']) ? sanitize_hex_color($_POST['dropdown_hover_color']) : '#fff';
        $data['dropdown_background_color'] = isset($_POST['dropdown_background_color']) ? sanitize_hex_color($_POST['dropdown_background_color']) : '#eee';

        $data['float_switcher_open_direction'] = isset($_POST['float_switcher_open_direction']) ? sanitize_text_field($_POST['float_switcher_open_direction']) : 'top';
        $data['switcher_open_direction'] = isset($_POST['switcher_open_direction']) ? sanitize_text_field($_POST['switcher_open_direction']) : 'top';

        $data['language_codes'] = (isset($_POST['language_codes']) and !empty($_POST['language_codes'])) ? sanitize_text_field($_POST['language_codes']) : 'af,sq,ar,hy,az,eu,be,bg,ca,zh-CN,zh-TW,hr,cs,da,nl,en,et,tl,fi,fr,gl,ka,de,el,ht,iw,hi,hu,is,id,ga,it,ja,ko,lv,lt,mk,ms,mt,no,fa,pl,pt,ro,ru,sr,sk,sl,es,sw,sv,th,tr,uk,ur,vi,cy,yi';
        $data['language_codes2'] = (isset($_POST['language_codes2']) and !empty($_POST['language_codes2'])) ? sanitize_text_field($_POST['language_codes2']) : 'af,sq,am,ar,hy,az,eu,be,bn,bs,bg,ca,ceb,ny,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,tl,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,iw,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,ko,ku,ky,lo,la,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,te,th,tr,uk,ur,uz,vi,cy,xh,yi,yo,zu';

        echo '<p style="color:red;">' . __('Changes Saved', 'gtranslate') . '</p>';
        update_option('GTranslate', $data);

        if($data['pro_version']) { // check if rewrite rules are in place
            $htaccess_file = get_home_path() . '.htaccess';
            // todo: use insert_with_markers functions instead
            if(is_writeable($htaccess_file)) {
                $htaccess = file_get_contents($htaccess_file);
                if(strpos($htaccess, 'gtranslate.php') === false) { // no config rules
                    $rewrite_rules = file_get_contents(dirname(__FILE__) . '/url_addon/rewrite.txt');
                    $rewrite_rules = str_replace('GTRANSLATE_PLUGIN_PATH', str_replace(str_replace(array('https:', 'http:'), array(':', ':'), home_url()), '', str_replace(array('https:', 'http:'), array(':', ':'), plugins_url())) . '/gtranslate', $rewrite_rules);

                    $htaccess = $rewrite_rules . "\r\n\r\n" . $htaccess;
                    if(!empty($htaccess)) { // going to update .htaccess
                        file_put_contents($htaccess_file, $htaccess);
                        echo '<p style="color:red;">' . __('.htaccess file updated', 'gtranslate') . '</p>';
                    }
                }
            } else {
                $rewrite_rules = file_get_contents(dirname(__FILE__) . '/url_addon/rewrite.txt');
                $rewrite_rules = str_replace('GTRANSLATE_PLUGIN_PATH', str_replace(home_url(), '', plugins_url()) . '/gtranslate', $rewrite_rules);

                echo '<p style="color:red;">' . __('Please add the following rules to the top of your .htaccess file', 'gtranslate') . '</p>';
                echo '<pre style="background-color:#eaeaea;">' . $rewrite_rules . '</pre>';
            }

            // update main_lang in config.php
            $config_file = dirname(__FILE__) . '/url_addon/config.php';
            if(is_readable($config_file) and is_writable($config_file)) {
                $config = file_get_contents($config_file);
                $config = preg_replace('/\$main_lang = \'[a-z-]{2,5}\'/i', '$main_lang = \''.$data['default_language'].'\'', $config);
                file_put_contents($config_file, $config);
            } else {
                echo '<p style="color:red;">' . __('Cannot update gtranslate/url_addon/config.php file. Make sure to update it manually and set correct $main_lang.', 'gtranslate') . '</p>';
            }

        } else { // todo: remove rewrite rules
            // do nothing
        }
    }

    public static function load_settings($data) {
        $languages = $data['fincl_langs'];
        $dropdown_languages = $data['incl_langs'];
        if($data['widget_look'] == 'dropdown' or $data['widget_look'] == 'globe' or $data['widget_look'] == 'lang_names' or $data['widget_look'] == 'lang_codes')
            $languages = $dropdown_languages;

        $url_structure = 'none';
        if($data['pro_version'])
            $url_structure = 'sub_directory';
        elseif($data['enterprise_version'])
            $url_structure = 'sub_domain';

        $gt_settings = array(
            'default_language' => $data['default_language'],
            'languages' => $languages,
            'dropdown_languages' => $dropdown_languages,
            'url_structure' => $url_structure,
            'wrapper_selector' => $data['wrapper_selector'],
            'globe_size' => $data['globe_size'],
            'globe_color' => $data['globe_color'],
            'flag_size' => $data['flag_size'],
            'flag_style' => $data['flag_style'],
            'custom_domains' => $data['custom_domains'] ? $data['custom_domains_data'] : null,
            'float_switcher_open_direction' => $data['float_switcher_open_direction'],
            'switcher_open_direction' => $data['switcher_open_direction'],
            'native_language_names' => $data['native_language_names'],
            'add_new_line' => $data['add_new_line'],
            'select_language_label' => $data['select_language_label'],
            'detect_browser_language' => $data['detect_browser_language'],
            'custom_css' => $data['custom_css'],
            'switcher_text_color' => $data['switcher_text_color'],
            'switcher_arrow_color' => $data['switcher_arrow_color'],
            'switcher_border_color' => $data['switcher_border_color'],
            'switcher_background_color' => $data['switcher_background_color'],
            'switcher_background_shadow_color' => $data['switcher_background_shadow_color'],
            'switcher_background_hover_color' => $data['switcher_background_hover_color'],
            'dropdown_text_color' => $data['dropdown_text_color'],
            'dropdown_hover_color' => $data['dropdown_hover_color'],
            'dropdown_background_color' => $data['dropdown_background_color'],
        );

        $alt_flags = array();
        $raw_alt_flags = $data['alt_flags']; // example raw_alt_flags: ['us', 'br', 'ar']
        foreach($raw_alt_flags as $country_code) {
            switch($country_code) {
                case 'us': $alt_flags['en'] = 'usa'; break;
                case 'ca': $alt_flags['en'] = 'canada'; break;
                case 'br': $alt_flags['pt'] = 'brazil'; break;
                case 'mx': $alt_flags['es'] = 'mexico'; break;
                case 'ar': $alt_flags['es'] = 'argentina'; break;
                case 'co': $alt_flags['es'] = 'colombia'; break;
                case 'qc': $alt_flags['fr'] = 'quebec'; break;
                default: break;
            }
        }
        $gt_settings['alt_flags'] = $alt_flags;

        if(!empty($gt_settings['custom_domains']))
            $gt_settings['custom_domains'] = json_decode(stripslashes($gt_settings['custom_domains']));

        return $gt_settings;
    }

    public static function load_defaults(& $data) {
        if(!is_array($data))
            $data = array();

        $data['pro_version'] = isset($data['pro_version']) ? $data['pro_version'] : '';
        $data['enterprise_version'] = isset($data['enterprise_version']) ? $data['enterprise_version'] : '';
        $data['wrapper_selector'] = isset($data['wrapper_selector']) ? $data['wrapper_selector'] : '.gtranslate_wrapper';
        $data['custom_domains'] = isset($data['custom_domains']) ? $data['custom_domains'] : '';
        $data['custom_domains_data'] = isset($data['custom_domains_data']) ? $data['custom_domains_data'] : '';
        $data['url_translation'] = isset($data['url_translation']) ? $data['url_translation'] : '';
        $data['add_hreflang_tags'] = isset($data['add_hreflang_tags']) ? $data['add_hreflang_tags'] : '';
        $data['email_translation'] = isset($data['email_translation']) ? $data['email_translation'] : '';
        $data['email_translation_debug'] = isset($data['email_translation_debug']) ? $data['email_translation_debug'] : '';
        $data['show_in_menu'] = isset($data['show_in_menu']) ? $data['show_in_menu'] : ((isset($data['show_in_primary_menu']) and $data['show_in_primary_menu'] == 1) ? 'primary' : '');

        $data['floating_language_selector'] = isset($data['floating_language_selector']) ? $data['floating_language_selector'] : 'no';
        $data['floating_language_selector'] = str_replace('_sticky', '', $data['floating_language_selector']);

        $data['native_language_names'] = isset($data['native_language_names']) ? $data['native_language_names'] : '';
        $data['enable_cdn'] = isset($data['enable_cdn']) ? $data['enable_cdn'] : '';
        $data['detect_browser_language'] = isset($data['detect_browser_language']) ? $data['detect_browser_language'] : '';
        $data['add_new_line'] = isset($data['add_new_line']) ? $data['add_new_line'] : 1;
        $data['select_language_label'] = isset($data['select_language_label']) ? $data['select_language_label'] : 'Select Language';

        $data['custom_css'] = isset($data['custom_css']) ? $data['custom_css'] : '';

        if(!isset($data['default_language'])) {
            $locale_map = array('af'=>'af','am'=>'am','arq'=>'ar','ar'=>'ar','ary'=>'ar','az'=>'az','az_TR'=>'az','azb'=>'az','bel'=>'be','bg_BG'=>'bg','bn_BD'=>'bn','bs_BA'=>'bs','ca'=>'ca','bal'=>'ca','ceb'=>'ceb','co'=>'co','cs_CZ'=>'cs','cy'=>'cy','da_DK'=>'da','de_DE'=>'de','de_CH'=>'de','gsw'=>'de','el'=>'el','en_AU'=>'en','en_CA'=>'en','en_NZ'=>'en','en_ZA'=>'en','en_GB'=>'en','eo'=>'eo','es_AR'=>'es','es_CL'=>'es','es_CO'=>'es','es_GT'=>'es','es_MX'=>'es','es_PE'=>'es','es_PR'=>'es','es_ES'=>'es','es_VE'=>'es','et'=>'et','eu'=>'eu','fa_IR'=>'fa','fa_AF'=>'fa','fi'=>'fi','fr_BE'=>'fr','fr_CA'=>'fr','fr_FR'=>'fr','fy'=>'fy','ga'=>'ga','gd'=>'gd','gl_ES'=>'gl','gu'=>'gu','hau'=>'ha','haw_US'=>'haw','hi_IN'=>'hi','hr'=>'hr','hat'=>'ht','hu_HU'=>'hu','hy'=>'hy','id_ID'=>'id','is_IS'=>'is','it_IT'=>'it','he_IL'=>'iw','ja'=>'ja','jv_ID'=>'jw','ka_GE'=>'ka','kk'=>'kk','km'=>'km','kn'=>'kn','ko_KR'=>'ko','ckb'=>'ku','kir'=>'ky','lb_LU'=>'lb','lo'=>'lo','lt_LT'=>'lt','lv'=>'lv','mg_MG'=>'mg','mri'=>'mi','mk_MK'=>'mk','ml_IN'=>'ml','mn'=>'mn','mr'=>'mr','ms_MY'=>'ms','my_MM'=>'my','ne_NP'=>'ne','nl_NL'=>'nl','nl_BE'=>'nl','nb_NO'=>'no','nn_NO'=>'no','pa_IN'=>'pa','pl_PL'=>'pl','ps'=>'ps','pt_BR'=>'pt','pt_PT'=>'pt','ro_RO'=>'ro','ru_RU'=>'ru','snd'=>'sd','si_LK'=>'si','sk_SK'=>'sk','sl_SI'=>'sl','so_SO'=>'so','sq'=>'sq','sr_RS'=>'sr','su_ID'=>'su','sv_SE'=>'sv','sw'=>'sw','ta_IN'=>'ta','ta_LK'=>'ta','te'=>'te','tg'=>'tg','th'=>'th','tr_TR'=>'tr','uk'=>'uk','ur'=>'ur','uz_UZ'=>'uz','vi'=>'vi','xho'=>'xh','yor'=>'yo','zh_CN'=>'zh-CN','zh_HK'=>'zh-CN','zh_TW'=>'zh-TW');
            $locale = get_locale();
            $data['default_language'] = isset($locale_map[$locale]) ? $locale_map[$locale] : 'en';
        }

        $data['widget_look'] = isset($data['widget_look']) ? $data['widget_look'] : 'float';
        $data['flag_size'] = isset($data['flag_size']) ? $data['flag_size'] : 24;
        $data['flag_style'] = isset($data['flag_style']) ? $data['flag_style'] : '2d';
        $data['globe_size'] = isset($data['globe_size']) ? intval($data['globe_size']) : 60;
        $data['globe_color'] = isset($data['globe_color']) ? sanitize_hex_color($data['globe_color']) : '#66aaff';
        $data['incl_langs'] = isset($data['incl_langs']) ? $data['incl_langs'] : array('en', 'es', 'it', 'pt', 'de', 'fr', 'ru', 'nl', 'ar', 'zh-CN');
        $data['fincl_langs'] = isset($data['fincl_langs']) ? $data['fincl_langs'] : array('en', 'es', 'it', 'pt', 'de', 'fr', 'ru', 'nl', 'ar', 'zh-CN');
        $data['alt_flags'] = isset($data['alt_flags']) ? $data['alt_flags'] : array();

        $data['switcher_text_color'] = isset($data['switcher_text_color']) ? $data['switcher_text_color'] : '#666';
        $data['switcher_arrow_color'] = isset($data['switcher_arrow_color']) ? $data['switcher_arrow_color'] : '#666';
        $data['switcher_border_color'] = isset($data['switcher_border_color']) ? $data['switcher_border_color'] : '#ccc';
        $data['switcher_background_color'] = isset($data['switcher_background_color']) ? $data['switcher_background_color'] : '#fff';
        $data['switcher_background_shadow_color'] = isset($data['switcher_background_shadow_color']) ? $data['switcher_background_shadow_color'] : '#efefef';
        $data['switcher_background_hover_color'] = isset($data['switcher_background_hover_color']) ? $data['switcher_background_hover_color'] : '#fff';
        $data['dropdown_text_color'] = isset($data['dropdown_text_color']) ? $data['dropdown_text_color'] : '#000';
        $data['dropdown_hover_color'] = isset($data['dropdown_hover_color']) ? $data['dropdown_hover_color'] : '#fff'; // #ffc
        $data['dropdown_background_color'] = isset($data['dropdown_background_color']) ? $data['dropdown_background_color'] : '#eee';

        $data['float_switcher_open_direction'] = isset($data['float_switcher_open_direction']) ? $data['float_switcher_open_direction'] : 'top';
        $data['switcher_open_direction'] = isset($data['switcher_open_direction']) ? $data['switcher_open_direction'] : 'top';

        $data['language_codes'] = (isset($data['language_codes']) and !empty($data['language_codes'])) ? $data['language_codes'] : 'af,sq,am,ar,hy,az,eu,be,bn,bs,bg,ca,ceb,ny,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,tl,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,iw,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,ko,ku,ky,lo,la,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,te,th,tr,uk,ur,uz,vi,cy,xh,yi,yo,zu';
        $data['language_codes2'] = (isset($data['language_codes2']) and !empty($data['language_codes2'])) ? $data['language_codes2'] : 'af,sq,am,ar,hy,az,eu,be,bn,bs,bg,ca,ceb,ny,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,tl,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,iw,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,ko,ku,ky,lo,la,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,te,th,tr,uk,ur,uz,vi,cy,xh,yi,yo,zu';

        // add missing languages once
        if(strlen($data['language_codes']) < strlen($data['language_codes2']))
            $data['language_codes'] = $data['language_codes2'];
    }
}

class GTranslateWidget extends WP_Widget {

    function __construct() {
        parent::__construct('gtranslate', esc_html__('GTranslate', 'gtranslate'), array('description' => esc_html__('GTranslate language switcher', 'gtranslate')));
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        if(!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        echo GTranslate::get_widget_code(array('position' => 'inline', 'wrapper_selector' => '.gtranslate_wrapper'));

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
        <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'gtranslate'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

}

class GTranslate_Notices {
    protected $prefix = 'gtranslate';
    public $notice_spam = 0;
    public $notice_spam_max = 3;

    // Basic actions to run
    public function __construct() {
        // Runs the admin notice ignore function incase a dismiss button has been clicked
        add_action('admin_init', array($this, 'admin_notice_ignore'));
        // Runs the admin notice temp ignore function incase a temp dismiss link has been clicked
        add_action('admin_init', array($this, 'admin_notice_temp_ignore'));

        // Adding notices
        add_action('admin_notices', array($this, 'gt_admin_notices'));
    }

    // Checks to ensure notices aren't disabled and the user has the correct permissions.
    public function gt_admin_notice() {

        $gt_settings = get_option($this->prefix . '_admin_notice');
        if (!isset($gt_settings['disable_admin_notices']) || (isset($gt_settings['disable_admin_notices']) && $gt_settings['disable_admin_notices'] == 0)) {
            if (current_user_can('manage_options')) {
                return true;
            }
        }
        return false;
    }

    // Primary notice function that can be called from an outside function sending necessary variables
    public function admin_notice($admin_notices) {

        // Check options
        if (!$this->gt_admin_notice()) {
            return false;
        }

        foreach ($admin_notices as $slug => $admin_notice) {
            // Call for spam protection

            if ($this->anti_notice_spam()) {
                return false;
            }

            // Check for proper page to display on
            if (isset( $admin_notices[$slug]['pages']) and is_array( $admin_notices[$slug]['pages'])) {

                if (!$this->admin_notice_pages($admin_notices[$slug]['pages'])) {
                    return false;
                }

            }

            // Check for required fields
            if (!$this->required_fields($admin_notices[$slug])) {

                // Get the current date then set start date to either passed value or current date value and add interval
                $current_date = current_time("n/j/Y");
                $start = (isset($admin_notices[$slug]['start']) ? $admin_notices[$slug]['start'] : $current_date);
                $start = date("n/j/Y", strtotime($start));
                $end = ( isset( $admin_notices[ $slug ]['end'] ) ? $admin_notices[ $slug ]['end'] : $start );
                $end = date( "n/j/Y", strtotime( $end ) );
                $date_array = explode('/', $start);
                $interval = (isset($admin_notices[$slug]['int']) ? $admin_notices[$slug]['int'] : 0);
                $date_array[1] += $interval;
                $start = date("n/j/Y", mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));
                // This is the main notices storage option
                $admin_notices_option = get_option($this->prefix . '_admin_notice', array());
                // Check if the message is already stored and if so just grab the key otherwise store the message and its associated date information
                if (!array_key_exists( $slug, $admin_notices_option)) {
                    $admin_notices_option[$slug]['start'] = $start;
                    $admin_notices_option[$slug]['int'] = $interval;
                    update_option($this->prefix . '_admin_notice', $admin_notices_option);
                }

                // Sanity check to ensure we have accurate information
                // New date information will not overwrite old date information
                $admin_display_check = (isset($admin_notices_option[$slug]['dismissed']) ? $admin_notices_option[$slug]['dismissed'] : 0);
                $admin_display_start = (isset($admin_notices_option[$slug]['start']) ? $admin_notices_option[$slug]['start'] : $start);
                $admin_display_interval = (isset($admin_notices_option[$slug]['int']) ? $admin_notices_option[$slug]['int'] : $interval);
                $admin_display_msg = (isset($admin_notices[$slug]['msg']) ? $admin_notices[$slug]['msg'] : '');
                $admin_display_title = (isset($admin_notices[$slug]['title']) ? $admin_notices[$slug]['title'] : '');
                $admin_display_link = (isset($admin_notices[$slug]['link']) ? $admin_notices[$slug]['link'] : '');
                $admin_display_dismissible= (isset($admin_notices[$slug]['dismissible']) ? $admin_notices[$slug]['dismissible'] : true);
                $output_css = false;

                // Ensure the notice hasn't been hidden and that the current date is after the start date
                if ($admin_display_check == 0 and strtotime($admin_display_start) <= strtotime($current_date)) {
                    // Get remaining query string
                    $query_str = esc_url(add_query_arg($this->prefix . '_admin_notice_ignore', $slug));

                    // Admin notice display output
                    echo '<div class="update-nag gt-admin-notice">';
                    echo '<div class="gt-notice-logo"></div>';
                    echo ' <p class="gt-notice-title">';
                    echo $admin_display_title;
                    echo ' </p>';
                    echo ' <p class="gt-notice-body">';
                    echo $admin_display_msg;
                    echo ' </p>';
                    echo '<ul class="gt-notice-body gt-red">
                          ' . $admin_display_link . '
                        </ul>';
                    if($admin_display_dismissible)
                        echo '<a href="' . $query_str . '" class="dashicons dashicons-dismiss"></a>';
                    echo '</div>';

                    $this->notice_spam += 1;
                    $output_css = true;
                }

                if ($output_css) {
                    wp_enqueue_style($this->prefix . '-admin-notices', plugins_url(plugin_basename(dirname(__FILE__))) . '/gtranslate-notices.css', array());
                }
            }

        }
    }

    // Spam protection check
    public function anti_notice_spam() {
        if ($this->notice_spam >= $this->notice_spam_max) {
            return true;
        }
        return false;
    }

    // Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
    public function admin_notice_ignore() {
        // If user clicks to ignore the notice, update the option to not show it again
        if (isset($_GET[$this->prefix . '_admin_notice_ignore'])) {
            $admin_notices_option = get_option($this->prefix . '_admin_notice', array());

            $key = $_GET[$this->prefix . '_admin_notice_ignore'];
            if(!preg_match('/^[a-z_0-9]+$/i', $key))
                return;

            $admin_notices_option[$key]['dismissed'] = 1;
            update_option($this->prefix . '_admin_notice', $admin_notices_option);
            $query_str = remove_query_arg($this->prefix . '_admin_notice_ignore');
            wp_redirect($query_str);
            exit;
        }
    }

    // Temp Ignore function that gets ran at admin init to ensure any messages that were temp dismissed get their start date changed
    public function admin_notice_temp_ignore() {
        // If user clicks to temp ignore the notice, update the option to change the start date - default interval of 14 days
        if (isset($_GET[$this->prefix . '_admin_notice_temp_ignore'])) {
            $admin_notices_option = get_option($this->prefix . '_admin_notice', array());
            $current_date = current_time("n/j/Y");
            $date_array   = explode('/', $current_date);
            $interval     = (isset($_GET['gt_int']) ? intval($_GET['gt_int']) : 14);
            $date_array[1] += $interval;
            $new_start = date("n/j/Y", mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));

            $key = $_GET[$this->prefix . '_admin_notice_temp_ignore'];
            if(!preg_match('/^[a-z_0-9]+$/i', $key))
                return;

            $admin_notices_option[$key]['start'] = $new_start;
            $admin_notices_option[$key]['dismissed'] = 0;
            update_option($this->prefix . '_admin_notice', $admin_notices_option);
            $query_str = remove_query_arg(array($this->prefix . '_admin_notice_temp_ignore', 'gt_int'));
            wp_redirect( $query_str );
            exit;
        }
    }

    public function admin_notice_pages($pages) {
        foreach ($pages as $key => $page) {
            if (is_array($page)) {
                if (isset($_GET['page']) and $_GET['page'] == $page[0] and isset($_GET['tab']) and $_GET['tab'] == $page[1]) {
                    return true;
                }
            } else {
                if ($page == 'all') {
                    return true;
                }
                if (get_current_screen()->id === $page) {
                    return true;
                }

                if (isset($_GET['page']) and $_GET['page'] == $page) {
                    return true;
                }
            }
        }

        return false;
    }

    // Required fields check
    public function required_fields( $fields ) {
        if (!isset( $fields['msg']) or (isset($fields['msg']) and empty($fields['msg']))) {
            return true;
        }
        if (!isset( $fields['title']) or (isset($fields['title']) and empty($fields['title']))) {
            return true;
        }
        return false;
    }

    // Special parameters function that is to be used in any extension of this class
    public function special_parameters($admin_notices) {
        // Intentionally left blank
    }

    public function gt_admin_notices() {

        $deactivate_plugins= array('WP Translator' => 'wptranslator/WPTranslator.php', 'TranslatePress' => 'translatepress-multilingual/index.php', 'Google Language Translator' => 'google-language-translator/google-language-translator.php', 'Google Website Translator' => 'google-website-translator/google-website-translator.php', 'Weglot' => 'weglot/weglot.php', 'TransPosh' => 'transposh-translation-filter-for-wordpress/transposh.php', 'Advanced Google Translate' => 'advanced-google-translate/advanced-google-translate.php', 'My WP Translate' => 'my-wp-translate/my-wp-translate.php', 'WPML Multilingual CMS' => 'sitepress-multilingual-cms/sitepress.php');
        foreach($deactivate_plugins as $name => $plugin_file) {
            if(is_plugin_active($plugin_file)) {
                $deactivate_link = wp_nonce_url('plugins.php?action=deactivate&amp;plugin='.urlencode($plugin_file ).'&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file);
                $notices['deactivate_plugin_'.strtolower(str_replace(' ', '', $name))] = array(
                    'title' => sprintf(__('Please deactivate %s plugin', 'gtranslate'), $name),
                    'msg' => sprintf(__('%s plugin causes conflicts with GTranslate.', 'gtranslate'), $name),
                    'link' => '<li><span class="dashicons dashicons-dismiss"></span><a href="'.$deactivate_link.'">' . sprintf(__('Deactivate %s plugin', 'gtranslate'), $name) . '</a></li>',
                    'dismissible' => false,
                    'int' => 0
                );
            }
        }

        /*
        $one_week_support = esc_url(add_query_arg(array($this->prefix . '_admin_notice_ignore' => 'one_week_support')));

        $notices['one_week_support'] = array(
          'title' => __('Hey! How is it going?', 'gtranslate'),
          'msg' => __('Thank you for using GTranslate! We hope that you have found everything you need, but if you have any questions you can use our Live Chat or Forum:', 'gtranslate'),
          'link' => '<li><span class="dashicons dashicons-admin-comments"></span><a target="_blank" href="https://gtranslate.io/#contact" rel="noreferrer">' . __('Get help', 'gtranslate') . '</a></li>' .
                    '<li><span class="dashicons dashicons-format-video"></span><a target="_blank" href="https://gtranslate.io/videos" rel="noreferrer">'.__('Check videos', 'gtranslate') . '</a></li>' .
                    '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $one_week_support . '">' . __('Never show again', 'gtranslate') . '</a></li>',
          'int' => 1
        );
        */

        $two_week_review_ignore = esc_url(add_query_arg(array($this->prefix . '_admin_notice_ignore' => 'two_week_review')));
        $two_week_review_temp = esc_url(add_query_arg(array($this->prefix . '_admin_notice_temp_ignore' => 'two_week_review', 'gt_int' => 6)));

        $notices['two_week_review'] = array(
            'title' => __('Please Leave a Review', 'gtranslate'),
            'msg' => __("We hope you have enjoyed using GTranslate! Would you mind taking a few minutes to write a review on WordPress.org? <br>Just writing a simple <b>'thank you'</b> will make us happy!", 'gtranslate'),
            'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://wordpress.org/support/plugin/gtranslate/reviews/?filter=5" target="_blank" rel="noreferrer">' . __('Sure! I would love to!', 'gtranslate') . '</a></li>' .
                      '<li><span class="dashicons dashicons-smiley"></span><a href="' . $two_week_review_ignore . '">' . __('I have already left a review', 'gtranslate') . '</a></li>' .
                      '<li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $two_week_review_temp . '">' . __('Maybe later', 'gtranslate') . '</a></li>' .
                      '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $two_week_review_ignore . '">' . __('Never show again', 'gtranslate') . '</a></li>',
            'later_link' => $two_week_review_temp,
            'int' => 5
        );

        $data = get_option('GTranslate');
        GTranslate::load_defaults($data);

        // check if email debug is on and add a notice
        if($data['email_translation_debug']) {
            $settings_link = admin_url('options-general.php?page=gtranslate_options');
            $view_debug_link = admin_url('plugin-editor.php?file=gtranslate%2Furl_addon%2Fdebug.txt&plugin=gtranslate%2Fgtranslate.php');
            $notices['gt_debug_notice'] = array(
                'title' => __('Email translation debug mode is ON.', 'gtranslate'),
                'msg' => __('Please note that sensitive information can be written into gtranslate/url_addon/debug.txt file, which can be accessed publicly. It is your responsibility to deny public access to it and clean debug information after you are done.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-admin-settings"></span><a href="'.$settings_link.'">' . __('GTranslate Settings', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-visibility"></span><a href="'.$view_debug_link.'">' . __('View debug.txt', 'gtranslate') . '</a></li>',
                'dismissible' => false,
                'int' => 0
            );
        }

        // check if translation debug is on and add a notice
        include dirname(__FILE__) . '/url_addon/config.php';
        if($debug) {
            $edit_file_link = admin_url('plugin-editor.php?file=gtranslate%2Furl_addon%2Fconfig.php&plugin=gtranslate%2Fgtranslate.php');
            $view_debug_link = admin_url('plugin-editor.php?file=gtranslate%2Furl_addon%2Fdebug.txt&plugin=gtranslate%2Fgtranslate.php');
            $notices['gt_debug_notice'] = array(
                'title' => __('Translation debug mode is ON.', 'gtranslate'),
                'msg' => __('Please note that sensitive information can be written into gtranslate/url_addon/debug.txt file, which can be accessed publicly. It is your responsibility to deny public access to it and clean debug information after you are done.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-edit"></span><a href="'.$edit_file_link.'">' . __('Edit config.php', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-visibility"></span><a href="'.$view_debug_link.'">' . __('View debug.txt', 'gtranslate') . '</a></li>',
                'dismissible' => false,
                'int' => 0
            );
        }

        $upgrade_tips_ignore = esc_url(add_query_arg(array($this->prefix . '_admin_notice_ignore' => 'upgrade_tips')));
        $upgrade_tips_temp = esc_url(add_query_arg(array($this->prefix . '_admin_notice_temp_ignore' => 'upgrade_tips', 'gt_int' => 7)));

        if($data['pro_version'] != '1' and $data['enterprise_version'] != '1') {
            $notices['upgrade_tips'][] = array(
                'title' => __('Did you know?', 'gtranslate'),
                'msg' => __('You can have <b>neural machine translations</b> which are human level by upgrading your GTranslate.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" rel="noreferrer">' . __('Learn more', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $upgrade_tips_temp . '">' . __('Maybe later', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $upgrade_tips_ignore . '">' . __('Never show again', 'gtranslate') . '</a></li>',
                'later_link' => $upgrade_tips_temp,
                'int' => 2
            );

            $notices['upgrade_tips'][] = array(
                'title' => __('Did you know?', 'gtranslate'),
                'msg' => __('You can <b>increase</b> your international <b>traffic</b> by upgrading your GTranslate.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" rel="noreferrer">' . __('Learn more', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $upgrade_tips_temp . '">' . __('Maybe later', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $upgrade_tips_ignore . '">' . __('Never show again', 'gtranslate') . '</a></li>',
                'later_link' => $upgrade_tips_temp,
                'int' => 2
            );

            $notices['upgrade_tips'][] = array(
                'title' => __('Did you know?', 'gtranslate'),
                'msg' => __('You can have your <b>translated pages indexed</b> in search engines by upgrading your GTranslate.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" rel="noreferrer">' . __('Learn more', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $upgrade_tips_temp . '">' . __('Maybe later', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $upgrade_tips_ignore . '">' . __('Never show again', 'gtranslate') . '</a></li>',
                'later_link' => $upgrade_tips_temp,
                'int' => 2
            );

            $notices['upgrade_tips'][] = array(
                'title' => __('Did you know?', 'gtranslate'),
                'msg' => __('You can <b>increase</b> your <b>AdSense revenue</b> by upgrading your GTranslate.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" rel="noreferrer">' . __('Learn more', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $upgrade_tips_temp . '">' . __('Maybe later', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $upgrade_tips_ignore . '">' . __('Never show again', 'gtranslate') . '</a></li>',
                'later_link' => $upgrade_tips_temp,
                'int' => 2
            );

            $notices['upgrade_tips'][] = array(
                'title' => __('Did you know?', 'gtranslate'),
                'msg' => __('You can <b>edit translations</b> by upgrading your GTranslate.', 'gtranslate'),
                'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://gtranslate.io/?xyz=998#pricing" target="_blank" rel="noreferrer">' . __('Learn more', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $upgrade_tips_temp . '">' . __('Maybe later', 'gtranslate') . '</a></li>' .
                          '<li><span class="dashicons dashicons-dismiss"></span><a href="' . $upgrade_tips_ignore . '">' . __('Never show again', 'gtranslate') . '</a></li>',
                'later_link' => $upgrade_tips_temp,
                'int' => 2
            );

            shuffle($notices['upgrade_tips']);
            $notices['upgrade_tips'] = $notices['upgrade_tips'][0];
        }

        $this->admin_notice($notices);
    }

}

if(is_admin()) {
    if(!defined('DOING_AJAX') or !DOING_AJAX)
        new GTranslate_Notices();
}

$data = get_option('GTranslate');
GTranslate::load_defaults($data);

if($data['pro_version']) { // gtranslate redirect rules with PHP (for environments with no .htaccess support (pantheon, flywheel, etc.), usually .htaccess rules override this)

    $url_params = explode('?', $_SERVER['REQUEST_URI']);
    $request_uri = $url_params[0];
    if(isset($url_params[1]))
        $query_params = $url_params[1];
    else
        $query_params = '';

    if(preg_match('/^\/(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)\/(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)\/(.*)$/', $request_uri, $matches)) {
        header('Location: ' . '/' . $matches[1] . '/' . $matches[3] . (empty($query_params) ? '' : '?'.$query_params), true, 301);
        exit;
    } // #1 redirect double language codes /es/en/...

    if(preg_match('/^\/(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)$/', $request_uri)) {
        header('Location: ' . $request_uri . '/' . (empty($query_params) ? '' : '?'.$query_params), true, 301);
        exit;
    } // #2 add trailing slash

    if($data['widget_look'] == 'float' or $data['widget_look'] == 'flags' or $data['widget_look'] == 'float' or $data['widget_look'] == 'dropdown_with_flags' or $data['widget_look'] == 'flags_name' or $data['widget_look'] == 'flags_code' or $data['widget_look'] == 'popup')
        $allowed_languages = $data['fincl_langs'];
    elseif($data['widget_look'] == 'flags_dropdown')
        $allowed_languages = array_values(array_unique(array_merge($data['fincl_langs'], $data['incl_langs'])));
    else
        $allowed_languages = $data['incl_langs'];
    $allowed_languages = implode('|', $allowed_languages); // ex: en|ru|it|de
    if(preg_match('/^\/('.$allowed_languages.')\/(.*)/', $request_uri, $matches)) {
        $_GET['glang'] = $matches[1];
        $_GET['gurl'] = rawurldecode($matches[2]);

        require_once dirname(__FILE__) . '/url_addon/gtranslate.php';
        exit;
    } // #3 proxy translation
}

if(!empty($data['show_in_menu'])) {
    function gtranslate_menu_item($items, $args) {
        $data = get_option('GTranslate');
        GTranslate::load_defaults($data);

        if($args->theme_location == $data['show_in_menu']) {
            if($data['widget_look'] == 'dropdown_with_flags' or $data['widget_look'] == 'float') {
                $unique_wrapper_id = wp_rand(10000, 88888);
                $items .= '<li style="position:relative;" class="menu-item menu-item-gtranslate">';
                $items .= '<div style="position:absolute;white-space:nowrap;" id="gtranslate_menu_wrapper_' . $unique_wrapper_id . '">';
                $items .= GTranslate::get_widget_code(array('wrapper_selector' => '#gtranslate_menu_wrapper_' . $unique_wrapper_id, 'position' => 'inline'));
                $items .= '</div>';
                $items .= '</li>';
            } elseif($data['widget_look'] == 'flags' or $data['widget_look'] == 'flags_code' or $data['widget_look'] == 'flags_name' or $data['widget_look'] == 'lang_codes' or $data['widget_look'] == 'lang_names') {
                $lang_array = $data['native_language_names'] ? json_decode(GTranslate::$lang_array_native_json, true) : GTranslate::$lang_array;

                $items .= '<li class="menu-item menu-item-gtranslate menu-item-has-children notranslate">';
                $items .= GTranslate::render_single_item(array('lang' => $data['default_language'], 'widget_look' => $data['widget_look'], 'label' => $lang_array[$data['default_language']], 'current_wrapper' => 1));

                if($data['widget_look'] == 'lang_names' or $data['widget_look'] == 'lang_codes')
                    $languages = $data['incl_langs'];
                else
                    $languages = $data['fincl_langs'];

                $items .= '<ul class="dropdown-menu sub-menu">';
                foreach($languages as $lang) {
                    $items .= '<li class="menu-item menu-item-gtranslate-child">';
                    $items .= GTranslate::render_single_item(array('lang' => $lang, 'widget_look' => $data['widget_look'], 'label' => $lang_array[$lang]));
                    $items .= '</li>';
                }
                $items .= '</ul>';

                $items .= '</li>';
            } else {
                $unique_menu_class = 'gt-menu-' . wp_rand(10000, 88888);
                $items .= '<li style="position:relative;" class="menu-item menu-item-gtranslate ' . $unique_menu_class . '">';
                $items .= GTranslate::get_widget_code(array('wrapper_selector' => 'li.menu-item-gtranslate.' . $unique_menu_class, 'position' => 'inline'));
                $items .= '</li>';
            }
        }

        return $items;
    }

    add_filter('wp_nav_menu_items', 'gtranslate_menu_item', 10, 2);
}

if($data['floating_language_selector'] != 'no' and !is_admin()) {
    function gtranslate_display_floating() {
        echo GTranslate::get_widget_code(false);
    }

    add_action('wp_footer', 'gtranslate_display_floating');
}

if($data['wrapper_selector'] != '.gtranslate_wrapper' and !empty(trim($data['wrapper_selector']))) {
    function gtranslate_add_inline_selector() {
        echo GTranslate::get_widget_code(array('position' => 'inline'));
    }

    add_action('wp_footer', 'gtranslate_add_inline_selector');
}

if($data['url_translation'] and ($data['pro_version'] or $data['enterprise_version'])) {
    function gtranslate_url_translation_meta() {
        echo '<meta name="uri-translation" content="on" />';
    }

    add_action('wp_head', 'gtranslate_url_translation_meta', 1);
}

if($data['add_hreflang_tags'] and ($data['pro_version'] or $data['enterprise_version'])) {
    function gtranslate_add_hreflang_tags() {
        $data = get_option('GTranslate');
        GTranslate::load_defaults($data);

        $enabled_languages = array();
        if($data['widget_look'] == 'flags' or $data['widget_look'] == 'float' or $data['widget_look'] == 'dropdown_with_flags' or $data['widget_look'] == 'flags_name' or $data['widget_look'] == 'flags_code' or $data['widget_look'] == 'popup')
            $enabled_languages = $data['fincl_langs'];
        elseif($data['widget_look'] == 'flags_dropdown')
            $enabled_languages = array_values(array_unique(array_merge($data['fincl_langs'], $data['incl_langs'])));
        else
            $enabled_languages = $data['incl_langs'];

        //$current_url = wp_get_canonical_url();
        $current_url = network_home_url(add_query_arg(null, null));

        if($current_url !== false) {
            // adding default language
            if($data['default_language'] === 'iw')
                echo '<link rel="alternate" hreflang="he" href="'.esc_url($current_url).'" />'."\n";
            elseif($data['default_language'] === 'jw')
                echo '<link rel="alternate" hreflang="jv" href="'.esc_url($current_url).'" />'."\n";
            else
                echo '<link rel="alternate" hreflang="'.$data['default_language'].'" href="'.esc_url($current_url).'" />'."\n";

            // adding enabled languages
            foreach($enabled_languages as $lang) {
                $href = '';
                $domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);

                if($data['enterprise_version']) {
                    if($data['custom_domains'] and !empty($data['custom_domains_data'])) {
                        $custom_domains_data = json_decode(stripslashes($data['custom_domains_data']), true);
                        if(isset($custom_domains_data[$lang]))
                            $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $custom_domains_data[$lang], $current_url);
                        else
                            $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $lang . '.' . $domain, $current_url);
                    } else
                        $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $lang . '.' . $domain, $current_url);
                } elseif($data['pro_version'])
                    $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $_SERVER['HTTP_HOST'] . '/' . $lang, $current_url);

                if(!empty($href) and $lang != $data['default_language']) {
                    if($lang === 'iw')
                        echo '<link rel="alternate" hreflang="he" href="'.esc_url($href).'" />'."\n";
                    elseif($lang === 'jw')
                        echo '<link rel="alternate" hreflang="jv" href="'.esc_url($href).'" />'."\n";
                    else
                        echo '<link rel="alternate" hreflang="'.$lang.'" href="'.esc_url($href).'" />'."\n";
                }
            }
        }
    }

    add_action('wp_head', 'gtranslate_add_hreflang_tags', 1);
}

// translate WP REST API posts and categories data in JSON response
if($data['pro_version'] or $data['enterprise_version']) {
    function gtranslate_rest_post($response, $post, $request) {
        if(isset($response->data['content']) and is_array($response->data['content']))
            $response->data['content']['gt_translate_keys'] = array(array('key' => 'rendered', 'format' => 'html'));

        if(isset($response->data['excerpt']) and is_array($response->data['excerpt']))
            $response->data['excerpt']['gt_translate_keys'] = array(array('key' => 'rendered', 'format' => 'html'));

        if(isset($response->data['title']) and is_array($response->data['title']))
            $response->data['title']['gt_translate_keys'] = array(array('key' => 'rendered', 'format' => 'text'));

        if(isset($response->data['link']))
            $response->data['gt_translate_keys'] = array(array('key' => 'link', 'format' => 'url'));

        // more fields can be added here

        return $response;
    }

    function gtranslate_rest_category($response, $category, $request) {
        if(isset($response->data['description']))
            $response->data['gt_translate_keys'][] = array('key' => 'description', 'format' => 'html');

        if(isset($response->data['name']))
            $response->data['gt_translate_keys'][] = array('key' => 'name', 'format' => 'text');

        if(isset($response->data['link']))
            $response->data['gt_translate_keys'][] = array('key' => 'link', 'format' => 'url');

        // more fields can be added here

        return $response;
    }

    add_filter('rest_prepare_post', 'gtranslate_rest_post', 10, 3);
    add_filter('rest_prepare_category', 'gtranslate_rest_category', 10, 3);
}

// auto redirect to browser language
if(($data['pro_version'] or $data['enterprise_version']) and $data['detect_browser_language'] and parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == parse_url(site_url(), PHP_URL_PATH) . '/' and isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) and isset($_SERVER['HTTP_USER_AGENT']) and !isset($_SERVER['HTTP_X_GT_LANG']) and preg_match('/bot|spider|slurp|facebook/i', $_SERVER['HTTP_USER_AGENT']) == 0) {
    if($data['widget_look'] == 'flags' or $data['widget_look'] == 'float' or $data['widget_look'] == 'dropdown_with_flags' or $data['widget_look'] == 'flags_name' or $data['widget_look'] == 'flags_code' or $data['widget_look'] == 'popup')
        $allowed_languages = $data['fincl_langs'];
    elseif($data['widget_look'] == 'flags_dropdown')
        $allowed_languages = array_values(array_unique(array_merge($data['fincl_langs'], $data['incl_langs'])));
    else
        $allowed_languages = $data['incl_langs'];

    $accept_language = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));

    // for debug purposes only
    if(isset($_GET['gt_auto_switch_to']))
        $accept_language = $_GET['gt_auto_switch_to'];

    if($accept_language == 'zh')
        $accept_language = 'zh-CN';
    elseif($accept_language == 'he')
        $accept_language = 'iw';

    if($accept_language != $data['default_language'] and in_array($accept_language, $allowed_languages) and !isset($_COOKIE['gt_auto_switch'])) {
        // set cookie for 30 days and redirect
        setcookie('gt_auto_switch', 1, time() + 2592000);

        if($data['pro_version'])
            header('Location: ' . home_url() . '/' . $accept_language . '/');

        if($data['enterprise_version'] and isset($_SERVER['HTTP_HOST'])) {
            if($data['custom_domains'] and !empty($data['custom_domains_data'])) {
                $custom_domains_data = json_decode(stripslashes($data['custom_domains_data']), true);
                if(isset($custom_domains_data[$accept_language]))
                    $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $custom_domains_data[$accept_language], site_url());
                else
                    $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $accept_language . '.' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']), site_url());
            } else
                $href = str_ireplace('://' . $_SERVER['HTTP_HOST'], '://' . $accept_language . '.' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']), site_url());
            header('Location: ' . $href);
        }

        header('Vary: Accept-Language');

        exit;
    }
}

if($data['pro_version'] or $data['enterprise_version']) {
    // filter for woocommerce script params
    function gt_filter_woocommerce_scripts_data($data, $handle) {
        switch($handle) {
            case 'wc-address-i18n': {
                $data['gt_translate_keys'] = array(
                    array('key' => 'locale', 'format' => 'json'),
                    array('key' => 'i18n_required_text', 'format' => 'text'),
                    array('key' => 'i18n_optional_text', 'format' => 'html'),
                );

                $locale = json_decode($data['locale']);

                if(isset($locale->default->address_1))
                    $locale->default->address_1->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->address_2))
                    $locale->default->address_2->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->city))
                    $locale->default->city->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->postcode))
                    $locale->default->postcode->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->state))
                    $locale->default->state->gt_translate_keys = array('label', 'placeholder');

                if(isset($locale->default->shipping->address_1))
                    $locale->default->shipping->address_1->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->shipping->address_2))
                    $locale->default->shipping->address_2->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->shipping->city))
                    $locale->default->shipping->city->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->shipping->postcode))
                    $locale->default->shipping->postcode->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->shipping->state))
                    $locale->default->shipping->state->gt_translate_keys = array('label', 'placeholder');

                if(isset($locale->default->billing->address_1))
                    $locale->default->billing->address_1->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->billing->address_2))
                    $locale->default->billing->address_2->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->billing->city))
                    $locale->default->billing->city->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->billing->postcode))
                    $locale->default->billing->postcode->gt_translate_keys = array('label', 'placeholder');
                if(isset($locale->default->billing->state))
                    $locale->default->billing->state->gt_translate_keys = array('label', 'placeholder');

                $data['locale'] = json_encode($locale);
            } break;

            case 'wc-single-product': {
                $data['gt_translate_keys'] = array('i18n_required_rating_text');
            } break;

            case 'wc-checkout': {
                $data['gt_translate_keys'] = array('i18n_checkout_error');
            } break;

            case 'wc-country-select': {
                $data['gt_translate_keys'] = array('i18n_ajax_error', 'i18n_input_too_long_1', 'i18n_input_too_long_n', 'i18n_input_too_short_1', 'i18n_input_too_short_n', 'i18n_load_more', 'i18n_no_matches', 'i18n_searching', 'i18n_select_state_text', 'i18n_selection_too_long_1', 'i18n_selection_too_long_n');
            } break;

            case 'wc-add-to-cart': {
                $data['gt_translate_keys'] = array('i18n_view_cart', array('key' => 'cart_url', 'format' => 'url'));
            } break;

            case 'wc-add-to-cart-variation': {
                $data['gt_translate_keys'] = array('i18n_no_matching_variations_text', 'i18n_make_a_selection_text', 'i18n_unavailable_text');
            } break;

            case 'wc-password-strength-meter': {
                $data['gt_translate_keys'] = array('i18n_password_error', 'i18n_password_hint', '');
            } break;

            default: break;
        }

        return $data;
    }

    function gt_woocommerce_geolocate_ip($false) {
        if(isset($_SERVER['HTTP_X_GT_VIEWER_IP']))
            $_SERVER['HTTP_X_REAL_IP'] = $_SERVER['HTTP_X_GT_VIEWER_IP'];
        elseif(isset($_SERVER['HTTP_X_GT_CLIENTIP']))
            $_SERVER['HTTP_X_REAL_IP'] = $_SERVER['HTTP_X_GT_CLIENTIP'];

        if(isset($_SERVER['HTTP_X_REAL_IP']))
            $_SERVER['HTTP_X_REAL_IP'] = trim(current(explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP'])))));

        if(isset($_SERVER['HTTP_X_GT_CLIENTIP'], $_SERVER['HTTP_CF_IPCOUNTRY']))
            unset($_SERVER['HTTP_CF_IPCOUNTRY']);

        return $false;
    }

    add_filter('woocommerce_get_script_data', 'gt_filter_woocommerce_scripts_data', 10, 2 );

    add_filter('woocommerce_geolocate_ip', 'gt_woocommerce_geolocate_ip', 10, 4);

    // translate emails
    if($data['email_translation']) {
        function gt_translate_emails($args) {
            if(!is_array($args) or !isset($args['subject']) or !isset($args['message']))
                return $args;

            $subject = $args['subject'];
            $message = $args['message'];

            if(function_exists('curl_init') and isset($_SERVER['HTTP_X_GT_LANG'])) {
                //file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', date('Y-m-d H:i:s') . " - <subject>$subject</subject><message>$message</message>\n", FILE_APPEND);

                // translate woocommerce
                if(strpos($message, 'woocommerce') !== false) {
                    $data = get_option('GTranslate');
                    GTranslate::load_defaults($data);

                    include dirname(__FILE__) . '/url_addon/config.php';
                    $server_id = intval(substr(md5(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])), 0, 5), 16) % count($servers);
                    $server = $servers[$server_id];
                    $host = $_SERVER['HTTP_X_GT_LANG'] . '.' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
                    if($data['custom_domains'] and !empty($data['custom_domains_data'])) {
                        $custom_domains_data = json_decode(stripslashes($data['custom_domains_data']), true);
                        if(isset($custom_domains_data[$_SERVER['HTTP_X_GT_LANG']]))
                            $host = $custom_domains_data[$_SERVER['HTTP_X_GT_LANG']];
                    }
                    $protocol = ((isset($_SERVER['HTTPS']) and ($_SERVER['HTTPS'] == 'on' or $_SERVER['HTTPS'] == 1)) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';

                    $headers = array();
                    $headers[] = 'Host: ' . $host;
                    // add real visitor IP header
                    if(isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP']))
                        $viewer_ip_address = $_SERVER['HTTP_CLIENT_IP'];
                    if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) and !empty($_SERVER['HTTP_CF_CONNECTING_IP']))
                        $viewer_ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];
                    if(isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) and !empty($_SERVER['HTTP_X_SUCURI_CLIENTIP']))
                        $viewer_ip_address = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
                    if(!isset($viewer_ip_address))
                        $viewer_ip_address = $_SERVER['REMOTE_ADDR'];

                    $headers[] = 'X-GT-Viewer-IP: ' . $viewer_ip_address;
                    $headers[] = 'User-Agent: GTranslate-Email-Translate';

                    // add X-Forwarded-For
                    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                        $headers[] = 'X-GT-Forwarded-For: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $protocol.'://'.$server.'.tdn.gtranslate.net'.wp_make_link_relative(plugins_url('gtranslate/url_addon/gtranslate-email.php').'?glang='.$_SERVER['HTTP_X_GT_LANG']));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    if(defined('CURL_IPRESOLVE_V4')) curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/url_addon/cacert.pem');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, array('body' => base64_encode(do_shortcode("<subject>$subject</subject><message>$message</message>")), 'access_key' => md5(substr(NONCE_SALT, 0, 10) . substr(NONCE_KEY, 0, 5))));

                    if($data['email_translation_debug']) {
                        $fh = fopen(dirname(__FILE__) . '/url_addon/debug.txt', 'a');
                        curl_setopt($ch, CURLOPT_VERBOSE, true);
                        curl_setopt($ch, CURLOPT_STDERR, $fh);
                    }

                    $response = curl_exec($ch);
                    $response_info = curl_getinfo($ch);
                    curl_close($ch);

                    if($data['email_translation_debug']) {
                        file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Response: ' . $response . "\n", FILE_APPEND);
                        file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Response_info: ' . print_r($response_info, true) . "\n", FILE_APPEND);
                    }

                    if(isset($response_info['http_code']) and $response_info['http_code'] == 200) {
                        $response = json_decode($response, true);
                        if(empty($response))
                            return $args;

                        $response = base64_decode($response['email-body']);
                        if($response === false)
                            return $args;

                        if($data['pro_version'])
                            $response = str_ireplace($host, $_SERVER['HTTP_HOST'] . '/' . $_SERVER['HTTP_X_GT_LANG'], $response);

                        preg_match_all('/<subject>(.*?)<\/subject><message>(.*?)<\/message>/s', $response, $matches);
                        //file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Matches: ' . print_r($matches, true) . "\n", FILE_APPEND);

                        if(isset($matches[1][0], $matches[2][0])) {
                            $subject = $matches[1][0];
                            $message = $matches[2][0];

                            if($data['email_translation_debug']) {
                                file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Translated Subject: ' . $subject . "\n", FILE_APPEND);
                                file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Translated Message: ' . $message . "\n", FILE_APPEND);
                            }

                            $args['subject'] = $subject;
                            $args['message'] = $message;
                        }
                    }
                }
            }

            return $args;
        }

        // woocommerce pdf invoice translation
        function gt_translate_invoice_pdf($html) {
            if(function_exists('curl_init') and isset($_SERVER['HTTP_X_GT_LANG'])) {
                $data = get_option('GTranslate');
                GTranslate::load_defaults($data);

                // add notranslate for addresses
                $html = str_replace('-address"', '-address notranslate"', $html);

                include dirname(__FILE__) . '/url_addon/config.php';
                $server_id = intval(substr(md5(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])), 0, 5), 16) % count($servers);
                $server = $servers[$server_id];
                $host = $_SERVER['HTTP_X_GT_LANG'] . '.' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
                if($data['custom_domains'] and !empty($data['custom_domains_data'])) {
                    $custom_domains_data = json_decode(stripslashes($data['custom_domains_data']), true);
                    if(isset($custom_domains_data[$_SERVER['HTTP_X_GT_LANG']]))
                        $host = $custom_domains_data[$_SERVER['HTTP_X_GT_LANG']];
                }
                $protocol = ((isset($_SERVER['HTTPS']) and ($_SERVER['HTTPS'] == 'on' or $_SERVER['HTTPS'] == 1)) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';

                $headers = array();
                $headers[] = 'Host: ' . $host;
                // add real visitor IP header
                if(isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP']))
                    $viewer_ip_address = $_SERVER['HTTP_CLIENT_IP'];
                if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) and !empty($_SERVER['HTTP_CF_CONNECTING_IP']))
                    $viewer_ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];
                if(isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) and !empty($_SERVER['HTTP_X_SUCURI_CLIENTIP']))
                    $viewer_ip_address = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
                if(!isset($viewer_ip_address))
                    $viewer_ip_address = $_SERVER['REMOTE_ADDR'];

                $headers[] = 'X-GT-Viewer-IP: ' . $viewer_ip_address;
                $headers[] = 'User-Agent: GTranslate-Email-Translate';

                // add X-Forwarded-For
                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                    $headers[] = 'X-GT-Forwarded-For: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $protocol.'://'.$server.'.tdn.gtranslate.net'.wp_make_link_relative(plugins_url('gtranslate/url_addon/gtranslate-email.php').'?format=pdf_html&glang='.$_SERVER['HTTP_X_GT_LANG']));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                if(defined('CURL_IPRESOLVE_V4')) curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/url_addon/cacert.pem');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('body' => base64_encode(do_shortcode("<subject>PDF Invoice</subject><message>$html</message>")), 'access_key' => md5(substr(NONCE_SALT, 0, 10) . substr(NONCE_KEY, 0, 5))));

                if($data['email_translation_debug']) {
                    $fh = fopen(dirname(__FILE__) . '/url_addon/debug.txt', 'a');
                    curl_setopt($ch, CURLOPT_VERBOSE, true);
                    curl_setopt($ch, CURLOPT_STDERR, $fh);
                }

                $response = curl_exec($ch);
                $response_info = curl_getinfo($ch);
                curl_close($ch);

                if($data['email_translation_debug']) {
                    file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Response PDF: ' . $response . "\n", FILE_APPEND);
                    file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Response_info PDF: ' . print_r($response_info, true) . "\n", FILE_APPEND);
                }

                if(isset($response_info['http_code']) and $response_info['http_code'] == 200) {
                    $response = json_decode($response, true);
                    if(empty($response))
                        return $html;

                    $response = base64_decode($response['email-body']);
                    if($response === false)
                        return $html;

                    if($data['pro_version'])
                        $response = str_ireplace($host, $_SERVER['HTTP_HOST'] . '/' . $_SERVER['HTTP_X_GT_LANG'], $response);

                    preg_match_all('/<subject>(.*?)<\/subject><message>(.*?)<\/message>/s', $response, $matches);
                    //file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Matches: ' . print_r($matches, true) . "\n", FILE_APPEND);

                    if(isset($matches[1][0], $matches[2][0])) {
                        $html = $matches[2][0];

                        // fix image
                        $html = str_replace(' src="https://' . $_SERVER['HTTP_HOST'], ' src="', $html);

                        if($data['email_translation_debug']) {
                            file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', 'Translated PDF HTML: ' . $html . "\n", FILE_APPEND);
                        }
                    }
                }
            }

            return $html;
        }

        function gt_translate_wp_smtp_email($args) {
            //file_put_contents(dirname(__FILE__) . '/url_addon/debug.txt', date('Y-m-d H:i:s') . " - gt_translate_wp_smtp_email args: " . print_r($args, true) . "\n", FILE_APPEND);

            if(!is_object($args) or !isset($args->Subject) or !isset($args->Body))
                return;

            $result = gt_translate_emails(array('subject' => $args->Subject, 'message' => $args->Body));
            $args->Subject = $result['subject'];
            $args->Body = $result['message'];
        }

        add_filter('wpo_wcpdf_get_html', 'gt_translate_invoice_pdf', 10000, 1);
        add_filter('wp_mail', 'gt_translate_emails', 10000, 1);
        add_filter('wp_mail_smtp_mailcatcher_smtp_pre_send_before', 'gt_translate_wp_smtp_email', 10000, 1);
    }
}

if($data['enterprise_version']) {
    // solve wp_get_referer issue
    function gt_allowed_redirect_hosts($hosts) {
        $data = get_option('GTranslate');
        GTranslate::load_defaults($data);

        if($data['custom_domains'] and !empty($data['custom_domains_data'])) {
            $custom_domains_data = json_decode(stripslashes($data['custom_domains_data']), true);
            $gt_hosts = array_values($custom_domains_data);
        } else {
            $gt_hosts = array();
        }

        if(isset($_SERVER['HTTP_X_GT_LANG']))
            $gt_hosts[] = $_SERVER['HTTP_X_GT_LANG'] . '.' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);

        return array_merge($hosts, $gt_hosts);
    }

    add_filter('allowed_redirect_hosts', 'gt_allowed_redirect_hosts');
}

// exclude Autoptimize minification
function ao_cache_exclude_js_gtranslate($exclude_js) {
    if(is_string($exclude_js))
        $exclude_js .= ', gtranslate';

    return $exclude_js;
}
add_filter('autoptimize_filter_js_exclude', 'ao_cache_exclude_js_gtranslate', 10, 1);

// exclude javascript minification by cache plugins
function cache_exclude_js_gtranslate($excluded_js) {
    if(is_array($excluded_js) or empty($excluded_js)) {
        $excluded_js[] = '/gtranslate/js/.+\.js';
        $excluded_js[] = 'cdn.gtranslate.net';
        $excluded_js[] = 'gtranslate';
    }

    return $excluded_js;
}

// LiteSpeed Cache
add_filter('litespeed_optimize_js_excludes', 'cache_exclude_js_gtranslate');
//todo: add_filter('litespeed_optm_js_defer_exc', '');

// WP Rocket
add_filter('rocket_exclude_js', 'cache_exclude_js_gtranslate');
add_filter('rocket_minify_excluded_external_js', 'cache_exclude_js_gtranslate');

// WP Rocket inline script exclusions
function rocket_exclude_inline_gt_scripts($excluded_patterns) {
    if(is_array($excluded_patterns)) {
        $excluded_patterns[] = 'gtranslate';
        return $excluded_patterns;
    }

    return array('gtranslate');
}
add_filter('rocket_defer_inline_exclusions', 'rocket_exclude_inline_gt_scripts', 1000, 1);

// W3 Total Cache
function w3tc_cache_exclude_js_gtranslate($do_tag_minification, $script_tag, $file) {
    if(strpos($file, 'gtranslate') !== false)
        return false;

    return $do_tag_minification;
}
add_filter('w3tc_minify_js_do_tag_minification', 'w3tc_cache_exclude_js_gtranslate', 10, 3);

// WP Optimize
function wpo_cache_exclude_js_gtranslate($excluded_js) {
    if(is_array($excluded_js) or empty($excluded_js))
        $excluded_js[] = 'gtranslate';

    return $excluded_js;
}
add_filter('wp-optimize-minify-default-exclusions', 'wpo_cache_exclude_js_gtranslate', 10, 1);

// Siteground SG Optimize
function sg_cache_exclude_js_gtranslate($excluded_js) {
    if(!is_array($excluded_js))
        return $excluded_js;

    global $wp_scripts;
    $registered_handles = array_keys($wp_scripts->registered);
    foreach($registered_handles as $handle) {
        if(strpos($handle, 'gt_widget_script') !== false)
            $excluded_js[] = $handle;
    }

    return $excluded_js;
}
add_filter('sgo_js_minify_exclude', 'sg_cache_exclude_js_gtranslate', 10, 1);
add_filter('sgo_javascript_combine_exclude', 'sg_cache_exclude_js_gtranslate', 10, 1);
add_filter('sgo_javascript_combine_excluded_external_paths', 'cache_exclude_js_gtranslate', 10, 1);