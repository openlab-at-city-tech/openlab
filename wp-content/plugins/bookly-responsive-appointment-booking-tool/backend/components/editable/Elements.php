<?php
namespace Bookly\Backend\Components\Editable;

use Bookly\Lib;

class Elements extends Lib\Base\Component
{
    /**
     * Render editable string (single line).
     *
     * @param array $options
     * @param string $title
     * @param bool $echo
     * @return string
     */
    public static function renderString( array $options, $title = '', $echo = true )
    {
        $attrs = $title !== '' ? array( 'data-title' => $title ) : array();

        return self::_renderEditable( $options, 'span', '', $attrs, $echo );
    }

    /**
     * Render editable label.
     *
     * @param array $options
     * @param string $title
     * @param bool $echo
     * @return string
     */
    public static function renderLabel( array $options, $title = '', $echo = true )
    {
        $attrs = $title !== '' ? array( 'data-title' => $title ) : array();

        return self::_renderEditable( $options, 'label', '', $attrs, $echo );
    }

    /**
     * Render editable text (multi-line).
     *
     * @param string $option_name
     * @param string $codes
     * @param string $placement
     * @param string $title
     */
    public static function renderText( $option_name, $codes = null, $placement = 'bottom', $title = '', $permanent_title = false )
    {
        $option_value = get_option( $option_name );

        printf(
            '<span class="bookly-editable bookly-js-editable %s text-pre-wrap %s" data-type="%s" data-fieldType="textarea" data-values="%s" data-codes="%s" data-title="%s" data-placement="%s" data-option="%s">%s</span>',
            $option_name,
            $permanent_title === false ? '' : 'bookly-js-permanent-title',
            $codes === null ? 'popover' : 'ace',
            esc_attr( json_encode( array( $option_name => $option_value ?: '' ) ) ),
            esc_attr( $codes ),
            esc_attr( $title ),
            $placement,
            $option_name,
            esc_html( $permanent_title === false ? $option_value : $permanent_title )
        );
    }

    /**
     * Render editable number.
     *
     * @param string $option_name
     * @param int    $min
     * @param int    $step
     */
    public static function renderNumber( $option_name, $min = 1, $step = 1 )
    {
        $option_value = get_option( $option_name );

        printf( '<span class="bookly-editable bookly-js-editable %s text-pre-wrap" data-fieldType="number" data-values="%s" data-min="%s" data-step="%s" data-option="%s">%s</span>',
            $option_name,
            esc_attr( json_encode( array( $option_name => $option_value ) ) ),
            esc_attr( $min ),
            esc_attr( $step ),
            $option_name,
            esc_html( $option_value )
        );
    }

    /**
     * Render editable element.
     *
     * @param array $options
     * @param string $tag
     * @param string $class
     * @param array $attrs
     * @param bool $echo
     * @return string|void
     */
    protected static function _renderEditable( array $options, $tag, $class = '', $attrs = array(), $echo = true )
    {
        $data = array();
        foreach ( $options as $option_name ) {
            $data[ $option_name ] = get_option( $option_name );
        }

        $main_option = $options[0];
        $data_values = esc_attr( json_encode( $data ) );
        $content     = esc_html( $data[ $options[0] ] );
        $attributes  = '';

        if ( $class !== '' ) {
            $class = " $class";
        }

        foreach ( $attrs as $attr => $value ) {
            $attributes .= sprintf( ' %s="%s"', $attr, esc_attr( $value ) );
        }

        $template = '<{tag} class="bookly-editable bookly-js-editable{class}" data-values="{data-values}" data-option="{data-option}"{attributes}>{content}</{tag}>';
        $html = strtr( $template, array(
            '{tag}'         => $tag,
            '{class}'       => $class,
            '{data-values}' => $data_values,
            '{data-option}' => $main_option,
            '{attributes}'  => $attributes,
            '{content}'     => $content,
        ) );

        if ( ! $echo ) {
            return $html;
        }

        echo Lib\Utils\Common::stripScripts( $html );
    }

    /**
     * Render modals and enqueue scripts
     *
     * @param string $doc_slug
     */
    public static function renderModals( $doc_slug )
    {
        self::enqueueStyles( array(
            'module' => array( 'css/editable.css', ),
        ) );

        self::enqueueScripts( array(
            'module' => array(
                'js/editable.js' => array( 'jquery' ),
                'js/type.popover.js' => array( 'bookly-editable.js' ),
                'js/type.ace.js' => array( 'bookly-editable.js' ),
            ),
        ) );

        wp_localize_script( 'bookly-editable.js', 'BooklyL10nEditable', array(
            'edit' => esc_html__( 'Edit', 'bookly' ),
            'empty' => esc_html__( 'Empty', 'bookly' ),
            'enter_a_content' => esc_html__( 'Enter a content', 'bookly' ),
            'script_is_used' => esc_html__( 'WARNING', 'bookly' ) . "\n\n" . esc_html__( 'Using <script> tag can be dangerous.', 'bookly' ) . "\n\n" . esc_html__( 'Unless you understand exactly what you are doing, click Cancel and stay safe.', 'bookly' )
        ) );

        self::renderTemplate( 'ace-modal', compact( 'doc_slug' ) );

        Proxy\Shared::renderModals();
    }
}