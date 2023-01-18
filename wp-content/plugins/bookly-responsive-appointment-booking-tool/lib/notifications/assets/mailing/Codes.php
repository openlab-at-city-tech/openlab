<?php
namespace Bookly\Lib\Notifications\Assets\Mailing;

use Bookly\Lib;
use Bookly\Lib\Notifications\Assets;


/**
 * Class Codes
 *
 * @package Bookly\Lib\Notifications\Assets\Mailing
 */
class Codes extends Assets\Base\Codes
{
    /** @var Lib\Entities\MailingQueue */
    public $queue_item;

    /**
     * @param Lib\Entities\MailingQueue $queue_item
     */
    public function __construct( $queue_item )
    {
        $this->queue_item = $queue_item;
    }

    /**
     * @param array $replace_codes
     * @return array
     */
    public function prepareReplaceCodes( $replace_codes )
    {
        $replace_codes['client_name'] = $this->queue_item->getName();
        $replace_codes['client_phone'] = $this->queue_item->getPhone();
        $full_name = explode( ' ', $this->queue_item->getName(), 2 );
        $replace_codes['client_first_name'] = $full_name[0];
        $replace_codes['client_last_name'] = isset ( $full_name[1] ) ? trim( $full_name[1] ) : '';

        return $replace_codes;
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );

        $replace_codes += $this->prepareReplaceCodes( $replace_codes );

        return $replace_codes;
    }
}