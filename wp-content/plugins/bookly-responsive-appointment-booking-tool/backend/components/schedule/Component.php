<?php
namespace Bookly\Backend\Components\Schedule;

use Bookly\Lib;

/**
 * Class Component
 * @package Bookly\Backend\Components\Schedule
 */
class Component extends Lib\Base\Component implements \Iterator
{
    /** @var array */
    protected $days = array();
    /** @var Range[] */
    protected $hours = array();
    /** @var array|null */
    protected $breaks;

    /** @var BreakItem[] */
    public $day_breaks = array();

    /** @var Select */
    public $start_select;

    /** @var Select */
    public $end_select;
    /** @var string */
    protected $end_default = '18:00:00';

    /** @var array */
    protected $keys;

    /**
     * Constructor.
     *
     * @param string $start_name
     * @param string $end_name
     * @param bool $with_breaks
     */
    public function __construct( $start_name, $end_name, $with_breaks = true )
    {
        $this->start_select = new Select( array(
            'use_empty'   => true,
            'empty_value' => __( 'OFF', 'bookly' ),
            'type'        => 'from',
            'class'       => 'bookly-js-parent-range-start',
            'name'        => $start_name
        ) );
        $this->end_select = new Select( array(
            'use_empty' => false,
            'type'      => 'to',
            'class'     => 'bookly-js-parent-range-end',
            'name'      => $end_name
        ) );

        if ( $with_breaks ) {
            $this->breaks = array();
        }
    }

    /**
     * Add working hours.
     *
     * @param string $key   record key
     * @param string $day_index
     * @param string $start
     * @param string $end
     * @return $this
     */
    public function addHours( $key, $day_index, $start, $end )
    {
        $this->hours[ $key ]      = new Range( $start, $end );
        $this->keys[ $day_index ] = $key;

        return $this;
    }

    /**
     * Whether schedule may have breaks or not.
     *
     * @return bool
     */
    public function withBreaks()
    {
        return is_array( $this->breaks );
    }

    /**
     * Add break.
     *
     * @param string $index
     * @param string $entity_id
     * @param string $start
     * @param string $end
     * @return $this
     */
    public function addBreak( $index, $entity_id, $start, $end )
    {
        $this->breaks[ $index ][] = new BreakItem( $entity_id, $start, $end );

        return $this;
    }

    /**
     * Get breaks as array.
     *
     * @return array
     */
    public function getBreaksArray()
    {
        $flat = array();
        foreach ( $this->breaks as $index => $day_breaks ) {
            /** @var BreakItem $break */
            foreach ( $day_breaks as $break ) {
                $flat[] = array(
                    'index' => $index,
                    'start' => $break->getStart(),
                    'end'   => $break->getEnd(),
                );
            }
        }

        return $flat;
    }

    /**
     * Render schedule.
     *
     * @param bool $echo
     * @return string|void
     */
    public function render( $echo = true )
    {
        /** @global \WP_Locale $wp_locale */
        global $wp_locale;

        $start_of_week = (int) get_option( 'start_of_week' );
        for ( $i = 1; $i <= 7; $i ++ ) {
            $day_index = ( $start_of_week + $i ) < 8 ? $start_of_week + $i : $start_of_week + $i - 7;
            $id = $this->keys[ $day_index ];
            $this->days[ $id ] = $wp_locale->weekday[ $day_index == 7 ? 6 : ( $day_index - 1 ) ];
        }

        return self::renderTemplate( 'schedule', array( 'schedule' => $this ), $echo );
    }

    /**
     * Return day index
     *
     * @return int
     */
    public function index()
    {
        return array_search( $this->key(), $this->keys );
    }

    /**
     * Render edit break dialog.
     *
     * @param bool $echo
     * @return string|void
     */
    public static function renderBreakDialog( $echo = true )
    {
        return self::renderTemplate( 'break_dialog', array(
            'start_select' => new Select( array(
                'use_empty' => false,
                'type'      => 'break_from',
                'class'     => 'bookly-js-popover-range-start',
            ) ),
            'end_select'   => new Select( array(
                'use_empty' => false,
                'type'      => 'to',
                'class'     => 'bookly-js-popover-range-end',
            ) ),
        ), $echo );
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current( $this->days );
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        if ( next( $this->days ) !== false ) {
            $this->setState();
        }
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key( $this->days );
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return (bool) key( $this->days );
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        if ( reset( $this->days ) !== false ) {
            $this->setState();
        }
    }

    /**
     * Set state for current iteration.
     */
    protected function setState()
    {
        $index = $this->key();
        $hours = $this->hours[ $index ];

        $this
            ->start_select
            ->setIndex( $index )
            ->setValue( $hours->getStart() );
        $this
            ->end_select
            ->setIndex( $index )
            ->setValue( $hours->getEnd() ?: $this->end_default );

        $this->day_breaks = isset ( $this->breaks[ $index ] ) ? $this->breaks[ $index ] : array();
    }
}