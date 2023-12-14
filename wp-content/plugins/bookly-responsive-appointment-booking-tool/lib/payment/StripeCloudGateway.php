<?php
namespace Bookly\Lib\Payment;

use Bookly\Lib;
use Bookly\Lib\Entities\Payment;

class StripeCloudGateway extends Lib\Base\Gateway
{
    protected $type = Payment::TYPE_CLOUD_STRIPE;

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
        $metadata = array();
        if ( get_option( 'bookly_cloud_stripe_custom_metadata' ) ) {
            foreach ( get_option( 'bookly_cloud_stripe_metadata', array() ) as $meta ) {
                $metadata[ preg_replace( '/[^ \w]+/', '', $meta['name'] ) ] = $meta['value'];
            }
        }
        $metadata['payment_id'] = $this->getPayment()->getId();

        return $metadata;
    }

    /**
     * @inerhitDoc
     */
    protected function createGatewayIntent()
    {
        $api = Lib\Cloud\API::getInstance();
        $response = $api->stripe
            ->createSession(
                array(
                    'total' => $this->getGatewayAmount(),
                    'description' => $this->request->getUserData()->cart->getItemsTitle(),
                    'customer_email' => $this->request->getUserData()->getEmail(),
                    'metadata' => $this->getMetaData(),
                ),
                $this->getResponseUrl( self::EVENT_RETRIEVE ),
                $this->getResponseUrl( self::EVENT_CANCEL )
            );
        if ( $response ) {
            return array(
                'ref_id' => $response['payment_intent'],
                'target_url' => $response['redirect_url'],
            );
        }

        throw new \Exception( current( $api->getErrors() ) );
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function retrieveStatus()
    {
        $payment_intent = $this->payment->getRefId();
        $data = Lib\Cloud\API::getInstance()->stripe->retrievePaymentIntent( $payment_intent );
        if ( ( $data['status'] !== 'canceled' )
            && strtoupper( $data['currency'] ) == Lib\Config::getCurrency()
        ) {
            $paid = $this->payment->getPaid();
            if ( ! Lib\Config::isZeroDecimalsCurrency() ) {
                $paid *= 100;
            }
            if ( (int) $paid == $data['amount'] ) {
                $pi_status = $data['status'];
                $good_statuses = array(
                    'succeeded' => self::STATUS_COMPLETED,
                    'processing' => self::STATUS_PROCESSING,
                );
                if ( array_key_exists( $pi_status, $good_statuses ) ) {
                    return $good_statuses[ $pi_status ];
                }
            }
        }

        return self::STATUS_FAILED;
    }
}