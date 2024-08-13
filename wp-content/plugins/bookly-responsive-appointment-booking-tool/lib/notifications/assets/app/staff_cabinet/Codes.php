<?php
namespace Bookly\Lib\Notifications\Assets\App\StaffCabinet;

use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Notifications\Assets\Base;
use Bookly\Lib\Utils\Common;

class Codes extends Base\Codes
{
    /** @var Staff */
    public $staff;

    public function setStaff( $staff )
    {
        $this->staff = $staff;
    }

    /**
     * @inheritDoc
     */
    protected function getReplaceCodes( $format )
    {
        $replace_codes = parent::getReplaceCodes( $format );

        // Prepare data.
        $staff_photo = $this->staff->getImageUrl();
        if ( $format == 'html' ) {
            // Staff photo as <img> tag.
            $staff_photo = Common::getImageTag( $staff_photo, $this->staff->getFullName() );
        }

        // Add replace codes.
        $replace_codes += array(
            'access_token' => $this->staff->getMobileStaffCabinetToken(),
            'staff_email' => $this->staff->getEmail(),
            'staff_info' => $format == 'html' ? nl2br( $this->staff->getInfo() ) : $this->staff->getInfo(),
            'staff_name' => $this->staff->getFullName(),
            'staff_phone' => $this->staff->getPhone(),
            'staff_photo' => $staff_photo,
        );

        return $replace_codes;
    }
}