<?php
/**
 * Admin UI Access
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Admin_UI_Access {

	// Contributor capability
	const EPKB_WP_CONTRIBUTOR_CAPABILITY = 'edit_posts';

	// Author capability
	const EPKB_WP_AUTHOR_CAPABILITY = 'publish_posts';

	// Editor capability
	const EPKB_WP_EDITOR_CAPABILITY = 'manage_categories';

	// Admin capability
	const EPKB_ADMIN_CAPABILITY = 'manage_options';

	// Allowed Contexts list
	const ADMIN_UI_CONTEXTS = array(
		'admin_eckb_access_frontend_editor_write',
		'admin_eckb_access_search_analytics_read',
		'admin_eckb_access_order_articles_write',
		'admin_eckb_access_need_help_read',
		'admin_eckb_access_addons_news_read',
		'admin_eckb_access_faqs_write',
	);

	/**
	 * Check if the current user has access to the current context
	 *
	 * @param $context
	 *
	 * @return bool
	 */
	public static function is_user_access_to_context_allowed( $context ) {

		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return false;
		}

		// always return true for users with WP admin
		if ( current_user_can( self::EPKB_ADMIN_CAPABILITY ) ) {
			return true;
		}

		// here we only handle KB contexts
		if ( empty( $context ) || ! in_array( $context, self::ADMIN_UI_CONTEXTS ) ) {
			return false;
		}

		// retrieve access configuration
		$config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		if ( empty( $config[$context] ) ) {
			return false;
		}

		// FAQs are not specific to KB so use default KB ID
		if ( $context == 'admin_eckb_access_faqs_write' ) {
			$config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
			if ( empty( $config[$context] ) ) {
				return false;
			}
		}

		if ( ! self::is_capability_in_allowed_list( $config[$context], $config['id'] ) ) {
			return false;
		}

		// access has to be one of the allowed levels
		$specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		if ( empty( $specs[$context]['allowed_access'] ) || ! self::is_context_allowed( EPKB_KB_Config_DB::DEFAULT_KB_ID, $config[$context], $specs, $context ) ) {
			return false;
		}

		// check if the current user has correct capability
		return current_user_can( $config[$context] );
	}

	/**
	 * Get capability for a certain context based on settings;
	 * If a few contexts are passed, then return Editor capability if any of the contexts is allowed for Editor (used to set capability for a tab that contains multiple contexts)
	 *
	 * @param $contexts
	 * @param null $kb_config
	 *
	 * @return string
	 */
	public static function get_context_required_capability( $contexts, $kb_config=null ) {

		if ( ! is_array( $contexts ) ) {
			$contexts = [$contexts];
		}

		$config = empty( $kb_config ) ? epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Handler::get_relevant_kb_id() ) : $kb_config;
		$specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_id = $config['id'];

		// FAQs are not specific to KB so use default KB ID
		if ( isset( $contexts[0] ) && $contexts[0] == 'admin_eckb_access_faqs_write' ) {
			$config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// CAPABILITY LEVEL 1: check in settings if any of the contexts has capability 'Author'
		foreach ( $contexts as $context ) {

			// skip context that is used internally and which is not listed in the specs
			if ( ! isset( $config[$context] ) ) {
				continue;
			}

			// access has to be one of the allowed levels
			if ( $config[$context] == self::get_author_capability( $kb_id ) && self::is_context_allowed( $kb_id, $config[$context], $specs, $context ) ) {
				return self::get_author_capability( $kb_id );
			}
		}

		// CAPABILITY LEVEL 2: check in settings if any of the contexts has capability 'Editor'
		foreach ( $contexts as $context ) {

			// skip context that is used internally and which is not listed in the specs
			if ( ! isset( $config[$context] ) ) {
				continue;
			}

			// access has to be one of the allowed levels
			if ( $config[$context] == self::get_editor_capability( $kb_id ) && self::is_context_allowed( $kb_id, $config[$context], $specs, $context ) ) {
				return self::get_editor_capability( $kb_id );
			}
		}

		// HIGHEST CAPABILITY LEVEL: 'Admin'
		return self::get_admin_capability();
	}

	/**
	 * Check if the current user is allowed to access the given context
	 *
	 * @param $kb_id
	 * @param $capability
	 * @param $specs
	 * @param $context
	 *
	 * @return bool
	 */
	private static function is_context_allowed( $kb_id, $capability, $specs, $context ) {

		$allowed_access_list = [];
		foreach ( $specs[$context]['allowed_access'] as $ix => $allowed_access ) {

			if ( $allowed_access == self::EPKB_WP_AUTHOR_CAPABILITY ) {
				$allowed_access_list[] = self::get_author_capability( $kb_id );
			} else if ( $allowed_access == self::EPKB_WP_EDITOR_CAPABILITY ) {
				$allowed_access_list[] = self::get_editor_capability( $kb_id );
			}
		}

		return in_array( $capability, $allowed_access_list );
	}

	/**
	 * Return true if given capability is in allowed capabilities list
	 *
	 * @param $capability
	 * @param $kb_id
	 *
	 * @return bool
	 */
	private static function is_capability_in_allowed_list( $capability, $kb_id ) {
		return in_array( $capability, [ self::get_contributor_capability( $kb_id ), self::get_author_capability( $kb_id ), self::get_editor_capability( $kb_id ), self::get_admin_capability() ] );
	}

	/**
	 * Return actual capability for Contributor users
	 *
	 * @param int $kb_id
	 * @return string
	 * @noinspection PhpUnusedParameterInspection*/
	public static function get_contributor_capability( $kb_id=0 ) {
		return self::EPKB_WP_CONTRIBUTOR_CAPABILITY;
	}

	/**
	 * Return actual capability for Author users
	 *
	 * @param int $kb_id
	 * @return string
	 * @noinspection PhpUnusedParameterInspection*/
	public static function get_author_capability( $kb_id=0 ) {
		return self::EPKB_WP_AUTHOR_CAPABILITY;
	}

	/**
	 * Return actual capability for Editor users
	 *
	 * @param int $kb_id
	 * @return string
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function get_editor_capability( $kb_id=0 ) {
		return self::EPKB_WP_EDITOR_CAPABILITY;
	}

	/**
	 * Return actual capability for Admin users
	 *
	 * @return string
	 */
	public static function get_admin_capability() {
		return self::EPKB_ADMIN_CAPABILITY;
	}

	/**
	 * Get configuration array for Access Control settings boxes
	 *
	 * @param $kb_config
	 * @return array
	 */
	public static function get_access_boxes( $kb_config ) {

		$boxes_config = [];
		$kb_config_specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_id = $kb_config['id'];

		// Box: Edit KB colors, fonts, labels and features.
		$boxes_config[] =
			array(
				'title' => $kb_config_specs['admin_eckb_access_frontend_editor_write']['label'],
				'html' => self::radio_buttons_vertical_access_control( array(
					'name'          => 'admin_eckb_access_frontend_editor_write',
					'radio_class'   => 'epkb-admin__radio-button-wrap',
					'return_html'   => true,
					'value'       => self::is_capability_in_allowed_list( $kb_config['admin_eckb_access_frontend_editor_write'], $kb_id )
						? $kb_config['admin_eckb_access_frontend_editor_write']
						: EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
					'options'       => self::get_access_control_options() ) ) );

		// Box: Order Articles and Categories
		$boxes_config[] =
			array(
				'title' => $kb_config_specs['admin_eckb_access_order_articles_write']['label'],
				'html' => self::radio_buttons_vertical_access_control( array(
					'name'          => 'admin_eckb_access_order_articles_write',
					'radio_class'   => 'epkb-admin__radio-button-wrap',
					'return_html'   => true,
					'value'       => self::is_capability_in_allowed_list( $kb_config['admin_eckb_access_order_articles_write'], $kb_id )
						? $kb_config['admin_eckb_access_order_articles_write']
						: EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
					'options'       => self::get_access_control_options() ) ) );

		// Box: KB Analytics
		$boxes_config[] =
			array(
				'title' => $kb_config_specs['admin_eckb_access_search_analytics_read']['label'],
				'html' => self::radio_buttons_vertical_access_control( array(
					'name'          => 'admin_eckb_access_search_analytics_read',
					'radio_class'   => 'epkb-admin__radio-button-wrap',
					'return_html'   => true,
					'value'       => self::is_capability_in_allowed_list( $kb_config['admin_eckb_access_search_analytics_read'], $kb_id )
						? $kb_config['admin_eckb_access_search_analytics_read']
						: self::get_admin_capability(),
					'options'       => self::get_access_control_options( true ) ) ) );

		// Box: Need Help?
		$boxes_config[] =
			array(
				'title' => $kb_config_specs['admin_eckb_access_need_help_read']['label'],
				'html' => self::radio_buttons_vertical_access_control( array(
					'name'          => 'admin_eckb_access_need_help_read',
					'radio_class'   => 'epkb-admin__radio-button-wrap',
					'return_html'   => true,
					'value'       => self::is_capability_in_allowed_list( $kb_config['admin_eckb_access_need_help_read'], $kb_id )
						? $kb_config['admin_eckb_access_need_help_read']
						: self::get_admin_capability(),
					'options'       => self::get_access_control_options( true ) ) ) );

		// Box: FAQs
		$boxes_config[] = $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ?
			array(
				'title' => $kb_config_specs['admin_eckb_access_faqs_write']['label'],
				'description'   => esc_html__( 'The FAQs feature is not linked to any specific KB; instead, access to it is defined within the default KB.', 'echo-knowledge-base' ),
				'html' => self::radio_buttons_vertical_access_control( array(
					'name'          => 'admin_eckb_access_faqs_write',
					'radio_class'   => 'epkb-admin__radio-button-wrap',
					'return_html'   => true,
					'value'         => self::is_capability_in_allowed_list( $kb_config['admin_eckb_access_faqs_write'], $kb_id )
						? $kb_config['admin_eckb_access_faqs_write']
						: self::get_admin_capability(),
					'options'       => self::get_access_control_options( true ) ) ) ) : '';

		// Box: Add-ons / News
		$boxes_config[] =
			array(
				'title' => $kb_config_specs['admin_eckb_access_addons_news_read']['label'],
				'html' => self::radio_buttons_vertical_access_control( array(
					'name'          => 'admin_eckb_access_addons_news_read',
					'radio_class'   => 'epkb-admin__radio-button-wrap',
					'return_html'   => true,
					'value'       => self::is_capability_in_allowed_list( $kb_config['admin_eckb_access_addons_news_read'], $kb_id )
						? $kb_config['admin_eckb_access_addons_news_read']
						: self::get_admin_capability(),
					'options'       => self::get_access_control_options( true ) ) ) );

		return $boxes_config;
	}

	/**
	 * Get options list for Access Control settings
	 *
	 * @param false $include_author
	 *
	 * @return array
	 */
	private static function get_access_control_options( $include_author=false ) {

		$access_control_ptions = [];

		if ( $include_author ) {
			$access_control_ptions[self::EPKB_WP_AUTHOR_CAPABILITY] = self::get_admins_distinct_box() . self::get_editors_distinct_box() . self::get_authors_distinct_box() . self::get_users_with_capability_distinct_box( self::EPKB_WP_AUTHOR_CAPABILITY );
		}

		$access_control_ptions[self::EPKB_WP_EDITOR_CAPABILITY] = self::get_admins_distinct_box() . self::get_editors_distinct_box() . self::get_users_with_capability_distinct_box( self::EPKB_WP_EDITOR_CAPABILITY );
		$access_control_ptions[self::EPKB_ADMIN_CAPABILITY]     = self::get_admins_distinct_box();

		return $access_control_ptions;
	}

	/**
	 * Handle saving of all options for Access Control feature
	 */
	public static function save_access_control() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// retrieve kb id
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 413 ) );
		}

		// retrieve contexts and save
		$specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		foreach ( self::ADMIN_UI_CONTEXTS as $context ) {

			// skip context that is used internally and which is not listed in the specs
			if ( ! isset( $specs[$context] ) ) {
				continue;
			}

			// retrieve option value
			$context_value = EPKB_Utilities::post( $context, self::get_admin_capability() );

			// make sure we save value that is within certain capabilities list or set admin capability by default
			if ( ! self::is_capability_in_allowed_list( $context_value, $kb_id ) ) {
				$context_value = self::get_admin_capability();
			}

			// access has to be higher than default
			if ( empty( $specs[$context]['allowed_access'] ) || ( ! self::is_context_allowed( $kb_id, $context_value, $specs, $context ) && $context_value != self::get_admin_capability() ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 417 ) );
			}

			// update option or die with error
			$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, $context, $context_value );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 416 ) );
			}
		}

		// we are done here
		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	private static function get_admins_distinct_box() {
		return sprintf( esc_html__( '%sAdmins%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--high">', '</span>' );
	}

	private static function get_editors_distinct_box() {
		return sprintf( esc_html__( '%sEditors%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--middle">', '</span>' );
	}

	private static function get_authors_distinct_box() {
		return sprintf( esc_html__( '%sAuthors%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--low">', '</span>' );
	}

	private static function get_users_with_capability_distinct_box( $capability ) {
		return sprintf( esc_html__( '%susers with "%s" capability%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--lowest">', $capability, '</span>' );
	}

	/**
	 * Detect if user have role Administrator or Editor or Author
	 *
	 * @return bool
	 */

	public static function is_user_admin_editor_author() {

		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return false;
		}

		if ( current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {
			return true;
		}

		if ( current_user_can( 'editor' ) ) {
			return true;
		}

		if ( current_user_can( 'author' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Detect if user have role Administrator or Editor
	 *
	 * @return bool
	 */

	public static function is_user_admin_editor() {

		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return false;
		}

		if ( current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {
			return true;
		}

		if ( current_user_can( 'editor' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Renders several HTML radio buttons in a column
	 *
	 * @param array $args
	 *
	 * @return false|string
	 */
	private static function radio_buttons_vertical_access_control( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
			'data'              => array()
		);
		$args = EPKB_HTML_Elements::add_defaults( $args, $defaults );
		$id =  esc_attr( $args['name'] );
		$ix = 0;

		$data_escaped = '';
		foreach ( $args['data'] as $key => $value ) {
			$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-radio-btn-vertical-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $id ); ?>_group">		<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-radio-btn-vertical-example__icon epkbfa epkbfa-eye"></div>';
			}

			if ( ! empty($args['label']) ) {     ?>
				<span class="main_label <?php echo esc_attr( $args['main_label_class'] ); ?>">
					<?php echo esc_html( $args['label'] ); ?>
				</span>            <?php
			}                       ?>

			<div class="radio-buttons-vertical <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $id ); ?>">
				<ul>	                <?php

					foreach( $args['options'] as $key => $label ) {         ?>

						<li class="<?php echo esc_attr( $args['radio_class'] ); ?>">			                <?php

							$checked_class ='';
							if ( $args['value'] == $key ) {
								$checked_class = 'checked-radio';
							} ?>

							<div class="input_container config-col-1 <?php echo esc_attr( $checked_class ); ?>">
								<input type="radio"
								       name="<?php echo esc_attr( $args['name'] ); ?>"
								       id="<?php echo esc_attr( $id . $ix ); ?>"
								       value="<?php echo esc_attr( $key ); ?>"					                <?php
								echo $data_escaped . ' ' . checked( $key, $args['value'], false ); ?> />
							</div>
							<label class="<?php echo esc_attr( $args['label_class'] ); ?> config-col-10" for="<?php echo esc_attr( $id . $ix ); ?>">
								<?php echo wp_kses_post( $label ); ?>
							</label>
						</li>		                <?php

						$ix++;
					} //foreach	                ?>

				</ul>

			</div>

		</div>        <?php

		return ob_get_clean();
	}
}
