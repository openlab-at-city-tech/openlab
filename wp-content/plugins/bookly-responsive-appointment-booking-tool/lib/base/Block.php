<?php
namespace Bookly\Lib\Base;

use Bookly\Lib;

abstract class Block extends Component
{
    /**
     * Init gutenberg block.
     */
    public static function init()
    {
        /** @var static $class */
        $class = get_called_class();
        add_action( 'enqueue_block_editor_assets', function () use ( $class ) {
            $class::registerBlockType();
        } );
    }

    /**
     * Register block for gutenberg
     */
    public static function registerBlockType()
    {

    }

}