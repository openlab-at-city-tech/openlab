<?php

require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');

class B2S_Dashboard_Calendar {

    public function __construct() {
        
    }

    public function getCalendarEntries() {

        $results = array();

        $start =  wp_date('Y-m-01 00:00:00',null, new DateTimeZone(date_default_timezone_get()));
        $end =  wp_date('Y-m-d 23:59:59', strtotime('+3 months'), new DateTimeZone(date_default_timezone_get()));

        $calendar = B2S_Calendar_Filter::getByTimespam($start, $end, 0, 0, 0);
        $entries = $calendar->asCalendarArray();
        foreach ($entries as $entry) {
            if (isset($entry['start']) && !empty($entry['start'])) {
                $results[] =  wp_date('Y-m-d', strtotime($entry['start']), new DateTimeZone(date_default_timezone_get()));
            }
        }
        return $results;
    }
}
