<?php
namespace Bookly\Frontend\Components\Payment;

use Bookly\Lib as BooklyLib;

/**
 * Class Gateway
 * @package Bookly\Frontend\Components\Payment
 */
class Gateway extends BooklyLib\Base\Component
{
    /**
     * @param string $form_id
     * @param string $gateway
     * @param string $page_url
     * @param null|string $bookly_action
     * @return void
     */
    public static function renderForm( $form_id, $gateway, $page_url, $bookly_action = null )
    {
        $userData = new BooklyLib\UserBookingData( $form_id );
        if ( $userData->load() ) {
            if ( ! $bookly_action ) {
                $bookly_action = $gateway . '-checkout';
            }
            $replacement = array(
                '%form_id%' => $form_id,
                '%response_url%' => esc_attr( $page_url ),
                '%bookly_action%' => $bookly_action,
                '%gateway%' => $gateway,
                '%back%' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_button_back' ),
                '%next%' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_payment_button_next' ),
                '%align_class%' => get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right',
            );
            $form = '<div class="bookly-gateway-buttons pay-%gateway% bookly-box bookly-nav-steps" style="display:none">
            <form method="post" class="bookly-%gateway%-form">
                <input type="hidden" name="bookly_fid" value="%form_id%"/>
                <input type="hidden" name="bookly_action" value="%bookly_action%"/>
                <input type="hidden" name="response_url" value="%response_url%"/>
                <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" style="margin-right: 10px;" data-spinner-size="40"><span class="ladda-label">%back%</span></button>
                <div class="%align_class%">
                    <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40"><span class="ladda-label">%next%</span></button>
                </div>
             </form></div>';
            echo strtr( $form, $replacement );
        }
    }
}