<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Contact Us tab on the Need Help? screen
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Need_Help_Contact_Us {

	/**
	 * Get configuration array for Contact Us page view
	 *
	 * @return array
	 */
	public static function get_page_view_config() {

		return array(

			// Shared
			'active' => true,
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_need_help_read', 'admin_eckb_access_frontend_editor_write'] ),
			'list_key' => 'contact-us',

			// Top Panel Item
			'label_text' => esc_html__( 'Contact Us', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-envelope-o',

			// Boxes List
			'boxes_list' => array(

				// Box: Contact Us
				array(
					'html' => self::contact_us_tab(),
				),
			),
		);
	}

	/**
	 * Get content for Contact Us tab
	 *
	 * @return false|string
	 */
	private static function contact_us_tab() {

		ob_start();     ?>

		<div class="epkb-kbnh__contact-us-container">

			<div class="epkb-kbnh__section-container epkb-kbnh__section--three-col-cta">

				<div class="epkb-kbnh__section__header-container">
					<h3 class="epkb-kbnh__header__title"><?php esc_html_e( 'We Are Here to Help', 'echo-knowledge-base' ); ?></h3>
				</div>

				<div class="epkb-kbnh__section__body-container"> <?php

					EPKB_HTML_Forms::call_to_action_box( array(
						'container_class'   => '',
						'style' => 'style-2',
						'icon_class'    => 'epkbfa-rocket',
						'title'         => esc_html__( 'Cannot Find a Feature?', 'echo-knowledge-base' ),
						'content'       => '<p>' . esc_html__( 'We can help you find it or add it to our road map if it is missing.', 'echo-knowledge-base' ) . '</p>',
						'btn_text'      => esc_html__( 'Ask About a Feature', 'echo-knowledge-base' ),
						'btn_url'       => 'https://www.echoknowledgebase.com/feature-request/',
						'btn_target'    => '__blank',
					) );

					EPKB_HTML_Forms::call_to_action_box( array(
						'container_class'   => '',
						'style' => 'style-2',
						'icon_class'    => 'epkbfa-life-ring',
						'title'         => esc_html__( 'Submit an Issue', 'echo-knowledge-base' ),
						'content'       => '<p>' . esc_html__( 'Submit a technical support question for something that is not working correctly.', 'echo-knowledge-base' ) . '</p>
										<p>' . esc_html__( 'We usually reply within an hour.', 'echo-knowledge-base' ) . '</p>',
						'btn_text'      => esc_html__( 'Contact Our Support', 'echo-knowledge-base' ),
						'btn_url'       => 'https://www.echoknowledgebase.com/technical-support/',
						'btn_target'    => '__blank',
					) );

					EPKB_HTML_Forms::call_to_action_box( array(
						'container_class'   => '',
						'style' => 'style-2',
						'icon_class'    => 'epkbfa-comments-o',
						'title'         => esc_html__( 'General and Pre-Sale Questions', 'echo-knowledge-base' ),
						'content'       => '<p>' . esc_html__( 'Do you have a pre-sale question, and do you need some clarification?', 'echo-knowledge-base' ) . '</p>',
						'btn_text'      => esc_html__( 'Ask a Question', 'echo-knowledge-base' ),
						'btn_url'       => 'https://www.echoknowledgebase.com/pre-sale-question/',
						'btn_target'    => '__blank',
					) );		?>
				</div>
			</div>
		</div>		<?php

		return ob_get_clean();
	}
}
