<?php
namespace Bookly\Backend\Modules\CloudSms;

use Bookly\Lib;

/**
 * Class Ajax
 *
 * @package Bookly\Backend\Modules\CloudSms
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array(
            'sendQueue' => array( 'supervisor', 'staff' ),
            'clearAttachments' => array( 'supervisor', 'staff' ),
        );
    }

    /**
     * Get SMS list.
     */
    public static function getSmsList()
    {
        $dates = explode( ' - ', self::parameter( 'range' ), 2 );
        $start = Lib\Utils\DateTime::applyTimeZoneOffset( $dates[0], 0 );
        $end = Lib\Utils\DateTime::applyTimeZoneOffset( date( 'Y-m-d', strtotime( '+1 day', strtotime( $dates[1] ) ) ), 0 );

        wp_send_json( Lib\Cloud\API::getInstance()->sms->getSmsList( $start, $end ) );
    }

    /**
     * Get price-list.
     */
    public static function getPriceList()
    {
        wp_send_json( Lib\Cloud\API::getInstance()->sms->getPriceList() );
    }

    /**
     * Send test SMS.
     */
    public static function sendTestSms()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $response = array(
            'success' => $cloud->sms->sendSms(
                self::parameter( 'phone_number' ),
                'Bookly test SMS.',
                'Bookly test SMS.',
                0
            ),
        );

        if ( $response['success'] ) {
            $response['message'] = __( 'SMS has been sent successfully.', 'bookly' );
        } else {
            $response['message'] = implode( ' ', $cloud->getErrors() );
        }

        wp_send_json( $response );
    }

    /**
     * Get Sender IDs list.
     */
    public static function getSenderIdsList()
    {
        wp_send_json( Lib\Cloud\API::getInstance()->sms->getSenderIdsList() );
    }

    /**
     * Request new Sender ID.
     */
    public static function requestSenderId()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $result = $cloud->sms->requestSenderId( self::parameter( 'sender_id' ) );
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
        } else {
            wp_send_json_success( array( 'request_id' => $result['request_id'] ) );
        }
    }

    /**
     * Cancel request for Sender ID.
     */
    public static function cancelSenderId()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $result = $cloud->sms->cancelSenderId();
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
        } else {
            wp_send_json_success();
        }
    }

    /**
     * Reset Sender ID to default (Bookly).
     */
    public static function resetSenderId()
    {
        $cloud = Lib\Cloud\API::getInstance();
        $result = $cloud->sms->resetSenderId();
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
        } else {
            wp_send_json_success();
        }
    }

    /**
     * Delete notification.
     */
    public static function deleteNotification()
    {
        Lib\Entities\Notification::query()
            ->delete()
            ->where( 'id', self::parameter( 'id' ) )
            ->execute();

        wp_send_json_success();
    }

    /**
     * Get data for notification list.
     */
    public static function getNotifications()
    {
        $types = Lib\Entities\Notification::getTypes( self::parameter( 'gateway' ) );

        $notifications = Lib\Entities\Notification::query()
            ->select( 'id, name, active, type' )
            ->where( 'gateway', self::parameter( 'gateway' ) )
            ->whereIn( 'type', $types )
            ->fetchArray();

        foreach ( $notifications as &$notification ) {
            $notification['order'] = array_search( $notification['type'], $types );
            $notification['icon'] = Lib\Entities\Notification::getIcon( $notification['type'] );
            $notification['title'] = Lib\Entities\Notification::getTitle( $notification['type'] );
        }

        wp_send_json_success( $notifications );
    }

    /**
     * Activate/Suspend notification.
     */
    public static function setNotificationState()
    {
        Lib\Entities\Notification::query()
            ->update()
            ->set( 'active', (int) self::parameter( 'active' ) )
            ->where( 'id', self::parameter( 'id' ) )
            ->execute();

        wp_send_json_success();
    }

    /**
     * Remove notification(s).
     */
    public static function deleteNotifications()
    {
        $notifications = array_map( 'intval', self::parameter( 'notifications', array() ) );
        Lib\Entities\Notification::query()->delete()->whereIn( 'id', $notifications )->execute();
        wp_send_json_success();
    }

    public static function saveAdministratorPhone()
    {
        update_option( 'bookly_sms_administrator_phone', self::parameter( 'bookly_sms_administrator_phone' ) );
        wp_send_json_success();
    }

    /**
     * Send queue
     */
    public static function sendQueue()
    {
        $notifications = self::parameter( 'notifications', array() );
        $type = self::parameter( 'type', 'all' );
        $token = self::parameter( 'token' );
        /** @var Lib\Entities\NotificationQueue $queue */
        $queue = Lib\Entities\NotificationQueue::query()->where( 'token', $token )->where( 'sent', 0 )->findOne();
        if ( $queue ) {
            $queue_data = json_decode( $queue->getData(), true );
            if ( isset( $queue_data[ $type ] ) ) {
                $cloud = Lib\Cloud\API::getInstance();
                foreach ( $notifications as $queue_id ) {
                    if ( isset( $queue_data[ $type ][ $queue_id ] ) ) {
                        $notification = $queue_data[ $type ][ $queue_id ];
                        $gateway = $notification['gateway'];
                        if ( $gateway === 'sms' ) {
                            $cloud->sms->sendSms( $notification['address'], $notification['message'], $notification['impersonal'], $notification['type_id'] );
                        } elseif ( $gateway === 'email' ) {
                            Lib\Utils\Mail::send( $notification['address'], $notification['subject'], $notification['message'], $notification['headers'], isset( $notification['attachments'] ) ? $notification['attachments'] : array(), $notification['type_id'] );
                        } elseif ( $gateway === 'voice' ) {
                            $cloud->voice->call( $notification['address'], $notification['message'], $notification['impersonal'] );
                        } elseif ( $gateway === 'whatsapp' ) {
                            $cloud->whatsapp->send( $notification['address'], $notification['message'] );
                        }
                    }
                }
            }
            self::_deleteAttachmentFiles( $queue_data );

            $queue->setSent( 1 )->save();
        }

        wp_send_json_success();
    }

    /**
     * Delete attachments files
     */
    public static function clearAttachments()
    {
        $token = self::parameter( 'token' );
        /** @var Lib\Entities\NotificationQueue $queue */
        $queue = Lib\Entities\NotificationQueue::query()->where( 'token', $token )->where( 'sent', 0 )->findOne();
        if ( $queue ) {
            $queue_data = json_decode( $queue->getData(), true );
            self::_deleteAttachmentFiles( $queue_data );

            $queue->setSent( 1 )->save();
        }

        wp_send_json_success();
    }

    /**
     * Get mailing list
     */
    public static function getMailingList()
    {
        global $wpdb;

        $columns = self::parameter( 'columns' );
        $order = self::parameter( 'order', array() );
        $filter = self::parameter( 'filter' );
        $limits = array(
            'length' => self::parameter( 'length' ),
            'start' => self::parameter( 'start' ),
        );

        $query = Lib\Entities\MailingList::query( 'm' )
            ->select( 'm.id, m.name, COUNT(r.id) AS number_of_recipients' )
            ->leftJoin( 'MailingListRecipient', 'r', 'r.mailing_list_id = m.id' )
            ->groupBy( 'm.id' );

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $filtered = $total = Lib\Entities\MailingList::query()->count();

        if ( $filter['search'] != '' ) {
            $fields = array();
            foreach ( $columns as $column ) {
                switch ( $column['data'] ) {
                    case 'name':
                    case 'id':
                        $fields[] = 'm.' . $column['data'];
                        break;
                }
            }
            $search_columns = array();
            foreach ( $fields as $field ) {
                $search_columns[] = $field . ' LIKE "%%%s%"';
            }
            if ( ! empty( $search_columns ) ) {
                $query->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), $wpdb->esc_like( $filter['search'] ) ) );
                $filtered = Lib\Entities\MailingList::query()->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), $wpdb->esc_like( $filter['search'] ) ) )->count();
            }
        }

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }

        $data = $query->fetchArray();

        unset( $filter['search'] );

        Lib\Utils\Tables::updateSettings( Lib\Utils\Tables::SMS_MAILING_LISTS, $columns, $order, $filter );

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
        ) );
    }

    /**
     * Get mailing list
     */
    public static function getMailingRecipients()
    {
        global $wpdb;

        $columns = self::parameter( 'columns' );
        $order = self::parameter( 'order', array() );
        $filter = self::parameter( 'filter' );
        $limits = array(
            'length' => self::parameter( 'length' ),
            'start' => self::parameter( 'start' ),
        );

        $query = Lib\Entities\MailingListRecipient::query()
            ->select( 'id, name, phone' )
            ->where( 'mailing_list_id', self::parameter( 'mailing_list_id' ) );

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $total = $query->count();

        if ( $filter['search'] != '' ) {
            $fields = array();
            foreach ( $columns as $column ) {
                switch ( $column['data'] ) {
                    case 'name':
                    case 'phone':
                        $fields[] = 'name';
                        break;
                }
            }
            $search_columns = array();
            foreach ( $fields as $field ) {
                $search_columns[] = $field . ' LIKE "%%%s%"';
            }
            if ( ! empty( $search_columns ) ) {
                $query->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), $wpdb->esc_like( $filter['search'] ) ) );
            }
        }

        $filtered = $query->count();

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }

        $data = $query->fetchArray();

        unset( $filter['search'] );

        Lib\Utils\Tables::updateSettings( Lib\Utils\Tables::SMS_MAILING_RECIPIENTS_LIST, $columns, $order, $filter );

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
        ) );
    }

    /**
     * Delete mailing lists
     */
    public static function deleteMailingLists()
    {
        $ids = array_map( 'intval', self::parameter( 'ids', array() ) );
        Lib\Entities\MailingList::query()->delete()->whereIn( 'id', $ids )->execute();

        Lib\Entities\MailingQueue::query()
            ->delete()
            ->whereIn( 'campaign_id', $ids )
            ->where( 'sent', '0' )
            ->execute();

        wp_send_json_success();
    }

    /**
     * Delete recipients from mailing list
     */
    public static function deleteMailingRecipients()
    {
        $ids = array_map( 'intval', self::parameter( 'ids', array() ) );
        Lib\Entities\MailingListRecipient::query()->delete()->whereIn( 'id', $ids )->execute();

        wp_send_json_success();
    }

    /**
     * Get mailing list
     */
    public static function getCampaignList()
    {
        global $wpdb;

        $columns = self::parameter( 'columns' );
        $order = self::parameter( 'order', array() );
        $filter = self::parameter( 'filter' );
        $limits = array(
            'length' => self::parameter( 'length' ),
            'start' => self::parameter( 'start' ),
        );

        $query = Lib\Entities\MailingCampaign::query()
            ->select( 'id, name, state, send_at' );

        foreach ( $order as $sort_by ) {
            $query->sortBy( str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] ) )
                ->order( $sort_by['dir'] == 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $total = $query->count();

        if ( $filter['search'] != '' ) {
            $fields = array();
            foreach ( $columns as $column ) {
                switch ( $column['data'] ) {
                    case 'name':
                    case 'id':
                        $fields[] = $column['data'];
                        break;
                }
            }
            $search_columns = array();
            foreach ( $fields as $field ) {
                $search_columns[] = $field . ' LIKE "%%%s%"';
            }
            if ( ! empty( $search_columns ) ) {
                $query->whereRaw( implode( ' OR ', $search_columns ), array_fill( 0, count( $search_columns ), $wpdb->esc_like( $filter['search'] ) ) );
            }
        }

        $filtered = $query->count();

        if ( ! empty( $limits ) ) {
            $query->limit( $limits['length'] )->offset( $limits['start'] );
        }

        $data = $query->fetchArray();

        unset( $filter['search'] );

        Lib\Utils\Tables::updateSettings( Lib\Utils\Tables::SMS_MAILING_CAMPAIGNS, $columns, $order, $filter );

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
        ) );
    }

    /**
     * Delete mailing lists
     */
    public static function deleteCampaigns()
    {
        $ids = array_map( 'intval', self::parameter( 'ids', array() ) );
        Lib\Entities\MailingCampaign::query()->delete()->whereIn( 'id', $ids )->execute();

        wp_send_json_success();
    }

    /**
     * Delete attachment files
     *
     * @param $queue_data
     */
    private static function _deleteAttachmentFiles( $queue_data )
    {
        $fs = Lib\Utils\Common::getFilesystem();
        foreach ( $queue_data as $data ) {
            foreach ( $data as $message ) {
                foreach ( $message['attachments'] as $file ) {
                    $fs->delete( $file, false, 'f' );
                }
            }
        }
    }
}