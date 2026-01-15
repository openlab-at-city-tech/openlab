<?php
namespace Bookly\Backend\Modules\Shop;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Get data for shop page.
     */
    public static function getShopData()
    {
        $response = array(
            'shop' => array(),
        );
        $query = Lib\Entities\Shop::query()
            ->sortBy( 'priority DESC, published' )
            ->order( 'DESC' );
        $shop = $query->fetchArray();
        if ( count( $shop ) == 0 ) {
            Lib\Routines::handleDailyInfo();
            $shop = $query->fetchArray();
        }

        // Get a list of installed plugins
        $plugins_installed = array_keys( apply_filters( 'bookly_plugins', array() ) );
        foreach ( glob( Lib\Plugin::getDirectory() . '/../bookly-addon-*', GLOB_ONLYDIR ) as $path ) {
            $plugins_installed[] = basename( $path );
        }

        // Build a list of plugins for a shop page
        foreach ( $shop as $plugin ) {
            $response['shop'][] = array(
                'title' => $plugin['title'],
                'slug' => $plugin['slug'],
                'demo_url' => $plugin['demo_url'],
                'description' => $plugin['description'],
                'license' => get_option( str_replace( array( '-addon', '-' ), array( '', '_' ), $plugin['slug'] ) . '_purchase_code' ),
                'id' => $plugin['plugin_id'],
                'bundle_plugins' => $plugin['bundle_plugins'] ? json_decode( $plugin['bundle_plugins'] ) : array(),
                'icon_url' => $plugin['icon'],
                'image' => $plugin['image'],
                'new' => ( $plugin['seen'] == 0 || ( strtotime( $plugin['published'] ) > strtotime( '-2 weeks' ) ) ) ? __( 'New', 'bookly' ) : '',
                'price' => '$' . $plugin['price'],
                'sub_price' => $plugin['sub_price'],
                'sales' => sprintf( _n( '%d sale', '%d sales', $plugin['sales'], 'bookly' ), $plugin['sales'] ),
                'rating' => $plugin['rating'],
                'reviews' => sprintf( _n( '%d review', '%d reviews', $plugin['reviews'], 'bookly' ), $plugin['reviews'] ),
                'installed' => in_array( $plugin['slug'], $plugins_installed ),
                'visible' => $plugin['visible'],
                'url' => Lib\Utils\Common::prepareUrlReferrers( $plugin['url'] . '?ref=ladela', 'shop' ),
            );
        }

        foreach ( $response['shop'] as $plugin ) {
            if ( $plugin['bundle_plugins'] ) {
                foreach ( $response['shop'] as &$_plugin ) {
                    if ( ! $_plugin['bundle_plugins'] && $_plugin['license'] === $plugin['license'] ) {
                        $_plugin['license'] = false;
                    }
                }
            }
        }

        // Mark all plugins as seen
        Lib\Entities\Shop::query()->update()->set( 'seen', 1 )->execute();

        wp_send_json_success( $response );
    }
}