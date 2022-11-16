<?php
namespace Bookly\Backend\Modules\CloudProducts;

use Bookly\Lib;

/**
 * Class Page
 * @package Bookly\Backend\Modules\CloudProducts
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/cloud-products.js' => array( 'bookly-backend-globals', ), ),
        ) );

        $cloud = Lib\Cloud\API::getInstance();
        // Get actual products data
        $cloud->general->loadInfo();

        $js_products = array();
        $products = array();
        $subscriptions = $cloud->account->getSubscriptions();
        foreach ( $cloud->general->getProducts() as $product ) {
            $js_products[ $product['id'] ] = array(
                'title'      => $product['texts']['title'],
                'info_title' => $product['texts']['info-title'],
                'active'     => $cloud->account->productActive( $product['id'] )
            );
            // Prepare next billing date
            if ( isset ( $product['prices'] ) ) {
                $js_products[ $product['id'] ]['has_subscription'] = true;
                foreach ( $product['prices'] as $price ) {
                    foreach ( $subscriptions as $subscription ) {
                        if ( $subscription['product_price_id'] === $price['id'] ) {
                            $product['next_billing_date'] = Lib\Utils\DateTime::formatDate( $subscription['next_billing_date'] );
                            $product['cancel_on_renewal'] = isset( $subscription['cancel_on_renewal'] ) ? $subscription['cancel_on_renewal'] : false;
                            if ( isset( $subscription['usage'] ) ) {
                                $product['usage'] = $subscription['usage']['limit'] === null
                                    ? __( 'unlimited in trial', 'bookly' )
                                    : sprintf( '%d / %d', $subscription['usage']['used'], $subscription['usage']['limit'] );
                            }
                            break 2;
                        }
                    }
                }
            }
            $products[] = $product;
        }

        wp_localize_script( 'bookly-cloud-products.js', 'BooklyL10n', array(
            'csrfToken' => Lib\Utils\Common::getCsrfToken(),
            'products'  => $js_products,
            'subscriptions' => $subscriptions,
        ) );

        self::renderTemplate( 'index', compact( 'cloud', 'products' ) );
    }
}