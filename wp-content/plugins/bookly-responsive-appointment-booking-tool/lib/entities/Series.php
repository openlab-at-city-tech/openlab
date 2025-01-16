<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class Series extends Lib\Base\Entity
{
    /** @var  string */
    protected $repeat;
    /** @var  string */
    protected $token;

    protected static $table = 'bookly_series';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'repeat' => array( 'format' => '%s' ),
        'token' => array( 'format' => '%s' ),
    );

    protected $loggable = true;

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets repeat
     *
     * @return string
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * Sets repeat
     *
     * @param string $repeat
     * @return $this
     */
    public function setRepeat( $repeat )
    {
        $this->repeat = $repeat;

        return $this;
    }

    /**
     * Gets token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets token
     *
     * @param string $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->token = $token;

        return $this;
    }

}