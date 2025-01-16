<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class EPKB_Abstract_Block {

	const EPKB_BLOCK_NAMESPACE = 'echo-knowledge-base';
	const EPKB_BLOCK_CATEGORY = 'echo-knowledge-base';
	const EPKB_BLOCK_CATEGORY_ICON = 'welcome-learn-more';  // not currently used by WordPress

	// each block should override the properties below
	protected $block_name = '';
	protected $block_var_name = '';

	public function __construct() {
		add_action( 'init', array( $this, 'initialize' ) );
		add_filter( 'block_type_metadata', array( $this, 'inject_attributes_custom_specs' ), 10, 1 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_block_editor_assets' ) ); // Backend
		add_filter( 'kb_' . $this->block_var_name . '_block_config', array( $this, 'filter_block_config_if_exists' ), 10, 2 );
		add_filter( 'get_block_templates', array( $this, 'filter_block_templates_by_post_type' ), 10, 3 );
	}

	public function initialize() {

		if ( empty( $this->block_name ) ) {
			return;
		}

		$this->register_block_category();

		$this->register_block_type();

		self::add_kb_block_page_template_if_missing();
	}

	protected function register_block_category() {
		// block_categories_all is a replacement for block_categories filter from WP v5.8
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) && class_exists( 'WP_Block_Editor_Context' ) ) {
			add_filter( 'block_categories_all', array( $this, 'add_block_category' ), 10, 2 );
		} else {
			add_filter( 'block_categories', array( $this, 'add_block_category' ), 10, 2 );
		}
	}

	/**
	 * Register block editor assets
	 * @return void
	 */
	function register_block_editor_assets() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// PER BLOCK: register block script for editor ( Enqueued via {name}-block.json editorScript )
		if ( ! wp_script_is( $this->get_block_editor_script_handle(), 'registered' ) ) {

			// use 'include' to have warning instead of generating PHP error and make the plugin not working due to the soft issue - the block can be not rendered, but the rest of the code (or blocks) will be still working
			$block_asset = include_once(  Echo_Knowledge_Base::$plugin_dir . 'includes/admin/blocks//block.asset.php' );
			$block_asset = empty( $block_asset ) || ! is_array( $block_asset ) ? array( 'dependencies' => array() ) : $block_asset;

			wp_register_script( $this->get_block_editor_script_handle(), Echo_Knowledge_Base::$plugin_url . 'js/' . $this->block_name . '-block' . $suffix . '.js', $block_asset['dependencies'], Echo_Knowledge_Base::$version );

			// used by JS to display input fields for the block edit screen. It is enqueued in the editor only.
			$block_ui_config = $this->get_block_ui_config();
			wp_add_inline_script( $this->get_block_editor_script_handle(), 'const epkb_' . $this->block_var_name . '_block_ui_config = ' . wp_json_encode( $block_ui_config, ENT_QUOTES ) . ';', 'before' );
		}

		// register styles if not already registered ( Enqueued via {name}-block.json editorStyle )
		if ( ! wp_style_is( $this->get_block_editor_styles_handle(), 'registered' ) ) {
			wp_register_style( $this->get_block_editor_styles_handle(),Echo_Knowledge_Base::$plugin_url . 'css/block-editor' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		}
	}

	protected function register_block_type() {
		register_block_type(
			Echo_Knowledge_Base::$plugin_dir . "includes/admin/blocks/{$this->block_name}/{$this->block_name}-block.json",
			array(
				'render_callback' => array( $this, 'render_block' ),
				'style' => 'epkb-' . $this->get_block_public_styles_handle(),	// TODO future: for RTL specify RTL slug as main here and the LTR as its dependency during registration
				'script' => 'epkb-public-scripts'
			)
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
		if ( $block_attributes['kb_id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $block_attributes['status'] ) ) {
			return esc_html__( 'This knowledge base was archived.', 'echo-knowledge-base' );
		}

		// apply defaults to missing attributes in $block_attributes
		foreach( $this->get_block_attributes_defaults() as $setting_name => $default ) {
			$block_attributes[ $setting_name ] = isset( $block_attributes[ $setting_name ] ) ? $block_attributes[ $setting_name ] : $default;
		}

		$custom_css_class_escaped = empty( $block_attributes['custom_css_class'] ) ? '' : ' ' . esc_attr( $block_attributes['custom_css_class'] );

		/* TODO: decide either remove this or add a setting to place this only for one block wrap as it should indicate the main content container: role="main" aria-labelledby="epkb-modular-main-page-container" */

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
			<style><?php echo $this->get_this_block_inline_styles( $block_attributes ); ?></style>	<?php
			$block_font_slugs = self::register_block_fonts( $block_attributes );
			self::print_block_fonts( $block_font_slugs );
		}

		return ob_get_clean();
	}

	/**
	 * Register product related block categories.
	 *
	 * @param array[] $block_categories Array of categories for block types.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 */
	public function add_block_category( $block_categories, $block_editor_context ) {

		// ensure the category is added only once (in case of child classes)
		if ( in_array( self::EPKB_BLOCK_CATEGORY, array_column( $block_categories, 'slug' ) ) ) {
			return $block_categories;
		}

		array_unshift( $block_categories,
			array(
				'slug' => self::EPKB_BLOCK_CATEGORY,
				'title' => __( 'Knowledge Base', 'echo-knowledge-base' ),
				'icon' => self::EPKB_BLOCK_CATEGORY_ICON,
			)
		);

		return $block_categories;
	}

	/**
	 * Register public styles, scripts, fonts, icons for the current block
	 * @return void
	 */
	public function register_block_assets() {
		global $post;

		// retrieve attributes for the current block
		$block_attributes = $this->get_parsed_block_attributes_or_defaults( $post );

		// add required specific attributes to work correctly with KB core functionality
		$block_attributes = $this->add_internal_kb_settings( $block_attributes );

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// core KB JS - enqueued via register_block_type()
		if ( ! wp_script_is( 'epkb-public-scripts', 'registered' ) ) {
			wp_register_script( 'epkb-public-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
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
			wp_localize_script( 'epkb-public-scripts', 'epkb_vars', $epkb_vars );
		}

		// register common public styles and scripts - enqueued as a dependency of main block style
		if ( ! wp_style_is( 'epkb-icon-fonts', 'registered' ) ) {
			wp_register_style( 'epkb-icon-fonts', Echo_Knowledge_Base::$plugin_url . 'css/epkb-icon-fonts' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		}

		// PER BLOCK: public styles and scripts - enqueued via register_block_type()
		if ( ! wp_style_is( 'epkb-' . $this->get_block_public_styles_handle(), 'registered' ) ) {

			// main styles dependencies
			$block_styles_dependencies = array_merge( array(  'epkb-icon-fonts' ), self::register_block_fonts( $block_attributes ) );

			// register main styles for current block
			wp_register_style( 'epkb-' . $this->get_block_public_styles_handle(), Echo_Knowledge_Base::$plugin_url . 'css/' . $this->get_block_public_styles_handle() . $suffix . '.css', $block_styles_dependencies, Echo_Knowledge_Base::$version );

			// register inline styles for current block
			wp_add_inline_style( 'epkb-' . $this->get_block_public_styles_handle(), EPKB_Utilities::minify_css( $this->get_this_block_inline_styles( $block_attributes ) ) );

			// TODO future: add RTL files - use RTL slug as main slug and LTR slug as its dependency; then specify the RTL slug inside register_block_type() as main slug
			/*if ( is_rtl() ) {
				wp_register_style( 'epkb-' . $this->get_block_public_styles_handle() . '-rtl', Echo_Knowledge_Base::$plugin_url . 'css/' . $this->get_block_public_styles_handle() . '-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
			}*/
		}
	}

	/**
	 * Modify global block definitions, if required and injects fully-fleshed attributes
	 */
	public function inject_attributes_custom_specs( array $meta_data ) {

		if ( self::EPKB_BLOCK_NAMESPACE . '/' . $this->block_name === $meta_data['name'] ) {

			// get attribute specifications for this block
			$block_json_attributes = $this->get_block_json_attributes();
			$block_config_defaults = $this->get_block_config_defaults();

			$kb_config_defaults = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );

			foreach ( $block_json_attributes as $block_setting_name => $block_spec ) {

				// allow block config to set default value instead of KB config
				if ( isset( $block_config_defaults[ $block_setting_name ] ) ) {

					// typography field
					if ( is_array( $block_config_defaults[ $block_setting_name ] ) ) {
						$block_json_attributes[ $block_setting_name ]['default'] = array(
							'font_family' => $block_config_defaults[ $block_setting_name ]['font_family'],
							'font_size' => $block_config_defaults[ $block_setting_name ]['font_size'],
							'font_appearance' => $block_config_defaults[ $block_setting_name ]['font_appearance'],
						);
					}

					$block_json_attributes[ $block_setting_name ]['default'] = $block_config_defaults[ $block_setting_name ];
					continue;
				}

				$block_json_attributes[ $block_setting_name ]['default'] = isset( $kb_config_defaults[ $block_setting_name ] ) ? $kb_config_defaults[ $block_setting_name ] : '';
			}

			$meta_data['attributes'] = $block_json_attributes;
		}

		// version of all the blocks should follow the version of the plugin
		$meta_data['version'] = Echo_Knowledge_Base::$version;

		return $meta_data;
	}

	/**
	 * Add default values from KB configuration to the block settings
	 * @return array
	 */
	private function get_block_attributes_defaults() {
		$kb_config_defaults = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$block_config_defaults = $this->get_block_config_defaults();
		$block_attributes_defaults = array();
		foreach ( $this->get_block_json_attributes() as $setting_name => $spec ) {

			// allow block config to set default value instead of KB config
			if ( isset( $block_config_defaults[ $setting_name ] ) ) {
				$block_attributes_defaults[ $setting_name ] = $block_config_defaults[ $setting_name ];
				continue;
			}

			$block_attributes_defaults[ $setting_name ] = isset( $kb_config_defaults[ $setting_name ] ) ? $kb_config_defaults[ $setting_name ] : '';
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
						foreach ( $field_specs['combined_settings'] as $setting_name => $setting_specs ) {

							// allow the default value to be empty, but continue only if it is set
							if ( ! isset( $setting_specs['default'] ) ) {
								continue;
							}

							$block_config_defaults[ $setting_name ] = $setting_specs['default'];
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

		$block_attributes = $this->get_parsed_block_attributes_or_defaults( $post );
		return empty( $block_attributes ) ? $kb_config : $this->add_internal_kb_settings( $block_attributes );
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

		$block_attributes['id'] = $kb_config['id'];
		$block_attributes['status'] = $kb_config['status'];
		$block_attributes['modular_main_page_toggle'] = $kb_config['modular_main_page_toggle'];
		$block_attributes['show_articles_before_categories'] = $kb_config['show_articles_before_categories'];
		$block_attributes['wpml_is_enabled'] = $kb_config['wpml_is_enabled'];
		$block_attributes['kb_main_pages'] = $kb_config['kb_main_pages'];

		// let blocks to hard-code value of certain KB settings regardless of actual KB config value
		$block_attributes = $this->add_this_block_required_kb_attributes( $block_attributes );

		return $block_attributes;
	}

	private static function get_block_editor_styles_handle() {
		return self::EPKB_BLOCK_NAMESPACE . '-block-editor';
	}

	private function get_block_editor_script_handle() {
		return self::EPKB_BLOCK_NAMESPACE . '-' . $this->block_name . '-block';
	}

	private function get_block_public_styles_handle() {
		return $this->block_name . '-block';
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
		foreach( $this->get_block_attributes_defaults() as $setting_name => $default ) {
			$block_attributes[ $setting_name ] = isset( $block_attributes[ $setting_name ] ) ? $block_attributes[ $setting_name ] : $default;
		}

		// blocks use 'kb_id' key while rest of KB code is using 'id' key - ensure the 'id' is updated before passing it to non-block functions
		$block_attributes['id'] = $block_attributes['kb_id'];

		return $block_attributes;
	}

	/**
	 * Return block attributes which are specified in the block JSON configuration
	 * @return array
	 */
	protected function get_block_json_attributes() {

		// get block configuration defined in the block JSON file
		$file_contents = json_decode(
			file_get_contents( Echo_Knowledge_Base::$plugin_dir . "includes/admin/blocks/{$this->block_name}/{$this->block_name}-block.json" ),
			true
		);

		return $file_contents['attributes'];
	}

	private function get_block_ui_config() {

		$block_ui_config = $this->get_this_block_ui_config();
		$kb_config_specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		foreach ( $block_ui_config as $tab_name => $tab_config ) {
			foreach ( $tab_config['groups'] as $group_name => $group_config ) {
				foreach ( $group_config['fields'] as $field_name => $field_specs ) {

					// combined field
					if ( isset( $field_specs['combined_settings'] ) ) {
						foreach ( $field_specs['combined_settings'] as $setting_name => $setting_specs ) {
							$block_ui_config[$tab_name]['groups'][ $group_name ]['fields'][ $field_name ]['combined_settings'][ $setting_name ] = array_merge( $kb_config_specs[ $setting_name ], $setting_specs );
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
	 * Print block fonts - use common KB slugs to avoid duplicated fonts loading
	 * @param $block_font_slugs
	 * @return void
	 */
	private static function print_block_fonts( $block_font_slugs ) {

		foreach ( $block_font_slugs as $one_font_slug ) {

			// do nothing if slug is empty, or the font is not registered yet, or the font is already enqueued
			if ( empty( $one_font_slug ) || ! wp_style_is( $one_font_slug, 'registered' ) || wp_style_is( $one_font_slug ) ) {
				continue;
			}

			wp_print_styles( $one_font_slug );
		}
	}

	/**
	 * Return 'kb_id' setting for each block
	 * @return array
	 */
	protected static function get_kb_id_setting() {

		$kb_id_setting = array(
			'setting_type' => 'custom_dropdown',
			'default' => EPKB_KB_Config_DB::DEFAULT_KB_ID,
			'label' => __( 'Selected KB', 'echo-knowledge-base' ),
			'options' => array(),
		);

		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $one_kb_config ) {

			$one_kb_id = $one_kb_config['id'];

			// do not show archived KBs
			if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
				continue;
			}

			// do not render the KB into the dropdown if the current user does not have at least minimum required capability (covers KB Groups)
			$required_capability = EPKB_Admin_UI_Access::get_contributor_capability( $one_kb_id );
			if ( ! current_user_can( $required_capability ) ) {
				continue;
			}

			// add current KB to the list
			$kb_id_setting['options'][] = array(
				'key' => (int)$one_kb_id,
				'name' => $one_kb_config['kb_name'],
				'style' => array(),
			);
		}

		return $kb_id_setting;
	}

	/**
	 * Return 'custom_css_class' setting for each block
	 * @return array
	 */
	protected static function get_custom_css_class_setting() {
		return array(
			'label'	=> __( 'Additional CSS Class(es)', 'echo-knowledge-base' ),
			'setting_type' => 'text',
			'default' => '',
			'description' => __( 'Separate multiple classes with spaces.', 'echo-knowledge-base' ),
		);
	}

	/**
	 * Return font family specs for Typography control
	 * @return array
	 */
	protected static function get_typography_control_font_family() {
		return array(
			'label' => __( 'Font', 'echo-knowledge-base' ),
			'default' => '',
			'options' => array_combine( EPKB_Typography::get_google_fonts_family_list(), EPKB_Typography::get_google_fonts_family_list() ),
		);
	}

	/**
	 * Return font size specs for Typography control
	 * @return array
	 */
	protected static function get_typography_control_font_size( $size_options, $default_size ) {

		$font_size_control = array(
			'label' => __( 'Size', 'echo-knowledge-base' ),
			'default' => $default_size,
			'units' => 'px',
			'options' => [],
		);

		$all_size_options = array(
			'small' => array(
				'name' => __( 'Small', 'echo-knowledge-base' ),
				'size' => 24,
			),
			'normal' => array(
				'name' => __( 'Medium', 'echo-knowledge-base' ),
				'size' => 36,
			),
			'big' => array(
				'name' => __( 'Large', 'echo-knowledge-base' ),
				'size' => 48,
				'slug' => 'big',
			),
		);

		foreach ( $size_options as $one_size_key => $one_size_value ) {
			if ( empty( $all_size_options[ $one_size_key ] ) ) {
				continue;
			}
			$font_size_control['options'][ $one_size_key ] = $all_size_options[ $one_size_key ];
			$font_size_control['options'][ $one_size_key ]['size'] = $one_size_value;
		}

		return $font_size_control;
	}

	/**
	 * Return font appearance specs for Typography control
	 * @param $default_args
	 * @return array
	 */
	protected static function get_typography_control_font_appearance( $default_args = array() ) {
		$default_args = wp_parse_args( $default_args, array(
			'fontWeight' => 400,
			'fontStyle' => 'normal',
		) );
		return array(
			'label' => __( 'Appearance', 'echo-knowledge-base' ),
			'default' => 'default',
			'options' => array(
				'default' => array(
					'name' => __( 'Default', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => $default_args['fontWeight'],
						'fontStyle' => $default_args['fontStyle'],
					),
				),
				'thin' => array(
					'name' => __( 'Thin', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 100,
						'fontStyle' => 'normal',
					),
				),
				'extra_light' => array(
					'name' => __( 'Extra Light', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 200,
						'fontStyle' => 'normal',
					),
				),
				'light' => array(
					'name' => __( 'Light', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 300,
						'fontStyle' => 'normal',
					),
				),
				'regular' => array(
					'name' => __( 'Regular', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 400,
						'fontStyle' => 'normal',
					),
				),
				'medium' => array(
					'name' => __( 'Medium', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 500,
						'fontStyle' => 'normal',
					),
				),
				'semi_bold' => array(
					'name' => __( 'Semi Bold', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 600,
						'fontStyle' => 'normal',
					),
				),
				'bold' => array(
					'name' => __( 'Bold', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 700,
						'fontStyle' => 'normal',
					),
				),
				'extra_bold' => array(
					'name' => __( 'Extra Bold', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 800,
						'fontStyle' => 'normal',
					),
				),
				'black' => array(
					'name' => __( 'Black', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 900,
						'fontStyle' => 'normal',
					),
				),
				'thin_italic' => array(
					'key' => 'thin_italic',
					'name' => __( 'Thin Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 100,
						'fontStyle' => 'italic',
					),
				),
				'extra_light_italic' => array(
					'name' => __( 'Extra Light Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 200,
						'fontStyle' => 'italic',
					),
				),
				'light_italic' => array(
					'name' => __( 'Light Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 300,
						'fontStyle' => 'italic',
					),
				),
				'regular_italic' => array(
					'name' => __( 'Regular Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 400,
						'fontStyle' => 'italic',
					),
				),
				'medium_italic' => array(
					'name' => __( 'Medium Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 500,
						'fontStyle' => 'italic',
					),
				),
				'semi_bold_italic' => array(
					'name' => __( 'Semi Bold Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 600,
						'fontStyle' => 'italic',
					),
				),
				'bold_italic' => array(
					'name' => __( 'Bold Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 700,
						'fontStyle' => 'italic',
					),
				),
				'extra_bold_italic' => array(
					'name' => __( 'Extra Bold Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 800,
						'fontStyle' => 'italic',
					),
				),
				'black_italic' => array(
					'name' => __( 'Black Italic', 'echo-knowledge-base' ),
					'style' => array(
						'fontWeight' => 900,
						'fontStyle' => 'italic',
					),
				),
			),
		);
	}

	/**
	 * Return configuration array for message about KB block page template
	 * @return array
	 */
	protected static function get_kb_block_template_mention() {
		return array(
			'setting_type' => 'section_description',
			'description' => __( 'Consider to use KB Block Page Template for block Main Page.', 'echo-knowledge-base' ),
			'link_text' => __( 'Learn More', 'echo-knowledge-base' ),
			'link_url' => '#',	// TODO: update URL
			'show_for_non_kb_template' => true,
		);
	}

	protected static function get_font_appearance_weight( $key = 'default' ) {
		$font_appearance_specs = self::get_typography_control_font_appearance();
		return $font_appearance_specs['options'][ $key ]['style']['fontWeight'];
	}

	protected static function get_font_appearance_style( $key = 'default' ) {
		$font_appearance_specs = self::get_typography_control_font_appearance();
		return $font_appearance_specs['options'][ $key ]['style']['fontStyle'];
	}

	/**
	 * Add KB block page template if missing
	 * @return void
	 */
	private static function add_kb_block_page_template_if_missing() {

		$kb_block_page_template_slug = 'kb-block-page-template';

		// get current Theme slug
		$theme = wp_get_theme();
		$current_theme_slug = $theme->get_stylesheet();

		// check if the template already exists for the current theme
		$kb_block_page_template_id = $current_theme_slug . '//' . $kb_block_page_template_slug;
		$existing_kb_block_template = get_block_template( $kb_block_page_template_id );

		// do not continue if error returned on the template retrieval - better to not have the template added rather than create duplicates
		if ( is_wp_error( $existing_kb_block_template ) ) {
			EPKB_Logging::add_log( 'Failed to retrieve KB block page template.', $existing_kb_block_template );
			return;
		}

		// do nothing if the template already registered for the current Theme
		if ( $existing_kb_block_template ) {
			return;
		}

		// try to retrieve existing template - the KB block page template may exist here but is not associated with the current Theme
		$existing_kb_block_templates = get_posts( array(
			'post_type'      => 'wp_template',
			'name'           => $kb_block_page_template_slug,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		) );


		// CASE: template is missing - create the template
		if ( empty( $existing_kb_block_templates ) ) {

			// define the content of the template
			$template_content =
				'<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->
				<!-- wp:post-content /-->
				<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->';

			// insert the custom template
			$inserted_template_id = wp_insert_post( array(
				'post_title'   => esc_html__( 'KB Block Page Template', 'echo-knowledge-base' ),
				'post_excerpt' => esc_html__( 'The recommended template to use for creating the KB main page.', 'echo-knowledge-base' ),
				'post_name'    => sanitize_title( $kb_block_page_template_slug ),
				'post_content' => $template_content,
				'post_status'  => 'publish',
				'post_type'    => 'wp_template',
				'tax_input'    => array(
					'wp_theme' => array( $current_theme_slug ),
				),
				'meta_input' => array(
					'wp_template_type'	=> 'custom',
					'postTypes'			=> array( 'page' ),
				),
			), true );

			if ( is_wp_error( $inserted_template_id ) ) {
				EPKB_Logging::add_log( 'Failed to insert KB block page template.', $inserted_template_id );
			}

			return;
		}

		// CASE: template exists - check if the template is associated with the current theme
		$kb_block_page_template = reset( $existing_kb_block_templates );
		$terms = wp_get_object_terms( $kb_block_page_template->ID, 'wp_theme', array( 'fields' => 'slugs' ) );
		if ( ! in_array( $current_theme_slug, $terms, true ) ) {
			wp_set_object_terms( $kb_block_page_template->ID, array_merge( $terms, array( $current_theme_slug ) ), 'wp_theme' );
		}
	}

	/**
	 * Filter out KB block page template for non-page post type
	 * @param $block_templates
	 * @param $query
	 * @param $template_type
	 * @return mixed
	 */
	public static function filter_block_templates_by_post_type( $block_templates, $query, $template_type ) {

		// only filter out the KB block page template if the post type is defined and is not page
		if ( empty( $query['post_type'] ) || $query['post_type'] == 'page' ) {
			return $block_templates;
		}

		// filter out KB block page template for non-page post type
		foreach ( $block_templates as $key => $template ) {
			if ( isset( $template->slug ) && $template->slug === 'kb-block-page-template' ) {
				unset( $block_templates[ $key ] );
			}
		}

		return $block_templates;
	}

	abstract protected function get_this_block_inline_styles( $block_attributes );

	abstract public function render_block_inner( $block_attributes );

	abstract protected function get_this_block_ui_config();

	abstract protected function get_this_block_typography_settings();

	abstract protected function add_this_block_required_kb_attributes( $block_attributes );
}