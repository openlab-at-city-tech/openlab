<?php
namespace Bookly\Lib\DataHolders\Details;

use Bookly\Lib;
use Bookly\Lib\DataHolders\Booking;
use Bookly\Lib\Entities\CustomerAppointment;

class Appointment extends Base
{
    protected $type = Lib\Entities\Payment::ITEM_APPOINTMENT;

    protected $fields = array(
        'app_start_info',
        'appointment_date',
        'ca_id',
        'deposit_format',
        'discounts',
        'duration',
        'extras',
        'number_of_persons',
        'service_name',
        'service_price',
        'service_tax',
        'staff_name',
        'units',
        'wait_listed',
    );

    protected function setItem( Booking\Item $item )
    {
        $extras = array();
        $extras_price = 0;
        $items = array();
        if ( $item->isCollaborative() || $item->isCompound() ) {
            foreach ( $item->getItems() as $c ) {
                $items[] = $c;
            }
        } else {
            $items[] = $item;
        }
        $extras_multiply_nop = (int) get_option( 'bookly_service_extras_multiply_nop', 1 );
        foreach ( $items as $sub_item ) {
            $extras = array_merge( $extras, $this->getExtras( $sub_item->getCA(), true, $extras_price ) );
        }

        $wait_listed = $item->getCA()->getStatus() == CustomerAppointment::STATUS_WAITLISTED;

        if ( ! $wait_listed ) {
            $price = $item->getServicePrice() * $item->getCA()->getNumberOfPersons();
            $price +=  Lib\Proxy\Discounts::prepareServicePrice( $extras_multiply_nop ? $extras_price * $item->getCA()->getNumberOfPersons() : $extras_price, $item->getService()->getId(), $item->getCA()
                ->getNumberOfPersons() );

            $this->price += $price;
            if ( Lib\Config::depositPaymentsActive() ) {
                $deposit_price = Lib\Proxy\DepositPayments::prepareAmount( $price, $item->getDeposit(), $item->getCA()->getNumberOfPersons() );
                $this->deposit += $deposit_price;
            }
        }

        $this->setCaExtras( $item->getCA(), $extras );
    }

    /**
     * @param CustomerAppointment $ca
     * @return $this
     */
    public function setCa( CustomerAppointment $ca )
    {
        return $this->setCaExtras( $ca, $this->getExtras( $ca ) );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice( $price )
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param CustomerAppointment $ca
     * @param array $extras
     * @return $this
     */
    private function setCaExtras( CustomerAppointment $ca, array $extras )
    {
        /** @var Booking\Item $item */
        $item = Booking\Item::collect( $ca );

        $this->setData(
            Lib\Proxy\Shared::preparePaymentDetailsItem(
                array(
                    'ca_id' => $ca->getId(),
                    'appointment_date' => $item->getAppointment()->getStartDate(),
                    'app_start_info' => $item->getServiceDuration() >= DAY_IN_SECONDS ? $item->getService()->getStartTimeInfo() : null,
                    'service_name' => $item->getService()->getTitle(),
                    'service_price' => $item->getServicePrice(),
                    'service_tax' => $item->getServiceTax(),
                    'wait_listed' => $ca->getStatus() === $ca::STATUS_WAITLISTED,
                    'number_of_persons' => $ca->getNumberOfPersons(),
                    'units' => $ca->getUnits() ?: 1,
                    'duration' => $item->getServiceDuration(),
                    'staff_name' => $item->getStaff()->getFullName(),
                    'deposit_format' => Lib\Proxy\DepositPayments::formatDeposit( $this->deposit, $item->getDeposit() ),
                    'extras' => $extras,
                ),
                $item
            )
        );

        return $this;
    }

    /**
     * @param CustomerAppointment $ca
     * @param bool $use_rate
     * @param float $extras_price
     * @return array
     */
    private function getExtras( CustomerAppointment $ca, $use_rate = true, &$extras_price = 0 )
    {
        $rates = $use_rate ? $this->getRates() : array();
        $extras_multiply_nop = (int) get_option( 'bookly_service_extras_multiply_nop', 1 );
        $extras = array();
        if ( $ca->getExtras() != '[]' ) {
            $_extras = json_decode( $ca->getExtras(), true );
            $service_id = Lib\Entities\Appointment::find( $ca->getAppointmentId() )->getServiceId();
            $rate = $use_rate && array_key_exists( $service_id, $rates ) ? $rates[ $service_id ] : 0;
            /** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra $service_extra */
            foreach ( Lib\Proxy\ServiceExtras::findByIds( array_keys( $_extras ) ) ?: array() as $service_extra ) {
                $quantity = (int) $_extras[ $service_extra->getId() ];
                $extras_amount = $service_extra->getPrice() * $quantity;
                if ( $extras_multiply_nop ) {
                    $extras_amount *= $ca->getNumberOfPersons();
                }
                $extras[] = array(
                    'title' => $service_extra->getTitle(),
                    'price' => $service_extra->getPrice(),
                    'quantity' => $quantity,
                    'tax' => Lib\Config::taxesActive()
                        ? Lib\Proxy\Taxes::calculateTax( $extras_amount, $rate )
                        : null,
                );
                $extras_price += $service_extra->getPrice() * $quantity;
            }
        }

        return $extras;
    }

    /**
     * @return array
     */
    private function getRates()
    {
        static $rates;
        if ( $rates === null ) {
            $rates = Lib\Proxy\Taxes::getServiceTaxRates() ?: array();
        }
        return $rates;
    }
}