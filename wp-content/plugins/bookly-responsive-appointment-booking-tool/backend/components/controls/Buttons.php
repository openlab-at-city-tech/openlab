<?php
namespace Bookly\Backend\Components\Controls;

class Buttons
{
    /**
     * Render button.
     *
     * @param string $id
     * @param string $class
     * @param string $caption
     * @param array  $attrs
     * @param string $caption_template
     * @param string $icon
     * @param bool $responsive
     */
    public static function render( $id = null, $class = null, $caption = null, array $attrs = array(), $caption_template = '{caption}', $icon = '', $responsive = false )
    {
        echo self::_createButton( 'button', $id, $class, null, $attrs, $caption, $caption_template, $icon, $responsive );
    }

    /**
     * Render default.
     *
     * @param string $id
     * @param string $extra_class
     * @param string $caption
     * @param array  $attrs
     * @param bool $ellipsis
     */
    public static function renderDefault( $id = null, $extra_class = null, $caption = null, array $attrs = array(), $ellipsis = false )
    {
        echo self::_createButton(
            'button',
            $id,
            'btn-default',
            $extra_class,
            $attrs,
            $caption,
            '{caption}' . ( $ellipsis ? '…' : '' )
        );
    }

    /**
     * Render Add button.
     *
     * @param string $id
     * @param string $extra_class
     * @param string $caption
     * @param array  $attrs
     * @param bool $ellipsis
     */
    public static function renderAdd( $id = 'bookly-add', $extra_class = null, $caption = null, array $attrs = array(), $ellipsis = true )
    {
        echo self::_createButton(
            'button',
            $id,
            'btn-success',
            $extra_class,
            $attrs,
            $caption !== null ? $caption : __( 'Add', 'bookly' ),
            '{caption}' . ( $ellipsis ? '…' : '' ),
            '<i class="fas fa-fw fa-plus mr-lg-1"></i>',
            true
        );
    }

    /**
     * Render delete button.
     *
     * @param string $id
     * @param string $extra_class
     * @param string $caption
     * @param array  $attrs
     * @param bool $ellipsis
     */
    public static function renderDelete( $id = 'bookly-delete', $extra_class = null, $caption = null, array $attrs = array(), $ellipsis = true )
    {
        echo self::_createButton(
            'button',
            $id,
            'btn-danger',
            $extra_class,
            $attrs,
            $caption !== null ? $caption : __( 'Delete', 'bookly' ),
            '{caption}' . ( $ellipsis ? '…' : '' ),
            '<i class="far fa-fw fa-trash-alt mr-lg-1"></i>',
            true
        );
    }

    /**
     * Render reset button.
     *
     * @param string $id
     * @param string $extra_class
     * @param string $caption
     * @param array  $attrs
     * @param bool $ellipsis
     */
    public static function renderReset( $id = null, $extra_class = null, $caption = null, array $attrs = array(), $ellipsis = false )
    {
        echo self::_createButton(
            'reset',
            $id,
            'btn-default',
            $extra_class,
            $attrs,
            $caption !== null ? $caption : __( 'Reset', 'bookly' ),
            '{caption}' . ( $ellipsis ? '…' : '' )
        );
    }

    /**
     * Render cancel button.
     *
     * @param null  $caption
     * @param array $attrs
     * @param bool  $ellipsis
     */
    public static function renderCancel( $caption = null, array $attrs = array(), $ellipsis = false )
    {
        $attrs += array( 'data-dismiss' => 'bookly-modal' );
        echo self::_createButton(
            'button',
            null,
            'btn-default',
            '',
            $attrs,
            $caption ?: __( 'Cancel', 'bookly' ),
            '{caption}' . ( $ellipsis ? '…' : '' )
        );
    }

    /**
     * Render submit button.
     *
     * @param string $id
     * @param string $extra_class
     * @param string $caption
     * @param array  $attrs
     * @param bool $ellipsis
     */
    public static function renderSubmit( $id = 'bookly-save', $extra_class = null, $caption = null, array $attrs = array(), $ellipsis = false )
    {
        echo self::_createButton(
            'submit',
            $id,
            'btn-success',
            $extra_class,
            $attrs,
            $caption !== null ? $caption : __( 'Save', 'bookly' ),
            '{caption}' . ( $ellipsis ? '…' : '' )
        );
    }

    /**
     * Create button.
     *
     * @param string $type
     * @param string $id
     * @param string $class
     * @param string $extra_class
     * @param array $attrs
     * @param string $caption
     * @param string $caption_template
     * @param string $icon
     * @param bool $responsive
     * @return string
     */
    private static function _createButton( $type, $id, $class, $extra_class, array $attrs, $caption, $caption_template, $icon = '', $responsive = false )
    {
        $attrs['id'] = $id;
        $attrs['class'] = implode( ' ', array_filter( array( 'btn ladda-button', $class, $extra_class ) ) );
        $attrs['data-spinner-size'] = '40';
        $attrs['data-style'] = 'zoom-in';

        $attrs_str = '';
        foreach ( $attrs as $attr => $value ) {
            if ( $value !== null ) {
                $attrs_str .= sprintf( ' %s="%s"', $attr, esc_attr( $value ) );
            }
        }

        return strtr(
            '<button type="{type}" title="{caption}" {attributes}><span class="ladda-label">{icon}{responsive_start}{caption_template}{responsive_end}</span></button>',
            array(
                '{type}' => $type,
                '{attributes}' => $attrs_str,
                '{caption}' => $caption,
                '{icon}' => $icon,
                '{caption_template}' => strtr( $caption_template, array( '{caption}' => esc_html( $caption ) ) ),
                '{responsive_start}' => $responsive ? '<span class="d-none d-lg-inline">' : '',
                '{responsive_end}' => $responsive ? '</span>' : '',
            )
        );
    }
}