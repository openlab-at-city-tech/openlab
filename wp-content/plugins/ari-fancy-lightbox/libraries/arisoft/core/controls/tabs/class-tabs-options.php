<?php
namespace Ari\Controls\Tabs;

use Ari\Utils\Array_Helper as Array_Helper;

class Tabs_Options {
    public $options = null;

    public $items = array();

    public $active = -1;

    public $stateless = false;

    function __construct( $options = array() ) {
        $tabs_options = Array_Helper::get_value( $options, 'options', array() );
        $items = Array_Helper::get_value( $options, 'items', array() );

        $this->active = intval( Array_Helper::get_value( $options, 'active', -1 ), 10 );
        $this->options = new Tabs_Main_Options( $tabs_options );

        foreach ( $items as $item ) {
            $this->items[] = new Tabs_Item_Options( $item );
        }
    }
}
