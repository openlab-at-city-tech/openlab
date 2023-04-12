<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Post_Tab_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Post_Tab extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

	public function get_name() {
        return Handler::get_name();
    }

    public function get_title() {
        return Handler::get_title();
    }

    public function get_icon() {
        return Handler::get_icon();
    }

	public function get_keywords() {
        return Handler::get_keywords();
    }

    public function get_categories() {
        return Handler::get_categories();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/how-to-add-post-tab-in-elementor/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_tab',
            [
                'label' => esc_html__('Widget settings', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'post_cat',
            [
                'label' =>esc_html__('Select Categories', 'elementskit-lite'),
				'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
				'description'	=> esc_html__('To avail this option you need to set/add a featured image to posts..', 'elementskit-lite'),
                'options'   =>'ajaxselect2/category',
                'label_block' => true,
                'multiple'  => true,
            ]
        );
        $this->add_control(
            'post_count',
            [
              'label'         => esc_html__( 'Post count', 'elementskit-lite' ),
              'type'          => Controls_Manager::NUMBER,
              'default'       => esc_html__( '3', 'elementskit-lite' ),

            ]
          );

        $this->add_responsive_control(
            'count_col',
            [
                'label'     => esc_html__( 'Select Column', 'elementskit-lite' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'ekit___column-2',
                'tablet_default' => 'ekit___column-2',
                'mobile_default' => 'ekit___column-2',
                'options'   => [
                      'ekit___column-2'     => esc_html__( '2 Column', 'elementskit-lite' ),
                      'ekit___column-3'     => esc_html__( '3 Column', 'elementskit-lite' ),
                      'ekit___column-4'     => esc_html__( '4 Column', 'elementskit-lite' ),
                ]
            ]
        );

        $this->add_control(
			'ekit_post_tab_on_click',
			[
				'label' => esc_html__( 'On Click', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
				'prefix_class' => 'is-click-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'ekit_post_tab_on_click_link_archive',
			[
				'label' => __( 'Link Archive?', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementskit-lite' ),
				'label_off' => __( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_post_tab_on_click' => 'yes'
				]
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_post_tab_wraper_style',
			[
				'label' => esc_html__( 'Tab Item Container', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_post_tab_wraper_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .tab__list',
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_post_tab_wraper_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', ],
                'exclude' => [
                    'image'
                ],
				'selector' => '{{WRAPPER}} .tab__list',
			]
        );

        $this->add_responsive_control(
			'ekit_post_tab_wraper_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tab__list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_post_tab_wraper_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_post_tab_style',
			[
				'label' => esc_html__( 'Tab Item', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_post_tab__item_content_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item',
			]
        );

        $this->add_responsive_control(
			'ekit_post_tab__item_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_post_tab__item_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs(
            'ekit_post_tab_normal_and_hover_tabs'
        );
        $this->start_controls_tab(
            'ekit_post_tab_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
			'ekit_post_tab__item__color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_post_tab__item_background_color_normal',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', ],
                'exclude' => [
                    'image'
                ],
				'selector' => '{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item',
			]
		);

        $this->add_responsive_control(
			'ekit_post_tab__item_border_radius_normal',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_post_tab_item_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .tab__list .tab__list__item',
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_post_tab_item_normal_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_post_tab_hover_tab',
            [
                'label' => esc_html__( 'Active', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
			'ekit_post_tab__item__color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item.active' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_post_tab__item_background_color_hover',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', ],
                'exclude' => [
                    'image'
                ],
				'selector' => '{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item.active',
			]
		);

        $this->add_responsive_control(
			'ekit_post_tab__item_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_post_tab_item_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .tab__list .tab__list__item.active',
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_post_tab_item_hover_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post--tab .tabHeader .tab__list .tab__list__item.active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_image',
			[
				'label' => esc_html__( 'Image', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_disable_hover_effect',
			[
				'label'     => esc_html__( 'Disable Hover Effect', 'elementskit-lite' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'default'   => 'no',
			]
		);

		$this->add_control(
			'ekit_image_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit--tab__post__details .tab__post--header>img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
	
		   $this->add_control(
			'ekit_image_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit--tab__post__details .tab__post--header>img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
            'ekit_image_alignment',
            [
                'label' =>esc_html__( 'Nav Content Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' =>esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' =>esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' =>esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit--tab__post__details .tab__post--header' => 'text-align: {{VALUE}};'
                ],
                'default' => 'left',
            ]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'elementskit-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_font',
					'label' 	=> esc_html__( 'Typography', 'elementskit-lite' ),
					'selector' 	=> '{{WRAPPER}} .tab__post--title',
				]
			);
			$this->add_control(
				'title_color_normal',
				[
					'label' 	=> esc_html__( 'Color', 'elementskit-lite' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'default' => 'inherit',
					'selectors' => [
						'{{WRAPPER}} .tab__post--title > a' => 'color: {{VALUE}}; transition: all 0.3s ease;',
					],
				]
			);
			$this->add_control(
				'title_color_hover',
				[
					'label'		=> esc_html__( 'Hover Color', 'elementskit-lite' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tab__post__single--item:hover .tab__post--title > a' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'title_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'elementskit-lite' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors'  => [
						'{{WRAPPER}} .tab__post--title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
        $settings = $this->get_settings_for_display();
        extract($settings); if(empty($post_cat)){return false;}; ?>
		<div class="ekit-post-tab post--tab hover--active <?php echo !empty($image_disable_hover_effect) && $image_disable_hover_effect == 'yes' ? 'disbale-hover' : ''; ?>">
            <div class="tabHeader">
                <div class="tab__list">
					<?php $i=1;  foreach($post_cat as $cat):
						$categoryURL    = get_category_link($cat);
						?>
						<?php if ($settings['ekit_post_tab_on_click_link_archive'] === 'yes') { ?>
                        <a href="<?php echo esc_url( $categoryURL ); ?>" class="<?php echo ($i==1)? 'active': ''; ?> tab__list__item">
                            <?php echo esc_html(get_cat_name($cat));?>
                        </a>
						<?php } else {  ?>
                        <span class="<?php echo ($i==1)? 'active': ''; ?> tab__list__item">
                            <?php echo esc_html(get_cat_name($cat));?>
                        </span>
						<?php }; ?>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
            <div class="ekit--tab__post__details tabContent">
                <?php $j=1; foreach($post_cat as $cat):

                    $query = array(
                        'post_type'      => 'post',
                        'post_status'    => 'publish',
                        'cat'    => $cat,
                        'posts_per_page' => $post_count,
					);

					$count_col_tablet = isset( $settings['count_col_tablet'] ) ? $settings['count_col_tablet'] : '';
					$count_col_mobile = isset( $settings['count_col_mobile'] ) ? $settings['count_col_mobile'] : '';

					$this->add_render_attribute(
						[
							'ekit-single-item' => [
								'class' => [
									'tab__post__single--item',
									$count_col,
									'tablet-' . $count_col_tablet,
									'mobile-' . $count_col_mobile,
									'post-count-' . $post_count
								],
							],
						]
					);
					?>
                    <div class="tabItem <?php echo ($j==1)? 'active': ''; ?>">
                        <?php $xs_query = new \WP_Query( $query );
                        if($xs_query->have_posts()):
                            while ($xs_query->have_posts()) :
                                $xs_query->the_post();
                                ?>
                                <?php if(has_post_thumbnail()): ?>
                                    <div <?php echo $this->get_render_attribute_string('ekit-single-item'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
                                        <div class="tab__post__single--inner">
                                            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="tab__post--header" aria-label="url">
                                                <?php the_post_thumbnail(); ?>
                                            </a>
                                            <h3 class="tab__post--title"><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a></h3>
                                        </div>
                                    </div>
                                <?php endif; ?>
                           <?php endwhile;
                        endif;
                        wp_reset_postdata(); ?>
                        <div class="clearfix"></div>
                    </div>
                <?php $j++; endforeach; ?>
            </div>
        </div>

    <?php
	}
}
