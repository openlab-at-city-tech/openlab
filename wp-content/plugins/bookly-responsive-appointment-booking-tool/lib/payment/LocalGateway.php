<?php
namespace Bookly\Lib\Payment;

use Bookly\Lib;

class LocalGateway extends Lib\Base\Gateway
{
    protected $type = Lib\Entities\Payment::TYPE_LOCAL;
    protected $on_site = true;

    /**
     * @inerhitDoc
     */
    protected function getCheckoutUrl( array $intent_data )
    {
        return $intent_data['target_url'];
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
            'target_url' => $this->getResponseUrl( self::EVENT_RETRIEVE ),
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