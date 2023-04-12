<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Blog_Posts_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Blog_Posts extends Widget_Base {
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

    public function get_categories() {
        return Handler::get_categories();
    }

    public function get_keywords() {
        return Handler::get_keywords();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/blog-posts-2/';
    }

    public function format_colname($str) {
        return str_replace('ekit', 'col', $str);
    }

    protected function register_controls() {

        // Layout
        $this->start_controls_section(
           'ekit_blog_posts_general',
           [
               'label' => esc_html__( 'Layout', 'elementskit-lite' ),
           ]
       );
       $this->add_control(
           'ekit_blog_posts_layout_style',
           [
               'label'     => esc_html__( 'Layout Style', 'elementskit-lite' ),
               'type'      => Controls_Manager::SELECT,
               'options'   => [
                   'elementskit-blog-block-post' => esc_html__( 'Block', 'elementskit-lite' ),
                   'elementskit-post-image-card' => esc_html__( 'Grid With Thumb', 'elementskit-lite' ),
                   'elementskit-post-card'       => esc_html__( 'Grid Without Thumb', 'elementskit-lite' ),
               ],
               'default'   => 'elementskit-blog-block-post',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_feature_img',
           [
               'label'     => esc_html__( 'Show Featured Image', 'elementskit-lite' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
               'label_off' => esc_html__( 'No', 'elementskit-lite' ),
               'default'   => 'yes',
               'condition' => [
                   'ekit_blog_posts_layout_style!' => 'elementskit-post-card',
               ],
           ]
       );
       
        $this->add_control(
            'ekit_blog_posts_layout_style_thumb',
            [
                'label'     => esc_html__( 'Image Position', 'elementskit-lite' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'block' => esc_html__( 'Top', 'elementskit-lite' ),
                    'flex' => esc_html__( 'Left', 'elementskit-lite' ),
                ],
                'default'   => 'block',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-post-image-card' => 'display: {{VALUE}}'
                ],
                'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                    'ekit_blog_posts_feature_img'  => 'yes',
                ],
            ]
        );

        /**
        * Control: Featured Image Size
        */
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'              => 'ekit_blog_posts_feature_img_size',
                'fields_options'    => [
                    'size'  => [
                        'label' => esc_html__( 'Featured Image Size', 'elementskit-lite' ),
                    ],
                ],
                'exclude'           => [ 'custom' ],
                'default'           => 'large',
                'condition'         => [
                    'ekit_blog_posts_feature_img'   => 'yes',
                    'ekit_blog_posts_layout_style!' => 'elementskit-post-card',
                ],
            ]
        );

       $this->add_control(
           'ekit_blog_posts_feature_img_float',
           [
               'label'     => esc_html__( 'Featured Image Alignment', 'elementskit-lite' ),
               'type'      => Controls_Manager::CHOOSE,
               'options'   => [
                   'left'  => [
                       'title' => esc_html__( 'Left', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'right' => [
                       'title' => esc_html__( 'Right', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
               ],
               'condition' => [
                   'ekit_blog_posts_feature_img' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-blog-block-post',
               ],
               'default'   => 'left',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_column',
           [
               'label'     => esc_html__( 'Show Posts Per Row', 'elementskit-lite' ),
               'type'      => Controls_Manager::SELECT,
               'options'   => [
                   'ekit-lg-12 ekit-md-12'   => esc_html__( '1', 'elementskit-lite' ),
                   'ekit-lg-6 ekit-md-6'     => esc_html__( '2', 'elementskit-lite' ),
                   'ekit-lg-4 ekit-md-6'     => esc_html__( '3', 'elementskit-lite' ),
                   'ekit-lg-3 ekit-md-6'     => esc_html__( '4', 'elementskit-lite' ),
                   'ekit-lg-2 ekit-md-6'     => esc_html__( '6', 'elementskit-lite' ),
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' => ['elementskit-post-image-card', 'elementskit-post-card'],
               ],
               'default'   => 'ekit-lg-4 ekit-md-6',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title',
           [
               'label'     => esc_html__( 'Show Title', 'elementskit-lite' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
               'label_off' => esc_html__( 'No', 'elementskit-lite' ),
               'default'   => 'yes',
           ]
       );
       $this->add_control(
            'ekit_blog_posts_title_trim',
            [
                'label'     => esc_html__( 'Crop title by word', 'elementskit-lite' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '',
                'condition' => [
                    'ekit_blog_posts_title' => 'yes',
                ],
            ]
        );
       $this->add_control(
           'ekit_blog_posts_content',
           [
               'label'     => esc_html__( 'Show Content', 'elementskit-lite' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
               'label_off' => esc_html__( 'No', 'elementskit-lite' ),
               'default'   => 'yes',
           ]
       );
       $this->add_control(
            'ekit_blog_posts_content_trim',
            [
                'label'     => esc_html__( 'Crop content by word', 'elementskit-lite' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '',
                'condition' => [
                    'ekit_blog_posts_content' => 'yes',
                ],
            ]
        );

       $this->add_control(
           'ekit_blog_posts_read_more',
           [
               'label'     => esc_html__( 'Show Read More', 'elementskit-lite' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
               'label_off' => esc_html__( 'No', 'elementskit-lite' ),
               'default'   => 'yes',
               'condition' => ['ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post'],
           ]
	   );
	   
			$this->add_control(
				'grid_masonry',
				[
					'label'	=> esc_html__( 'Enable Masonry', 'elementskit-lite' ),
					'type'	=> Controls_Manager::SWITCHER,
					'condition'	=> [
						'ekit_blog_posts_layout_style!'	=> 'elementskit-blog-block-post',
					]
				]
			);

       $this->end_controls_section();
       // Query
       $this->start_controls_section(
           'ekit_blog_posts_content_section',
           [
               'label' => esc_html__( 'Query', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_num',
           [
               'label'     => esc_html__( 'Posts Count', 'elementskit-lite' ),
               'type'      => Controls_Manager::NUMBER,
               'min'       => 1,
               'max'       => 100,
               'default'   => 3,
           ]
       );

       $this->add_control(
        'ekit_blog_posts_is_manual_selection',
        [
            'label' => esc_html__( 'Select posts by:', 'elementskit-lite' ),
            'type' => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                'recent'    => esc_html__( 'Recent Post', 'elementskit-lite' ),
                'yes'       => esc_html__( 'Selected Post', 'elementskit-lite' ),
                ''        => esc_html__( 'Category Post', 'elementskit-lite' ),
            ],

        ]
    );

       $this->add_control(
           'ekit_blog_posts_manual_selection',
           [
               'label' =>esc_html__('Search & Select', 'elementskit-lite'),
               'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
               'options'   =>'ajaxselect2/post_list',
               'label_block' => true,
               'multiple'  => true,
               'condition' => [ 'ekit_blog_posts_is_manual_selection' => 'yes' ]
           ]
       );
       $this->add_control(
           'ekit_blog_posts_cats',
           [
               'label' =>esc_html__('Select Categories', 'elementskit-lite'),
               'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
               'options'   =>'ajaxselect2/category',
               'label_block' => true,
               'multiple'  => true,
               'condition' => [ 'ekit_blog_posts_is_manual_selection' => '' ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_offset',
           [
               'label'     => esc_html__( 'Offset', 'elementskit-lite' ),
               'type'      => Controls_Manager::NUMBER,
               'min'       => 0,
               'max'       => 20,
               'default'   => 0,
           ]
       );

       $this->add_control(
           'ekit_blog_posts_order_by',
           [
               'label'   => esc_html__( 'Order by', 'elementskit-lite' ),
               'type'    => Controls_Manager::SELECT,
               'options' => [
                   'date'          => esc_html__( 'Date', 'elementskit-lite' ),
                   'title'         => esc_html__( 'Title', 'elementskit-lite' ),
                   'author'        => esc_html__( 'Author', 'elementskit-lite' ),
                   'modified'      => esc_html__( 'Modified', 'elementskit-lite' ),
                   'comment_count' => esc_html__( 'Comments', 'elementskit-lite' ),
               ],
               'default' => 'date',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_sort',
           [
               'label'   => esc_html__( 'Order', 'elementskit-lite' ),
               'type'    => Controls_Manager::SELECT,
               'options' => [
                   'ASC'  => esc_html__( 'ASC', 'elementskit-lite' ),
                   'DESC' => esc_html__( 'DESC', 'elementskit-lite' ),
               ],
               'default' => 'DESC',
           ]
       );

       $this->end_controls_section();

        // meta data
		$this->start_controls_section(
			'ekit_blog_posts_meta_data_tab',
			[
				'label' => esc_html__( 'Meta Data', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'ekit_blog_posts_floating_date',
            [
                'label'     => esc_html__( 'Show Floating Date', 'elementskit-lite' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'default'   => 'no',
                'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_floating_date_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit-lite'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'style1',
                'options' => [
                    'style1' => [
                        'title' => esc_html__( 'Image style 1', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/floating-date-1.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/floating-date-1.png',
                        'width' => '50%',
                    ],
                    'style2' => [
                        'title' => esc_html__( 'Image style 2', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/floating-date-2.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/floating-date-2.png',
                        'width' => '50%',
                    ],
                ],
                'condition' => [
                    'ekit_blog_posts_floating_date' => 'yes',
                ],
            ]
        );

		$this->add_control(
            'ekit_blog_posts_floating_category',
            [
                'label'     => esc_html__( 'Show Floating Category', 'elementskit-lite' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'default'   => 'no',
                'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta',
            [
                'label'     => esc_html__( 'Show Meta Data', 'elementskit-lite' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'default'   => 'yes',
            ]
        );
        $this->add_control(
         'ekit_blog_posts_title_position',
             [
                 'label' => esc_html__( 'Meta Position', 'elementskit-lite' ),
                 'type'  => Controls_Manager::SELECT,
                 'options' => [
                    'after_meta'  => esc_html__( 'Before Title', 'elementskit-lite' ),
                     'before_meta' => esc_html__( 'After Title', 'elementskit-lite' ),
                     'after_content'  => esc_html__( 'After Content', 'elementskit-lite' ),
                 ],
                 'default' => 'after_meta',
                 'condition' => [
                     'ekit_blog_posts_meta' => 'yes',
                 ]
             ]
         );
        $this->add_control(
            'ekit_blog_posts_meta_select',
            [
                'label'     => esc_html__( 'Meta Data', 'elementskit-lite' ),
                'type'      => Controls_Manager::SELECT2,
                'options'   => [
                    'author'     => esc_html__( 'Author', 'elementskit-lite' ),
                    'date'   => esc_html__( 'Date', 'elementskit-lite' ),
                    'category'     => esc_html__( 'Category', 'elementskit-lite' ),
                    'comment'     => esc_html__( 'Comment', 'elementskit-lite' ),
                ],
                'multiple' => true,
                // 'default'   => [
                //     'author',
                //     'date'
                // ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_author_image',
            [
                'label'     => esc_html__( 'Show Author Image', 'elementskit-lite' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'default'   => 'no',
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'  => 'author'
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_author_icons',
            [
                'label' => esc_html__( 'Author Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_author_icon',
                'default' => [
                    'value' => 'icon icon-user',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_author_image!' => 'yes',
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'author'
                ]
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_date_icons',
            [
                'label' => esc_html__( 'Date Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_date_icon',
                'default' => [
                    'value' => 'icon icon-calendar3',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'date'
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_category_icons',
            [
                'label' => esc_html__( 'Category Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_category_icon',
                'default' => [
                    'value' => 'icon icon-folder',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'category'
                ],
            ]
        );
        $this->add_control(
            'ekit_blog_posts_meta_comment_icons',
            [
                'label' => esc_html__( 'Comment Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_blog_posts_meta_comment_icon',
                'default' => [
                    'value' => 'icon icon-comment',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_blog_posts_meta' => 'yes',
                    'ekit_blog_posts_meta_select'   => 'comment'
                ],
            ]
        );
        

		$this->end_controls_section();

       // Read More Button
       $this->start_controls_section(
           'ekit_blog_posts_more_section',
           [
               'label' => esc_html__( 'Read More Button', 'elementskit-lite' ),
               'condition' => ['ekit_blog_posts_read_more' => 'yes', 'ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post'],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_text',
           [
               'label' =>esc_html__( 'Label', 'elementskit-lite' ),
               'type' => Controls_Manager::TEXT,
			   'dynamic' => [
					'active' => true,
			   ],
               'default' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
               'placeholder' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
        'ekit_blog_posts_btn_icons__switch',
        [
            'label' => esc_html__('Add icon? ', 'elementskit-lite'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
            'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
        ]
    );

       $this->add_control(
           'ekit_blog_posts_btn_icons',
           [
               'label' =>esc_html__( 'Icon', 'elementskit-lite' ),
               'type' => Controls_Manager::ICONS,
               'fa4compatibility' => 'ekit_blog_posts_btn_icon',
                'default' => [
                    'value' => '',
                ],
               'label_block' => true,
               'condition'  => [
                   'ekit_blog_posts_btn_icons__switch'  => 'yes'
               ]
           ]
       );
       $this->add_control(
        'ekit_blog_posts_btn_icon_align',
        [
            'label' =>esc_html__( 'Icon Position', 'elementskit-lite' ),
            'type' => Controls_Manager::SELECT,
            'default' => 'left',
            'options' => [
                'left' =>esc_html__( 'Before', 'elementskit-lite' ),
                'right' =>esc_html__( 'After', 'elementskit-lite' ),
            ],
            'condition'  => [
                 'ekit_blog_posts_btn_icons__switch'  => 'yes'
             ]
        ]
    );
       $this->add_responsive_control(
           'ekit_blog_posts_btn_align',
           [
               'label' =>esc_html__( 'Alignment', 'elementskit-lite' ),
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
               'selectors'=> [
                    '{{WRAPPER}} .btn-wraper' => 'text-align: {{VALUE}};',
                ],
               'default' => 'left',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_class',
           [
               'label' => esc_html__( 'Class', 'elementskit-lite' ),
               'type' => Controls_Manager::TEXT,
			   'dynamic' => [
					'active' => true,
			   ],
               'placeholder' => esc_html__( 'Class Name', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_id',
           [
               'label' => esc_html__( 'id', 'elementskit-lite' ),
               'type' => Controls_Manager::TEXT,
			   'dynamic' => [
					'active' => true,
			   ],
               'placeholder' => esc_html__( 'ID', 'elementskit-lite' ),
           ]
       );
       
       $this->end_controls_section();



       // Post Styles
       $this->start_controls_section(
           'ekit_blog_posts_style',
           [
               'label' => esc_html__( 'Wrapper', 'elementskit-lite' ),
               'tab'   => Controls_Manager::TAB_STYLE,
           ]
       );

        $this->start_controls_tabs(
            'ekit_blog_posts_tabs'
        );

        $this->start_controls_tab(
            'ekit_blog_posts_tab_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           [
               'name'     => 'ekit_blog_posts_background',
               'label'    => esc_html__( 'Background', 'elementskit-lite' ),
               'types'    => [ 'classic', 'gradient' ],
               'selector' => '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card',
           ]
       );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'     => 'ekit_blog_posts_shadow',
               'selector' => '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card',
           ]
       );
       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_tab_hover',
           [
               'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
           ]
       );
       $this->add_group_control(
          Group_Control_Background::get_type(),
          [
              'name'     => 'ekit_blog_posts_background_hover',
              'label'    => esc_html__( 'Background', 'elementskit-lite' ),
              'types'    => [ 'classic', 'gradient' ],
              'selector' => '{{WRAPPER}} .elementskit-blog-block-post:hover, {{WRAPPER}} .elementskit-post-image-card:hover, {{WRAPPER}} .elementskit-post-card:hover',
              'fields_options'  => [
                  'background' => [
                    'prefix_class' => 'ekit-blog-posts--bg-hover bg-hover-',
                  ],
              ],
          ]
       );

      $this->add_group_control(
          Group_Control_Box_Shadow::get_type(), [
              'name'     => 'ekit_blog_posts_shadow_hover',
              'selector' => '{{WRAPPER}} .elementskit-blog-block-post:hover, {{WRAPPER}} .elementskit-post-image-card:hover, {{WRAPPER}} .elementskit-post-card:hover',
          ]
      );
       $this->end_controls_tab();
       $this->end_controls_tabs();

		$this->add_control(
			'ekit_blog_posts_hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

       $this->add_control(
           'ekit_blog_posts_vertical_alignment',
           [
               'label'   => esc_html__( 'Vertical Alignment', 'elementskit-lite' ),
               'type'    => Controls_Manager::CHOOSE,
               'options' => [
                   'flex-start'  => [
                       'title' => esc_html__( 'Top', 'elementskit-lite' ),
                       'icon'  => 'eicon-v-align-top',
                   ],
                   'center' => [
                       'title' => esc_html__( 'Middle', 'elementskit-lite' ),
                       'icon'  => 'eicon-v-align-middle',
                   ],
                   'flex-end'    => [
                       'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
                       'icon'  => 'eicon-v-align-bottom',
                   ],
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' => 'elementskit-blog-block-post',
               ],
               'default'   => 'flex-start',
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-blog-block-post > .row'    => 'align-items: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
        'ekit_blog_posts_radius',
        [
            'label'      => esc_html__( 'Container Border radius', 'elementskit-lite' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'ekit_blog_posts_padding',
        [
            'label'      => esc_html__( 'Container Padding', 'elementskit-lite' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'ekit_blog_posts_margin',
        [
            'label'      => esc_html__( 'Container Margin', 'elementskit-lite' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'tablet_default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '30',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
            ],
            'mobile_default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '30',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
            ],
            'selectors'  => [
                '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'ekit_blog_posts_text_content_wraper_padding',
        [
            'label' => esc_html__( 'Content Padding', 'elementskit-lite' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-blog-block-post .elementskit-post-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .elementskit-post-image-card .elementskit-post-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

        $this->add_control(
            'ekit_blog_posts_container_border_title',
            [
                'label' => esc_html__( 'Container Border', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'     => 'ekit_blog_posts_border',
               'label'    => esc_html__( 'Container Border', 'elementskit-lite' ),
               'selector' => '{{WRAPPER}} .elementskit-blog-block-post, {{WRAPPER}} .elementskit-post-image-card, {{WRAPPER}} .elementskit-post-card',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_border_title',
           [
               'label' => esc_html__( 'Content Border', 'elementskit-lite' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
               'condition' => [
                   'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
                   'ekit_blog_posts_feature_img' => 'yes'
               ]
           ]
       );

        $this->add_control(
            'ekit_blog_posts_content_background',
            [
                'label' => esc_html_x( 'Container Background Color', 'elementskit', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-post-body' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_blog_posts_content_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-post-body',
            ]
        );

       $this->add_control(
           'ekit_blog_posts_content_border_style',
           [
               'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::SELECT,
               'options' => [
                   '' => esc_html__( 'None', 'elementskit-lite' ),
                   'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
                   'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
                   'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
                   'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
                   'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body' => 'border-style: {{VALUE}};',
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
                   'ekit_blog_posts_feature_img' => 'yes'
               ]
           ]
       );
       $this->add_control(
           'ekit_blog_posts_content_border_dimensions',
           [
               'label' => esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
                   'ekit_blog_posts_feature_img' => 'yes'
               ]
           ]
       );
       $this->start_controls_tabs( 'ekit_blog_posts_content_border_tabs', [
           'condition' => [
               'ekit_blog_posts_layout_style' =>  'elementskit-post-image-card',
               'ekit_blog_posts_feature_img' => 'yes'
           ]
       ] );
       $this->start_controls_tab(
           'ekit_blog_posts_content_border_normal',
           [
               'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_border_color_normal',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body' => 'border-color: {{VALUE}};',
               ],
           ]
       );
       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_content_border_color_hover_style',
           [
               'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
           ]
       );
       $this->add_control(
           'ekit_blog_posts_content_border_color_hover',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-image-card:hover .elementskit-post-body ' => 'border-color: {{VALUE}};',
               ],
           ]
       );
       $this->end_controls_tab();
       $this->end_controls_tabs();

       $this->end_controls_section();


       // Featured Image Styles
       $this->start_controls_section(
           'ekit_blog_posts_feature_img_style',
           [
               'label'     => esc_html__( 'Featured Image', 'elementskit-lite' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_layout_style!' => 'elementskit-post-card',
                   'ekit_blog_posts_feature_img'    => 'yes'
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_size',
           [
               'label' => esc_html__( 'Image Width', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'range' => [
                   'px' => [
                       'min' => 1,
                       'max' => 500,
                   ],
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-entry-thumb' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}',
               ],
               'condition' => [
                    'ekit_blog_posts_layout_style' => 'elementskit-post-image-card',
                    'ekit_blog_posts_layout_style_thumb' => 'flex',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'      => 'ekit_blog_posts_feature_img_shadow',
               'selector'  => '{{WRAPPER}} .elementskit-entry-thumb',
           ]
       );

       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'      => 'ekit_blog_posts_feature_img_border',
               'label'     => esc_html__( 'Border', 'elementskit-lite' ),
               'selector'  => '{{WRAPPER}} .elementskit-entry-thumb',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_radius',
           [
               'label'     => esc_html__( 'Border radius', 'elementskit-lite' ),
               'type'      => Controls_Manager::DIMENSIONS,
               'size_units'=> [ 'px', '%', 'em' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-entry-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_margin',
           [
               'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-entry-thumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_feature_img_padding',
           [
               'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   ' {{WRAPPER}} .ekit-wid-con .elementskit-entry-thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->end_controls_section();


       // Meta Styles
       $this->start_controls_section(
           'ekit_blog_posts_meta_style',
           [
               'label'     => esc_html__( 'Meta', 'elementskit-lite' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_meta' => 'yes',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_meta_typography',
               'selector'   => '{{WRAPPER}} .post-meta-list a, {{WRAPPER}} .post-meta-list .meta-date-text',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_meta_alignment',
           [
               'label'    => esc_html__( 'Alignment', 'elementskit-lite' ),
               'type'     => Controls_Manager::CHOOSE,
               'options'  => [
                   'left'   => [
                       'title' => esc_html__( 'Left', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'center' => [
                       'title' => esc_html__( 'Center', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-center',
                   ],
                   'right'  => [
                       'title' => esc_html__( 'Right', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
               ],
               'default'  => 'left',
               'selectors'=> [
                   '{{WRAPPER}} .post-meta-list' => 'text-align: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
            'ekit_blog_posts_meta_margin',
            [
                'label'     => esc_html__( 'Container Margin', 'elementskit-lite' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'size_units'=> [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

       $this->add_responsive_control(
           'ekit_blog_posts_meta_item_margin',
           [
               'label'     => esc_html__( 'Item Margin', 'elementskit-lite' ),
               'type'      => Controls_Manager::DIMENSIONS,
               'size_units'=> [ 'px', '%', 'em' ],
               'selectors' => [
                   '{{WRAPPER}} .post-meta-list > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_control(
            'ekit_blog_posts_meta_padding',
            [
                'label' => esc_html__( 'Item Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

       $this->add_control(
            'ekit_blog_posts_meta_icon_padding',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span > i, {{WRAPPER}} .post-meta-list > span > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_meta_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .post-meta-list > span > svg'  => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'ekit_blog_posts_meta_background_normal_and_hover_tab'
        );
        $this->start_controls_tab(
            'ekit_blog_posts_meta_background_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
		);

        $this->add_control(
            'ekit_blog_posts_meta_color_normal',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta_color_icon_normal',
            [
                'label'      => esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span > svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_background_normal',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient', ],
                'selector' => '{{WRAPPER}} .post-meta-list > span',
                'exclude' => [
                    'image'
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_border_normal',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span',
			]
		);

        $this->add_control(
            'ekit_blog_posts_meta_border_radius_normal',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_box_shadow_normal',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span',
			]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_blog_posts_meta_shadow_normal',
                'selector'   => '{{WRAPPER}} .post-meta-list > span',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_blog_posts_meta_background_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta_color_hover',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span:hover > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};',

                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_meta_color_icon_hover',
            [
                'label'      => esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .post-meta-list > span:hover > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-meta-list > span:hover > svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',

                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span:hover > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span > svg path' => 'strock: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_background_hover',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient', ],
                'selector' => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
                'exclude' => [
                    'image'
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_border_hover',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
			]
		);

        $this->add_control(
            'ekit_blog_posts_meta_border_radius_hover',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_blog_posts_meta_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
			]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_blog_posts_meta_shadow_hover',
                'selector'   => '{{WRAPPER}} .post-meta-list > span:hover, {{WRAPPER}}.ekit-blog-posts--bg-hover .elementskit-post-image-card:hover .post-meta-list > span',
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();

       $this->end_controls_section();

       // Floating Date Styles
       $this->start_controls_section(
           'ekit_blog_posts_floating_date_style_area',
           [
               'label'     => esc_html__( 'Floating Date', 'elementskit-lite' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_floating_date' => 'yes',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_height', [
               'label'			 =>esc_html__( 'Height', 'elementskit-lite' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'range'			 => [
                   'px' => [
                       'min'	 => -30,
                       'step'	 => 1,
                   ],
               ],
               'size_units'	 => ['px'],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta'	=> 'height: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],

           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_width', [
               'label'			 =>esc_html__( 'Width', 'elementskit-lite' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'range'			 => [
                   'px' => [
                       'min'	 => -30,
                       'step'	 => 1,
                   ],
               ],
               'size_units'	 => ['px'],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta'	=> 'width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_left_pos', [
               'label'			 =>esc_html__( 'Left', 'elementskit-lite' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => -100,
                       'max'	 => 1000,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => 0,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists'	=> 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_top_pos', [
               'label'			 =>esc_html__( 'Top', 'elementskit-lite' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => -100,
                       'max'	 => 1000,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => 0,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists'	=> 'top: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_bottom_pos', [
               'label'			 =>esc_html__( 'Bottom', 'elementskit-lite' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '',
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => 0,
                       'max'	 => 1000,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => 0,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag'	=> 'bottom: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_style2_left_pos', [
               'label'			 =>esc_html__( 'Left', 'elementskit-lite' ),
               'type'			 => Controls_Manager::SLIDER,
               'default'		 => [
                   'size' => '-10',
                   'unit' => 'px'
               ],
               'size_units' => [ 'px', '%' ],
               'range'		 => [
                   'px' => [
                       'min'	 => -10,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
                   '%'	 => [
                       'min'	 => -10,
                       'max'	 => 100,
                       'step'	 => 1,
                   ],
               ],
               'selectors'		 => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag'	=> 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_heading',
           [
               'label' => esc_html__( 'Date Typography', 'elementskit-lite' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
           ]
       );
       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_floating_date_typography_group',
               'selector'   => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta .elementskit-meta-wraper strong',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta .elementskit-meta-wraper strong' => 'color: {{VALUE}};'
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_month_heading',
           [
               'label' => esc_html__( 'Month Typography', 'elementskit-lite' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
           ]
       );
       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_floating_date_month_typography_group',
               'selector'   => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_month_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta .elementskit-meta-wraper' => 'color: {{VALUE}};'
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Background::get_type(),
           array(
               'name'     => 'ekit_blog_posts_floating_date_bg_color_group',
               'selector' => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
               'separator' => 'before',
           )
       );

       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_padding',
           [
               'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', 'em', '%' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'     => 'ekit_blog_posts_floating_date_border_group',
               'label'    => esc_html__( 'Border', 'elementskit-lite' ),
               'selector' => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style1',
               ],
           ]
       );
       $this->add_responsive_control(
           'ekit_blog_posts_floating_date_border_radius',
           [
               'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px'],
               'default' => [
                   'top' => '',
                   'right' => '',
                   'bottom' => '' ,
                   'left' => '',
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style',
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'      => 'ekit_blog_posts_floating_date_shadow_group',
               'selector'  => '{{WRAPPER}} .elementskit-meta-lists .elementskit-single-meta',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_title',
           [
               'label' => esc_html__( 'Triangle', 'elementskit-lite' ),
               'type' => Controls_Manager::HEADING,
               'separator' => 'before',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_color',
           [
               'label' => esc_html__( 'Triangle Background', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'color: {{VALUE}}',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_size',
           [
               'label' => esc_html__( 'Triangle Size', 'elementskit-lite' ),
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px' ],
               'range' => [
                   'px' => [
                       'min' => 0,
                       'max' => 100,
                       'step' => 1,
                   ],
               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 5,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'border-width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_position_left',
           [
               'label' => esc_html__( 'Triangle Position Left', 'elementskit-lite' ),
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => 0,
                       'max' => 100,
                       'step' => 1,
                   ],
               ],
               'default' => [
                   'unit' => '%',
                   'size' => 0,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_position_top',
           [
               'label' => esc_html__( 'Triangle Position Top', 'elementskit-lite' ),
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => -100,
                       'max' => 100,
                       'step' => 1,
                   ],
               ],
               'default' => [
                   'unit' => 'px',
                   'size' => -10,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-meta-lists.elementskit-style-tag > .elementskit-single-meta::before' => 'top: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_position_alignment',
           [
               'label' => esc_html__( 'Triangle Direction', 'elementskit-lite' ),
               'type' =>   Controls_Manager::CHOOSE,
               'options' => [
                   'triangle_left' => [
                       'title' => esc_html__( 'From Left', 'elementskit-lite' ),
                       'icon' => 'fa fa-caret-right',
                   ],
                   'triangle_right' => [
                       'title' => esc_html__( 'From Right', 'elementskit-lite' ),
                       'icon' => 'fa fa-caret-left',
                   ],
               ],
               'default' => 'triangle_left',
               'toggle' => true,
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_floating_date_triangle_hr',
           [
               'type' => Controls_Manager::DIVIDER,
               'style' => 'thick',
               'condition' => [
                   'ekit_blog_posts_floating_date_style' => 'style2',
               ],
           ]
       );
       $this->end_controls_section();

        // Floating Category Styles
        $this->start_controls_section(
            'ekit_blog_posts_floating_category_style',
            [
                'label'     => esc_html__( 'Floating Category', 'elementskit-lite' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_blog_posts_floating_category' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_top_pos', [
                'label'			 =>esc_html__( 'Top', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'size_units' => [ 'px', '%' ],
                'range'		 => [
                    'px' => [
                        'min'	 => -100,
                        'max'	 => 1000,
                        'step'	 => 1,
                    ],
                    '%'	 => [
                        'min'	 => 0,
                        'max'	 => 100,
                        'step'	 => 1,
                    ],
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-meta-categories'	=> 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_left_pos', [
                'label'			 =>esc_html__( 'Left', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'size_units' => [ 'px', '%' ],
                'range'		 => [
                    'px' => [
                        'min'	 => -100,
                        'max'	 => 1000,
                        'step'	 => 1,
                    ],
                    '%'	 => [
                        'min'	 => 0,
                        'max'	 => 100,
                        'step'	 => 1,
                    ],
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-meta-categories'	=> 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_blog_posts_floating_category_typography',
                'selector'   => '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span a',
            ]
        );

        $this->add_control(
            'ekit_blog_posts_floating_category_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span a' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'ekit_blog_posts_floating_category_bg_color',
            [
                'label'      => esc_html__( 'Background Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span' => 'background-color: {{VALUE}};'
                ],
            ]
        );

        
        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'    => [
                    'unit'      => 'px',
                    'top'       => '4',
                    'right'     => '8',
                    'bottom'    => '4',
                    'left'      => '8',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_padding_radius',
            [
                'label'     => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'size_units'=> [ 'px', '%', 'em' ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'ekit_blog_posts_floating_category_margin_right', [
                'label'			 =>esc_html__( 'Space Between Categories', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'size_units' => [ 'px', '%' ],
                'range'		 => [
                    'px' => [
                        'min'	 => -100,
                        'max'	 => 1000,
                        'step'	 => 1,
                    ],
                    '%'	 => [
                        'min'	 => 0,
                        'max'	 => 100,
                        'step'	 => 1,
                    ],
                ],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-meta-categories .elementskit-meta-wraper span:not(:last-child)'	=> 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

       // Title Styles
       $this->start_controls_section(
           'ekit_blog_posts_title_style',
           [
               'label'     => esc_html__( 'Title', 'elementskit-lite' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_title' => 'yes',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_title_typography',
               'selector'   => '{{WRAPPER}} .elementskit-post-body .entry-title, {{WRAPPER}} .elementskit-entry-header .entry-title, {{WRAPPER}} .elementskit-post-image-card .elementskit-post-body .entry-title  a,  {{WRAPPER}} .elementskit-post-card .elementskit-entry-header .entry-title  a,{{WRAPPER}} .elementskit-blog-block-post .elementskit-post-body .entry-title a',
           ]
       );

       $this->start_controls_tabs(
           'ekit_blog_posts_title_tabs'
       );

       $this->start_controls_tab(
           'ekit_blog_posts_title_normal',
           [
               'label' => esc_html__( 'Normal', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title a' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title a' => 'color: {{VALUE}};'
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Text_Shadow::get_type(), [
               'name'       => 'ekit_blog_posts_title_shadow',
               'selector'   => '{{WRAPPER}} .elementskit-post-body .entry-title a, {{WRAPPER}} .elementskit-entry-header .entry-title a',
           ]
       );

       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_title_hover',
           [
               'label' => esc_html__( 'Hover', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_hover_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title a:hover' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title a:hover' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-card:hover .entry-title a' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-image-card:hover .entry-title a' => 'color: {{VALUE}};'
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Text_Shadow::get_type(), [
               'name'       => 'ekit_blog_posts_title_hover_shadow',
               'selector'   => '{{WRAPPER}} .elementskit-post-body .entry-title a:hover, {{WRAPPER}} .elementskit-entry-header .entry-title a:hover',
           ]
       );

       $this->end_controls_tab();

       $this->end_controls_tabs();

       $this->add_control(
           'ekit_blog_posts_title_hover_shadow_hr',
           [
               'type' => Controls_Manager::DIVIDER,
               'style' => 'thick',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_title_alignment',
           [
               'label'   => esc_html__( 'Alignment', 'elementskit-lite' ),
               'type'    => Controls_Manager::CHOOSE,
               'options' => [
                   'left'   => [
                       'title' => esc_html__( 'Left', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'center'  => [
                       'title' => esc_html__( 'Center', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-center',
                   ],
                   'right'   => [
                       'title' => esc_html__( 'Right', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
                   'justify' => [
                       'title' => esc_html__( 'justify', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-justify',
                   ],
               ],
               'default'   => 'left',
               'devices'   => [ 'desktop', 'tablet', 'mobile' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title' => 'text-align: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title' => 'text-align: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_title_margin',
           [
               'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-body .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                   '{{WRAPPER}} .elementskit-entry-header .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_hr',
           [
               'type' => Controls_Manager::DIVIDER,
               'style' => 'thick',
               'condition' => [
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator',
           [
               'label'     => esc_html__( 'Show Separator', 'elementskit-lite' ),
               'type'      => Controls_Manager::SWITCHER,
               'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
               'label_off' => esc_html__( 'No', 'elementskit-lite' ),
               'default'   => 'yes',
               'condition' => [
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_color',
           [
               'label'      => esc_html__( 'Separator Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'background-color: {{VALUE}};',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_width',
           [
               'label' => esc_html__( 'Width', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ '%' ],
               'range' => [
                   '%' => [
                       'min' => 0,
                       'max' => 100,
                   ],
               ],
               'default' => [
                   'unit' => '%',
                   'size' => 5,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_height',
           [
               'label' => esc_html__( 'Height', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px' ],
               'range' => [
                   'px' => [
                       'min' => 0,
                       'max' => 100,
                       'step' => 1,
                   ],
               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 3,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'height: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_title_separator_margin',
           [
               'label' => esc_html__( 'Margin', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-border-hr' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_title_separator' => 'yes',
                   'ekit_blog_posts_layout_style' => 'elementskit-post-card',
               ],
           ]
       );

       $this->end_controls_section();


       // Content Styles
       $this->start_controls_section(
           'ekit_blog_posts_content_style',
           [
               'label' => esc_html__( 'Content', 'elementskit-lite' ),
               'tab'   => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_content' => 'yes',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_color',
           [
               'label'      => esc_html__( 'Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-body > p'   => 'color: {{VALUE}};',
               ],
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_color_hover',
           [
               'label'      => esc_html__( 'Hover Color', 'elementskit-lite' ),
               'type'       => Controls_Manager::COLOR,
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-blog-block-post:hover .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-image-card:hover .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-card:hover .elementskit-post-footer > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-blog-block-post:hover .elementskit-post-body > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-image-card:hover .elementskit-post-body > p' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-card:hover .elementskit-post-body > p' => 'color: {{VALUE}};',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Typography::get_type(), [
               'name'       => 'ekit_blog_posts_content_typography',
               'selector'   => '{{WRAPPER}} .elementskit-post-footer > p, {{WRAPPER}} .elementskit-post-body > p',
           ]
       );

       $this->add_group_control(
           Group_Control_Text_Shadow::get_type(), [
               'name'       => 'ekit_blog_posts_content_shadow',
               'selector'   => '{{WRAPPER}} .elementskit-post-footer > p, {{WRAPPER}} .elementskit-post-body > p',
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_content_alignment',
           [
               'label'   => esc_html__( 'Alignment', 'elementskit-lite' ),
               'type'    => Controls_Manager::CHOOSE,
               'options' => [
                   'left'    => [
                       'title' => esc_html__( 'Left', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-left',
                   ],
                   'center'  => [
                       'title' => esc_html__( 'Center', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-center',
                   ],
                   'right'   => [
                       'title' => esc_html__( 'Right', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-right',
                   ],
                   'justify' => [
                       'title' => esc_html__( 'justify', 'elementskit-lite' ),
                       'icon'  => 'eicon-text-align-justify',
                   ],
               ],
               'default'   => 'left',
               'devices'   => [ 'desktop', 'tablet', 'mobile' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-footer'   => 'text-align: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-post-body > p' => 'text-align: {{VALUE}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_content_margin',
           [
               'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
               'type'       => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'selectors'  => [
                   '{{WRAPPER}} .elementskit-post-footer'   => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                   '{{WRAPPER}} .elementskit-blog-block-post .elementskit-post-footer > p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                   '{{WRAPPER}} .elementskit-post-body > p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       //  content highlight

       $this->add_control(
           'ekit_blog_posts_content_highlight_border',
           [
               'label' => esc_html__( 'Show Highlight  Border', 'elementskit-lite' ),
               'type' => Controls_Manager::SWITCHER,
               'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
               'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
               'return_value' => 'yes',
               'default' => '',
               'separator' => 'before'
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_height',
           [
               'label' => esc_html__( 'Hight', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => 5,
                       'max' => 300,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 100,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'height: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                       'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_width',
           [
               'label' => esc_html__( 'Width', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px', '%' ],
               'range' => [
                   'px' => [
                       'min' => 1,
                       'max' => 10,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 2,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'width: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_top_bottom_pos',
           [
               'label' => esc_html__( 'Top Bottom Position', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ '%' ],
               'range' => [
                   '%' => [
                       'min' => -10,
                       'max' => 110,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => '%',
                   'size' => 50,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'top: {{SIZE}}{{UNIT}};',
               ],

               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_left_right_pos',
           [
               'label' => esc_html__( 'Left Right Position', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ '%' ],
               'range' => [
                   '%' => [
                       'min' => -5,
                       'max' => 120,
                       'step' => 1,
                   ],

               ],
               'default' => [
                   'unit' => '%',
                   'size' => 0,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => 'left: {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->start_controls_tabs('ekit_blog_posts_border_highlight_color_tabs',[
           'condition' => [
               'ekit_blog_posts_content_highlight_border' => 'yes'
           ]
       ]);

       $this->start_controls_tab(
           'ekit_blog_posts_border_highlight_color_normal_tab',
           [
               'label' => esc_html__( 'Normal', 'elementskit-lite' ),
           ]
       );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           [
               'name' => 'ekit_blog_posts_border_highlight_bg_color',
               'label' => esc_html__( 'Separator Color', 'elementskit-lite' ),
               'types' => [ 'classic', 'gradient' ],
               'selector' => '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before',
           ]
       );

       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_border_highlight_color_hover_tab',
           [
               'label' => esc_html__( 'Hover', 'elementskit-lite' ),
           ]
       );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           [
               'name' => 'ekit_blog_posts_border_highlight_bg_color_hover',
               'label' => esc_html__( 'Separator Color', 'elementskit-lite' ),
               'types' => [ 'classic', 'gradient' ],
               'selector' => '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:hover:before',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_content_highlight_border_transition',
           [
               'label' => esc_html__( 'Transition', 'elementskit-lite' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 's' ],
               'range' => [
                   's' => [
                       'min' => .1,
                       'max' => 5,
                       'step' => .1,
                   ],

               ],
               'default' => [
                   'unit' => 's',
                   'size' => 0,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body.ekit-highlight-border:before' => '-webkit-transition: all {{SIZE}}{{UNIT}}; -o-transition: all {{SIZE}}{{UNIT}}; transition: all {{SIZE}}{{UNIT}};',
               ],
               'condition' => [
                   'ekit_blog_posts_content_highlight_border' => 'yes'
               ]
           ]
       );

       $this->end_controls_tab();
       $this->end_controls_tabs();

       $this->end_controls_section();


       // Author Image Styles
       $this->start_controls_section(
           'ekit_blog_posts_author_img_style',
           [
               'label'     => esc_html__( 'Author Image', 'elementskit-lite' ),
               'tab'       => Controls_Manager::TAB_STYLE,
               'condition' => [
                   'ekit_blog_posts_author_image' => 'yes',
               ],
           ]
       );

       $this->add_control(
        'ekit_blog_posts_author_img_size_width',
        [
            'label' => esc_html__( 'Image Width', 'elementskit-lite' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 30,
                    'max' => 200,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 30,
            ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-post-body  .meta-author .author-img' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

       $this->add_control(
        'ekit_blog_posts_author_img_size_height',
        [
            'label' => esc_html__( 'Image Height', 'elementskit-lite' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 30,
                    'max' => 200,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 30,
            ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-post-body  .meta-author .author-img' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'      => 'ekit_blog_posts_author_img_shadow',
               'selector'  => '{{WRAPPER}} .elementskit-post-body .author-img',
           ]
       );

       $this->add_group_control(
           Group_Control_Border::get_type(),
           [
               'name'     => 'ekit_blog_posts_author_img_border',
               'label'    => esc_html__( 'Border', 'elementskit-lite' ),
               'selector' => '{{WRAPPER}} .elementskit-post-body .author-img',
           ]
       );

       $this->add_control(
           'ekit_blog_posts_author_img_margin',
           [
               'label' => esc_html__( 'Margin', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', '%', 'em' ],
               'default' => [
                   'top' => '0',
                   'right' => '15',
                   'bottom' => '0',
                   'left' => '0',
                   'isLinked' => false,
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body .author-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_author_img_radius',
           [
               'label'     => esc_html__( 'Radius', 'elementskit-lite' ),
               'type'      => Controls_Manager::DIMENSIONS,
               'size_units'=> [ 'px', '%', 'em' ],
               'separator' => 'after',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-post-body .author-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->end_controls_section();




       // Button
       $this->start_controls_section(
           'ekit_blog_posts_btn_section_style',
           [
               'label' =>esc_html__( 'Button', 'elementskit-lite' ),
               'tab' => Controls_Manager::TAB_STYLE,
               'condition' => ['ekit_blog_posts_read_more' => 'yes', 'ekit_blog_posts_layout_style!' => 'elementskit-blog-block-post']
           ]
       );

       $this->add_responsive_control(
           'ekit_blog_posts_btn_text_padding',
           [
               'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px', 'em', '%' ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_responsive_control(
            'ekit_blog_posts_btn_normal_icon_font_size',
            array(
                'label'      => esc_html__( 'Font Size', 'elementskit-lite' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array(
                    'px', 'em', 'rem',
                ),
                'range'      => array(
                    'px' => array(
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .elementskit-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-btn svg'  => 'max-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

       $this->add_group_control(
           Group_Control_Typography::get_type(),
           [
               'name' => 'ekit_blog_posts_btn_typography',
               'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
               'selector' => '{{WRAPPER}} .elementskit-btn',
           ]
       );

       $this->start_controls_tabs( 'ekit_blog_posts_btn_tabs_style' );

       $this->start_controls_tab(
           'ekit_blog_posts_btn_tabnormal',
           [
               'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_text_color',
           [
               'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-btn svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
               ],
           ]
       );
       $this->add_group_control(
           Group_Control_Background::get_type(),
           array(
               'name'     => 'ekit_blog_posts_btn_bg_color',
               'selector' => '{{WRAPPER}} .elementskit-btn',
           )
       );

       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_btn_tab_button_hover',
           [
               'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_hover_color',
           [
               'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'default' => '#ffffff',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn:hover' => 'color: {{VALUE}};',
                   '{{WRAPPER}} .elementskit-btn:hover svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Background::get_type(),
           array(
               'name'     => 'ekit_blog_posts_btn_bg_hover_color',
               'selector' => '{{WRAPPER}} .elementskit-btn:hover',
           )
       );

       $this->end_controls_tab();
       $this->end_controls_tabs();

       $this->add_control(
           'ekit_blog_posts_btn_border_style',
           [
               'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::SELECT,
               'options' => [
                   '' => esc_html__( 'None', 'elementskit-lite' ),
                   'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
                   'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
                   'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
                   'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
                   'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'border-style: {{VALUE}};',
               ],
           ]
       );
       $this->add_control(
           'ekit_blog_posts_btn_border_dimensions',
           [
               'label' => esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
               'condition'  => [
                   'ekit_blog_posts_btn_border_style!' => ''
               ]
           ]
       );
       $this->start_controls_tabs( 'xs_tabs_button_border_style' );
       $this->start_controls_tab(
           'ekit_blog_posts_btn_tab_border_normal',
           [
               'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
               'condition'  => [
                   'ekit_blog_posts_btn_border_style!' => ''
               ]
           ]
       );

       $this->add_control(
           'ekit_blog_posts_btn_border_color',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' => 'border-color: {{VALUE}};',
               ],
               'condition'  => [
                    'ekit_blog_posts_btn_border_style!' => ''
                ]
           ]
       );
       $this->end_controls_tab();

       $this->start_controls_tab(
           'ekit_blog_posts_btn_tab_button_border_hover',
           [
               'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
               'condition'  => [
                   'ekit_blog_posts_btn_border_style!' => ''
               ]
           ]
       );
       $this->add_control(
           'ekit_blog_posts_btn_hover_border_color',
           [
               'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
               'type' => Controls_Manager::COLOR,
               'default' => '',
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn:hover' => 'border-color: {{VALUE}};',
               ],
               'condition'  => [
                    'ekit_blog_posts_btn_border_style!' => ''
                ]
           ]
       );
       $this->end_controls_tab();
       $this->end_controls_tabs();
       $this->add_responsive_control(
           'ekit_blog_posts_btn_border_radius',
           [
               'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
               'type' => Controls_Manager::DIMENSIONS,
               'size_units' => [ 'px'],
               'default' => [
                   'top' => '',
                   'right' => '',
                   'bottom' => '' ,
                   'left' => '',
               ],
               'selectors' => [
                   '{{WRAPPER}} .elementskit-btn' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               ],
           ]
       );

       $this->add_group_control(
           Group_Control_Box_Shadow::get_type(), [
               'name'     => 'ekit_blog_posts_btn_box_shadow_group',
               'selector' => '{{WRAPPER}} .elementskit-btn',
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
       $settings = $this->get_settings();
       extract($settings);

       $highlight_border = $ekit_blog_posts_content_highlight_border == 'yes' ? 'ekit-highlight-border' : '';
       $ekit_blog_posts_offset = ($ekit_blog_posts_offset == '') ? 0 : $ekit_blog_posts_offset;

       $default    = [
           'orderby'           => array( $ekit_blog_posts_order_by => $ekit_blog_posts_sort ),
           'posts_per_page'    => $ekit_blog_posts_num,
           'offset'            => $ekit_blog_posts_offset,
           'post_status'       => 'publish'
       ];

        if($ekit_blog_posts_is_manual_selection === 'yes'){
            $default = \ElementsKit_Lite\Utils::array_push_assoc(
                $default, 'post__in', (!empty($ekit_blog_posts_manual_selection  && count($ekit_blog_posts_manual_selection) > 0 )) ? $ekit_blog_posts_manual_selection : [-1]
            );
        }

        if($ekit_blog_posts_is_manual_selection == '' && $ekit_blog_posts_cats != ''){
            $default = \ElementsKit_Lite\Utils::array_push_assoc(
                $default, 'category__in', $ekit_blog_posts_cats
            );
        }

        // Post Items
        $this->add_render_attribute(
            'post_items',
            [
                'id'    => 'post-items--' . $this->get_id(),
                'class' => 'row post-items',
            ]
        );

        if ($grid_masonry === 'yes'):
            $this->add_render_attribute('post_items', 'data-masonry-config', 'true');
        endif;

       // Post Query
       $post_query = new \WP_Query( $default );

       ?>
        <div <?php echo $this->get_render_attribute_string('post_items'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
        <?php if ( 'elementskit-blog-block-post' == $ekit_blog_posts_layout_style ) {
			$ekit_blog_posts_column = 'ekit-md-12';
        }
        $column_size   = 'ekit-md-12';
        $img_order     = 'order-1';
        $content_order = 'order-2';

        if ( 'right' == $ekit_blog_posts_feature_img_float ) {
            $img_order = 'order-2';
            $content_order = 'order-1';
        }
        while ( $post_query->have_posts() ) : $post_query->the_post();
            if ( 'yes' == $ekit_blog_posts_feature_img
            && has_post_thumbnail()
            && ( 'yes' == $ekit_blog_posts_title
            || 'yes' == $ekit_blog_posts_content
            || 'yes' == $ekit_blog_posts_meta
            || 'yes' == $ekit_blog_posts_author ) ) {
                $column_size = 'ekit-md-6';
            }

			ob_start(); ?>
				<h2 class="entry-title">
					<a href="<?php the_permalink(); ?>">
						<?php if($ekit_blog_posts_title_trim !='' || $ekit_blog_posts_title_trim > 0):
							echo esc_html( wp_trim_words(get_the_title(), $ekit_blog_posts_title_trim) );
						else:
							the_title();
						endif; ?>
					</a>
				</h2>
			<?php $title_html = ob_get_clean();

			$meta_data_html = '';
			if ( 'yes' == $ekit_blog_posts_meta ):
				ob_start(); ?>
					<?php if($ekit_blog_posts_meta == 'yes' && $ekit_blog_posts_meta_select != '') : ?>
						<div class="post-meta-list">
							<?php foreach($ekit_blog_posts_meta_select as $meta): ?>
								<?php if($meta == 'author'): ?>
									<span class="meta-author">
										<?php if( 'yes' == $ekit_blog_posts_author_image): ?>
											<span class="author-img">
												<?php echo get_avatar( get_the_author_meta( "ID" )); ?>
											</span>
										<?php else: ?>

											<?php
												// new icon
												$migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_author_icons'] );
												// Check if its a new widget without previously selected icon using the old Icon control
												$is_new = empty( $settings['ekit_blog_posts_meta_author_icon'] );
												if ( $is_new || $migrated ) {
													// new icon
													Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_author_icons'], [ 'aria-hidden' => 'true'] );
												} else {
													?>
													<i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_author_icon']); ?>" aria-hidden="true"></i>
													<?php
												}
											?>

										<?php endif; ?>
										<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="author-name"><?php the_author_meta('display_name'); ?></a>
									</span>
								<?php endif; ?>
								<?php if($meta == 'date'): ?>
									<span class="meta-date">

										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_date_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_blog_posts_meta_date_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_date_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_date_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>

										<span class="meta-date-text">
											<?php echo esc_html( get_the_date() ); ?>
										</span>
									</span>
								<?php endif; ?>
								<?php if($meta == 'category'): ?>
									<span class="post-cat">

										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_category_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_blog_posts_meta_category_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_category_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_category_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>

										<?php echo get_the_category_list( ' | ' ); // phpcs:ignore WordPress.Security.EscapeOutput -- Already escaped by WordPress ?>
									</span>
								<?php endif; ?>
								<?php if($meta == 'comment'): ?>
									<span class="post-comment">

										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_meta_comment_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_blog_posts_meta_comment_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_blog_posts_meta_comment_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_blog_posts_meta_comment_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>

										<a href="<?php comments_link(); ?>"><?php echo esc_html( get_comments_number() ); ?></a>
									</span>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif;
				$meta_data_html .= ob_get_clean();
			endif;

            $column_size = self::format_colname($column_size);
            $ekit_blog_posts_column = self::format_colname($ekit_blog_posts_column);
            ?>
            <div class="<?php echo esc_attr( $ekit_blog_posts_column ); ?>">

                <?php if ( 'elementskit-blog-block-post' == $ekit_blog_posts_layout_style ): ?>
                    <div class="<?php echo esc_attr( $ekit_blog_posts_layout_style ); ?>">
                        <div class="row no-gutters">
                            <?php if ( 'yes' == $ekit_blog_posts_feature_img && has_post_thumbnail() ): ?>
                                <div class="<?php echo esc_attr( $column_size.' '.$img_order ); ?>">
                                    <a href="<?php the_permalink(); ?>" class="elementskit-entry-thumb">
                                        <img src="<?php the_post_thumbnail_url( esc_attr( $ekit_blog_posts_feature_img_size_size ) ); ?>" alt="<?php the_title(); ?>">
                                    </a><!-- .elementskit-entry-thumb END -->
                                </div>
                            <?php endif; ?>

                            <div class="<?php echo esc_attr( $column_size.' '.$content_order ); ?>">
                                <div class="elementskit-post-body <?php echo esc_attr($highlight_border); ?>">
                                    <div class="elementskit-entry-header">
                                        <?php if ( 'yes' == $ekit_blog_posts_title && 'before_meta' == $ekit_blog_posts_title_position ): ?>
											<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                                        <?php endif; ?>

                                            <?php if ('after_content' != $ekit_blog_posts_title_position ): ?>
                                                <?php echo $meta_data_html; // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 2972 ?>
                                            <?php endif; ?>

                                            <?php if ('yes' == $ekit_blog_posts_title && 'after_content' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                                            <?php endif; ?>

                                            <?php if ( 'yes' == $ekit_blog_posts_title && 'after_meta' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                                            <?php endif; ?>
                                    </div><!-- .elementskit-entry-header END -->

                                    <?php if ( 'yes' == $ekit_blog_posts_content ): ?>
                                        <div class="elementskit-post-footer">
                                            <?php if($ekit_blog_posts_content_trim !='' || $ekit_blog_posts_content_trim > 0): ?>
                                                <p><?php echo esc_html( wp_trim_words(get_the_excerpt(), $ekit_blog_posts_content_trim) ); ?></p>
                                            <?php else: ?>
                                                <?php the_excerpt(); ?>
                                            <?php endif; ?>
                                            <?php if ( 'after_content' == $ekit_blog_posts_title_position ): ?>
                                                <?php echo $meta_data_html; // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 2972 ?>
                                            <?php endif; ?>
                                        </div><!-- .elementskit-post-footer END -->
                                    <?php endif; ?>
                                </div><!-- .elementskit-post-body END -->
                            </div>
                        </div>
                    </div><!-- .elementskit-blog-block-post .radius .gradient-bg END -->
                <?php else: ?>
                    <div class="<?php echo esc_attr( $ekit_blog_posts_layout_style ); ?>">
                        <div class="elementskit-entry-header">
                            <?php if ( 'elementskit-post-image-card' == $ekit_blog_posts_layout_style && 'yes' == $ekit_blog_posts_feature_img && has_post_thumbnail() ): ?>
                                <a href="<?php the_permalink(); ?>" class="elementskit-entry-thumb">
                                    <img src="<?php the_post_thumbnail_url( esc_attr( $ekit_blog_posts_feature_img_size_size ) ); ?>" alt="<?php the_title(); ?>">
                                </a><!-- .elementskit-entry-thumb END -->
                                <?php if('yes' == $settings['ekit_blog_posts_floating_date']) : ?>
                                <?php if($ekit_blog_posts_floating_date_style == 'style1'): ?>
                                    <div class="elementskit-meta-lists">
                                        <div class="elementskit-single-meta"><span class="elementskit-meta-wraper"><strong><?php echo get_the_date( 'd' );?></strong><?php echo get_the_date( 'M' );?></span></div>
                                    </div>
                                <?php elseif($ekit_blog_posts_floating_date_style == 'style2'): ?>
                                    <div class="elementskit-meta-lists elementskit-style-tag">
                                        <div class="elementskit-single-meta <?php echo esc_attr($settings['ekit_blog_posts_floating_date_triangle_position_alignment']); ?>"><span class="elementskit-meta-wraper"><strong><?php echo get_the_date( 'd' );?></strong><?php echo get_the_date( 'M' );?></span></div>
                                    </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if('yes' == $settings['ekit_blog_posts_floating_category']) : ?>
                                <div class="elementskit-meta-categories">
                                    <span class="elementskit-meta-wraper">
                                        <span><?php echo get_the_category_list( '</span><span>' ); // phpcs:ignore WordPress.Security.EscapeOutput -- Already escaped by WordPress ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>

							<?php if ( 'elementskit-post-card' == $ekit_blog_posts_layout_style) :
								if('yes' == $ekit_blog_posts_title && 'before_meta' == $ekit_blog_posts_title_position ): ?>
									<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>

									<?php if ( 'yes' == $ekit_blog_posts_title_separator ): ?>
										<span class="elementskit-border-hr"></span>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ( 'after_content' != $ekit_blog_posts_title_position ): ?>
									<?php echo $meta_data_html; // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 2972 ?>
								<?php endif; ?>

								<?php if ( 'yes' == $ekit_blog_posts_title && 'after_content' == $ekit_blog_posts_title_position ): ?>
									<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>

									<?php if ( 'yes' == $ekit_blog_posts_title_separator ): ?>
										<span class="elementskit-border-hr"></span>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ( 'yes' == $ekit_blog_posts_title && 'after_meta' == $ekit_blog_posts_title_position ): ?>
									<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>

									<?php if ( 'yes' == $ekit_blog_posts_title_separator ): ?>
										<span class="elementskit-border-hr"></span>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
						</div><!-- .elementskit-entry-header END -->

						<div class="elementskit-post-body <?php echo esc_attr($highlight_border); ?>">
							<?php if ( 'elementskit-post-image-card' == $ekit_blog_posts_layout_style):
								if ('yes' == $ekit_blog_posts_title && 'before_meta' == $ekit_blog_posts_title_position ): ?>
									<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
								<?php endif; ?>

								<?php if ( 'after_content' != $ekit_blog_posts_title_position ): ?>
									<?php echo $meta_data_html; // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 2972 ?>
								<?php endif; ?>

								<?php if ( 'yes' == $ekit_blog_posts_title && 'after_content' == $ekit_blog_posts_title_position ): ?>
									<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
								<?php endif; ?>

								<?php if ( 'yes' == $ekit_blog_posts_title && 'after_meta' == $ekit_blog_posts_title_position ): ?>
									<?php echo wp_kses($title_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ( 'yes' == $ekit_blog_posts_content ): ?>
								<?php if($ekit_blog_posts_content_trim !='' || $ekit_blog_posts_content_trim > 0): ?>
									<p><?php echo esc_html( wp_trim_words(get_the_excerpt(), $ekit_blog_posts_content_trim) ); ?></p>
								<?php else: ?>
									<?php the_excerpt(); ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ( 'after_content' == $ekit_blog_posts_title_position ): ?>
									<?php echo $meta_data_html; // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 2972 ?>
							<?php endif; ?>
                            <?php
                            if($ekit_blog_posts_read_more == 'yes'):
                                $btn_text = $settings['ekit_blog_posts_btn_text'];
                                $btn_class = ($settings['ekit_blog_posts_btn_class'] != '') ? $settings['ekit_blog_posts_btn_class'] : '';
                                $btn_id = ($settings['ekit_blog_posts_btn_id'] != '') ? 'id='.$settings['ekit_blog_posts_btn_id'] : '';
                                $icon_align = $settings['ekit_blog_posts_btn_icon_align'];
                                
                                // Reset Whitespace for this specific widget
                                $btn_class .= ' whitespace--normal';
                                ?>
                                <div class="btn-wraper">
                                    <?php if($icon_align == 'right'): ?>
                                        <a href="<?php echo esc_url( the_permalink() ); ?>" class="elementskit-btn <?php echo esc_attr( $btn_class ); ?>" <?php echo esc_attr($btn_id); ?>>
                                            <?php echo esc_html( $btn_text ); ?>
                                            <?php if($settings['ekit_blog_posts_btn_icons__switch'] === 'yes'): 

                                                // new icon
                                                $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_btn_icons'] );
                                                // Check if its a new widget without previously selected icon using the old Icon control
                                                $is_new = empty( $settings['ekit_blog_posts_btn_icon'] );
                                                if ( $is_new || $migrated ) {
                                                    // new icon
                                                    Icons_Manager::render_icon( $settings['ekit_blog_posts_btn_icons'], [ 'aria-hidden' => 'true' ] );
                                                } else {
                                                    ?>
                                                    <i class="<?php echo esc_attr($settings['ekit_blog_posts_btn_icon']); ?>" aria-hidden="true"></i>
                                                    <?php
                                                }
                                                
                                                endif; ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if($icon_align == 'left'): ?>
                                        <a href="<?php echo esc_url( the_permalink() ); ?>" class="elementskit-btn <?php echo esc_attr( $btn_class ); ?>" <?php echo esc_attr($btn_id); ?>>
                                        <?php if($settings['ekit_blog_posts_btn_icons__switch'] === 'yes'): 
                                                // new icon
                                                $migrated = isset( $settings['__fa4_migrated']['ekit_blog_posts_btn_icons'] );
                                                // Check if its a new widget without previously selected icon using the old Icon control
                                                $is_new = empty( $settings['ekit_blog_posts_btn_icon'] );
                                                if ( $is_new || $migrated ) {
                                                    // new icon
                                                    Icons_Manager::render_icon( $settings['ekit_blog_posts_btn_icons'], [ 'aria-hidden' => 'true' ] );
                                                } else {
                                                    ?>
                                                    <i class="<?php echo esc_attr($settings['ekit_blog_posts_btn_icon']); ?>" aria-hidden="true"></i>
                                                    <?php
                                                }
                                                
                                            endif; ?>
                                            <?php echo esc_html( $btn_text ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div><!-- .elementskit-post-body END -->
                    </div>
                <?php endif; ?>

            </div>
        <?php endwhile; ?>
        </div>
       <?php
        wp_reset_postdata();

        if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ):
            $this->render_editor_script();
        endif;
   }

   protected function render_editor_script() {
       ?>
       <script>
           (function ($) {
               'use strict';

               $(function () {
                   var $postItems = $('#post-items--<?php echo esc_attr( $this->get_id() ); ?>[data-masonry-config]');

                   $postItems.imagesLoaded(function () {
                       $postItems.masonry();
                   });
               });
           }(jQuery));
       </script>
       <?php
   }
}
