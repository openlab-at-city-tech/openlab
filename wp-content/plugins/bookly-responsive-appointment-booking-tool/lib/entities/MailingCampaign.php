<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class MailingCampaign
 *
 * @package Bookly\Lib\Entities
 */
class MailingCampaign extends Lib\Base\Entity
{
    const STATE_PENDING = 'pending';
    const STATE_IN_PROGRESS = 'in-progress';
    const STATE_COMPLETED = 'completed';
    const STATE_CANCELED  = 'canceled';

    /** @var int */
    protected $mailing_list_id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $text;
    /** @var string */
    protected $state;
    /** @var string */
    protected $send_at;
    /** @var string */
    protected $created_at;

    protected static $table = 'bookly_mailing_campaigns';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'mailing_list_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'MailingList' ) ),
        'name' => array( 'format' => '%s' ),
        'text' => array( 'format' => '%s' ),
        'state' => array( 'format' => '%s' ),
        'send_at' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

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
     * Gets text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets text
     *
     * @param string $text
     * @return $this
     */
    public function setText( $text )
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Gets state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets state
     *
     * @param string $state
     * @return $this
     */
    public function setState( $state )
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Gets send_at
     *
     * @return string
     */
    public function getSendAt()
    {
        return $this->send_at;
    }

    /**
     * Sets send_at
     *
     * @param string $send_at
     * @return $this
     */
    public function setSendAt( $send_at )
    {
        $this->send_at = $send_at;

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