<?php

namespace com\cminds\PLUGIN_NAMESPASE\settings;

class SettingsView {

    public static function renderOptionControls($optionKey, $settings) {

        switch ($settings['type']) {

            case 'bool':

                return self::renderBool($optionKey, $settings);

            case 'int':

                return self::renderInputNumber($optionKey, $settings);

            case 'textarea':

                return self::renderTextarea($optionKey, $settings);

            case 'rich_text':

                return self::renderRichText($optionKey, $settings);

            case 'radio':

                return '<div class="multiline">' . self::renderRadio($optionKey, $settings['options'], $settings['value']) . '</div>';

            case 'select':

                return self::renderSelect($optionKey, $settings);

            case 'multiselect':

                return self::renderMultiSelect($optionKey, $settings);

            case 'multicheckbox':

                /*
                 * That's by decision, multiselect works better
                 */
                return self::renderMultiSelect($optionKey, $settings);

            case 'custom':

                return self::renderCustomFunctionInput($optionKey, $settings);

            case 'string':

                return self::renderInputText($optionKey, $settings);

            case 'label':

                return self::renderLabel($optionKey, $settings);

            case 'color':

                return self::renderColor($optionKey, $settings);

            default:
                if (isset($settings['html'])) {
                    return $settings['html'];
                } else {
                    throw new \Exception('Missing "html" value for custom setting with id: ' . $optionKey . ' in config');
                }
        }
    }

    protected static function renderCustomFunctionInput($optionKey, $settings) {
        if (isset($settings['callback'])) {
            return call_user_func($settings['callback'], $settings);
        } else {
            throw new \Exception('Missing "callback" value for custom setting with id: ' . $optionKey . ' in config');
        }
    }

    protected static function renderColor($optionKey, $settings) {
        ob_start();
        ?>
        <script>
            jQuery(function ($) {
                $('input[name="<?php echo esc_attr($optionKey); ?>"]').wpColorPicker();
            });
        </script>
        <?php echo sprintf('<input type="text" name="%s" value="%s" />', esc_attr($optionKey), esc_attr($settings['value'])); ?>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    protected static function renderBool($optionKey, $settings) {
        return self::renderRadio($optionKey, array(1 => 'On', 0 => 'Off'), $settings['value']);
    }

    protected static function renderInputNumber($name, $settings) {
        return sprintf('<input type="number" name="%s" value="%s" />', esc_attr($name), esc_attr($settings['value']));
    }

    protected static function renderTextarea($name, $settings) {
        return sprintf('<textarea name="%s" cols="60" rows="5">%s</textarea>', esc_attr($name), $settings['value']);
    }

    protected static function renderRichText($name, $settings) {
        ob_start();

        wp_editor($settings['value'], $name, array(
            'textarea_name' => $name,
            'textarea_rows' => 10,
        ));
        return ob_get_clean();
    }

    protected static function renderRadio($name, $options, $currentValue) {

        $result = '';
        $fieldName = esc_attr($name);
        foreach ($options as $value => $text) {
            $fieldId = esc_attr($name . '_' . $value);
            $result .= sprintf('<label><input type="radio" name="%s" id="%s" value="%s"%s /> %s</label>',
                    $fieldName, $fieldId, esc_attr($value),
                    ( $currentValue == $value ? ' checked="checked"' : ''), esc_html($text)
            );
        }
        return $result;
    }

    protected static function renderSelect($name, $settings) {
        return sprintf('<div><select name="%s">%s</select></div>', esc_attr($name), self::renderSelectOptions($name, $settings['options'], $settings['value']));
    }

    protected static function renderSelectOptions($name, $options, $currentValue) {

        $result = '';
        foreach ($options as $value => $text) {
            $result .= sprintf('<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    ( self::isSelected($value, $currentValue) ? ' selected="selected"' : ''),
                    esc_html($text)
            );
        }
        return $result;
    }

    protected static function isSelected($option, $value) {
        if (is_array($value)) {
            return in_array($option, $value);
        } else {
            return ((string) $option == (string) $value);
        }
    }

    protected static function renderMultiSelect($name, $settings) {
        return sprintf('<div><select name="%s[]" multiple="multiple">%s</select></div>',
                esc_attr($name), self::renderSelectOptions($name, $settings['options'], $settings['value']));
    }

    protected static function renderMultiCheckbox($name, $settings) {
        $result = '';
        foreach ($settings['options'] as $value => $label) {
            $result .= self::renderMultiCheckboxItem($name, $value, $label, $settings['value']);
        }
        return '<div>' . $result . '</div>';
    }

    protected static function renderMultiCheckboxItem($name, $value, $label, $currentValue) {
        return sprintf('<div><label><input type="checkbox" name="%s[]" value="%s"%s /> %s</label></div>',
                esc_attr($name),
                esc_attr($value),
                (in_array($value, $currentValue) ? ' checked="checked"' : ''),
                esc_html($label)
        );
    }

    protected static function renderInputText($name, $settings) {
        return sprintf('<input type="text" name="%s" value="%s" />', esc_attr($name), esc_attr($settings['value']));
    }

    protected static function renderLabel($name, $settings) {
        return sprintf('<input type="text" name="%s" value="%s" />', $name, $settings['value']);
    }

}
