<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib as BooklyLib;

/**
 * @method static void addBooklyMenuItem() Add 'Customer Information' to Bookly menu.
 * @method static \stdClass[] getFields( array $exclude = array() ) Get fields.
 * @method static \stdClass[] getOnlyFields( array $types = array() ) Get fields.
 * @method static \stdClass[] getFieldsWhichMayHaveData() Get fields which may have data (no Text Content).
 * @method static \stdClass[] getTranslatedFields() Get translated fields.
 * @method static array prepareInfoFields( array $info_fields ) Prepare information fields for customer.
 * @method static void renderCustomerCabinet( int $field_id, BooklyLib\Entities\Customer $customer ) Render 'Customer Information' row in customer cabinet.
 * @method static array validate( array $errors, array $values ) Validate fields.
 * @method static BooklyLib\UserBookingData setFromCookies( BooklyLib\UserBookingData $userData ) Set customer information data from cookies.
 */
abstract class CustomerInformation extends BooklyLib\Base\Proxy
{

}