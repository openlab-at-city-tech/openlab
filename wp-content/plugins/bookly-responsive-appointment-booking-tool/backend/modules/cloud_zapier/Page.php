<?php
namespace Bookly\Backend\Modules\CloudZapier;

use Bookly\Lib;
use Bookly\Backend\Components;

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
        } elseif ( $cloud->account->productActive( Lib\Cloud\Account::PRODUCT_ZAPIER ) ) {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );

            self::enqueueScripts( array(
                'module' => array( 'js/zapier.js' => array( 'bookly-backend-globals', ), ),
            ) );

            wp_localize_script( 'bookly-zapier.js', 'BooklyL10n', array(
                'areYouSure' => __( 'Are you sure?', 'bookly' ),
            ) );

            self::renderTemplate( 'index' );
        } else {
            Lib\Utils\Common::redirect( add_query_arg(
                    array( 'page' => \Bookly\Backend\Modules\CloudProducts\Page::pageSlug() ),
                    admin_url( 'admin.php' ) )
            );
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