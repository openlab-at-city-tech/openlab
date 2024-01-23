<?php
namespace Bookly\Backend\Modules\Settings;

class Codes
{
    /**
     * Get JSON for appearance codes
     *
     * @param string $section
     * @return string
     */
    public static function getJson( $section )
    {
        $appointment_codes = array(
            'appointment_id' => array( 'description' => __( 'Appointment ID', 'bookly' ) ),
            'appointment_date' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true ),
            'appointment_time' => array( 'description' => __( 'Time of appointment', 'bookly' ), 'if' => true ),
            'booking_number' => array( 'description' => __( 'Booking number', 'bookly' ) ),
            'category_name' => array( 'description' => __( 'Name of category', 'bookly' ), 'if' => true ),
            'company_address' => array( 'description' => __( 'Address of company', 'bookly' ), 'if' => true ),
            'company_name' => array( 'description' => __( 'Name of company', 'bookly' ), 'if' => true ),
            'company_phone' => array( 'description' => __( 'Company phone', 'bookly' ), 'if' => true ),
            'company_website' => array( 'description' => __( 'Company web-site address', 'bookly' ), 'if' => true ),
            'internal_note' => array( 'description' => __( 'Internal note', 'bookly' ), 'if' => true ),
            'service_capacity' => array( 'description' => __( 'Capacity of service', 'bookly' ) ),
            'service_duration' => array( 'description' => __( 'Duration of service', 'bookly' ) ),
            'service_info' => array( 'description' => __( 'Info of service', 'bookly' ), 'if' => true ),
            'service_name' => array( 'description' => __( 'Name of service', 'bookly' ) ),
            'service_price' => array( 'description' => __( 'Price of service', 'bookly' ), 'if' => true ),
            'staff_email' => array( 'description' => __( 'Email of staff', 'bookly' ), 'if' => true ),
            'staff_info' => array( 'description' => __( 'Info of staff', 'bookly' ), 'if' => true ),
            'staff_name' => array( 'description' => __( 'Name of staff', 'bookly' ) ),
            'staff_phone' => array( 'description' => __( 'Phone of staff', 'bookly' ), 'if' => true ),
        );
        $client_codes = array(
            'appointment_id' => array( 'description' => __( 'Appointment ID', 'bookly' ) ),
            'appointment_notes' => array( 'description' => __( 'Customer notes for appointment', 'bookly' ), 'if' => true ),
            'booking_number' => array( 'description' => __( 'Booking number', 'bookly' ) ),
            'client_email' => array( 'description' => __( 'Email of client', 'bookly' ), 'if' => true ),
            'client_first_name' => array( 'description' => __( 'First name of client', 'bookly' ), 'if' => true ),
            'client_last_name' => array( 'description' => __( 'Last name of client', 'bookly' ), 'if' => true ),
            'client_name' => array( 'description' => __( 'Full name of client', 'bookly' ) ),
            'client_note' => array( 'description' => __( 'Note of client', 'bookly' ) ),
            'client_phone' => array( 'description' => __( 'Phone of client', 'bookly' ), 'if' => true ),
            'payment_status' => array( 'description' => __( 'Status of payment', 'bookly' ) ),
            'payment_type' => array( 'description' => __( 'Payment type', 'bookly' ) ),
            'status' => array( 'description' => __( 'Status of appointment', 'bookly' ) ),
        );
        switch ( $section ) {
            case 'calendar_one_participant' :
                $codes = array_merge( $appointment_codes, $client_codes );
                break;
            case 'calendar_many_participants' :
                $codes = array_merge( $appointment_codes, array(
                    'participants' => array(
                        'description' => array(
                            __( 'Loop over participants list', 'bookly' ),
                            __( 'Loop over participants list with delimiter', 'bookly' ),
                        ),
                        'loop' => array(
                            'item' => 'participant',
                            'codes' => $client_codes,
                        ),
                    ),
                ) );
                break;
            case 'ics_for_staff' :
            case 'ics_for_customer' :
                $codes = array(
                    'appointment_date' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true ),
                    'appointment_time' => array( 'description' => __( 'Time of appointment', 'bookly' ), 'if' => true ),
                    'service_name' => array( 'description' => __( 'Name of service', 'bookly' ) ),
                    'service_price' => array( 'description' => __( 'Price of service', 'bookly' ), 'if' => true ),
                    'staff_name' => array( 'description' => __( 'Name of staff', 'bookly' ) ),
                    'client_name' => array( 'description' => __( 'Full name of client', 'bookly' ) ),
                    'client_email' => array( 'description' => __( 'Email of client', 'bookly' ), 'if' => true ),
                    'client_phone' => array( 'description' => __( 'Phone of client', 'bookly' ), 'if' => true ),
                );
                break;
            default:
                $codes = array();
                break;
        }

        $codes = Proxy\Shared::prepareCodes( $codes, $section );

        return json_encode( $codes );
    }
}