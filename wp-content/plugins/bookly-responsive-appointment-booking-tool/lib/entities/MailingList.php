<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

class MailingList extends Lib\Base\Entity
{
    /** @var string */
    protected $name;

    protected static $table = 'bookly_mailing_lists';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'name' => array( 'format' => '%s' ),
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
}