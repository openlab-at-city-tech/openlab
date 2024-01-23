<?php
namespace Bookly\Lib\Notifications\Assets\StaffAgenda;

use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Notifications\Assets\Base;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Common;

class Codes extends Base\Codes
{
    // Core
    public $agenda_date;
    public $next_day_agenda;
    public $next_day_agenda_extended;
    /** @var Staff */
    public $staff;

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
            'agenda_date'              => $this->agenda_date ? DateTime::formatDate( $this->agenda_date ) : '',
            'next_day_agenda'          => $this->next_day_agenda,
            'next_day_agenda_extended' => $this->next_day_agenda_extended,
            'staff_email'              => $this->staff->getEmail(),
            'staff_info'               => $format == 'html' ? nl2br( $this->staff->getInfo() ) : $this->staff->getInfo(),
            'staff_name'               => $this->staff->getFullName(),
            'staff_phone'              => $this->staff->getPhone(),
            'staff_photo'              => $staff_photo,
            'tomorrow_date'            => DateTime::formatDate( date_create( current_time( 'mysql' ) )->modify( '+1 day' )->format( 'Y-m-d' ) ),
        );

        return $replace_codes;
    }
}