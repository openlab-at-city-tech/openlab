<?php
/**
 * @deprecated
 */
namespace Bookly\Backend\Components\Appearance\Proxy
{
    abstract class Pro extends \Bookly\Lib\Base\Proxy{}
    abstract class Shared extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Components\Dialogs\Appointment\AttachPayment\Proxy
{
    abstract class Taxes extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails\Proxy
{
    abstract class Files extends \Bookly\Lib\Base\Proxy{}
    abstract class Pro extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Components\Dialogs\Appointment\Edit\Proxy
{
    abstract class Tasks extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Components\Dialogs\Customer\Edit\Proxy
{
    abstract class CustomerGroups extends \Bookly\Lib\Base\Proxy{}
    abstract class Pro extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Components\Dialogs\Customer\Proxy
{
    abstract class CustomerGroups extends \Bookly\Lib\Base\Proxy{}
    abstract class CustomerInformation extends \Bookly\Lib\Base\Proxy{}
    abstract class Pro extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Modules\Appointments\Proxy
{
    abstract class GroupBooking extends \Bookly\Lib\Base\Proxy{}
    abstract class Ratings extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Modules\Payments\Proxy
{
    abstract class Pro extends \Bookly\Lib\Base\Proxy{}
    abstract class Shared extends \Bookly\Lib\Base\Proxy{}
    abstract class CollaborativeServices extends \Bookly\Lib\Base\Proxy{}
    abstract class CompoundServices extends \Bookly\Lib\Base\Proxy{}
    abstract class CustomDuration extends \Bookly\Lib\Base\Proxy{}
    abstract class CustomerGroups extends \Bookly\Lib\Base\Proxy{}
    abstract class DepositPayments extends \Bookly\Lib\Base\Proxy{}
    abstract class GroupBooking extends \Bookly\Lib\Base\Proxy{}
    abstract class Packages extends \Bookly\Lib\Base\Proxy{}
    abstract class RecurringAppointments extends \Bookly\Lib\Base\Proxy{}
    abstract class ServiceExtras extends \Bookly\Lib\Base\Proxy{}
    abstract class ServiceSchedule extends \Bookly\Lib\Base\Proxy{}
    abstract class ServiceSpecialDays extends \Bookly\Lib\Base\Proxy{}
    abstract class Tasks extends \Bookly\Lib\Base\Proxy{}
    abstract class Taxes extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Modules\Sms\Proxy
{
    abstract class Shared extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Backend\Modules\Staff\Proxy
{
    abstract class Locations extends \Bookly\Lib\Base\Proxy{}
    abstract class OutlookCalendar extends \Bookly\Lib\Base\Proxy{}
    abstract class Ratings extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Frontend\Modules\Booking\Proxy
{
    abstract class ChainAppointments extends \Bookly\Lib\Base\Proxy{}
    abstract class Packages extends \Bookly\Lib\Base\Proxy{}
    abstract class PaypalCheckout extends \Bookly\Lib\Base\Proxy{}
    abstract class PaypalPaymentsStandard extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Frontend\Modules\ModernBookingForm\Proxy
{
    abstract class Packages extends \Bookly\Lib\Base\Proxy{}
    abstract class Pro extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Frontend\Modules\SearchForm\Proxy
{
    abstract class Shared extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Frontend\Modules\ServicesForm\Proxy
{
    abstract class Shared extends \Bookly\Lib\Base\Proxy{}
}

namespace Bookly\Lib\Base
{
    abstract class ProxyProvider{public static function registerMethods(){}}
    abstract class Controller{public static function getInstance(){$class = get_called_class();return new $class();}}
}

namespace Bookly\Lib\Proxy
{
    abstract class CollaborativeServices extends \Bookly\Lib\Base\Proxy{}
    abstract class CompoundServices extends \Bookly\Lib\Base\Proxy{}
}

namespace BooklyPro\Lib\Payment
{
    abstract class PayPal {
        const TYPE_EXPRESS_CHECKOUT = 'ec';
        const TYPE_PAYMENTS_STANDARD = 'ps';
        const TYPE_CHECKOUT = 'checkout';
    }
}