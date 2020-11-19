<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Textarea extends Field {
    protected $placeholder;

    protected $cols = 0;

    protected $rows = 0;

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'placeholder':
                $this->$name = (string) $value;
                break;

            case 'cols':
            case 'rows':
                $this->$name = intval( $value, 10 );
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    public function output() {
        $output = array(
            '<textarea'
        );

        $output[] = ' id="' . $this->get_id() . '"';
        $output[] = ' name="' . $this->get_name() . '"';

        if ( $this->rows > 0 )
            $output[] = ' rows="' . $this->rows . '"';

        if ( $this->cols > 0 )
            $output[] = ' cols="' . $this->cols . '"';

        if ( ! $this->autocomplete )
            $output[] = ' autocomplete="off"';

        if ( $this->placeholder )
            $output[] = ' placeholder="' . htmlspecialchars( $this->placeholder, ENT_COMPAT, 'UTF-8' ) . '"';

        if ( $this->class )
            $output[] = ' class="' . $this->class . '"';

        $output[] = '>' . htmlspecialchars( $this->value, ENT_COMPAT, 'UTF-8' ) . '</textarea>';

        return implode( $output );
    }
}
