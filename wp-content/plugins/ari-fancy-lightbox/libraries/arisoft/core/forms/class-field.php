<?php
namespace Ari\Forms;

abstract class Field {
    protected $id;

    protected $label;

    protected $description;

    protected $class;

    protected $attributes;

    protected $group = '';

    protected $autocomplete = false;

    protected $value;

    protected $form;

    protected $postfix;

    function __construct( $options = array() ) {
        unset( $options['type'] );

        foreach ( $options as $key => $val ) {
            $this->$key = $val;
        }
    }

    public function __get( $name ) {
        switch ( $name ) {
            case 'id':
            case 'label':
            case 'description':
            case 'class':
            case 'group':
            case 'attributes':
                return $this->$name;
        }

        return ;
    }

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'class':
				$value = preg_replace( '/\s+/', ' ', trim( (string) $value ) );

            case 'group':
            case 'id':
            case 'label':
            case 'description':
                $this->$name = (string) $value;
                break;

            case 'autocomplete':
                $this->$name = (bool) $value;
                break;

            case 'attributes':
                $this->$name = (array) $value;
                break;

            case 'postfix':
                if ( ! is_bool( $value ) )
                    $value = (string) $value;

                $this->$name = $value;
                break;

            default:
                if ( ! property_exists( __CLASS__, $name ) ) {
                    $this->$name = $value;
                }
        }
    }

    public function set_form( $form ) {
        $this->form = $form;
    }

    public function set_value( $value ) {
        $this->value = $value;
    }

    public function get_value() {
        return $this->value;
    }

    public function get_id() {
        $id = $this->id;

        if ( $this->form && $this->form->options->prefix ) {
            $id = $this->form->options->prefix . '_' . str_replace( '$', '_', $id );
        }

        return $id;
    }

    public function get_name() {
        $name = $this->id;

        if ( $this->form && $this->form->options->prefix ) {
            $name = $this->form->options->prefix . '[' . $name . ']';
        }

        return $name;
    }

    public function get_label() {
        return $this->label;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_postfix() {
        $postfix = '';

        if ( is_bool( $this->postfix ) ) {
            if ( $this->postfix )
                $postfix = $this->get_description();
        } else {
            $postfix = $this->postfix;
        }

        return $postfix;
    }

    protected function attributes_string() {
        $content = array();
        $attributes = $this->attributes;

        if ( is_array( $attributes ) ) {
            foreach ( $attributes as $key => $val ) {
                $content[] = sprintf(
                    '%s="%s"',
                    $key,
                    htmlspecialchars( $val, ENT_COMPAT, 'UTF-8' )
                );
            }
        }

        return implode( ' ', $content );
    }

    public function output() {
        return '';
    }
}
