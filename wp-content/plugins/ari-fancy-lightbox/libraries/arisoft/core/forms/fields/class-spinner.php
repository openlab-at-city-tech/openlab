<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Spinner extends Field {
    static protected $assets_loaded = false;

    protected $min = null;

    protected $max = null;

    protected $float = false;

    protected $options = array();

    function __construct( $options = array() ) {
        if ( isset( $options['float'] ) ) {
            $this->float = $options['float'];

            unset( $options['float'] );
        }

        parent::__construct( $options );
    }

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'float':
                $this->$name = (bool) $value;
                break;

            case 'options':
                $this->$name = is_array( $value ) ? $value : array();
                break;

            case 'min':
            case 'max':
                $this->$name = $this->float ? floatval( $value ) : intlva( $value, 10 );
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    protected function load_assets() {
        if ( self::$assets_loaded )
            return ;

        wp_enqueue_script( 'ari-form-elements' );

        self::$assets_loaded = true;
    }

    protected function get_options() {
        $options = $this->options;

        unset( $options['min'] );
        unset( $options['max'] );
        unset( $options['format'] );

        if ( ! is_null( $this->min ) )
            $options['min'] = $this->min;

        if ( ! is_null( $this->max ) )
            $options['max'] = $this->max;

        $options['format'] = $this->float ? 'c' : 'n';

        return $options;
    }

    public function output() {
        $this->load_assets();

        $value = $this->value;
        $id = $this->get_id();
        $options = $this->get_options();
        $options['value'] = $value;

        $js_options = json_encode( $options, JSON_NUMERIC_CHECK );

        $output = array();

        $output[] = sprintf(
            '<input type="text" id="%1$s" name="%2$s" value="%3$s" data-spinner-options="%4$s" class="ari-form-spinner%5$s" ',
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
            $output[] = '<span class="ari-form-element-postfix">' . $postfix . '</span>';

        return implode( $output );
    }
}
