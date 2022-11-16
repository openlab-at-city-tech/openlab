<?php
namespace Bookly\Backend\Modules\News;

use Bookly\Lib;

/**
 * Class Page
 *
 * @package Bookly\Backend\Modules\News
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/news.js' => array( 'bookly-backend-globals' ) ),
        ) );

        self::renderTemplate( 'index' );
    }

    /**
     * @return int
     */
    public static function getNewsCount()
    {
        if ( isset ( $_REQUEST['page'] ) && $_REQUEST['page'] === self::pageSlug() ) {
            return 0;
        }
        
        return Lib\Entities\News::query()
            ->where( 'seen', 0 )
            ->count();
    }

    /**
     * Show 'News' submenu with counter inside Bookly main menu
     */
    public static function addBooklyMenuItem()
    {
        $news = __( 'News', 'bookly' );
        if ( get_option( 'bookly_gen_badge_consider_news' ) ) {
            $count = self::getNewsCount();
            add_submenu_page( 'bookly-menu', $news, sprintf( '%s <span class="update-plugins count-%d"><span class="update-count">%d</span></span>', $news, $count, $count ), Lib\Utils\Common::getRequiredCapability(),
                self::pageSlug(), function () { Page::render(); } );
        } else {
            add_submenu_page( 'bookly-menu', $news, $news, Lib\Utils\Common::getRequiredCapability(),
                self::pageSlug(), function () { Page::render(); } );
        }
    }

}