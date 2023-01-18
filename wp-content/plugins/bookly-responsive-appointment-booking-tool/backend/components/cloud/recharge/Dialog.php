<?php
namespace Bookly\Backend\Components\Cloud\Recharge;

use Bookly\Lib;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Cloud\Recharge
 */
class Dialog extends Lib\Base\Component
{

    public static function render()
    {
        $cloud = Lib\Cloud\API::getInstance();
        if ( $cloud->account->loadProfile() ) {
            self::enqueueStyles( array(
                'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
            ) );

            self::enqueueScripts( array(
                'module' => array( 'js/recharge-dialog.js' => array( 'bookly-backend-globals' ), ),
            ) );

            $recharge = $cloud->account->getRechargeData();
            wp_localize_script( 'bookly-recharge-dialog.js', 'BooklyRechargeDialogL10n', array(
                'country' => $cloud->account->getCountry(),
                'no_card' => $recharge['no_card'],
                'payment' => array(
                    'manual' => array(
                        'action' => __( 'Pay using', 'bookly' ),
                        'accepted' => __( 'Your payment has been accepted for processing', 'bookly' ),
                        'cancelled' => __( 'Your payment has been cancelled', 'bookly' ),
                    ),
                    'auto' => array(
                        'action' => __( 'Continue with', 'bookly' ),
                        'cancelled' => __( 'Auto-Recharge has been cancelled', 'bookly' ),
                        'enabled' => __( 'Auto-Recharge has been enabled', 'bookly' ),
                        'renewed' => __( 'Auto-Recharge has been renewed', 'bookly' ),
                    ),
                ),
                'auto_recharge' => array(
                    'enabled' => $cloud->account->autoRechargeEnabled(),
                    'amount' => $cloud->account->getAutoRechargeAmount(),
                    'bonus' => $cloud->account->getAutoRechargeBonus(),
                ),
                'dont_have_auto_recharge' => __( 'You don\'t have active auto-recharge', 'bookly' ),
            ) );

            self::renderTemplate( 'dialog', compact( 'cloud' ) );
        }
    }
}