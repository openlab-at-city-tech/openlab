<?php
namespace Bookly\Lib\Notifications\Assets\Verification;

use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Notifications\Assets\ClientBirthday;

/**
 * Class Codes
 * @package BooklyPro\Lib\Notifications\Assets\NewWpUser
 */
class Codes extends ClientBirthday\Codes
{
    // Core
    public $verification_code;
    public $site_address;

    /**
     * Constructor.
     *
     * @param Customer $customer
     * @param string $verification_code
     */
    public function __construct( Customer $customer, $verification_code )
    {
        parent::__construct( $customer );

        $this->verification_code = $verification_code;
        $this->site_address = site_url();
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );

        // Add replace codes.
        $replace_codes += array(
            'verification_code' => $this->verification_code,
            'site_address' => $this->site_address,
        );

        return $replace_codes;
    }
}