<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class MailingListRecipient extends Lib\Base\Entity
{
    /** @var int */
    protected $mailing_list_id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $phone;
    /** @var string */
    protected $created_at;

    protected static $table = 'bookly_mailing_list_recipients';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'mailing_list_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'MailingList' ) ),
        'name' => array( 'format' => '%s' ),
        'phone' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets mailing_list_id
     *
     * @return int
     */
    public function getMailingListId()
    {
        return $this->mailing_list_id;
    }

    /**
     * Sets mailing_list_id
     *
     * @param int $mailing_list_id
     * @return $this
     */
    public function setMailingListId( $mailing_list_id )
    {
        $this->mailing_list_id = $mailing_list_id;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets name
     *
     * @param string $name
     * @return $this
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone( $phone )
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Sets created_at
     *
     * @param string $created_at
     * @return $this
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
        if ( $this->getId() == null ) {
            $this->setCreatedAt( current_time( 'mysql' ) );
        }

        return parent::save();
    }
}