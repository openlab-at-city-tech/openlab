<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class FormSession extends Lib\Base\Entity
{
    /** @var  string */
    protected $token;
    /** @var  string */
    protected $value;
    /** @var  string */
    protected $expire;

    protected static $table = 'bookly_form_sessions';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'token' => array( 'format' => '%s' ),
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

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    /**
     * Save entity to database.
     * Generate token before saving.
     *
     * @return int|false
     */
    public function save()
    {
        // Generate a new token if it is not set.
        if ( ! $this->getToken() ) {
            $this->setToken( Lib\Utils\Common::generateToken( get_class( $this ), 'token' ) );
        }

        if ( ! $this->getExpire() ) {
            $this->setExpire( date_create( current_time( 'mysql' ) )->modify( '+30 minutes' )->format( 'Y-m-d H:i:s' ) );
        }

        return parent::save();
    }
}