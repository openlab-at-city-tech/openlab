<?php
namespace Bookly\Backend\Components\Schedule;

use Bookly\Lib;

class Select
{
    /** @var array */
    protected $values = array();

    /** @var string */
    protected $name;
    /** @var string */
    protected $class = 'form-control custom-select';
    /** @var string */
    protected $index;
    /** @var string */
    protected $value;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct( array $options = array() )
    {
        // Handle widget options.
        $options = array_merge( array(
            'use_empty'   => true,
            'empty_value' => null,
            'type'        => 'from',
        ), $options );

        if ( array_key_exists( 'name', $options ) ) {
            $this->name = $options['name'];
        }
        if ( array_key_exists( 'class', $options ) ) {
            $this->class .= ' ' . $options['class'];
        }

        // Insert empty value if required.
        if ( $options['use_empty'] ) {
            $this->values[ null ] = $options['empty_value'];
        }

        $ts_length  = Lib\Config::getTimeSlotLength();
        $time_start = 0;
        $time_end   = DAY_IN_SECONDS;

        if ( $options['type'] == 'from' ) {
            $time_end -= $ts_length;    // Exclude last slot.
        } else if ( $options['type'] == 'break_from' ) {
            $time_end *= 2;             // Create slots for 2 days.
            $time_end -= $ts_length;    // Exclude last slot.
        } else if ( $options['type'] == 'to' ) {
            $time_end *= 2;             // Create slots for 2 days.
        }

        // Run the loop.
        while ( $time_start <= $time_end ) {
            $this->values[ Lib\Utils\DateTime::buildTimeString( $time_start ) ] = $time_start >= DAY_IN_SECONDS
                ? Lib\Utils\DateTime::formatTime( $time_start ) . ' (' . esc_attr__( 'next day', 'bookly' ) . ')'
                : Lib\Utils\DateTime::formatTime( $time_start );
            $time_start += $ts_length;
        }
    }

    /**
     * @param $index
     * @return $this
     */
    public function setIndex( $index )
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue( $value = null )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Render select.
     *
     * @param bool $echo
     * @return string|void
     */
    public function render( $echo = true )
    {
        $attributes_str = 'class="' . $this->class . '"';
        if ( $this->name ) {
            $attributes_str .= ' name="' . str_replace( '{index}', $this->index, $this->name ) . '"';
        }
        $options     = '';
        $value_added = false;
        foreach ( $this->values as $option_value => $option_text ) {
            if ( $value_added === false ) {
                if ( $this->value == $option_value ) {
                    $value_added = true;
                } elseif ( $this->value < $option_value ) {
                    // Make sure that value presents in the list,
                    // even if corresponding option did not exist in $this->values.
                    $options .= sprintf(
                        '<option value="%s" selected="selected">%s</option>',
                        $this->value,
                        Lib\Utils\DateTime::formatTime( Lib\Utils\DateTime::timeToSeconds( $this->value ) )
                    );
                    $value_added = true;
                }
            }
            $options .= sprintf(
                '<option value="%s"%s>%s</option>',
                $option_value,
                selected( $this->value, $option_value, false ),
                $option_text
            );
        }

        $html = sprintf( '<select %s data-default_value="%s">%s</select>',
            $attributes_str,
            $this->value,
            $options
        );

        if ( $echo ) {
            echo Lib\Utils\Common::stripScripts( $html );
        } else {
            return $html;
        }
    }
}