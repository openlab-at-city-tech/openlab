<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class Shared
 *
 * @package Bookly\Lib\Proxy
 * @method static Lib\CartInfo applyGateway( Lib\CartInfo $cart_info, string $gateway ) Set gateway.
 * @method static string buildOnlineMeetingUrl( string $default, Lib\Entities\Appointment $appointment, Lib\Entities\Customer $customer ) Build online meeting URL for given appointment.
 * @method static string buildOnlineMeetingPassword( string $default, Lib\Entities\Appointment $appointment ) Build online meeting password for given appointment.
 * @method static string buildOnlineMeetingStartUrl( string $default, Lib\Entities\Appointment $appointment ) Build online meeting host url for given appointment.
 * @method static string buildOnlineMeetingJoinUrl( string $default, Lib\Entities\Appointment $appointment, Lib\Entities\Customer $customer ) Build online meeting join url for given appointment.
 * @method static void   deleteCustomerAppointment( Lib\Entities\CustomerAppointment $ca ) Deleting customer appointment
 * @method static void   doDailyRoutine() Execute daily routines.
 * @method static void   doHourlyRoutine() Execute hourly routines.
 * @method static array  prepareOutdatedUnpaidPayments( array $payments ) Get list of outdated unpaid payments ids.
 * @method static void   unpaidPayments( array $payments )
 * @method static array  handleRequestAction( string $action ) Handle requests with given action.
 * @method static array  prepareAppointmentCodes( array $codes, Lib\Entities\Appointment $appointment ) Prepare codes for given appointment.
 * @method static array  prepareCaSeSt( array $result ) Prepare Categories Services Staff data
 * @method static Lib\Query prepareCaSeStQuery( Lib\Query $query ) Prepare CaSeSt query
 * @method static array  prepareCategoryServiceStaffLocation( array $location_data, array $row ) Prepare Category Service Staff Location data by row
 * @method static array  prepareCategoryService( array $result, array $row ) Prepare Category Service data by row
 * @method static array  prepareCustomerAppointmentCodes( array $codes, Lib\Entities\CustomerAppointment $customer_appointment, string $format ) Prepare codes for given customer appointment.
 * @method static string prepareIcsEventTemplate( string $template, Lib\Utils\Ics\Event $event ) Prepare template for ICS.
 * @method static array  prepareNotificationTitles( array $titles ) Prepare notification titles.
 * @method static array  prepareNotificationTypes( array $types, string $gateway ) Prepare notification type IDs.
 * @method static array  preparePaymentDetails( array $details, Lib\DataHolders\Booking\Order $order, Lib\CartInfo $cart_info ) Add info about payment
 * @method static array  preparePaymentDetailsItem( array $details, Lib\DataHolders\Booking\Item $item ) Add info about payment item
 * @method static string preparePaymentImage( string $url, string $gateway ) Prepare payment image.
 * @method static Lib\Query prepareStaffServiceQuery( Lib\Query $query ) Prepare StaffService query for Finder.
 * @method static array  prepareTableColumns( array $columns, $table ) Prepare table columns for given table.
 * @method static array  prepareTableDefaultSettings( array $columns, $table ) Get default settings for hide/show table columns.
 * @method static array  prepareServices( array $result ) Prepare casest services list.
 * @method static string prepareStatement( string $value, string $statement, string $table ) Prepare default value for sql statement.
 * @method static void   renderAdminNotices( bool $bookly_page ) Render admin notices from add-ons.
 * @method static bool   saveUserBookingData( Lib\UserBookingData $userData ) Save UserBookingData.
 * @method static bool   showPaymentSpecificPrices( bool $show ) Whether to show specific price for each payment system.
 * @method static array  syncOnlineMeeting( array $errors, Lib\Entities\Appointment $appointment, Lib\Entities\Service $service ) Synchronize online meeting data with appointment.
 * @method static mixed  prepareGlobalSetting( $obj, string $token ) Extend BooklyL10Global JavaScript object
 * @method static array  prepareL10nGlobal( array $obj ) Extend BooklyL10Global JavaScript object
 * @method static array  prepareColorsStatuses( array $statuses ) Prepare colors for statuses.
 */
abstract class Shared extends Lib\Base\Proxy
{
}
