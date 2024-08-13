<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib\Base\Component;

abstract class Tool extends Component
{
    /** @var bool */
    protected $hidden = false;
    /** @var string */
    protected $slug;
    /** @var string */
    protected $title;
    /** @var int */
    public $position;

    /**
     * Render template
     *
     * @return string
     */
    public function render()
    {
        return '';
    }

    /**
     * Get tool slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get tool title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get tool broken.
     *
     * @return bool
     */
    public function hasError()
    {
        return false;
    }

    /**
     * Get tool hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }
}