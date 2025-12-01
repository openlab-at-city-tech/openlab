<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class Auth extends Lib\Base\Entity
{
    /** @var int */
    protected $staff_id;
    /** @var int */
    protected $wp_user_id;
    /** @var string */
    protected $token;
    /** @var int */
    protected $created_at;

    protected static $table = 'bookly_auths';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'staff_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Staff' ) ),
        'wp_user_id' => array( 'format' => '%d' ),
        'token' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
    );

    /**
     * Get staff id
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->staff_id;
    }

    /**
     * Set staff id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId( $staff_id )
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set created_at
     *
     * @param int $created_at
     * @return $this
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get wp_user_id
     *
     * @return int
     */
    public function getWpUserId()
    {
        return $this->wp_user_id;
    }

    /**
     * Set wp_user_id
     *
     * @param int $wp_user_id
     * @return $this
     */
    public function setWpUserId( $wp_user_id )
    {
        $this->wp_user_id = $wp_user_id;

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