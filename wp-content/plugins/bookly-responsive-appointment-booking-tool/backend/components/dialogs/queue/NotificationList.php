<?php
namespace Bookly\Backend\Components\Dialogs\Queue;

use Bookly\Lib\Entities\Notification;

class NotificationList
{
    protected $list = array();

    /**
     * @param Notification $notification
     * @param string|array $message
     * @param string $address
     * @param array $queue_data
     * @param \Bookly\Lib\Notifications\Assets\Base\Attachments[] $attachments
     * @param string|null $impersonal
     * @param string $subject
     * @param array $headers
     * @return void
     */
    public function add( Notification $notification, $message, $address, $queue_data = array(), $attachments = array(), $impersonal = null, $subject = null, $headers = array() )
    {
        $this->list[] = array(
            'data' => $queue_data,
            'gateway' => $notification->getGateway(),
            'name' => $notification->getName(),
            'address' => $address,
            'subject' => $subject,
            'message' => $message,
            'headers' => $headers,
            'type_id' => $notification->getTypeId(),
            'impersonal' => $impersonal,
            'attachments' => $attachments,
        );
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }
}