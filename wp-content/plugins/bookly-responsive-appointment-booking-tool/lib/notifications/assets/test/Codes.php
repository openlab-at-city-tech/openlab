<?php
namespace Bookly\Lib\Notifications\Assets\Test;

use Bookly\Lib;
use Bookly\Lib\DataHolders;
use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Simple;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Entities;
use Bookly\Lib\Notifications\Assets;
use Bookly\Lib\Utils;

/**
 * Class Codes
 * @package Bookly\Lib\Notifications\Assets\Test
 */
class Codes extends Assets\Item\Codes
{
    public $agenda_date;
    public $cart_info;
    public $new_password;
    public $new_username;
    public $next_day_agenda;
    public $next_day_agenda_extended;

    /**
     * @inheritDoc
     */
    public function __construct( $order = null )
    {
        if ( $order && $order->getCustomer() ) {
            $customer = $order->getCustomer();
        } else {
            $customer = new Entities\Customer();
            $customer
                ->setPhone( '12345678' )
                ->setEmail( 'client@example.com' )
                ->setNotes( 'Client notes' )
                ->setFullName( 'Client Name' )
                ->setFirstName( 'Client First Name' )
                ->setLastName( 'Client Last Name' )
                ->setBirthday( '2000-01-01' )
                ->setCity( 'City' )
                ->setCountry( 'Country' )
                ->setPostcode( 'Post code' )
                ->setState( 'State' )
                ->setStreet( 'Street' )
                ->setAdditionalAddress( 'Addition address' );
        }

        parent::__construct( $order ?: new Order( $customer ) );

        $this->item = Simple::create( new Entities\CustomerAppointment( array( 'token' => '2000200020002000200020002' ) ) );

        $start_date  = date_create( '-1 month' );
        $event_start = $start_date->format( 'Y-m-d 12:00:00' );
        $event_end = $start_date->format( 'Y-m-d 13:00:00' );
        $cart_info = array( array(
            'service_name'      => 'Service Name',
            'appointment_start' => $event_start,
            'staff_name'        => 'Staff Name',
            'appointment_price' => 24,
            'cancel_url'        => '#',
            'appointment_start_info' => null,
            'deposit'           => Lib\Proxy\DepositPayments::formatDeposit( 12, '50%' )
        ) );
        $schedule = array(
            array(
                'start' => $start_date->format( 'Y-m-d 12:00:00' ),
                'token' => '3000300030003000300030003',
                'duration' => '3600'
            ),
            array(
                'start' => $start_date->modify( '1 day' )->format( 'Y-m-d 14:00:00' ),
                'token' => '4000400040004000400040004',
                'duration' => '3600'
            ),
            array(
                'start' => $start_date->modify( '1 day' )->format( 'Y-m-d 12:00:00' ),
                'token' => '5000500050005000500050005',
                'duration' => '3600'
            ),
        );
        $this->series_token             = '1000100010001000100010001';
        $this->agenda_date              = Utils\DateTime::formatDate( current_time( 'mysql' ) );
        $this->amount_due               = '';
        $this->amount_paid              = '';
        $this->appointment_end          = $event_end;
        $this->appointment_id           = '1';
        $this->appointment_start        = $event_start;
        $this->appointment_token        = '';
        $this->booking_number           = '1';
        $this->cancellation_reason      = 'Some Reason';
        $this->cart_info                = $cart_info;
        $this->category_image           = 'https://dummyimage.com/100/cccccc/000000';
        $this->category_info            = 'Category info text';
        $this->category_name            = 'Category Name';
        $this->client_timezone          = 'UTC';
        $this->extras                   = 'Extras 1, Extras 2';
        $this->extras_total_price       = '4';
        $this->new_password             = 'New Password';
        $this->new_username             = 'New User';
        $this->next_day_agenda          = '';
        $this->next_day_agenda_extended = '';
        $this->number_of_persons        = '1';
        $this->payment_type             = Entities\Payment::typeToString( Entities\Payment::TYPE_LOCAL );
        $this->service_duration         = '3600';
        $this->service_info             = 'Service info text';
        $this->service_name             = 'Service Name';
        $this->service_image            = 'https://dummyimage.com/100/dddddd/000000';
        $this->service_price            = '10';
        $this->schedule                 = $schedule;
        $this->staff_email              = 'staff@example.com';
        $this->staff_info               = 'Staff info text';
        $this->staff_name               = 'Staff Name';
        $this->staff_phone              = '23456789';
        $this->staff_photo              = 'https://dummyimage.com/100/dddddd/000000';
        $this->staff_category_image     = 'https://dummyimage.com/100/666666/000000';
        $this->staff_category_info      = 'Staff Category info text';
        $this->staff_category_name      = 'Staff Category Name';
        $this->total_price              = '24';
        $this->client_birthday          = date_i18n( 'F j', strtotime( $customer->getBirthday() ) );

        Proxy\Shared::prepareCodes( $this );
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );
        $replace_codes['verification_code'] = 123456;

        return Proxy\Shared::prepareReplaceCodes( $replace_codes, $this, $format );
    }

    /**
     * @inheritDoc
     */
    public function prepareForItem( Item $item, $recipient )
    {
        // Do nothing.
    }
}