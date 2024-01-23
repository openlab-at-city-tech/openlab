<?php
namespace Bookly\Backend\Modules\Settings\Proxy;

use Bookly\Lib;

/**
 * @method static void renderAppointmentsSettings() Render Appointments settings.
 * @method static void renderCancellationConfirmationUrl() Render cancellation confirmation URL setting.
 * @method static void renderCreateWordPressUser() Render Create WordPress user account for customers.
 * @method static void renderCustomersAddress() Render address settings in the customers tab.
 * @method static void renderCustomersAddressTemplate() Customer address template.
 * @method static void renderCustomersBirthday() Render birthday settings in the customers tab.
 * @method static void renderCustomersLimitStatuses() Render limit appointments statuses in the customers tab.
 * @method static void renderFinalStepUrl() Render final step URL setting.
 * @method static void renderMinimumTimeRequirement() Render minimum time requirement prior to booking and canceling.
 * @method static void renderNewClientAccountRole() Render New user account role for client.
 * @method static void renderNewStaffAccountRole() Render New user account role for staff.
 * @method static void renderMenuItem( $title, $slug ) Render menu item.
 */
abstract class Pro extends Lib\Base\Proxy
{

}