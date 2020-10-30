<?php
namespace Ari_Fancy_Lightbox\Views;

use Ari\Views\View as View;
use Ari\Utils\Request as Request;

class Base extends View {
    protected $title = '';

    public function display( $tmpl = null ) {
        wp_enqueue_style( 'ari-fancy-lightbox-app' );
        wp_enqueue_script( 'ari-fancy-lightbox-app' );
        wp_enqueue_script( 'ari-fancy-lightbox-app-helper' );

        echo '<div id="ari_fancybox_plugin" class="wrap ari-theme">';

        $this->render_message();
        $this->render_title();

        parent::display( $tmpl );

        echo '</div>';
        $app_options = $this->get_app_options();

        $app_helper_options = array(
        );

        $global_app_options = array(
            'options' => $app_helper_options,

            'app' => $app_options,
        );
        wp_localize_script( 'ari-fancy-lightbox-app', 'ARI_APP', $global_app_options );
    }

    public function set_title( $title ) {
        $this->title = $title;
    }

    protected function render_title() {
        if ( $this->title )
            printf(
                '<h1 class="wp-heading-inline">%s</h1>',
                $this->title
            );
    }

    protected function render_message() {
        if ( ! Request::exists( 'msg' ) )
            return ;

        $message_type = Request::get_var( 'msg_type', ARIFANCYLIGHTBOX_MESSAGETYPE_NOTICE, 'alpha' );
        $message = Request::get_var( 'msg' );

        printf(
            '<div class="notice notice-%2$s is-dismissible"><p>%1$s</p></div>',
            $message,
            $message_type
        );
    }

    protected function get_app_options() {
        return null;
    }
}
