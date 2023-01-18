<?php
namespace Bookly\Backend\Modules\CloudZapier;

use Bookly\Lib;
use Bookly\Backend\Components;

/**
 * Class Page
 * @package Bookly\Backend\Modules\CloudZapier
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
            Components\Cloud\LoginRequired\Page::render( 'Zapier', self::pageSlug() );
        } elseif ( $cloud->account->productActive( 'zapier' ) ) {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );

            self::enqueueScripts( array(
                'module' => array( 'js/zapier.js' => array( 'bookly-backend-globals', ), ),
            ) );

            wp_localize_script( 'bookly-zapier.js', 'BooklyL10n', array(
                'csrfToken'  => Lib\Utils\Common::getCsrfToken(),
                'areYouSure' => __( 'Are you sure?', 'bookly' ),
            ) );

            self::renderTemplate( 'index' );
        } else {
            wp_redirect( add_query_arg(
                array( 'page' => \Bookly\Backend\Modules\CloudProducts\Page::pageSlug() ),
                admin_url( 'admin.php' ) )
            );
            exit;
        }
    }

    /**
     * Show 'Zapier' submenu with counter inside Bookly Cloud main menu.
     *
     * @param array $product
     */
    public static function addBooklyCloudMenuItem( $product )
    {
        $title = $product['texts']['title'];

        add_submenu_page(
            'bookly-cloud-menu',
            $title,
            $title,
            Lib\Utils\Common::getRequiredCapability(),
            self::pageSlug(),
            function () {
                \Bookly\Backend\Modules\CloudZapier\Page::render();
            }
        );
    }
}