<?php
namespace Bookly\Lib\Slots;

interface IPoint
{
    /**
     * Get value.
     *
     * @return mixed
     */
    public function value();

    /**
     * Tells whether two points are equal.
     *
     * @param self $point
     * @return bool
     */
    public function eq( IPoint $point );

    /**
     * Tells whether two points are not equal.
     *
     * @param self $point
     * @return bool
     */
    public function neq( IPoint $point );

    /**
     * Tells whether one point is less than another.
     *
     * @param self $point
     * @return bool
     */
    public function lt( IPoint $point );

    /**
     * Tells whether one point is less or equal than another.
     *
     * @param self $point
     * @return bool
     */
    public function lte( IPoint $point );

    /**
     * Tells whether one point is greater than another.
     *
     * @param self $point
     * @return bool
     */
    public function gt( IPoint $point );

    /**
     * Tells whether one point is greater or equal than another.
     *
     * @param self $point
     * @return bool
     */
    public function gte( IPoint $point );

    /**
     * Computes difference between two points.
     *
     * @param self $point
     * @return int
     */
    public function diff( IPoint $point );

    /**
     * Modify point.
     *
     * @param mixed $value
     * @return self
     */
    public function modify( $value );

    /**
     * Convert point to WP time zone.
     *
     * @return self
     */
    public function toWpTz();

    /**
     * Convert point to client time zone.
     *
     * @return self
     */
    public function toClientTz();
}