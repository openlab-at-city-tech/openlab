<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class Session extends Lib\Base\Entity
{
    /** @var  string */
    protected $token;
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $value;
    /** @var  string */
    protected $expire;

    protected static $table = 'bookly_sessions';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'token' => array( 'format' => '%s' ),
        'name' => array( 'format' => '%s' ),
        'value' => array( 'format' => '%s' ),
        'expire' => array( 'format' => '%s' ),
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
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @param string $expire
     */
    public function setExpire( $expire )
    {
        $this->expire = $expire;

        return $this;
    }
}