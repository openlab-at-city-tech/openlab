<?php
namespace Bookly\Backend\Components\Schedule;

use Bookly\Lib;

class Range extends Lib\Base\Component
{
    /** @var string */
    protected $start;
    /** @var string */
    protected $end;

    /**
     * Constructor.
     *
     * @param string $start
     * @param string $end
     */
    public function __construct( $start, $end )
    {
        $this->start = $start;
        $this->end   = $end;
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
}