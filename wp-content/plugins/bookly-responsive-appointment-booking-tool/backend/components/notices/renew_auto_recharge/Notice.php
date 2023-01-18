<?php
namespace Bookly\Backend\Components\Notices\RenewAutoRecharge;

use Bookly\Backend\Components\Notices\Base;

/**
 * Class Notice
 * @package Bookly\Backend\Components\Notices\RenewAutoRecharge
 */
class Notice extends Base\Notice
{
    /**
     * @inheritDoc
     */
    public static function create( $id )
    {
        return parent::create( $id )
            ->addMainButton( __( 'Renew', 'bookly' ), 'bookly-js-renew-' . get_option( 'bookly_cloud_auto_recharge_gateway' ) )
            ->addDefaultButton( __( 'Dismiss', 'bookly' ), 'bookly-js-maybe-later' )
            ->setDismissClass( 'bookly-js-dismiss' )
            ;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $hide_until = (int) $this->getUserMeta( 'bookly_notice_renew_auto_recharge_hide_until' ) ?: get_option( 'bookly_cloud_renew_auto_recharge_notice_hide_until' );
        if ( $hide_until > 0
            && get_option( 'bookly_cloud_auto_recharge_gateway' )
            && ( time() >= $hide_until )
            && ( ( get_option( 'bookly_cloud_auto_recharge_end_at_ts' ) - time() ) > 0 )
        ) {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );
            self::enqueueScripts( array(
                'module' => array( 'js/renew-auto-recharge.js' => array( 'bookly-backend-globals' ), ),
            ) );

            $remaining_days = (int) ( ( get_option( 'bookly_cloud_auto_recharge_end_at_ts' ) - time() ) / DAY_IN_SECONDS );
            if ( $remaining_days > 0 ) {
                $this->setMessage(
                    sprintf( _n( 'Your Auto-Recharge will end in %d day.', 'Your Auto-Recharge will end in %d days.', $remaining_days, 'bookly' ), $remaining_days ) . ' ' . __( 'Please renew the connection to keep using Bookly Cloud services.', 'bookly' )
                );
            } else {
                $this->setMessage( __( 'Your Auto-Recharge will end today.', 'bookly' ) );
            }
            parent::render();
        }
    }

}