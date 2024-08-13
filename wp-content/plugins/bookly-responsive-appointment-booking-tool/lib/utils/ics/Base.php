<?php
namespace Bookly\Lib\Utils\Ics;

use Bookly\Lib;

class Base
{
    protected $empty = true;
    protected $data;

    /**
     * @param $recipient
     * @return string
     */
    public function getDescriptionTemplate( $recipient = 'client' )
    {
        return Lib\Utils\Codes::getICSDescriptionTemplate( $recipient );
    }

    /**
     * Create ICS file.
     *
     * @return bool|string
     */
    public function create()
    {
        if ( ! $this->empty ) {
            $path = tempnam( get_temp_dir(), 'Bookly_' );

            if ( $path ) {
                $info = pathinfo( $path );
                $new_path = sprintf( '%s%s%s.ics', $info['dirname'], DIRECTORY_SEPARATOR, $info['filename'] );
                if ( rename( $path, $new_path ) ) {
                    $path = $new_path;
                } else {
                    $new_path = sprintf( '%s%s%s.ics', $info['dirname'], DIRECTORY_SEPARATOR, $info['basename'] );
                    if ( rename( $path, $new_path ) ) {
                        $path = $new_path;
                    }
                }
                Lib\Utils\Common::getFilesystem()->put_contents( $path, $this->data );

                return $path;
            }
        }

        return false;
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
    public function formatDateTime( $datetime )
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