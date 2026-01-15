<?php
namespace Bookly\Backend\Modules\Shop;

use Bookly\Lib;

class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'tailwind/tailwind.css', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/addons.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $has_new_items = Lib\Entities\Shop::query()
            ->whereGt( 'published', date_create( 'now' )->modify( '-2 weeks' )->format( 'Y-m-d H:i:s' ) )
            ->where( 'seen', 0, 'OR' )
            ->count();

        wp_localize_script( 'bookly-addons.js', 'BooklyL10nAddonsForm', array(
            'l10n' => array(
                'addons_subtitle' => __( 'Discover features to extend your Bookly functionality', 'bookly' ),
                'addons_title' => __( 'Explore Bookly products', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
                'demo' => __( 'Demo', 'bookly' ),
                'detach' => __( 'Detach', 'bookly' ),
                'detach_info' => __( 'You are going to detach your purchase code from this domain', 'bookly' ),
                'enter_purchase_code' => __( 'Enter purchase code', 'bookly' ),
                'get_it' => __( 'Get it', 'bookly' ),
                'installed' => __( 'Installed', 'bookly' ),
                'installed_subtitle' => __( 'View and manage your active bundles and add-ons', 'bookly' ),
                'installed_title' => __( 'Bundles and add-ons', 'bookly' ),
                'lifetime' => __( 'Lifetime', 'bookly' ),
                'new' => __( 'New', 'bookly' ),
                'subscription' => __( 'Subscription', 'bookly' ),
            )
        ) );

        self::renderTemplate( 'index', compact( 'has_new_items' ) );
    }

    /**
     * @return int
     */
    public static function getNotSeenCount()
    {
        if ( isset ( $_REQUEST['page'] ) && $_REQUEST['page'] === self::pageSlug() ) {
            return 0;
        }

        return Lib\Entities\Shop::query()
            ->where( 'seen', 0 )
            ->count();
    }

    /**
     * Show 'Add-ons' submenu with counter inside Bookly main menu
     */
    public static function addBooklyMenuItem()
    {
        $title = __( 'Add-ons', 'bookly' );
        $count = self::getNotSeenCount();
        if ( $count ) {
            add_submenu_page( 'bookly-menu', $title, sprintf( '%s <span class="update-plugins count-%d"><span class="update-count">%d</span></span>', $title, $count, $count ), Lib\Utils\Common::getRequiredCapability(),
                self::pageSlug(), function() { Page::render(); } );
        } else {
            add_submenu_page( 'bookly-menu', $title, $title, Lib\Utils\Common::getRequiredCapability(),
                self::pageSlug(), function() { Page::render(); } );
        }
    }
}