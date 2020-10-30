<?php
namespace Ari\Controls\Tabs;

use Ari\Utils\Options as Options;

class Tabs_Item_Options extends Options {
    public $id = '';

    public $title = null;

    public $content = null;

    public $active = false;

    function __construct( $options = array() ) {
        parent::__construct( $options );
    }
}
