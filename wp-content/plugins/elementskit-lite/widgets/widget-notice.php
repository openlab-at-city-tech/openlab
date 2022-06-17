<?php 
namespace ElementsKit_Lite\Widgets;
defined( 'ABSPATH' ) || exit;

trait Widget_Notice{
    /**
     * Adding Go Premium message to all widgets
     *
     * @since 1.4.2
     */
    public function insert_pro_message()
    {
        if(\ElementsKit_Lite::package_type() != 'pro'){
            $this->start_controls_section(
                'ekit_section_pro',
                [
                    'label' => __('Go Premium for More Features', 'elementskit-lite'),
                ]
            );

            $this->add_control(
                'ekit_control_get_pro',
                [
                    'label' => __('Unlock more possibilities', 'elementskit-lite'),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
                    'options' => [
                        '1' => [
                            'title' => '',
                            'icon' => 'fa fa-unlock-alt',
                        ],
                    ],
                    'default' => '1',
                    'toggle'    => false,
                    'description' => '<span class="ekit-widget-pro-feature"> Get the  <a href="https://wpmet.com/elementskit-pricing" target="_blank">Pro version</a> for more awesome elements and powerful modules.</span>',
                ]
            );

            $this->end_controls_section();
        }
    }
}
