<?php
namespace Bookly\Lib\Payment;

use Bookly\Lib;
use Bookly\Lib\Entities\Payment;

class ZeroGateway extends Lib\Base\Gateway
{
    protected $type = Payment::TYPE_FREE;
    protected $on_site = true;

    /**
     * @inerhitDoc
     */
    protected function getCheckoutUrl( array $intent_data )
    {
        return $this->request->isBookingForm()
            ? $intent_data['target_url']
            : null;
    }

    /**
     * @inerhitDoc
     */
    protected function getInternalMetaData()
    {
        return array();
    }

    /**
     * @inerhitDoc
     */
    protected function createGatewayIntent()
    {
        return array(
            'target_url' => $this->getResponseUrl( self::EVENT_RETRIEVE )
        );
    }

    /**
     * @inerhitDoc
     */
    public function retrieveStatus()
    {
        return self::STATUS_COMPLETED;
    }
}