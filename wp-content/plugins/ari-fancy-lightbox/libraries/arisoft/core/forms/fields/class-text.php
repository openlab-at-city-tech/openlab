<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Text extends Field {
    protected $placeholder;

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'placeholder':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    public function output() {
        $output = array(
            '<input type="text"'
        );

        $output[] = ' id="' . $this->get_id() . '"';
        $output[] = ' name="' . $this->get_name() . '"';
        $output[] = ' value="' . htmlspecialchars( $this->value, ENT_COMPAT, 'UTF-8' ) . '"';

        if ( ! $this->autocomplete )
            $output[] = ' autocomplete="off"';

        if ( $this->placeholder )
            $output[] = ' placeholder="' . htmlspecialchars( $this->placeholder, ENT_COMPAT, 'UTF-8' ) . '"';

        if ( $this->class )
            $output[] = ' class="' . $this->class . '"';

        $output[] = ' />';

        return implode( $output );
    }
}
