<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Color extends Field {
    static protected $assets_loaded = false;

    protected $options = array();

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'options':
                $this->$name = is_array( $value ) ? $value : array();
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    protected function load_assets() {
        if ( self::$assets_loaded )
            return ;

        wp_enqueue_style( 'wp-color-picker');
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'ari-form-elements' );

        self::$assets_loaded = true;
    }

    public function output() {
        $this->load_assets();

        $value = $this->value;
        $id = $this->get_id();
        $options = $this->options;
        $options['value'] = $value;

        $js_options = json_encode( $options, JSON_NUMERIC_CHECK );

        $output = array();

        $output[] = sprintf(
            '<input type="text" id="%1$s" name="%2$s" value="%3$s" data-color-options="%4$s" class="ari-form-color%5$s" ',
            $id,
            $this->get_name(),
            htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ),
            htmlspecialchars( $js_options, ENT_COMPAT, 'UTF-8' ),
            $this->class ? ' ' . $this->class : ''
        );

        if ( ! $this->autocomplete )
            $output[] = ' autocomplete="off"';

        $output[] = ' />';

        $postfix = $this->get_postfix();
        if ( $postfix )
            $output[] = '<span class="ari-form-element-postfix">' . $this->postfix . '</span>';

        return implode( $output );
    }
}
