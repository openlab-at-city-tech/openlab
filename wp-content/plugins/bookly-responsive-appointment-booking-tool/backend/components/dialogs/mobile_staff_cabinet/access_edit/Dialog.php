<?php
namespace Bookly\Backend\Components\Dialogs\MobileStaffCabinet\AccessEdit;

use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib;

class Dialog extends Lib\Base\Component
{
    /**
     * Render create/edit access dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/mobile-staff-cabinet-dialog.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $staff_members = Lib\Entities\Staff::query()
            ->select( 'id, full_name' )
            ->sortBy( 'full_name' )
            ->whereNot( 'visibility', 'archive' )
            ->where( 'cloud_msc_token', null )
            ->fetchArray();

        wp_localize_script( 'bookly-mobile-staff-cabinet-dialog.js', 'BooklyL10nMobileStaffCabinet', array(
            'edit' => __( 'Edit access token', 'bookly' ),
            'new' => __( 'New access token', 'bookly' ),
            'staff_required' => __( 'Staff member required', 'bookly' ),
            'staff_members' => $staff_members
        ) );

        self::renderTemplate( 'edit' );
    }

    /**
     * Render button
     */
    public static function renderNewToken()
    {
        print '<div class="col-auto">';
        Buttons::renderAdd( 'bookly-js-new-key', 'btn-success', __( 'New access token', 'bookly' ) );
        print '</div>';
    }
}