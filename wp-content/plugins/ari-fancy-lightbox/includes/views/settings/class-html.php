<?php
namespace Ari_Fancy_Lightbox\Views\Settings;

use Ari_Fancy_Lightbox\Views\Base as Base;
use Ari\Controls\Tabs\Tabs as Tabs;
use Ari\Wordpress\Woocommerce as Woocommerce_Helper;
use Ari\Wordpress\Nextgen as Nextgen_Helper;

class Html extends Base {
    public $tabs;

    public function display( $tmpl = null ) {
        $this->set_title( __( 'ARI Fancy Lightbox - Settings', 'ari-fancy-lightbox' ) );

        wp_enqueue_style( 'ari-qtip' );

        $this->init_tabs();

        parent::display( $tmpl );
    }

    protected function groups_output( $groups ) {
        $data = $this->get_data();
        $form = $data['form'];

        return $form->groups_output( $groups );
    }

    public function init_tabs() {
        $tab_options = array(
            'items' => array(
                array(
                    'id' => 'integration',

                    'title' => __( 'Integration', 'ari-fancy-lightbox' ),

                    'content' => function() {
                        $groups = array(
                            'wp_gallery',

                            'wp_gallery_config',
                        );

                        if ( Nextgen_Helper::is_installed_v2() ) {
                            $groups[] = 'nextgen';
                        }

                        $groups[] = 'images';

                        if ( Woocommerce_Helper::is_installed() ) {
                            $groups[] = 'woocommerce';
                        }

                        $groups = array_merge(
                            $groups,
                            array(
                                'youtube',

                                'vimeo',

                                'metacafe',

                                'dailymotion',

                                'vine',

                                'instagram',

                                'google_maps',

                                'pdf',

                                'pdf_config',

                                'links',
                            )
                        );

                        $groups = apply_filters( 'ari-fancybox-tab-integration-groups', $groups );

                        return $this->groups_output( $groups );
                    },
                ),

                array(
                    'id' => 'lightbox',

                    'title' => __( 'Lightbox', 'ari-fancy-lightbox' ),

                    'content' => function() {
                        $groups = array(
                            'lightbox',

                            'lightbox_buttons',
                        );

                        $groups = apply_filters( 'ari-fancybox-tab-lightbox-groups', $groups );

                        return $this->groups_output( $groups );
                    },
                ),

                array(
                    'id' => 'style',

                    'title' => __( 'Style', 'ari-fancy-lightbox' ),

                    'content' => function() {
                        $groups = array(
                            'style',
                        );

                        $groups = apply_filters( 'ari-fancybox-tab-style-groups', $groups );

                        return $this->groups_output( $groups );
                    },
                ),

                array(
                    'id' => 'advanced',

                    'title' => __( 'Advanced', 'ari-fancy-lightbox' ),

                    'content' => function() {
                        $groups = array(
                            'advanced',
                        );

                        $groups = apply_filters( 'ari-fancybox-tab-advanced-groups', $groups );

                        return $this->groups_output( $groups );
                    },
                ),

                array(
                    'id' => 'upgrade',

                    'title' => __( 'Upgrade', 'ari-fancy-lightbox'),

                    'content' => function() {
                        return '<p>Like the plugin, but need more features? <a href="http://wp-quiz.ari-soft.com/plugins/wordpress-fancy-lightbox.html#pricing" target="_blank">Upgrade to PRO</a> version:
                                <ul class="ari-features">
                                    <li><strong>Deeplinking</strong> creates unique links for lightbox items. Possible to open the lightbox by a deeplink.</li>
                                    <li><strong>Social networks integration</strong> share content via popular social networks: Facebook, Twitter, Google+, Pinterest, LinkedIn, VKontakte.</li>
                                    <li><strong>Facebook comment plugin integration</strong> helps to comment any content directly into the lightbox.</li>
                                    <li><strong>Disable the lightbox on small screens</strong>.</li>
                                    <li><strong>Show popup on page load automatically</strong>.</li>
                                    <li>and other awesome features.</li>
                                </ul>
                                <div class="afb-upgrade-pro-toolbar">
                                    <a href="http://wp-quiz.ari-soft.com/plugins/wordpress-fancy-lightbox.html#pricing" target="_blank" class="button">Upgrade to PRO</a>
                                </div>
                            </p>';
                    }
                )
            )
        );

        $tab_options = apply_filters( 'ari-fancybox-tabs-options', $tab_options );

        $tabs = new Tabs(
            'afl_settings_tabs',

            $tab_options
        );

        $this->tabs = $tabs;
    }
}
