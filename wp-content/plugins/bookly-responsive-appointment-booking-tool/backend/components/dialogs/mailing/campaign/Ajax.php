<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\Campaign;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\Mailing\Campaign
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Save campaign.
     */
    public static function saveCampaign()
    {
        $mode = self::parameter( 'mode' );
        $campaign = new Lib\Entities\MailingCampaign();
        $id = self::parameter( 'id' );
        if ( $id ) {
            $campaign->load( $id );
        }
        $campaign
            ->setName( self::parameter( 'name' ) )
            ->setSendAt( $mode == 'at_time' ? self::parameter( 'send_at' ) : current_time( 'mysql' ) )
            ->setText( self::parameter( 'text' ) )
            ->setState( Lib\Entities\MailingCampaign::STATE_PENDING )
            ->setMailingListId( self::parameter( 'mailing_list_id' ) ?: null )
            ->save();

        if ( $mode == 'immediately' ) {
            Lib\Routines::mailing();
        }

        wp_send_json_success();
    }

    /**
     * Cancel campaign.
     */
    public static function cancelCampaign()
    {
        $campaign_id = self::parameter( 'id' );

        Lib\Entities\MailingQueue::query()
            ->delete()
            ->where( 'campaign_id', $campaign_id )
            ->where( 'sent', '0' )
            ->execute();

        Lib\Entities\MailingCampaign::query()
            ->update()
            ->set( 'state', Lib\Entities\MailingCampaign::STATE_CANCELED )
            ->where( 'id', $campaign_id )
            ->execute();

        wp_send_json_success();
    }

    /**
     * Get campaign data
     */
    public static function getCampaignData()
    {
        $mailing_lists = Lib\Entities\MailingList::query()
            ->select( 'id, name' )->fetchArray();
        $campaign = new Lib\Entities\MailingCampaign();
        $id = self::parameter( 'campaign_id' );
        if ( $id ) {
            $campaign->load( $id );
        } else {
            $mailing_lists = array_merge( array( array( 'id' => null, 'name' => __( 'Select mailing list', 'bookly' ), ), ), $mailing_lists );
        }

        wp_send_json_success( array( 'campaign' => $campaign->getFields(), 'mailing_lists' => $mailing_lists, 'current_time' => current_time( 'mysql' ) ) );
    }
}