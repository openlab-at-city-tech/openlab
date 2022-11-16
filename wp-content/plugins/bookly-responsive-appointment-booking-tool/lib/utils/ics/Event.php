<?php
namespace Bookly\Lib\Utils\Ics;

use Bookly\Lib;

/**
 * Class Event
 *
 * @package Bookly\Lib\Utils\Ics
 */
class Event
{
    /** @var string */
    protected $start_date;
    /** @var string */
    protected $end_date;
    /** @var string */
    protected $summary;
    /** @var string */
    protected $description;
    /** @var int */
    protected $location_id;

    /**
     * @return string
     */
    public function render()
    {
        if ( $this->start_date === null ) {
            // Do not render VEVENT for tasks
            return '';
        }

        $template = "BEGIN:VEVENT\r\n"
            . "UID:%s\r\n"
            . "DTSTAMP:%s\r\n"
            . "DTSTART:%s\r\n"
            . "DTEND:%s\r\n"
            . "SUMMARY:%s\r\n"
            . "DESCRIPTION:%s\r\n";
        $template = Lib\Proxy\Shared::prepareIcsEventTemplate( $template, $this );
        $template .= "END:VEVENT\r\n";

        return sprintf(
            $template,
            $this->escape( $this->start_date . '-' . substr( md5( uniqid( time(), true ) ), 0, 8 ) . '@' . site_url() ),
            $this->_formatDateTime( $this->start_date ),
            $this->_formatDateTime( $this->start_date ),
            $this->_formatDateTime( $this->end_date ),
            $this->escape( $this->summary ),
            $this->escape( $this->description )
        );
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param string $start_date
     * @return Event
     */
    public function setStartDate( $start_date )
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param string $end_date
     * @return Event
     */
    public function setEndDate( $end_date )
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return Event
     */
    public function setSummary( $summary )
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Event
     */
    public function setDescription( $description )
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * @param int $location_id
     * @return Event
     */
    public function setLocationId( $location_id )
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Escape string.
     *
     * @param string $input
     * @return string
     */
    public function escape( $input )
    {
        $input = preg_replace( '/([\,;])/', '\\\$1', $input );
        $input = str_replace( array( "\r\n", "\n" ), "\\n", $input );

        return implode( "\r\n ", $this->_strSplitUnicode( $input, 60 ) );
    }

    /**
     * Format date and time.
     *
     * @param string $datetime
     * @return string
     */
    protected function _formatDateTime( $datetime )
    {
        return date_create( Lib\Utils\DateTime::convertTimeZone( $datetime, Lib\Config::getWPTimeZone(), 'UTC' ) )->format( 'Ymd\THis\Z' );
    }

    /**
     * Implementation of mb_str_split
     *
     * @param $str
     * @param int $l
     * @return array
     */
    protected function _strSplitUnicode( $str, $l = 0 )
    {
        $mb_str = preg_split( '//u', $str, -1, PREG_SPLIT_NO_EMPTY );
        if ( $l > 0 ) {
            $ret = array();
            $cnt = count( $mb_str );
            for ( $i = 0; $i < $cnt; $i += $l ) {
                $ret[] = implode( '', array_slice( $mb_str, $i, $l ) );
            }

            return $ret;
        }

        return $mb_str;
    }
}