<?php
namespace Bookly\Lib\DataHolders\Details;

use Bookly\Lib\DataHolders\Booking\Item;

class Base
{
    /** @var string */
    protected $type;

    protected $price = 0;
    protected $deposit = 0;
    /** @var array */
    protected $fields = array();
    protected $data = array();

    public function __construct( $data = array() )
    {
        $this->setData( $data );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    protected function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set data.
     *
     * @param array $data
     * @return $this
     */
    public function setData( array $data )
    {
        foreach ( $this->fields as $field ) {
            if ( array_key_exists( $field, $data ) ) {
                if ( property_exists( $this, $field ) ) {
                    $this->$field = $data[ $field ];
                } else {
                    $this->data[ $field ] = $data[ $field ];
                }
            }
        }

        return $this;
    }

    public function getData()
    {
        $data = array(
            'type' => $this->getType(),
        );
        foreach ( $this->fields as $field ) {
            $data[ $field ] = $this->getValue( $field );
        }

        return $data;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public static function create( Item $item ) {
        switch ( $item->getType() ) {
            case Item::TYPE_SIMPLE:
            case Item::TYPE_COLLABORATIVE:
            case Item::TYPE_COMPOUND:
                $details = new Appointment();
                break;
            default:
                $details = \Bookly\Lib\Payment\Proxy\Shared::paymentCreateDetailsFromItem( null, $item );
                break;

        }
        $details->setItem( $item );

        return $details;
    }

    /**
     * @param string $key
     * @param string $default
     * @return string|array
     */
    public function getValue( $key, $default = null )
    {
        return array_key_exists( $key, $this->data )
            ? $this->data[ $key ]
            : $default;
    }

    /**
     * @param Item $item
     * @return $this
     */
    protected function setItem( Item $item )
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getDeposit()
    {
        return $this->deposit;
    }
}