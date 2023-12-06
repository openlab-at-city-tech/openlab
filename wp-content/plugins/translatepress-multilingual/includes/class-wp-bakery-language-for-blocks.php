<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit();
}

class TRP_WPBakery {
    private static $_instance = null;

    public $shortcode_param_type_dropdown_multi = 'trp_dropdown_multi';
    public $param_name_show = 'trp_param_show';
    public $param_name_show_language = 'trp_param_show_language';
    public $param_name_exclude = 'trp_param_exclude';
    public $param_name_exclude_languages = 'trp_param_exclude_languages';

    /**
     * Register plugin action hooks and filters
     */
    public function __construct() {
        add_action('init', [$this, 'init'], PHP_INT_MAX /* We need to collect all available shortcodes */);
        add_filter(
            'do_shortcode_tag',
            [$this, 'do_shortcode_tag'],
            PHP_INT_MAX /* We should be the last one, so no other filter can add additional content */,
            3
        );
    }

    public function init() {
        if (class_exists('WPBMap')) {
            WPBMap::addAllMappedShortcodes();
        }
        $this->register_dropdown_multi();
        $this->register_params_show_for_shortcodes();
        $this->register_params_exclude_for_shortcodes();
    }

    /**
     * Modify the output of a given shortcode if TranslatePress is configured.
     */
    public function do_shortcode_tag($output, $tag, $attr) {
        return $this->is_hidden($attr) ? '' : $output;
    }

    /**
     * Visual Composer does not a multi-dropdown out-of-the-box. But we can add an alternative
     * `type` which supports the `<select multiple` usage.
     *
     * @see https://stackoverflow.com/a/48125515/5506547
     */
    private function register_dropdown_multi() {
        vc_add_shortcode_param($this->shortcode_param_type_dropdown_multi, function ($param, $value) {
            if (!is_array($value)) {
                $param_value_arr = explode(',', $value);
            } else {
                $param_value_arr = $value;
            }

            $param_line = '';
            $param_line .=
                '<select multiple name="' .
                esc_attr($param['param_name']) .
                '" class="wpb_vc_param_value wpb-input wpb-select ' .
                esc_attr($param['param_name']) .
                ' ' .
                esc_attr($param['type']) .
                '">';
            foreach ($param['value'] as $text_val => $val) {
                if (is_numeric($text_val) && (is_string($val) || is_numeric($val))) {
                    $text_val = $val;
                }
                $selected = '';
                if (!empty($param_value_arr) && in_array($val, $param_value_arr)) {
                    $selected = ' selected="selected"';
                }
                $param_line .=
                    '<option class="' . $val . '" value="' . $val . '"' . $selected . '>' . $text_val . '</option>';
            }
            $param_line .= '</select>';

            return $param_line;
        });
    }

    /**
     * We need to register the parameter attributes for all available shortcodes.
     *
     * @see https://kb.wpbakery.com/docs/inner-api/vc_add_param/
     */
    private function register_params_show_for_shortcodes() {
        global $shortcode_tags;
        $shortcode_bases = array_keys($shortcode_tags);
        $group = $this->get_group();

        $attributes_checkbox = [
            'type' => 'checkbox',
            'heading' => __('Restrict element to language', 'translatepress-multilingual'),
            'param_name' => $this->param_name_show,
            'group' => $group,
            'description' => __('Show this element only in one language.', 'translatepress-multilingual')
        ];

        $attributes_value = [
            'type' => 'dropdown',
            'heading' => __('Select language', 'translatepress-multilingual'),
            'param_name' => $this->param_name_show_language,
            'group' => $group,
            'value' => array_flip($this->get_published_languages(true)),
            'description' => __('Choose in which language to show this element.', 'translatepress-multilingual'),
            'dependency' => [
                'element' => $this->param_name_show,
                'value' => 'true'
            ]
        ];

        $skip_sc = apply_filters( 'trp_wpbakery_skip_shortcodes', $this->get_skip_sc_array());
        foreach ($shortcode_bases as $sh) {
            if ( !in_array( $sh, $skip_sc ) ) {
                vc_add_param( $sh, $attributes_checkbox );
                vc_add_param( $sh, $attributes_value );
            }
        }
    }

    /**
     * We need to register the parameter attributes for all available shortcodes.
     *
     * @see https://kb.wpbakery.com/docs/inner-api/vc_add_param/
     */
    private function register_params_exclude_for_shortcodes() {
        global $shortcode_tags;
        $shortcode_bases = array_keys($shortcode_tags);
        $group = $this->get_group();

        $attributes_checkbox = [
            'type' => 'checkbox',
            'heading' => __('Exclude from Language', 'translatepress-multilingual'),
            'param_name' => $this->param_name_exclude,
            'group' => $group,
            'description' => __('Exclude this element from specific languages.', 'translatepress-multilingual')
        ];

        $message =
            '<p>' .
            __(
                'This element will still be visible when you are translating your website through the Translation Editor.',
                'translatepress-multilingual'
            ) .
            '</p>';
        $message .=
            '<p>' .
            __('The content of this element should be written in the default language.', 'translatepress-multilingual') .
            '</p>';

        $attributes_value = [
            'type' => $this->shortcode_param_type_dropdown_multi,
            'heading' => __('Select languages', 'translatepress-multilingual'),
            'param_name' => $this->param_name_exclude_languages,
            'group' => $group,
            'value' => array_flip($this->get_published_languages(true)),
            'description' => __('Choose from which languages to exclude this element.', 'translatepress-multilingual') . $message,
            'dependency' => [
                'element' => $this->param_name_exclude,
                'value' => 'true'
            ]
        ];

        $skip_sc = apply_filters( 'trp_wpbakery_skip_shortcodes', $this->get_skip_sc_array());
        foreach ($shortcode_bases as $sh) {
            if ( !in_array( $sh, $skip_sc ) ) {
                vc_add_param( $sh, $attributes_checkbox );
                vc_add_param( $sh, $attributes_value );
            }
        }
    }

    private function get_group() {
        return __('TranslatePress', 'translatepress-multilingual');
    }

    private function get_published_languages($placeholder = false) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_languages = $trp->get_component('languages');
        $trp_settings = $trp->get_component('settings');
        $result = $trp_languages->get_language_names($trp_settings->get_settings()['publish-languages']);

        if ($placeholder) {
            $result = array_merge(['' => ''], $result);
        }
        return $result;
    }

    private function is_inline_editor() {
        return ( isset($_GET['vc_action']) && $_GET['vc_action'] === 'vc_inline' ) ||
            ( isset($_GET['vc_editable']) && $_GET['vc_editable'] === 'true' );
    }

    private function is_hidden($attr) {
        if (!is_array($attr) || $this->is_inline_editor()) {
            return false;
        }

        // Restrict to only one language
        if (isset($attr[$this->param_name_show], $attr[$this->param_name_show_language])) {
            $current_language = get_locale();

            if ($current_language !== $attr[$this->param_name_show_language]) {
                return true;
            }
        }

        // Exclude to multiple languages
        if (isset($attr[$this->param_name_exclude], $attr[$this->param_name_exclude_languages])) {
            $current_language = get_locale();
            $exclude = explode(',', $attr[$this->param_name_exclude_languages]);

            if (in_array($current_language, $exclude)) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return TRP_WPBakery An instance of the class.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     *
     * Shortcodes with missing 'params' trigger notice, so don't add TP settings to them
     *
     * Also, shortcodes with 'params' set to empty string instead of array trigger fatal error.
     *
     * @return array of shortcodes to skip
     */
    public function get_skip_sc_array(){
        $skip_sc = array();
        $sc = WPBMap::getAllShortCodes();

        if ( isset( $sc) && is_array($sc) ){
            foreach( $sc as $key => $value ){
                if ( isset($sc[$key] ) && (!isset($sc[$key]['params']) || !is_array($sc[$key]['params']) || $this->has_invalid_params($sc[$key]['params']))){
                    $skip_sc[] = $key;
                }
            }
        }
        return $skip_sc;
    }

    /**
     * Check if the parameters are valid (have numeric keys).
     * @param $arr array
     */
    public function has_invalid_params($arr){
        $bool=false;

        foreach (array_keys($arr) as $key){
            if(!is_numeric($key)){
                $bool = true;
                break;
            }
        }

        $invalid_params = apply_filters('trp_wp_bakery_invalid_params', $bool, $arr);
        return $invalid_params;
    }

}



// Instantiate Plugin Class
TRP_WPBakery::instance();

