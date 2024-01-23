<?php
namespace Bookly\Frontend\Components\Payment;

use Bookly\Lib as BooklyLib;

class Gateway extends BooklyLib\Base\Component
{
    /**
     * @param string $form_id
     * @param string $gateway_slug
     * @return void
     */
    public static function renderForm( $form_id, $gateway_slug )
    {
        $userData = new BooklyLib\UserBookingData( $form_id );
        if ( $userData->load() ) {
            $replacement = array(
                '%gateway%' => str_replace( array( 'bookly-addon-', '-' ), array( '', '_' ), $gateway_slug ),
                '%back%' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_button_back' ),
                '%next%' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_payment_button_next' ),
                '%align_class%' => get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right',
            );

            $html = '<div class="bookly-gateway-buttons pay-%gateway% bookly-box bookly-nav-steps" style="display:none">
                <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" style="margin-right: 10px;" data-spinner-size="40"><span class="ladda-label">%back%</span></button>
                <div class="%align_class%">
                    <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40"><span class="ladda-label">%next%</span></button>
                </div>
             </div>';
            echo strtr( $html, $replacement );
        }
    }
}