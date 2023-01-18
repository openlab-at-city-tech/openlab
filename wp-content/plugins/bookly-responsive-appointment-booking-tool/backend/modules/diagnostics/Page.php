<?php

namespace Bookly\Backend\Modules\Diagnostics;

use Bookly\Lib;

/**
 * Class Diagnostics
 *
 * @package Bookly\Backend\Modules\Diagnostics
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
            'module' => array( 'css/style.css' ),
        ) );

        self::enqueueScripts( array(
            'module' => array(
                'js/diagnostics.js' => array( 'bookly-backend-globals' ),
                'js/database.js' => array( 'bookly-backend-globals' ),
            ),
        ) );

        $tools = array();
        foreach ( glob( __DIR__ . '/tools/*.php' ) as $path ) {
            $test = basename( $path, '.php' );
            if ( $test !== 'Tool' ) {
                $class_name = '\Bookly\Backend\Modules\Diagnostics\Tools\\' . $test;
                if ( class_exists( $class_name, true ) ) {
                    $class = new $class_name;
                    if ( ! $class->isHidden() || self::hasParameter( 'debug' ) ) {
                        $tools[] = $class;
                    }
                }
            }
        }

        $tests = array();
        foreach ( glob( __DIR__ . '/tests/*.php' ) as $path ) {
            $test = basename( $path, '.php' );
            if ( $test !== 'Test' ) {
                $class_name = '\Bookly\Backend\Modules\Diagnostics\Tests\\' . $test;
                if ( class_exists( $class_name, true ) ) {
                    $class = new $class_name;
                    if ( ! $class->isHidden() || self::hasParameter( 'debug' ) ) {
                        $tests[] = $class;
                    }
                }
            }
        }

        self::renderTemplate( 'index', compact( 'tests', 'tools' ) );
    }

    /**
     * Show 'Diagnostics' submenu inside Bookly main menu
     */
    public static function addBooklyMenuItem()
    {
        $title = __( 'Diagnostics', 'bookly' );
        add_submenu_page(
            'bookly-menu', $title, $title, Lib\Utils\Common::getRequiredCapability(),
            self::pageSlug(), function () { Page::render(); }
        );
    }
}