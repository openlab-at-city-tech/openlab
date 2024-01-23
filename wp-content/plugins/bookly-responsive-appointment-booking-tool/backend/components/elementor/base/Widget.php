<?php
namespace Bookly\Backend\Components\Elementor\Base;

use Elementor\Widget_Base;

abstract class Widget extends Widget_Base
{
    /** @var string widget name */
    protected $name;
    /** @var string widget icon */
    protected $icon;

    /**
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public static function register( $widgets_manager )
    {
        if ( method_exists( $widgets_manager, 'register' ) ) {
            $widgets_manager->register( new static() );
        } else {
            // for Elementor < 3.1
            $widgets_manager->register_widget_type( new static() );
        }

        wp_enqueue_style( 'bookly-elementor' );
    }

    /**
     * @inheritDoc
     */
    public function get_name()
    {
        return 'bookly-widget-' . $this->name;
    }

    /**
     * @inheritDoc
     */
    public function get_icon()
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function get_categories()
    {
        return array( 'bookly' );
    }
}
