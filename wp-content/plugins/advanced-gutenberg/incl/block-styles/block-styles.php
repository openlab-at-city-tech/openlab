<?php
use PublishPress\Blocks\Utilities;

defined('ABSPATH') || die;

/**
 * Block styles functionality
 *
 * @since 3.4.0
 */
class AdvancedGutenbergBlockStyles
{
    public $proActive;

    /**
     * Default custom styles
     *
     * @var array   Default custom styles for first install
     */
    public static $default_custom_styles = array(
        0 => array(
            'id' => 1,
            'title' => 'Blue message',
            'name' => 'blue-message',
            'identifyColor' => '#3399ff',
            'css' => array(
                'background-color' => '#3399ff',
                'color' => '#ffffff',
                'text-shadow' => 'none',
                'font-size' => '16px',
                'line-height' => '24px',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px'
            )
        ),
        1 => array(
            'id' => 2,
            'title' => 'Green message',
            'name' => 'green-message',
            'identifyColor' => '#8cc14c',
            'css' => array(
                'background-color' => '#8cc14c',
                'color' => '#ffffff',
                'text-shadow' => 'none',
                'font-size' => '16px',
                'line-height' => '24px',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px'
            )
        ),
        2 => array(
            'id' => 3,
            'title' => 'Orange message',
            'name' => 'orange-message',
            'identifyColor' => '#faa732',
            'css' => array(
                'background-color' => '#faa732',
                'color' => '#ffffff',
                'text-shadow' => 'none',
                'font-size' => '16px',
                'line-height' => '24px',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px'
            )
        ),
        3 => array(
            'id' => 4,
            'title' => 'Red message',
            'name' => 'red-message',
            'identifyColor' => '#da4d31',
            'css' => array(
                'background-color' => '#da4d31',
                'color' => '#ffffff',
                'text-shadow' => 'none',
                'font-size' => '16px',
                'line-height' => '24px',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px'
            )
        ),
        4 => array(
            'id' => 5,
            'title' => 'Grey message',
            'name' => 'grey-message',
            'identifyColor' => '#53555c',
            'css' => array(
                'background-color' => '#53555c',
                'color' => '#ffffff',
                'text-shadow' => 'none',
                'font-size' => '16px',
                'line-height' => '24px',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px'
            )
        ),
        5 => array(
            'id' => 6,
            'title' => 'Left block',
            'name' => 'left-block',
            'identifyColor' => '#ff00ff',
            'css' => array(
                'background' => 'radial-gradient(ellipse at center center, #ffffff 0%, #f2f2f2 100%)',
                'color' => '#8b8e97',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px',
                'margin' => '10px',
                'margin-top' => '10px',
                'margin-right' => '10px',
                'margin-bottom' => '10px',
                'margin-left' => '10px',
                'float' => 'left'
            )
        ),
        6 => array(
            'id' => 7,
            'title' => 'Right block',
            'name' => 'right-block',
            'identifyColor' => '#00ddff',
            'css' => array(
                'background' => 'radial-gradient(ellipse at center center, #ffffff 0%, #f2f2f2 100%)',
                'color' => '#8b8e97',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px',
                'margin' => '10px',
                'margin-top' => '10px',
                'margin-right' => '10px',
                'margin-bottom' => '10px',
                'margin-left' => '10px',
                'float' => 'right'
            )
        ),
        7 => array(
            'id' => 8,
            'title' => 'Blockquotes',
            'name' => 'blockquotes',
            'identifyColor' => '#cccccc',
            'css' => array(
                'background-color' => 'none',
                'border-left' => '5px solid #f1f1f1',
                'color' => '#8B8E97',
                'font-size' => '16px',
                'font-style' => 'italic',
                'line-height' => '22px',
                'padding-left' => '15px',
                'padding' => '10px',
                'padding-top' => '10px',
                'padding-right' => '10px',
                'padding-bottom' => '10px',
                'width' => '60%',
                'float' => 'left'
            )
        )
    );


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proActive = Utilities::isProActive();

        $this->initHooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function initHooks()
    {
        add_action('wp_ajax_advgb_custom_styles_ajax', array($this, 'customStylesAjax'));
        add_action('admin_notices', array($this, 'advgb_custom_styles_save_page'));
    }


    /**
     * Redirect after saving custom styles page data
     * Name is build in registerMainMenu() > $function_name
     *
     * @return boolean true on success, false on failure
     * @since 3.0.0
     */
    public function advgb_custom_styles_save_page()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        if (isset($_POST['save_custom_styles'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
            if (
                !wp_verify_nonce(
                    sanitize_key($_POST['advgb_cstyles_nonce_field']),
                    'advgb_cstyles_nonce'
                )
            ) {
                return;
            }
            echo '<div id="message" class="updated fade">';
            echo '<p>' . esc_html__('Your styles have been saved!', 'advanced-gutenberg') . '</p>';
            echo '</div>';
        }
    }

    /**
     * Ajax for custom styles
     *
     * @return boolean,void     Return false if failure, echo json on success
     */
    public function customStylesAjax()
    {
        // Check users permissions
        if (!current_user_can('activate_plugins')) {
            wp_send_json(__('No permission!', 'advanced-gutenberg'), 403);
            return false;
        }

        $regex = '/^[a-zA-Z0-9_\-]+$/';

        if (!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'advgb_cstyles_nonce')) {
            wp_send_json(__('Invalid nonce token!', 'advanced-gutenberg'), 400);
        }

        $check_exist = get_option('advgb_custom_styles');
        if ($check_exist === false) {
            update_option('advgb_custom_styles', $this::$default_custom_styles, false);
        }

        $custom_style_data = get_option('advgb_custom_styles');
        $task = isset($_POST['task']) ? sanitize_text_field($_POST['task']) : '';
        $active_tab = isset($_POST['active_tab']) ? sanitize_text_field($_POST['active_tab']) : 'style-editor';

        if ($task === '') {
            return false;
        } elseif ($task === 'new') {
            $new_style_id = end($custom_style_data);
            $new_style_id = $new_style_id['id'] + 1;
            $new_style_array = array(
                'id' => $new_style_id,
                'title' => __('Style title', 'advanced-gutenberg') . ' ' . $new_style_id,
                'name' => 'new-class-' . rand(0, 99) . $new_style_id . rand(0, 99),
                'css' => [],
                'identifyColor' => '#000000',
                'active_tab' => $active_tab
            );
            array_push($custom_style_data, $new_style_array);
            update_option('advgb_custom_styles', $custom_style_data, false);
            wp_send_json($new_style_array);
        } elseif ($task === 'delete') {
            $custom_style_data_delete = get_option('advgb_custom_styles');
            $style_id = (int) $_POST['id'];
            $new_style_deleted_array = array();
            $done = false;
            foreach ($custom_style_data_delete as $data) {
                if ($data['id'] === $style_id) {
                    $done = true;
                    continue;
                }
                array_push($new_style_deleted_array, $data);
            }
            update_option('advgb_custom_styles', $new_style_deleted_array, false);
            if ($done) {
                wp_send_json(array('id' => $style_id), 200);
            }
        } elseif ($task === 'copy') {
            $data_saved = get_option('advgb_custom_styles');
            $style_id = (int) $_POST['id'];
            $new_style_copied_array = get_option('advgb_custom_styles');
            $copied_styles = [];
            $new_id = end($new_style_copied_array);

            foreach ($data_saved as $data) {
                if ($data['id'] === $style_id) {
                    $copied_styles = array(
                        'id' => $new_id['id'] + 1,
                        'title' => sanitize_text_field($data['title']) . ' ' . __('copy', 'advanced-gutenberg'),
                        'name' => sanitize_text_field($data['name']) . '-' . rand(0, 999),
                        'css' => is_array($data['css']) ? $data['css'] : self::css_string_to_array($data['css']),
                        'identifyColor' => sanitize_hex_color($data['identifyColor']),
                        'active_tab' => isset($data['active_tab']) ? $data['active_tab'] : $active_tab
                    );
                    array_push($new_style_copied_array, $copied_styles);
                }
            }
            update_option('advgb_custom_styles', $new_style_copied_array, false);
            wp_send_json($copied_styles);

        } elseif ($task === 'preview') {
            $style_id = (int) $_POST['id'];
            $data_saved = get_option('advgb_custom_styles');

            foreach ($data_saved as $data) {
                if ($data['id'] === $style_id) {
                    $response = array(
                        'id' => $data['id'],
                        'title' => esc_html($data['title']),
                        'name' => esc_html($data['name']),
                        'css' => '',
                        'identifyColor' => esc_html($data['identifyColor']),
                        'active_tab' => isset($data['active_tab']) ? $data['active_tab'] : 'style-editor',
                        'generated_css' => ''
                    );

                    if (is_array($data['css'])) {
                        $response['css_array'] = $data['css'];
                        $response['css'] = AdvancedGutenbergBlockStyles::array_to_scss($data['css']);
                    } else {
                        $response['css'] = wp_specialchars_decode($data['css'], ENT_QUOTES);
                    }

                    if (isset($data['generated_css']) && !empty($data['generated_css'])) {
                        $response['generated_css'] = $data['generated_css'];
                    } else {
                        if (is_array($data['css'])) {
                            $response['generated_css'] = AdvancedGutenbergBlockStyles::generate_final_css($data['css'], $data['name']);
                        } else {
                            $response['generated_css'] = $data['css'];
                        }
                    }

                    wp_send_json($response);
                }
            }
            wp_send_json(false, 404);
        } elseif ($task === 'style_save') {
            $style_id = (int) $_POST['id'];
            $new_styletitle = sanitize_text_field($_POST['title']);
            $new_classname = sanitize_text_field($_POST['name']);
            $new_identify_color = sanitize_hex_color($_POST['mycolor']);
            $generated_css = isset($_POST['generated_css']) ? wp_strip_all_tags(wp_specialchars_decode($_POST['generated_css'], ENT_QUOTES)) : '';

            $new_css = array();

            if (isset($_POST['css_array']) && is_array($_POST['css_array']) && !empty($_POST['css_array'])) {
                $new_css = $this->sanitize_css_array($_POST['css_array']);
            } else {
                $new_css = wp_strip_all_tags(wp_specialchars_decode($_POST['mycss'], ENT_QUOTES));
            }

            // Validate new name
            if (!preg_match($regex, $new_classname)) {
                wp_send_json(
                    'Please use valid characters for a CSS classname! As example: hyphen or underscore instead of empty spaces.',
                    403
                );
                return false;
            }

            $data_saved = get_option('advgb_custom_styles');
            $new_data_array = array();
            foreach ($data_saved as $data) {
                if ($data['id'] === $style_id) {
                    $data['title'] = $new_styletitle;
                    $data['name'] = $new_classname;
                    $data['css'] = $new_css;
                    $data['identifyColor'] = $new_identify_color;
                    $data['active_tab'] = $active_tab;
                    $data['generated_css'] = $generated_css;
                }
                array_push($new_data_array, $data);
            }
            update_option('advgb_custom_styles', $new_data_array, false);
            wp_send_json(array('success' => true));
        } else {
            wp_send_json(null, 404);
        }
    }

    /**
     * Sanitize CSS array with nested structure
     */
    private function sanitize_css_array($css_data)
    {
        if (is_string($css_data)) {
            return wp_strip_all_tags(wp_specialchars_decode($css_data, ENT_QUOTES));
        }

        if (is_array($css_data) && !isset($css_data['base']) && !isset($css_data['nested']) && !isset($css_data['states'])) {
            $sanitized = array();
            foreach ($css_data as $property => $value) {
                $sanitized[sanitize_text_field($property)] = sanitize_text_field($value);
            }
            return $sanitized;
        }

        $sanitized = array();

        if (isset($css_data['base']) && is_array($css_data['base'])) {
            foreach ($css_data['base'] as $property => $value) {
                $sanitized['base'][sanitize_text_field($property)] = sanitize_text_field($value);
            }
        }

        if (isset($css_data['states']) && is_array($css_data['states'])) {
            foreach ($css_data['states'] as $state => $rules) {
                $sanitized_state = sanitize_text_field($state);
                foreach ($rules as $property => $value) {
                    $sanitized['states'][$sanitized_state][sanitize_text_field($property)] = sanitize_text_field($value);
                }
            }
        }

        if (isset($css_data['nested']) && is_array($css_data['nested'])) {
            foreach ($css_data['nested'] as $selector => $element_rules) {
                $sanitized_selector = sanitize_text_field($selector);

                // Handle regular nested properties
                foreach ($element_rules as $property => $value) {
                    if ($property !== 'states') {
                        $sanitized['nested'][$sanitized_selector][sanitize_text_field($property)] = sanitize_text_field($value);
                    }
                }

                // Handle nested states
                if (isset($element_rules['states']) && is_array($element_rules['states'])) {
                    foreach ($element_rules['states'] as $state => $state_rules) {
                        $sanitized_state = sanitize_text_field($state);
                        foreach ($state_rules as $property => $value) {
                            $sanitized['nested'][$sanitized_selector]['states'][$sanitized_state][sanitize_text_field($property)] = sanitize_text_field($value);
                        }
                    }
                }
            }
        }

        return $sanitized;
    }


    /**
     * Convert legacy flat array to new nested structure
     */
    public static function convert_legacy_array_to_nested($legacy_array)
    {
        $nested = array(
            'base' => array(),
            'nested' => array()
        );

        $valid_elements = array('p', 'span', 'blockquote', 'ul', 'li', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'img', 'video', 'div', 'section', 'button', 'input');
        $valid_states = array('hover', 'focus', 'active', 'visited');

        foreach ($legacy_array as $property => $value) {
            if (empty($value))
                continue;

            $parts = explode('-', $property);

            // Check if it's a nested element property
            if (count($parts) >= 2 && in_array($parts[0], $valid_elements)) {
                $element = $parts[0];
                $css_property = implode('-', array_slice($parts, 1));

                // Check for states in nested properties
                if (count($parts) >= 3 && in_array($parts[1], $valid_states)) {
                    // Element with state
                    $element = $parts[0];
                    $state = ':' . $parts[1];
                    $css_property = implode('-', array_slice($parts, 2));

                    if (!isset($nested['nested'][$element])) {
                        $nested['nested'][$element] = array('states' => array());
                    }
                    if (!isset($nested['nested'][$element]['states'][$state])) {
                        $nested['nested'][$element]['states'][$state] = array();
                    }
                    $nested['nested'][$element]['states'][$state][$css_property] = $value;
                } else {
                    // Regular nested element
                    if (!isset($nested['nested'][$element])) {
                        $nested['nested'][$element] = array();
                    }
                    $nested['nested'][$element][$css_property] = $value;
                }
            } else {
                // Base property
                $nested['base'][$property] = $value;
            }
        }

        // Clean up empty arrays
        if (empty($nested['base'])) {
            unset($nested['base']);
        }
        if (empty($nested['nested'])) {
            unset($nested['nested']);
        }

        return $nested;
    }

    /**
     * Parse CSS string to array structure (for legacy custom CSS)
     */
    public static function parse_css_string_to_array($css_string)
    {
        // Remove braces and trim
        $clean_css = trim(str_replace(array('{', '}'), '', $css_string));
        if (empty($clean_css))
            return array();

        $declarations = explode(';', $clean_css);
        $base_styles = array();

        foreach ($declarations as $declaration) {
            $declaration = trim($declaration);
            if (empty($declaration))
                continue;

            $parts = explode(':', $declaration, 2);
            if (count($parts) === 2) {
                $property = trim($parts[0]);
                $value = trim($parts[1]);
                $base_styles[$property] = $value;
            }
        }

        return array('base' => $base_styles);
    }

    /**
     * Convert CSS array to CSS string
     */
    public static function css_array_to_string($css_array)
    {
        if (!is_array($css_array)) {
            return $css_array;
        }

        $css = '';
        foreach ($css_array as $property => $value) {
            // Handle special cases
            if ($property === 'background' && strpos($value, 'gradient') !== false) {
                $css .= "    background: {$value};\n";
            } else {
                $css .= "    {$property}: {$value};\n";
            }
        }

        return trim($css);
    }

    /**
     * Convert CSS string to CSS array
     */
    public static function css_string_to_array($css_string)
    {
        if (is_array($css_string)) {
            return $css_string;
        }

        $css_array = array();
        $declarations = explode(';', trim($css_string));

        foreach ($declarations as $declaration) {
            $declaration = trim($declaration);
            if (empty($declaration))
                continue;

            $parts = explode(':', $declaration, 2);
            if (count($parts) < 2)
                continue;

            $property = trim($parts[0]);
            $value = trim($parts[1]);

            // Handle shorthand properties
            if ($property === 'padding' || $property === 'margin') {
                $values = preg_split('/\s+/', $value);
                $css_array[$property . '-top'] = $values[0];
                $css_array[$property . '-right'] = isset($values[1]) ? $values[1] : $values[0];
                $css_array[$property . '-bottom'] = isset($values[2]) ? $values[2] : $values[0];
                $css_array[$property . '-left'] = isset($values[3]) ? $values[3] :
                    (isset($values[1]) ? $values[1] : $values[0]);
            }

            $css_array[$property] = $value;
        }

        return $css_array;
    }

    /**
     * Convert SCSS-like syntax to structured CSS array
     */
    public static function scss_to_array($scss, $base_class = '')
    {
        $result = [];
        $lines = explode("\n", $scss);
        $current_selector = 'base';
        $current_state = 'normal';
        $in_nested_block = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || $line === '{' || $line === '}') {
                continue;
            }

            // Check for nested selector with states
            if (strpos($line, '&') === 0) {
                $selector = trim($line, '&: ;{');

                // Check if it's a state (hover, focus, etc.)
                if (strpos($selector, ':') === 0) {
                    $current_state = $selector;
                    $current_selector = 'base'; // State applies to base
                } else {
                    $current_selector = $selector;
                    $current_state = 'normal';
                }

                $in_nested_block = true;
                continue;
            }

            // Parse CSS declarations with state context
            if (strpos($line, ':') !== false) {
                list($property, $value) = explode(':', $line, 2);
                $property = trim($property);
                $value = trim(rtrim($value, ';'));

                if ($current_selector === 'base' && $current_state === 'normal') {
                    // Base styles
                    $result[$property] = $value;
                } elseif ($current_selector === 'base' && $current_state !== 'normal') {
                    // State styles for base element
                    if (!isset($result['states'][$current_state])) {
                        $result['states'][$current_state] = [];
                    }
                    $result['states'][$current_state][$property] = $value;
                } elseif ($current_selector !== 'base' && $current_state === 'normal') {
                    // Nested element styles
                    if (!isset($result['nested'][$current_selector])) {
                        $result['nested'][$current_selector] = [];
                    }
                    $result['nested'][$current_selector][$property] = $value;
                } else {
                    // Nested element with state
                    if (!isset($result['nested'][$current_selector]['states'][$current_state])) {
                        $result['nested'][$current_selector]['states'][$current_state] = [];
                    }
                    $result['nested'][$current_selector]['states'][$current_state][$property] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Convert structured CSS array back to SCSS syntax
     */
    public static function array_to_scss($css_array)
    {
        // Handle legacy flat arrays by converting them first
        if (is_array($css_array) && !isset($css_array['base']) && !isset($css_array['nested']) && !isset($css_array['states'])) {
            $css_array = self::convert_legacy_array_to_nested($css_array);
        }

        $scss = "";

        // Base styles
        if (isset($css_array['base']) && is_array($css_array['base'])) {
            foreach ($css_array['base'] as $property => $value) {
                if (!empty($value)) {
                    $scss .= "    {$property}: {$value};\n";
                }
            }
        }

        // Base states
        if (isset($css_array['states']) && is_array($css_array['states'])) {
            foreach ($css_array['states'] as $state => $rules) {
                if (!empty($rules)) {
                    $scss .= "\n    &{$state} {\n";
                    foreach ($rules as $property => $value) {
                        if (!empty($value)) {
                            $scss .= "        {$property}: {$value};\n";
                        }
                    }
                    $scss .= "    }\n";
                }
            }
        }

        // Nested elements
        if (isset($css_array['nested']) && is_array($css_array['nested'])) {
            foreach ($css_array['nested'] as $selector => $element_rules) {
                $has_regular_rules = false;
                $has_state_rules = isset($element_rules['states']) && !empty($element_rules['states']);
                $regular_rules = '';

                // Collect regular rules
                foreach ($element_rules as $property => $value) {
                    if ($property !== 'states' && !empty($value)) {
                        $has_regular_rules = true;
                        $regular_rules .= "        {$property}: {$value};\n";
                    }
                }

                if ($has_regular_rules || $has_state_rules) {
                    $scss .= "\n    &{$selector} {\n";

                    if ($has_regular_rules) {
                        $scss .= $regular_rules;
                    }

                    if ($has_state_rules) {
                        foreach ($element_rules['states'] as $state => $state_rules) {
                            if (!empty($state_rules)) {
                                $scss .= "\n        &{$state} {\n";
                                foreach ($state_rules as $property => $value) {
                                    if (!empty($value)) {
                                        $scss .= "            {$property}: {$value};\n";
                                    }
                                }
                                $scss .= "        }\n";
                            }
                        }
                    }

                    $scss .= "    }\n";
                }
            }
        }

        return $scss;
    }

    /**
     * Generate final CSS from structured array for frontend
     */
    public static function generate_final_css($css_data, $class_name)
    {
        // make sure to trim off starting . in class name if it starts with it
        $class_name = ltrim($class_name, '.');
        // Handle string format (legacy custom CSS)
        if (is_string($css_data)) {
            return ".{$class_name} {\n" . $css_data . "\n}";
        }

        // Handle flat array format (legacy UI data)
        if (is_array($css_data) && !isset($css_data['base']) && !isset($css_data['nested']) && !isset($css_data['states'])) {
            $css = ".{$class_name} {\n";
            foreach ($css_data as $property => $value) {
                if (!empty($value)) {
                    $css .= "    {$property}: {$value};\n";
                }
            }
            $css .= "}";
            return $css;
        }

        // Handle new nested structure
        $css = "";

        // Base styles
        if (isset($css_data['base']) && is_array($css_data['base'])) {
            $css .= ".{$class_name} {\n";
            foreach ($css_data['base'] as $property => $value) {
                if (!empty($value)) {
                    $css .= "    {$property}: {$value};\n";
                }
            }
            $css .= "}\n";
        }

        // Base states
        if (isset($css_data['states']) && is_array($css_data['states'])) {
            foreach ($css_data['states'] as $state => $rules) {
                if (!empty($rules)) {
                    $css .= ".{$class_name}{$state} {\n";
                    foreach ($rules as $property => $value) {
                        if (!empty($value)) {
                            $css .= "    {$property}: {$value};\n";
                        }
                    }
                    $css .= "}\n";
                }
            }
        }

        // Nested elements
        if (isset($css_data['nested']) && is_array($css_data['nested'])) {
            foreach ($css_data['nested'] as $selector => $element_rules) {
                $full_selector = ".{$class_name} {$selector}";

                // Regular nested styles
                $regular_rules = array_filter($element_rules, function ($key) {
                    return $key !== 'states';
                }, ARRAY_FILTER_USE_KEY);

                if (!empty($regular_rules)) {
                    $css .= "{$full_selector} {\n";
                    foreach ($regular_rules as $property => $value) {
                        if (!empty($value)) {
                            $css .= "    {$property}: {$value};\n";
                        }
                    }
                    $css .= "}\n";
                }

                // Nested element states
                if (isset($element_rules['states']) && is_array($element_rules['states'])) {
                    foreach ($element_rules['states'] as $state => $state_rules) {
                        if (!empty($state_rules)) {
                            $css .= "{$full_selector}{$state} {\n";
                            foreach ($state_rules as $property => $value) {
                                if (!empty($value)) {
                                    $css .= "    {$property}: {$value};\n";
                                }
                            }
                            $css .= "}\n";
                        }
                    }
                }
            }
        }

        return $css;
    }

    public static function generate_control_group($field, $property)
    {
        $proActive = Utilities::isProActive();

        if ($field['type'] === 'fieldset') {
            $additional_class = (!$proActive && isset($field['pro']) && $field['pro']) ? 'advgb-promo-overlay-area' : '';
            $html = '<fieldset class="advgb-fieldset '. esc_attr($additional_class) .'">';
            $html .= '<legend>';
            $html .= '<span class="dashicons dashicons-arrow-down"></span>';
            $html .= esc_html($field['legend']);
            $html .= '</legend>';
            $html .= '<div class="advgb-fieldset-content">';

            foreach ($field['fields'] as $sub_property => $sub_field) {
                $html .= self::generate_control_group($sub_field, $sub_property);
            }

            $html .= '</div>';
            $html .= '</fieldset>';
            return $html;
        }

        $additional_class = '';

        if (!$proActive && isset($field['pro']) && $field['pro']) {
            $additional_class = 'advgb-blur';

            if (isset($field['aliases'])) {
                unset($field['aliases']);
            }
            if (isset($field['special_values'])) {
                unset($field['special_values']);
            }
            $property = 'promo-' . rand(0, 99) . '_' . rand(0, 99);
        }

        if ($field['type'] == 'promo' && $proActive) {
            return;
        }

        $html = '';
        if ($field['type'] !== 'promo') {
            $html = '<div class="control-group ' . esc_attr($additional_class) . '">';
            $html .= '<label>' . esc_html($field['label']) . '</label>';
        }

        $field_config = [
            'type' => $field['type'],
            'property' => $property
        ];

        if (isset($field['aliases'])) {
            $field_config['aliases'] = $field['aliases'];
        }

        if (isset($field['special_values'])) {
            $field_config['special_values'] = $field['special_values'];
        }

        switch ($field['type']) {
            case 'color':
                $html .= '<input type="text" class="minicolors style-input"
                    data-css-property="' . esc_attr($property) . '"
                    data-field-config="' . esc_attr(json_encode($field_config)) . '"';

                if (isset($field['special_values'])) {
                    $html .= ' data-special-values="' . esc_attr(json_encode($field['special_values'])) . '"';
                }
                $html .= ' />';
                break;

            case 'text':
                $html .= '<input type="text" class="style-input"
                    data-css-property="' . esc_attr($property) . '"
                    data-field-config="' . esc_attr(json_encode($field_config)) . '"';

                if (isset($field['placeholder'])) {
                    $html .= ' placeholder="' . esc_attr($field['placeholder']) . '"';
                }
                $html .= ' />';
                break;

            case 'number':
                $attrs = '';
                if (isset($field['min']))
                    $attrs .= ' min="' . esc_attr($field['min']) . '"';
                if (isset($field['max']))
                    $attrs .= ' max="' . esc_attr($field['max']) . '"';
                if (isset($field['step']))
                    $attrs .= ' step="' . esc_attr($field['step']) . '"';

                $html .= '<input type="number" class="style-input"
                    data-css-property="' . esc_attr($property) . '"
                    data-field-config="' . esc_attr(json_encode($field_config)) . '"';

                if (isset($field['unit'])) {
                    $html .= ' data-unit="' . esc_attr($field['unit']) . '"';
                    $field_config['unit'] = $field['unit'];
                }
                $html .= $attrs . ' />';
                break;

            case 'select':
                $html .= '<select class="style-input"
                    data-css-property="' . esc_attr($property) . '"
                    data-field-config="' . esc_attr(json_encode($field_config)) . '">';

                foreach ($field['options'] as $value => $label) {
                    $html .= '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
                }
                $html .= '</select>';
                break;

            case 'promo':
                $html .= '<div class="advgb-pro-small-overlay-text">
                        <a class="advgb-pro-link" href="' . esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK) . '" target="_blank">
                            <span class="dashicons dashicons-lock"></span> ' . __('Pro feature', 'advanced-gutenberg') . '
                        </a>
                    </div>
                    ';
                break;
        }

        if ($field['type'] !== 'promo') {
            $html .= '</div>';
        }
        return $html;
    }

    public static function get_font_weight_options()
    {
        return [
            '' => __('Default', 'advanced-gutenberg'),
            'normal' => __('Normal', 'advanced-gutenberg'),
            'bold' => __('Bold', 'advanced-gutenberg'),
            'lighter' => __('Lighter', 'advanced-gutenberg'),
            '100' => '100',
            '200' => '200',
            '300' => '300',
            '400' => '400',
            '500' => '500',
            '600' => '600',
            '700' => '700',
            '800' => '800',
            '900' => '900'
        ];
    }

    public static function get_text_decoration_options()
    {
        return [
            '' => __('Default', 'advanced-gutenberg'),
            'none' => __('None', 'advanced-gutenberg'),
            'underline' => __('Underline', 'advanced-gutenberg'),
            'overline' => __('Overline', 'advanced-gutenberg'),
            'line-through' => __('Line Through', 'advanced-gutenberg')
        ];
    }

    public static function get_transform_options()
    {
        return [
            '' => __('None', 'advanced-gutenberg'),
            'scale(1.05)' => __('Scale Up', 'advanced-gutenberg'),
            'scale(0.95)' => __('Scale Down', 'advanced-gutenberg'),
            'translateY(-5px)' => __('Lift Up', 'advanced-gutenberg'),
            'translateX(5px)' => __('Shift Right', 'advanced-gutenberg'),
            'rotate(5deg)' => __('Rotate', 'advanced-gutenberg')
        ];
    }

    public static function get_style_fields()
    {
        $fields = [
            'colors' => [
                'legend' => __('Colors', 'advanced-gutenberg'),
                'fields' => [
                    'background-color' => [
                        'label' => __('Background Color', 'advanced-gutenberg'),
                        'type' => 'color',
                        'aliases' => ['background'],
                        'special_values' => ['none', 'transparent']
                    ],
                    'color' => [
                        'label' => __('Text Color', 'advanced-gutenberg'),
                        'type' => 'color'
                    ]
                ]
            ],
            'spacing' => [
                'legend' => __('Spacing', 'advanced-gutenberg'),
                'fields' => [
                    'padding-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Padding', 'advanced-gutenberg'),
                        'fields' => [
                            'padding-top' => [
                                'label' => __('Padding Top (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'padding-right' => [
                                'label' => __('Padding Right (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'padding-bottom' => [
                                'label' => __('Padding Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'padding-left' => [
                                'label' => __('Padding Left (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                        ]
                    ],
                    'margin-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Margin', 'advanced-gutenberg'),
                        'fields' => [
                            'margin-top' => [
                                'label' => __('Margin Top (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px'
                            ],
                            'margin-right' => [
                                'label' => __('Margin Right (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px'
                            ],
                            'margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px'
                            ],
                            'margin-left' => [
                                'label' => __('Margin Left (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px'
                            ],
                        ]
                    ],
                    'border-width-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Border Width', 'advanced-gutenberg'),
                        'fields' => [
                            'border-top-width' => [
                                'label' => __('Border Top Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-right-width' => [
                                'label' => __('Border Right Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-bottom-width' => [
                                'label' => __('Border Bottom Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-left-width' => [
                                'label' => __('Border Left Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                        ]
                    ],
                    'border-radius-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Border Radius', 'advanced-gutenberg'),
                        'fields' => [
                            'border-top-left-radius' => [
                                'label' => __('Border Top Left Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-top-right-radius' => [
                                'label' => __('Border Top Right Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-bottom-right-radius' => [
                                'label' => __('Border Bottom Right Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-bottom-left-radius' => [
                                'label' => __('Border Bottom Left Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                        ]
                    ],
                ]
            ],
            'typography' => [
                'legend' => __('Typography', 'advanced-gutenberg'),
                'fields' => [
                    'font-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Font', 'advanced-gutenberg'),
                        'fields' => [
                            'font-family' => [
                                'label' => __('Font Family', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'Arial' => 'Arial',
                                    'Helvetica' => 'Helvetica',
                                    'Times New Roman' => 'Times New Roman',
                                    'Courier New' => 'Courier New',
                                    'Georgia' => 'Georgia',
                                    'Verdana' => 'Verdana',
                                    'system-ui' => 'System UI',
                                    'inherit' => 'Inherit'
                                ]
                            ],
                            'font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8
                            ],
                            'font-style' => [
                                'label' => __('Font Style', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'normal' => __('Normal', 'advanced-gutenberg'),
                                    'italic' => __('Italic', 'advanced-gutenberg'),
                                    'oblique' => __('Oblique', 'advanced-gutenberg')
                                ]
                            ],
                            'font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options()
                            ],
                        ]
                    ],
                    'text-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Text', 'advanced-gutenberg'),
                        'fields' => [
                            'line-height' => [
                                'label' => __('Line Height', 'advanced-gutenberg'),
                                'type' => 'number',
                                'step' => 0.1,
                                'min' => 0.5,
                                'unit' => 'px'
                            ],
                            'letter-spacing' => [
                                'label' => __('Letter Spacing (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px'
                            ],
                            'text-align' => [
                                'label' => __('Text Align', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'left' => __('Left', 'advanced-gutenberg'),
                                    'center' => __('Center', 'advanced-gutenberg'),
                                    'right' => __('Right', 'advanced-gutenberg'),
                                    'justify' => __('Justify', 'advanced-gutenberg')
                                ]
                            ],
                            'text-decoration' => [
                                'label' => __('Text Decoration', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_text_decoration_options()
                            ],
                            'text-transform' => [
                                'label' => __('Text Transform', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'none' => __('None', 'advanced-gutenberg'),
                                    'capitalize' => __('Capitalize', 'advanced-gutenberg'),
                                    'uppercase' => __('Uppercase', 'advanced-gutenberg'),
                                    'lowercase' => __('Lowercase', 'advanced-gutenberg')
                                ]
                            ],
                            'text-shadow' => [
                                'label' => __('Text Shadow', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 1px 1px 2px #000'
                            ]
                        ]
                    ]
                ]
            ],
            'layout' => [
                'legend' => __('Layout', 'advanced-gutenberg'),
                'fields' => [
                    'display-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Display & Position', 'advanced-gutenberg'),
                        'fields' => [
                            'display' => [
                                'label' => __('Display', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'block' => __('Block', 'advanced-gutenberg'),
                                    'inline' => __('Inline', 'advanced-gutenberg'),
                                    'inline-block' => __('Inline Block', 'advanced-gutenberg'),
                                    'flex' => __('Flex', 'advanced-gutenberg'),
                                    'grid' => __('Grid', 'advanced-gutenberg'),
                                    'none' => __('None', 'advanced-gutenberg')
                                ]
                            ],
                            'position' => [
                                'label' => __('Position', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'static' => __('Static', 'advanced-gutenberg'),
                                    'relative' => __('Relative', 'advanced-gutenberg'),
                                    'absolute' => __('Absolute', 'advanced-gutenberg'),
                                    'fixed' => __('Fixed', 'advanced-gutenberg'),
                                    'sticky' => __('Sticky', 'advanced-gutenberg')
                                ]
                            ],
                        ]
                    ],
                    'sizing-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Sizing', 'advanced-gutenberg'),
                        'fields' => [
                            'width' => [
                                'label' => __('Width (%)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => '%',
                                'min' => 0,
                                'max' => 100
                            ],
                            'height' => [
                                'label' => __('Height (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'max-width' => [
                                'label' => __('Max Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'min-width' => [
                                'label' => __('Min Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'max-height' => [
                                'label' => __('Max Height (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'min-height' => [
                                'label' => __('Min Height (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                        ]
                    ],
                    'flow-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Flow', 'advanced-gutenberg'),
                        'fields' => [
                            'float' => [
                                'label' => __('Float', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('None', 'advanced-gutenberg'),
                                    'left' => __('Left', 'advanced-gutenberg'),
                                    'right' => __('Right', 'advanced-gutenberg')
                                ]
                            ],
                            'clear' => [
                                'label' => __('Clear', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('None', 'advanced-gutenberg'),
                                    'left' => __('Left', 'advanced-gutenberg'),
                                    'right' => __('Right', 'advanced-gutenberg'),
                                    'both' => __('Both', 'advanced-gutenberg')
                                ]
                            ],
                            'overflow' => [
                                'label' => __('Overflow', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'visible' => __('Visible', 'advanced-gutenberg'),
                                    'hidden' => __('Hidden', 'advanced-gutenberg'),
                                    'scroll' => __('Scroll', 'advanced-gutenberg'),
                                    'auto' => __('Auto', 'advanced-gutenberg')
                                ]
                            ],
                            'z-index' => [
                                'label' => __('Z-Index', 'advanced-gutenberg'),
                                'type' => 'number'
                            ],
                        ]
                    ]
                ]
            ],
            'border' => [
                'legend' => __('Border', 'advanced-gutenberg'),
                'fields' => [
                    'left-border-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Left Border', 'advanced-gutenberg'),
                        'fields' => [
                            'border-left-width' => [
                                'label' => __('Left Border Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-left-style' => [
                                'label' => __('Left Border Style', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'none' => __('None', 'advanced-gutenberg'),
                                    'solid' => __('Solid', 'advanced-gutenberg'),
                                    'dashed' => __('Dashed', 'advanced-gutenberg'),
                                    'dotted' => __('Dotted', 'advanced-gutenberg'),
                                    'double' => __('Double', 'advanced-gutenberg')
                                ]
                            ],
                            'border-left-color' => [
                                'label' => __('Left Border Color', 'advanced-gutenberg'),
                                'type' => 'color'
                            ],
                        ]
                    ],
                    'right-border-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Right Border', 'advanced-gutenberg'),
                        'fields' => [
                            'border-right-width' => [
                                'label' => __('Right Border Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-right-style' => [
                                'label' => __('Right Border Style', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'none' => __('None', 'advanced-gutenberg'),
                                    'solid' => __('Solid', 'advanced-gutenberg'),
                                    'dashed' => __('Dashed', 'advanced-gutenberg'),
                                    'dotted' => __('Dotted', 'advanced-gutenberg'),
                                    'double' => __('Double', 'advanced-gutenberg')
                                ]
                            ],
                            'border-right-color' => [
                                'label' => __('Right Border Color', 'advanced-gutenberg'),
                                'type' => 'color'
                            ],
                        ]
                    ],
                    'top-border-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Top Border', 'advanced-gutenberg'),
                        'fields' => [
                            'border-top-width' => [
                                'label' => __('Top Border Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-top-style' => [
                                'label' => __('Top Border Style', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'none' => __('None', 'advanced-gutenberg'),
                                    'solid' => __('Solid', 'advanced-gutenberg'),
                                    'dashed' => __('Dashed', 'advanced-gutenberg'),
                                    'dotted' => __('Dotted', 'advanced-gutenberg'),
                                    'double' => __('Double', 'advanced-gutenberg')
                                ]
                            ],
                            'border-top-color' => [
                                'label' => __('Top Border Color', 'advanced-gutenberg'),
                                'type' => 'color'
                            ],
                        ]
                    ],
                    'bottom-border-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Bottom Border', 'advanced-gutenberg'),
                        'fields' => [
                            'border-bottom-width' => [
                                'label' => __('Bottom Border Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0
                            ],
                            'border-bottom-style' => [
                                'label' => __('Bottom Border Style', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'none' => __('None', 'advanced-gutenberg'),
                                    'solid' => __('Solid', 'advanced-gutenberg'),
                                    'dashed' => __('Dashed', 'advanced-gutenberg'),
                                    'dotted' => __('Dotted', 'advanced-gutenberg'),
                                    'double' => __('Double', 'advanced-gutenberg')
                                ]
                            ],
                            'border-bottom-color' => [
                                'label' => __('Bottom Border Color', 'advanced-gutenberg'),
                                'type' => 'color'
                            ],
                        ]
                    ],
                ]
            ],



            'text-elements' => [
                'legend' => __('Text Elements', 'advanced-gutenberg'),
                'fields' => [
                    'paragraph-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Paragraph', 'advanced-gutenberg'),
                        'fields' => [
                            'p-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'p-line-height' => [
                                'label' => __('Line Height', 'advanced-gutenberg'),
                                'type' => 'number',
                                'step' => 0.1,
                                'min' => 0.5,
                                'pro' => true,
                            ],
                            'paragraph-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'p-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'p-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'p-text-align' => [
                                'label' => __('Text Align', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'left' => __('Left', 'advanced-gutenberg'),
                                    'center' => __('Center', 'advanced-gutenberg'),
                                    'right' => __('Right', 'advanced-gutenberg'),
                                    'justify' => __('Justify', 'advanced-gutenberg')
                                ],
                                'pro' => true,
                            ]
                        ]
                    ],

                    'span-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Span', 'advanced-gutenberg'),
                        'fields' => [
                            'span-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'span-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'span-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'span-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'span-background-color' => [
                                'label' => __('Background Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'blockquote-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Blockquote', 'advanced-gutenberg'),
                        'fields' => [
                            'blockquote-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'blockquote-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'blockquote-border-color' => [
                                'label' => __('Border Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'blockquote-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'blockquote-background-color' => [
                                'label' => __('Background Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'blockquote-padding' => [
                                'label' => __('Padding (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'blockquote-margin' => [
                                'label' => __('Margin (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'list-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Lists', 'advanced-gutenberg'),
                        'fields' => [
                            'ul-margin' => [
                                'label' => __('List Margin (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'ul-padding' => [
                                'label' => __('List Padding (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'list-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'li-margin-bottom' => [
                                'label' => __('Item Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'li-color' => [
                                'label' => __('Item Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ]
                        ]
                    ]
                ]
            ],

            'heading-elements' => [
                'legend' => __('Heading Elements', 'advanced-gutenberg'),
                'fields' => [
                    'h1-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('H1 Heading', 'advanced-gutenberg'),
                        'fields' => [
                            'h1-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'h1-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h1-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'h1-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h1-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'h1-line-height' => [
                                'label' => __('Line Height', 'advanced-gutenberg'),
                                'type' => 'number',
                                'step' => 0.1,
                                'min' => 0.5,
                                'pro' => true,
                            ],
                            'h1-text-align' => [
                                'label' => __('Text Align', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('Default', 'advanced-gutenberg'),
                                    'left' => __('Left', 'advanced-gutenberg'),
                                    'center' => __('Center', 'advanced-gutenberg'),
                                    'right' => __('Right', 'advanced-gutenberg')
                                ],
                                'pro' => true,
                            ]
                        ]
                    ],

                    'h2-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('H2 Heading', 'advanced-gutenberg'),
                        'fields' => [
                            'h2-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'h2-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h2-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'h2-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h2-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'h2-line-height' => [
                                'label' => __('Line Height', 'advanced-gutenberg'),
                                'type' => 'number',
                                'step' => 0.1,
                                'min' => 0.5,
                                'pro' => true,
                            ]
                        ]
                    ],

                    'h3-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('H3 Heading', 'advanced-gutenberg'),
                        'fields' => [
                            'h3-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'h3-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h3-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h3-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'h3-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'h3-line-height' => [
                                'label' => __('Line Height', 'advanced-gutenberg'),
                                'type' => 'number',
                                'step' => 0.1,
                                'min' => 0.5,
                                'pro' => true,
                            ]
                        ]
                    ],

                    'h4-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('H4 Heading', 'advanced-gutenberg'),
                        'fields' => [
                            'h4-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'h4-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h4-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h4-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'h4-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'h5-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('H5 Heading', 'advanced-gutenberg'),
                        'fields' => [
                            'h5-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'h5-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h5-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h5-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'h5-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'h6-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('H6 Heading', 'advanced-gutenberg'),
                        'fields' => [
                            'h6-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ],
                            'h6-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h6-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h6-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'h6-margin-bottom' => [
                                'label' => __('Margin Bottom (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'heading-hover-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Heading Hover Effects', 'advanced-gutenberg'),
                        'fields' => [
                            'h1-hover-color' => [
                                'label' => __('H1 Hover Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h2-hover-color' => [
                                'label' => __('H2 Hover Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'heading-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h3-hover-color' => [
                                'label' => __('H3 Hover Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'heading-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ]
                        ]
                    ]
                ]
            ],

            'link-elements' => [
                'legend' => __('Link Elements', 'advanced-gutenberg'),
                'fields' => [
                    'link-base-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Link Base Styles', 'advanced-gutenberg'),
                        'fields' => [
                            'a-color' => [
                                'label' => __('Link Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'a-text-decoration' => [
                                'label' => __('Text Decoration', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_text_decoration_options(),
                                'pro' => true,
                            ],
                            'a-link-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'a-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ],
                            'a-font-size' => [
                                'label' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 8,
                                'pro' => true,
                            ]
                        ]
                    ],

                    'link-hover-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Link Hover States', 'advanced-gutenberg'),
                        'fields' => [
                            'a-hover-color' => [
                                'label' => __('Hover Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'a-hover-text-decoration' => [
                                'label' => __('Hover Text Decoration', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_text_decoration_options(),
                                'pro' => true,
                            ],
                            'a-link-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'a-hover-background-color' => [
                                'label' => __('Hover Background', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'a-hover-font-weight' => [
                                'label' => __('Hover Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ]
                        ]
                    ],

                    'link-in-heading-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Links in Headings', 'advanced-gutenberg'),
                        'fields' => [
                            'h1-a-color' => [
                                'label' => __('H1 Link Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h2-a-color' => [
                                'label' => __('H2 Link Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h3-a-color' => [
                                'label' => __('H3 Link Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h1-a-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'h1-a-hover-color' => [
                                'label' => __('H1 Link Hover Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'h2-a-hover-color' => [
                                'label' => __('H2 Link Hover Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ]
                        ]
                    ]
                ]
            ],

            'media-elements' => [
                'legend' => __('Media Elements', 'advanced-gutenberg'),
                'fields' => [
                    'image-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Images', 'advanced-gutenberg'),
                        'fields' => [
                            'img-border-radius' => [
                                'label' => __('Border Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'img-border-width' => [
                                'label' => __('Border Width (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'img-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'img-border-color' => [
                                'label' => __('Border Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'img-max-width' => [
                                'label' => __('Max Width (%)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => '%',
                                'min' => 0,
                                'max' => 100,
                                'pro' => true,
                            ],
                            'img-box-shadow' => [
                                'label' => __('Box Shadow', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 0 2px 10px rgba(0,0,0,0.1)',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'image-hover-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Image Hover Effects', 'advanced-gutenberg'),
                        'fields' => [
                            'img-hover-transform' => [
                                'label' => __('Hover Transform', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_transform_options(),
                                'pro' => true,
                            ],
                            'img-hover-opacity' => [
                                'label' => __('Hover Opacity', 'advanced-gutenberg'),
                                'type' => 'number',
                                'min' => 0,
                                'max' => 1,
                                'step' => 0.1,
                                'pro' => true,
                            ],
                            'img-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'img-hover-filter' => [
                                'label' => __('Hover Filter', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => [
                                    '' => __('None', 'advanced-gutenberg'),
                                    'grayscale(100%)' => __('Grayscale', 'advanced-gutenberg'),
                                    'sepia(100%)' => __('Sepia', 'advanced-gutenberg'),
                                    'blur(2px)' => __('Blur', 'advanced-gutenberg'),
                                    'brightness(1.2)' => __('Brightness', 'advanced-gutenberg'),
                                    'contrast(1.2)' => __('Contrast', 'advanced-gutenberg')
                                ],
                                'pro' => true,
                            ],
                            'img-hover-border-color' => [
                                'label' => __('Hover Border Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'video-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Videos', 'advanced-gutenberg'),
                        'fields' => [
                            'video-width' => [
                                'label' => __('Width (%)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => '%',
                                'min' => 0,
                                'max' => 100,
                                'pro' => true,
                            ],
                            'vidoe-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'video-border-radius' => [
                                'label' => __('Border Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ]
                        ]
                    ]
                ]
            ],

            'container-elements' => [
                'legend' => __('Container Elements', 'advanced-gutenberg'),
                'fields' => [
                    'div-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Div Containers', 'advanced-gutenberg'),
                        'fields' => [
                            'div-background-color' => [
                                'label' => __('Background Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'div-padding' => [
                                'label' => __('Padding (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'div-border-radius' => [
                                'label' => __('Border Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'div-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'div-box-shadow' => [
                                'label' => __('Box Shadow', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 0 2px 10px rgba(0,0,0,0.1)',
                                'pro' => true,
                            ],
                            'div-border' => [
                                'label' => __('Border', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 1px solid #ccc',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'section-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Sections', 'advanced-gutenberg'),
                        'fields' => [
                            'section-background-color' => [
                                'label' => __('Background Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'section-margin' => [
                                'label' => __('Margin (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'pro' => true,
                            ],
                            'section-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'section-padding' => [
                                'label' => __('Padding (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'section-border' => [
                                'label' => __('Border', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 1px solid #ccc',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'container-hover-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Container Hover Effects', 'advanced-gutenberg'),
                        'fields' => [
                            'div-hover-background-color' => [
                                'label' => __('Div Hover Background', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'div-hover-box-shadow' => [
                                'label' => __('Div Hover Shadow', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 0 4px 20px rgba(0,0,0,0.15)',
                                'pro' => true,
                            ],
                            'container-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'section-hover-background-color' => [
                                'label' => __('Section Hover Background', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ]
                        ]
                    ]
                ]
            ],

            'interactive-elements' => [
                'legend' => __('Interactive Elements', 'advanced-gutenberg'),
                'fields' => [
                    'button-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Buttons', 'advanced-gutenberg'),
                        'fields' => [
                            'button-background-color' => [
                                'label' => __('Background Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'button-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'button-border-radius' => [
                                'label' => __('Border Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'buton-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'button-padding' => [
                                'label' => __('Padding (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'button-border' => [
                                'label' => __('Border', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 2px solid #333',
                                'pro' => true,
                            ],
                            'button-font-weight' => [
                                'label' => __('Font Weight', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_font_weight_options(),
                                'pro' => true,
                            ]
                        ]
                    ],

                    'button-hover-group' => [
                        'type' => 'fieldset',
                        'pro' => true,
                        'legend' => __('Button Hover States', 'advanced-gutenberg'),
                        'fields' => [
                            'button-hover-background-color' => [
                                'label' => __('Hover Background', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'button-hover-color' => [
                                'label' => __('Hover Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'button-hover-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'button-hover-transform' => [
                                'label' => __('Hover Transform', 'advanced-gutenberg'),
                                'type' => 'select',
                                'options' => self::get_transform_options(),
                                'pro' => true,
                            ],
                            'button-hover-border-color' => [
                                'label' => __('Hover Border Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'button-hover-box-shadow' => [
                                'label' => __('Hover Shadow', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 0 4px 15px rgba(0,0,0,0.2)',
                                'pro' => true,
                            ]
                        ]
                    ],

                    'input-group' => [
                        'type' => 'fieldset',
                        'legend' => __('Form Inputs', 'advanced-gutenberg'),
                        'pro' => true,
                        'fields' => [
                            'input-background-color' => [
                                'label' => __('Background Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'input-color' => [
                                'label' => __('Text Color', 'advanced-gutenberg'),
                                'type' => 'color',
                                'pro' => true,
                            ],
                            'input-border' => [
                                'label' => __('Border', 'advanced-gutenberg'),
                                'type' => 'text',
                                'placeholder' => 'e.g. 1px solid #ddd',
                                'pro' => true,
                            ],
                            'input-promo' => [
                                'label' => '',
                                'type' => 'promo'
                            ],
                            'input-border-radius' => [
                                'label' => __('Border Radius (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ],
                            'input-padding' => [
                                'label' => __('Padding (px)', 'advanced-gutenberg'),
                                'type' => 'number',
                                'unit' => 'px',
                                'min' => 0,
                                'pro' => true,
                            ]
                        ]
                    ]
                ]
            ]

        ];

        return $fields;
    }
}