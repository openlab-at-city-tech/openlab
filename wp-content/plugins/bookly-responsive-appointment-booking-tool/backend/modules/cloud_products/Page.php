<?php
namespace Bookly\Backend\Modules\CloudProducts;

use Bookly\Lib;

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
            $active = $cloud->account->productActive( $product['id'] );
            $js_products[ $product['id'] ] = array(
                'title' => $product['texts']['title'],
                'info_title' => $product['texts']['info-title'],
                'active' => $active,
            );
            // Prepare next billing date
            if ( isset ( $product['prices'] ) ) {
                $has_product_price_id = false;
                $js_products[ $product['id'] ]['has_subscription'] = true;
                foreach ( $product['prices'] as $price ) {
                    foreach ( $subscriptions as $subscription ) {
                        if ( $subscription['product_price_id'] === $price['id'] ) {
                            $product['next_billing_date'] = Lib\Utils\DateTime::formatDate( $subscription['next_billing_date'] );
                            $product['cancel_on_renewal'] = isset( $subscription['cancel_on_renewal'] ) ? $subscription['cancel_on_renewal'] : false;
                            if ( isset( $subscription['usage'] ) ) {
                                if ( $product['id'] === 'whatsapp' ) {
                                    $prefix = __( 'Messages', 'bookly' );
                                } else {
                                    $prefix = __( 'Tasks', 'bookly' );
                                }
                                if ( $subscription['usage']['limit'] === null ) {
                                    $product['usage'] = sprintf( '%s: %s', $prefix, __( 'unlimited in trial', 'bookly' ) );
                                } else {
                                    $product['usage'] = sprintf( '%s: %d / %d', $prefix, $subscription['usage']['used'], $subscription['usage']['limit'] );
                                }
                            }
                            $has_product_price_id = true;
                            break 2;
                        }
                    }
                }
                if ( isset( $product['accept_pc'] ) && $product['accept_pc'] && $active && ! $has_product_price_id ) {
                    $js_products[ $product['id'] ]['activated_by_pc'] = true;
                }
            }
            $products[] = $product;
        }

        wp_localize_script( 'bookly-cloud-products.js', 'BooklyL10n', array(
            'products' => $js_products,
            'subscriptions' => $subscriptions,
        ) );

        self::renderTemplate( 'index', compact( 'cloud', 'products' ) );
    }
}