<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class NotificationQueue extends Lib\Base\Entity
{
    /** @var string */
    protected $token;
    /** @var string */
    protected $data;
    /** @var int */
    protected $sent = 0;
    /** @var string */
    protected $created_at;

    protected static $table = 'bookly_notifications_queue';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'token' => array( 'format' => '%s' ),
        'data' => array( 'format' => '%s' ),
        'sent' => array( 'format' => '%d' ),
        'created_at' => array( 'format' => '%s' ),
    );


    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return NotificationQueue
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @return NotificationQueue
     */
    public function setData( $data )
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return int
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param int $sent
     *
     * @return NotificationQueue
     */
    public function setSent( $sent )
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     *
     * @return NotificationQueue
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * @inheritDoc
     */
    public function save()
    {
        // Generate new token if it is not set.
        if ( $this->getToken() === null ) {
            $this->setToken( Lib\Utils\Common::generateToken( get_class( $this ), 'token' ) );
        }
        // Generate created_at if it is not set.
        if ( $this->getId() === null ) {
            $this->setCreatedAt( current_time( 'mysql' ) );
        }

        return parent::save();
    }

    public function delete()
    {
        $queue_data = json_decode( $this->getData(), true );

        Lib\Notifications\Routine::deleteNotificationAttachmentFiles( $queue_data );

        return parent::delete();
    }

}