<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Select extends Field {
    protected $multiple = false;

    protected $options = array();

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'multiple':
                $this->$name = (bool) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    protected function get_options() {
        return $this->options;
    }

    public function output() {
        $output = array(
            '<select'
        );

        $output[] = ' id="' . $this->get_id() . '"';
        $output[] = ' name="' . $this->get_name() . ( $this->multiple ? '[]' : '' ) . '"';

        if ( ! $this->autocomplete )
            $output[] = ' autocomplete="off"';

        if ( $this->multiple )
            $output[] = ' multiple';

        if ( $this->class )
            $output[] = ' class="' . $this->class . '"';

        $output[] = '>';

        $options = $this->get_options();

        if ( is_array( $options ) ) {
            foreach ( $options as $key => $val ) {
                $output[] = sprintf(
                    '<option value="%1$s"%2$s>%3$s</option>',
                    htmlspecialchars( $key, ENT_COMPAT, 'UTF-8' ),
                    $key === $this->value ? ' selected' : '',
                    esc_html( $val )
                );
            }
        }

        $output[] = '</select>';

        return implode( $output );
    }
}
