<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Mail_Chimp_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Mail_Chimp extends Widget_Base {
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
        return 'https://wpmet.com/doc/mailchimp-3/';
    }
    protected function is_dynamic_content(): bool {
        return false;
    }

	public function __get_lists() {
		$options = ['' => 'Select List'];
		$dataApi = Handler::get_data();
		$token = isset($dataApi['token']) ? $dataApi['token'] : '';

		$server = explode('-', $token);

		if (!isset($server[1])) {
			return $options;
		}

		$url = 'https://' . $server[1] . '.api.mailchimp.com/3.0/lists';

		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'apikey ' . $token,
				'Content-Type' => 'application/json; charset=utf-8',
			],
		]);

		if (is_array($response) && !is_wp_error($response)) {
			$body = (array) json_decode($response['body']);
			$listed = isset($body['lists']) ? $body['lists'] : [];
			if (is_array($listed) && sizeof($listed) > 0) {
				foreach ($listed as $v) {
					$options[$v->id] = $v->name;
				}
			}
		}
		return $options;
	}

    protected function register_controls() {

        //start content Mail form design
        $this->start_controls_section(
            'ekit_mail_chimp_section_form', [
                'label' => esc_html__( 'Form ', 'elementskit-lite' ),
            ]
        );

		$this->add_control(
            'ekit_mail_chimp_select_check_api',
            [
                'raw' => '<strong>' . esc_html__( 'Please note!', 'elementskit-lite' ) . '</strong> ' . esc_html__( 'Please set API Key in ElementsKit_Lite Dashboard - User Data - MailChimp and Create Campaign..', 'elementskit-lite' ),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'render_type' => 'ui',
                'condition' => [
                    'ekit_mail_chimp_select_listed_id' => '',
                ],
            ]
        );
		$this->add_control(
			'ekit_mail_chimp_select_listed_id',
			[
				'label' => esc_html__( 'Select List', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->__get_lists(),
				'description' => esc_html__('Create a campaign in mailchimp account <a href="https://mailchimp.com/help/create-a-regular-email-campaign/#Create_a_campaign" target="_blank"> Create Campaign</a>', 'elementskit-lite'),
			]
		);	

		$this->add_control(
            'ekit_mail_chimp_double_opt_in',
            [
                'label' => esc_html__( 'Double Opt-in', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => esc_html__('If you enable this feature , then you must need to enable it inside Mailchimp campaign settings. Otherwise please disable it.', 'elementskit-lite')
            ]
        );
 
        $this->add_control(
            'ekit_mail_chimp_opt_in_success_message',
            [
                'label' => esc_html__( 'Opt-in Success Message', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
                'default' => esc_html__( 'Please check your mail and confirm subscribe', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Type your title here', 'elementskit-lite' ),
                'condition' => [
                    'ekit_mail_chimp_double_opt_in' => 'yes',
                ],
            ]
        );

		// show name control
		$this->add_control(
			'ekit_mail_chimp_section_form_name_show',
			[
				'label' => esc_html__( 'Show Name', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		// first name
		$this->add_control(
			'ekit_mail_first_heading_title',
			[
				'label' => esc_html__( 'First Name ', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes',
				],
			]
		);
		$this->add_control(
            'ekit_mail_chimp_first_name_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'First name', 'elementskit-lite' ),
				'label_block'	 => false,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
            ]
		);
		$this->add_control(
            'ekit_mail_chimp_first_name_placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'Your first name', 'elementskit-lite' ),
				'label_block'	 => false,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_mail_chimp_first_name_icon_show',
			[
				'label' => esc_html__( 'Show Input Group Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
			]
		);
		$this->add_control(
            'ekit_mail_chimp_first_name_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_mail_chimp_first_name_icon',
                'default' => [
                    'value' => 'icon icon-user',
                    'library' => 'ekiticons',
                ],
                'condition' => [
					'ekit_mail_chimp_first_name_icon_show' => 'yes',
					'ekit_mail_chimp_section_form_name_show' => 'yes'
                ]
            ]
		);
		$this->add_control(
			'ekit_mail_chimp_first_name_icon_before_after',
			[
				'label' => esc_html__( 'Before After', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_mail_chimp_first_name_icon_show' => 'yes',
					'ekit_mail_chimp_first_name_icons!' => '',
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_mail_last_and_first_name_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
			]
		);

		// last name
		$this->add_control(
			'ekit_mail_last_heading_title',
			[
				'label' => esc_html__( 'Last Name:', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes',
				],
			]
		);
		$this->add_control(
            'ekit_mail_chimp_last_name_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'Last name:', 'elementskit-lite' ),
				'label_block'	 => false,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
            ]
        );
		$this->add_control(
            'ekit_mail_chimp_last_name_placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'Your last name', 'elementskit-lite' ),
				'label_block'	 => false,
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
            ]
		);

		$this->add_control(
			'ekit_mail_chimp_last_name_icon_show',
			[
				'label' => esc_html__( 'Show Input Group Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
			]
		);
		$this->add_control(
            'ekit_mail_chimp_last_name_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_mail_chimp_last_name_icon',
                'default' => [
                    'value' => 'icon icon-user',
                    'library' => 'ekiticons',
                ],
                'condition' => [
					'ekit_mail_chimp_last_name_icon_show' => 'yes',
					'ekit_mail_chimp_section_form_name_show' => 'yes'
                ]
            ]
		);
		$this->add_control(
			'ekit_mail_chimp_last_name_icon_before_after',
			[
				'label' => esc_html__( 'Before After', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_mail_chimp_last_name_icon_show' => 'yes',
					'ekit_mail_chimp_last_name_icons!' => '',
					'ekit_mail_chimp_section_form_name_show' => 'yes'
				]
			]
		);

		// phone number
		$this->add_control(
			'ekit_mail_chimp_section_form_phone_show',
			[
				'label' => esc_html__( 'Show Phone:', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before'
			]
		);
		$this->add_control(
			'ekit_mail_phone_heading_title',
			[
				'label' => esc_html__( 'Phone:', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_mail_chimp_section_form_phone_show' => 'yes',
				],
			]
		);
		$this->add_control(
            'ekit_mail_chimp_phone_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'Phone', 'elementskit-lite' ),
				'label_block'	 => false,
				'condition' => [
					'ekit_mail_chimp_section_form_phone_show' => 'yes'
				]
            ]
        );
		$this->add_control(
            'ekit_mail_chimp_phone_placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'Your phone No', 'elementskit-lite' ),
				'label_block'	 => false,
				'condition' => [
					'ekit_mail_chimp_section_form_phone_show' => 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_mail_chimp_phone_icon_show',
			[
				'label' => esc_html__( 'Show Input Group Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_mail_chimp_section_form_phone_show' => 'yes'
				]
			]
		);
		$this->add_control(
            'ekit_mail_chimp_phone_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_mail_chimp_phone_icon',
                'default' => [
                    'value' => 'icon icon-phone-handset',
                    'library' => 'ekiticons',
                ],
                'condition' => [
					'ekit_mail_chimp_phone_icon_show' => 'yes',
					'ekit_mail_chimp_section_form_phone_show' => 'yes'
                ]
            ]
		);
		$this->add_control(
			'ekit_mail_chimp_phone_icon_before_after',
			[
				'label' => esc_html__( 'Before After', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_mail_chimp_phone_icon_show' => 'yes',
					'ekit_mail_chimp_phone_icons!' => '',
					'ekit_mail_chimp_section_form_phone_show' => 'yes'
				]
			]
		);

		// Email Address
		$this->add_control(
			'ekit_mail_email_address_heading_title',
			[
				'label' => esc_html__( 'Email Address:', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
            'ekit_mail_chimp_email_address_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' => esc_html__( 'Email address', 'elementskit-lite' ),
				'label_block'	 => false,

            ]
        );
		$this->add_control(
            'ekit_mail_chimp_email_address_placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'placeholder' 	 => esc_html__( 'Your email address', 'elementskit-lite' ),
				'label_block'	 => false,
            ]
		);

		$this->add_control(
			'ekit_mail_chimp_email_icon_show',
			[
				'label' => esc_html__( 'Show Input Group Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
            'ekit_mail_chimp_email_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_mail_chimp_email_icon',
                'default' => [
                    'value' => 'icon icon-envelope',
                    'library' => 'ekiticons',
                ],
                'condition' => [
					'ekit_mail_chimp_email_icon_show' => 'yes',
                ]
            ]
		);
		$this->add_control(
			'ekit_mail_chimp_email_icon_before_after',
			[
				'label' => esc_html__( 'Before After', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_mail_chimp_email_icon_show' => 'yes',
					'ekit_mail_chimp_email_icons!' => '',
				]
			]
		);

		$this->add_control(
			'ekit_mail_chimp_email_and_button_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		// submit button text
		$this->add_control(
            'ekit_mail_chimp_submit',
            [
                'label' => esc_html__( 'Submit Button Text', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Sign Up', 'elementskit-lite' ),
				'label_block'	 => false,
            ]
        );
		$this->add_control(
			'ekit_mail_chimp_submit_button_heading',
			[
				'label' => esc_html__( 'Submit Button:', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'ekit_mail_chimp_submit_icon_show',
			[
				'label' => esc_html__( 'Show Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'ekit_mail_chimp_submit_icons',
			[
				'label' => esc_html__( 'Button Icons', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_mail_chimp_submit_icon',
                'default' => [
                    'value' => 'icon icon-tick',
                    'library' => 'ekiticons',
                ],
				'condition' => [
					'ekit_mail_chimp_submit_icon_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'ekit_mail_chimp_submit_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_mail_chimp_submit_icon_show' => 'yes',
					'ekit_mail_chimp_submit_icons!' => ''
				]
			]
		);

		$this->add_control(
            'ekit_mail_chimp_form_style_switcher',
            [
                'label' =>esc_html__( 'Form Style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' =>esc_html__( 'Inline', 'elementskit-lite' ),
                    'no' =>esc_html__( 'Full Width', 'elementskit-lite' ),
                ],
            ]
		);
		
		$this->add_control(
			'ekit_mail_chimp_success_message',
			[
				'label' => __( 'Success Message', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'default' => __( 'Successfully listed this email', 'elementskit-lite' ),
				'placeholder' => __( 'Type your title here', 'elementskit-lite' ),
			]
		);

		$this->end_controls_section();
		// end content form

		// label
		$this->start_controls_section(
			'ekit_mail_chimp_input_label_style',
			[
				'label' => esc_html__( 'Label', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_label_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit_input_label',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_label_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_label_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// input style
		$this->start_controls_section(
			'ekit_mail_chimp_input_style',
			[
				'label' => esc_html__( 'Input', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_form_control',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_style_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit_form_control',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_form_control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_style_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_form_control',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_style_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_form_control, {{WRAPPER}} .ekit_form_control:focus',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'		=> [
					'top'		=> 0,
					'right'		=> 20,
					'bottom'	=> 0,
					'left'		=> 20
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_form_control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_mail_chimp_input_style_width__switch',
			[
				'label' => esc_html__( 'Use Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'	=> [
					'unit'	=> '%',
					'size'	=> 66
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_container' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'ekit_mail_chimp_input_style_width__switch' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_margin_bottom',
			[
				'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_wraper:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_mail_chimp_form_style_switcher!' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_margin_right',
			[
				'label' => esc_html__( 'Margin Right', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_inline_form .elementskit_input_wraper:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_mail_chimp_form_style_switcher' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'inline_margin_bottom',
			[
				'label'		=> esc_html__( 'Margin Bottom', 'elementskit-lite' ),
				'type'		=> Controls_Manager::SLIDER,
				'devices'	=> ['desktop', 'mobile'],
				'selectors' => [
					'{{WRAPPER}} .has-extra-fields > .elementskit_input_wraper:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_mail_chimp_form_style_switcher' => 'yes', // Inline Style
					'ekit_mail_chimp_section_form_name_show' => 'yes', // Show Names
				]
			]
		);

		$this->add_control(
			'ekit_mail_chimp_input_style_placeholder_heading',
			[
				'label' => esc_html__( 'Placeholder', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_placeholder_color',
			[
				'label' => esc_html__( 'Placeholder Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ekit_form_control::-webkit-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_form_control::-moz-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_form_control:-ms-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_form_control:-moz-placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_style_placeholder_font_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
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
					'size' => 14,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_form_control::-webkit-input-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_form_control::-moz-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_form_control:-ms-input-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_form_control:-moz-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_mail_chimp_button_style_holder',
			[
				'label' => esc_html__( 'Button', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_mail_chimp_button_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-submit',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_border_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	=> [
					'top'		=> 8,
					'right'		=> 20,
					'bottom'	=> 8,
					'left'		=> 20
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_mail_chimp_button_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_mail_chimp_button_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-submit',
			]
		);

		$this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_mail_chimp_button_title_shadow',
                'selector' => '{{WRAPPER}} .ekit-mail-submit' ,
            ]
		);

		$this->add_control(
			'ekit_mail_chimp_button_style_use_width_height',
			[
				'label' => esc_html__( 'Use Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_mail_chimp_button_style_use_width_height' => 'yes'
				]
			]
		);


		$this->add_responsive_control(
			'ekit_mail_chimp_button_style_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
            'ekit_mail_chimp_button_normal_and_hover_tabs'
        );
        $this->start_controls_tab(
            'ekit_mail_chimp_button_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-mail-submit svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_mail_chimp_button_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-mail-submit',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->end_controls_tab();
        $this->start_controls_tab(
            'ekit_mail_chimp_button_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-mail-submit:hover svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_mail_chimp_button_background_hover',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-mail-submit:before',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ekit_mail_chimp_button_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_icon_padding_right',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit > i, {{WRAPPER}} .ekit-mail-submit > svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_mail_chimp_submit_icon_position' => 'before'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_button_icon_padding_left',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-submit > i, {{WRAPPER}} .ekit-mail-submit > svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_mail_chimp_submit_icon_position' => 'after'
				]
			]
		);

		$this->add_responsive_control(
            'ekit_simple_tab_title_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-mail-submit > i, {{WRAPPER}} .ekit-mail-submit > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-mail-submit > i, {{WRAPPER}} .ekit-mail-submit > svg' => 'max-width: {{SIZE}}{{UNIT}}; height: auto',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_mail_chimp_input_icon_style_holder',
			[
				'label' => esc_html__( 'Input Icon', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_icon_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit_input_group_text',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->add_control(
			'ekit_mail_chimp_input_icon_color_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_group_text i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit_input_group_text svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_icon_font_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_group_text' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit_input_group_text svg'	=> 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_mail_chimp_input_icon_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit_input_group_text',
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_group_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_input_icon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit_input_group_text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_mail_chimp_success_error',
			[
				'label' => esc_html__( 'Sucess & Error message', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_success_error_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_mail_chimp_success_error_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_mail_chimp_success_error_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-mail-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_mail_chimp_success_error_typography',
			'selector'	 => '{{WRAPPER}} .ekit-mail-message',
			]
		);

		$this->add_control(
            'ekit_mail_chimp_success_heading',
            [
                'label' => esc_html__( 'Success:', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
		);
		
		$this->add_responsive_control(
			'ekit_mail_chimp_success_color', [
				'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ekit-mail-message.success' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_mail_chimp_success_bg_color',
				'label'		 => esc_html__( 'Background Color', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-message.success',
            )
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_mail_chimp_success_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-message.success',
			]
		);

		$this->add_control(
            'ekit_mail_chimp_error_heading',
            [
                'label' => esc_html__( 'Error:', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
		);
		
		$this->add_responsive_control(
			'ekit_mail_chimp_error_color', [
				'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ekit-mail-message.error' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_mail_chimp_error_bg_color',
				'label'		 => esc_html__( 'Background Color', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-message.error',
            )
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_mail_chimp_error_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-mail-message.error',
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
		extract($settings);
		
		$this->add_render_attribute(
			'content_wrapper',
			[
				'class'	=> 'elementskit_form_wraper'.($ekit_mail_chimp_form_style_switcher == 'yes' ? ' elementskit_inline_form' : '' ).(($ekit_mail_chimp_section_form_phone_show === 'yes' || $ekit_mail_chimp_section_form_name_show === 'yes') ? ' has-extra-fields' : ''),
			]
		);
		?>
		<div class="ekit-mail-chimp">
		<form method="post" class="ekit-mailChimpForm" data-listed="<?php echo esc_attr($ekit_mail_chimp_select_listed_id);?>" data-success-message="<?php echo esc_attr($ekit_mail_chimp_success_message); ?>" data-success-opt-in-message="<?php echo esc_attr($ekit_mail_chimp_opt_in_success_message)?>">
			<div class="ekit-mail-message"></div>
			<input type="hidden" name="double_opt_in" value="<?php echo esc_attr($ekit_mail_chimp_double_opt_in)?>">

				<div <?php echo $this->get_render_attribute_string('content_wrapper'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
				<?php if(isset($ekit_mail_chimp_section_form_name_show) && $ekit_mail_chimp_section_form_name_show == 'yes'):?>
					<div class="ekit-mail-chimp-name elementskit_input_wraper elementskit_input_container">
						<div class="elementskit_form_group">
							<?php if($ekit_mail_chimp_first_name_label != ''): ?>
							<label class="elementskit_input_label"><?php echo esc_html( $ekit_mail_chimp_first_name_label );?> </label>
							<?php endif; ?>
							<div class="elementskit_input_element_container <?php if(($ekit_mail_chimp_first_name_icon_show == 'yes') && ($ekit_mail_chimp_first_name_icons != '')) : ?>elementskit_input_group<?php endif; ?>">
								<?php if(($ekit_mail_chimp_first_name_icon_show == 'yes') && ($ekit_mail_chimp_first_name_icons != '') && ($ekit_mail_chimp_first_name_icon_before_after == 'before')) : ?>
								<div class="elementskit_input_group_prepend">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_first_name_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_first_name_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_first_name_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_first_name_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
								<input type="text" aria-label="firstname" class="ekit_user_first ekit_form_control <?php if(($ekit_mail_chimp_first_name_icon_show == 'yes') && ($ekit_mail_chimp_first_name_icons != '') && ($ekit_mail_chimp_first_name_icon_before_after == 'after')) : ?> ekit_append_input <?php endif; ?>"  name="firstname" placeholder="<?php echo esc_html( $ekit_mail_chimp_first_name_placeholder );?>" required />

								<?php if(($ekit_mail_chimp_first_name_icon_show == 'yes') && ($ekit_mail_chimp_first_name_icons != '') && ($ekit_mail_chimp_first_name_icon_before_after == 'after')) : ?>
								<div class="elementskit_input_group_append">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_first_name_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_first_name_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_first_name_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_first_name_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<?php // endif; ?>
					</div>
				<?php endif; ?>
				<?php if(isset($ekit_mail_chimp_section_form_name_show) && $ekit_mail_chimp_section_form_name_show == 'yes'):?>
					<div class="ekit-mail-chimp-name elementskit_input_wraper elementskit_input_container">
						<div class="elementskit_form_group">
							<?php if($ekit_mail_chimp_last_name_label != ''): ?>
							<label class="elementskit_input_label"><?php echo esc_html( $ekit_mail_chimp_last_name_label ); ?> </label>
							<?php endif; ?>
							<div class="elementskit_input_element_container <?php if(($ekit_mail_chimp_last_name_icon_show == 'yes') && ($ekit_mail_chimp_last_name_icons != '')) : ?>elementskit_input_group<?php endif; ?>">
								<?php if(($ekit_mail_chimp_last_name_icon_show == 'yes') && ($ekit_mail_chimp_last_name_icons != '') && ($ekit_mail_chimp_last_name_icon_before_after == 'before')) : ?>
								<div class="elementskit_input_group_prepend">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_last_name_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_last_name_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_last_name_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_last_name_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
								<input type="text" aria-label="lastname" class="ekit_user_last ekit_form_control <?php if(($ekit_mail_chimp_last_name_icon_show == 'yes') && ($ekit_mail_chimp_last_name_icons != '') && ($ekit_mail_chimp_last_name_icon_before_after == 'after')) : ?> ekit_append_input <?php endif; ?>" name="lastname" placeholder="<?php echo esc_html( $ekit_mail_chimp_last_name_placeholder );?>" required />

								<?php if(($ekit_mail_chimp_last_name_icon_show == 'yes') && ($ekit_mail_chimp_last_name_icons != '') && ($ekit_mail_chimp_last_name_icon_before_after == 'after')) : ?>
								<div class="elementskit_input_group_append">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_last_name_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_last_name_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_last_name_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_last_name_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<?php //endif; ?>
					</div>
				<?php endif;
				if(isset($ekit_mail_chimp_section_form_phone_show) && $ekit_mail_chimp_section_form_phone_show == 'yes'):?>
					<div class="ekit-mail-chimp-phone elementskit_input_wraper elementskit_input_container">
						<div class="elementskit_form_group">
							<?php if($ekit_mail_chimp_phone_label != ''): ?>
							<label class="elementskit_input_label"><?php echo esc_html( $ekit_mail_chimp_phone_label );?> </label>
							<?php endif; ?>
							<div class="elementskit_input_element_container <?php if(($ekit_mail_chimp_phone_icon_show == 'yes') && ($ekit_mail_chimp_phone_icons != '')) : ?>elementskit_input_group<?php endif; ?>">
								<?php if(($ekit_mail_chimp_phone_icon_show == 'yes') && ($ekit_mail_chimp_phone_icons != '') && ($ekit_mail_chimp_phone_icon_before_after == 'before')) : ?>
								<div class="elementskit_input_group_prepend">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_phone_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_phone_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_phone_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_phone_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
								<input type="tel" aria-label="phone" class="ekit_mail_phone ekit_form_control <?php if(($ekit_mail_chimp_phone_icon_show == 'yes') && ($ekit_mail_chimp_phone_icons != '') && ($ekit_mail_chimp_phone_icon_before_after == 'after')) : ?> ekit_append_input <?php endif; ?>" name="phone" placeholder="<?php echo esc_html( $ekit_mail_chimp_phone_placeholder ); ?>" required />

								<?php if(($ekit_mail_chimp_phone_icon_show == 'yes') && ($ekit_mail_chimp_phone_icons != '') && ($ekit_mail_chimp_phone_icon_before_after == 'after')) : ?>
								<div class="elementskit_input_group_append">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_phone_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_phone_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_phone_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_phone_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
					<div class="ekit-mail-chimp-email elementskit_input_wraper elementskit_input_container">
						<div class="elementskit_form_group">
							<?php if($ekit_mail_chimp_email_address_label != ''): ?>
							<label class="elementskit_input_label"><?php echo esc_html( $ekit_mail_chimp_email_address_label ); ?> </label>
							<?php endif; ?>
							<div class="elementskit_input_element_container <?php if(($ekit_mail_chimp_email_icon_show == 'yes') && ($ekit_mail_chimp_email_icons != '')) : ?>elementskit_input_group<?php endif; ?>">
								<?php if(($ekit_mail_chimp_email_icon_show == 'yes') && ($ekit_mail_chimp_email_icons != '') && ($ekit_mail_chimp_email_icon_before_after == 'before')) : ?>
								<div class="elementskit_input_group_prepend">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_email_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_email_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_email_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_email_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
								<input type="email" aria-label="email" name="email" class="ekit_mail_email ekit_form_control <?php if(($ekit_mail_chimp_email_icon_show == 'yes') && ($ekit_mail_chimp_email_icons != '') && ($ekit_mail_chimp_email_icon_before_after == 'after')) : ?> ekit_append_input <?php endif; ?>" placeholder="<?php echo esc_html( $ekit_mail_chimp_email_address_placeholder ); ?>" required />

								<?php if(($ekit_mail_chimp_email_icon_show == 'yes') && ($ekit_mail_chimp_email_icons != '') && ($ekit_mail_chimp_email_icon_before_after == 'after')) : ?>
								<div class="elementskit_input_group_append">
									<div class="elementskit_input_group_text">
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_email_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_mail_chimp_email_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_mail_chimp_email_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_mail_chimp_email_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="ekit_submit_input_holder elementskit_input_wraper">
						<button type="submit" aria-label="submit" class="ekit-mail-submit" name="ekit_mail_chimp"><?php if(($ekit_mail_chimp_submit_icon_show == 'yes') && ($ekit_mail_chimp_submit_icons != '') && ($ekit_mail_chimp_submit_icon_position == 'before')): ?> 

							<?php
								// new icon
								$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_submit_icons'] );
								// Check if its a new widget without previously selected icon using the old Icon control
								$is_new = empty( $settings['ekit_mail_chimp_submit_icon'] );
								if ( $is_new || $migrated ) {
									// new icon
									Icons_Manager::render_icon( $settings['ekit_mail_chimp_submit_icons'], [ 'aria-hidden' => 'true' ] );
								} else {
									?>
									<i class="<?php echo esc_attr($settings['ekit_mail_chimp_submit_icon']); ?>" aria-hidden="true"></i>
									<?php
								}
							?>

							<?php endif; ?><?php echo esc_html( $ekit_mail_chimp_submit );?><?php if(($ekit_mail_chimp_submit_icon_show == 'yes') && ($ekit_mail_chimp_submit_icons != '') && ($ekit_mail_chimp_submit_icon_position == 'after')): ?> 

								<?php
									// new icon
									$migrated = isset( $settings['__fa4_migrated']['ekit_mail_chimp_submit_icons'] );
									// Check if its a new widget without previously selected icon using the old Icon control
									$is_new = empty( $settings['ekit_mail_chimp_submit_icon'] );
									if ( $is_new || $migrated ) {
										// new icon
										Icons_Manager::render_icon( $settings['ekit_mail_chimp_submit_icons'], [ 'aria-hidden' => 'true' ] );
									} else {
										?>
										<i class="<?php echo esc_attr($settings['ekit_mail_chimp_submit_icon']); ?>" aria-hidden="true"></i>
										<?php
									}
								?>

							<?php endif; ?></button>
					</div>
				</div>
			</form>
		</div>
		<?php
	  }
}
