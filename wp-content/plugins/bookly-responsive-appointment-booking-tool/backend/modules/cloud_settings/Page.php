<?php
namespace Bookly\Backend\Modules\CloudSettings;

use Bookly\Lib;
use Bookly\Backend\Components;

/**
 * Class Page
 * @package Bookly\Backend\Modules\CloudSettings
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        $cloud = Lib\Cloud\API::getInstance();
        if ( ! $cloud->account->loadProfile() ) {
            Components\Cloud\LoginRequired\Page::render( __( 'Bookly Cloud Settings', 'bookly' ), self::pageSlug() );
        } else {
            self::enqueueStyles( array(
                'frontend' => array( 'css/intlTelInput.css' => array( 'bookly-backend-globals' )),
            ) );

            self::enqueueScripts( array(
                'bookly' => array( 'backend/components/cloud/account/resources/js/select-country.js' => array( 'bookly-backend-globals' ) ),
                'module' => array( 'js/cloud-settings.js' => array( 'bookly-select-country.js' ) ),
            ) );

            wp_localize_script( 'bookly-cloud-settings.js', 'BooklyL10n', array(
                'country' => $cloud->account->getCountry(),
                'noResults' => __( 'No records.', 'bookly' ),
                'settingsSaved' => __( 'Settings saved.', 'bookly' ),
                'passwords_no_match' => __( 'Passwords don\'t match', 'bookly' ),
            ) );

            self::renderTemplate( 'index', compact( 'cloud' ) );
        }
    }
}