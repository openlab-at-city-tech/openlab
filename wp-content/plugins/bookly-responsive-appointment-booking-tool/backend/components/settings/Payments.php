<?php
namespace Bookly\Backend\Components\Settings;

use Bookly\Lib;

/**
 * Class Payments
 * @package Bookly\Backend\Components\Settings
 */
class Payments extends Lib\Base\Component
{
    /**
     * Render discount and deduction for payment gateway.
     *
     * @param string $gateway
     */
    public static function renderPriceCorrection( $gateway )
    {
        self::renderTemplate( 'price_correction', compact( 'gateway' ) );
    }

    /**
     * Render tax settings for payment gateway.
     *
     * @param string $gateway
     */
    public static function renderTax( $gateway )
    {
        if ( Lib\Config::taxesActive() ) {
            Selects::renderSingle(
                'bookly_' . $gateway . '_send_tax',
                __( 'Send tax information', 'bookly' ),
                null,
                array(
                    array( 0, __( 'No', 'bookly' ) ),
                    array( 1, __( 'Yes', 'bookly' ) ),
                )
            );
        }
    }
}