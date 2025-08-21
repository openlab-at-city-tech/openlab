<?php
namespace Bookly\Lib\Utils;

abstract class Advertisement
{
    public static function render( $internal_id, $show = true, $echo = true )
    {
        if ( $show ) {
            $ads = get_option( 'bookly_advertisement', array() );
            $ad_data = array();
            if ( ! $ads || ! is_array( $ads ) ) {
                $ad_data = array(
                    'texts' => array(
                        'content' => sprintf( __( 'To get access to more <a href="%s" target="_blank">features</a>, lifetime free updates and 24/7 support, upgrade to the Pro version of Bookly.', 'bookly' ), 'https://api.booking-wp-plugin.com/go/bookly-addon-pro' ),
                        'button' => __( 'Upgrade', 'bookly' ),
                    ),
                    'button_url' => 'https://api.booking-wp-plugin.com/go/bookly-pricing',
                );
            } else {
                foreach ( $ads as $ad ) {
                    if ( $ad['internal_id'] == $internal_id ) {
                        $ad_data = $ad;
                        break;
                    }
                }
            }
            if ( $ad_data ) {
                // Start output buffering.
                ob_start();
                ob_implicit_flush( 0 );

                printf( '<div class="alert alert-info"><div class="row"><div class="col-12 col-sm-8 align-content-center">%s</div><div class="col-12 col-sm-4 text-right align-self-sm-center"><a href="%s" class="btn btn-info mt-4 mt-sm-0" target="_blank">%s</a></div></div></div>', $ad_data['texts']['content'], $ad_data['button_url'], $ad_data['texts']['button'] );

                if ( ! $echo ) {
                    return ob_get_clean();
                }
                echo ob_get_clean();
            }
        }
    }

    public static function isVisible( $internal_id )
    {
        $ads = get_option( 'bookly_advertisement', array() );
        if ( ! $ads || ! is_array( $ads ) ) {
            return true;
        } else {
            foreach ( $ads as $ad ) {
                if ( $ad['internal_id'] == $internal_id ) {
                    return true;
                }
            }
        }

        return false;
    }
}