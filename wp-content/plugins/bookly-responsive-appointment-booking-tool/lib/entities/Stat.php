<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class Stat
 * @package Bookly\Lib\Entities
 */
class Stat extends Lib\Base\Entity
{
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $value;
    /** @var  string */
    protected $created_at;

    protected static $table = 'bookly_stats';

    protected static $schema = array(
        'id'    => array( 'format' => '%d' ),
        'name'  => array( 'format' => '%s' ),
        'value' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
    );

    /**
     * @param string $variable
     * @param int    $affected
     */
    public static function record( $variable, $affected )
    {
        if ( $affected > 0 ) {
            $parameters = array(
                'name'     => $variable,
                'created_at' => substr( current_time( 'mysql' ), 0, 10 ),
            );
            $stat       = new Stat();
            $stat->loadBy( $parameters );
            if ( ! $stat->isLoaded() ) {
                $stat->setFields( $parameters );
            }
            $stat
                ->setValue( ( (int) $stat->getValue() ) + $affected )
                ->save();
        }
    }

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
     * Gets value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets value
     *
     * @param string $value
     * @return $this
     */
    public function setValue( $value )
    {
        $this->value = $value;

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

}