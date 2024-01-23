<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Order;

use Bookly\Lib;

class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render()
    {
        global $wpdb;

        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'backend' => array( 'js/sortable.min.js' => array( 'bookly-backend-globals' ) ),
            'module' => array( 'js/staff-order-dialog.js' => array( 'bookly-sortable.min.js' ) ),
        ) );

        $query = Lib\Entities\Staff::query( 's' )
            ->select( 's.id, s.full_name, s.visibility = \'archive\' AS archived' )
            ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = s.wp_user_id' );

        if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
            $query->where( 's.wp_user_id', get_current_user_id() );
        }
        $staff = array();
        foreach ( $query->sortBy( 'position' )->fetchArray() as $_staff ) {
            $staff[] = array(
                'id' => $_staff['id'],
                'full_name' => esc_html( $_staff['full_name'] ),
                'archived' => $_staff['archived'],
            );
        }

        wp_localize_script( 'bookly-staff-order-dialog.js', 'BooklyStaffOrderDialogL10n', array(
            'staff' => $staff,
            'archived' => __( 'Archived', 'bookly' )
        ) );

        self::renderTemplate( 'dialog' );
    }
}