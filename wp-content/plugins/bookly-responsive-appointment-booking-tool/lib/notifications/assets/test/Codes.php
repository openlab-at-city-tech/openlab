<?php
namespace Bookly\Lib\Notifications\Assets\Test;

use Bookly\Lib;
use Bookly\Lib\Config;
use Bookly\Lib\DataHolders;
use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Simple;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Entities;
use Bookly\Lib\Notifications\Assets;
use Bookly\Lib\Utils;

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

        $order = $order ?: new Order( $customer );

        $appointment = new Entities\Appointment();
        $ca = new Entities\CustomerAppointment();
        $service = new Entities\Service();
        $staff = new Entities\Staff();
        $this->item = Simple::create( $ca );
        $this->item
            ->setAppointment( $appointment )
            ->setService( $service )
            ->setStaff( $staff );

        $service
            ->setId( 1 )
            ->setTitle( 'Service Name' )
            ->setInfo( 'Service info text' )
            ->setPrice( 10 )
            ->setDuration( 3600 )
            ->setStartTimeInfo( null )
            ->setDeposit( '50%' );

        $staff
            ->setId( 1 )
            ->setFullName( 'Staff Name' )
            ->setEmail( 'staff@example.com' )
            ->setPhone( '23456789' )
            ->setInfo( 'Staff info text' );

        $start_date  = date_create( '-1 month' );
        $appointment
            ->setId( 1 )
            ->setStartDate( $start_date->format( 'Y-m-d 12:00:00' ) )
            ->setEndDate( $start_date->format( 'Y-m-d 13:00:00' ) )
            ->setService( $service )
            ->setStaff( $staff );

        $ca
            ->setId( 1 )
            ->setToken( '2000200020002000200020002' )
            ->setNumberOfPersons( 1 )
            ->setAppointment( $appointment )
            ->setCustomer( $customer )
            ->setTimeZone( 'UTC' );

        $cart_info = array( array(
            'service_name' => $service->getTitle(),
            'appointment_start' => $appointment->getStartDate(),
            'staff_name' => $staff->getFullName(),
            'appointment_price' => 24,
            'cancel_url' => '#',
            'appointment_start_info' => $service->getStartTimeInfo(),
            'deposit' => Lib\Proxy\DepositPayments::formatDeposit( 12, $service->getDeposit() ),
        ) );

        $order->addItem( 1, $this->item );

        parent::__construct( $order );

        $this->series_token = '1000100010001000100010001';
        $this->agenda_date = Utils\DateTime::formatDate( current_time( 'mysql' ) );
        $this->amount_due = '';
        $this->amount_paid = '';
        $this->appointment_end = $appointment->getStartDate();
        $this->appointment_id = $appointment->getId();
        $this->appointment_start = $appointment->getEndDate();
        $this->appointment_token = '';
        $this->booking_number = Config::groupBookingActive() ? $appointment->getId() . '-' . $this->item->getCA()->getId() : $this->item->getCA()->getId();
        $this->cancellation_reason = 'Some Reason';
        $this->cart_info = $cart_info;
        $this->category_image = 'https://dummyimage.com/100/cccccc/000000';
        $this->category_info = 'Category info text';
        $this->category_name = 'Category Name';
        $this->client_timezone = $ca->getTimeZone();
        $this->extras = 'Extras 1, Extras 2';
        $this->extras_total_price = '4';
        $this->new_password = 'New Password';
        $this->new_username = 'New User';
        $this->next_day_agenda = '';
        $this->next_day_agenda_extended = '';
        $this->number_of_persons = $ca->getNumberOfPersons();
        $this->payment_type = Entities\Payment::typeToString( Entities\Payment::TYPE_LOCAL );
        $this->service_duration = $service->getDuration();
        $this->service_info = $service->getInfo();
        $this->service_name = $service->getTitle();
        $this->service_image = 'https://dummyimage.com/100/dddddd/000000';
        $this->service_price = $service->getPrice();
        $this->staff_email = $staff->getEmail();
        $this->staff_info = $staff->getInfo();
        $this->staff_name = $staff->getFullName();
        $this->staff_phone = $staff->getPhone();
        $this->staff_photo = 'https://dummyimage.com/100/dddddd/000000';
        $this->staff_category_image = 'https://dummyimage.com/100/666666/000000';
        $this->staff_category_info = 'Staff Category info text';
        $this->staff_category_name = 'Staff Category Name';
        $this->total_price = '24';
        $this->schedule = array(
            array(
                'start' => $start_date->format( 'Y-m-d 12:00:00' ),
                'token' => '3000300030003000300030003',
                'duration' => '3600',
                'staff_name' => $this->staff_name,
                'staff_email' => $this->staff_email,
            ),
            array(
                'start' => $start_date->modify( '1 day' )->format( 'Y-m-d 14:00:00' ),
                'token' => '4000400040004000400040004',
                'duration' => '3600',
                'staff_name' => $this->staff_name,
                'staff_email' => $this->staff_email,
            ),
            array(
                'start' => $start_date->modify( '1 day' )->format( 'Y-m-d 12:00:00' ),
                'token' => '5000500050005000500050005',
                'duration' => '3600',
                'staff_name' => $this->staff_name,
                'staff_email' => $this->staff_email,
            ),
        );

        Proxy\Shared::prepareCodes( $this );
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );
        $replace_codes['verification_code'] = 123456;
        $replace_codes['access_token'] = 'nice-access-token';

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