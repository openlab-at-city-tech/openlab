<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Checkbox extends Field {
    public function __set( $name, $value ) {
        switch ( $name ) {
            default:
                parent::__set( $name, $value );
        }
    }

    public function get_value() {
        return (bool) $this->value;
    }

    public function output() {
        $id = $this->get_id();

        $output = array(
            '<label for="' . $id . '"><input type="checkbox"'
        );

        $output[] = ' id="' . $id . '"';
        $output[] = ' name="' . $this->get_name() . '"';
        $output[] = ' value="1"';

        $value = $this->get_value();

        if ( $value )
            $output[] = ' checked="checked"';

        if ( ! $this->autocomplete )
            $output[] = ' autocomplete="off"';

        if ( $this->class )
            $output[] = ' class="' . $this->class . '"';

        $attributes = $this->attributes_string();
        if ( $attributes )
            $output[] = ' ' . $attributes;

        $output[] = ' />';

        $postfix = $this->get_postfix();
        if ( $postfix )
            $output[] = '<span class="ari-form-element-postfix">' . $postfix . '</span>';

        $output[] = '</label>';

        return implode( $output );
    }
}
