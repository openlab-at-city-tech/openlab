<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class Pro
 *
 * @package Bookly\Lib\Proxy
 * @method static void   addLicenseBooklyMenuItem() Add 'License' to Bookly admin menu.
 * @method static string createWPUser( Lib\Entities\Customer $customer ) Crate WP user for customer.
 * @method static void   createBackendPayment( array $data, Lib\Entities\CustomerAppointment $ca ) Crate WP user for customer.
 * @method static void   deleteGoogleCalendarEvent( Lib\Entities\Appointment $appointment ) Delete Google Calendar event for given appointment.
 * @method static void   deleteOnlineMeeting( Lib\Entities\Appointment $appointment ) Delete online meeting.
 * @method static string getFullAddressByCustomerData( array $data ) Get address string from customer data.
 * @method static string getCustomerTimezone( string|null $time_zone, string $time_zone_offset ) Get last appointment timezone for customer.
 * @method static array  getDisplayedAddressFields() Get displayed address fields.
 * @method static array  getGoogleCalendarBookings( array $staff_ids, Lib\Slots\DatePoint $dp ) Get bookings fromGoogle Calendar  for Finder.
 * @method static string getGoogleCalendarSyncMode() Get Google Calendar synchronization mode ( 1-way, 1.5-way, 2-way. null means Google Calendar integration is not configured ).
 * @method static string getLastCustomerTimezone( int $customer_id ) Get last appointment timezone for customer.
 * @method static int    getMinimumTimePriorBooking( int|null $service_id ) Get minimum time ( in seconds ) prior to booking.
 * @method static int    getMinimumTimePriorCancel( int|null $service_id ) Get minimum time ( in seconds ) prior to cancel.
 * @method static array  getStaffCategoryName( int $category_id ) Get staff category name.
 * @method static array  getStaffDataForDropDown() Get staff grouped by categories for drop-down list.
 * @method static array  getTimeZoneOffset( string $time_zone_value ) Get timezone offset from string.
 * @method static bool   graceExpired() Check whether grace period has expired or not.
 * @method static void   logEmail( string $to, string $subject, string $body, array $headers, array $attachments, int $type_id ) Log sent emails.
 * @method static string prepareNotificationMessage( \string $message, \string $recipient, \string $gateway ) Prepare notification for staff.
 * @method static Lib\Slots\RangeCollection prepareGeneratorRanges( Lib\Slots\RangeCollection $ranges, Lib\Slots\Staff $staff, int $duration ) Prepare range collection depends on staff hours limit.
 * @method static bool   getWorkingTimeLimitError( Lib\Entities\Staff $staff, string $start_date, string $end_date, int $duration, int $appointment_id ) Check if interval is suitable for staff's hours limit.
 * @method static void   revokeGoogleCalendarToken( Lib\Entities\Staff $staff ) Revoke Google Calendar token for given staff.
 * @method static bool   showFacebookLoginButton() Whether to show Facebook login button at the time step of booking form.
 * @method static void   syncGoogleCalendarEvent( Lib\Entities\Appointment $appointment ) Synchronize Google Calendar with appointment.
 */
abstract class Pro extends Lib\Base\Proxy
{

}