<?php
namespace Ari\Forms\Fields;

class Group_Switcher extends Checkbox {
    static protected $assets_loaded = false;

    protected $child_group;

    public function __set( $name, $value ) {
        switch ( $name ) {
            case 'child_group':
                $this->child_group = (string) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    public function __get( $name ) {
        switch ( $name ) {
            case 'child_group':
                return $this->$name;
                break;
        }

        return parent::__get( $name );
    }

    public function set_form( $form ) {
        parent::set_form( $form );

        $this->handle_group_visibility( ! $this->get_value() );
    }

    protected function load_assets() {
        if ( self::$assets_loaded )
            return ;

        wp_enqueue_script( 'ari-form-elements' );

        self::$assets_loaded = true;
    }

    public function set_value( $value ) {
        $value = (bool) $value;
        $this->handle_group_visibility( ! $value );

        parent::set_value( $value );
    }

    protected function handle_group_visibility( $hidden ) {
        $groups = $this->form->get_groups();
        $child_group_id = $this->child_group;

        foreach ( $groups as $group_id => $group_options ) {
            if ( $group_id === $child_group_id ) {
                $group_options['hidden'] = $hidden;
                $this->form->register_group( $group_id, $group_options );
                break;
            }
        }
    }

    public function output() {
        $this->load_assets();

        if ( $this->class )
            $this->class .= ' ';
        else
            $this->class = '';

        $this->class .= 'ari-group-switcher';
        $this->attributes = array(
            'data-child-group' => $this->child_group
        );

        return parent::output();
    }
}
