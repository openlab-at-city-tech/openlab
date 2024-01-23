<?php
namespace Bookly\Backend\Components\Settings;

class Menu
{
    /**
     * Render menu item on settings page.
     *
     * @param string $title
     * @param string $tab
     */
    public static function renderItem( $title, $tab )
    {
        printf( '<a class="nav-link mb-2" href="#bookly_settings_%s" data-toggle="bookly-pill">%s</a>',
            $tab,
            esc_html( $title )
        );
    }
}