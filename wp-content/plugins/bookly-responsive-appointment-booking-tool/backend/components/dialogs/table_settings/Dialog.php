<?php
namespace Bookly\Backend\Components\Dialogs\TableSettings;

use Bookly\Lib;

class Dialog extends Lib\Base\Component
{
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'backend' => array( 'js/sortable.min.js' => array( 'bookly-backend-globals' ), ),
            'module' => array( 'js/table-settings-dialog.js' => array( 'bookly-sortable.min.js' ) ),
        ) );

        self::renderTemplate( 'dialog' );
    }

    /**
     * Render 'settings' button
     *
     * @param string $table_name
     * @param string $setting_name
     * @param string $location
     */
    public static function renderButton( $table_name, $setting_name = 'BooklyL10n', $location = '' )
    {
        self::renderTemplate( 'button', compact( 'table_name', 'setting_name', 'location' ) );
    }
}