<?php
namespace Bookly\Backend\Components\Settings;
use Bookly\Backend\Components\Controls;

/**
 * Class Selects
 * @package Bookly\Backend\Components\Settings
 */
class Selects
{
    /**
     * Render multiple select (checkbox group).
     *
     * @param string $option_name
     * @param string $label
     * @param string $help
     * @param array  $options
     */
    public static function renderMultiple( $option_name, $label = null, $help = null, array $options = array() )
    {
        $values  = (array) get_option( $option_name );
        $control = '';
        foreach ( $options as $i => $option ) {
            $control .= strtr(
                '<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="{id}"
                           name="{name}[]"
                           value="{value}"{checked}
                    />
                    <label class="custom-control-label" for="{id}">
                        {caption}
                    </label>
                </div>',
                array(
                    '{id}'      => "{$option_name}_{$i}",
                    '{name}'    => $option_name,
                    '{value}'   => esc_attr( $option[0] ),
                    '{checked}' => checked( in_array( $option[0], $values ), true, false ),
                    '{caption}' => esc_html( $option[1] ),
                )
            );
        }
        $control = "<div id=\"$option_name\">$control</div>";

        echo Inputs::buildControl( $option_name, $label, $help, $control );
    }

    /**
     * Render radios.
     *
     * @param string $option_name
     * @param string $label
     * @param array  $radios
     * @param string $help
     */
    public static function renderRadios( $option_name, $label = null, $help = null, array $radios = array() )
    {
        if ( empty ( $radios ) ) {
            $radios = array(
                //value => data
                0 => array( 'title' => __( 'Disabled', 'bookly' ) ),
                1 => array( 'title' => __( 'Enabled', 'bookly' ) ),
            );
        }

        Controls\Inputs::renderRadioGroup( $label, $help,
            $radios,
            get_option( $option_name ), array( 'name' => $option_name )
        );
    }

    /**
     * Render drop-down select.
     *
     * @param string $option_name
     * @param null $label
     * @param null $help
     * @param array $options
     * @param array $attributes
     */
    public static function renderSingle( $option_name, $label = null, $help = null, array $options = array(), $attributes = array() )
    {
        self::renderSingleValue( $option_name, get_option( $option_name ), $label, $help, $options, $attributes );
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $label
     * @param string $help
     * @param array $options
     * @param array $attributes
     * @return void
     */
    public static function renderSingleValue( $name, $value, $label, $help = null, $options = array(), $attributes = array() )
    {
        if ( empty ( $options ) ) {
            $options = array(
                //  value        title              disabled
                array( 0, __( 'Disabled', 'bookly' ), 0 ),
                array( 1, __( 'Enabled', 'bookly' ),  0 ),
            );
        }

        $options_str = '';
        foreach ( $options as $attr ) {
            $options_str .= strtr(
                '<option value="{value}"{attr}>{caption}</option>',
                array(
                    '{value}' => esc_attr( $attr[0] ),
                    '{attr}' => empty ( $attr[2] )
                        ? selected( $value, $attr[0], false )
                        : disabled( true, true, false ),
                    '{caption}' => esc_html( $attr[1] ),
                )
            );
        }

        $attributes['id'] = $name;
        $attributes['class'] = 'form-control custom-select';
        $attributes['name'] = $name;

        $attributes_str = '';
        foreach ( $attributes as $attr => $value ) {
            if ( $value !== null ) {
                $attributes_str .= sprintf( ' %s="%s"', $attr, esc_attr( $value ) );
            }
        }

        $control = strtr(
            '<select {attributes}>{options}</select>',
            array(
                '{attributes}' => $attributes_str,
                '{options}' => $options_str,
            )
        );

        echo Inputs::buildControl( $name, $label, $help, $control );
    }

    /**
     * Render drop-down select.
     *
     * @param $option_name
     * @param null $label
     * @param null $help
     * @param array $options
     * @param array $attributes
     */
    public static function renderSingleWithCategories( $option_name, $label = null, $help = null, array $options = array(), $attributes = array() )
    {
        $options_str = '';
        foreach ( $options as $option => $value ) {
            if ( is_array( $value ) ) {
                $options_str .= sprintf( '<optgroup label="%s">', esc_attr( $option ) );
                foreach ( $value as $option_value => $option_label ) {
                    $options_str .= strtr(
                        '<option value="{value}"{attr}>{caption}</option>',
                        array(
                            '{value}' => esc_attr( $option_value ),
                            '{attr}' => selected( get_option( $option_name ), $option_value, false ),
                            '{caption}' => esc_html( $option_label ),
                        )
                    );
                }
                $options_str .= '</optgroup>';
            } else {
                $options_str .= strtr(
                    '<option value="{value}"{attr}>{caption}</option>',
                    array(
                        '{value}' => esc_attr( $option ),
                        '{attr}' => selected( get_option( $option_name ), $option, false ),
                        '{caption}' => esc_html( $value ),
                    )
                );
            }
        }

        $attributes['id'] = $option_name;
        $attributes['class'] = 'form-control custom-select';
        $attributes['name'] = $option_name;

        $attributes_str = '';
        foreach ( $attributes as $attr => $value ) {
            if ( $value !== null ) {
                $attributes_str .= sprintf( ' %s="%s"', $attr, esc_attr( $value ) );
            }
        }

        $control = strtr(
            '<select {attributes}>{options}</select>',
            array(
                '{attributes}' => $attributes_str,
                '{options}' => $options_str,
            )
        );

        echo Inputs::buildControl( $option_name, $label, $help, $control );
    }
}