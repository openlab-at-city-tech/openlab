<?php
namespace Bookly\Lib\Slots;

/**
 * Class Service
 *
 * @package Bookly\Lib\Slots
 */
class Service
{
    /** @var double */
    protected $price;
    /** @var int */
    protected $capacity_min;
    /** @var int */
    protected $capacity_max;
    /** @var int */
    protected $waiting_list_capacity;
    /** @var string */
    protected $staff_preference_rule;
    /** @var int */
    protected $staff_preference_order;
    /** @var array */
    protected $staff_preference_settings;

    /**
     * Constructor.
     *
     * @param double $price
     * @param int $capacity_min
     * @param int $capacity_max
     * @param string $staff_preference_rule
     * @param array $staff_preference_settings
     * @param int $staff_preference_order
     */
    public function __construct(
        $price,
        $capacity_min,
        $capacity_max,
        $waiting_list_capacity,
        $staff_preference_rule,
        $staff_preference_settings,
        $staff_preference_order
    ) {
        $this->price = (double) $price;
        $this->capacity_min = (int) $capacity_min;
        $this->capacity_max = (int) $capacity_max;

        $this->waiting_list_capacity = $waiting_list_capacity;

        $this->staff_preference_rule = $staff_preference_rule;
        $this->staff_preference_settings = $staff_preference_settings;
        $this->staff_preference_order = $staff_preference_order;
    }

    /**
     * Gets staff preference rule
     *
     * @return string
     */
    public function getStaffPreferenceRule()
    {
        return $this->staff_preference_rule;
    }

    /**
     * Gets staff preference settings
     *
     * @return array
     */
    public function getStaffPreferenceSettings()
    {
        return $this->staff_preference_settings;
    }

    /**
     * Gets staff preference order
     *
     * @return int
     */
    public function getStaffPreferenceOrder()
    {
        return $this->staff_preference_order;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function price()
    {
        return $this->price;
    }

    /**
     * Get capacity min.
     *
     * @return int
     */
    public function capacityMin()
    {
        return $this->capacity_min;
    }

    /**
     * Get capacity max.
     *
     * @return int
     */
    public function capacityMax()
    {
        return $this->capacity_max;
    }

    /**
     * Get waiting list capacity.
     *
     * @return int
     */
    public function waitingListCapacity()
    {
        return $this->waiting_list_capacity;
    }
}