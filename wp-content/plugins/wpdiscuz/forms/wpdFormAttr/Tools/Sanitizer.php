<?php

namespace wpdFormAttr\Tools;

class Sanitizer {

    public static function sanitize($action, $variable_name, $filter, $default = "") {
        if ($filter === "FILTER_SANITIZE_STRING") {
            $glob = INPUT_POST === $action ? $_POST : $_GET;
            if (key_exists($variable_name, $glob)) {
                return sanitize_text_field($glob[$variable_name]);
            } else {
                return $default;
            }
        }
        $variable = isset($variable_name) ? filter_input($action, $variable_name, $filter) : '';
        return $variable ? $variable : $default;
    }

}
