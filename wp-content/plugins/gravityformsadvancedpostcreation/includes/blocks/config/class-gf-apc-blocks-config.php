<?php

namespace Gravity_Forms\Gravity_Forms_APC\Blocks\Config;

use Gravity_Forms\Gravity_Forms\Config\GF_Config;
use Gravity_Forms\Gravity_Forms\Config\GF_Config_Data_Parser;
use GFAPI;

/**
 * Config items for Blocks.
 *
 * @since 1.6.0
 */
class GF_APC_Blocks_Config extends GF_Config {

	protected $name               = 'gform_apc_admin_config';
	protected $script_to_localize = 'gform_apc_vendor_admin';
	protected $attributes         = array();

	public function __construct( GF_Config_Data_Parser $parser, array $attributes ) {
		parent::__construct( $parser );
		$this->attributes = $attributes;
	}

	/**
	 * Get list of forms that have an advanced post creation feed.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function get_forms() {
		// Get form objects.
		$form_objects = GFAPI::get_forms( true, false, 'title', 'ASC' );

		$block_forms = array(
			array(
				'label' => esc_html__( 'All forms', 'gravityformsadvancedpostcreation' ),
				'value' => '',
			),
		);

		foreach ( $form_objects as $form ) {
			$feeds = GFAPI::get_feeds( null, $form['id'], gf_advancedpostcreation()->get_slug() );
			if ( ! empty( $feeds ) && ! is_wp_error( $feeds ) ) {
				$block_forms[] = array(
					'label' => $form['title'],
					'value' => $form['id'],
				);
			}
		}

		return $block_forms;
	}

	/**
	 * Config data.
	 *
	 * @return array[]
	 */
	public function data() {
		$attributes    = $this->attributes;
		$forms         = $this->get_forms();
		$defaultFormId = '';
		foreach ( $forms as $form ) {
			if ( ! empty( $form['value'] ) ) {
				$defaultFormId = $form['value'];
				break;
			}
		}

		return array(
			'is_block_editor' => \GFCommon::is_block_editor_page(),
			'block_editor'    => array(
				'apcPosts' => array(
					'data' => array(
						'attributes'    => $attributes,
						'forms'         => $forms,
						'defaultFormId' => $defaultFormId,
					),
					'i18n' => array(
						'title'            => esc_html__( 'APC Post List', 'gravityformsadvancedpostcreation' ),
						'dataGridHeader'   => esc_html__( 'Header Text', 'gravityformsadvancedpostcreation' ),
						'form'             => esc_html__( 'Form', 'gravityformsadvancedpostcreation' ),
						'description'      => esc_html__( 'Display a list of user created posts.', 'gravityformsadvancedpostcreation' ),
						'allForms'         => esc_html__( 'All Forms', 'gravityformsadvancedpostcreation' ),
						'postsPerPage'     => esc_html__( 'Posts per page', 'gravityformsadvancedpostcreation' ),
						'style'            => esc_html__( 'Style', 'gravityformsadvancedpostcreation' ),
						'light'            => esc_html__( 'Light', 'gravityformsadvancedpostcreation' ),
						'dark'             => esc_html__( 'Dark', 'gravityformsadvancedpostcreation' ),
						'buttonColor'      => esc_html__( 'Active Page Button Color', 'gravityformsadvancedpostcreation' ),
						'transparentBg'    => esc_html__( 'Transparent Background', 'gravityformsadvancedpostcreation' ),
						'hideStatus'       => esc_html__( 'Hide Post Status', 'gravityformsadvancedpostcreation' ),
						'hideDate'         => esc_html__( 'Hide Post Date', 'gravityformsadvancedpostcreation' ),
						'gridTitle'        => esc_html__( 'Data Grid Title', 'gravityformsadvancedpostcreation' ),
						'loginMessage'     => esc_html__( 'Login Message', 'gravityformsadvancedpostcreation' ),
						'loginMessageHelp' => esc_html__( 'Message to display to users who are not logged in.', 'gravityformsadvancedpostcreation' ),
					),
				),
			),
		);
	}
}
