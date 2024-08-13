<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib;
use Bookly\Lib\Entities;

abstract class Codes
{
    protected static $tokens = array(
        'T_CODE' => '{(\w+(?:\.\w+)*(?:\#\w+)*)}',
        'T_IF' => '{#if\s+(\w+(?:\.\w+)*(?:\#\w+)*)\s*(?:(==|!=|>=|>|<=|<|=|!empty|empty)\s*(.+?))?}\n?',
        'T_END_IF' => '{\/if}\n?',
        'T_EACH' => '{#each\s+(\w+(?:\.\w+)*)\s+as\s+(\w+)(?:\s+delimited\s+by\s+"(.+?)")?\s*}\n?',
        'T_END_EACH' => '{\/each}\n?',
    );

    /**
     * Replace codes in text
     *
     * @param string $text
     * @param array $codes
     * @param bool $bold
     * @param array $exclude
     * @param bool $escape
     * @return string
     */
    public static function replace( $text, $codes, $bold = true, $exclude = array(), $escape = false )
    {
        return self::stringify( self::tokenize( $text ), $codes, $bold, $exclude, $escape );
    }

    /**
     * Build string from tokens and codes data
     *
     * @param array $tokens
     * @param array $codes
     * @param bool $bold
     * @param array $exclude
     * @param bool $escape
     * @return string
     */
    public static function stringify( $tokens, $codes, $bold, $exclude = array(), $escape = false )
    {
        $output = '';

        foreach ( $tokens as $token ) {
            switch ( $token[0] ) {
                case 'T_TEXT':
                    $output .= $token[1];
                    break;
                case 'T_CODE':
                    $data = $code = self::get( $token[1], $codes );
                    if ( $code !== null ) {
                        if ( $escape ) {
                            $data = strip_tags( $code, '<br><div>' );
                        }
                        if ( $bold !== false && ! in_array( $token[1], $exclude ) ) {
                            $output .= '<b>' . $data . '</b>';
                        } else {
                            $output .= $data;
                        }
                    }
                    break;
                case 'T_IF':
                    $data = self::get( $token[1], $codes );
                    $nested_tokens = $token[3];
                    $if = false;
                    switch ( $token[2]['operator'] ) {
                        case '==' :
                        case '=' :
                            if ( $data == $token[2]['operand'] ) {
                                $if = true;
                            }
                            break;
                        case '!=' :
                            if ( $data != $token[2]['operand'] ) {
                                $if = true;
                            }
                            break;
                        case '>' :
                            if ( $data > $token[2]['operand'] ) {
                                $if = true;
                            }
                            break;
                        case '>=' :
                            if ( $data >= $token[2]['operand'] ) {
                                $if = true;
                            }
                            break;
                        case '<' :
                            if ( $data < $token[2]['operand'] ) {
                                $if = true;
                            }
                            break;
                        case '<=' :
                            if ( $data <= $token[2]['operand'] ) {
                                $if = true;
                            }
                            break;
                        case 'empty' :
                            if ( empty( $data ) ) {
                                $if = true;
                            }
                            break;
                        default :
                            if ( ! empty( $data ) ) {
                                $if = true;
                            }
                            break;
                    }
                    if ( $if ) {
                        $output .= self::stringify( $nested_tokens, $codes, $bold, $exclude, $escape );
                    }
                    break;
                case 'T_EACH':
                    $data = self::get( $token[1], $codes );
                    $context_code = $token[2];
                    $delimiter = $token[3];
                    $nested_tokens = $token[4];
                    if ( is_array( $data ) ) {
                        $parts = array();
                        foreach ( $data as $context_codes ) {
                            $parts[] = self::stringify( $nested_tokens, array( $context_code => $context_codes ) + $codes, $bold, $exclude, $escape );
                        }
                        $output .= implode( $delimiter, $parts );
                    }
                    break;
                default:
                    // Do nothing
            }
        }

        return $output;
    }

    /**
     * Split text into array of tokens
     *
     * @param string $text
     * @param int $offset
     * @param string $stop_token
     * @return array
     */
    public static function tokenize( $text, &$offset = 0, $stop_token = null )
    {
        $tokens = array();
        $text_start = null;

        while ( isset ( $text[ $offset ] ) ) {
            if ( $type = self::match( $text, $matches, $offset ) ) {
                if ( $text_start !== null ) {
                    // Raw text ended
                    $tokens[] = array( 'T_TEXT', substr( $text, $text_start, $offset - $text_start ) );
                    $text_start = null;
                }

                $offset += strlen( $matches[0] );

                if ( $type == $stop_token ) {
                    break;
                }

                $token = array( $type );

                if ( $type == 'T_CODE' ) {
                    $token[] = $matches[1];
                } elseif ( $type == 'T_IF' ) {
                    $token[] = $matches[1];
                    $token[] = array(
                        'operator' => isset( $matches[2] ) ? $matches[2] : null,
                        'operand' => isset( $matches[3] ) ? $matches[3] : null,
                    );
                    $token[] = self::tokenize( $text, $offset, 'T_END_IF' );
                } elseif ( $type == 'T_EACH' ) {
                    $token[] = $matches[1]; // code
                    $token[] = $matches[2]; // context code
                    $token[] = isset ( $matches[3] ) ? $matches[3] : ''; // delimiter
                    $token[] = self::tokenize( $text, $offset, 'T_END_EACH' );
                }

                $tokens[] = $token;
            } else {
                if ( $text_start === null ) {
                    // Raw text started
                    $text_start = $offset;
                }
                ++$offset;
            }
        }
        if ( $text_start !== null ) {
            // Raw text ended
            $tokens[] = array( 'T_TEXT', substr( $text, $text_start ) );
        }

        return $tokens;
    }

    /**
     * Match string with tokens
     *
     * @param string $subject
     * @param array &$matches
     * @param int $offset
     * @return false|string
     */
    protected static function match( $subject, &$matches, $offset )
    {
        foreach ( self::$tokens as $type => $pattern ) {
            if ( preg_match( "/$pattern/A", $subject, $matches, 0, $offset ) ) {

                return $type;
            }
        }

        return false;
    }

    /**
     * Get dot-notated path from array
     *
     * @param string $path
     * @param array $array
     * @return mixed|null
     */
    protected static function get( $path, $array )
    {
        $result = $array;
        foreach ( explode( '.', $path ) as $key ) {
            if ( isset ( $result[ $key ] ) ) {
                $result = $result[ $key ];
            } else {
                return null;
            }
        }

        return $result;
    }

    /**
     * Generate HTML for codes table
     *
     * @param array $codes
     * @return string
     */
    public static function tableHtml( array $codes )
    {
        $tbody = '';
        foreach ( $codes as $code => $description ) {
            $tbody .= sprintf(
                '<tr><td class="p-0"><input value="{%s}" class="border-0 bookly-outline-0" readonly="readonly" onclick="this.select()" /> &ndash; %s</td></tr>',
                $code,
                esc_html( $description )
            );
        }

        return '<table><tbody>' . $tbody . '</tbody></table>';
    }

    /**************************************************************************
     * Codes for entities                                                     *
     **************************************************************************/

    /**
     * Get codes for Appointment entity
     *
     * @param Entities\Appointment $appointment
     * @param string $format
     * @return array
     */
    public static function getAppointmentCodes( Entities\Appointment $appointment, $format = 'text' )
    {
        $staff = Entities\Staff::find( $appointment->getStaffId() );
        if ( $appointment->getServiceId() === null ) {
            $service = new Entities\Service( array(
                'duration' => $appointment->getStartDate() !== null ? strtotime( $appointment->getEndDate() ) - strtotime( $appointment->getStartDate() ) : null,
                'price' => $appointment->getCustomServicePrice(),
            ) );
        } else {
            $service = Entities\Service::find( $appointment->getServiceId() );
        }

        $timezone = $staff->getTimeZone() ?: Lib\Config::getWPTimeZone();
        $appointment_start = $appointment->getStartDate() ? Lib\Utils\DateTime::convertTimeZone( $appointment->getStartDate(), Lib\Config::getWPTimeZone(), $timezone ) : null;
        if ( $appointment->getStartDate() ) {
            $appointment_end = Lib\Slots\DatePoint::fromStr( $appointment->getEndDate() )->modify( $appointment->getExtrasDuration() );
            $appointment_end_date = clone $appointment_end;
            if ( $service->getDuration() >= DAY_IN_SECONDS && get_option( 'bookly_appointment_end_date_method' ) === 'accurate' ) {
                $appointment_end_date->modify( '-1 day' );
            }
            $appointment_end = Lib\Utils\DateTime::convertTimeZone( $appointment_end->format( 'Y-m-d H:i:s' ), Lib\Config::getWPTimeZone(), $timezone );
            $appointment_end_date = Lib\Utils\DateTime::convertTimeZone( $appointment_end_date->format( 'Y-m-d H:i:s' ), Lib\Config::getWPTimeZone(), $timezone );
        } else {
            $appointment_end = null;
            $appointment_end_date = null;
        }
        $service_name = $appointment->getServiceId() === null ? $appointment->getCustomServiceName() : $service->getTranslatedTitle();
        $staff_photo = $staff->getImageUrl();
        $category = $service->getCategoryId() ? Entities\Category::find( $service->getCategoryId() ) : false;
        $category_image = $category ? $category->getImageUrl() : '';

        $company_logo = '';
        if ( $format === 'html' ) {
            $company_logo = Common::getImageTag( Common::getAttachmentUrl( get_option( 'bookly_co_logo_attachment_id' ), 'full' ), get_option( 'bookly_co_name' ) );
            $staff_photo = Common::getImageTag( $staff_photo, $staff->getFullName() );
            $category_image = Common::getImageTag( $category_image, $category->getName() );
        }

        $codes = array(
            'signed_up' => 0,
            'number_of_persons' => 0,
            'participants' => array(),
            'appointment_date' => $appointment_start === null ? __( 'N/A', 'bookly' ) : Lib\Utils\DateTime::formatDate( $appointment_start ),
            'appointment_end_date' => $appointment_end === null ? __( 'N/A', 'bookly' ) : Lib\Utils\DateTime::formatDate( $appointment_end_date ),
            'appointment_end_time' => $appointment_end === null ? __( 'N/A', 'bookly' ) : ( $service->getDuration() < DAY_IN_SECONDS ? Lib\Utils\DateTime::formatTime( $appointment_end_date ) : $service->getEndTimeInfo() ),
            'appointment_id' => $appointment->getId(),
            'appointment_time' => $appointment_start === null ? __( 'N/A', 'bookly' ) : ( $service->getDuration() < DAY_IN_SECONDS ? Lib\Utils\DateTime::formatTime( $appointment_start ) : $service->getStartTimeInfo() ),
            'booking_number' => $appointment->getId(),
            'category_image' => $category_image,
            'category_info' => $category ? $category->getTranslatedInfo() : '',
            'category_name' => $service->getTranslatedCategoryName(),
            'company_address' => $format === 'html' ? nl2br( get_option( 'bookly_co_address' ) ) : get_option( 'bookly_co_address' ),
            'company_logo' => $company_logo,
            'company_name' => get_option( 'bookly_co_name' ),
            'company_phone' => get_option( 'bookly_co_phone' ),
            'company_website' => get_option( 'bookly_co_website' ),
            'google_calendar_url' => sprintf( 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=%s&dates=%s/%s&details=%s',
                urlencode( $service_name ),
                date( 'Ymd\THis', strtotime( $appointment_start ) ),
                date( 'Ymd\THis', strtotime( $appointment_end ) ),
                urlencode( sprintf( "%s\n%s", $service_name, $staff->getTranslatedName() ) )
            ),
            'internal_note' => $appointment->getInternalNote(),
            'service_duration' => Lib\Utils\DateTime::secondsToInterval( $service->getDuration() ),
            'service_info' => $format === 'html' ? nl2br( $service->getTranslatedInfo() ) : $service->getTranslatedInfo(),
            'service_name' => $service_name,
            'service_price' => Lib\Utils\Price::format( $service->getPrice() ),
            'staff_email' => $staff->getEmail(),
            'staff_info' => $format === 'html' ? nl2br( $staff->getTranslatedInfo() ) : $staff->getTranslatedInfo(),
            'staff_name' => $staff->getTranslatedName(),
            'staff_phone' => $staff->getPhone(),
            'staff_photo' => $staff_photo,
            'staff_timezone' => $staff->getTimeZone( false ) ?: '',
        );

        if ( $appointment->getServiceId() ) {
            $result = Entities\StaffService::query()
                ->select( 'capacity_max' )
                ->where( 'staff_id', $staff->getId() )
                ->where( 'service_id', $service->getId() )
                ->fetchRow();
            if ( $result ) {
                $codes['service_capacity'] = $result['capacity_max'];
            } else {
                $codes['service_capacity'] = 0;
            }
        } else {
            $codes['service_capacity'] = 9999;
        }

        $appointment_notes = array();
        $client_names = array();
        $client_phones = array();
        foreach ( $appointment->getCustomerAppointments( true ) as $customer_appointment ) {
            $codes['participants'][] = self::getCustomerAppointmentCodes( $customer_appointment );
            $codes['signed_up'] += $customer_appointment->getNumberOfPersons();
            $codes['number_of_persons'] += $customer_appointment->getNumberOfPersons();
            $codes['booking_number'] = Lib\Config::groupBookingActive() ? $codes['appointment_id'] . '-' . $customer_appointment->getId() : $customer_appointment->getId();
            $appointment_notes[] = $customer_appointment->getNotes();
            $client_names[] = $customer_appointment->customer->getFullName();
            $client_phones[] = $customer_appointment->customer->getPhone();
        }
        $codes['coupon'] = $codes['participants'] ? $codes['participants'][0]['coupon'] : '';
        $codes['client_names'] = implode( ', ', $client_names );
        $codes['client_phones'] = implode( ', ', $client_phones );
        $codes['appointment_notes'] = implode( ', ', $appointment_notes );

        return Lib\Proxy\Shared::prepareAppointmentCodes( $codes, $appointment );
    }

    /**
     * Get codes for CustomerAppointment entity
     *
     * @param Entities\CustomerAppointment $customer_appointment
     * @param string $format
     * @return array
     */
    public static function getCustomerAppointmentCodes( Entities\CustomerAppointment $customer_appointment, $format = 'text' )
    {
        $customer = $customer_appointment->customer;
        $payment = $customer_appointment->getPaymentId() ? Entities\Payment::find( $customer_appointment->getPaymentId() ) : null;
        $cancel_appointment_url = admin_url( 'admin-ajax.php?action=bookly_cancel_appointment&token=' . $customer_appointment->getToken() );

        $codes = array(
            'amount_due' => $payment ? Lib\Utils\Price::format( $payment->getTotal() - $payment->getPaid() ) : '',
            'amount_paid' => $payment ? Lib\Utils\Price::format( $payment->getPaid() ) : '',
            'appointment_id' => $customer_appointment->getAppointmentId(),
            'appointment_notes' => $customer_appointment->getNotes(),
            'booking_number' => Lib\Config::groupBookingActive() ? $customer_appointment->getAppointmentId() . '-' . $customer_appointment->getId() : $customer_appointment->getId(),
            'cancel_appointment' => sprintf( '<a href="%s" target="_blank">%s</a>', $cancel_appointment_url, __( 'Cancel Appointment', 'bookly' ) ),
            'cancel_appointment_url' => $cancel_appointment_url,
            'client_email' => $customer->getEmail(),
            'client_first_name' => $customer->getFirstName(),
            'client_last_name' => $customer->getLastName(),
            'client_name' => $customer->getFullName(),
            'client_note' => $customer->getNotes(),
            'client_phone' => $customer->getPhone(),
            'coupon' => '',
            'number_of_persons' => $customer_appointment->getNumberOfPersons(),
            'payment_status' => $payment ? Lib\Entities\Payment::statusToString( $payment->getStatus() ) : '',
            'payment_type' => $payment ? Lib\Entities\Payment::typeToString( $payment->getType() ) : '',
            'status' => $customer_appointment->getStatus(),
            'total_price' => $payment ? Lib\Utils\Price::format( $payment->getTotal() ) : '',
        );

        return Lib\Proxy\Shared::prepareCustomerAppointmentCodes( $codes, $customer_appointment, $format );
    }

    /**
     * @param Lib\DataHolders\Booking\Item $item
     * @return array
     */
    public static function getICSCodes( Lib\DataHolders\Booking\Item $item )
    {
        $customer = Lib\Entities\Customer::find( $item->getCA()->getCustomerId() );
        $appointment_start_date = $item->getAppointment()->getStartDate() === null ? __( 'N/A', 'bookly' ) : Lib\Utils\DateTime::formatDate( $item->getAppointment()->getStartDate() );
        $appointment_start_time = $item->getAppointment()->getStartDate() === null ? __( 'N/A', 'bookly' ) : Lib\Utils\DateTime::formatTime( $item->getAppointment()->getStartDate() );

        $codes = array(
            'appointment_date' => $appointment_start_date,
            'appointment_time' => $item->getService()->getDuration() > DAY_IN_SECONDS && $item->getService()->getStartTimeInfo() !== null ? $item->getService()->getStartTimeInfo() : $appointment_start_time,
            'service_name' => $item->getService()->getTranslatedTitle(),
            'service_price' => Lib\Utils\Price::format( $item->getServicePrice() ),
            'staff_name' => $item->getStaff()->getTranslatedName(),
            'client_name' => $customer->getFullName(),
            'client_email' => $customer->getEmail(),
            'client_phone' => $customer->getPhone(),
            'status' => Entities\CustomerAppointment::statusToString( $item->getCA()->getStatus() ),
            'online_meeting_url' => Lib\Proxy\Shared::buildOnlineMeetingUrl( '', $item->getAppointment(), $customer ),
            'online_meeting_password' => Lib\Proxy\Shared::buildOnlineMeetingPassword( '', $item->getAppointment() ),
            'online_meeting_start_url' => Lib\Proxy\Shared::buildOnlineMeetingStartUrl( '', $item->getAppointment() ),
            'online_meeting_join_url' => Lib\Proxy\Shared::buildOnlineMeetingJoinUrl( '', $item->getAppointment(), $customer ),
        );

        $ca = $item->getCA();
        $ca->customer = $customer;
        return Lib\Proxy\Shared::prepareCustomerAppointmentCodes( $codes, $ca, 'text' );
    }

    /**
     * @param $recipient
     * @return string
     */
    public static function getICSDescriptionTemplate( $recipient = 'client' )
    {
        return $recipient === 'client' ? Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_ics_customer_template' ) : get_option( 'bookly_ics_staff_template', '' );
    }
}