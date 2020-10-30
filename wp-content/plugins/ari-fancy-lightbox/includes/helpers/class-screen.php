<?php
namespace Ari_Fancy_Lightbox\Helpers;

class Screen {
    static public function register() {
        $screen = get_current_screen();

        $screen->add_help_tab(
            array(
                'id' => 'ari_fancybox_help_tab',
                'title'	=> __( 'Help', 'ari-fancy-lightbox' ),
                'content' => sprintf(
                    '<p>' . __( 'User\'s guide is available <a href="%s" target="_blank">here</a>.', 'ari-fancy-lightbox') . '</p>',
                    'http://www.ari-soft.com/docs/wordpress/ari-fancy-lightbox/v1/en/index.html'
                )
            )
        );
    }
}
