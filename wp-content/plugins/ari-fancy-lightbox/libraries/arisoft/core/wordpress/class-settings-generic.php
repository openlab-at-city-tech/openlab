<?php
namespace Ari\Wordpress;

use Ari\Utils\Array_Helper;

class Settings_Generic {
    protected $options = null;

    protected $settings_name;

    protected $default_settings = array(
    );

    public static function instance() {
        static $instance = null;

        $class = get_called_class();

        if ( is_null( $instance ) ) {
            $instance = new $class();
        }

        return $instance;
    }

    protected function __construct() {

    }

    public function options() {
        if ( ! is_null( $this->options ) )
            return $this->options;

        if ( ! empty( $this->settings_name ) )
            $this->options = get_option( $this->settings_name );
        else
            $this->options = $this->get_default_options();

        return $this->options;
    }

    public function get_default_options() {
        return $this->default_settings;
    }

    public function full_options() {
        $current_options = $this->options();

        if ( ! is_array( $current_options ) )
            $current_options = array();

        $default_settings = $this->get_default_options();

        $flat_default_settings = Array_Helper::to_flat_array( $default_settings );
        $flat_current_options = Array_Helper::to_flat_array( $current_options );

        $full_options = Array_Helper::to_complex_array(
            array_merge(
                $flat_default_settings,
                $flat_current_options
            )
        );

        return $full_options;
    }

    public function get_option( $name, $default = null ) {
        $options = $this->options();

        $default_settings = $this->get_default_options();
        $complex_name = strpos( $name, '.' ) !== false;
        $val = $default;

        if ( $complex_name ) {
            $val = Array_Helper::value_by_path( $name, $options );

            if ( is_null( $val ) ) {
                $val = Array_Helper::value_by_path( $name, $default_settings );
                if ( is_null( $val ) )
                    $val = $default;
            }
        } else {
            if ( isset( $options[$name] ) ) {
                $val = $options[$name];
            } else if ( is_null( $default ) && isset( $default_settings[$name] ) ) {
                $val = $default_settings[$name];
            }
        }

        return $val;
    }

    public function sanitize( $input, $defaults = false ) {
        $new_input = array();

        if ( false === $defaults)
            $defaults = $this->get_default_options();

        foreach ( $defaults as $key => $val ) {
            $type = gettype( $val );

            if ( 'boolean' == $type && ! isset( $input[$key] ) ) {
                $new_input[$key] = false;
            } else if ( 'array' == $type && ! isset( $input[$key] ) ) {
                $new_input[$key] = array();
            } else if ( isset( $input[$key] ) ) {
                $input_val = $input[$key];
                $filtered_val = null;
                switch ( $type ) {
                    case 'boolean':
                        $filtered_val = (bool) $input_val;
                        break;

                    case 'integer':
                        $filtered_val = intval( $input_val, 10 );
                        break;

                    case 'double':
                        $filtered_val = floatval( $input_val );
                        break;

                    case 'array':
                        $filtered_val = $input_val;
                        break;

                    case 'string':
                        $filtered_val = trim( $input_val );
                        break;
                }

                if ( ! is_null( $filtered_val) ) {
                    $new_input[$key] = $filtered_val;
                }
            }
        }

        return $new_input;
    }

    public function save( $data ) {
        if ( empty( $this->settings_name ) )
            return false;

        $data = $this->sanitize( $data );

        if ( false === $data )
            return false;

        update_option(
            $this->settings_name,
            $data
        );

        return true;
    }
}
