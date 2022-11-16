<?php
namespace Bookly\Lib\Notifications\Assets\Base;

use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Utils\Common;

/**
 * Class Attachments
 * @package Bookly\Lib\Notifications\Assets\Base
 */
abstract class Attachments
{
    /** @var array */
    protected $files = array();

    /**
     * Create attachment files.
     *
     * @param Notification $notification
     * @param string $recipient
     * @return array
     */
    abstract public function createFor( Notification $notification, $recipient );

    /**
     * Remove attachment files.
     */
    public function clear()
    {
        $fs = Common::getFilesystem();
        foreach ( $this->files as $file ) {
            $fs->delete( $file, false, 'f' );
        }

        $this->files = array();
    }
}