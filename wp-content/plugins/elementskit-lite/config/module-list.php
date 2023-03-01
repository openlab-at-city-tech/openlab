<?php

namespace ElementsKit_Lite\Config;

defined( 'ABSPATH' ) || exit;
class Module_List extends \ElementsKit_Lite\Core\Config_List {

	protected $type = 'module';
	
	protected function set_required_list() {

		$this->required_list = array(
			'dynamic-content' => array(
				'slug'    => 'dynamic-content',
				'package' => 'free',
			),
			'layout-manager' => array(
				'slug'    => 'layout-manager',
				'package' => 'free',
			),
			//            'controls' => [
			//                'slug' => 'controls',
			//                'package' => 'free',
			//            ],
		);
	}

	protected function set_optional_list() {

		$this->optional_list = apply_filters(
			'elementskit/modules/list',
			array(
				'elementskit-icon-pack' => array(
					'slug'       => 'elementskit-icon-pack',
					'title'      => 'ElementsKit Icon Pack',
					'package'    => 'free', // free, pro, pro-disabled
					//'path' => null,
					//'base_class_name' => null,
					//'live' => null
					'attributes' => array( 'new' ),
				),
				'header-footer' => array(
					'slug'    => 'header-footer',
					'title'   => 'Header Footer',
					'package' => 'free',
				),
				'megamenu' => array(
					'slug'    => 'megamenu',
					'package' => 'free',
					'title'   => 'Mega Menu',
				),
				'onepage-scroll' => array(
					'slug'    => 'onepage-scroll',
					'package' => 'free',
					'title'   => 'Onepage Scroll',
				),
				'widget-builder' => array(
					'slug'    => 'widget-builder',
					'package' => 'free',
					'title'   => 'Widget Builder',
				),
				'parallax' => array(
					'slug'    => 'parallax',
					'package' => 'pro-disabled',
					'title'   => 'Parallax Effects',
				),
				'sticky-content' => array(
					'slug'    => 'sticky-content',
					'package' => 'pro-disabled',
					'title'   => 'Sticky Content',
				),
				'facebook-messenger' => array(
					'slug'    => 'facebook-messenger',
					'package' => 'pro-disabled',
					'title'   => 'Facebook Messenger',
				),
				'conditional-content' => array(
					'slug'    => 'conditional-content',
					'package' => 'pro-disabled',
					'title'   => 'Conditional Content',
				),
				'copy-paste-cross-domain' => array(
					'slug'    => 'copy-paste-cross-domain',
					'package' => 'pro-disabled',
					'title'   => 'Cross-Domain Copy Paste',
				),
				'advanced-tooltip' => array(
					'slug'    => 'advanced-tooltip',
					'package' => 'pro-disabled',
					'title'   => 'Advanced Tooltip',
				),
				'pro-form-reset-button' => array(
					'slug'    => 'pro-form-reset-button',
					'package' => 'pro-disabled',
					'title'   => 'Reset Button For Elementor Pro Form',
				),
				'google_sheet_for_elementor_pro_form' => array(
					'slug'    => 'google-sheet-for-elementor-pro-form',
					'package' => 'pro-disabled',
					'title'   => 'Google Sheet For Elementor Pro Form',
				),
				'masking' => array(
					'slug'    => 'masking',
					'package' => 'pro-disabled',
					'title'   => 'Masking',
				),
				'particles' => array(
					'slug'    => 'particles',
					'package' => 'pro-disabled',
					'title'   => 'Particles',
				),
			)
		);
	}
}
