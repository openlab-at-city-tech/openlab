<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class Files
 * @package Bookly\Lib\Proxy
 *
 * @method static \stdClass[] getAll() Return all custom fields with type file.
 * @method static array getAllIds() Get ids of custom fields for file.
 * @method static void attachFiles( array $custom_fields, Lib\Entities\CustomerAppointment $ca ) Attach uploaded files to Customer Appointment, and safely REMOVE unnecessary files
 * @method static array saveCustomFields( \stdClass[] $custom_fields ) Remove the missing (deprecated fields) custom fields.
 * @method static array setFileNamesForCustomFields( array $data, array $custom_fields ) Prepare data for showing 'customer file name'.
 * @method static array getFileNamesForCustomFields( array $custom_fields ) Get file names for custom fields.
 * @method static void renderCustomFieldButton() Render button file on page Custom Fields.
 * @method static void renderCustomFieldTemplate( string $services_html, string $description_html ) Render custom fields row in customer profile
 * @method static Lib\Query getSubQueryAttachmentExists() Sub Query for column attachments on page Appointments.
 */
abstract class Files extends Lib\Base\Proxy
{

}