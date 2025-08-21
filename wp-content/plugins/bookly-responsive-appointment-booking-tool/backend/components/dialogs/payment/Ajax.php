<?php
namespace Bookly\Backend\Components\Dialogs\Payment;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array(
            'completePayment' => array( 'staff', 'supervisor' ),
            'getPaymentDetails' => array( 'customer' ),
            'getPaymentInfo' => array( 'staff', 'supervisor' ),
            'refundPayment' => array( 'staff', 'supervisor' ),
        );
    }

    /**
     * Get payment details.
     */
    public static function getPaymentDetails()
    {
        $payment = new Lib\Entities\Payment();
        $payment->loadBy( array( 'id' => self::parameter( 'payment_id' ) ) );
        
        $payment = $payment->getId() ? $payment : false;
        if ( $payment && ! Lib\Utils\Common::isCurrentUserSupervisor() && ! Lib\Utils\Common::isCurrentUserStaff() ) {
            // Check if customer trying to get his own payment.
            $customer = Lib\Entities\Customer::query( 'c' )
                ->select( 'c.wp_user_id' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.customer_id = c.id' )
                ->where( 'ca.payment_id', $payment->getId() )
                ->fetchCol( 'wp_user_id' );
            if ( ! $customer || $customer[0] != get_current_user_id() ) {
                $payment = false;
            }
        }
        if ( $payment ) {
            $data = $payment->getPaymentData();
            $show_deposit = false;
            if ( $payment->getPaidType() === Lib\Entities\Payment::PAY_DEPOSIT ) {
                foreach ( $data['payment']['items'] as $item ) {
                    if ( array_key_exists( 'deposit_format', $item ) && $item['deposit_format'] ) {
                        $show_deposit = true;
                        break;
                    }
                }
            }

            switch ( $data['payment']['type'] ) {
                case Lib\Entities\Payment::TYPE_PAYPAL:
                    $price_correction = get_option( 'bookly_paypal_increase' ) != 0
                        || get_option( 'bookly_paypal_addition' ) != 0;
                    break;
                case Lib\Entities\Payment::TYPE_CLOUD_STRIPE:
                    $price_correction = get_option( 'bookly_cloud_stripe_increase' ) != 0
                        || get_option( 'bookly_cloud_stripe_addition' ) != 0;
                    break;
                default:
                    $price_correction = Lib\Payment\Proxy\Shared::paymentSpecificPriceExists( $data['payment']['type'] ) === true;
                    break;
            }

            $data['show'] = array(
                'coupons' => isset( $data['payment']['coupon'] ) && ( $data['payment']['coupon']['discount'] > 0 || $data['payment']['coupon']['deduction'] > 0 ),
                'customer_groups' => Lib\Config::customerGroupsActive() && isset( $data['group_discount'] ),
                'deposit' => $show_deposit,
                'price_correction' => $price_correction,
                'taxes' => Lib\Config::taxesActive() || $data['payment']['tax_total'] > 0,
            );

            $data['payment']['created_at'] = Lib\Utils\DateTime::applyStaffTimeZone( $data['payment']['created_at'] );
            if ( isset( $data['payment']['gateway_ref_id'] )
                && $payment->getType() === Lib\Entities\Payment::TYPE_WOOCOMMERCE
                && current_user_can( 'edit_post', $data['payment']['gateway_ref_id'] )
            ) {
                $data['payment']['order_link'] = admin_url( 'post.php?action=edit&post=' . $data['payment']['gateway_ref_id'] );
            }

            $refundable = false;
            if ( $payment->getStatus() === Lib\Entities\Payment::STATUS_COMPLETED && $payment->getRefId() ) {
                switch ( $payment->getType() ) {
                    case Lib\Entities\Payment::TYPE_CLOUD_STRIPE:
                        $refundable = Lib\Cloud\API::getInstance()->account->productActive( Lib\Cloud\Account::PRODUCT_STRIPE );
                        break;
                    default:
                        $refundable = Lib\Payment\Proxy\Shared::getGatewayForRefund( null, $payment ) !== null;
                        break;
                }
            }
            $data['refundable'] = $refundable;

            foreach ( $data['payment']['items'] as &$item ) {
                if ( isset( $item['units'], $item['duration'] ) && $item['units'] > 1 ) {
                    $item['service_name'] .= ' (' . Lib\Utils\DateTime::secondsToInterval( $item['duration'] ) . ')';
                }
                $item['appointment_date'] = isset( $item['appointment_date'] ) ? Lib\Utils\DateTime::applyStaffTimeZone( $item['appointment_date'] ) : '';
            }

            wp_send_json_success( $data );
        }

        wp_send_json_error();
    }

    /**
     * Complete payment.
     */
    public static function completePayment()
    {
        $payment = Lib\Entities\Payment::find( self::parameter( 'payment_id' ) );
        $details = $payment->getDetailsData();
        $details->setData(
            array( 'tax_paid' => $payment->getTax() )
        );
        $payment
            ->setPaid( $payment->getTotal() )
            ->setStatus( Lib\Entities\Payment::STATUS_COMPLETED )
            ->save();

        $payment_title = Lib\Utils\Price::format( $payment->getPaid() );
        if ( $payment->getPaid() != $payment->getTotal() ) {
            $payment_title = sprintf( __( '%s of %s', 'bookly' ), $payment_title, Lib\Utils\Price::format( $payment->getTotal() ) );
        }
        $payment_title .= sprintf(
            ' %s <span%s>%s</span>',
            Lib\Entities\Payment::typeToString( $payment->getType() ),
            $payment->getStatus() == Lib\Entities\Payment::STATUS_PENDING ? ' class="text-danger"' : '',
            Lib\Entities\Payment::statusToString( $payment->getStatus() )
        );

        list( $sync ) = Lib\Config::syncCalendars();
        if ( $sync ) {
            $appointments = Lib\Entities\Appointment::query( 'a' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                ->whereNot( 'a.start_date', null )
                ->where( 'ca.payment_id', $payment->getId() )
                ->find();
            foreach ( $appointments as $appointment ) {
                Lib\Utils\Common::syncWithCalendars( $appointment );
            }
        }

        wp_send_json_success( array( 'payment_title' => $payment_title ) );
    }

    /**
     * Refund payment.
     */
    public static function refundPayment()
    {
        $payment = Lib\Entities\Payment::find( self::parameter( 'payment_id' ) );
        if ( $payment->getStatus() != Lib\Entities\Payment::STATUS_REFUNDED ) {
            try {
                self::getGatewayForRefund( $payment )->refund();
            } catch ( \Exception $e ) {
                wp_send_json_error( array( 'message' => $e->getMessage() ) );
            }
        }

        $payment_title = Lib\Utils\Price::format( $payment->getPaid() );
        if ( $payment->getPaid() != $payment->getTotal() ) {
            $payment_title = sprintf( __( '%s of %s', 'bookly' ), $payment_title, Lib\Utils\Price::format( $payment->getTotal() ) );
        }
        $payment_title .= sprintf(
            ' %s <span%s>%s</span>',
            Lib\Entities\Payment::typeToString( $payment->getType() ),
            $payment->getStatus() == Lib\Entities\Payment::STATUS_PENDING ? ' class="text-danger"' : '',
            Lib\Entities\Payment::statusToString( $payment->getStatus() )
        );

        wp_send_json_success( compact( 'payment_title' ) );
    }

    /**
     * @param Lib\Entities\Payment $payment
     * @return Lib\Base\Gateway
     * @throws \Exception
     */
    protected static function getGatewayForRefund( Lib\Entities\Payment $payment )
    {
        $gateway = null;
        switch ( $payment->getType() ) {
            case Lib\Entities\Payment::TYPE_CLOUD_STRIPE:
                $gateway = new Lib\Payment\StripeCloudGateway( \Bookly\Frontend\Modules\Payment\Request::getInstance() );
                break;
            default:
                $gateway = Lib\Payment\Proxy\Shared::getGatewayForRefund( $gateway, $payment );
                break;
        }

        if ( $gateway ) {
            return $gateway->setPayment( $payment );
        }

        throw new \Exception( __( 'Unsupported action', 'bookly' ) );
    }

    /**
     * Get payment info
     */
    public static function getPaymentInfo()
    {
        $payment = null;
        if ( self::hasParameter( 'payment_id' ) ) {
            $payment = Lib\Entities\Payment::find( self::parameter( 'payment_id' ) );
            if ( ! $payment ) {
                wp_send_json_error( array( 'message' => __( 'Payment is not found.', 'bookly' ) ) );
            }
            $paid = $payment->getPaid();
            $total = $payment->getTotal();
            $type = $payment->getType();
            $status = $payment->getStatus();
        } else {
            $paid = self::parameter( 'paid' );
            $total = self::parameter( 'total' );
            $type = self::parameter( 'type' );
            $status = self::parameter( 'status' );
        }

        update_user_meta( get_current_user_id(), 'bookly_attach_payment_for', self::parameter( 'for' ) );
        $payment_info = array(
            'payment_title' => Lib\Entities\Payment::paymentInfo( $paid, $total, $type, $status ),
            'payment_type' => $paid == $total ? 'full' : 'partial',
        );

        if ( ! $payment ) {
            $payment_info = Proxy\Shared::preparePaymentInfo( $payment_info, $total );
        }

        wp_send_json_success( $payment_info );
    }
}