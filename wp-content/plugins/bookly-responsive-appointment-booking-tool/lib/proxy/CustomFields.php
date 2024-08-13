<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * @method static void addBooklyMenuItem() Add 'Custom Fields' to Bookly menu.
 * @method static \stdClass[] getAll( array $exclude ) Get custom fields
 * @method static \stdClass[] getOnly( array $types ) Get custom fields
 * @method static array filterForService( array $custom_fields, int $service_id ) Get custom fields
 * @method static \stdClass[] getTranslated( $service_id = null, $translate = true, $language_code = null ) Get translated custom fields
 * @method static \stdClass[] getWhichHaveData() Get custom fields which may have data ( no Captcha and Text Content )
 * @method static array getForCustomerAppointment( Lib\Entities\CustomerAppointment $ca, $translate = false, $locale = null, $backend_only = true ) Get custom fields data for given customer appointment
 * @method static string getFormatted( Lib\Entities\CustomerAppointment $ca, $format, $locale = null ) Get formatted custom fields
 * @method static array validate( array $errors, $value, $form_id, $cart_key ) Validate custom fields
 */
abstract class CustomFields extends Lib\Base\Proxy
{

}