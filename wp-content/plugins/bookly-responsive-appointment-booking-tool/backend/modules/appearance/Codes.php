<?php
namespace Bookly\Backend\Modules\Appearance;

class Codes
{
    /**
     * Get JSON for appearance codes
     *
     * @param int $step
     * @param bool $extra_codes
     * @return string
     */
    public static function getJson( $step = null, $extra_codes = false, $format = 'json' )
    {
        $codes = Proxy\Shared::prepareCodes( array(
            'appointments' => array(
                'description' => array(
                    __( 'Loop over appointments list', 'bookly' ),
                    __( 'Loop over appointments list with delimiter', 'bookly' ),
                ),
                'loop' => array(
                    'item' => 'appointment',
                    'codes' => array(
                        'appointment_id' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true ),
                        'appointment_date' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true ),
                        'appointment_time' => array( 'description' => __( 'Time of appointment', 'bookly' ), 'if' => true ),
                        'category_image' => array( 'description' => __( 'Image of category', 'bookly' ), 'if' => true ),
                        'category_info' => array( 'description' => __( 'Service category info of appointment', 'bookly' ), 'if' => true ),
                        'category_name' => array( 'description' => __( 'Service category name of appointment', 'bookly' ), 'if' => true ),
                        'service_duration' => array( 'description' => __( 'Service duration of appointment', 'bookly' ) ),
                        'service_info' => array( 'description' => __( 'Info of service', 'bookly' ), 'if' => true ),
                        'service_name' => array( 'description' => __( 'Service name of appointment', 'bookly' ) ),
                        'service_price' => array( 'description' => __( 'Service price of appointment', 'bookly' ), 'if' => true ),
                        'staff_name' => array( 'description' => __( 'Staff member full name in appointment', 'bookly' ) ),
                        'total_duration' => array( 'description' => __( 'Duration of appointment', 'bookly' ) ),
                    ),
                ),
                'flags' => array( 'step' => '>1' ),
            ),
            'appointment_id' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>3' ) ),
            'appointment_date' => array( 'description' => __( 'Date of appointment', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>3' ) ),
            'appointment_time' => array( 'description' => __( 'Time of appointment', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>3' ) ),
            'appointments_count' => array( 'description' => __( 'Total quantity of appointments in cart', 'bookly' ), 'flags' => array( 'step' => 7, 'extra_codes' => true ) ),
            'booking_number' => array( 'description' => __( 'Booking number', 'bookly' ), 'flags' => array( 'step' => 8, 'extra_codes' => true ) ),
            'category_image' => array( 'description' => __( 'Image of category', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>1' ) ),
            'category_info' => array( 'description' => __( 'Info of category', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>1' ) ),
            'category_name' => array( 'description' => __( 'Name of category', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>1' ) ),
            'client_address' => array( 'description' => __( 'Address of client', 'bookly' ), 'if' => true ),
            'client_email' => array( 'description' => __( 'Email of client', 'bookly' ), 'if' => true ),
            'client_first_name' => array( 'description' => __( 'First name of client', 'bookly' ), 'if' => true ),
            'client_last_name' => array( 'description' => __( 'Last name of client', 'bookly' ), 'if' => true ),
            'client_name' => array( 'description' => __( 'Full name of client', 'bookly' ), 'if' => true ),
            'client_note' => array( 'description' => __( 'Note of client', 'bookly' ), 'if' => true ),
            'client_phone' => array( 'description' => __( 'Phone of client', 'bookly' ), 'if' => true ),
            'login_form' => array( 'description' => __( 'Login form', 'bookly' ), 'flags' => array( 'step' => 6, 'extra_codes' => true ) ),
            'service_duration' => array( 'description' => __( 'Duration of service', 'bookly' ) ),
            'service_image' => array( 'description' => __( 'Image of service', 'bookly' ), 'if' => true ),
            'service_info' => array( 'description' => __( 'Info of service', 'bookly' ), 'if' => true ),
            'service_name' => array( 'description' => __( 'Name of service', 'bookly' ) ),
            'service_price' => array( 'description' => __( 'Price of service', 'bookly' ), 'if' => true ),
            'staff_info' => array( 'description' => __( 'Info of staff member', 'bookly' ), 'if' => true ),
            'staff_name' => array( 'description' => __( 'Full name of staff member', 'bookly' ) ),
            'staff_photo' => array( 'description' => __( 'Photo of staff member', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>1' ) ),
            'total_price' => array( 'description' => __( 'Total price of booking', 'bookly' ), 'if' => true ),
            'total_duration' => array( 'description' => __( 'Duration of appointment', 'bookly' ) ),
        ) );

        $codes = self::filter( $codes, compact( 'step', 'extra_codes' ) );

        if ( $format === 'json' ) {
            return json_encode( $codes );
        } else {
            $tbody = '';
            foreach ( $codes as $key => $code ) {
                if ( ! isset( $code['loop'] ) ) {
                    $tbody .= sprintf(
                        '<tr><td class="p-0"><input value="{%s}" class="border-0 bookly-outline-0" readonly="readonly" onclick="this.select()" /> &ndash; %s</td></tr>',
                        $key,
                        esc_html( $code['description'] )
                    );
                }
            }

            return sprintf( '<table class="bookly-js-codes"><tbody>%s</tbody></table>', $tbody );
        }
    }

    /**
     * Get JSON for appearance services codes
     *
     * @return string
     */
    public static function getServiceCodes()
    {
        return json_encode( array(
            'service_duration' => array( 'description' => __( 'Duration of service', 'bookly' ) ),
            'service_image' => array( 'description' => __( 'Image of service', 'bookly' ), 'if' => true ),
            'service_image_url' => array( 'description' => __( 'URL of service image (to use inside img tag)', 'bookly' ), 'if' => true ),
            'service_info' => array( 'description' => __( 'Info of service', 'bookly' ), 'if' => true ),
            'service_name' => array( 'description' => __( 'Name of service', 'bookly' ) ),
            'service_price' => array( 'description' => __( 'Price of service', 'bookly' ), 'if' => true ),
        ) );
    }

    /**
     * Get JSON for appearance category codes
     *
     * @return string
     */
    public static function getCategoryCodes()
    {
        return json_encode( array(
            'category_image' => array( 'description' => __( 'Image of category', 'bookly' ), 'if' => true ),
            'category_image_url' => array( 'description' => __( 'URL of category image (to use inside img tag)', 'bookly' ), 'if' => true ),
            'category_info' => array( 'description' => __( 'Info of category', 'bookly' ), 'if' => true ),
            'category_name' => array( 'description' => __( 'Name of category', 'bookly' ) ),
            'staff_category_image' => array( 'description' => __( 'Image of staff category', 'bookly' ), 'if' => true ),
            'staff_category_info' => array( 'description' => __( 'Info of staff category', 'bookly' ), 'if' => true ),
            'staff_category_name' => array( 'description' => __( 'Name of staff category', 'bookly' ) ),
        ) );
    }

    /**
     * Get JSON for appearance staff codes
     *
     * @return string
     */
    public static function getStaffCodes()
    {
        return json_encode( array(
            'staff_info' => array( 'description' => __( 'Info of staff member', 'bookly' ), 'if' => true ),
            'staff_name' => array( 'description' => __( 'Full name of staff member', 'bookly' ) ),
            'staff_photo' => array( 'description' => __( 'Photo of staff member', 'bookly' ), 'if' => true ),
            'staff_photo_url' => array( 'description' => __( 'URL of staff photo (to use inside img tag)', 'bookly' ), 'if' => true ),
        ) );
    }

    /**
     * Filter codes
     *
     * @param array $codes
     * @param array $flags
     * @return array
     */
    protected static function filter( array $codes, $flags = array() )
    {
        // Sort codes alphabetically.
        ksort( $codes );

        $result = array();
        foreach ( $codes as $code => $data ) {
            $valid = true;
            if ( isset ( $data['flags'] ) ) {
                foreach ( $data['flags'] as $flag => $value ) {
                    $valid = false;
                    if ( isset ( $flags[ $flag ] ) ) {
                        if ( is_string( $value ) && preg_match( '/([!>=<]+)(\d+)/', $value, $match ) ) {
                            switch ( $match[1] ) {
                                case '<':
                                    $valid = $flags[ $flag ] < $match[2];
                                    break;
                                case '<=':
                                    $valid = $flags[ $flag ] <= $match[2];
                                    break;
                                case '=':
                                    $valid = $flags[ $flag ] == $match[2];
                                    break;
                                case '!=':
                                    $valid = $flags[ $flag ] != $match[2];
                                    break;
                                case '>=':
                                    $valid = $flags[ $flag ] >= $match[2];
                                    break;
                                case '>':
                                    $valid = $flags[ $flag ] > $match[2];
                                    break;
                            }
                        } else {
                            $valid = $flags[ $flag ] == $value;
                        }
                    }
                    if ( ! $valid ) {
                        break;
                    }
                }
            }
            if ( $valid ) {
                if ( isset( $data['loop']['codes'] ) ) {
                    $data['loop']['codes'] = self::filter( $data['loop']['codes'], $flags );
                }
                $result[ $code ] = $data;
            }
        }

        return $result;
    }
}