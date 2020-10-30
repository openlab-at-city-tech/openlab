<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Checkbox_Group extends Field {
    public function __set( $name, $value ) {
        switch ( $name ) {
            default:
                parent::__set( $name, $value );
        }
    }

    protected function get_options() {
        return $this->options;
    }

    public function output() {
        $output = array();

        $options = $this->get_options();

        $idx = 0;
        $id = $this->get_id();
        foreach ( $options as $key => $option ) {
            $is_complex_option = is_array( $option );

            $label = $is_complex_option ? $options['label'] : $option;
            $value = $is_complex_option && isset( $options['value'] ) ? $options['value'] : $key;

            $output[] = '<p><label><input type="checkbox"';

            $output[] = ' id="' . $id . '_' . $idx . '"';
            $output[] = ' name="' . $this->get_name() . '[]"';
            $output[] = ' value="' . htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ) . '"';

            if ( is_array( $this->value ) && in_array( $value, $this->value ) )
                $output[] = ' checked="checked"';

            if ( ! $this->autocomplete )
                $output[] = ' autocomplete="off"';

            if ( $this->class )
                $output[] = ' class="' . $this->class . '"';

            $output[] = ' />' . $label . '</label></p>';

            $idx++;
        }

        return implode( $output );
    }
}
