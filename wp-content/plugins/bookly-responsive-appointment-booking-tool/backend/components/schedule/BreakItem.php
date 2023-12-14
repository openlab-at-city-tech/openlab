<?php
namespace Bookly\Backend\Components\Schedule;

use Bookly\Lib;

class BreakItem extends Lib\Base\Component
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $start;
    /** @var string */
    protected $end;

    /**
     * Constructor.
     *
     * @param int $id
     * @param string $start
     * @param string $end
     */
    public function __construct( $id, $start, $end )
    {
        $this->id    = $id;
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * Render break item.
     *
     * @param bool $echo
     * @return string|void
     */
    public function render( $echo = true )
    {
        return self::renderTemplate( 'break', array(
            'id'       => $this->id,
            'interval' => $this->getFormattedInterval(),
        ), $echo );
    }

    /**
     * Gets start
     *
     * @return string
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Gets end
     *
     * @return string
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Format interval.
     *
     * @return string
     */
    public function getFormattedInterval()
    {
        return Lib\Utils\DateTime::formatInterval( $this->start, $this->end );
    }
}