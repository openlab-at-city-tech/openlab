<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class EPKB_Abstract_Block {

	const EPKB_BLOCK_NAMESPACE = 'echo-knowledge-base';
	const EPKB_BLOCK_CATEGORY = 'echo-knowledge-base';
	const EPKB_BLOCK_CATEGORY_ICON = 'welcome-learn-more';  // not currently used by WordPress

	const EPKB_KB_BLOCK_PAGE_NAMESPACE = 'echo-knowledge-base';
	const EPKB_KB_BLOCK_PAGE_TEMPLATE = 'kb-block-page-template';

	// each block should override the properties below
	protected $block_name = '';
	protected $block_var_name = '';
	protected $keywords = array();	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization
	protected $has_rtl_css = false;

	public function __construct( $init_hooks = true ) {

		// when insert blocks programmatically we need to utilize non-static methods of the block classes, but we do not need hooks for this
		if ( ! $init_hooks ) {
			return;
		}

		add_action( 'init', array( $this, 'register_block_type' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_block_editor_assets' ) ); // Backend

		// for Search blocks (KB core and AS.EA) we need to retrieve the block config in AJAX handlers - this filter is applied to get relevant configuration for each block in such cases
		add_filter( 'kb_' . $this->block_var_name . '_block_config', array( $this, 'filter_block_config_if_exists' ), 10, 2 );  // e.g. kb_advanced_search_block_config
	}

	public function register_block_type() {

		if ( empty( $this->block_name ) ) {
			return;
		}

		$name = $this->block_name;

		if ( WP_Block_Type_Registry::get_instance()->is_registered( 'echo-knowledge-base/' . $name ) ) {
			return;
		}

		if ( ! self::is_block_available() ) {
			return;
		}

		// if block provides RTL styles RTL always specify RTL handle on block registration - it is too earlier to use is_rtl() on 'init' hook; the RTL handle will be ignored if corresponding CSS file is not registered
		$block_public_style_handles = [ $this->get_block_public_styles_handle() ];
		if ( $this->has_rtl_css ) {
			$block_public_style_handles[] = $this->get_block_public_styles_handle() . '-rtl';
		}

		$block_title = $this->block_title;
		if ( EPKB_Utilities::is_advanced_search_enabled() && $this->block_name == 'search' ) {
			$block_title = esc_html__( 'KB Basic Search', 'echo-knowledge-base' );
		}

		register_block_type(
			'echo-knowledge-base/' . $name,
			[
				'api_version' => 3,
				'name' => 'echo-knowledge-base/' . $name,
				'title' => esc_html__( $block_title, 'echo-knowledge-base' ),
				'category' => 'echo-knowledge-base',
				'icon' => $this->icon,
				'description' => '',
				'keywords' => $this->keywords,	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization
				'attributes' => $this->get_attribute_types_and_defaults(),
				'supports' => ['html' => false, 'align '=> true, 'reusable' => false, 'customClassName' => false ],
				'editor_script_handles' => [ $this->get_the_block_script_handle_for_editor_only() ],
				'script_handles' => [ $this->get_block_public_scripts_handle() ],
				'editor_style_handles' => ['echo-knowledge-base-block-editor'],
				'style_handles' => $block_public_style_handles,
				'render_callback' => array( $this, 'render_block' ),
				'style' => $block_public_style_handles,
				'script' => $this->get_block_public_scripts_handle(),
				'example' => array(
					'viewportWidth' => 1200,
					'attributes' => array(),
				),
			]
		);
	}

	/**
	 * Return block content for public display
	 * @param $block_attributes
	 * @param $content
	 * @param $wp_block
	 * @return false|string
	 */
	public function render_block( $block_attributes=[], $content=null, $wp_block=null ) {

		// empty 'kb_id' in stored block attributes means the block has default value
		$block_kb_id = empty( $block_attributes['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $block_attributes['kb_id'];

		// blocks are available only if modular is enabled
		if ( epkb_get_instance()->kb_config_obj->get_value( $block_kb_id, 'modular_main_page_toggle', 'off' ) == 'off' ) {
			return esc_html__( 'Please switch to Modular mode to use this block. Contact us for help.', 'echo-knowledge-base' );
		}

		$is_editor_preview = EPKB_Utilities::get( 'is_editor_preview', null );

		// ensure block has all attributes before proceeding to rendering of HTML and CSS
		$block_attributes = $this->add_internal_kb_settings( $block_attributes );

		// do not display Main Page of Archived KB
		if ( $block_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $block_attributes['status'] ) ) {
			return esc_html__( 'This knowledge base was archived.', 'echo-knowledge-base' );
		}

		// apply defaults to missing attributes in $block_attributes
		foreach( $this->get_block_attributes_defaults() as $block_setting_name => $default ) {
			$block_attributes[ $block_setting_name ] = isset( $block_attributes[ $block_setting_name ] ) ? $block_attributes[ $block_setting_name ] : $default;
		}

		$custom_css_class_escaped = empty( $block_attributes['custom_css_class'] ) ? '' : ' ' . esc_attr( $block_attributes['custom_css_class'] );

		ob_start(); ?>

		<div class="eckb-kb-block eckb-kb-block-<?php echo esc_attr( $this->block_name ) . $custom_css_class_escaped . ( empty( $is_editor_preview ) ? '' : ' ' . 'eckb-block-editor-preview' ); ?>">
			<div class="epkb-block-main-page-container epkb-css-full-reset <?php echo esc_attr( EPKB_Utilities::get_active_theme_classes() ); ?>">
				<div class="epkb-ml__row epkb-ml__row-<?php echo esc_attr( $this->block_name ); ?>">    <?php
					$this->render_block_inner( $block_attributes );	?>
				</div>
			</div>
		</div>  <?php

		// editor styles - in editor preview render inline CSS into dedicated style tag in the block container to update it with the block HTML
		if ( $is_editor_preview ) {	?>
			<style><?php echo $this->get_block_inline_styles( $block_attributes ); ?></style>	<?php
			$block_font_slugs = self::register_block_fonts( $block_attributes );
			EPKB_Blocks_Settings::print_block_fonts( $block_font_slugs );
		}

		return ob_get_clean();
	}

	/**
	 * Register public styles, scripts, fonts, icons for the current block
	 * @return void
	 */
	public function register_block_assets() {
		global $post;

		// allow to register block assets only for 'page' post type
		if ( empty( $post ) || $post->post_type != 'page' ) {
			return;
		}

		// register block assets only if either in the editor (backend) or the post is not empty AND has KB blocks (frontend)
		// (otherwise non-block Themes which support blocks enqueue KB block styles on every page regardless of KB block presence)
		//		- edit action detects the editor view for existing pages
		//		- is_admin() detects the editor for new page creation
		if ( EPKB_Utilities::post( 'action' ) != 'edit' && ! is_admin() && ( empty( $post ) || ! EPKB_Block_Utilities::content_has_the_kb_block( $post->post_content, $this->block_name ) ) ) {
			return;
		}

		// retrieve attributes for the current block
		$block_attributes = $this->get_parsed_block_attributes_or_defaults( $post );

		// add required specific attributes to work correctly with KB core functionality
		$block_attributes = $this->add_internal_kb_settings( $block_attributes );

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// core KB JS - enqueued via register_block_type()
		if ( ! wp_script_is( $this->get_block_public_scripts_handle(), 'registered' ) ) {
			$this->register_block_public_scripts( $suffix );
		}

		// register common public styles and scripts - enqueued as a dependency of main block style
		if ( ! wp_style_is( 'epkb-icon-fonts', 'registered' ) ) {
			wp_register_style( 'epkb-icon-fonts', Echo_Knowledge_Base::$plugin_url . 'css/epkb-icon-fonts' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		}

		// PER BLOCK: public styles and scripts - enqueued via register_block_type()
		if ( ! wp_style_is( $this->get_block_public_styles_handle(), 'registered' ) ) {

			// main styles dependencies
			$block_styles_dependencies = array_merge( array(  'epkb-icon-fonts' ), self::register_block_fonts( $block_attributes ) );

			// register main styles for current block
			$this->register_block_public_styles( $suffix, $block_styles_dependencies );

			// register inline styles for current block
			wp_add_inline_style( $this->get_block_public_styles_handle(), EPKB_Utilities::minify_css( $this->get_block_inline_styles( $block_attributes ) ) );

			// optional RTL styles - used only if the current block provides RTL styles
			if ( $this->has_rtl_css && is_rtl() ) {
				wp_register_style( $this->get_block_public_styles_handle() . '-rtl', Echo_Knowledge_Base::$plugin_url . 'css/' . $this->block_name . '-block' . '-rtl' . $suffix . '.css', array( $this->get_block_public_styles_handle() ), Echo_Knowledge_Base::$version );
			}
		}

		return;
	}

	/**
	 * Register block assets which used for editor only
	 * @return void
	 */
	public function register_block_editor_assets() {
		global $post;

		// allow to register block assets only for 'page' post type
		if ( empty( $post->post_type ) || $post->post_type !== 'page' ) {
			return;
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// PER BLOCK: register block script for editor ( Enqueued via register_block_type() by handle )
		if ( ! wp_script_is( $this->get_the_block_script_handle_for_editor_only(), 'registered' ) ) {

			// use 'include' to have warning instead of generating PHP error and make the plugin not working due to the soft issue - the block can be not rendered, but the rest of the code (or blocks) will be still working
			$block_asset = include_once(  Echo_Knowledge_Base::$plugin_dir . 'includes/admin/blocks/block.asset.php' );
			$shared_dependencies = array( 'echo-knowledge-base-block-editor-shared' );
			$dependencies = empty( $block_asset ) || ! is_array( $block_asset ) ? $shared_dependencies : array_merge( $shared_dependencies, $block_asset['dependencies'] );

			wp_register_script( $this->get_the_block_script_handle_for_editor_only(), Echo_Knowledge_Base::$plugin_url . 'js/' . $this->block_name . '-block' . $suffix . '.js', $dependencies, Echo_Knowledge_Base::$version );

			// used by JS to display input fields for the block edit screen. It is enqueued in the editor only.
			$block_ui_config = $this->get_block_ui_config();
			$block_ui_config['settings']['kb_block_page_template'] = self::EPKB_KB_BLOCK_PAGE_TEMPLATE;
			wp_add_inline_script( $this->get_the_block_script_handle_for_editor_only(), 'const epkb_' . $this->block_var_name . '_block_ui_config = ' . wp_json_encode( $block_ui_config, ENT_QUOTES ) . ';', 'before' );
		}

		// register styles if not already registered ( Enqueued via register_block_type() by handle )
		if ( ! wp_style_is( $this->get_block_editor_styles_handle(), 'registered' ) ) {

			// register block editor script (is common for all KB blocks)
			$epkb_block_editor_vars = array(
				'font_families' => array_combine( EPKB_Typography::get_google_fonts_family_list(), EPKB_Typography::get_google_fonts_family_list() ),
			);
			wp_register_script( 'echo-knowledge-base-block-editor-shared', false );
			wp_localize_script( 'echo-knowledge-base-block-editor-shared', 'epkb_block_editor_vars', $epkb_block_editor_vars );

			// block editor UI
			wp_register_style( $this->get_block_editor_styles_handle(),Echo_Knowledge_Base::$plugin_url . 'css/block-editor' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

			// hide KB block page template in the available templates list by default - when user adds layout block, then dedicated script will remove the inline style and the template will be shown
			wp_add_inline_style( $this->get_block_editor_styles_handle(), EPKB_Utilities::minify_css( '.block-editor-block-patterns-list .block-editor-block-patterns-list__list-item:has(#kb-block-page-template){display:none!important;}' ) );
		}
	}

	/**
	 * Register block fonts - use common KB slugs to avoid duplicated fonts loading
	 * @param $block_attributes
	 * @return array
	 */
	public function register_block_fonts( $block_attributes ) {

		$typography_settings = $this->get_this_block_typography_settings();
		$font_file_slugs = array();

		foreach ( $typography_settings as $one_typography_setting ) {

			// do nothing if font family is default
			if ( empty( $block_attributes[ $one_typography_setting ]['font_family'] ) || in_array( $block_attributes[ $one_typography_setting ]['font_family'], array( 'inherit', 'default' ) ) ) {
				continue;
			}

			// register font only once (it can be already registered with the same KB common slug in another block)
			$block_font_slug = 'epkb-font-' . sanitize_title( $block_attributes[ $one_typography_setting ]['font_family'] );
			if ( wp_style_is( $block_font_slug, 'registered' ) ) {
				continue;
			}

			// do nothing if the font link is empty
			$font_link = EPKB_Typography::get_google_font_link( $block_attributes[ $one_typography_setting ]['font_family'] );
			if ( empty( $font_link ) ) {
				continue;
			}

			$one_font_file_slug = 'epkb-font-' . sanitize_title( $block_attributes[ $one_typography_setting ]['font_family'] );
			wp_register_style( $one_font_file_slug, $font_link );

			$font_file_slugs[] = $one_font_file_slug;
		}

		return $font_file_slugs;
	}

	/**
	 * Modify global block definitions, if required and injects fully-fleshed attributes
	 */
	private function get_attribute_types_and_defaults() {

		$kb_config_defaults = EPKB_KB_Config_Specs::get_default_all_kb_config();
		$block_config_defaults = $this->get_block_config_defaults();
		$block_attributes = epkb_get_block_attributes( $this->block_name );
		foreach ( $block_attributes as $block_setting_name => $block_spec ) {

			// allow block config to set default value instead of KB config
			if ( isset( $block_config_defaults[ $block_setting_name ] ) ) {

				// typography field
				if ( is_array( $block_config_defaults[ $block_setting_name ] ) && isset( $block_config_defaults[ $block_setting_name ]['font_family'] ) ) {
					$block_attributes[ $block_setting_name ]['default'] = array(
						'font_family' => $block_config_defaults[ $block_setting_name ]['font_family'],
						'font_size' => $block_config_defaults[ $block_setting_name ]['font_size'],
						'font_appearance' => $block_config_defaults[ $block_setting_name ]['font_appearance'],
					);
				}

				$block_attributes[ $block_setting_name ]['default'] = $block_config_defaults[ $block_setting_name ];
				continue;
			}

			$block_attributes[ $block_setting_name ]['default'] = isset( $kb_config_defaults[ $block_setting_name ] ) ? $kb_config_defaults[ $block_setting_name ] : '';

			// ensure attributes type to avoid type errors on attributes validation by WordPress blocks core
			if ( $block_spec['type'] === 'string' ) {
				$block_attributes[ $block_setting_name ]['default'] = strval( $block_attributes[ $block_setting_name ]['default'] );
			}
			if ( $block_spec['type'] === 'number' ) {
				$block_attributes[ $block_setting_name ]['default'] = intval( $block_attributes[ $block_setting_name ]['default'] );
			}
		}

		return $block_attributes;
	}

	/**
	 * Add default values from KB configuration to the block settings
	 * @return array
	 */
	public function get_block_attributes_defaults() {

		$kb_config_defaults = EPKB_KB_Config_Specs::get_default_all_kb_config();
		$block_config_defaults = $this->get_block_config_defaults();
		$block_attributes = epkb_get_block_attributes( $this->block_name );
		$block_attributes_defaults = array();
		foreach ( $block_attributes as $block_setting_name => $block_spec ) {

			// allow block config to set default value instead of KB config
			if ( isset( $block_config_defaults[ $block_setting_name ] ) ) {
				$block_attributes_defaults[ $block_setting_name ] = $block_config_defaults[ $block_setting_name ];
				continue;
			}

			$block_attributes_defaults[ $block_setting_name ] = isset( $kb_config_defaults[ $block_setting_name ] ) ? $kb_config_defaults[ $block_setting_name ] : '';
		}

		return $block_attributes_defaults;
	}

	/**
	 * Return only those block config settings which have default value
	 * @return array
	 */
	private function get_block_config_defaults() {
		$block_ui_config = $this->get_this_block_ui_config();
		$block_config_defaults = array();

		foreach ( $block_ui_config as $tab_name => $tab_config ) {
			foreach ( $tab_config['groups'] as $group_name => $group_config ) {
				foreach ( $group_config['fields'] as $field_name => $field_specs ) {

					// combined fields
					if ( isset( $field_specs['combined_settings'] ) ) {
						foreach ( $field_specs['combined_settings'] as $block_setting_name => $setting_specs ) {

							// allow the default value to be empty, but continue only if it is set
							if ( ! isset( $setting_specs['default'] ) ) {
								continue;
							}

							$block_config_defaults[ $block_setting_name ] = $setting_specs['default'];
						}
						continue;
					}

					// typography fields
					if ( $field_specs['setting_type'] == 'typography_controls' ) {
						$block_config_defaults[ $field_name ] = array(
							'font_family' => $field_specs['controls']['font_family']['default'],
							'font_size' => $field_specs['controls']['font_size']['default'],
							'font_appearance' => $field_specs['controls']['font_appearance']['default'],
						);
						continue;
					}

					// allow the default value to be empty, but continue only if it is set
					if ( ! isset( $field_specs['default'] ) ) {
						continue;
					}

					$block_config_defaults[ $field_name ] = $field_specs['default'];
				}
			}
		}

		return $block_config_defaults;
	}

	/**
	 * Return block configuration if the block is present in the post with given ID
	 * @param $kb_config
	 * @param $post_id
	 * @return array|string[]
	 */
	public function filter_block_config_if_exists( $kb_config, $post_id ) {

		if ( empty( $post_id ) ) {
			return $kb_config;
		}

		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			return $kb_config;
		}

		$block_attributes = $this->get_parsed_block_attributes_or_defaults( $post );

		return empty( $block_attributes ) ? $kb_config : array_merge( $kb_config, $this->add_internal_kb_settings( $block_attributes ) );
	}

	/**
	 * Update special settings
	 * @param $post_id
	 * @param $post
	 * @param $update
	 * @return void
	 */
	public function update_kb_setting_on_save_post( $post_id, $post, $update ) {

		// Verify nonce, permissions, and autosave to ensure security
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// if block is not present in the post, then do nothing
		$block_attributes = EPKB_Block_Utilities::parse_block_attributes_from_post( $post, $this->block_name );
		if ( ! is_array( $block_attributes ) ) {
			return;
		}

		$kb_id = isset( $block_attributes['kb_id'] ) ? $block_attributes['kb_id'] : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		// update search highlight for Advanced Search
		if ( $this->block_name == 'advanced-search' && EPKB_Utilities::is_advanced_search_enabled() ) {
			$text_highlight_enabled = isset( $block_attributes['advanced_search_text_highlight_enabled'] ) ? $block_attributes['advanced_search_text_highlight_enabled'] : 'on';
			do_action( 'eckb_kb_config_save_value', $kb_id, 'advanced_search_text_highlight_enabled', $text_highlight_enabled );
			return;
		}

		// for layout block need to update 'templates_for_kb' in the current KB configuration:
		// - the KB Template toggle for the block Main Page in Settings UI tab of the admin page is hidden (since they are controlled by the layout block settings in Gutenberg Editor).
		// - When user chooses KB Template or KB Custom Block Page template then we need to update the value in the KB config (e.g. not only the block attribute)
		// - So this is to make all the rest functionality, which depends on the 'templates_for_kb' setting, to work correctly without a need to retrieve the layout block attributes form post content.
		// Reference:
		//    'kb_block_template_toggle' - internal temporary indicator of user intention to use KB Custom Block Template
		//    'template_toggle' - used by JS when user toggles 'on' the kb_block_template_toggle' settings
		if ( EPKB_Block_Utilities::is_block_theme() ) {
			$templates_for_kb = isset( $block_attributes['kb_block_template_toggle'] ) && $block_attributes['kb_block_template_toggle'] == 'on' ? 'kb_templates' : 'current_theme_templates';
		} else {
			$templates_for_kb = isset( $block_attributes['templates_for_kb'] ) ? $block_attributes['templates_for_kb'] : 'kb_templates';
		}

		$updated_kb_config = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'templates_for_kb', $templates_for_kb );

		// update icons if user chose another theme design
		if ( isset( $block_attributes['theme_name'] ) && $block_attributes['theme_name'] != 'current' && is_array( $updated_kb_config ) ) {
			$block_attributes = array_merge( $updated_kb_config, $block_attributes );
			// if user selects Image theme then change font icons to image icons
			EPKB_Core_Utilities::get_or_update_new_category_icons( $block_attributes, $block_attributes['theme_name'], true );
		}
	}

	/**
	 * Add to block attributes the internal KB settings which are required for compatibility with non-block KB functionality but are not using in KB blocks directly
	 * @param $block_attributes
	 * @return mixed
	 */
	private function add_internal_kb_settings( $block_attributes ) {

		// ensure the 'kb_id' is set - empty 'kb_id' in stored block attributes means the block has default value
		$block_attributes['kb_id'] = empty( $block_attributes['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int)$block_attributes['kb_id'];

		// retrieve selected KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $block_attributes['kb_id'] );

		// distinguish blocks for cache (article icon cache)
		$block_attributes['block_name'] = $this->block_name;

		$block_attributes['id'] = $kb_config['id'];
		$block_attributes['status'] = $kb_config['status'];
		$block_attributes['kb_main_pages'] = $kb_config['kb_main_pages'];
		$block_attributes['first_plugin_version'] = $kb_config['first_plugin_version'];
		$block_attributes['upgrade_plugin_version'] = $kb_config['upgrade_plugin_version'];
		$block_attributes['modular_main_page_toggle'] = $kb_config['modular_main_page_toggle'];
		$block_attributes['show_articles_before_categories'] = $this->block_name == 'sidebar-layout' ? $kb_config['sidebar_show_articles_before_categories'] : $kb_config['show_articles_before_categories'];
		$block_attributes['wpml_is_enabled'] = $kb_config['wpml_is_enabled'];

		// let blocks to hard-code value of certain KB settings regardless of actual KB config value
		$block_attributes = $this->add_this_block_required_kb_attributes( $block_attributes );

		return $block_attributes;
	}

	/**
	 * Retrieve current block settings if the block is present in the current post and add missing settings with default values, otherwise return default block settings
	 * @param $post
	 * @return array|string[]
	 */
	private function get_parsed_block_attributes_or_defaults( $post ) {

		$block_attributes = EPKB_Block_Utilities::parse_block_attributes_from_post( $post, $this->block_name );

		// return empty array if the block was not found in the given post
		if ( $block_attributes === false ) {
			$block_attributes = [];
		}

		// if the current block has default config, then its attributes can be empty
		if ( empty( $block_attributes ) || ! is_array( $block_attributes ) ) {
			$block_attributes = [];
		}

		// apply defaults to missing attributes in $block_attributes
		foreach( $this->get_block_attributes_defaults() as $block_setting_name => $default ) {
			$block_attributes[ $block_setting_name ] = isset( $block_attributes[ $block_setting_name ] ) ? $block_attributes[ $block_setting_name ] : $default;
		}

		// blocks use 'kb_id' key while rest of KB code is using 'id' key - ensure the 'id' is updated before passing it to non-block functions
		$block_attributes['id'] = empty( $block_attributes['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int)$block_attributes['kb_id'];

		return $block_attributes;
	}

	private function get_block_ui_config() {

		$block_ui_config = $this->get_this_block_ui_config();
		$kb_config_specs = EPKB_Core_Utilities::retrieve_all_kb_specs( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		foreach ( $block_ui_config as $tab_name => $tab_config ) {
			foreach ( $tab_config['groups'] as $group_name => $group_config ) {
				foreach ( $group_config['fields'] as $field_name => $field_specs ) {

					// combined field
					if ( isset( $field_specs['combined_settings'] ) ) {
						foreach ( $field_specs['combined_settings'] as $block_setting_name => $setting_specs ) {
							$block_ui_config[$tab_name]['groups'][ $group_name ]['fields'][ $field_name ]['combined_settings'][ $block_setting_name ] = array_merge( $kb_config_specs[ $block_setting_name ], $setting_specs );
						}
						continue;
					}

					// typography fields
					if ( isset( $field_specs['typography_controls'] ) ) {
						continue;
					}

					if ( empty( $kb_config_specs[ $field_name ] ) ) {
						continue;
					}

					$block_ui_config[$tab_name]['groups'][ $group_name ]['fields'][ $field_name ] = array_merge( $kb_config_specs[ $field_name ], $field_specs );
				}
			}
		}

		return $block_ui_config;
	}

	/**
	 * Return array where each element key is field name and value is specification
	 * @return array
	 */
	protected function get_block_ui_specs() {
		$block_ui_config = $this->get_this_block_ui_config();
		$block_ui_specs = array();
		foreach ( $block_ui_config as $tab_key => $tab_config ) {
			foreach ( $tab_config['groups'] as $group_key => $group_config ) {
				foreach ( $group_config['fields'] as $field_key => $field_specs ) {

					// combined settings
					if ( isset( $field_specs['combined_settings'] ) ) {
						foreach ( $field_specs['combined_settings'] as $combined_field_name => $combined_field_specs ) {
							$block_ui_specs[ $combined_field_name ] = $combined_field_specs;
							$block_ui_specs[ $combined_field_name ]['setting_type'] = 'range';
						}
						continue;
					}

					// single setting
					$block_ui_specs[ $field_key ] = $field_specs;
				}
			}
		}
		return $block_ui_specs;
	}

	/**
	 * Block inline styles (common + block-dedicated)
	 * @param $block_attributes
	 * @return string
	 */
	private function get_block_inline_styles( $block_attributes ) {

		// common block inline styles
		$block_max_width_sanitized = $block_attributes['block_full_width_toggle'] == 'on' ? '100%' : intval( $block_attributes['block_max_width'] ) . 'px';
		$output = '.eckb-kb-block .epkb-block-main-page-container .epkb-ml__row-' . $this->block_name . ' {
			max-width: ' . $block_max_width_sanitized . ' ' . '!important;
		}';

		// dedicated block inline styles
		$output .= $this->get_this_block_inline_styles( $block_attributes );

		return $output;
	}

	private static function get_block_editor_styles_handle() {
		return self::EPKB_BLOCK_NAMESPACE . '-block-editor';
	}

	private function get_the_block_script_handle_for_editor_only() {
		return self::EPKB_BLOCK_NAMESPACE . '-' . $this->block_name . '-block';
	}

	/**
	 * Return handle for block public styles - add-on's block can override this method
	 * @return string
	 */
	protected function get_block_public_styles_handle() {
		return 'epkb-' . $this->block_name . '-block';
	}

	/**
	 * Return handle for block public scripts - add-on's block can override this method
	 * @return string
	 */
	protected function get_block_public_scripts_handle() {
		return 'epkb-blocks-public-scripts';
	}

	/**
	 * Add-on dedicated classes can override the method to control the block availability - add-on's block class needs to be checked inside hook handler when all available classes are registered
	 * @return bool
	 */
	protected static function is_block_available() {
		return true;
	}

	/**
	 * Provides a possibility for add-on's block to register its own styles by overriding the method
	 * @param $suffix
	 * @param $block_styles_dependencies
	 * @return void
	 */
	protected function register_block_public_styles( $suffix, $block_styles_dependencies ) {
		wp_register_style( $this->get_block_public_styles_handle(), Echo_Knowledge_Base::$plugin_url . 'css/' . $this->block_name . '-block' . $suffix . '.css', $block_styles_dependencies, Echo_Knowledge_Base::$version );
	}

	/**
	 * Provides a possibility for add-on's block to register its own public scripts by overriding the method
	 * @param $suffix
	 * @return void
	 */
	protected function register_block_public_scripts( $suffix ) {
		wp_register_script( $this->get_block_public_scripts_handle(), Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
		$epkb_vars = array(
			'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
			'msg_try_again' => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
			'error_occurred' => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (1936)',
			'unknown_error' => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1247)',
			'reload_try_again' => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
			'save_config' => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
			'input_required' => esc_html__( 'Input is required', 'echo-knowledge-base' ),
			'nonce' => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
			'creating_demo_data' => esc_html__( 'Creating a Knowledge Base with demo categories and articles. It will be completed shortly.', 'echo-knowledge-base' )
		);
		wp_localize_script( $this->get_block_public_scripts_handle(), 'epkb_vars', $epkb_vars );
	}

	/**
	 * Sanitize attributes before pass to KB legacy code (unlike to KB config, the block attributes can be modified in the post content and thus become unsafe)
	 * (used for add-ons legacy code which renders inline styles directly in HTML until we update the add-ons code to sanitize values where they are applied)
	 * @param $block_attributes
	 * @return array
	 */
	protected function sanitize_block_attributes( $block_attributes ) {

		$block_specs = $this->get_block_ui_specs();
		foreach ( $block_attributes as $attribute_name => $attribute_value ) {

			if ( empty( $block_specs[ $attribute_name ]['setting_type'] ) ) {
				continue;
			}

			switch ( $block_specs[ $attribute_name ]['setting_type'] ) {
				case 'range':
					$block_attributes[ $attribute_name ] = intval( $attribute_value );
					break;
				case 'range_float':
					$block_attributes[ $attribute_name ] = number_format( floatval( $attribute_value ), 2 );
					break;
				case 'color':
					$block_attributes[ $attribute_name ] = EPKB_Utilities::sanitize_hex_color( $attribute_value );
					break;
				case 'select_buttons_string':
				case 'toggle':
				case 'dropdown':
					$block_attributes[ $attribute_name ] = sanitize_text_field( $attribute_value );
					break;
				default:
				case 'text':
					// text and html is sanitized where they are applied accordingly to the place where they are used (e.g. via esc_attr(), esc_html(), or via wp_kses() with appropriate allowed tags)
					break;
			}
		}

		return $block_attributes;
	}

	abstract protected function get_this_block_inline_styles( $block_attributes );

	abstract public function render_block_inner( $block_attributes );

	abstract protected function get_this_block_ui_config();

	abstract protected function get_this_block_typography_settings();

	abstract protected function add_this_block_required_kb_attributes( $block_attributes );
}