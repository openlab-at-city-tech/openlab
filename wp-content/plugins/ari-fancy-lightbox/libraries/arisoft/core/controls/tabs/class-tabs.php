<?php
namespace Ari\Controls\Tabs;

use Ari\Utils\Request as Request;

class Tabs {
    static protected $assets_loaded = false;

    protected $id = null;

    protected $options = null;

    function __construct( $id, $options ) {
        $this->id = $id;
        $this->options = new Tabs_Options( $options );

        if ( ! $this->options->stateless && $this->options->active < 0 ) {
            $state_id = $this->get_state_ctrl_id();
            $active = -1;
            if ( Request::exists( $state_id ) ) {
                $active = intval( Request::get_var( $state_id, -1 ), 10 );
            } else {
                $active = intval(get_transient( $state_id ), 10);

                if ( false !== $active) {
                    delete_transient( $state_id );
                } else {
                    $active = -1;
                }
            }
            $this->options->active = $active;
        }

        $this->prepare_items();
    }

    protected function prepare_items() {
        $has_active = false;

        foreach ( $this->options->items as $item ) {
            if ( $item->active ) {
                if ( $has_active ) {
                    $item->active = false;
                } else {
                    $has_active = true;
                }
            }
        }

        if ( ! $has_active ) {
            $item_count = count( $this->options->items );
            if ( $item_count > 0 ) {
                $active_item_index = $this->options->active > -1 && $item_count > $this->options->active ? $this->options->active : 0;

                $this->options->items[$active_item_index]->active = true;
            }
        }
    }

    public function render() {
        $this->load_assets();

        require dirname( __FILE__ ) . '/tmpl/tabs.php';
    }

    static public function load_assets() {
        if ( self::$assets_loaded ) {
            return ;
        }

        wp_enqueue_script( 'ari-wp-tabs' );

        self::$assets_loaded = true;
    }

    public function get_state_ctrl_id() {
        return $this->id . '_state';
    }
}
