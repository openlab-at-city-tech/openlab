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
        $response = array();
        $order = self::parameter( 'sort' );
        if ( ! Lib\Entities\Shop::query()->count() ) {
            Lib\Routines::handleDailyInfo();
            $order = 'date';
        }
        $query = Lib\Entities\Shop::query();
        switch ( $order ) {
            case 'sales':
                $query = $query
                    ->sortBy( 'priority DESC, sales' )
                    ->order( 'DESC' );
                break;
            case 'rating':
                $query = $query
                    ->sortBy( 'priority DESC, rating' )
                    ->order( 'DESC' );
                break;
            case 'date':
                $query = $query
                    ->sortBy( 'priority DESC, published' )
                    ->order( 'DESC' );
                break;
            case 'price_low':
                $query = $query
                    ->sortBy( 'priority DESC, price' );
                break;
            case 'price_high':
                $query = $query
                    ->sortBy( 'priority DESC, price' )
                    ->order( 'DESC' );
                break;
            default:
                $query = $query
                    ->sortBy( 'priority DESC, type DESC, created_at' )
                    ->order( 'DESC' );
                break;
        }
        $shop = $query->fetchArray();

        // Get a list of installed plugins
        $plugins_installed = array_keys( apply_filters( 'bookly_plugins', array() ) );
        foreach ( glob( Lib\Plugin::getDirectory() . '/../bookly-addon-*', GLOB_ONLYDIR ) as $path ) {
            $plugins_installed[] = basename( $path );
        }

        $disabled = Lib\Config::proActive() ? null : ' disabled';
        // Build a list of plugins for a shop page
        $response['shop'] = array();
        foreach ( $shop as $plugin ) {
            $installed = in_array( $plugin['slug'], $plugins_installed );
            $response['shop'][] = array(
                'plugin_class' => $plugin['highlighted'] ? 'bookly-card-highlighted border-danger' : ( $plugin['type'] == 'bundle' ? 'bg-warning' : 'bg-white' ),
                'title' => $plugin['title'],
                'demo_url_class' => $plugin['demo_url'] === null ? 'bookly-collapse' : '',
                'demo_url' => $plugin['demo_url'],
                'description' => $plugin['description'],
                'icon' => '<img src="' . $plugin['icon'] . '"/>',
                'image' => $plugin['image'],
                'new' => ( $plugin['seen'] == 0 || ( strtotime( $plugin['published'] ) > strtotime( '-2 weeks' ) ) ) ? __( 'New', 'bookly' ) : '',
                'price' => '$' . $plugin['price'],
                'sales' => sprintf( _n( '%d sale', '%d sales', $plugin['sales'], 'bookly' ), $plugin['sales'] ),
                'rating_class' => (int) $plugin['rating'] ? '' : 'bookly-collapse',
                'rating' => $plugin['rating'],
                'reviews' => sprintf( _n( '%d review', '%d reviews', $plugin['reviews'], 'bookly' ), $plugin['reviews'] ),
                'url_class' => $installed ? 'btn-default' : ( $plugin['slug'] === 'bookly-addon-pro' ? 'btn-success' : 'btn-success' . $disabled ),
                'url_text' => $installed ? __( 'Installed', 'bookly' ) : __( 'Get it!', 'bookly' ),
                'url' => Lib\Utils\Common::prepareUrlReferrers( $plugin['url'] . '?ref=ladela', 'shop' ),
            );
        }

        // Mark all plugins as seen
        Lib\Entities\Shop::query()->update()->set( 'seen', 1 )->execute();

        wp_send_json_success( $response );
    }
}