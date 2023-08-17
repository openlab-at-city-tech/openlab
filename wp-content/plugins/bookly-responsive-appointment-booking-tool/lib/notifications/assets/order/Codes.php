<?php
namespace Bookly\Lib\Notifications\Assets\Order;

use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Config;
use Bookly\Lib\Entities;
use Bookly\Lib\Notifications\Assets\Base;
use Bookly\Lib\Slots\DatePoint;
use Bookly\Lib\Utils;

/**
 * Class Codes
 *
 * @package Bookly\Lib\Notifications\Assets\Order
 */
class Codes extends Base\Codes
{
    // Core
    public $amount_due;
    public $amount_paid;
    public $client_address;
    public $client_email;
    public $client_first_name;
    public $client_last_name;
    public $client_name;
    public $client_phone;
    public $client_note;
    public $client_timezone;
    public $client_locale;
    public $client_birthday;
    public $client_full_birthday;
    public $deposit_value;
    public $invoice_number;     // payment_id
    public $payment_type;
    public $payment_status;
    public $total_price;
    public $total_tax;
    public $coupon;
    public $gift_card;
    // Invoices
    public $invoice_date;
    public $invoice_due_date;

    /** @var Order */
    protected $order;

    /**
     * Constructor.
     *
     * @param Order $order
     */
    public function __construct( Order $order )
    {
        $this->order = $order;

        $this->client_address = $order->getCustomer()->getAddress();
        $this->client_email = $order->getCustomer()->getEmail();
        $this->client_first_name = $order->getCustomer()->getFirstName();
        $this->client_last_name = $order->getCustomer()->getLastName();
        $this->client_name = $order->getCustomer()->getFullName();
        $this->client_phone = $order->getCustomer()->getPhone();
        $this->client_note = $order->getCustomer()->getNotes();
        if ( $order->hasPayment() ) {
            $this->amount_paid = $order->getPayment()->getPaid();
            $this->amount_due = $order->getPayment()->getTotal() - $order->getPayment()->getPaid();
            $this->total_price = $order->getPayment()->getTotal();
            $this->total_tax = $order->getPayment()->getTax();
            $this->invoice_number = $order->getPayment()->getId();
            $this->payment_status = $order->getPayment()->getStatus();
            $this->payment_type = $order->getPayment()->getType();
        }

        Proxy\Shared::prepareCodes( $this );
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );

        // Add replace codes.
        $replace_codes += array(
            'amount_due' => Utils\Price::format( $this->amount_due ),
            'amount_paid' => Utils\Price::format( $this->amount_paid ),
            'client_email' => $this->client_email,
            'client_address' => $format === 'html' ? nl2br( $this->client_address ) : $this->client_address,
            'client_name' => $this->client_name,
            'client_first_name' => $this->client_first_name,
            'client_last_name' => $this->client_last_name,
            'client_phone' => $this->client_phone,
            'client_timezone' => $this->client_timezone,
            'client_locale' => $this->client_locale,
            'client_note' => $this->client_note,
            'payment_type' => Entities\Payment::typeToString( $this->payment_type ),
            'payment_status' => Entities\Payment::statusToString( $this->payment_status ),
            'total_price' => Utils\Price::format( $this->total_price ),
            'total_tax' => Utils\Price::format( $this->total_tax ),
            'total_price_no_tax' => Utils\Price::format( $this->total_price - $this->total_tax ),
        );

        return Proxy\Shared::prepareReplaceCodes( $replace_codes, $this, $format );
    }

    /**
     * Apply client time zone to given datetime string in WP time zone.
     *
     * @param string $datetime
     * @param Item $item
     * @return string
     */
    public function applyItemTz( $datetime, Item $item )
    {
        if ( $datetime != '' ) {
            $time_zone = $item->getCA()->getTimeZone();
            $time_zone_offset = $item->getCA()->getTimeZoneOffset();

            if ( $time_zone !== null ) {
                $datetime = DatePoint::fromStrInTz( $datetime, Config::getWPTimeZone() );

                return date_format( date_timestamp_set( date_create( $time_zone ), $datetime->value()->getTimestamp() ), 'Y-m-d H:i:s' );
            } elseif ( $time_zone_offset !== null ) {
                return Utils\DateTime::applyTimeZoneOffset( $datetime, $time_zone_offset );
            }
        }

        return $datetime;
    }

    /**
     * Get order.
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}