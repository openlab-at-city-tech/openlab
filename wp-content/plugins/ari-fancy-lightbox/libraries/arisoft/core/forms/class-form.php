<?php
namespace Ari\Forms;

use Ari\Utils\Object_Factory as Object_Factory;
use Ari\Utils\Array_Helper as Array_Helper;

class Form {
    protected $fields = array();

    protected $fields_id_mapping = array();

    protected $groups = array();

    function __construct( $options = array() ) {
        $this->options = new Form_Options( $options );

        $this->setup();
    }

    protected function setup() {

    }

    public function get_groups() {
        return $this->groups;
    }

    public function register_group( $group_id, $options = array() ) {
        $this->groups[$group_id] = $options;
    }

    public function register_groups( $groups ) {
        foreach ( $groups as $group_id => $group_options ) {
            if ( is_int( $group_id ) ) {
                $group_id = $group_options;
                $group_options = array();
            }

            $this->register_group( $group_id, $group_options );
        }
    }

    public function register_field( $field_options, $group = null ) {
        $field = null;
        $type = Array_Helper::get_value( $field_options, 'type' );

        if ( ! isset( $field_options['group'] ) && ! is_null( $group ) ) {
            $field_options['group'] = $group;
        }

        foreach ( $this->options->fields_namespace as $ns ) {
            $field = Object_Factory::get_object( $type, $ns, array( $field_options ) );

            if ( $field ) {
                $field->set_form( $this );
                break ;
            }
        }

        if ( $field ) {
            $field_id = $field->id;

            $this->fields[] = $field;
            $this->fields_id_mapping[$field_id] = $field;

            return true;
        }

        return false;
    }

    public function register_fields( $fields, $group = null ) {
        foreach ( $fields as $field ) {
            $this->register_field( $field, $group );
        }
    }

    public function bind( $val ) {
        if ( is_object( $val ) ) {
            $val = (array) $val;
        }

        if ( ! is_array( $val ) )
            return ;

        foreach ( $val as $k => $v ) {
            $field = $this->field_by_id( $k );

            if ( $field )
                $field->set_value( $v );
        }
    }

    public function field_by_id( $id ) {
        $field = null;

        if ( isset( $this->fields_id_mapping[$id] ) ) {
            $field = $this->fields_id_mapping[$id];
        }

        return $field;
    }

    public function fields_by_group( $group ) {
        $group_fields = array();

        foreach ( $this->fields as $field ) {
            if ( $field->group == $group ) {
                $group_fields[] = $field;
            }
        }

        return $group_fields;
    }

    public function output( $group = null, $override_options = array() ) {
        $output = array();
        $fields = $this->fields_by_group( $group );
        $options = isset( $this->groups[$group] ) ? $this->groups[$group] : array();

        if ( count( $override_options ) > 0 )
            $options = array_merge( $options, $override_options );

        if ( ! empty( $options['header'] ) ) {
            $output[] = sprintf(
                '<h3>%1$s</h3>',
                $options['header']
            );
        }

        if ( ! empty( $options['description'] ) ) {
            $output[] = sprintf(
                '<p>%1$s</p>',
                $options['description']
            );
        }

        if ( is_array( $fields ) && count( $fields) > 0 ) {
            $group_postfix = $group ? $group : 'default';
            $hidden = isset( $options['hidden'] ) ? $options['hidden'] : false;

            $output[] = '<table class="form-table params-' . $group_postfix . '"' . ( $hidden ? ' style="display:none;"' : '' ) . '>';
            $output[] = '<tbody>';

            foreach ( $fields as $field ) {
                $field_id = $field->get_id();
                $field_label = $field->get_label();
                $field_description = $field->get_description();

                $output[] = sprintf(
                    '<tr><th scope="row"><label for="%1$s" class="ari-form-tooltip" title="%4$s" data-tooltip="%4$s">%2$s</label></th><td>%3$s</td></tr>',
                    $field_id,
                    $field_label,
                    $field->output(),
                    htmlspecialchars( $field_description, ENT_COMPAT, 'UTF-8' )
                );
            }

            $output[] = '</tbody>';
            $output[] = '</table>';
        }

        return implode( $output );
    }

    public function groups_output( $groups ) {
        $output = '';

        foreach ( $groups as $key => $val ) {
            $group = $val;
            $options = array();
            if ( is_array( $val ) ) {
                $group = $key;
                $options = $val;
            }

            $output .= $this->output( $group, $options );
        }

        return $output;
    }

    public function render( $group = '', $options = array() ) {
        echo $this->output( $group, $options );
    }
}
