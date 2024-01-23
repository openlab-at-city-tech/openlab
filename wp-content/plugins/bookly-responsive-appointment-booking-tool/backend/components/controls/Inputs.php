<?php
namespace Bookly\Backend\Components\Controls;

use Bookly\Lib\Utils\Common;

class Inputs
{
    /**
     * Add hidden input with CSRF token.
     */
    public static function renderCsrf()
    {
        printf(
            '<input type="hidden" name="csrf_token" value="%s" />',
            esc_attr( Common::getCsrfToken() )
        );
    }

    /**
     * Render custom checkbox
     *
     * @param string $label
     * @param string $value
     * @param bool   $checked
     * @param array  $attrs
     */
    public static function renderCheckBox( $label, $value = null, $checked = null, $attrs = array() )
    {
        $attributes = array_merge( array(
            'id'      => array_key_exists( 'id', $attrs ) ? $attrs['id'] : 'bookly-ch-' . mt_rand( 0, PHP_INT_MAX ),
            'checked' => $checked ? 'checked' : null,
            'value'   => $value,
        ), $attrs );

        self::renderCustom( 'checkbox', $label, $attributes );
    }

    /**
     * Render radio group
     *
     * @param string $label
     * @param string $help
     * @param array  $radios
     * @param string $value
     * @param array  $attrs
     */
    public static function renderRadioGroup( $label, $help, array $radios, $value, $attrs = array() )
    {
        if ( empty( $radios ) ) {
            $radios = array(
                //value => data
                0 => array( 'title' => __( 'Disabled', 'bookly' ) ),
                1 => array( 'title' => __( 'Enabled', 'bookly' ) ),
            );
        }

        echo '<div class="form-group">';
        if ( $label ) {
            printf( '<label>%s</label>', esc_html( $label ) );
        }
        foreach ( $radios as $r_value => $r_data ) {
            $attributes = array_merge( array(
                'id'      => array_key_exists( 'id', $attrs ) ? $attrs['id'] : 'bookly-ra-' . mt_rand( 0, PHP_INT_MAX ),
                'checked' => $r_value == $value ? 'checked' : null,
                'value'   => $r_value,
            ), $attrs );

            self::renderCustom( 'radio', $r_data['title'], $attributes );
        }
        if ( $help ) {
            printf( '<small class="text-muted form-text">%s</small>', esc_html( $help ) );
        }
        echo '</div>';
    }

    /**
     * Render radio
     *
     * @param string $label
     * @param null   $value
     * @param null   $checked
     * @param array  $attrs
     */
    public static function renderRadio( $label, $value = null, $checked = null, $attrs = array() )
    {
        $attributes = array_merge( array(
            'id'      => array_key_exists( 'id', $attrs ) ? $attrs['id'] : 'bookly-ra-' . mt_rand( 0, PHP_INT_MAX ),
            'checked' => $checked ? 'checked' : null,
            'value'   => $value,
        ), $attrs );

        self::renderCustom( 'radio', $label, $attributes );
    }

    /**
     * Render custom input
     *
     * @param string $type
     * @param string $label
     * @param array  $attributes
     */
    private static function renderCustom( $type, $label, $attributes = array() )
    {
        $attrs_str = '';
        if ( array_key_exists( 'class', $attributes ) ) {
            $attributes['class'] = 'custom-control-input ' . $attributes['class'];
        } else {
            $attributes['class'] = 'custom-control-input';
        }
        $parent_class = '';
        if ( array_key_exists( 'parent-class', $attributes ) ) {
            $parent_class = $attributes['parent-class'];
            unset( $attributes['parent-class'] );
        }
        foreach ( $attributes as $n => $v ) {
            if( $v !== null ) {
                $attrs_str .= sprintf( ' %s="%s"', $n, esc_attr( $v ) );
            }
        }

        printf( '<div class="custom-control custom-%1$s %5$s"><input type="%1$s"%2$s><label class="custom-control-label" for="%3$s">%4$s</label></div>',
            $type, $attrs_str, $attributes['id'], esc_html( $label ), $parent_class
        );
    }
}