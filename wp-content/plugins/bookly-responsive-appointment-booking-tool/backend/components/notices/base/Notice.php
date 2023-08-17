<?php
namespace Bookly\Backend\Components\Notices\Base;

use Bookly\Lib;
use Bookly\Backend\Modules;

/**
 * Class Notice
 * @package Bookly\Backend\Components\Notices\Base
 */
class Notice extends Lib\Base\Component
{
    protected $id;
    protected $title;
    protected $sub_title;
    protected $message;
    protected $buttons = array();
    protected $dismiss_js_class;
    protected $hidden;

    /**
     * Render notice
     */
    public function render()
    {
        Notice::renderTemplate( 'notice', array(
            'id'        => $this->id,
            'buttons'   => $this->buttons,
            'dismiss_js_class' => $this->dismiss_js_class,
            'message'   => $this->message,
            'hidden'    => $this->hidden,
            'sub_title' => $this->sub_title,
            'title'     => $this->title,
        ) );
    }

    /**
     * Create object
     *
     * @param string $id
     * @return Notice
     */
    public static function create( $id )
    {
        $block = new static();
        $block->id = $id;

        return $block;
    }

    /**
     * Set notice title and sub title.
     *
     * @param string $title
     * @param string $sub_title
     * @return $this
     */
    public function setTitle( $title, $sub_title = null )
    {
        $this->title     = $title;
        $this->sub_title = $sub_title;

        return $this;
    }

    /**
     * Set notice message.
     *
     * @param string $message
     * @return $this
     */
    public function setMessage( $message )
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Add button.
     *
     * @param string $caption
     * @param string $js_class
     * @return $this
     */
    public function addMainButton( $caption, $js_class )
    {
        $this->buttons[] = array(
            'caption' => $caption,
            'class'   => 'btn-success ' . $js_class,
        );

        return $this;
    }

    /**
     * Add button.
     *
     * @param string $caption
     * @param string $js_class
     * @return $this
     */
    public function addDefaultButton( $caption, $js_class )
    {
        $this->buttons[] = array(
            'caption' => $caption,
            'class'   => 'btn-default ' . $js_class,
        );

        return $this;
    }

    /**
     * Set dismiss class name.
     *
     * @param string $js_class
     * @return $this
     */
    public function setDismissClass( $js_class )
    {
        $this->dismiss_js_class = $js_class;

        return $this;
    }

    /**
     * Hide notice
     *
     * @return $this
     */
    public function hidden()
    {
        $this->hidden = true;

        return $this;
    }

    /**
     * Get user meta
     *
     * @param $user_meta
     * @return mixed
     */
    protected function getUserMeta( $user_meta )
    {
        return get_user_meta( get_current_user_id(), $user_meta, true );
    }

}