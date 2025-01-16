<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib;

class Logs extends Tool
{
    protected $slug = 'logs';
    protected $hidden = false;
    protected $title = 'Logs';
    public $position = 30;

    public function render()
    {
        $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::LOGS );
        $debug = self::hasParameter( 'debug' );
        $options = $debug
            ? $this->getLogTypes()
            : array();

        return self::renderTemplate( '_logs', compact( 'datatables', 'debug', 'options' ), false );
    }

    public function hasError()
    {
        $errors = Lib\Entities\Log::query( 'l' )
            ->where( 'action', Lib\Utils\Log::ACTION_ERROR )
            ->whereGt( 'created_at', date_create( current_time( 'mysql' ) )->modify( '-1 day' )->format( 'Y-m-d H:i:s' ) )
            ->whereLike( 'target', '%bookly-%' )
            ->count();

        return $errors > 0;
    }

    /**
     * @return void
     */
    public function enableLogs()
    {
        $option = trim( self::parameter( 'option' ) );
        $period = (int) self::parameter( 'period' );
        $until = false;
        $options = $this->getLogTypes();
        if ( in_array( $option, $options, true ) ) {
            if ( $period ) {
                $until = date_create()->modify( $period . ' days' )->format( 'Y-m-d' );
                update_option( $option, date_create()->modify( $period . ' days' )->format( 'Y-m-d' ) );
            } else {
                update_option( $option, '0' );
            }
        }

        wp_send_json_success( array( 'until' => $until ? Lib\Utils\DateTime::formatDate( $until ) : false ) );
    }

    public function restore()
    {
        global $wpdb;

        $ids = self::parameter( 'ids' ) ?: array();

        do {
            $changed = false;
            foreach ( $ids as $key => $id ) {
                $record = Lib\Entities\Log::find( $id );
                if ( $record->getAction() === Lib\Utils\Log::ACTION_DELETE ) {
                    // Try to restore record
                    try {
                        $data = json_decode( $record->getDetails(), true );
                        $wpdb->insert(
                            $record->getTarget(),
                            $data
                        );
                        if ( ! $wpdb->last_error ) {
                            $appointments_db_name = 'bookly_appointments';
                            if ( substr( $record->getTarget(), -strlen( $appointments_db_name ) ) === $appointments_db_name ) {
                                $appointment = Lib\Entities\Appointment::find( $data['id'] );
                                if ( $appointment->getStartDate() ) {
                                    Lib\Proxy\Shared::syncOnlineMeeting( array(), $appointment, Lib\Entities\Service::find( $appointment->getServiceId() ) );
                                    Lib\Utils\Common::syncWithCalendars( $appointment );
                                }
                            }
                            $author_id = get_current_user_id();

                            $author = $author_id ? ( trim( get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true ) ) ?: get_user_meta( $author_id, 'nickname', true ) ) : '';
                            $record
                                ->setComment( 'Restored by ' . $author )
                                ->save();
                            $changed = true;
                            unset( $ids[ $key ] );
                        }
                    } catch ( \Exception $e ) {
                    }
                }
            }
        } while ( count( $ids ) > 0 && $changed );

        if ( count( $ids ) ) {
            wp_send_json_error();
        }

        wp_send_json_success();
    }

    /**
     * @return array
     */
    protected function getLogTypes()
    {
        $options = array();
        if ( Lib\Config::proActive() ) {
            $options[] = Lib\Utils\Log::OPTION_GOOGLE;
        }
        if ( Lib\Config::outlookCalendarActive() ) {
            $options[] = Lib\Utils\Log::OPTION_OUTLOOK;
        }

        return $options;
    }

}