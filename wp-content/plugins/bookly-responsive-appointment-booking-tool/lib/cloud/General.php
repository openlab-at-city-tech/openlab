<?php

namespace Bookly\Lib\Cloud;

use Bookly\Lib\Config;

/**
 * Class General
 * @package Bookly\Lib\Cloud
 */
class General extends Base
{
    const GET_INFO                     = '/1.0/info';                                 //GET
    const GET_PRODUCT_INFO             = '/1.0/products/%product%/info';              //GET

    /** @var array */
    protected $products;
    /** @var array */
    protected $promotions;
    /** @var bool */
    protected $info_is_loaded;

    /**
     * @inheritDoc
     */
    public function setup()
    {
        $this->products   = get_option( 'bookly_cloud_products', '' );
        $this->promotions = get_option( 'bookly_cloud_promotions', '' );
    }

    /**
     * Load info.
     *
     * @return bool
     */
    public function loadInfo()
    {
        if ( $this->info_is_loaded === null ) {
            $response = $this->api->sendGetRequest( self::GET_INFO, array( 'token' => $this->api->getToken() ) );
            if ( $response ) {
                $this->products = $this->localize( 'texts', $response['products'] );
                foreach ( $this->products as &$product ) {
                    if ( isset( $product['prices'] ) ) {
                        $product['prices'] = $this->localize( 'caption', $product['prices'] );
                    }
                }
                $this->promotions = $this->localize( 'texts', $response['promotions'] );

                update_option( 'bookly_cloud_products', $this->products );
                update_option( 'bookly_cloud_promotions', $this->promotions );

                $this->info_is_loaded = true;
                $this->api->dispatch( Events::GENERAL_INFO_LOADED, $response );
            } else {
                $this->products   = array();
                $this->promotions = array();

                $this->info_is_loaded = false;
                $this->api->dispatch( Events::GENERAL_INFO_NOT_LOADED );
            }
        }

        return $this->info_is_loaded;
    }

    /**
     * Get cloud product info
     *
     * @param string $product
     * @return mixed
     */
    public function getProductInfo( $product )
    {
        $response = $this->api->sendGetRequest(
            self::GET_PRODUCT_INFO,
            array(
                '%product%' => $product,
                'locale' => Config::getShortLocale(),
            )
        );

        if ( $response ) {
            return $response['content'];
        }

        return false;
    }

    /**
     * Get products list
     *
     * @return array
     */
    public function getProducts()
    {
        if ( ! is_array( $this->products ) ) {
            $this->loadInfo();
        }

        return $this->products;
    }

    /**
     * Get promotion for displaying in a notice
     *
     * @param null &$type
     * @return string|null
     */
    public function getPromotionForNotice( &$type = null )
    {
        if ( ! is_array( $this->promotions ) ) {
            $this->loadInfo();
        }

        $dismissed  = get_user_meta( get_current_user_id(), 'bookly_dismiss_cloud_promotion_notices', true ) ?: array();
        foreach ( $this->promotions as $type => $promotion ) {
            if ( ! isset ( $dismissed[ $promotion['id'] ] ) || time() > $dismissed[ $promotion['id'] ] ) {
                return $promotion;
            }
        }

        return null;
    }

    /**
     * Go through items and set texts for the current locale only
     *
     * @param string $key
     * @param array  $items
     *
     * @return array
     */
    protected function localize( $key, $items )
    {
        $locale = Config::getLocale();
        foreach ( $items as &$item ) {
            if ( isset ( $item[ $key ] ) ) {
                if ( isset ( $item[ $key ][ $locale ] ) ) {
                    if ( is_array( $item[ $key ][ $locale ] ) ) {
                        $texts = array();
                        foreach ( $item[ $key ][ $locale ] as $k => $text ) {
                            if ( $text == '' ) {
                                $text = $item[ $key ]['en'][ $k ];
                            }
                            $texts[ $k ] = $text;
                        }
                    } else {
                        $texts = $item[ $key ][ $locale ] != '' ? $item[ $key ][ $locale ] : $item[ $key ]['en'];
                    }
                    $item[ $key ] = $texts;
                } else {
                    $item[ $key ] = $item[ $key ]['en'];
                }
            }
        }

        return $items;
    }
}