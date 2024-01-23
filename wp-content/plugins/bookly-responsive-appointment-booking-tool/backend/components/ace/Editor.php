<?php
namespace Bookly\Backend\Components\Ace;

use Bookly\Lib;

class Editor extends Lib\Base\Component
{
    /**
     * Render the editor
     *
     * @param string $doc_slug
     * @param string $id
     * @param string $codes
     * @param string $value
     */
    public static function render( $doc_slug, $id = 'bookly-ace-editor', $codes = '', $value = '', $additional_classes = null )
    {
        self::enqueueStyles( array(
            'module' => array( 'css/ace.css', ),
        ) );

        self::enqueueScripts( array(
            'module' => array(
                'js/ace.js' => array(),
                'js/ext-language_tools.js' => array(),
                'js/mode-bookly.js' => array(),
                'js/editor.js' => array(),
            ),
        ) );

        self::renderTemplate( 'editor', compact( 'id', 'codes', 'value', 'doc_slug', 'additional_classes' ) );
    }
}