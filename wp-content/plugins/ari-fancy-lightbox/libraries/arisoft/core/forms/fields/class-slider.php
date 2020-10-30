<?php
namespace Ari\Forms\Fields;

use Ari\Forms\Field as Field;

class Slider extends Field {
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

        if ( ! is_null( $this->min ) )
            $options['min'] = $this->min;

        if ( ! is_null( $this->max ) )
            $options['max'] = $this->max;

        return $options;
    }

    public function output() {
        $this->load_assets();

        $value = $this->value;
        $id = $this->get_id();
        $postfix = $this->get_postfix();
        $slider_id = $id . '_slider';
        $container_id = $id . '_container';
        $options = $this->get_options();
        $options['value'] = $value;

        $js_options = json_encode( $options, JSON_NUMERIC_CHECK );

        $output = array();

        $output[] = '<div id="' . $container_id . '" class="ari-form-slider-container' . ( $this->class ? ' ' . $this->class : '' ) . '">';

        $output[] = '<div class="ari-form-slider-el">';
        $output[] = sprintf(
            '<input type="text" id="%1$s" name="%2$s" value="%3$s" readonly />%4$s',
            $id,
            $this->get_name(),
            htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ),
            $postfix ? '<span class="ari-form-element-postfix">' . $postfix . '</span>' : ''
        );
        $output[] = '</div>';
        $output[] = '<div id="' . $slider_id . '"';
        $output[] = ' class="ari-form-slider" data-slider-options="' . htmlspecialchars( $js_options, ENT_COMPAT, 'UTF-8' ) . '" data-slider-id="' . $id . '"';

        $output[] = '></div></div>';


        return implode( $output );
    }
}
