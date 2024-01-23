<?php
namespace Bookly\Backend\Components\Dialogs\Service\Edit;

use Bookly\Lib;
use Bookly\Backend\Modules\Services\Page;

class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'backend' => array(
                'js/nav-scrollable.js' => array( 'bookly-backend-globals' ),
                'js/sortable.min.js' => array( 'bookly-backend-globals' ),
            ),
            'module' => array( 'js/service-edit-dialog.js' => array( 'bookly-sortable.min.js' ) ),
        ) );

        // Allow add-ons to enqueue their assets.
        Proxy\Shared::enqueueAssetsForServices();

        $staff = array();
        foreach ( Page::getStaffDropDownData() as $category ) {
            foreach ( $category['items'] as $employee ) {
                $staff[ $employee['id'] ] = $employee['full_name'];
            }
        }

        wp_localize_script( 'bookly-service-edit-dialog.js', 'BooklyServiceEditDialogL10n', compact( 'staff' ) );

        self::renderTemplate( 'dialog' );
    }
}