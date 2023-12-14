<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class Log extends Lib\Base\Entity
{
    /** @var string */
    protected $action;
    /** @var string */
    protected $target;
    /** @var int */
    protected $target_id;
    /** @var string */
    protected $author;
    /** @var string */
    protected $details;
    /** @var string */
    protected $ref;
    /** @var string */
    protected $comment;
    /** @var string */
    protected $created_at;

    protected static $table = 'bookly_log';

    protected static $schema = array(
        'id'           => array( 'format' => '%d' ),
        'action'       => array( 'format' => '%s' ),
        'target'       => array( 'format' => '%s' ),
        'target_id'    => array( 'format' => '%d' ),
        'author'       => array( 'format' => '%s' ),
        'details'      => array( 'format' => '%s' ),
        'ref'          => array( 'format' => '%s' ),
        'comment'      => array( 'format' => '%s' ),
        'created_at'   => array( 'format' => '%s' ),
    );


    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction( $action )
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor( $author )
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * @param string $target_id
     *
     * @return $this
     */
    public function setTargetId( $target_id )
    {
        $this->target_id = $target_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return $this
     */
    public function setTarget( $target )
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     *
     * @return $this
     */
    public function setDetails( $details )
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     *
     * @return $this
     */
    public function setRef( $ref )
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment( $comment )
    {
        $this->comment = $comment;

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
     * @return $this
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }
}