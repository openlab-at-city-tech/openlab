<?php
namespace Bookly\Lib\Payment;

use Bookly\Lib;

class NullGateway extends Lib\Base\Gateway
{
    protected $type = null;
    protected $on_site = true;

    /**
     * @inerhitDoc
     */
    protected function getCheckoutUrl( array $intent_data )
    {
        return '';
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
        return array();
    }

    /**
     * @inerhitDoc
     */
    public function retrieveStatus()
    {
        /**
         * Check for NullGateway availability.
         * If NullGateway should not be available, then we are in a state where the webhook
         * has cancelled the payment faster than we returned from the payment system page.
         * Because the payment has been deleted, there is no information about which payment system was used,
         * therefore the payment has failed.
         */
        return ( Lib\Config::paymentStepDisabled() || \Bookly\Frontend\Modules\Booking\Proxy\CustomerGroups::getSkipPayment( $this->request->getUserData()->getCustomer() ) )
            ? self::STATUS_COMPLETED
            : self::STATUS_FAILED;
    }
}