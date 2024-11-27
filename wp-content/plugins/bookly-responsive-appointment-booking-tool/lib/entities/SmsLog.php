<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class SmsLog extends Lib\Base\Entity
{
    /** @var string */
    protected $phone;
    /** @var string */
    protected $impersonal_message;
    /** @var string */
    protected $ref_id;
    /** @var integer */
    protected $type_id;
    /** @var string */
    protected $message;
    /** @var string */
    protected $created_at;

    protected static $table = 'bookly_sms_log';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'phone' => array( 'format' => '%s' ),
        'impersonal_message' => array( 'format' => '%s' ),
        'ref_id' => array( 'format' => '%s' ),
        'type_id' => array( 'format' => '%d' ),
        'message' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return SmsLog
     */
    public function setPhone( $phone )
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getImpersonalMessage()
    {
        return $this->impersonal_message;
    }

    /**
     * @param string $impersonal_message
     * @return SmsLog
     */
    public function setImpersonalMessage( $impersonal_message )
    {
        $this->impersonal_message = $impersonal_message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return SmsLog
     */
    public function setMessage( $message )
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefId()
    {
        return $this->ref_id;
    }

    /**
     * @param string $ref_id
     * @return SmsLog
     */
    public function setRefId( $ref_id )
    {
        $this->ref_id = $ref_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @param int $type_id
     * @return SmsLog
     */
    public function setTypeId( $type_id )
    {
        $this->type_id = $type_id;

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
     * @return SmsLog
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
        // Generate created_at if it is not set.
        if ( $this->getId() === null ) {
            $this->setCreatedAt( current_time( 'mysql' ) );
        }

        return parent::save();
    }
}