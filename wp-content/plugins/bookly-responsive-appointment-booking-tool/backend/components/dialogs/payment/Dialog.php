<?php
namespace Bookly\Backend\Components\Dialogs\Payment;

use Bookly\Lib;
use Bookly\Lib\Entities\Payment;

class Dialog extends Lib\Base\Component
{
    /**
     * Render payment details dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/payment-details-dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );

        $types = array();
        foreach ( Payment::getTypes() as $type ) {
            $types[ $type ] = Payment::typeToString( $type );
        }

        $statuses = array();
        foreach ( array( Payment::STATUS_COMPLETED, Payment::STATUS_PENDING, Payment::STATUS_REJECTED, Payment::STATUS_REFUNDED, ) as $status ) {
            $statuses[ $status ] = Payment::statusToString( $status );
        }

        wp_localize_script( 'bookly-payment-details-dialog.js', 'BooklyL10nPaymentDetailsDialog', array(
            'types' => $types,
            'statuses' => $statuses,
            'moment_format_date' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
            'moment_format_time' => Lib\Utils\DateTime::convertFormat( 'time', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
            'format_price' => Lib\Utils\Price::formatOptions(),
            'can_edit' => (int) ( Lib\Utils\Common::isCurrentUserSupervisor() || Lib\Utils\Common::isCurrentUserStaff() ),
            'l10n' => array(
                'amount' => __( 'Amount', 'bookly' ),
                'apply' => __( 'Apply', 'bookly' ),
                'bind_payment' => __( 'Bind payment', 'bookly' ),
                'cancel' => __( 'Cancel', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
                'complete_payment' => __( 'Complete payment', 'bookly' ),
                'coupon_discount' => __( 'Coupon discount', 'bookly' ),
                'gift_card_discount' => __( 'Gift card discount', 'bookly' ),
                'customer' => __( 'Customer', 'bookly' ),
                'date' => __( 'Date', 'bookly' ),
                'deposit' => __( 'Deposit', 'bookly' ),
                'discount' => __( 'Discount', 'bookly' ),
                'due' => __( 'Due', 'bookly' ),
                'group_discount' => __( 'Group discount', 'bookly' ),
                'manual_adjustment' => __( 'Manual adjustment', 'bookly' ),
                'invoice_number' => __( 'Invoice number', 'bookly' ),
                'invoice_number_exists' => __( 'This invoice number is already in use', 'bookly' ),
                'na' => __( 'N/A', 'bookly' ),
                'paid' => __( 'Paid', 'bookly' ),
                'payment' => __( 'Payment', 'bookly' ),
                'payment_is_not_found' => __( 'Payment is not found.', 'bookly' ),
                'price' => __( 'Price', 'bookly' ),
                'price_correction' => __( 'Price correction', 'bookly' ),
                'provider' => __( 'Provider', 'bookly' ),
                'reason' => __( 'Reason', 'bookly' ),
                'item' => __( 'Item', 'bookly' ),
                'status' => __( 'Status', 'bookly' ),
                'subtotal' => __( 'Subtotal', 'bookly' ),
                'tax' => __( 'Tax', 'bookly' ),
                'tips' => __( 'Tips', 'bookly' ),
                'total' => __( 'Total', 'bookly' ),
                'type' => __( 'Type', 'bookly' ),
                'wc_order_id' => __( 'order ID', 'bookly' ),
                'refund' => __( 'Refund', 'bookly' ),
                'are_you_sure_want_refund' => __( 'Are you sure you want to approve refund? This action cannot be undone.', 'bookly' )
            ),
        ) );
    }
}