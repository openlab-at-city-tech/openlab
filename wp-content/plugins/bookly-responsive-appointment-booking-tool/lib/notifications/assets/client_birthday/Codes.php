<?php
namespace Bookly\Lib\Notifications\Assets\ClientBirthday;

use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Notifications\Assets\Base;

class Codes extends Base\Codes
{
    // Core
    public $client_address;
    public $client_email;
    public $client_first_name;
    public $client_last_name;
    public $client_name;
    public $client_note;
    public $client_phone;
    public $client_birthday;
    public $client_full_birthday;

    /**
     * Constructor.
     *
     * @param Customer $customer
     */
    public function __construct( Customer $customer )
    {
        $this->client_address = $customer->getAddress();
        $this->client_email = $customer->getEmail();
        $this->client_first_name = $customer->getFirstName();
        $this->client_last_name = $customer->getLastName();
        $this->client_name = $customer->getFullName();
        $this->client_phone = $customer->getPhone();
        $this->client_note = $customer->getNotes();
        if ( $customer->getBirthday() ) {
            $this->client_full_birthday = \Bookly\Lib\Utils\DateTime::formatDate( $customer->getBirthday() );
            $this->client_birthday = date_i18n( 'F j', strtotime( $customer->getBirthday() ) );
        }
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );

        // Add replace codes.
        $replace_codes += array(
            'client_email' => $this->client_email,
            'client_address' => $format === 'html' ? nl2br( $this->client_address ) : $this->client_address,
            'client_name' => $this->client_name,
            'client_first_name' => $this->client_first_name,
            'client_last_name' => $this->client_last_name,
            'client_phone' => $this->client_phone,
            'client_note' => $this->client_note,
            'client_birthday' => $this->client_birthday,
            'client_full_birthday' => $this->client_full_birthday,
        );

        return $replace_codes;
    }
}