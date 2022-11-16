<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class Orders
 * @package Bookly\Lib\Entities
 */
class Order extends Lib\Base\Entity
{
    /** @var  string */
    protected $token;

    protected static $table = 'bookly_orders';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'token' => array( 'format' => '%s' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

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