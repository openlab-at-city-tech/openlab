<?php
namespace Bookly\Backend\Components\Settings;

use Bookly\Lib;

/**
 * Class Image
 * @package Bookly\Backend\Components\Settings
 */
class Image extends Lib\Base\Component
{
    /**
     * Render media image attachment.
     *
     * @param string $option_name
     * @param string $class
     */
    public static function render( $option_name, $class = 'lg' )
    {
        $img = Lib\Utils\Common::getAttachmentUrl( get_option( $option_name ), 'full' );

        self::renderTemplate( 'image', array(
            'option_name' => $option_name,
            'option_value' => get_option( $option_name ),
            'class' => $class,
            'img_style' => $img ? 'background-image: url(' . $img . '); background-size: contain;' : '',
            'delete_style' => $img ? '' : 'display: none;',
        ) );
    }
}