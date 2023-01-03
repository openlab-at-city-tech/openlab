<?php
namespace Bookly\Backend\Components\TinyMce;

use Bookly\Backend\Components\TinyMce\Proxy;
use Bookly\Lib;

/**
 * Class Tools
 *
 * @package Bookly\Backend\Modules\TinyMce
 */
class Tools extends Lib\Base\Component
{
    public static function init()
    {
        global $PHP_SELF;
        if ( // check if we are in admin area and current page is adding/editing the post
            is_admin() && ( strpos( $PHP_SELF, 'post-new.php' ) !== false || strpos( $PHP_SELF, 'post.php' ) !== false || strpos( $PHP_SELF, 'admin-ajax.php' ) )
        ) {
            add_action( 'admin_footer', array( '\Bookly\Backend\Components\TinyMce\Tools', 'renderPopup' ), 10, 0 );
            add_filter( 'media_buttons', array( '\Bookly\Backend\Components\TinyMce\Tools', 'addButton' ), 50, 1 );
            add_action( 'elementor/editor/footer', array( '\Bookly\Backend\Components\TinyMce\Tools', 'renderPopup' ), 10, 0 );
        }
    }

    public static function addButton( $editor_id )
    {
        // don't show on dashboard (QuickPress)
        $current_screen = get_current_screen();
        if ( $current_screen && 'dashboard' == $current_screen->base ) {
            return;
        }

        // don't display button for users who don't have access
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }

        // do a version check for the new 3.5 UI
        $version = get_bloginfo( 'version' );

        if ( $version < 3.5 ) {
            // show button for v 3.4 and below
            echo '<a href="#TB_inline?width=640&inlineId=bookly-tinymce-popup&height=650" id="add-bookly-form" title="' . esc_attr__( 'Add Bookly booking form', 'bookly' ) . '">' . __( 'Add Bookly booking form', 'bookly' ) . '</a>';
        } else {
            // display button matching new UI
            $img = '<span class="bookly-media-icon"></span> ';
            echo '<a href="#TB_inline?width=640&inlineId=bookly-tinymce-popup&height=650" id="add-bookly-form" class="thickbox button bookly-media-button" title="' . esc_attr__( 'Add Bookly booking form', 'bookly' ) . '">' . $img . __( 'Add Bookly booking form', 'bookly' ) . '</a>';
        }
        Proxy\Shared::renderMediaButtons( $version );
    }

    public static function enqueueAssets()
    {
        self::enqueueScripts( array(
            'module' => array( 'js/bookly-form-settings.js' => array( 'jquery', 'bookly-backend-globals' ), ),
        ) );

        self::enqueueData( array(
            'casest',
            'custom_location_settings',
        ) );

        wp_localize_script( 'bookly-bookly-form-settings.js', 'BooklyFormShortCodeL10n', array(
            'title' => __( 'Insert Appointment Booking Form', 'bookly' ),
        ) );
    }

    public static function renderPopup()
    {
        self::enqueueAssets();
        self::renderTemplate( 'bookly_popup' );

        Proxy\Shared::renderPopup();
    }
}